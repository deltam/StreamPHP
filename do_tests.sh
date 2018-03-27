#!/bin/sh
DIR=`dirname $0`
$DIR/vendor/bin/phpunit --bootstrap $DIR/tests/bootstrap.php $DIR/tests/
