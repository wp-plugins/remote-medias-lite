<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Service\Resource;

use WPRemoteMediaExt\Guzzle\Service\Resource\MapResourceIteratorFactory;
use WPRemoteMediaExt\Guzzle\Tests\Service\Mock\Command\MockCommand;

/**
 * @covers WPRemoteMediaExt\Guzzle\Service\Resource\MapResourceIteratorFactory
 */
class MapResourceIteratorFactoryTest extends \Guzzle\Tests\GuzzleTestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Iterator was not found for mock_command
     */
    public function testEnsuresIteratorClassExists()
    {
        $factory = new MapResourceIteratorFactory(array('Foo', 'Bar'));
        $factory->build(new MockCommand());
    }

    public function testBuildsResourceIterators()
    {
        $factory = new MapResourceIteratorFactory(array(
            'mock_command' => 'WPRemoteMediaExt\Guzzle\Tests\Service\Mock\Model\MockCommandIterator'
        ));
        $iterator = $factory->build(new MockCommand());
        $this->assertInstanceOf('WPRemoteMediaExt\Guzzle\Tests\Service\Mock\Model\MockCommandIterator', $iterator);
    }

    public function testUsesWildcardMappings()
    {
        $factory = new MapResourceIteratorFactory(array(
            '*' => 'WPRemoteMediaExt\Guzzle\Tests\Service\Mock\Model\MockCommandIterator'
        ));
        $iterator = $factory->build(new MockCommand());
        $this->assertInstanceOf('WPRemoteMediaExt\Guzzle\Tests\Service\Mock\Model\MockCommandIterator', $iterator);
    }
}
