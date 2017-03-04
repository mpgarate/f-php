<?php
declare(strict_types=1);

require './vendor/autoload.php';

use PHPUnit\Framework\TestCase;

use FPHP\Enum;
use FPHP\Predicate;
use FPHP\IncompleteMatchException;

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
            ->when(Predicate::Any(),
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
            ->when(Predicate::Any(),
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
            ->when(Predicate::Or(Color::RED, Color::BLUE),
                function() {
                    throw new Exception("this should not be called");
                })
            ->when(Color::YELLOW,
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
            ->when(Predicate::Or(Color::RED, Color::BLUE),
                function() {
                    throw new Exception("this should not be called");
                })
            // we have not covered Color::YELLOW
            ->match();
    }
}
