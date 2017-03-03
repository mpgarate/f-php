<?php
declare(strict_types=1);

namespace FPHP;

class NoSuchPropertyException extends \Exception {}

abstract class CaseClass implements \ArrayAccess {
    public function destructure(): array {
        return array_values($this->data);
    }

    /**
     * @throws NoSuchPropertyException
     */
    public function __call($name, $args) {
        return Opt::from($this->data[$name] ?? null) 
            ->unwrapOrElse(function() use ($name) {
                throw new NoSuchPropertyException("no property $name for this case class");
            });
    }

    public function offsetExists($offset) {
        return isset(array_values($this->data)[$offset]);
    }

    public function offsetGet($offset) {
        return array_values($this->data)[$offset];
    }

    public function offsetSet($offset, $value) {
        throw new \BadMethodCallException();
    }

    public function offsetUnset($offset) {
        throw new \BadMethodCallException();
    }
}
