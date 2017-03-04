<?php
declare(strict_types=1);

namespace FPHP;

class Chain {
    private $initial_xs;
    private $fn_queue = [];

    function __construct(array $xs) {
        $this->initial_xs = $xs;
    }

    private function enqueue(callable $fn) {
        $this->fn_queue[]= $fn;
    }

    public function __call(string $name, array $arguments): Chain {
        $this->enqueue(function(array $xs) use ($name, $arguments) {
            return Iter::$name($xs, ...$arguments);
        });

        return $this;
    }

    public function collect(): array {
        return Iter::fold($this->fn_queue, $this->initial_xs, function(array $xs, callable $fn) {
            return $fn($xs);
        });
    }
}
