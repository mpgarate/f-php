<?php
declare(strict_types=1);

namespace FPHP;

class NoSuchPropertyException extends \Exception {}

trait Tuple {
    private $data;

    public function destructure(): array {
        return array_values($this->data);
    }

    /**
     * @throws NoSuchPropertyException
     */
    public function __call($name, $args) {
        return Opt::from($this->data[$name] ?? null) 
            ->unwrapOrElse(function() use ($name) {
                throw new NoSuchPropertyException("no data named $name for this tuple");
            });
    }
}
