<?php
require __DIR__ . "/../vendor/autoload.php";
require __DIR__ . "/_util.php";

use StreamPHP\Stream;

// 自然数の無限ストリーム
$naturals = Stream::iterate(function($n) {return $n+1;}, 1);
echo "natural numbers:\n";
display_line($naturals, 20);


function divisible($a, $b) {
    return 0 == $a % $b;
}

// 7の倍数を除外したストリーム
$no_sevens = $naturals->filter(function($n) {return !divisible($n, 7);});
echo "no_sevens:\n";
display_line($no_sevens, 20);


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

echo "100th prime:\n";
echo $primes[99] . "\n";
