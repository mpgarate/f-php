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

    public function testError() {
        $error_message = "something went wrong";

        $result = Result::error($error_message);
        $this->assertEquals(Result::error($error_message), $result);
    }

    public function testError_nested() {
        $error_1_message = "file permissions denied write access";
        $error_2_message = "could not write data to disk";

        $error_1 = Result::error($error_1_message);
        $error_2 = Result::error($error_2_message, $error_1);

        $this->assertEquals(
            Result::error($error_2_message, Result::error($error_1_message)),
            $error_2
        );
    }

    public function testIsOk() {
        $ok = Result::ok(1.0);
        $error = Result::error("some reason");

        $this->assertEquals(true, $ok->isOk());
        $this->assertEquals(false, $error->isOk());
    }

    public function testIsError() {
        $ok = Result::ok(1.0);
        $error = Result::error("some reason");

        $this->assertEquals(false, $ok->isError());
        $this->assertEquals(true, $error->isError());
    }
}
