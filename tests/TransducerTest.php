<?php
/**
 * Transducer Test
 */

namespace StreamPHP;

class TransducerTest extends \PHPUnit_Framework_TestCase
{
    public function testReduce()
    {
        $s = Stream::fromArray(array(1,2,3));
        $sum = Transducer::reduce(function($result, $input) {
            return $result + $input;
        }, 0, $s);
        $this->assertEquals(1+2+3, $sum);
    }

    public function testConj()
    {
        $s1 = Stream::fromArray(array(1,2,3));
        $s2 = Transducer::conj($s1, 4);
        $this->assertEquals(4, $s2->cdr()->cdr()->cdr()->car());
    }

    public function testMap()
    {
        $mapFn = Transducer::map(function($n) {return $n*$n;});
        $s = $mapFn('StreamPHP\Transducer::conj')(Stream::empty(), 9);
        $this->assertEquals(9*9, $s->car());
        $this->assertNull($s->cdr());
    }

    public function testFilter()
    {
        $filterFn = Transducer::filter(function($n) {return $n%2 == 0;});
        $s1 = $filterFn('StreamPHP\Transducer::conj')(Stream::empty(), 1);
        $this->assertNull($s1->car());
        $this->assertNull($s1->cdr());
        $s2 = $filterFn('StreamPHP\Transducer::conj')(Stream::empty(), 2);
        $this->assertEquals(2, $s2->car());
        $this->assertNull($s2->cdr());
    }

    public function testTake()
    {
        $s = Stream::fromArray(array(1,2,3,4,5,6));
        $takeFn = Transducer::take(3);
        $taken = Transducer::reduce(
            $takeFn('StreamPHP\Transducer::conj'),
            Stream::empty(),
            $s);
        $this->assertEquals(1, $taken->car());
        $this->assertEquals(2, $taken->cdr()->car());
        $this->assertEquals(3, $taken->cdr()->cdr()->car());
        $this->assertNull($taken->cdr()->cdr()->cdr());
    }

    public function testComp()
    {
        $xform = Transducer::comp(
            Transducer::filter(function($n) {return $n%2 == 0;}),
            Transducer::map(function($n) {return $n*$n;})
        );
        $s1 = $xform('StreamPHP\Transducer::conj')(Stream::empty(), 1);
        $this->assertNull($s1->car());
        $this->assertNull($s1->cdr());
        $s2 = $xform('StreamPHP\Transducer::conj')(Stream::empty(), 2);
        $this->assertEquals(2*2, $s2->car());
        $this->assertNull($s2->cdr());
        $s3 = $xform('StreamPHP\Transducer::conj')(Stream::empty(), 3);
        $this->assertNull($s3->car());
        $this->assertNull($s3->cdr());
    }

    public function testTransduce()
    {
        $xform = Transducer::comp(
            Transducer::filter(function($n) {return $n%2 == 0;}),
            Transducer::map(function($n) {return $n*$n;})
        );
        $s = Stream::fromArray(array(1,2,3,4,5));

        $transduced = Transducer::transduce(
            $xform,
            'StreamPHP\Transducer::conj',
            Stream::empty(),
            $s
        );
        $this->assertEquals(2*2, $transduced->car());
        $this->assertEquals(4*4, $transduced->cdr()->car());
        $this->assertNull($transduced->cdr()->cdr());
    }

    public function testInto()
    {
        $xform = Transducer::comp(
            Transducer::filter(function($n) {return $n%2 == 0;}),
            Transducer::map(function($n) {return $n*$n;})
        );
        $s = Stream::fromArray(array(1,2,3,4,5));

        $into = Transducer::into(
            $xform,
            $s
        );
        $this->assertEquals(2*2, $into->car());
        $this->assertEquals(4*4, $into->cdr()->car());
        $this->assertNull($into->cdr()->cdr());
    }
}
