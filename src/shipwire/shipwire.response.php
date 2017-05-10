<?php
namespace CharityRoot;
use GuzzleHttp\Psr7\Response;

class ShipwireResponse {

    protected $_status;
    protected $_message;
    protected $_count;
    protected $_endpoint;
    protected $_raw;
    protected $_resource;
    protected $_http_status;

    public function __construct(Response $guzzle_response, $endpoint)
    {
        $this->_endpoint = $endpoint;

        if ($guzzle_response === null) {
            return;
        }

        $this->_status = $guzzle_response->getStatusCode();
        $this->_raw = $guzzle_response->getBody()->getContents();

        if (!$this->_raw) {
            throw new ShipwireException('Response body content empty');
        }

        $array = $this->_jsonDecode($this->_raw);
        $this->_count = $array['resource']['total'];
        $this->_message = $array['message'];
        $this->_http_status = $array['status'];
        $this->_errors = isset($array['errors']) ? new ShipwireErrors($array['errors']) : new ShipwireErrors();
        if (!$array['resource']) {
            return;
        }

        $this->_resource = new ShipwireResource($array['resource']);
        $this->_treatResource();
    }

    public function success(): bool
    {
        return $this->_http_status == 200;
    }

    public function results()
    {
        return $this->_resource;
    }

    public function errors(): ShipwireErrors
    {
        return $this->_errors;
    }

    protected function _jsonDecode($json)
    {
        $json = json_decode($json, true);
        if (is_array($json)) {
            return $json;
        }

        throw new ShipwireException('Invalid JSON Response: ' . json_last_error());
    }

    protected function _treatResource(): self
    {
        $method = '_treat' . ucwords($this->_endpoint) . 'Resource';
        if (method_exists($this, $method)) {
            $this->$method();
        }

        return $this;
    }

    protected function _treatStockResource(): self
    {
        $items = $this->_resource->get('items');

        if ($this->_count === 1) {
            $this->_resource = new ShipwireInventory($items[0]['resource']);
            return $this;
        }

        $replace = [];
        if (empty($items)) {
            return $this;
        }

        foreach ($items as $item) {
            $replace[] = new ShipwireInventory($item['resource']);
        }

        $this->_resource->set('items', new ShipwireItems($replace));
        return $this;
    }

    protected function _treatProductsResource(): self
    {
        $items = $this->_resource->get('items');

        if ($this->_count === 1) {
            $this->_resource = new ShipwireProduct($items[0]['resource']);
            return $this;
        }

        $replace = [];
        if (empty($items)) {
            return $this;
        }

        foreach ($items as $item) {
            $replace[] = new ShipwireProduct($item['resource']);
        }

        $this->_resource->set('items', new ShipwireItems($replace));
        return $this;
    }

    protected function _treatOrdersResource(): self
    {
        $items = $this->_resource->get('items');

        if ($this->_count === 1) {
            $this->_resource = new ShipwireOrder($items[0]['resource']);
            return $this;
        }

        $replace = [];
        if (empty($items)) {
            return $this;
        }

        foreach ($items as $item) {
            $replace[] = new ShipwireOrder($item['resource']);
        }

        $this->_resource->set('items', new ShipwireItems($replace));
        return $this;
    }

    protected function _treatRateResource(): self
    {
        $items = $this->_resource->get('rates');
        $items = $items[0]['serviceOptions'];
        if ($this->_count === 1) {
            $this->_resource = new ShipwireRate($items[0]['resource']);
            return $this;
        }

        $replace = [];
        if (empty($items)) {
            return $this;
        }

        foreach ($items as $item) {
            $replace[] = new ShipwireRate($item);
        }

        $this->_resource = new ShipwireRateItems($replace);
        return $this;
    }

}
