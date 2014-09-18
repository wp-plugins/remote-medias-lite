<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Plugin\Cache;

use WPRemoteMediaExt\Guzzle\Http\Message\Request;
use WPRemoteMediaExt\Guzzle\Http\Message\Response;
use WPRemoteMediaExt\Guzzle\Plugin\Cache\SkipRevalidation;

/**
 * @covers WPRemoteMediaExt\Guzzle\Plugin\Cache\SkipRevalidation
 */
class SkipRevalidationTest extends \Guzzle\Tests\GuzzleTestCase
{
    public function testSkipsRequestRevalidation()
    {
        $skip = new SkipRevalidation();
        $this->assertTrue($skip->revalidate(new Request('GET', 'http://foo.com'), new Response(200)));
    }
}
