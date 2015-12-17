<?php
namespace CharityRoot;

class ShipwireProduct extends ShipwireResource {

    const DEFAULT_PRODUCT_CLASS = 'baseProduct';

    function __construct($input = [], $flags = 0, $iterator_class = Shipwire::ITERATOR)
    {
        $input['sku'] = $input['sku'] ? strval($input['sku']) : null;
        $input['externalId'] = $input['externalId'] ? strval($input['externalId']) : null;
        $input['countryOfOrigin'] = $input['countryOfOrigin'] ?: 'US';
        $input['category'] = $input['category'] ?: 'OTHER';
        $input['batteryConfiguration'] = $input['batteryConfiguration'] ?: 'NOBATTERY';
        $input['classification'] = $input['classification'] ?: self::DEFAULT_PRODUCT_CLASS;
        $input['creationDate'] = $input['creationDate'] ? new \DateTime($input['creationDate']) : null;
        parent::__construct($input, $flags, $iterator_class);
    }

    public function setValues(ShipwireProductValues $values)
    {
        $this->set('values', $values);
        return $this;
    }

    public function setDimensions(ShipwireProductDimensions $dimensions)
    {
        $this->set('dimensions', $dimensions);
        return $this;
    }

    public function setFlags(ShipwireProductFlags $flags)
    {
        $this->set('flags', $flags);
        return $this;
    }

    protected function _buildRequestBody()
    {
        $json = [];

        $json['sku'] = $this->get('sku');
        $json['countryOfOrigin'] = $this->get('countryOfOrigin');
        $json['classification'] = $this->get('classification');
        $json['category'] = $this->get('category');
        $json['batteryConfiguration'] = $this->get('batteryConfiguration');

        if ($this->get('externalId')) {
            $json['externalId'] = $this->get('externalId');
        }

        if ($this->get('hsCode')) {
            $json['hsCode'] = $this->get('hsCode');
        }

        if ($this->get('description')) {
            $json['description'] = $this->get('description');
        }

        if ($this->get('values')) {
            $json['values'] = $this->get('values')->getArray();
        }

        if ($this->get('dimensions')) {
            $json['dimensions'] = $this->get('dimensions')->getArray();
        }

        if ($this->get('flags')) {
            $json['flags'] = $this->get('flags')->getArray();
        }

        $this->_request_body = [$json];
    }

}
