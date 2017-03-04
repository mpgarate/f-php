<?php
declare(strict_types=1);

require './vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use FPHP\Option;
use FPHP\UnwrappingNoneException;

class OptionTest extends TestCase {
    public function testIsSome_trueForSome() {
        $opt = Option::some('val');
        $this->assertEquals(true, $opt->isSome());
    }

    public function testIsSome_falseForNone() {
        $opt = Option::none();
        $this->assertEquals(false, $opt->isSome());
    }

    public function testIsNone_trueForNone() {
        $opt = Option::none();
        $this->assertEquals(true, $opt->isNone());
    }

    public function testIsNone_falseForSome() {
        $opt = Option::some('val');
        $this->assertEquals(false, $opt->isNone());
    }

    public function testUnwrap_getsValueForSome() {
        $val = 'val_sentinel';
        $opt = Option::some($val);
        $this->assertEquals($val, $opt->unwrap());
    }

    public function testUnwrap_throwsExceptionForNone() {
        $this->expectException(UnwrappingNoneException::class);

        $opt = Option::none();
        $opt->unwrap();
    }

    public function testExpect_getsValueForSome() {
        $message = 'message_sentinel';
        $val = 'val_sentinel';
        $opt = Option::some($val);
        $this->assertEquals($val, $opt->expect($message));
    }

    public function testExpect_throwsExceptionForNone() {
        $message = "message_sentinel";

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage($message);

        $opt = Option::none();
        $opt->expect($message);
    }

    public function testUnwrapOr_returnsValueForSome() {
        $val = 'val_sentinel';
        $default = 'default_sentinel';
        $opt = Option::some($val);
        $this->assertEquals($val, $opt->unwrapOr($default));
    }

    public function testUnwrapOr_returnsFallbackForNone() {
        $val = 'val_sentinel';
        $default = 'default_sentinel';
        $opt = Option::none();
        $this->assertEquals($default, $opt->unwrapOr($default));
    }

    public function testUnwrapOrElse_callsCallbackForNone() {
        $opt = Option::none();
        $callback_sentinel = 'callback_sentinel';

        $this->assertEquals(
            $callback_sentinel,
            $opt->unwrapOrElse(function() use ($callback_sentinel) {
                return $callback_sentinel;
            })
        );
    }

    public function testUnwrapOrElse_doesNotCallCallbackForSome() {
        $val = 'val_sentinel';
        $opt = Option::some($val);

        $callback_sentinel = 'callback_sentinel';

        $this->assertEquals(
            $val,
            $opt->unwrapOrElse(function() use ($callback_sentinel) {
                return $callback_sentinel;
            })
        );
    }

    public function testMap_appliesFunctionToValueForSome() {
        $val = 3;
        $opt = Option::some($val);

        $result = $opt->map(function($n) {
            return $n + 1;
        });

        $this->assertEquals(Option::some(4), $result);
    }

    public function testMap_returnsNoneForNone() {
        $opt = Option::none();

        $result = $opt->map(function($n) {
            throw new Exception("this should not be reached");
        });

        $this->assertEquals(Option::none(), $result);
    }

    public function testFlatMap_appliesFunctionToValueForSome() {
        $val = 3;
        $opt = Option::some($val);

        $result = $opt->flatMap(function($n) {
            return Option::some($n + 1);
        });

        $this->assertEquals(Option::some(4), $result);
    }

    public function testFlatMap_returnsNoneForNone() {
        $opt = Option::none();

        $result = $opt->flatMap(function($n) {
            return Option::some($n + 1);
        });

        $this->assertEquals($opt, $result);
    }

    /**
     * @dataProvider provideForTestConstructFrom
     */
    public function testConstructFrom($val, Option $expected) {
        $opt = Option::from($val);

        $this->assertEquals($expected, $opt);
    }

    public function provideForTestConstructFrom() {
        return [
            [
                $val = 1,
                $expected = Option::some(1),
            ],
            [
                $val = true,
                $expected = Option::some(true),
            ],
            [
                $val = 'false',
                $expected = Option::some('false'),
            ],
            [
                $val = false,
                $expected = Option::some(false),
            ],
            [
                $val = null,
                $expected = Option::none(),
            ],
            [
                $val = 'null',
                $expected = Option::some('null'),
            ],
            [
                $val = '',
                $expected = Option::some(''),
            ],
            [
                $val = 0,
                $expected = Option::some(0),
            ],
            [
                $val = [],
                $expected = Option::some([]),
            ],
            [
                $val = new stdClass,
                $expected = Option::some(new stdClass),
            ],
        ];
    }
}
