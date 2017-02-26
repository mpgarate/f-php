<?php
declare(strict_types=1);

namespace FPHP;

require './vendor/autoload.php';

trait Matchable {
    abstract public static function vals(): array;
    abstract public static function name($val): array;

    private function getCallbackForVal(int $val, array $cases): Option {
        return Iter::findFirst($cases, function(array $case) use ($val) {
            list($predicate, $callback) = $case;
            return $predicate === $val || (is_callable($predicate) && $predicate($val));
        })->map(function($case) {
            return $case[1]; // the callback
        });
    }

    public function match(...$cases) {
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
