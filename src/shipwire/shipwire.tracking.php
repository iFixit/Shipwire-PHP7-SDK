<?php
namespace CharityRoot;

class ShipwireTracking extends ShipwireResource {

    function __construct($input = [], $flags = 0, $iterator_class = Shipwire::ITERATOR)
    {
        $input['trackedDate'] = $input['trackedDate'] ? new \DateTime($input['trackedDate']) : null;
        $input['deliveredDate'] = $input['deliveredDate'] ? new \DateTime($input['deliveredDate']) : null;
        $input['createdDate'] = $input['createdDate'] ? new \DateTime($input['createdDate']) : null;
        $input['summaryDate'] = $input['summaryDate'] ? new \DateTime($input['summaryDate']) : null;
        parent::__construct($input, $flags, $iterator_class);
    }

}
