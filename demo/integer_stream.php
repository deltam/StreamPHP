<?php
require __DIR__ . "/../vendor/autoload.php";
require __DIR__ . "/_util.php";

use StreamPHP\Stream;

function integers_start_from($n)
{
    return Stream::cons($n, function() use ($n) {return integers_start_from($n + 1);});
}
// 自然数の無限ストリーム
$naturals = integers_start_from(1);
echo "natural numbers:\n";
display_line($naturals, 20);


// 立法数のj無限ストリーム
$squares = $naturals->map(function($n) {return $n*$n;});
echo "square numbers:\n";
display_line($squares, 20);


// interateによる自然数ストリーム
$naturals2 = Stream::iterate(function($n) {return $n+1;}, 1);
echo "natural numbers(iterate):\n";
display_line($naturals2, 20);
