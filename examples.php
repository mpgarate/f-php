<?php
declare(strict_types=1);

require './vendor/autoload.php';

// ##########################################
// ##  e  x  a  m  p  l  e  s  .  p  h  p  ##
// ##########################################
//
// Series of examples to be included in README.md
//
// Running this file should emit no warnings or errors.

/**
 * Option
 */

use FPHP\Option;

function divide(int $numerator, int $denominator): Option {
    if ($denominator === 0) {
        return Option::none();
    }

    return Option::some($numerator / $denominator);
}

assert(divide(5, 0)->unwrapOr(0) === 0);
assert(divide(5, 2)->isSome() === true);

$result = divide(10, 2)->map(function(float $n) {
    return $n * 3;
})->unwrap();
assert(15.0 === $result);

// construct from values that could be null
$opt = Option::from(null);
assert($opt == Option::none());
$opt = Option::from(123);
assert($opt == Option::some(123));

/**
 * Result
 */
use FPHP\Result;

$error_result = Result::from(function() {
    throw new Exception("something went wrong");
});

assert($error_result->isError() === true);
assert($error_result->getMessage() === "something went wrong");

$ok_result = Result::from(function() {
    return 42;
})->map(function($n) {
    return $n + 8;
});;

assert($ok_result->isOk() === true);
assert($ok_result == Result::ok(50));

/**
 * Tuple
 */

use FPHP\Tuple;

$point = new Tuple(3, 4);

list($x, $y) = $point;

assert($x === 3);

assert($point[0] === 3);

/**
 * Case Class
 */
use FPHP\CaseClass;

class Point {
    use CaseClass;

    private $data;

    function __construct(int $x, int $y) {
        $this->data = [
            'x' => $x,
            'y' => $y,
        ];
    }
}

$point = new Point(3, 4);

assert($point->x() === 3);
assert($point->y() === 4);

/**
 * Enum
 */
use FPHP\Enum;
use FPHP\Predicate;
use FPHP\IncompleteMatchException;

class Color {
    use Enum;

    const RED = 1;
    const BLUE = 2;

    public static function valsToNames(): array {
        return [
            self::RED => 'RED',
            self::BLUE => 'BLUE',
        ];
    }
}

$color = Color::RED();

// strict equality
assert($color === Color::RED());

// basic pattern matching
$result = Color::matcher($color)
    ->case(Color::RED,
        function() {
            return "got red";
        })
    ->case(Predicate::Any(),
        function() {
            return "got other";
        })
    ->match();

assert($result === "got red");

// exception thrown when match cases do not cover all values
$e = null;
try {
    Color::matcher($color)
        ->case(Color::RED, function() {})
        // BLUE case not covered
        ->match();
} catch (IncompleteMatchException $ex) {
    $e = $ex;
}

assert($e instanceof IncompleteMatchException);

