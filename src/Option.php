<?php
declare(strict_types=1);

namespace FPHP;

abstract class Opt {
    public static function none(): Option {
        return new None();
    }

    public static function some($val): Option {
        return new Some($val);
    }
}

class UnwrappingNoneException extends \Exception {};

interface Option {
    /**
     * @throws UnwrappingNoneException
     *
     * @return mixed the value if present
     */
    function unwrap();

    /**
     * @param string message to use in exception
     *
     * @throws Exception
     *
     * @return mixed the value if present
     */
    function expect(string $message);

    /**
     * @param mixed default the fallback value
     *
     * @return mixed the value if present, else the provided default
     */
    function unwrapOr($default);

    /**
     * @param callable returning a default value
     *
     * @return mixed the value if present, else the result from running the
     *   provided callback. 
     */
    function unwrapOrElse(callable $f);

    /**
     * @param callable returning a new value
     *
     * @return Option None or Some wrapping the result of the callback
     */
    function map(callable $f): Option;

    /**
     * @param callable returning an Option
     *
     * @return Option None or the result of the callback
     */
    function flatMap(callable $f): Option;

    /*
     * @return bool is there a value?
     */
    function isSome(): bool;

    /**
     * @return bool is there no value?
     */
    function isNone(): bool;
}

class None implements Option {
    public function unwrap() {
        throw new UnwrappingNoneException("unwrapping a none value");
    }

    public function expect(string $message) {
        throw new \Exception($message);
    }

    public function unwrapOr($default) {
        return $default;
    }

    public function unwrapOrElse(callable $f) {
        return $f();
    }

    public function map(callable $f): Option {
        return $this;
    }

    public function flatMap(callable $f): Option {
        return $this;
    }

    public function isSome(): bool {
        return false;
    }

    public function isNone(): bool {
        return true;
    }
}

trait HoldsValue {
    private $val;

    function __construct($val) {
        $this->val = $val;
    }
}

class Some implements Option {
    use HoldsValue;

    public function unwrap() {
        return $this->val;
    }

    public function expect(string $message) {
        return $this->val;
    }

    public function unwrapOr($default) {
        return $this->val;
    }

    public function unwrapOrElse(callable $f) {
        return $this->val;
    }

    public function map(callable $f): Option {
        return Opt::some($f($this->val));
    }

    public function flatMap(callable $f): Option {
        return $f($this->val);
    }

    public function isSome(): bool {
        return true;
    }

    public function isNone(): bool {
        return false;
    }
}
