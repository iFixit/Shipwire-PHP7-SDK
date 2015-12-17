<?php
namespace CharityRoot;
use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;

class Shipwire extends Client {

    const DefaultCurrency = 'USD';

    const ITERATOR = 'ArrayIterator';

    static $environment = 'live';
    static $base_url_sandbox = 'https://api.beta.shipwire.com';
    static $base_url = 'https://api.shipwire.com';
    static $api_version = 'v3';

    public $auth_code;

    function __construct($username, $password, $sandbox = false)
    {
        parent::__construct(['base_uri' => $sandbox ? self::$base_url_sandbox : self::$base_url]);

        $this->auth_code = base64_encode($username . ':' . $password);
    }

    public function stock(array $args = [])
    {
        $response = $this->_request('stock');
        return $response;
    }

    public function products(array $args = [])
    {
        $response = $this->_request('products');
        return $response;
    }

    public function orders(array $args = [])
    {
        $response = $this->_request('orders', $args);
        return $response;
    }

    public function quote(ShipwireQuote $quote)
    {
        $response = $this->_post('rate', $quote->getBody());
        return $response;
    }

    public function createOrder(ShipwireOrder $order)
    {
        $response = $this->_post('orders', $order->getBody());
        return $response;
    }

    public function createProduct(ShipwireProduct $product)
    {
        $response = $this->_post('products', $product->getBody());
        return $response;
    }

    protected function _request($endpoint, array $query = [])
    {
        $request = new ShipwireRequest();
        $resonse = $request->
            setEndpoint($endpoint)->
            setQuery($query)->
            submit();

        return new ShipwireResponse($response, $endpoint);
    }

    protected function _post($endpoint, array $body = [])
    {
        $request = new ShipwireRequest();
        $resonse = $request->
            setEndpoint($endpoint)->
            setbody($body)->
            setMethod(ShipwireRequest::POST)->
            submit();

        return new ShipwireResponse($response, $endpoint);
    }

}
