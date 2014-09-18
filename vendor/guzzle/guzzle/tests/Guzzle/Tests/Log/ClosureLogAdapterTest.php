<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Log;

use WPRemoteMediaExt\Guzzle\Log\ClosureLogAdapter;

/**
 * @covers WPRemoteMediaExt\Guzzle\Log\ClosureLogAdapter
 */
class ClosureLogAdapterTest extends \Guzzle\Tests\GuzzleTestCase
{
    public function testClosure()
    {
        $that = $this;
        $modified = null;
        $this->adapter = new ClosureLogAdapter(function($message, $priority, $extras = null) use ($that, &$modified) {
            $modified = array($message, $priority, $extras);
        });
        $this->adapter->log('test', LOG_NOTICE, 'localhost');
        $this->assertEquals(array('test', LOG_NOTICE, 'localhost'), $modified);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testThrowsExceptionWhenNotCallable()
    {
        $this->adapter = new ClosureLogAdapter(123);
    }
}
