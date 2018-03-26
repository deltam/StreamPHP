<?php
/**
 * StreamTest
 */

namespace StreamPHP;

class StreamTest extends \PHPUnit_Framework_TestCase
{
    public function testIsNull()
    {
        $s = Stream::cons(1, function() {return 2;});
        $this->assertFalse($s->is_null());

        $s = Stream::cons(null, null);
        $this->assertTrue($s->is_null());

        $s = Stream::cons(1, null);
        $this->assertFalse($s->is_null());

        $s = Stream::cons(null, function() {return 2;});
        $this->assertFalse($s->is_null());
    }

    // cons()のテスト用
    private function number_stream($n=0) {
        $self = $this;
        return Stream::cons($n, function() use($n,$self) {
            return $self->number_stream($n+1);
        });
    }

    public function testCons()
    {
        $s = $this->number_stream(1);
        $this->assertEquals(1, $s->car());
        $this->assertInstanceOf(Stream::class, $s->cdr());
    }

    public function testCar()
    {
        $s = Stream::cons(1, function() {return 2;});
        $this->assertEquals(1 , $s->car());
    }

    public function testCdr()
    {
        $s1 = Stream::cons(1, function() {return 2;});
        $this->assertEquals(1 , $s1->car());
        $this->assertEquals(2 , $s1->cdr());
        $s2 = $this->number_stream(1);
        $this->assertEquals(1, $s2->car());
        $this->assertEquals(2, $s2->cdr()->car());
    }

    public function testMap()
    {
        $s1 = $this->number_stream(1);
        $this->assertEquals(1, $s1->car());
        $this->assertEquals(2, $s1->cdr()->car());
        $this->assertEquals(3, $s1->cdr()->cdr()->car());
        $s2 = $s1->map(function($n) {return $n * $n;});
        $this->assertEquals(1, $s2->car());
        $this->assertEquals(4, $s2->cdr()->car());
        $this->assertEquals(9, $s2->cdr()->cdr()->car());
    }

    public function testFilter()
    {
        $s1 = $this->number_stream(1);
        $this->assertEquals(1, $s1->car());
        $this->assertEquals(2, $s1->cdr()->car());
        $this->assertEquals(3, $s1->cdr()->cdr()->car());
        $s2 = $s1->filter(function($n) {return $n > 10;});
        $this->assertEquals(11, $s2->car());
        $this->assertEquals(12, $s2->cdr()->car());
        $this->assertEquals(13, $s2->cdr()->cdr()->car());
    }

    public function testTake()
    {
        $s = $this->number_stream(1);
        $ret1 = $s->take(3);
        $this->assertCount(3, $ret1);
        $this->assertEquals(1, $ret1[0]);
        $this->assertEquals(2, $ret1[1]);
        $this->assertEquals(3, $ret1[2]);
        $ret2 = $s->take(3, 10);
        $this->assertCount(3, $ret2);
        $this->assertEquals(11, $ret2[0]);
        $this->assertEquals(12, $ret2[1]);
        $this->assertEquals(13, $ret2[2]);
    }

    public function testIterate()
    {
        $s = Stream::iterate(function($n) {return $n+1;}, 0);
        $this->assertInstanceOf(Stream::class, $s);
        $this->assertEquals(0, $s->car());
        $this->assertEquals(1, $s->cdr()->car());
        $this->assertEquals(2, $s->cdr()->cdr()->car());
    }
}
