<?php

require './vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use FPHP\Opt;
use FPHP\UnwrappingNoneException;

class OptionTest extends TestCase {
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
        $fallback = 'fallback_sentinel';
        $opt = Opt::some($val);
        $this->assertEquals($val, $opt->unwrapOr($fallback));
    }

    public function testUnwrapOr_returnsFallbackForNone() {
        $val = 'val_sentinel';
        $fallback = 'fallback_sentinel';
        $opt = Opt::none();
        $this->assertEquals($fallback, $opt->unwrapOr($fallback));
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
}
