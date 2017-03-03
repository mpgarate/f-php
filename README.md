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
```

## Tuple
```php
use FPHP\Tuple;

$point = new Tuple(3, 4);

list($x, $y) = $point;

assert($x === 3);

assert($point[0] === 3);
```
