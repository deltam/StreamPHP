<?php
namespace StreamPHP;

class Stream
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
        if ($fn instanceOf \Closure)
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
     * @return self
     */
    public static function cons($a, $b = null)
    {
        if ($b == null)
            return new self($a, null);
        else
            return new self($a, self::delay($b));
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
     * @return Stream
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
     */
    public function for_each($fn, $limit=100)
    {
        if ($this->is_null() || $limit == 0)
            return;
        else {
            call_user_func($fn, $this->car());
            $this->cdr()->for_each($fn, $limit - 1);
        }
    }

    /**
     * take n items from stream
     * @param int $n
     * @param int $offset
     * @return array
     */
    public function take($n, $offset=0)
    {
        $head = $this;
        for (; $offset > 0; $offset--)
            $head = $head->cdr();

        if ($head->is_null() || $n <= 0)
            return array();
        else
            return array_merge(array($head->car()),
                               $head->cdr()->take($n-1));
    }

    /**
     * iterate
     * @param Closure $fn
     * @param mixed $init
     * @return Stream
     */
    public static function iterate($fn, $init=0) {
        $val = call_user_func($fn, $init);
        return self::cons($val, function() use($fn, $val) {
            return self::iterate($fn, $val);
        });
    }
}
