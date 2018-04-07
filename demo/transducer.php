<?php
require __DIR__ . "/../vendor/autoload.php";
require __DIR__ . "/_util.php";

use StreamPHP\Stream;

$ns = Stream::iterate(function($n) {return $n+1;}, 1);
echo "numbers\n";
display_line($ns , 10);

function map($fn, Stream $s)
{
    return Stream::cons($fn($s->car()), function() use($s,$fn) {
        return map($fn, $s->cdr());
    });
}

$s1 = map(function($n) {return $n*$n;}, $ns);
echo "map\n";
display_line($s1, 10);

function reduce($fn, $init, Stream $s)
{
    $v = $fn($init, $s->car());
    if ($s->cdr() != null)
        return reduce($fn, $v, $s->cdr());
    return $v;
}

$s2 = $ns->take(10);
echo "reduce\n";
echo 'reduce(1..10) = ' . reduce(function($acc, $n) {return $acc+$n;}, 0, $s2);
echo "\n\n";

function conj(Stream $s, $item)
{
    if ($s->car() == null) {
        return Stream::cons($item, null);
    }

    return Stream::cons($s->car(), function() use($s, $item) {
        if ($s->cdr() == null)
            return Stream::cons($item, null);
        else
            return conj($s->cdr(), $item);
    });
}


function inc($n)
{
    return $n+1;
}

function mapIncReducer(Stream $result, $input)
{
    return conj($result, inc($input));
}

$s3 = reduce('mapIncReducer',
             Stream::empty(),
             $ns->take(10));
echo "mapIncReducer\n";
display_line($s3, 10);


function mapReducer($fn)
{
    return function($result, $input) use($fn) {
        $v = call_user_func($fn, $input);
        return conj($result, $v);
    };
}

$s4 = reduce(mapReducer(function($input) {return $input * $input;}),
             Stream::empty(),
             $ns->take(10));
echo "mapReducer\n";
display_line($s4, 10);


function isEven($n)
{
    return $n % 2 == 0;
}

function filterEvenReducer(Stream $result, $input)
{
    if (isEven($input))
        return conj($result, $input);
    else
        return $result;
}

$s5 = reduce('filterEvenReducer',
             Stream::empty(),
             $ns->take(10));
echo "filterEvenReducer\n";
display_line($s5, 10);

function filterReducer($pred)
{
    return function($result, $input) use($pred) {
        if ($pred($input))
            return conj($result, $input);
        else
            return $result;
    };
}

$s6 = reduce(filterReducer('isEven'),
             Stream::empty(),
             $ns->take(10));
echo "filterReducer\n";
display_line($s6, 10);

$s7 = reduce(filterReducer('isEven'),
             Stream::empty(),
             reduce(mapReducer(function($n) {return $n*$n;}),
                    Stream::empty(),
                    $ns->take(10)));
echo "filterReducer(mapReducer)\n";
display_line($s7, 10);


function mapping($fn)
{
    return function($reducing) use($fn) {
        return function($result, $input) use($fn, $reducing) {
            return $reducing($result,$fn($input));
        };
    };
}

function filtering($pred)
{
    return function($reducing) use($pred) {
        return function($result, $input) use($pred, $reducing) {
            if ($pred($input))
                return $reducing($result, $input);
            else
                return $result;
        };
    };
}

$s8 = reduce(filtering('isEven')('conj'),
             Stream::empty(),
             reduce(mapping(function($n) {return $n*$n;})('conj'),
                    Stream::empty(),
                    $ns->take(10)));
echo "filtering(mapping)\n";
display_line($s8, 10);



$reduceFn = mapping(function($n){return $n*$n*$n;})(filtering('isEven')('conj'));
$s9 = reduce($reduceFn, Stream::empty(), $ns->take(10));
echo "reduceFunc\n";
display_line($s9, 10);


function comp(...$fnArray)
{
    return function($paramFn) use($fnArray) {
        $compFn = $paramFn;
        while($f = array_pop($fnArray))
            $compFn = $f($compFn);
        return $compFn;
    };
}

function square($n)
{
    return $n*$n;
}

$xform = comp(filtering('isEven'),
              filtering(function($n) {return $n<10;}),
              mapping('square'),
              mapping('inc')
);
$s10 = reduce($xform('conj'), Stream::empty(), $ns->take(100));
echo "compose funcs\n";
display_line($s10, 10);

$s11 = $xform('conj')(Stream::empty(), 2);
echo "xform\n";
display_line($s11, 10);

function transduce($xform, $reducing, $init, Stream $s)
{
    return reduce($xform($reducing), $init, $s);
}

$s12 = transduce($xform, 'conj', Stream::empty(), $ns->take(100));
echo "transduce\n";
display_line($s12, 10);
