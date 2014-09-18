<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Service;

use WPRemoteMediaExt\Guzzle\Cache\DoctrineCacheAdapter;
use WPRemoteMediaExt\Guzzle\Service\CachingConfigLoader;
use Doctrine\Common\Cache\ArrayCache;

/**
 * @covers WPRemoteMediaExt\Guzzle\Service\CachingConfigLoader
 */
class CachingConfigLoaderTest extends \Guzzle\Tests\GuzzleTestCase
{
    public function testLoadsPhpFileIncludes()
    {
        $cache = new DoctrineCacheAdapter(new ArrayCache());
        $loader = $this->getMockBuilder('WPRemoteMediaExt\Guzzle\Service\ConfigLoaderInterface')
            ->setMethods(array('load'))
            ->getMockForAbstractClass();
        $data = array('foo' => 'bar');
        $loader->expects($this->once())
            ->method('load')
            ->will($this->returnValue($data));
        $cache = new CachingConfigLoader($loader, $cache);
        $this->assertEquals($data, $cache->load('foo'));
        $this->assertEquals($data, $cache->load('foo'));
    }

    public function testDoesNotCacheArrays()
    {
        $cache = new DoctrineCacheAdapter(new ArrayCache());
        $loader = $this->getMockBuilder('WPRemoteMediaExt\Guzzle\Service\ConfigLoaderInterface')
            ->setMethods(array('load'))
            ->getMockForAbstractClass();
        $data = array('foo' => 'bar');
        $loader->expects($this->exactly(2))
            ->method('load')
            ->will($this->returnValue($data));
        $cache = new CachingConfigLoader($loader, $cache);
        $this->assertEquals($data, $cache->load(array()));
        $this->assertEquals($data, $cache->load(array()));
    }
}
