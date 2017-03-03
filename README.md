# functional-php
php toys inspired by function programming

## Run tests
```sh
./vendor/bin/phpunit tests/
```

## Option
```php
use FPHP\Opt;
use FPHP\Option;

function divide(int $numerator, int $denominator): Option {
    if ($denominator === 0) {
        return Opt::none();
    }

    return Opt::some($numerator / $denominator);
}

assert(divide(5, 0)->unwrapOr(0) === 0);
assert(divide(5, 2)->isSome() === true);

$result = divide(10, 2)->map(function(float $n) {
    return $n * 3;
})->unwrap();
assert(15.0 === $result);

$items = ['a' => 1, 'b' => 2];
$item_a = Opt::from($items['a'] ?? null)->unwrapOr(3);
assert(1 === $item_a);
$item_c = Opt::from($items['c'] ?? null)->unwrapOr(3);
assert(3 === $item_c);
```

## Tuple
```php
use FPHP\Tuple;

$point = new Tuple(3, 4);

list($x, $y) = $point;

assert($x === 3);

assert($point[0] === 3);
```

## Case Class
```php
use FPHP\CaseClass;

class Point {
    use CaseClass;

    private $data;

    function __construct(int $x, int $y) {
        $this->data = [
            'x' => $x,
            'y' => $y,
        ];
    }
}

$point = new Point(3, 4);

assert($point->x() === 3);
assert($point->y() === 4);
```
