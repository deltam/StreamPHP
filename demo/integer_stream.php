<?php
require __DIR__ . "/../vendor/autoload.php";

use StreamPHP\Stream;

function display_line($s, $limit = 20)
{
    $s->for_each(function($n) {echo $n." ";}, $limit);
    echo "\n\n";
}

function integers_start_from($n)
{
    return Stream::cons($n, function() use ($n) {return integers_start_from($n + 1);});
}
// 自然数の無限ストリーム
$naturals = integers_start_from(1);
echo "natural numbers\n";
display_line($naturals, 20);

// 立法数の無限ストリーム
$squares = $naturals->map(function($n) {return $n*$n;});
echo "square numbers:\n";
display_line($squares, 20);


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

// 素数の無限ストリーム
$primes = sieve(integers_start_from(2));
echo "primes:\n";
display_line($primes, 20);


// interateによる自然数ストリーム
$naturals2 = Stream::iterate(function($n) {return $n+1;}, 0);
echo "natural number(iterate):\n";
display_line($naturals2, 20);
