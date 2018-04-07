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

        $s1 = Stream::cons(2, function() {return 3;});
        $s2 = Stream::cons(1, $s1);
        $this->assertEquals(1, $s2->car());
        $this->assertEquals(2, $s2->cdr()->car());
        $this->assertEquals(3, $s2->cdr()->cdr());
    }

    public function testEmpty()
    {
        $empty = Stream::empty();
        $this->assertNull($empty->car());
        $this->assertNull($empty->cdr());
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
        $ret = $s->take(3);
        $this->assertInstanceOf(Stream::class, $ret);
        $this->assertEquals(1, $ret->car());
        $this->assertEquals(2, $ret->cdr()->car());
        $this->assertEquals(3, $ret->cdr()->cdr()->car());
        $this->assertNull($ret->cdr()->cdr()->cdr());

        $s2 = Stream::cons(1, Stream::cons(2, null));
        $this->assertEquals(1, $s2->car());
        $this->assertEquals(2, $s2->cdr()->car());
        $this->assertNull($s2->cdr()->cdr());
        $s3 = $s2->take(3);
        $this->assertEquals(1, $s3->car());
        $this->assertEquals(2, $s3->cdr()->car());
        $this->assertNull($s3->cdr()->cdr());
    }

    public function testDrop()
    {
        $s = $this->number_stream(1);
        $ret = $s->drop(3);
        $this->assertInstanceOf(Stream::class, $ret);
        $this->assertEquals(4, $ret[0]);
        $this->assertEquals(5, $ret[1]);
        $this->assertEquals(6, $ret[2]);
    }

    public function testReduce()
    {
        $s = $this->number_stream(1);
        $ten = $s->take(10); // 1 2 ... 10
        $sum = $ten->reduce(function($acc, $x) {return $acc + $x;}, 0);
        $this->assertEquals(55, $sum);
        $str = $ten->reduce(function($acc, $x) {return $acc . $x . " ";}, "");
        $this->assertEquals("1 2 3 4 5 6 7 8 9 10 ", $str);
    }

    public function testRef()
    {
        $ns = $this->number_stream(1);
        $this->assertEquals(1, $ns->ref(0));
        $this->assertEquals(2, $ns->ref(1));
        $this->assertEquals(100, $ns->ref(99));
        $this->assertNull($ns->ref(-1));

        $s = Stream::cons(1, function() {return 2;});
        $this->assertNull($s->ref(-1));
        $this->assertEquals(1, $s->ref(0));
        $this->assertEquals(2, $s->ref(1));
        $this->assertNull($s->ref(2));
    }

    public function testIterate()
    {
        $s = Stream::iterate(function($n) {return $n+1;}, 0);
        $this->assertInstanceOf(Stream::class, $s);
        $this->assertEquals(0, $s->car());
        $this->assertEquals(1, $s->cdr()->car());
        $this->assertEquals(2, $s->cdr()->cdr()->car());
    }

    public function testConj()
    {
        $empty = Stream::empty();
        $s = $empty->conj(1);
        $this->assertEquals(1, $s->car());
        $this->assertNull($s->cdr());

        $s2 = $s->conj(2);
        $this->assertEquals(1, $s2->car());
        $this->assertNotNull($s2->cdr());
        $this->assertEquals(2, $s2->cdr()->car());
        $this->assertNull($s2->cdr()->cdr());

        $s3 = $s2->conj(3);
        $this->assertNotNull($s3->cdr()->cdr());
        $this->assertEquals(3, $s3->cdr()->cdr()->car());
        $this->assertNull($s3->cdr()->cdr()->cdr());
    }

    public function testOffsetExists()
    {
        $s1 = $this->number_stream(1);
        $this->assertTrue($s1->offsetExists(0));
        $this->assertTrue($s1->offsetExists(1));
        $this->assertTrue($s1->offsetExists(100));
        $this->assertFalse($s1->offsetExists(-1));
        $this->assertFalse($s1->offsetExists('one'));

        $s2 = $s1->take(10);
        $this->assertTrue($s2->offsetExists(0));
        $this->assertTrue($s2->offsetExists(1));
        $this->assertTrue($s2->offsetExists(9));
        $this->assertFalse($s2->offsetExists(10));
        $this->assertFalse($s2->offsetExists(11));
    }

    public function testOffsetGet()
    {
        $s1 = $this->number_stream(1);
        $this->assertEquals(1, $s1[0]);
        $this->assertEquals(2, $s1[1]);
        $this->assertEquals(100, $s1[99]);
        $this->assertNull($s1['one']);

        $s2 = $s1->take(10);
        $this->assertEquals(1, $s2[0]);
        $this->assertEquals(2, $s2[1]);
        $this->assertEquals(10, $s2[9]);
        $this->assertNull($s2[10]);
    }
}
