<?php
declare(strict_types=1);

namespace FPHP;

abstract class Result {
    public static function error(string $message, Error $cause = null): Result {
        return new Error($message, $cause);
    }

    public static function ok($value): Result {
        return new Ok($value);
    }
}

class Error extends Result {
    private $message;
    private $cause;

    function __construct(string $message, Error $cause = null) {
        $this->message = $message;
        $this->cause = $cause;
    }
}

class Ok extends Result {
    private $val;

    function __construct($val) {
        $this->val = $val;
    }
}
