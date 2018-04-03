<?php
require __DIR__ . "/../vendor/autoload.php";

use StreamPHP\Stream;

// ストリームを出力する
function display_line($s, $limit = 20)
{
    echo $s->take($limit)
           ->reduce(function($acc, $x) {return $acc . $x . " ";}, "");
    echo "\n\n";
}
