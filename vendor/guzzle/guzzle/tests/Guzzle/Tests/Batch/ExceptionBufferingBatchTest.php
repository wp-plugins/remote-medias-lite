<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Batch;

use WPRemoteMediaExt\Guzzle\Batch\ExceptionBufferingBatch;
use WPRemoteMediaExt\Guzzle\Batch\Batch;
use WPRemoteMediaExt\Guzzle\Batch\BatchSizeDivisor;

/**
 * @covers WPRemoteMediaExt\Guzzle\Batch\ExceptionBufferingBatch
 */
class ExceptionBufferingBatchTest extends \Guzzle\Tests\GuzzleTestCase
{
    public function testFlushesEntireBatchWhileBufferingErroredBatches()
    {
        $t = $this->getMockBuilder('WPRemoteMediaExt\Guzzle\Batch\BatchTransferInterface')
            ->setMethods(array('transfer'))
            ->getMock();

        $d = new BatchSizeDivisor(1);
        $batch = new Batch($t, $d);

        $called = 0;
        $t->expects($this->exactly(3))
            ->method('transfer')
            ->will($this->returnCallback(function ($batch) use (&$called) {
                if (++$called === 2) {
                    throw new \Exception('Foo');
                }
            }));

        $decorator = new ExceptionBufferingBatch($batch);
        $decorator->add('foo')->add('baz')->add('bar');
        $result = $decorator->flush();

        $e = $decorator->getExceptions();
        $this->assertEquals(1, count($e));
        $this->assertEquals(array('baz'), $e[0]->getBatch());

        $decorator->clearExceptions();
        $this->assertEquals(0, count($decorator->getExceptions()));

        $this->assertEquals(array('foo', 'bar'), $result);
    }
}
