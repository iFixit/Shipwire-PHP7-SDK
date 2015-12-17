<?php
namespace CharityRoot;

class ShipwireRate extends ShipwireResource {

    private $_options;
    private $_order;
    protected $_request_body;

    function __construct($input = [], $flags = 0, $iterator_class = Shipwire::ITERATOR)
    {
        $replace_items = [];
        foreach ($input['shipments'] as $shipment) {
            $replace_items[] = new ShipwireRateShipment($shipment);
        }

        $input['shipments'] = new ShipwireItems($replace_items);
        parent::__construct($input, $flags, $iterator_class);
    }

}
