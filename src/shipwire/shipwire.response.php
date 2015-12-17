<?php
namespace CharityRoot;
use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;

class ShipwireResponse {

    private $_status;
    private $_message;
    private $_count;
    private $_endpoint;
    private $_raw;
    private $_resource;

    public function __construct(Response $response, $endpoint)
    {
        $this->_status = $response->getStatusCode();
        $this->_endpoint = $endpoint;
        $this->_raw = $response->getBody()->getContents();

        if (!$this->_raw) {
            throw new ShipwireException('Response body content empty');
        }

        $array = $this->_jsonDecode($this->_raw);
        $this->_count = $array['resource']['total'];
        $this->_message = $array['message'];
        if (!$array['resource']) {
            return;
        }

        $this->_resource = new ShipwireResource($array['resource']);
        $this->_treatResource();
    }

    public function get()
    {
        return $this->_resource;
    }

    protected function _jsonDecode($json)
    {
        $json = json_decode($json, true);
        if (is_array($json)) {
            return $json;
        }

        throw new ShipwireException('Invalid JSON Response: ' . json_last_error());
    }

    protected function _treatResource()
    {
        $method = '_treat' . ucwords($this->_endpoint) . 'Resource';
        if (method_exists($this, $method)) {
            $this->$method();
        }

    }

    protected function _treatStockResource()
    {
        $items = $this->_resource->get('items');

        if ($this->_count === 1) {
            $this->_resource = new ShipwireInventory($items[0]['resource']);
            return;
        }

        $replace = [];
        if (empty($items)) {
            return;
        }

        foreach ($items as $item) {
            $replace[] = new ShipwireInventory($item['resource']);
        }

        $this->_resource->set('items', new ShipwireItems($replace));
    }

    protected function _treatProductsResource()
    {
        $items = $this->_resource->get('items');

        if ($this->_count === 1) {
            $this->_resource = new ShipwireProduct($items[0]['resource']);
            return;
        }

        $replace = [];
        if (empty($items)) {
            return;
        }

        foreach ($items as $item) {
            $replace[] = new ShipwireProduct($item['resource']);
        }

        $this->_resource->set('items', new ShipwireItems($replace));
    }

    protected function _treatOrdersResource()
    {
        $items = $this->_resource->get('items');

        if ($this->_count === 1) {
            $this->_resource = new ShipwireOrder($items[0]['resource']);
            return;
        }

        $replace = [];
        if (empty($items)) {
            return;
        }

        foreach ($items as $item) {
            $replace[] = new ShipwireOrder($item['resource']);
        }

        $this->_resource->set('items', new ShipwireItems($replace));
    }

    protected function _treatRateResource()
    {
        $items = $this->_resource->get('rates');
        $items = $items[0]['serviceOptions'];
        if ($this->_count === 1) {
            $this->_resource = new ShipwireRate($items[0]['resource']);
            return;
        }

        $replace = [];
        if (empty($items)) {
            return;
        }

        foreach ($items as $item) {
            $replace[] = new ShipwireRate($item);
        }

        $this->_resource = new ShipwireRateItems($replace);
    }

}
