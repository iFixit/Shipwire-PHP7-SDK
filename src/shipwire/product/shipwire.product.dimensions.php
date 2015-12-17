<?php
namespace CharityRoot;

class ShipwireProductDimensions extends ShipwireResource {

    function __construct($input = [], $flags = 0, $iterator_class = Shipwire::ITERATOR)
    {
        $input['length'] = $input['length'] ? intval($input['length']) : 0;
        $input['width'] = $input['width'] ? intval($input['width']) : 0;
        $input['height'] = $input['height'] ? intval($input['height']) : 0;
        $input['length'] = $input['length'] ? intval($input['length']) : 0;
        parent::__construct($input, $flags, $iterator_class);
    }

}
