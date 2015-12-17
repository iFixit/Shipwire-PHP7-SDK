<?php
namespace CharityRoot;

class ShipwireCarrier extends ShipwireResource {

    function __construct($input = [], $flags = 0, $iterator_class = Shipwire::ITERATOR)
    {
        $input['properties'] = $input['properties'] ? new ShipwireOptions($input['properties']) : null;
        parent::__construct($input, $flags, $iterator_class);
    }

}
