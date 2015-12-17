<?php
namespace CharityRoot;

class ShipwireProductValues extends ShipwireResource {

    function __construct($input = [], $flags = 0, $iterator_class = Shipwire::ITERATOR)
    {
        $input['costValue'] = $input['costValue'] ? intval($input['costValue']) : 0;
        $input['wholesaleValue'] = $input['wholesaleValue'] ? intval($input['wholesaleValue']) : 0;
        $input['retailValue'] = $input['retailValue'] ? intval($input['retailValue']) : 0;
        $input['costCurrency'] = $input['costCurrency'] ? strval($input['costCurrency']) : Shipwire::DefaultCurrency;
        $input['wholesaleCurrency'] = $input['wholesaleCurrency'] ? strval($input['wholesaleCurrency']) : Shipwire::DefaultCurrency;
        $input['retailCurrency'] = $input['retailCurrency'] ? strval($input['retailCurrency']) : Shipwire::DefaultCurrency;
        parent::__construct($input, $flags, $iterator_class);
    }

}
