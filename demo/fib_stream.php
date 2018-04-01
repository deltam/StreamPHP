<?php
require __DIR__ . "/../vendor/autoload.php";
require __DIR__ . "/_util.php";

use StreamPHP\Stream;


// フィボナッチ数列
function fib($a, $b)
{
    return Stream::cons($a, function() use($a,$b) {
        return fib($b, $a+$b);
    });
}
$fibs = fib(0,1);
echo "Fibonacci numbers:\n";
display_line($fibs, 20);
