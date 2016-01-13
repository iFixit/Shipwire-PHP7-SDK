<?php
namespace CharityRoot;

class ShipwireOrder extends ShipwireResource {
    const ARG_EXPAND_ALL = 'all';
    const ARG_EXPAND_ITEMS = 'items';
    const ARG_EXPAND_TRACKINGS = 'trackings';

    function __construct($input = [], $flags = 0, $iterator_class = Shipwire::ITERATOR)
    {
        $input['processAfterDate'] = $input['processAfterDate'] ? new \DateTime($input['processAfterDate']) : null;
        $input['lastUpdatedDate'] = $input['lastUpdatedDate'] ? new \DateTime($input['lastUpdatedDate']) : null;
        $input['shipTo'] = $input['shipTo']['resource'] ? new ShipwireAddress($input['shipTo']['resource']) : null;
        $input['shipFrom'] = $input['shipFrom']['resource'] ? new ShipwireAddress($input['shipFrom']['resource']) : null;
        $input['routing'] = $input['routing']['resource'] ? new ShipwireOrderRouting($input['routing']['resource']) : null;
        $input['options'] = $input['options']['resource'] ? new ShipwireOptions($input['options']['resource']) : null;
        $input['events'] = $input['events']['resource'] ? new ShipwireOrderEvents($input['events']['resource']) : null;
        $input['pricing'] = $input['pricing']['resource'] ? new ShipwireOrderPricing($input['pricing']['resource']) : null;
        $input['pricingEstimate'] = $input['pricingEstimate']['resource'] ? new ShipwireOrderPricing($input['pricingEstimate']['resource']) : null;

        $replace_items = [];
        foreach ($input['items']['resource']['items'] as $order_item) {
            $replace_items[] = new ShipwireOrderItem($order_item['resource']);
        }
        $input['items'] = new ShipwireItems($replace_items);

        $replace_items = [];
        foreach ($input['trackings']['resource']['items'] as $order_item) {
            $replace_items[] = new ShipwireTracking($order_item['resource']);
        }

        $input['trackings'] = new ShipwireItems($replace_items);

        parent::__construct($input, $flags, $iterator_class);
    }

    public function setOptions(ShipwireOptions $options): self
    {
        $this->set('options', $options);
        return $this;
    }

    public function addItem(ShipwireOrderItem $item): self
    {
        if (!$this->get('items')) {
            $this->set('items', new ShipwireItems());
        }

        $_filtered = [
            'sku' => strval($item['sku']),
            'quantity' => intval($item['quantity'])
        ];

        $this->get('items')->append($_filtered);

        return $this;
    }

    public function setToAddress(ShipwireAddress $address): self
    {
        $this->set('shipTo', $address);
        return $this;
    }

    public function setFromAddress(ShipwireAddress $address): self
    {
        $this->set('shipFrom', $address);
        return $this;
    }

    public function setCommercialInvoice(ShipwireOrderCommercialInvoice $invoice): self
    {
        $this->set('commercialInvoice', $invoice);
        return $this;
    }

    public function setPackingList(ShipwireOrderPackingList $packing_list): self
    {
        $this->set('packingList', $packing_list);
        return $this;
    }

    protected function _buildRequestBody()
    {
        $json = [];

        if ($this->get('orderNo')) {
            $json['orderNo'] = $this->get('orderNo');
        }

        if ($this->get('externalId')) {
            $json['externalId'] = $this->get('externalId');
        }

        if ($this->get('options')) {
            $json['options'] = $this->get('options')->getArray();
        }

        if ($this->get('commercialInvoice')) {
            $json['commercialInvoice'] = $this->get('commercialInvoice')->getArray();
        }

        if ($this->get('packingList')) {
            $json['packingList'] = $this->get('packingList')->getArray();
        }

        if ($this->get('shipFrom')) {
            $json['shipFrom'] = $this->get('shipFrom')->getArray();
        }

        $json['shipTo'] = $this->get('shipTo')->getArray();
        $json['items'] = [];

        foreach ($this->get('items') as $item) {
            $json['items'][] = $item;
        }

        $this->_request_body = $json;
    }

}
