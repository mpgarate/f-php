# functional-php
php toys inspired by function programming

## Run tests
```sh
./vendor/bin/phpunit tests/
```

## Option
```php
function divide(int $numerator, int $denominator): Option {
    if ($numerator === 0) {
        return Opt::none();
    }

    return Opt::some($numerator / $denominator);
}

// prints 0
echo divide(5, 0)->unwrapOr(0); 

// prints true
echo divide(5, 2)->isSome() ? 'true' : 'false;    
```

## Tuple
```php

$point = new Tuple(3, 4);

list($x, $y) = $point;

// prints true
echo $x == 3 ? 'true' : 'false';

// prints true
echo $point[0] == 3 ? 'true' : 'false';
```
