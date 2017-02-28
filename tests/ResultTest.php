<?php
declare(strict_types=1);

require './vendor/autoload.php';

use PHPUnit\Framework\TestCase;

use FPHP\Result;

function divide(int $x, int $y): Result {
    if ($y === 0) {
        return Result::error('divide by zero');
    }

    return Result::ok($x / $y);
}

class ErrTest extends TestCase {
    public function testErr() {
        $this->assertEquals(Result::ok(1.0), divide(4, 4));
    }
}
