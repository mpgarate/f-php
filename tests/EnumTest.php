<?php
declare(strict_types=1);

require './vendor/autoload.php';

use PHPUnit\Framework\TestCase;

use FPHP\Enum;
use FPHP\Predicate;

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
        $color = Color::get(Color::YELLOW);

        (function(Color $color): Color {
            return $color;
        })($color);

        $this->assertTrue(true);
    }

    public function testEnumTypeHint_throwsErrorForAnotherType() {
        $this->expectException(TypeError::class);

        $not_color = new stdClass;

        (function(Color $color): Color {
            return $color;
        })($not_color);
    }

    public function testMatchAny_matchesAValue() {
        $color = Color::get(Color::YELLOW);

        $expected_result = 'result_sentinel';

        $result = $color->match([
            Predicate::Any(), function() use ($expected_result) {
                return $expected_result;
            }]
        );

        $this->assertEquals($expected_result, $result);
    }

    public function testMatchOr_doesNotMatchDifferentValue() {
        $color = Color::get(Color::YELLOW);

        $expected_result = 'result_sentinel';

        $result = $color->match([
            Predicate::Or(Color::RED, Color::BLUE), function() {
                return 'unexpected_result';
            }], [
            Color::YELLOW, function() use ($expected_result) {
                return $expected_result;
            }]
        );

        $this->assertEquals($expected_result, $result);
    }
}
