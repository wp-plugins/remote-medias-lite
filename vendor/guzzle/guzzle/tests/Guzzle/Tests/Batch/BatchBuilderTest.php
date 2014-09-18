<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Batch;

use WPRemoteMediaExt\Guzzle\Batch\BatchBuilder;

/**
 * @covers WPRemoteMediaExt\Guzzle\Batch\BatchBuilder
 */
class BatchBuilderTest extends \Guzzle\Tests\GuzzleTestCase
{
    private function getMockTransfer()
    {
        return $this->getMock('WPRemoteMediaExt\Guzzle\Batch\BatchTransferInterface');
    }

    private function getMockDivisor()
    {
        return $this->getMock('WPRemoteMediaExt\Guzzle\Batch\BatchDivisorInterface');
    }

    private function getMockBatchBuilder()
    {
        return BatchBuilder::factory()
            ->transferWith($this->getMockTransfer())
            ->createBatchesWith($this->getMockDivisor());
    }

    public function testFactoryCreatesInstance()
    {
        $builder = BatchBuilder::factory();
        $this->assertInstanceOf('WPRemoteMediaExt\Guzzle\Batch\BatchBuilder', $builder);
    }

    public function testAddsAutoFlush()
    {
        $batch = $this->getMockBatchBuilder()->autoFlushAt(10)->build();
        $this->assertInstanceOf('WPRemoteMediaExt\Guzzle\Batch\FlushingBatch', $batch);
    }

    public function testAddsExceptionBuffering()
    {
        $batch = $this->getMockBatchBuilder()->bufferExceptions()->build();
        $this->assertInstanceOf('WPRemoteMediaExt\Guzzle\Batch\ExceptionBufferingBatch', $batch);
    }

    public function testAddHistory()
    {
        $batch = $this->getMockBatchBuilder()->keepHistory()->build();
        $this->assertInstanceOf('WPRemoteMediaExt\Guzzle\Batch\HistoryBatch', $batch);
    }

    public function testAddsNotify()
    {
        $batch = $this->getMockBatchBuilder()->notify(function() {})->build();
        $this->assertInstanceOf('WPRemoteMediaExt\Guzzle\Batch\NotifyingBatch', $batch);
    }

    /**
     * @expectedException WPRemoteMediaExt\Guzzle\Common\Exception\RuntimeException
     */
    public function testTransferStrategyMustBeSet()
    {
        $batch = BatchBuilder::factory()->createBatchesWith($this->getMockDivisor())->build();
    }

    /**
     * @expectedException WPRemoteMediaExt\Guzzle\Common\Exception\RuntimeException
     */
    public function testDivisorStrategyMustBeSet()
    {
        $batch = BatchBuilder::factory()->transferWith($this->getMockTransfer())->build();
    }

    public function testTransfersRequests()
    {
        $batch = BatchBuilder::factory()->transferRequests(10)->build();
        $this->assertInstanceOf('WPRemoteMediaExt\Guzzle\Batch\BatchRequestTransfer', $this->readAttribute($batch, 'transferStrategy'));
    }

    public function testTransfersCommands()
    {
        $batch = BatchBuilder::factory()->transferCommands(10)->build();
        $this->assertInstanceOf('WPRemoteMediaExt\Guzzle\Batch\BatchCommandTransfer', $this->readAttribute($batch, 'transferStrategy'));
    }
}
