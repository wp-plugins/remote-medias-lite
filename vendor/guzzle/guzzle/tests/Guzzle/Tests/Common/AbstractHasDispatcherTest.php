<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Common;

use WPRemoteMediaExt\Guzzle\Common\Event;
use WPRemoteMediaExt\Guzzle\Common\AbstractHasDispatcher;
use WPRemoteMediaExt\Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @covers WPRemoteMediaExt\Guzzle\Common\AbstractHasDispatcher
 */
class AbstractHasAdapterTest extends \Guzzle\Tests\GuzzleTestCase
{
    public function testDoesNotRequireRegisteredEvents()
    {
        $this->assertEquals(array(), AbstractHasDispatcher::getAllEvents());
    }

    public function testAllowsDispatcherToBeInjected()
    {
        $d = new EventDispatcher();
        $mock = $this->getMockForAbstractClass('WPRemoteMediaExt\Guzzle\Common\AbstractHasDispatcher');
        $this->assertSame($mock, $mock->setEventDispatcher($d));
        $this->assertSame($d, $mock->getEventDispatcher());
    }

    public function testCreatesDefaultEventDispatcherIfNeeded()
    {
        $mock = $this->getMockForAbstractClass('WPRemoteMediaExt\Guzzle\Common\AbstractHasDispatcher');
        $this->assertInstanceOf('WPRemoteMediaExt\Symfony\Component\EventDispatcher\EventDispatcher', $mock->getEventDispatcher());
    }

    public function testHelperDispatchesEvents()
    {
        $data = array();
        $mock = $this->getMockForAbstractClass('WPRemoteMediaExt\Guzzle\Common\AbstractHasDispatcher');
        $mock->getEventDispatcher()->addListener('test', function(Event $e) use (&$data) {
            $data = $e->getIterator()->getArrayCopy();
        });
        $mock->dispatch('test', array(
            'param' => 'abc'
        ));
        $this->assertEquals(array(
            'param' => 'abc',
        ), $data);
    }

    public function testHelperAttachesSubscribers()
    {
        $mock = $this->getMockForAbstractClass('WPRemoteMediaExt\Guzzle\Common\AbstractHasDispatcher');
        $subscriber = $this->getMockForAbstractClass('WPRemoteMediaExt\Symfony\Component\EventDispatcher\EventSubscriberInterface');

        $dispatcher = $this->getMockBuilder('WPRemoteMediaExt\Symfony\Component\EventDispatcher\EventDispatcher')
            ->setMethods(array('addSubscriber'))
            ->getMock();

        $dispatcher->expects($this->once())
            ->method('addSubscriber');

        $mock->setEventDispatcher($dispatcher);
        $mock->addSubscriber($subscriber);
    }
}
