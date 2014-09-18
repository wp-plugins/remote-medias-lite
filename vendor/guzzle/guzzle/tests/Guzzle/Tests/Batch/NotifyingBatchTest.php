<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Batch;

use WPRemoteMediaExt\Guzzle\Batch\NotifyingBatch;
use WPRemoteMediaExt\Guzzle\Batch\Batch;

/**
 * @covers WPRemoteMediaExt\Guzzle\Batch\NotifyingBatch
 */
class NotifyingBatchTest extends \Guzzle\Tests\GuzzleTestCase
{
    public function testNotifiesAfterFlush()
    {
        $batch = $this->getMock('WPRemoteMediaExt\Guzzle\Batch\Batch', array('flush'), array(
            $this->getMock('WPRemoteMediaExt\Guzzle\Batch\BatchTransferInterface'),
            $this->getMock('WPRemoteMediaExt\Guzzle\Batch\BatchDivisorInterface')
        ));

        $batch->expects($this->once())
            ->method('flush')
            ->will($this->returnValue(array('foo', 'baz')));

        $data = array();
        $decorator = new NotifyingBatch($batch, function ($batch) use (&$data) {
            $data[] = $batch;
        });

        $decorator->add('foo')->add('baz');
        $decorator->flush();
        $this->assertEquals(array(array('foo', 'baz')), $data);
    }

    /**
     * @expectedException WPRemoteMediaExt\Guzzle\Common\Exception\InvalidArgumentException
     */
    public function testEnsuresCallableIsValid()
    {
        $batch = new Batch(
            $this->getMock('WPRemoteMediaExt\Guzzle\Batch\BatchTransferInterface'),
            $this->getMock('WPRemoteMediaExt\Guzzle\Batch\BatchDivisorInterface')
        );
        $decorator = new NotifyingBatch($batch, 'foo');
    }
}
