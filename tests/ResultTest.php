<?php
declare(strict_types=1);

require './vendor/autoload.php';

use PHPUnit\Framework\TestCase;

use FPHP\Result;

class ErrTest extends TestCase {
    public function testOk() {
        $result = Result::ok(1.0);
        $this->assertEquals(Result::ok(1.0), $result);
    }
}
