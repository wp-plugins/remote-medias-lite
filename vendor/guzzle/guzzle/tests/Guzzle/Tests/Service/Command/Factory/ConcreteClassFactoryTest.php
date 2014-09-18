<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Service\Command;

use WPRemoteMediaExt\Guzzle\Tests\Service\Mock\MockClient;
use WPRemoteMediaExt\Guzzle\Service\Command\Factory\ConcreteClassFactory;

/**
 * @covers WPRemoteMediaExt\Guzzle\Service\Command\Factory\ConcreteClassFactory
 */
class ConcreteClassFactoryTest extends \Guzzle\Tests\GuzzleTestCase
{
    public function testProvider()
    {
        return array(
            array('foo', null, 'WPRemoteMediaExt\Guzzle\\Tests\\Service\\Mock\\Command\\'),
            array('mock_command', 'WPRemoteMediaExt\Guzzle\Tests\Service\Mock\Command\MockCommand', 'WPRemoteMediaExt\Guzzle\\Tests\\Service\\Mock\\Command\\'),
            array('other_command', 'WPRemoteMediaExt\Guzzle\Tests\Service\Mock\Command\OtherCommand', 'WPRemoteMediaExt\Guzzle\\Tests\\Service\\Mock\\Command\\'),
            array('sub.sub', 'WPRemoteMediaExt\Guzzle\Tests\Service\Mock\Command\Sub\Sub', 'WPRemoteMediaExt\Guzzle\\Tests\\Service\\Mock\\Command\\'),
            array('sub.sub', null, 'WPRemoteMediaExt\Guzzle\\Foo\\'),
            array('foo', null, null),
            array('mock_command', 'WPRemoteMediaExt\Guzzle\Tests\Service\Mock\Command\MockCommand', null),
            array('other_command', 'WPRemoteMediaExt\Guzzle\Tests\Service\Mock\Command\OtherCommand', null),
            array('sub.sub', 'WPRemoteMediaExt\Guzzle\Tests\Service\Mock\Command\Sub\Sub', null)
        );
    }

    /**
     * @dataProvider testProvider
     */
    public function testCreatesConcreteCommands($key, $result, $prefix)
    {
        if (!$prefix) {
            $client = new MockClient();
        } else {
            $client = new MockClient('', array(
                'command.prefix' => $prefix
            ));
        }

        $factory = new ConcreteClassFactory($client);

        if (is_null($result)) {
            $this->assertNull($factory->factory($key));
        } else {
            $this->assertInstanceof($result, $factory->factory($key));
        }
    }
}
