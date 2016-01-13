<?php
namespace CharityRoot;

class ShipwireItem extends \ArrayObject {
    const DESC = 'DESC';
    const ASC = 'ASC';

    function __construct($input = [], $flags = 0, $iterator_class = Shipwire::ITERATOR)
    {
        parent::__construct($input, $flags, $iterator_class);
    }

    public function get($key) {
        return $this->offsetGet($key);
    }

    public function set($key, $value): self
    {
        $array = $this->getArray();
        $array[$key] = $value;
        $this->exchangeArray($array);
        return $this;
    }

    public function removeKey($key): self
    {
        $this->offsetUnset($key);
        return $this;
    }

    public function getArray(): array
    {
        return $this->getArrayCopy();
    }

    public function getKeys(): array
    {
        $array = $this->get(0);
        if (!$array) {
            return [];
        }

        $array = $this->get(0)->getArray();
        return array_keys($array);
    }

    public function prepend($data): self
    {
        $array = $this->getArrayCopy();
        array_unshift($array, $data);
        $this->exchangeArray($array);

        return $this;
    }

    public function getJSON(): string
    {
        return json_encode($this->getArray());
    }

}
