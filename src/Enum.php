<?php
declare(strict_types=1);

namespace FPHP;

require './vendor/autoload.php';

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

trait Matchable {
    abstract public static function vals(): array;
    abstract public static function isValue($val): array;
    abstract public static function name($val): array;

    private function getCallbackForVal(int $val, array $cases): Option {
        return Iter::findFirst($cases, function(MatchCase $case) use ($val) {
            return $case->predicate($val);
        })->map(function($case) {
            return $case->callback;
        });
    }

    public function match(...$cases) {
        $cases = Iter::map($cases, function($case) {
            list($predicate, $callback) = $case;

            if (self::isValue($predicate)) {
                return new MatchCase(Predicate::StrictEquals($predicate), $callback);
            }

            return new MatchCase($predicate, $callback);
        });

        $missing = [];
        foreach (static::vals() as $val) {
            if ($this->getCallbackForVal($val, $cases)->isNone()) {
                $missing[]= static::name($val);
            }
        }

        if (!empty($missing)) {
            $missing_str = implode(",", $missing);

            throw new \InvalidArgumentException("Missing match case for $missing_str");
        }

        $callback = $this->getCallbackForVal($val, $cases)->unwrap();

        return $callback();
    }
}

trait Enum {
    use Matchable;

    abstract public static function valsToNames(): array;

    private $val;

    private function __construct(int $val) {
        if (!$this->isValidValue($val)) {
            throw new \InvalidArgumentException("invalid value for enum");
        }

        $this->val = $val;
    }

    public static function vals(): array {
        return array_keys(static::valsToNames());
    }

    public static function isValue($val): bool {
        return is_int($val) && isset(static::valsToNames()[$val]);
    }

    public static function names(): array {
        return array_values(static::valsToNames());
    }

    public static function name($val): string {
        if (!static::isValidValue($val)) {
            throw new \InvalidArgumentException("invalid value for enum");
        }

        return static::valsToNames()[$val];
    }

    public static function isValidValue(int $val): bool {
        return isset(static::valsToNames()[$val]);
    }
}
