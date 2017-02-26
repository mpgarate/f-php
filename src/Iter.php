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
}
