<?php
require __DIR__ . "/../vendor/autoload.php";
require __DIR__ . "/_util.php";

use StreamPHP\Stream;
use StreamPHP\Transducer as T;

$ns = Stream::iterate(function($n) {return $n+1;}, 1);
echo "numbers\n";
display_line($ns , 10);

$conjFn = 'StreamPHP\Transducer::conj';


function inc($n)
{
    return $n + 1;
}

$s1 = T::reduce(T::map('inc')($conjFn),
                Stream::empty(),
                $ns->take(10));
echo "map inc transducer\n";
display_line($s1, 10);


function square($n)
{
    return $n*$n;
}

$s3 = T::reduce(T::map('square')($conjFn),
                Stream::empty(),
                $ns->take(10));
echo "map square transducer\n";
display_line($s3, 10);


function isEven($n)
{
    return $n % 2 == 0;
}

$s6 = T::reduce(T::filter('isEven')($conjFn),
                Stream::empty(),
                $ns->take(10));
echo "filter transducer\n";
display_line($s6, 10);


$s7 = T::reduce(T::filter('isEven')($conjFn),
                Stream::empty(),
                T::reduce(T::map('square')($conjFn),
                          Stream::empty(),
                          $ns->take(10)));
echo "filter map transducer\n";
display_line($s7, 10);



$xform = T::comp(T::filter('isEven'),
                 T::filter(function($n) {return $n<10;}),
                 T::map('square'),
                 T::map('inc'));
$s10 = T::reduce($xform($conjFn), Stream::empty(), $ns->take(100));
echo "compose transducers\n";
display_line($s10, 10);

$s11 = $xform($conjFn)(Stream::empty(), 2);
echo "xform\n";
display_line($s11, 10);

$s12 = T::transduce($xform, $conjFn, Stream::empty(), $ns->take(100));
echo "transduce\n";
display_line($s12, 10);

$s13 = T::into($xform, $ns->take(100));
echo "into\n";
display_line($s12, 10);


function twins($a)
{
    return Stream::from($a, $a);
}

function mapcatting($fn)
{
    return function($reducing) use($fn) {
        return function($result, $input) use($fn, $reducing) {
            return T::reduce($reducing, $result, $fn($input));
        };
    };
}

$s14 = T::reduce(mapcatting('twins')($conjFn),
                 Stream::empty(),
                 $ns->take(5));
echo "mapcatting\n";
display_line($s14);


$s15 = T::reduce(T::take(3)($conjFn), Stream::empty(), $ns->take(10));
echo "take transducer\n";
display_line($s15, 10);

$takeFn = T::take(3)($conjFn);
$take1 = $takeFn(Stream::empty(), 1);
echo "take1: " . $take1->car() . "\n";
$take2 = $takeFn(Stream::empty(), 2);
echo "take2: " . $take2->car() . "\n";
$take3 = $takeFn(Stream::empty(), 3);
echo "take3: " . $take3->car() . "\n";
$take4 = $takeFn(Stream::empty(), 4);
echo "take4: " . $take4->car() . "\n";
