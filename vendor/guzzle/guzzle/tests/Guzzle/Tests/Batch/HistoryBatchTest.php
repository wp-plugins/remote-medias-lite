<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Batch;

use WPRemoteMediaExt\Guzzle\Batch\HistoryBatch;
use WPRemoteMediaExt\Guzzle\Batch\Batch;

/**
 * @covers WPRemoteMediaExt\Guzzle\Batch\HistoryBatch
 */
class HistoryBatchTest extends \Guzzle\Tests\GuzzleTestCase
{
    public function testMaintainsHistoryOfItemsAddedToBatch()
    {
        $batch = new Batch(
            $this->getMock('WPRemoteMediaExt\Guzzle\Batch\BatchTransferInterface'),
            $this->getMock('WPRemoteMediaExt\Guzzle\Batch\BatchDivisorInterface')
        );

        $history = new HistoryBatch($batch);
        $history->add('foo')->add('baz');
        $this->assertEquals(array('foo', 'baz'), $history->getHistory());
        $history->clearHistory();
        $this->assertEquals(array(), $history->getHistory());
    }
}
