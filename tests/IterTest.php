<?php
declare(strict_types=1);

require './vendor/autoload.php';

use PHPUnit\Framework\TestCase;

use FPHP\Iter;
use FPHP\Opt;

class IterTest extends TestCase {
    /**
     * @dataProvider provideForTestFindFirst
     */
    public function testFindFirst($xs, $f, $expected) {
        $result = Iter::findFirst($xs, $f);
        $this->assertEquals($expected, $result);
    }

    public function provideForTestFindFirst() {
        return [
            'always true' => [
                $xs = [5, 6, 7],
                $f = function($x) { return true; },
                $expected = opt::some(5),
            ],
            'specific item' => [
                $xs = [5, 6, 7],
                $f = function($x) { return $x === 6; },
                $expected = opt::some(6),
            ],
            'item not found' => [
                $xs = [5, 6, 7],
                $f = function($x) { return $x === 0; },
                $expected = opt::none(),
            ],
            'no items' => [
                $xs = [],
                $f = function($x) { return $x === 6; },
                $expected = opt::none(),
            ],
        ];
    }

    /**
     * @dataProvider provideForTestMap
     */
    public function testMap($xs, $f, $expected) {
        $result = Iter::map($xs, $f);
        $this->assertEquals($expected, $result);
    }

    public function provideForTestMap() {
        return [
            'multiply by 2' => [
                $xs = [5, 6, 7],
                $f = function($x) { return $x * 2; },
                $expected = [10, 12, 14],
            ],
            'specific item' => [
                $xs = [5, 6, 7],
                $f = function($x) { return $x === 6; },
                $expected = [false, true, false],
            ],
            'return some nulls' => [
                $xs = [5, 6, 7],
                $f = function($x) { return null; },
                $expected = [null, null, null],
            ],
            'no items' => [
                $xs = [],
                $f = function($x) { throw new Exception("I should not be called"); },
                $expected = [],
            ],
        ];
    }

    /**
     * @dataProvider provideForTestFilter
     */
    public function testFilter($xs, $f, $expected) {
        $result = Iter::filter($xs, $f);
        $this->assertEquals($expected, $result);
    }

    public function provideForTestFilter() {
        return [
            'get some values' => [
                $xs = [5, 6, 7],
                $f = function($x) { return $x > 5; },
                $expected = [6, 7],
            ],
            'specific item' => [
                $xs = [5, 6, 7],
                $f = function($x) { return $x === 6; },
                $expected = [6],
            ],
            'nothing matches' => [
                $xs = [5, 6, 7],
                $f = function($x) { return false; },
                $expected = [],
            ],
            'no items' => [
                $xs = [],
                $f = function($x) { throw new Exception("I should not be called"); },
                $expected = [],
            ],
        ];
    }

    /**
     * @dataProvider provideForTestFold
     */
    public function testFold($xs, $initial, $f, $expected) {
        $result = Iter::fold($xs, $initial, $f);
        $this->assertEquals($expected, $result);
    }

    public function provideForTestFold() {
        return [
            'add them up' => [
                $xs = [1, 2, 3],
                $initial = 0,
                $f = function($acc, $x) { return $acc + $x; },
                $expected = 6,
            ],
            'multiply them together' => [
                $xs = [2, 2, 2],
                $initial = 1,
                $f = function($acc, $x) { return $acc * $x; },
                $expected = 8,
            ],
            'no items' => [
                $xs = [],
                $initial = 42,
                $f = function($acc, $x) { throw new Exception("I should not be called"); },
                $expected = 42,
            ],
        ];
    }
}
