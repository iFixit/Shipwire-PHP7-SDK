<?php
namespace CharityRoot;

class ShipwireProductFlags extends ShipwireResource {

    function __construct($input = [], $flags = 0, $iterator_class = Shipwire::ITERATOR)
    {
        $input['isPackagedReadyToShip'] = $input['length'] ? intval($input['length']) : 0;
        $input['isFragile'] = $input['width'] ? intval($input['width']) : 0;
        $input['isDangerous'] = $input['height'] ? intval($input['height']) : 0;
        $input['isPerishable'] = $input['length'] ? intval($input['length']) : 0;
        $input['isMedia'] = $input['length'] ? intval($input['length']) : 0;
        $input['isAdult'] = $input['length'] ? intval($input['length']) : 0;
        $input['isLiquid'] = $input['length'] ? intval($input['length']) : 0;
        $input['hasInnerPack'] = $input['length'] ? intval($input['length']) : 0;
        $input['hasMasterCase'] = $input['length'] ? intval($input['length']) : 0;
        $input['hasPallet'] = $input['length'] ? intval($input['length']) : 0;
        parent::__construct($input, $flags, $iterator_class);
    }

}
