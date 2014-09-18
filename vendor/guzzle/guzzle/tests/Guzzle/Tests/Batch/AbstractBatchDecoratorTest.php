<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Batch;

use WPRemoteMediaExt\Guzzle\Batch\Batch;

/**
 * @covers WPRemoteMediaExt\Guzzle\Batch\AbstractBatchDecorator
 */
class AbstractBatchDecoratorTest extends \Guzzle\Tests\GuzzleTestCase
{
    public function testProxiesToWrappedObject()
    {
        $batch = new Batch(
            $this->getMock('WPRemoteMediaExt\Guzzle\Batch\BatchTransferInterface'),
            $this->getMock('WPRemoteMediaExt\Guzzle\Batch\BatchDivisorInterface')
        );

        $decoratorA = $this->getMockBuilder('WPRemoteMediaExt\Guzzle\Batch\AbstractBatchDecorator')
            ->setConstructorArgs(array($batch))
            ->getMockForAbstractClass();

        $decoratorB = $this->getMockBuilder('WPRemoteMediaExt\Guzzle\Batch\AbstractBatchDecorator')
            ->setConstructorArgs(array($decoratorA))
            ->getMockForAbstractClass();

        $decoratorA->add('foo');
        $this->assertFalse($decoratorB->isEmpty());
        $this->assertFalse($batch->isEmpty());
        $this->assertEquals(array($decoratorB, $decoratorA), $decoratorB->getDecorators());
        $this->assertEquals(array(), $decoratorB->flush());
    }
}
