<?php
declare(strict_types=1);

require './vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use FPHP\Opt;
use FPHP\Option;
use FPHP\UnwrappingNoneException;

class OptionTest extends TestCase {
    public function testIsSome_trueForSome() {
        $opt = Opt::some('val');
        $this->assertEquals(true, $opt->isSome());
    }

    public function testIsSome_falseForNone() {
        $opt = Opt::none();
        $this->assertEquals(false, $opt->isSome());
    }

    public function testIsNone_trueForNone() {
        $opt = Opt::none();
        $this->assertEquals(true, $opt->isNone());
    }

    public function testIsNone_falseForSome() {
        $opt = Opt::some('val');
        $this->assertEquals(false, $opt->isNone());
    }

    public function testUnwrap_getsValueForSome() {
        $val = 'val_sentinel';
        $opt = Opt::some($val);
        $this->assertEquals($val, $opt->unwrap());
    }

    public function testUnwrap_throwsExceptionForNone() {
        $this->expectException(UnwrappingNoneException::class);

        $opt = Opt::none();
        $opt->unwrap();
    }

    public function testExpect_getsValueForSome() {
        $message = 'message_sentinel';
        $val = 'val_sentinel';
        $opt = Opt::some($val);
        $this->assertEquals($val, $opt->expect($message));
    }

    public function testExpect_throwsExceptionForNone() {
        $message = "message_sentinel";

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage($message);

        $opt = Opt::none();
        $opt->expect($message);
    }

    public function testUnwrapOr_returnsValueForSome() {
        $val = 'val_sentinel';
        $default = 'default_sentinel';
        $opt = Opt::some($val);
        $this->assertEquals($val, $opt->unwrapOr($default));
    }

    public function testUnwrapOr_returnsFallbackForNone() {
        $val = 'val_sentinel';
        $default = 'default_sentinel';
        $opt = Opt::none();
        $this->assertEquals($default, $opt->unwrapOr($default));
    }

    public function testUnwrapOrElse_callsCallbackForNone() {
        $opt = Opt::none();
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
        $opt = Opt::some($val);

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
        $opt = Opt::some($val);

        $result = $opt->map(function($n) {
            return $n + 1;
        });

        $this->assertEquals(Opt::some(4), $result);
    }

    public function testMap_returnsNoneForNone() {
        $opt = Opt::none();

        $result = $opt->map(function($n) {
            return $n + 1;
        });

        $this->assertEquals(Opt::none(), $result);
    }

    public function testFlatMap_appliesFunctionToValueForSome() {
        $val = 3;
        $opt = Opt::some($val);

        $result = $opt->flatMap(function($n) {
            return Opt::some($n + 1);
        });

        $this->assertEquals(Opt::some(4), $result);
    }

    public function testFlatMap_returnsNoneForNone() {
        $opt = Opt::none();

        $result = $opt->flatMap(function($n) {
            return Opt::some($n + 1);
        });

        $this->assertEquals($opt, $result);
    }

    /**
     * @dataProvider provideForTestConstructFrom
     */
    public function testConstructFrom($val, Option $expected) {
        $opt = Opt::from($val);

        $this->assertEquals($expected, $opt);
    }

    public function provideForTestConstructFrom() {
        return [
            [
                $val = 1,
                $expected = Opt::some(1),
            ],
            [
                $val = true,
                $expected = Opt::some(true),
            ],
            [
                $val = 'false',
                $expected = Opt::some('false'),
            ],
            [
                $val = false,
                $expected = Opt::some(false),
            ],
            [
                $val = null,
                $expected = Opt::none(),
            ],
            [
                $val = 'null',
                $expected = Opt::some('null'),
            ],
            [
                $val = '',
                $expected = Opt::some(''),
            ],
            [
                $val = 0,
                $expected = Opt::some(0),
            ],
            [
                $val = [],
                $expected = Opt::some([]),
            ],
            [
                $val = new stdClass,
                $expected = Opt::some(new stdClass),
            ],
        ];
    }
}
