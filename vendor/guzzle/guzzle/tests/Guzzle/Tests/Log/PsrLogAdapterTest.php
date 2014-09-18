<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Log;

use WPRemoteMediaExt\Guzzle\Log\PsrLogAdapter;
use Monolog\Logger;
use Monolog\Handler\TestHandler;

/**
 * @covers WPRemoteMediaExt\Guzzle\Log\PsrLogAdapter
 * @covers WPRemoteMediaExt\Guzzle\Log\AbstractLogAdapter
 */
class PsrLogAdapterTest extends \Guzzle\Tests\GuzzleTestCase
{
    public function testLogsMessagesToAdaptedObject()
    {
        $log = new Logger('test');
        $handler = new TestHandler();
        $log->pushHandler($handler);
        $adapter = new PsrLogAdapter($log);
        $adapter->log('test!', LOG_INFO);
        $this->assertTrue($handler->hasInfoRecords());
        $this->assertSame($log, $adapter->getLogObject());
    }
}
