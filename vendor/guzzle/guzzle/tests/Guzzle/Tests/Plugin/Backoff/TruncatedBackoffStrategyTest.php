<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Plugin\Backoff;

use WPRemoteMediaExt\Guzzle\Http\Message\Response;
use WPRemoteMediaExt\Guzzle\Plugin\Backoff\TruncatedBackoffStrategy;
use WPRemoteMediaExt\Guzzle\Plugin\Backoff\HttpBackoffStrategy;
use WPRemoteMediaExt\Guzzle\Plugin\Backoff\ConstantBackoffStrategy;

/**
 * @covers WPRemoteMediaExt\Guzzle\Plugin\Backoff\TruncatedBackoffStrategy
 */
class TruncatedBackoffStrategyTest extends \Guzzle\Tests\GuzzleTestCase
{
    public function testRetriesWhenLessThanMax()
    {
        $strategy = new TruncatedBackoffStrategy(2);
        $this->assertTrue($strategy->makesDecision());
        $request = $this->getMock('WPRemoteMediaExt\Guzzle\Http\Message\Request', array(), array(), '', false);
        $this->assertFalse($strategy->getBackoffPeriod(0, $request));
        $this->assertFalse($strategy->getBackoffPeriod(1, $request));
        $this->assertFalse($strategy->getBackoffPeriod(2, $request));

        $response = new Response(500);
        $strategy->setNext(new HttpBackoffStrategy(null, new ConstantBackoffStrategy(10)));
        $this->assertEquals(10, $strategy->getBackoffPeriod(0, $request, $response));
        $this->assertEquals(10, $strategy->getBackoffPeriod(1, $request, $response));
        $this->assertFalse($strategy->getBackoffPeriod(2, $request, $response));
    }
}
