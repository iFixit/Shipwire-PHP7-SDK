<?php
namespace CharityRoot;

class ShipwireQuote extends ShipwireResource {

    private $_options;
    private $_order;
    protected $_request_body;

    function __construct($input = [], $flags = 0, $iterator_class = Shipwire::ITERATOR)
    {
        parent::__construct($input, $flags, $iterator_class);
    }

    public function setOptions(ShipwireOptions $options)
    {
        $this->_options = $options;
        return $this;
    }

    public function setItems(ShipwireItems $items)
    {
        $this->_order = $this->_order ?: new ShipwireOrder();

        $this->_order->set('items', $items);

        return $this;
    }

    public function addItem(ShipwireQuoteItem $item)
    {
        $this->_order = $this->_order ?: new ShipwireOrder;
        $this->_order['_items'] = $this->_order['_items'] ?: new ShipwireItems();

        $this->_order->get('_items')->append($item);
        return $this;
    }

    public function setAddress(ShipwireAddress $address)
    {
        $this->_order = $this->_order ?: new ShipwireOrder;

        $this->_order->set('_address', $address);

        return $this;
    }

    protected function _buildRequestBody()
    {
        $json = [];
        $json['options'] = $this->_options->getArray();
        $json['order'] = [
            'shipTo' => $this->_order->get('_address')->getArray(),
            'items' => [],
        ];

        foreach ($this->_order->get('_items') as $item) {
            $json['order']['items'][] = $item->getArray();
        }

        $this->_request_body = $json;
    }

}
