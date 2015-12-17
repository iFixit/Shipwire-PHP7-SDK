<?php
namespace CharityRoot;

class ShipwireCollection extends ShipwireItem {

    function __construct($input = [], $flags = 0, $iterator_class = Shipwire::ITERATOR)
    {
        parent::__construct($input, $flags, $iterator_class);
    }

    public function sortOnKey($key, $order = self::DESC, $flag = SORT_REGULAR) {
        $sort = [];
        $missing = [];

        foreach ($this as $index => $item) {
            $item_key;
            if (is_array($item)) {
                $item_key = $item[$key];
            }
            else if (is_object($item)) {
                $item_key = $item->$key;
            }

            if(!isset($item_key)) {
                $missing[] = $item;
                continue;
            }

            $item_key = self::_sanitizeKey($item_key);
            $sort[$item_key . '_' . $index] = $item;
        }

        ksort($sort, $flag);
        $sorted = [];
        foreach ($sort as $original_key => $item) {
            $key_parts = explode('_', $original_key);
            $original_key = $key_parts[1];
            $sorted[$original_key] = $item;
        }

        $sorted = array_merge($sorted, $missing);
        if ($order == self::DESC) {
            $sorted = array_reverse($sorted, true);
        }

        $this->exchangeArray($sorted);
    }
    protected function _filterEquals($value, $actual, $index)
    {
        if ($value == $actual) {
            return;
        }

        $this->removeKey($index);
    }

    protected function _filterNotEqual($value, $actual, $index)
    {
        if ($value != $actual) {
            return;
        }

        $this->removeKey($index);
    }

    protected function _filterEqualsStrict($value, $actual, $index)
    {
        if ($value === $actual) {
            return;
        }

        $this->removeKey($index);
    }

    protected function _filterNotEqualStrict($value, $actual, $index)
    {
        if ($value !== $actual) {
            return;
        }

        $this->removeKey($index);
    }

    protected function _filterInArray($value, $actual, $index)
    {
        if (in_array($actual, $value)) {
            return;
        }

        $this->removeKey($index);
    }

    public function filter($key, $operator, $value)
    {
        foreach ($this as $index => $item) {
            if (is_array($item)) {
                $actual = $item[$key];
            }
            else if (is_object($item)) {
                $actual = $item->$key;
            }

            switch ($operator) {
                case '=':
                    $this->_filterEquals($value, $actual, $index);
                    break;
                case '!=':
                case '<>':
                    $this->_filterNotEqual($value, $actual, $index);
                    break;
                case '!==':
                    $this->_filterNotEqualStrict($value, $actual, $index);
                    break;
                case '===':
                    $this->_filterEqualsStrict($value, $actual, $index);
                    break;
                case 'in':
                    $this->_filterInArray($value, $actual, $index);
                    break;
            }

        }

    }

    public function paginate($page = 1, $items_per_page = 20)
    {
        $array = $this->getArray();
        $array = array_chunk($array, $items_per_page, true);
        $this->exchangeArray($array);
        $this->pages = $this->count();
        $this->current_page = $page;
    }

    public function getCurrentPage()
    {
        $page = $this->get($this->current_page - 1);
        if (!$page) {
            return;
        }

        return new ShipwireItems($page);
    }

    protected static function _sanitizeKey($string = '') {
        $string = preg_replace('/[^\w\-]+/u', '-', $string);
        $r = mb_strtolower(preg_replace('/--+/u', '-', $string), 'UTF-8');
        $url = rtrim($r, '-');
        $url = ltrim($url, '-');
        return $url;
    }

}
