<?php

require __DIR__ . "/../vendor/autoload.php";

use StreamPHP\Stream;

$delayFunc = Stream::delay(function() { echo "Hello Stream!\n"; });

echo "......";

Stream::force($delayFunc);