<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Batch;

use WPRemoteMediaExt\Guzzle\Batch\BatchClosureDivisor;

/**
 * @covers WPRemoteMediaExt\Guzzle\Batch\BatchClosureDivisor
 */
class BatchClosureDivisorTest extends \Guzzle\Tests\GuzzleTestCase
{
    /**
     * @expectedException WPRemoteMediaExt\Guzzle\Common\Exception\InvalidArgumentException
     */
    public function testEnsuresCallableIsCallable()
    {
        $d = new BatchClosureDivisor(new \stdClass());
    }

    public function testDividesBatch()
    {
        $queue = new \SplQueue();
        $queue[] = 'foo';
        $queue[] = 'baz';

        $d = new BatchClosureDivisor(function (\SplQueue $queue, $context) {
            return array(
                array('foo'),
                array('baz')
            );
        }, 'Bar!');

        $batches = $d->createBatches($queue);
        $this->assertEquals(array(array('foo'), array('baz')), $batches);
    }
}
