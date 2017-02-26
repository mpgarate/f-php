<?php
declare(strict_types=1);

require './vendor/autoload.php';

use PHPUnit\Framework\TestCase;

use FPHP\Iter;
use FPHP\Opt;

class IterTest extends TestCase {
    /**
     * @dataProvider provideForFindFirst
     */
    public function testFindFirst($xs, $f, $expected) {
        $result = Iter::findFirst($xs, $f);
        $this->assertEquals($expected, $result);
    }

    public function provideForFindFirst() {
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
}
