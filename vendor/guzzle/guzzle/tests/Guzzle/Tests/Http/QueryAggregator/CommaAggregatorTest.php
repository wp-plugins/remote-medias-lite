<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Http;

use WPRemoteMediaExt\Guzzle\Http\QueryString;
use WPRemoteMediaExt\Guzzle\Http\QueryAggregator\CommaAggregator as Ag;

class CommaAggregatorTest extends \Guzzle\Tests\GuzzleTestCase
{
    public function testAggregates()
    {
        $query = new QueryString();
        $a = new Ag();
        $key = 'test 123';
        $value = array('foo 123', 'baz', 'bar');
        $result = $a->aggregate($key, $value, $query);
        $this->assertEquals(array('test%20123' => 'foo%20123,baz,bar'), $result);
    }

    public function testEncodes()
    {
        $query = new QueryString();
        $query->useUrlEncoding(false);
        $a = new Ag();
        $key = 'test 123';
        $value = array('foo 123', 'baz', 'bar');
        $result = $a->aggregate($key, $value, $query);
        $this->assertEquals(array('test 123' => 'foo 123,baz,bar'), $result);
    }
}
