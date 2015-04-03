<?php

namespace StreamPHP;

class StreamTest extends \PHPUnit_Framework_TestCase
{
  public function testDelayForce()
  {
      $delay=Stream::delay(function(){return 3;});

      $this->assertInstanceOf('Closure',$delay);
      $this->assertEquals(3,Stream::force($delay));
  }
}