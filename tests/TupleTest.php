<?php
declare(strict_types=1);

require './vendor/autoload.php';

use PHPUnit\Framework\TestCase;

use FPHP\Tuple;
use FPHP\NoSuchPropertyException;

class Point {
    use Tuple;

    private $data;

    function __construct(int $x, int $y) {
        $this->data = [
            'x' => $x,
            'y' => $y,
        ];
    }
}

class TupleTest extends TestCase {
    public function testTuple() {
        $x = 3;
        $y = 4;

        $point = new Point($x, $y);

        list($x_1, $y_1) = $point->destructure();

        $this->assertEquals($x, $x_1);
        $this->assertEquals($y, $y_1);

        $this->assertEquals($x, $point->x());
        $this->assertEquals($y, $point->y());
    }

    public function testTuple_noSuchProperty() {
        $point = new Point(1, 2);

        $this->expectException(NoSuchPropertyException::class);

        $point->foo();
    }
}
