<?php
require __DIR__ . "/../vendor/autoload.php";

use StreamPHP\Stream;

function display_line($s, $limit = 20)
{
    foreach($s->take($limit) as $n) {
        echo $n." ";
    }
    echo "\n\n";
}


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
