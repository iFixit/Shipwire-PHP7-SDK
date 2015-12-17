<?php
namespace CharityRoot;

class ShipwireOrderEvents extends ShipwireResource {

    function __construct($input = [], $flags = 0, $iterator_class = Shipwire::ITERATOR)
    {
        $input['createdDate'] = $input['createdDate'] ? new \DateTime($input['createdDate']) : null;
        $input['pickedUpDate'] = $input['pickedUpDate'] ? new \DateTime($input['pickedUpDate']) : null;
        $input['submittedDate'] = $input['submittedDate'] ? new \DateTime($input['submittedDate']) : null;
        $input['processedDate'] = $input['processedDate'] ? new \DateTime($input['processedDate']) : null;
        $input['completedDate'] = $input['completedDate'] ? new \DateTime($input['completedDate']) : null;
        $input['expectedDate'] = $input['expectedDate'] ? new \DateTime($input['expectedDate']) : null;
        $input['cancelledDate'] = $input['cancelledDate'] ? new \DateTime($input['cancelledDate']) : null;
        $input['returnedDate'] = $input['returnedDate'] ? new \DateTime($input['returnedDate']) : null;
        $input['lastManualUpdateDate'] = $input['lastManualUpdateDate'] ? new \DateTime($input['lastManualUpdateDate']) : null;
        parent::__construct($input, $flags, $iterator_class);
    }

}
