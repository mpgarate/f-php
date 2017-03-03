<?php
declare(strict_types=1);

require './vendor/autoload.php';

use PHPUnit\Framework\TestCase;

use FPHP\Tuple;

class TupleTest extends TestCase {
    public function testTuple() {
        $x = 3;
        $y = 5;

        $point = new Tuple($x, $y);

        list($x_1, $y_1) = $point;

        $this->assertEquals($x, $x_1);
        $this->assertEquals($y, $y_1);

        $this->assertEquals($x, $point[0]);
        $this->assertEquals($y, $point[1]);
    }

    public function testTuple_hasArbitraryLength() {
        $sentinel = 999;

        $tuple = new Tuple(0, 1, 2, 3, 4, $sentinel);

        $this->assertEquals($sentinel, $tuple[5]);
    }

    public function testTuple_cannotBeModifiedWithUnset() {
        $tuple = new Tuple(1, 2);

        $this->expectException(\BadMethodCallException::class);

        unset($tuple[0]);
    }

    public function testTuple_cannotBeModifiedWithAssignment() {
        $tuple = new Tuple(1, 2);

        $this->expectException(\BadMethodCallException::class);

        $tuple[0] = 999;
    }
}
