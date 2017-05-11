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
        $response = $this->_request('stock', $args);
        return $response;
    }

    public function products(array $args = []): ShipwireResponse
    {
        $response = $this->_request('products', $args);
        return $response;
    }

    public function orders(array $args = []): ShipwireResponse
    {
        $response = $this->_request('orders', $args);
        return $response;
    }

    public function quote(ShipwireQuote $quote): ShipwireResponse
    {
        $response = $this->_post('rate', $quote->getBody());
        return $response;
    }

    public function createOrder(ShipwireOrder $order): ShipwireResponse
    {
        $response = $this->_post('orders', $order->getBody());
        return $response;
    }

    public function createProduct(ShipwireProduct $product): ShipwireResponse
    {
        $response = $this->_post('products', $product->getBody());
        return $response;
    }

    public function getTrackingsByOrderNo($orderNo)
    {
        $response = $this->orders(['orderNo' => $orderNo, 'expand' => ShipwireOrder::ARG_EXPAND_TRACKINGS]);
        $results = $response->results();
        if ($results === null) {
            return;
        }

        return $results->get('trackings');
    }

    public function api(string $endpoint, array $args = [], $method = ShipwireRequest::GET): ShipwireResponse
    {
        switch ($method) {
            case ShipwireRequest::POST:
                return $this->_post($endpoint, $args);
            default:
                return $this->_request($endpoint, $args);
        }
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

}
