<?php
namespace StreamPHP;

class Stream implements \ArrayAccess
{
    /**
     */
    private $car = null;
    private $cdr = null;

    /**
     * インスタンスはconsで作る
     * @param mixed
     */
    private function __construct($a, $b) {
        $this->car = $a;
        $this->cdr = $b;
    }

    /**
     * @param Closure
     * @return Closure
     */
    private static function delay(\Closure $fn)
    {
        $already_run = false;
        $result = null;
        return function() use($fn, $already_run, $result) {
            // メモ化しないと遅い
            if (!$already_run) {
                $result = self::force($fn);
                $already_run = true;
            }
            return $result;
        };
    }

    /**
     * @param mixed
     * @return mixed
     */
    private static function force($fn)
    {
        if ($fn instanceof \Closure)
            return call_user_func($fn);
        else
            return $fn;
    }

    /**
     */
    public function is_null()
    {
        return $this->car == null && $this->cdr == null;
    }

    /**
     * @param
     * @return Stream
     */
    public static function cons($a, $b = null)
    {
        if ($b == null)
            return new self($a, null);
        else if ($b instanceof Stream) {
            return self::cons($a, function() use($b) {return $b;});
        }
        else
            return new self($a, self::delay($b));
    }

    /**
     * return empty stream
     * @return Stream
     */
    public static function empty()
    {
        return self::cons(null, null);
    }

    /**
     * car
     * @return mixed
     */
    public function car()
    {
        return $this->car;
    }

    /**
     * cdr
     * @return mixed
     */
    public function cdr()
    {
        if ($this->cdr == null)
            return null;
        else {
            $next = self::force($this->cdr);
            return $next;
        }
    }

    /**
     * @return self
     */
    public function map(\Closure $fn)
    {
        if ($this->is_null())
            return null;
        else {
            $cdr = $this->cdr();
            return self::cons(call_user_func($fn, $this->car()),
                              function() use($cdr, $fn) {
                                  return $cdr->map($fn);
                              });
        }
    }

    /**
     * Reduce stream
     * @param Closure $fn reduce func
     * @param mixed $init
     * @return mixed
     */
    public function reduce(\Closure $fn, $init)
    {
        $val = call_user_func($fn, $init, $this->car());
        if ($this->cdr() == null)
            return $val;
        else
            return $this->cdr()->reduce($fn, $val);
    }

    /**
     * filtering stream
     * @param Closure $pred フィルタ条件の真偽値を返すクロージャ
     * @return Stream
     */
    public function filter(\Closure $pred) {
        if ($this->is_null())
            return null;
        else {
            $cdr = $this->cdr();
            if (call_user_func($pred, $this->car()))
                return self::cons($this->car(),
                                  function() use($pred, $cdr) {
                                      return $cdr->filter($pred);
                                  });
            else
                return $cdr->filter($pred);
        }
    }

    /**
     * take n items from stream
     * return as Stream
     * @param int $n
     * @return Stream
     */
    public function take($n)
    {
        if ($n <= 0 || $this->is_null())
            return null;

        return self::cons($this->car(), function() use($n) {
            if ($this->cdr() == null)
                return null;
            else
                return $this->cdr()->take($n-1);
        });
    }

    /**
     * drop n items from stream
     * return as Stream
     * @param int $n
     * @return Stream
     */
    public function drop($n)
    {
        for ($head = $this; 0 < $n && !$head->is_null(); $n--, $head = $head->cdr())
            ;

        return $head;
    }

    /**
     * index access for stream
     * if index out of bounds, return null
     *
     * @param int $n
     * @return mixed
     */
    public function ref($n)
    {
        if ($n < 0)
            return null;

        if ($n == 0)
            return $this->car();
        else if ($this->cdr() instanceof Stream)
            return $this->cdr()->ref($n-1);
        else if ($n == 1)
            return $this->cdr();
        else
            return null;
    }

    /**
     * iterate
     * @param Closure $fn
     * @param mixed $init
     * @return Stream
     */
    public static function iterate(\Closure $fn, $init=0)
    {
        $val = call_user_func($fn, $init);
        return self::cons($init, function() use($fn, $val) {
            return self::iterate($fn, $val);
        });
    }

    /**
     * generate stream from array
     * @param array $ary
     * @return Stream
     */
    public static function fromArray($ary)
    {
        $v = array_shift($ary);
        if ($v != null) {
            return self::cons($v, function() use($ary) {
                return self::fromArray($ary);
            });
        }
        else
            return null;
    }

    /**
     * generate stream from arguments
     * @param array $items
     * @return Stream
     */
    public static function from(...$items)
    {
        return self::fromArray($items);
    }

    /**
     * append stream
     * @param mixed $item
     */
    public function conj($item)
    {
        // 空のストリームに追加する場合
        if ($this->car == null) {
            $this->car = $item;
            return $this;
        }

        $cdr = $this->cdr();
        return self::cons($this->car(), function() use($cdr, $item) {
            if ($cdr == null)
                return self::cons($item, null);
            else
                return $cdr->conj($item);
        });
    }

    // ArrayAccess

    // read only
    public function offsetSet($offset, $value) {}
    public function offsetUnset($offset) {}

    public function offsetExists($offset)
    {
        if (!is_numeric($offset) || $offset < 0)
            return false;

        for ($head = $this; 0<=$offset; $offset--, $head = $head->cdr())
            if ($head == null)
                return false;

        return true;
    }

    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset))
            return null;

        return $this->ref($offset);
    }
}
