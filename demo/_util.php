<?php
require __DIR__ . "/../vendor/autoload.php";

use StreamPHP\Stream;

// ストリームを出力する
function display_line($s, $limit = 20)
{
    for ($i=0; $i<$limit; $i++) {
        echo $s[$i] . " ";
    }
    echo "\n\n";
}
