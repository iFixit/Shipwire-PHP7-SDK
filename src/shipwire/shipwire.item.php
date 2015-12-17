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

    public function set($key, $value) {
        $array = $this->getArray();
        $array[$key] = $value;
        $this->exchangeArray($array);
    }

    public function removeKey($key)
    {
        return $this->offsetUnset($key);
    }

    public function getArray()
    {
        return $this->getArrayCopy();
    }

    public function getKeys()
    {
        $array = $this->get(0);
        if (!$array) {
            return [];
        }

        $array = $this->get(0)->getArray();
        return array_keys($array );
    }

    public function prepend($data)
    {
        $array = $this->getArrayCopy();
        array_unshift($array, $data);
        $this->exchangeArray($array);

        return $this;
    }


    public function getJSON()
    {
        return json_encode($this->getArray());
    }

    public function __call($func, $argv)
    {
        if (!is_callable($func) || substr($func, 0, 6) !== 'array_') {
            throw new ShipwireException(__CLASS__.'->'.$func);
        }

        return call_user_func_array($func, array_merge(array($this->getArrayCopy()), $argv));
    }

}
