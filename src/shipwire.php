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

    public function stock(array $args = []): ShipwireResource
    {
        return $this->api('stock', $args);
    }

    public function products(array $args = []): ShipwireResource
    {
        return $this->api('products', $args);
    }

    public function orders(array $args = []): ShipwireResource
    {
        return $this->api('orders', $args);
    }

    public function quote(ShipwireQuote $quote): ShipwireResource
    {
        return $this->api('rate', $quote->getBody(), ShipmentRequest::POST);
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
}
