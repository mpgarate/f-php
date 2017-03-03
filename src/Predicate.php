<?php
declare(strict_types=1);

namespace FPHP;

abstract class Predicate {
    public static function Any(): callable {
        return function($candidate) { return true; };
    }

    public static function StrictEquals($x1): callable {
        return function($x2) use ($x1): bool {
            return $x1 === $x2;
        };
    }

    public static function Or(...$acceptable_values): callable {
        return function($candidate) use ($acceptable_values): bool {
            return Iter::findFirst($acceptable_values, Predicate::StrictEquals($candidate))
                ->isSome();
        };
    }

    public static function Not(...$unacceptable_values): callable {
        return function($candidate) use ($unacceptable_values): bool {
            return Iter::findFirst($unacceptable_values, Predicate::StrictEquals($candidate))
                ->isNone();
        };
    }
}
