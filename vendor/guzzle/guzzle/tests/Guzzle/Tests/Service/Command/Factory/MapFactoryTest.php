<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Service\Command;

use WPRemoteMediaExt\Guzzle\Service\Command\Factory\MapFactory;

/**
 * @covers WPRemoteMediaExt\Guzzle\Service\Command\Factory\MapFactory
 */
class MapFactoryTest extends \Guzzle\Tests\GuzzleTestCase
{
    public function mapProvider()
    {
        return array(
            array('foo', null),
            array('test', 'WPRemoteMediaExt\Guzzle\Tests\Service\Mock\Command\MockCommand'),
            array('test1', 'WPRemoteMediaExt\Guzzle\Tests\Service\Mock\Command\OtherCommand')
        );
    }

    /**
     * @dataProvider mapProvider
     */
    public function testCreatesCommandsUsingMappings($key, $result)
    {
        $factory = new MapFactory(array(
            'test'  => 'WPRemoteMediaExt\Guzzle\Tests\Service\Mock\Command\MockCommand',
            'test1' => 'WPRemoteMediaExt\Guzzle\Tests\Service\Mock\Command\OtherCommand'
        ));

        if (is_null($result)) {
            $this->assertNull($factory->factory($key));
        } else {
            $this->assertInstanceof($result, $factory->factory($key));
        }
    }
}
