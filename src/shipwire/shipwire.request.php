<?php
namespace CharityRoot;
use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;

class ShipwireRequest {
	private $_authcode;
	private $_query;
	private $_body;
	private $_method;
	private $_endpoint;
	private $_request;
	private $_response;

    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';

	function __construct($authcode)
	{
		$this->_authcode = $authcode;
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
		return $this->_request();
	}

    protected function _request()
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