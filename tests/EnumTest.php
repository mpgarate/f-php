<?php
declare(strict_types=1);

require './vendor/autoload.php';

use PHPUnit\Framework\TestCase;

use FPHP\Enum;
use FPHP\IncompleteMatchException;
use FPHP\Predicate;
use FPHP\Result;

class Color {
    use Enum;

    const RED = 1;
    const BLUE = 2;
    const YELLOW = 3;

    public static function valsToNames(): array {
        return [
            self::RED => 'RED',
            self::BLUE => 'BLUE',
            self::YELLOW => 'YELLOW',
        ];
    }
}

class EnumTest extends TestCase {
    public function testEquals() {
        $color_1 = Color::RED();
        $color_2 = Color::RED();

        $this->assertEquals(true, $color_1 === $color_2);
    }

    public function testEnumTypeHint_doesNotThrowError() {
        $color = Color::YELLOW();

        (function(Color $color): Color {
            return $color;
        })($color);

        $this->assertTrue(true);
    }

    public function testEnumGetName_withRawVal() {
        $color = Color::YELLOW;

        $name = Color::getName($color);

        $this->assertEquals('YELLOW', $name);
    }

    public function testEnumGetName_withWrappedVal() {
        $color = Color::YELLOW();

        $name = Color::getName($color);

        $this->assertEquals('YELLOW', $name);
    }

    public function testFromInt_okForValidVal() {
        $color = Color::fromInt(Color::RED);

        $this->assertEquals(Result::ok(Color::RED()), $color);
    }

    public function testFromInt_ErrorForInvalidVal() {
        $color = Color::fromInt(999);

        $this->assertEquals(Result::error("no such value for Color enum"), $color);
    }

    public function testEnumTypeHint_throwsErrorForAnotherType() {
        $this->expectException(TypeError::class);

        $not_color = new stdClass;

        (function(Color $color): Color {
            return $color;
        })($not_color);
    }

    public function testMatchAny_matchesAValue() {
        $color = Color::YELLOW;

        $expected_result = 'result_sentinel';

        $result = Color::matcher($color)
            ->case(Predicate::Any(),
                function() use ($expected_result) {
                    return $expected_result;
                })
            ->match();

        $this->assertEquals($expected_result, $result);
    }

    public function testMatchWorksWithWrappedVal() {
        $color = Color::YELLOW();

        $expected_result = 'result_sentinel';

        $result = Color::matcher($color)
            ->case(Predicate::Any(),
                function() use ($expected_result) {
                    return $expected_result;
                })
                ->match();

        $this->assertEquals($expected_result, $result);
    }

    public function testMatchOr_doesNotMatchDifferentValue() {
        $color = Color::YELLOW;

        $expected_result = 'result_sentinel';

        $result = Color::matcher($color)
            ->case(Predicate::Or(Color::RED, Color::BLUE),
                function() {
                    throw new Exception("this should not be called");
                })
            ->case(Color::YELLOW,
                function() use ($expected_result) {
                    return $expected_result;
                })
            ->match();

        $this->assertEquals($expected_result, $result);
    }

    public function testMatch_throwsExceptionForIncompleteCases() {
        $color = Color::RED;

        $this->expectException(IncompleteMatchException::class);

        $result = Color::matcher($color)
            ->case(Predicate::Or(Color::RED, Color::BLUE),
                function() {
                    throw new Exception("this should not be called");
                })
            // we have not covered Color::YELLOW
            ->match();
    }
}
