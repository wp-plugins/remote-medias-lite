<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Http\Message\Header;

use WPRemoteMediaExt\Guzzle\Http\Message\Header\HeaderFactory;

/**
 * @covers WPRemoteMediaExt\Guzzle\Http\Message\Header\HeaderFactory
 */
class HeaderFactoryTest extends \Guzzle\Tests\GuzzleTestCase
{
    public function testCreatesBasicHeaders()
    {
        $f = new HeaderFactory();
        $h = $f->createHeader('Foo', 'Bar');
        $this->assertInstanceOf('WPRemoteMediaExt\Guzzle\Http\Message\Header', $h);
        $this->assertEquals('Foo', $h->getName());
        $this->assertEquals('Bar', (string) $h);
    }

    public function testCreatesSpecificHeaders()
    {
        $f = new HeaderFactory();
        $h = $f->createHeader('Link', '<http>; rel="test"');
        $this->assertInstanceOf('WPRemoteMediaExt\Guzzle\Http\Message\Header\Link', $h);
        $this->assertEquals('Link', $h->getName());
        $this->assertEquals('<http>; rel="test"', (string) $h);
    }
}
