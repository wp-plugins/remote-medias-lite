<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Plugin\Cache;

use WPRemoteMediaExt\Guzzle\Http\Message\Request;
use WPRemoteMediaExt\Guzzle\Http\Message\Response;
use WPRemoteMediaExt\Guzzle\Plugin\Cache\DenyRevalidation;

/**
 * @covers WPRemoteMediaExt\Guzzle\Plugin\Cache\DenyRevalidation
 */
class DenyRevalidationTest extends \Guzzle\Tests\GuzzleTestCase
{
    public function testDeniesRequestRevalidation()
    {
        $deny = new DenyRevalidation();
        $this->assertFalse($deny->revalidate(new Request('GET', 'http://foo.com'), new Response(200)));
    }
}
