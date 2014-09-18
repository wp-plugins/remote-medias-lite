<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Plugin\Backoff;

use WPRemoteMediaExt\Guzzle\Plugin\Backoff\ConstantBackoffStrategy;

/**
 * @covers WPRemoteMediaExt\Guzzle\Plugin\Backoff\ConstantBackoffStrategy
 */
class ConstantBackoffStrategyTest extends \Guzzle\Tests\GuzzleTestCase
{
    public function testRetriesWithConstantDelay()
    {
        $strategy = new ConstantBackoffStrategy(3.5);
        $this->assertFalse($strategy->makesDecision());
        $request = $this->getMock('WPRemoteMediaExt\Guzzle\Http\Message\Request', array(), array(), '', false);
        $this->assertEquals(3.5, $strategy->getBackoffPeriod(0, $request));
        $this->assertEquals(3.5, $strategy->getBackoffPeriod(1, $request));
    }
}
