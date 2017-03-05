<?php
declare(strict_types=1);

namespace FPHP;

class IncompleteMatchException extends \InvalidArgumentException {}

class MatchCase {
    private $predicate;
    public $callback;

    public function __construct(callable $predicate, callable $callback) {
        $this->predicate = $predicate;
        $this->callback = $callback;
    }

    public function predicate($val): bool {
        return ($this->predicate)($val);
    }
}

class Matcher {
    private $cases = [];

    private $val;
    private $vals_to_names;

    function __construct(int $val, array $vals_to_names) {
        $this->val = $val;
        $this->vals_to_names = $vals_to_names;
    }

    private function isValue($val): bool {
        return is_int($val) && isset($this->vals_to_names[$val]);
    }

    private function getCallbackForVal(int $val): Option {
        return Iter::findFirst($this->cases, function(MatchCase $case) use ($val) {
            return $case->predicate($val);
        })->map(function($case) {
            return $case->callback;
        });
    }

    public function case($predicate, callable $callback): Matcher {
        if ($this->isValue($predicate)) {
            $predicate = Predicate::StrictEquals($predicate);
        }

        $this->cases[] = new MatchCase($predicate, $callback);

        return $this;
    }

    public function matchUnsafe() {
        return $this->getCallbackForVal($this->val)->unwrap()();
    }

    public function match() {
        $missing = Iter::chain(array_keys($this->vals_to_names))
            ->filter(function($val) {
                return $this->getCallbackForVal($val)->isNone();
            })
            ->map(function($val) {
                return $this->vals_to_names[$val];
            })
            ->collect();

        if (!empty($missing)) {
            $missing_str = implode(",", $missing);

            throw new IncompleteMatchException("Missing match case for $missing_str");
        }

        return $this->matchUnsafe();
    }
}

trait Enum {
    abstract public static function valsToNames(): array;

    private static $singleton_instances = [];

    private $val;

    private function __construct(int $val) {
        static::assertValidValue($val);

        $this->val = $val;
    }

    public static function matcher($val): Matcher {
        $val = static::toInt($val);

        static::assertValidValue($val);

        return new Matcher($val, static::valsToNames());
    }

    public static function fromInt(int $val) {
        if (static::isValue($val)) {
            return Result::ok(new static($val));
        }

        return Result::error("no such value for " . __CLASS__ . " enum");
    }

    public static function getName($val): string {
        $val = static::toInt($val);
        return static::valsToNames()[$val];
    }

    /**
     * @throws NoSuchPropertyException
     */
    public static function __callStatic($name, $args) {
        return Option::from(array_flip(static::valsToNames())[$name] ?? null)
            ->map(function(int $val) {
                if (!isset(static::$singleton_instances[$val])) {
                    static::$singleton_instances[$val] = new static($val);
                }

                return static::$singleton_instances[$val];
            })
            ->unwrapOrElse(function() use ($name) {
                throw new NoSuchPropertyException("no property $name for this case class");
            });
    }

    private static function assertValidValue(int $val) {
        if (!static::isValue($val)) {
            throw new \InvalidArgumentException("invalid value for enum");
        }
    }

    private static function isValue(int $val): bool {
        return isset(static::valsToNames()[$val]);
    }

    private static function toInt($val): int {
        if ($val instanceof static) {
            return $val->val;
        }

        return $val;
    }
}
