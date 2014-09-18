<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Plugin\Backoff;

use WPRemoteMediaExt\Guzzle\Http\Message\Response;
use WPRemoteMediaExt\Guzzle\Plugin\Backoff\CurlBackoffStrategy;
use WPRemoteMediaExt\Guzzle\Http\Exception\CurlException;

/**
 * @covers WPRemoteMediaExt\Guzzle\Plugin\Backoff\CurlBackoffStrategy
 * @covers WPRemoteMediaExt\Guzzle\Plugin\Backoff\AbstractErrorCodeBackoffStrategy
 */
class CurlBackoffStrategyTest extends \Guzzle\Tests\GuzzleTestCase
{
    public function testRetriesWithExponentialDelay()
    {
        $this->assertNotEmpty(CurlBackoffStrategy::getDefaultFailureCodes());
        $strategy = new CurlBackoffStrategy();
        $this->assertTrue($strategy->makesDecision());
        $request = $this->getMock('WPRemoteMediaExt\Guzzle\Http\Message\Request', array(), array(), '', false);
        $e = new CurlException();
        $e->setError('foo', CURLE_BAD_CALLING_ORDER);
        $this->assertEquals(false, $strategy->getBackoffPeriod(0, $request, null, $e));

        foreach (CurlBackoffStrategy::getDefaultFailureCodes() as $code) {
            $this->assertEquals(0, $strategy->getBackoffPeriod(0, $request, null, $e->setError('foo', $code)));
        }
    }

    public function testIgnoresNonErrors()
    {
        $strategy = new CurlBackoffStrategy();
        $request = $this->getMock('WPRemoteMediaExt\Guzzle\Http\Message\Request', array(), array(), '', false);
        $this->assertEquals(false, $strategy->getBackoffPeriod(0, $request, new Response(200)));
    }
}
