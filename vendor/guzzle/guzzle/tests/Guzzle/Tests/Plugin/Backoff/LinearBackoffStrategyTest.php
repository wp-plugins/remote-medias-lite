<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Plugin\Backoff;

use WPRemoteMediaExt\Guzzle\Plugin\Backoff\LinearBackoffStrategy;

/**
 * @covers WPRemoteMediaExt\Guzzle\Plugin\Backoff\LinearBackoffStrategy
 */
class LinearBackoffStrategyTest extends \Guzzle\Tests\GuzzleTestCase
{
    public function testRetriesWithLinearDelay()
    {
        $strategy = new LinearBackoffStrategy(5);
        $this->assertFalse($strategy->makesDecision());
        $request = $this->getMock('WPRemoteMediaExt\Guzzle\Http\Message\Request', array(), array(), '', false);
        $this->assertEquals(0, $strategy->getBackoffPeriod(0, $request));
        $this->assertEquals(5, $strategy->getBackoffPeriod(1, $request));
        $this->assertEquals(10, $strategy->getBackoffPeriod(2, $request));
    }
}
