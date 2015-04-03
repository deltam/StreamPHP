<?php
namespace StreamPHP;

class Stream
{
    /**
     * @param Closure
     * @return Closure
     */
    public static function delay(\Closure $fn)
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
    public static function force($fn)
    {
        if ($fn instanceOf \Closure)
            return call_user_func($fn);
        else
            return $fn;
    }
}