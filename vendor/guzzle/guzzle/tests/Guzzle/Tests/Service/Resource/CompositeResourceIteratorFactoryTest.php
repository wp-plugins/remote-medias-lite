<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Service\Resource;

use WPRemoteMediaExt\Guzzle\Service\Resource\CompositeResourceIteratorFactory;
use WPRemoteMediaExt\Guzzle\Service\Resource\ResourceIteratorClassFactory;
use WPRemoteMediaExt\Guzzle\Tests\Service\Mock\Command\MockCommand;

/**
 * @covers WPRemoteMediaExt\Guzzle\Service\Resource\CompositeResourceIteratorFactory
 */
class CompositeResourceIteratorFactoryTest extends \Guzzle\Tests\GuzzleTestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Iterator was not found for mock_command
     */
    public function testEnsuresIteratorClassExists()
    {
        $factory = new CompositeResourceIteratorFactory(array(
            new ResourceIteratorClassFactory(array('Foo', 'Bar'))
        ));
        $cmd = new MockCommand();
        $this->assertFalse($factory->canBuild($cmd));
        $factory->build($cmd);
    }

    public function testBuildsResourceIterators()
    {
        $f1 = new ResourceIteratorClassFactory('WPRemoteMediaExt\Guzzle\Tests\Service\Mock\Model');
        $factory = new CompositeResourceIteratorFactory(array());
        $factory->addFactory($f1);
        $command = new MockCommand();
        $iterator = $factory->build($command, array('client.namespace' => 'WPRemoteMediaExt\Guzzle\Tests\Service\Mock'));
        $this->assertInstanceOf('WPRemoteMediaExt\Guzzle\Tests\Service\Mock\Model\MockCommandIterator', $iterator);
    }
}
