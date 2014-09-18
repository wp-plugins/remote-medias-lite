<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Plugin\Backoff;

use WPRemoteMediaExt\Guzzle\Plugin\Backoff\ReasonPhraseBackoffStrategy;
use WPRemoteMediaExt\Guzzle\Http\Message\Response;

/**
 * @covers WPRemoteMediaExt\Guzzle\Plugin\Backoff\ReasonPhraseBackoffStrategy
 * @covers WPRemoteMediaExt\Guzzle\Plugin\Backoff\AbstractErrorCodeBackoffStrategy
 */
class ReasonPhraseBackoffStrategyTest extends \Guzzle\Tests\GuzzleTestCase
{
    public function testRetriesWhenCodeMatches()
    {
        $this->assertEmpty(ReasonPhraseBackoffStrategy::getDefaultFailureCodes());
        $strategy = new ReasonPhraseBackoffStrategy(array('Foo', 'Internal Server Error'));
        $this->assertTrue($strategy->makesDecision());
        $request = $this->getMock('WPRemoteMediaExt\Guzzle\Http\Message\Request', array(), array(), '', false);
        $response = new Response(200);
        $this->assertEquals(false, $strategy->getBackoffPeriod(0, $request, $response));
        $response->setStatus(200, 'Foo');
        $this->assertEquals(0, $strategy->getBackoffPeriod(0, $request, $response));
    }

    public function testIgnoresNonErrors()
    {
        $strategy = new ReasonPhraseBackoffStrategy();
        $request = $this->getMock('WPRemoteMediaExt\Guzzle\Http\Message\Request', array(), array(), '', false);
        $this->assertEquals(false, $strategy->getBackoffPeriod(0, $request));
    }
}
