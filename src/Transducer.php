<?php
namespace StreamPHP;

class Transducer
{
    /**
     * reduce Stream
     * @param Closure $fn
     * @param mixed $init
     * @param Stream $s
     * @return mixed
     */
    public static function reduce($fn, $init, Stream $s)
    {
        $result = $fn($init, $s->car());
        if ($s->cdr() != null)
            return self::reduce($fn, $result, $s->cdr());
        else
            return $result;
    }

    /**
     * conj stream
     * @param Stream $s
     * @param mixed $item
     * @return Stream
     */
    public static function conj(Stream $s, $item)
    {
        if ($s->car() == null) {
            return Stream::cons($item, null);
        }

        return Stream::cons($s->car(), function() use($s, $item) {
            if ($s->cdr() == null)
                return Stream::cons($item, null);
            else
                return self::conj($s->cdr(), $item);
        });
    }

    /**
     * map transducer
     * @param Closure $fn
     * @return Closure
     */
    public static function map($fn)
    {
        return function($reducing) use($fn) {
            return function($result, $input) use($fn, $reducing) {
                return $reducing($result,$fn($input));
            };
        };
    }

    /**
     * filter transducer
     * @param Closure $pred
     * @return Closure
     */
    public static function filter($pred)
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

    /**
     * take transducer
     * @param int $n
     * @return Closure
     */
    public static function take(int $n)
    {
        return self::filter(function($input) use(&$n) {
            return 0 < $n--;
        });
    }

    /**
     * compose transducers
     * @param Closure ...$fnArray
     * @return Closure
     */
    public static function comp(...$fnArray)
    {
        return function($reducing) use($fnArray) {
            $compFn = $reducing;
            while($f = array_pop($fnArray))
                $compFn = $f($compFn);
            return $compFn;
        };
    }

    /**
     * transduce
     * @param Closure $xform
     * @param Closure $reducing
     * @param mixed $init
     * @param Stream $s
     * @return Stream
     */
    public static function transduce($xform, $reducing, $init, Stream $s)
    {
        return self::reduce($xform($reducing), $init, $s);
    }

    /**
     * into
     * @param Closure $xform
     * @param Stream $s
     * @return Stream
     */
    public static function into($xform, Stream $s)
    {
        return self::transduce(
            $xform,
            'StreamPHP\Transducer::conj',
            Stream::empty(),
            $s);
    }
}
