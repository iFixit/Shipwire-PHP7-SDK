<?php
namespace CharityRoot;

class ShipwireResource extends ShipwireItem {
    protected $_request_body;

    public function getBody() {
        $this->_buildRequestBody();
        return $this->_request_body;
    }

}
