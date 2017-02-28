<?php
declare(strict_types=1);

namespace FPHP;

abstract class Iter {
    public static function findFirst(array $xs, callable $f): Option {
        foreach ($xs as $x) {
            if ($f($x)) {
                return Opt::some($x);
            }
        }

        return Opt::none();
    }

    public static function map(array $xs, callable $f): array {
        return array_map($f, $xs);
    }

    public static function filter(array $xs, callable $f): array {
        return array_values(array_filter($xs, $f));
    }

    public static function chain(array $xs): Chain {
        return new Chain($xs);
    }

    public static function fold(array $xs, $initial, callable $fn) {
        return array_reduce($xs, $fn, $initial);
    }
}

