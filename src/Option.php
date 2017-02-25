<?php
namespace Option;

abstract class Opt {
    public static function none(): Option {
        return new OptionNone();
    }

    public static function some($val): Option {
        return new OptionSome($val);
    }
}

class UnwrappingNoneException extends \Exception {};

interface Option {
    function unwrap();
    function expect(string $message);
    function unwrapOr($fallback_val);
    function map(callable $f): Option;
    function flatMap(callable $f): Option;
}

class OptionNone implements Option {
    public function unwrap() {
        throw new UnwrappingNoneException("unwrapping a none value");
    }

    public function expect(string $message) {
        throw new \Exception($message);
    }

    public function unwrapOr($fallback_val) {
        return $fallback_val;
    }

    public function map(callable $callback): Option {
        return $this;
    }

    public function flatMap(callable $f): Option {
        return $this;
    }
}

trait HoldsValue {
    private $val;

    function __construct($val) {
        $this->val = $val;
    }
}

class OptionSome implements Option {
    use HoldsValue;

    public function unwrap() {
        return $this->val;
    }

    public function expect(string $message) {
        return $this->val;
    }

    public function unwrapOr($fallback_val) {
        return $this->val;
    }

    public function map(callable $callback): Option {
        return Opt::some($callback($this->val));
    }

    public function flatMap(callable $callback): Option {
        return $callback($this->val);
    }
}
