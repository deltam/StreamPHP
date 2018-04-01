# StreamPHP

SICP like infinite stream for PHP.

(This is toy library, not practical.)

## Requirements

`php >= 5.6`

## Demo

`demo/integer_stream.php`

```php
function integers_start_from($n)
{
    return Stream::cons($n, function() use ($n) {return integers_start_from($n + 1);});
}
// 自然数の無限ストリーム
$naturals = integers_start_from(1);
echo "natural numbers:\n";
display_line($naturals, 20);

// natural numbers:
// 1 2 3 4 5 6 7 8 9 10 11 12 13 14 15 16 17 18 19 20
```


`demo/prime_stream.php`

```php
/** SICP3.5.2 エラトステネスの篩 */
function sieve($s)
{
    return Stream::cons(
        $s->car(),
        function() use($s) {
            return sieve(
                $s->cdr()->filter(
                    function($n) use($s) {
                        return !divisible($n, $s->car());
                    }));
        });
}

$n2 = $naturals->cdr(); // 2 3 4 5 ...

// 素数の無限ストリーム
$primes = sieve($n2);
echo "primes:\n";
display_line($primes, 20);

// primes:
// 2 3 5 7 11 13 17 19 23 29 31 37 41 43 47 53 59 61 67 71
```


ArrayAccess is implemented.

```php
echo "100th prime:\n";
echo $primes[99] . "\n";

// 100th prime:
// 541
```

## License

Copyright (c) 2018 Masaru MISUMI (deltam@gmail.com).

Licensed under the MIT License (http://www.opensource.org/licenses/mit-license.php)
