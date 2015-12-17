<?php
namespace CharityRoot;
use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;

class ShipwireRequest extends Client  {
	
	const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';

	private $_authcode;
	private $_query;
	private $_body;
	private $_method = self::GET;
	private $_endpoint;
	private $_request;
	private $_response;
    
    static $api_version = 'v3';
    static $environment = 'live';

    private static $_base_url_sandbox = 'https://api.beta.shipwire.com';
    private static $_base_url = 'https://api.shipwire.com';

    function __construct($authcode, $sandbox = false)
    {
    	$this->_authcode = $authcode;
		$this->_base_uri = $sandbox ? self::$_base_url_sandbox : self::$_base_url;
        parent::__construct(['base_uri' => $this->_base_uri]);
    }

	public function setQuery(array $query)
	{
		$this->_query = $query;
		return $this;
	}

	public function setBody($body = null)
	{
		$this->_body = $body;
		return $this;
	}

	public function setMethod($method = self::GET)
	{
		$this->_method = $method;
		return $this;
	}

	public function setEndpoint($endpoint)
	{
		$this->_endpoint = $endpoint;
		return $this;
	}

	public function submit()
    {
    	$this->_request = [
            'exceptions' => FALSE,
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . $this->_authcode,
                'User-Agent' => 'CharityRoot'
            ],
            'query' => $this->_query ?: null,
            'body' => $this->_body ? json_encode($this->_body) : null
        ];

        $this->_response = $this->request($this->_method, '/api/v3/' . $this->_endpoint, $this->_request);
        return new ShipwireResponse($this->_response, $this->_endpoint);
    }

    public function getLastRequest()
    {
        return $this->_request;
    }

    public function getLastResponse()
    {
        return $this->_response;
    }

}