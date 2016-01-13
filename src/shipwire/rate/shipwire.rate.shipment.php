<?php
namespace CharityRoot;

class ShipwireRateShipment extends ShipwireResource {
    function __construct($input = [], $flags = 0, $iterator_class = Shipwire::ITERATOR)
    {
        $input['expectedShipDate'] = $input['expectedShipDate'] ? new \DateTime($input['expectedShipDate']) : null;
        $input['expectedDeliveryMinDate'] = $input['expectedDeliveryMinDate'] ? new \DateTime($input['expectedDeliveryMinDate']) : null;
        $input['expectedDeliveryMaxDate'] = $input['expectedDeliveryMaxDate'] ? new \DateTime($input['expectedDeliveryMaxDate']) : null;
        $input['carrier'] = $input['carrier'] ? new ShipwireCarrier($input['carrier']) : null;
        $input['cost'] = $input['cost'] ? new ShipwireMoney($input['cost']) : null;

        $replace_items = [];
        foreach ($input['subtotals'] as $monies) {
            $replace_items[] = new ShipwireMoney($monies);
        }

        $input['subtotals'] = new ShipwireItems($replace_items);

        $replace_items = [];
        foreach ($input['pieces'] as $piece) {
            $replace_items[] = new ShipwireRateDimensions($piece);
        }

        $input['pieces'] = new ShipwireItems($replace_items);

        parent::__construct($input, $flags, $iterator_class);
    }

}
