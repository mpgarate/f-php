<?php
declare(strict_types=1);

require './vendor/autoload.php';

use PHPUnit\Framework\TestCase;

use FPHP\Result;
use FPHP\Option;
use FPHP\UnwrappingErrorException;

class ResultTest extends TestCase {
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

    public function testMap_appliesFunctionToValueForOk() {
        $val = 3;
        $result = Result::ok($val);

        $mapped = $result->map(function($n) {
            return $n + 1;
        });

        $this->assertEquals(Result::ok(4), $mapped);
    }

    public function testMap_returnsErrorForError() {
        $result = Result::error("some reason");

        $mapped = $result->map(function($n) {
            throw new Exception("this should not be reached");
        });

        $this->assertEquals($result, $mapped);
    }

    public function testFlatMap_appliesFunctionToValueForOk() {
        $val = 3;
        $result = Result::ok($val);

        $mapped = $result->flatMap(function($n) {
            return Result::ok($n + 1);
        });

        $this->assertEquals(Result::ok(4), $mapped);
    }

    public function testFlatMap_returnsErrorForError() {
        $result = Result::error("some reason");

        $mapped = $result->flatMap(function($n) {
            throw new Exception("this should not be reached");
        });

        $this->assertEquals($result, $mapped);
    }

    public function testFrom_returnsOkWhenOk() {
        $result = Result::from(function(): int {
            return 42;
        });

        $this->assertEquals(Result::ok(42), $result);
    }

    public function testFrom_returnsErrorForException() {
        $message = "something went wrong";

        $result = Result::from(function() use ($message): int {
            throw new RuntimeException($message);
        });

        $expected_result = Result::error($message);

        $this->assertEquals($expected_result, $result);
    }

    public function testFrom_returnsErrorForExceptionNested() {
        $message_1 = "message 1 sentinel";
        $message_2 = "message 2 sentinel";

        $result = Result::from(function() use ($message_1, $message_2): int {
            $previous = new InvalidArgumentException($message_2);
            throw new RuntimeException($message_1, 0, $previous);
        });

        $expected_result = Result::error($message_1, Result::error($message_2));

        $this->assertEquals($expected_result, $result);
    }

    public function testErrorGetMessage() {
        $message = "message sentinel";
        $error = Result::error($message);

        $this->assertEquals($message, $error->getMessage());
    }

    public function testUnwrap_throwsExceptionForError() {
        $message = "message sentinel";
        $error = Result::error($message);

        $this->expectException(UnwrappingErrorException::class);
        $this->expectExceptionMessage($message);

        $error->unwrap();
    }

    public function testUnwrap_returnsValueForOk() {
        $result = Result::ok(123);
        $this->assertEquals(123, $result->unwrap());
    }

    public function testUnwrapOr_returnsValueForOk() {
        $result = Result::ok(123);
        $this->assertEquals(123, $result->unwrapOr(555));
    }

    public function testUnwrapOr_returnsFallbackForError() {
        $result = Result::error("something bad");
        $this->assertEquals(555, $result->unwrapOr(555));
    }
}
