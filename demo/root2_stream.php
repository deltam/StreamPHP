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

/** ニュートン法で2の平方根を出す */
echo "sqrt 2\n";
$root2 = Stream::iterate(function($v) {return $v - (($v*$v - 2)/(2*$v));}, 2.0);
display_line($root2, 20);
printf("sprt(2)         = %1.20f\n", sqrt(2.0));
printf("\$root2->ref(20) = %1.20f\n", $root2->ref(20));
