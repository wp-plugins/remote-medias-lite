<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Service\Command;

use WPRemoteMediaExt\Guzzle\Service\Client;
use WPRemoteMediaExt\Guzzle\Service\Command\Factory\AliasFactory;
use WPRemoteMediaExt\Guzzle\Service\Command\Factory\MapFactory;
use WPRemoteMediaExt\Guzzle\Service\Command\Factory\CompositeFactory;

/**
 * @covers WPRemoteMediaExt\Guzzle\Service\Command\Factory\AliasFactory
 */
class AliasFactoryTest extends \Guzzle\Tests\GuzzleTestCase
{
    private $factory;
    private $client;

    public function setup()
    {
        $this->client = new Client();

        $map = new MapFactory(array(
            'test'  => 'WPRemoteMediaExt\Guzzle\Tests\Service\Mock\Command\MockCommand',
            'test1' => 'WPRemoteMediaExt\Guzzle\Tests\Service\Mock\Command\OtherCommand'
        ));

        $this->factory = new AliasFactory($this->client, array(
            'foo'      => 'test',
            'bar'      => 'sub',
            'sub'      => 'test1',
            'krull'    => 'test3',
            'krull_2'  => 'krull',
            'sub_2'    => 'bar',
            'bad_link' => 'jarjar'
        ));

        $map2 = new MapFactory(array(
            'test3'  => 'WPRemoteMediaExt\Guzzle\Tests\Service\Mock\Command\Sub\Sub'
        ));

        $this->client->setCommandFactory(new CompositeFactory(array($map, $this->factory, $map2)));
    }

    public function aliasProvider()
    {
        return array(
            array('foo', 'WPRemoteMediaExt\Guzzle\Tests\Service\Mock\Command\MockCommand', false),
            array('bar', 'WPRemoteMediaExt\Guzzle\Tests\Service\Mock\Command\OtherCommand', false),
            array('sub', 'WPRemoteMediaExt\Guzzle\Tests\Service\Mock\Command\OtherCommand', false),
            array('sub_2', 'WPRemoteMediaExt\Guzzle\Tests\Service\Mock\Command\OtherCommand', false),
            array('krull', 'WPRemoteMediaExt\Guzzle\Tests\Service\Mock\Command\Sub\Sub', false),
            array('krull_2', 'WPRemoteMediaExt\Guzzle\Tests\Service\Mock\Command\Sub\Sub', false),
            array('missing', null, true),
            array('bad_link', null, true)
        );
    }

    /**
     * @dataProvider aliasProvider
     */
    public function testAliasesCommands($key, $result, $exception)
    {
        try {
            $command = $this->client->getCommand($key);
            if (is_null($result)) {
                $this->assertNull($command);
            } else {
                $this->assertInstanceof($result, $command);
            }
        } catch (\Exception $e) {
            if (!$exception) {
                $this->fail('Got exception when it was not expected');
            }
        }
    }
}
