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

    public function stock(array $args = []): ShipwireResponse
    {
        $response = $this->api('stock', $args);
        return $response;
    }

    public function products(array $args = []): ShipwireResponse
    {
        $response = $this->api('products', $args);
        return $response;
    }

    public function orders(array $args = []): ShipwireResponse
    {
        $response = $this->api('orders', $args);
        return $response;
    }

    public function quote(ShipwireQuote $quote): ShipwireResponse
    {
        $response = $this->api('rate', $quote->getBody(), ShipmentRequest::POST);
        return $response;
    }

    public function createOrder(ShipwireOrder $order): ShipwireResponse
    {
        $response = $this->api('orders', $order->getBody(), ShipmentRequest::POST);
        return $response;
    }

    public function createProduct(ShipwireProduct $product): ShipwireResponse
    {
        $response = $this->api('products', $product->getBody(), ShipmentRequest::POST);
        return $response;
    }

    public function getTrackingsByOrderNo($orderNo): ShipwireItems
    {
        $response = $this->orders(['orderNo' => $orderNo, 'expand' => ShipwireOrder::ARG_EXPAND_TRACKINGS]);
        $results = $response->results();

        return $results->get('trackings');
    }

    public function webhooks(): ShipwireItems
    {
       $response = $this->api('webhooks');

       $webhooks = array_map(function($item) {
            return new ShipwireWebhook($item['resource']);
       }, $response->results()->get('items'));
       return new ShipwireItems($webhooks);
    }

    public function webhook(int $id): ShipwireWebhook
    {
      $response = $this->api("webhooks/$id");

      $results = $response->results();
      return new ShipwireWebhook($results->getArray());
    }

    public function createWebhook(ShipwireWebhook $webhook): ShipwireWebhook
    {
      $response = $this->api('webhooks', $webhook->getBody(), ShipwireRequest::POST);

      $resource = $response->results()->get('items')[0]['resource'];
      return new ShipwireWebhook($resource);
    }

    public function updateWebhook(ShipwireWebhook $webhook): ShipwireWebhook
    {
      $id = $webhook->get('id');
      $response = $this->api("webhooks/$id", $webhook->getBody(), ShipwireRequest::PUT);

      $results = $response->results();
      return new ShipwireWebhook($results->getArray());
    }

    public function deleteWebhook(int $id): void
    {
      $this->api("webhooks/$id", [], ShipwireRequest::DELETE);
    }

    public function api(string $endpoint, array $args = [], $method = ShipwireRequest::GET): ShipwireResponse
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

        return $response;
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
}
