<?php
namespace CharityRoot;

class ShipwireQuoteItem extends ShipwireItem {

    function __construct($input = [], $flags = 0, $iterator_class = Shipwire::ITERATOR)
    {
        $_filtered = [
            'sku' => strval($input['sku']),
            'quantity' => intval($input['quantity'])
        ];
        parent::__construct($_filtered, $flags, $iterator_class);
    }

}
