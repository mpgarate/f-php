<?php
declare(strict_types=1);

namespace FPHP;

final class Tuple implements \ArrayAccess {
    private $data;

    function __construct(...$data) {
        $this->data = $data;
    }

    public function offsetExists($offset) {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset) {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value) {
        throw new \BadMethodCallException();
    }

    public function offsetUnset($offset) {
        throw new \BadMethodCallException();
    }
}
