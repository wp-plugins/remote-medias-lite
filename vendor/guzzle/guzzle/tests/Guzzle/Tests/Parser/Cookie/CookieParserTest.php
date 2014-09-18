<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Parser\Cookie;

use WPRemoteMediaExt\Guzzle\Parser\Cookie\CookieParser;

/**
 * @covers WPRemoteMediaExt\Guzzle\Parser\Cookie\CookieParser
 */
class CookieParserTest extends CookieParserProvider
{
    protected $cookieParserClass = 'WPRemoteMediaExt\Guzzle\Parser\Cookie\CookieParser';

    public function testUrlDecodesCookies()
    {
        $parser = new CookieParser();
        $result = $parser->parseCookie('foo=baz+bar', null, null, true);
        $this->assertEquals(array(
            'foo' => 'baz bar'
        ), $result['cookies']);
    }
}
