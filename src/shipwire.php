<?php
namespace CharityRoot;
use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;

class Shipwire extends Client{

    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';

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
        //$args['expand'] = ShipwireOrder::ARG_EXPAND_ITEMS . ',' . ShipwireOrder::ARG_EXPAND_TRACKINGS;
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
        $response = $this->request(self::GET, '/api/v3/' . $endpoint, [
            'exceptions' => FALSE,
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . $this->auth_code,
                'User-Agent' => 'CharityRoot'
            ],
            'query' => $query,
            'body' => null
        ]);

        return new ShipwireResponse($response, $endpoint);
    }

    protected function _post($endpoint, array $post = [])
    {
        $response = $this->request(self::POST, '/api/v3/' . $endpoint, [
            'exceptions' => FALSE,
            'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Basic ' . $this->auth_code,
                    'User-Agent' => 'CharityRoot'
                ],
                'body' => json_encode($post)
            ]);

        $this->_last_post_body = json_encode($post);
        return new ShipwireResponse($response, $endpoint);
    }

    public function getLastPostBody()
    {
        return $this->_last_post_body;
    }

}
