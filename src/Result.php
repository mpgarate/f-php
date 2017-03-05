<?php
declare(strict_types=1);

namespace FPHP;

class UnwrappingErrorException extends \Exception {};

abstract class Result {
    public static function error(string $message, Error $cause = null): Error {
        return new Error($message, Option::from($cause));
    }

    public static function ok($value): Ok {
        return new Ok($value);
    }

    public static function from(callable $f): Result {
        try {
            return Result::ok($f());
        } catch (\Exception $e) {
            return Error::fromException($e);
        }
    }

    abstract public function isOk(): bool;

    abstract public function isError(): bool;

    abstract public function map(callable $f): Result;

    abstract public function flatMap(callable $f): Result;

    abstract public function unwrap();

    abstract public function unwrapOr($default);

    abstract public function unwrapOrElse(callable $f);
}

class Error extends Result {
    private $message;
    private $cause;

    /**
     * @param strinc message
     * @param Option<Error>
     */
    function __construct(string $message, Option $cause) {
        $this->message = $message;
        $this->cause = $cause;
    }

    public static function fromException(\Exception $e): Error {
        return new Error(
            get_class($e) . ": " . $e->getMessage(),
            Option::from($e->getPrevious())->map(function(\Exception $prev) {
                return Error::fromException($prev);
            })
        );
    }

    public function getMessage(): string {
        return $this->message;
    }

    public function isOk(): bool {
        return false;
    }

    public function isError(): bool {
        return true;
    }

    public function map(callable $f): Result {
        return $this;
    }

    public function flatMap(callable $f): Result {
        return $this;
    }

    public function unwrap() {
        throw new UnwrappingErrorException($this->message);
    }

    public function unwrapOr($default) {
        return $default;
    }

    public function unwrapOrElse(callable $f) {
        return $f();
    }
}

class Ok extends Result {
    private $val;

    function __construct($val) {
        $this->val = $val;
    }

    public function isOk(): bool {
        return true;
    }

    public function isError(): bool {
        return false;
    }

    public function map(callable $f): Result {
        return Result::ok($f($this->val));
    }

    public function flatMap(callable $f): Result {
        return $f($this->val);
    }

    public function unwrap() {
        return $this->val;
    }

    public function unwrapOr($default) {
        return $this->val;
    }

    public function unwrapOrElse(callable $f) {
        return $this->val;
    }
}
