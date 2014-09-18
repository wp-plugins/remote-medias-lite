<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Cache;

use WPRemoteMediaExt\Guzzle\Cache\CacheAdapterFactory;
use WPRemoteMediaExt\Guzzle\Cache\DoctrineCacheAdapter;
use Doctrine\Common\Cache\ArrayCache;
use Zend\Cache\StorageFactory;

/**
 * @covers WPRemoteMediaExt\Guzzle\Cache\CacheAdapterFactory
 */
class CacheAdapterFactoryTest extends \Guzzle\Tests\GuzzleTestCase
{
    /** @var ArrayCache */
    private $cache;

    /** @var DoctrineCacheAdapter */
    private $adapter;

    /**
     * Prepares the environment before running a test.
     */
    protected function setup()
    {
        parent::setUp();
        $this->cache = new ArrayCache();
        $this->adapter = new DoctrineCacheAdapter($this->cache);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEnsuresConfigIsObject()
    {
        CacheAdapterFactory::fromCache(array());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEnsuresKnownType()
    {
        CacheAdapterFactory::fromCache(new \stdClass());
    }

    public function cacheProvider()
    {
        return array(
            array(new DoctrineCacheAdapter(new ArrayCache()), 'WPRemoteMediaExt\Guzzle\Cache\DoctrineCacheAdapter'),
            array(new ArrayCache(), 'WPRemoteMediaExt\Guzzle\Cache\DoctrineCacheAdapter'),
            array(StorageFactory::factory(array('adapter' => 'memory')), 'WPRemoteMediaExt\Guzzle\Cache\Zf2CacheAdapter'),
        );
    }

    /**
     * @dataProvider cacheProvider
     */
    public function testCreatesNullCacheAdapterByDefault($cache, $type)
    {
        $adapter = CacheAdapterFactory::fromCache($cache);
        $this->assertInstanceOf($type, $adapter);
    }
}
