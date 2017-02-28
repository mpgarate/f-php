<?php
declare(strict_types=1);

require './vendor/autoload.php';

use PHPUnit\Framework\TestCase;

use FPHP\Iter;

class ChainTest extends TestCase {
    public function testChainTwoThings() {
        $xs = [1, 2, 3, 4, 5 ];

        $expected = [8, 10];

        $result = Iter::chain($xs)
            ->filter(function(int $x): bool {
                return $x > 3;
            })
            ->map(function(int $x): int {
                return $x * 2;
            })
            ->collect();

        $this->assertEquals($expected, $result);
    }

    public function testChainIdentity() {
        $xs = [1, 2, 3, 4, 5 ];

        $expected = [1, 2, 3, 4, 5 ];

        $result = Iter::chain($xs)->collect();

        $this->assertEquals($expected, $result);
    }

    public function testChain_ErrorForMethodMissing() {
        $this->expectException(Error::class);

        $result = Iter::chain([1, 2, 3])
            ->foo()
            ->collect();
    }
}
