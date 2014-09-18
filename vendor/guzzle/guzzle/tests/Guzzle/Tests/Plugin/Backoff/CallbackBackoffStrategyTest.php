<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Plugin\Backoff;

use WPRemoteMediaExt\Guzzle\Plugin\Backoff\CallbackBackoffStrategy;

/**
 * @covers WPRemoteMediaExt\Guzzle\Plugin\Backoff\CallbackBackoffStrategy
 */
class CallbackBackoffStrategyTest extends \Guzzle\Tests\GuzzleTestCase
{
    /**
     * @expectedException WPRemoteMediaExt\Guzzle\Common\Exception\InvalidArgumentException
     */
    public function testEnsuresIsCallable()
    {
        $strategy = new CallbackBackoffStrategy(new \stdClass(), true);
    }

    public function testRetriesWithCallable()
    {
        $request = $this->getMock('WPRemoteMediaExt\Guzzle\Http\Message\Request', array(), array(), '', false);
        $strategy = new CallbackBackoffStrategy(function () { return 10; }, true);
        $this->assertTrue($strategy->makesDecision());
        $this->assertEquals(10, $strategy->getBackoffPeriod(0, $request));
        // Ensure it chains correctly when null is returned
        $strategy = new CallbackBackoffStrategy(function () { return null; }, false);
        $this->assertFalse($strategy->makesDecision());
        $this->assertFalse($strategy->getBackoffPeriod(0, $request));
    }
}
