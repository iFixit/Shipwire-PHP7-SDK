<?php
namespace CharityRoot;

class Shipwire {

    const DefaultCurrency = 'USD';

    const ITERATOR = 'ArrayIterator';

    static $environment = 'live';
    static $base_url_sandbox = 'https://api.beta.shipwire.com';
    static $base_url = 'https://api.shipwire.com';
    static $api_version = 'v3';

    private $_authcode;
    private $_sandbox;

    function __construct(string $username, string $password, bool $sandbox = false)
    {
        $this->_sandbox = $sandbox;
        $this->_authcode = base64_encode($username . ':' . $password);
    }

    public function stock(array $args = []): \Iterator
    {
        $stockApi = function(array $args) {
          return $this->api('stock', $args);
        };
        $getAllStock = $this->makePaginated($stockApi);

        foreach ($getAllStock($args) as $stock) {
          yield new ShipwireInventory($stock);
        }
    }

    public function products(array $args = []): ShipwireItems
    {
        $resource = $this->api('products', $args);

        $products = array_map(function($item) {
            return new ShipwireProduct($item['resource']);
        }, $resource->get('items'));

        return new ShipwireItems($products);
    }

    public function orders(array $args = []): \Iterator
    {
        $ordersApi = function(array $args) {
          return $this->api('orders', $args);
        };
        $getAllOrders = $this->makePaginated($ordersApi);

        foreach ($getAllOrders($args) as $order) {
          yield new ShipwireOrder($order);
        }
    }

    public function order(string $orderNo): ShipwireOrder
    {
      $resource = $this->orders(['orderNo' => $orderNo, 'expand' => ShipwireOrder::ARG_EXPAND_ALL]);
      $orders = $resource->get('items');

      $order = reset($orders);

      if ($order) {
         return new ShipwireOrder($order);
      } else {
         return new ShipwireOrder();
      }
    }

    public function quote(ShipwireQuote $quote): ShipwireRateItems
    {
        $resource =  $this->api('rate', $quote->getBody(), ShipmentRequest::POST);

        $rates = array_map(function($rate) {
            return new ShipwireRate($item['resource']);
        }, $resource->get('rates'));

        return new ShipwireRateItems($rates);
    }

    public function createOrder(ShipwireOrder $order): ShipwireResource
    {
        return $this->api('orders', $order->getBody(), ShipmentRequest::POST);
    }

    public function createProduct(ShipwireProduct $product): ShipwireResource
    {
        return $this->api('products', $product->getBody(), ShipmentRequest::POST);
    }

    public function getTrackingsByOrderNo($orderNo): ShipwireItems
    {
        $results = $this->orders(['orderNo' => $orderNo, 'expand' => ShipwireOrder::ARG_EXPAND_TRACKINGS]);
        return $results->get('trackings');
    }

    public function webhooks(): ShipwireItems
    {
       $results = $this->api('webhooks');
       $webhooks = array_map(function($item) {
            return new ShipwireWebhook($item['resource']);
       }, $results->get('items'));

       return new ShipwireItems($webhooks);
    }

    public function webhook(int $id): ShipwireWebhook
    {
      $results = $this->api("webhooks/$id");
      return new ShipwireWebhook($results->getArray());
    }

    public function createWebhook(ShipwireWebhook $webhook): ShipwireWebhook
    {
      $results = $this->api('webhooks', $webhook->getBody(), ShipwireRequest::POST);
      $resource = $results->get('items')[0]['resource'];

      return new ShipwireWebhook($resource);
    }

    public function updateWebhook(ShipwireWebhook $webhook): ShipwireWebhook
    {
      $id = $webhook->get('id');
      $results = $this->api("webhooks/$id", $webhook->getBody(), ShipwireRequest::PUT);

      return new ShipwireWebhook($results->getArray());
    }

    public function deleteWebhook(int $id): void
    {
      $this->api("webhooks/$id", [], ShipwireRequest::DELETE);
    }

    public function secrets(): ShipwireItems
    {
      $results = $this->api('secret');
      $secrets = array_map(function($item) {
         return new ShipwireWebhookSecret($item['resource']);
      }, $results->get('items'));

      return new ShipwireItems($secrets);
    }

    public function secret(int $id): ShipwireWebhookSecret
    {
      $results = $this->api("secret/$id");
      return new ShipwireWebhookSecret($results->getArray());
    }

    public function createSecret(): ShipwireWebhookSecret
    {
       $results = $this->api("secret", [], ShipwireRequest::POST);
       return new ShipwireWebhookSecret($results->getArray());
    }

    public function deleteSecret(int $id): void
    {
      $this->api("secret/$id", [], ShipwireRequest::DELETE);
    }

    public function api(string $endpoint, array $args = [], $method = ShipwireRequest::GET): ShipwireResource
    {
        switch ($method) {
            case ShipwireRequest::POST:
                $response = $this->_post($endpoint, $args);
                break;
            case ShipwireRequest::PUT:
                $response = $this->_put($endpoint, $args);
                break;
            case ShipwireRequest::DELETE:
                $response = $this->_delete($endpoint, $args);
                break;
            default:
                $response = $this->_request($endpoint, $args);
        }

        if (!$response->success()) {
           throw new ShipwireException($response->message());
        }

        return $response->results();
    }

    protected function _request(string $endpoint, array $query = []): ShipwireResponse
    {
        $request = new ShipwireRequest($this->_authcode, $this->_sandbox);
        return $request->
            setEndpoint($endpoint)->
            setQuery($query)->
            submit();
    }

    protected function _post(string $endpoint, array $body = []): ShipwireResponse
    {
        $request = new ShipwireRequest($this->_authcode, $this->_sandbox);
        return $request->
            setEndpoint($endpoint)->
            setbody($body)->
            setMethod(ShipwireRequest::POST)->
            submit();
    }

    protected function _put(string $endpoint, array $body = []): ShipwireResponse
    {
        $request = new ShipwireRequest($this->_authcode, $this->_sandbox);
        return $request->
            setEndpoint($endpoint)->
            setbody($body)->
            setMethod(ShipwireRequest::PUT)->
            submit();
    }

    protected function _delete(string $endpoint, array $body = []): ShipwireResponse
    {
        $request = new ShipwireRequest($this->_authcode, $this->_sandbox);
        return $request->
            setEndpoint($endpoint)->
            setbody($body)->
            setMethod(ShipwireRequest::DELETE)->
            submit();
    }

    /**
     * Consumes a function that gets shipwire resources and produces a function
     * a function that paginates through all pages of the API response.
     *
     * This returned function lazily grabs more data from the API as needed, so
     * you can grab arbitrarily large amounts of resources.
     */
    protected function makePaginated(callable $fn): callable
    {
        $paginationRunner = function(array $args) use ($fn): \Iterator {
            do {
              $resource = $fn($args);

              foreach ($resource->get('items') as $item) {
                yield $item['resource'];
              }

              if ($resource['next']) {
                $queryStr = parse_url($resource['next'], PHP_URL_QUERY);
                parse_str($queryStr, $params);
                $args['offset'] = $params['offset'];
              }
            } while ($resource['next']);
        };

        return $paginationRunner;
    }
}
