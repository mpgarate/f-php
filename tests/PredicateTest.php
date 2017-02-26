<?php
declare(strict_types=1);

require './vendor/autoload.php';

use PHPUnit\Framework\TestCase;

use FPHP\Predicate;

class PredicateTest extends TestCase {
    /**
     * @dataProvider provideForTestOr
     */
    public function testOr($predicate_args, $input, $expected) {
        $result = Predicate::Or(...$predicate_args)($input);

        $this->assertEquals($expected, $result);
    }

    public function provideForTestOr() {
        return [
            'true for valid input' => [
                $predicate_args = [1, 2],
                $input = 2,
                $expected = true,
            ],
            'false for type mismatch' => [
                $predicate_args = [1, 2],
                $input = '2',
                $expected = false,
            ],
            'false for valid input' => [
                $predicate_args = [1, 2],
                $input = 3,
                $expected = false,
            ],
            'false for no predicate args' => [
                $predicate_args = [],
                $input = null,
                $expected = false,
            ],
        ];
    }

    /**
     * @dataProvider provideForTestNot
     */
    public function testNot($predicate_args, $input, $expected) {
        $result = Predicate::Not(...$predicate_args)($input);

        $this->assertEquals($expected, $result);
    }

    public function provideForTestNot() {
        return [
            'false for valid input' => [
                $predicate_args = [1, 2],
                $input = 2,
                $expected = false,
            ],
            'true for type mismatch' => [
                $predicate_args = [1, 2],
                $input = '2',
                $expected = true,
            ],
            'true for valid input' => [
                $predicate_args = [1, 2],
                $input = 3,
                $expected = true,
            ],
            'true for no predicate args' => [
                $predicate_args = [],
                $input = null,
                $expected = true,
            ],
        ];
    }

    /**
     * @dataProvider provideForTestAny
     */
    public function testAny($predicate_args, $input, $expected) {
        $result = Predicate::Any(...$predicate_args)($input);

        $this->assertEquals($expected, $result);
    }

    public function provideForTestAny() {
        return [
            'true for valid input' => [
                $predicate_args = [1, 2],
                $input = 2,
                $expected = false,
            ],
            'true for type mismatch' => [
                $predicate_args = [1, 2],
                $input = '2',
                $expected = true,
            ],
            'true for valid input' => [
                $predicate_args = [1, 2],
                $input = 3,
                $expected = true,
            ],
            'true for no predicate args' => [
                $predicate_args = [],
                $input = null,
                $expected = true,
            ],
        ];
    }

    /**
     * @dataProvider provideForTestStrictEquals
     */
    public function testStrictEquals($predicate_arg, $input, $expected) {
        $result = Predicate::StrictEquals($predicate_arg)($input);

        $this->assertEquals($expected, $result);
    }

    public function provideForTestStrictEquals() {
        return [
            'false for non equal' => [
                $predicate_arg = 1,
                $input = 2,
                $expected = false,
            ],
            'false for type mismatch' => [
                $predicate_arg = 1,
                $input = '1',
                $expected = false,
            ],
            'true for equal' => [
                $predicate_arg = 6,
                $input = 6,
                $expected = true,
            ],
        ];
    }
}
