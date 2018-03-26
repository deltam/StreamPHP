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
$integers = integers_start_from(1);
echo "integers\n";
display_line($integers, 20);

// 立法数の無限ストリーム
$squares = $integers->map(function($n) {return $n*$n;});
echo "integers^2:\n";
display_line($squares, 20);


function divisible($a, $b) {
    return 0 == $a % $b;
}
// 7の倍数を除外したストリーム
$no_sevens = $integers->filter(function($n) {return !divisible($n, 7);});
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
