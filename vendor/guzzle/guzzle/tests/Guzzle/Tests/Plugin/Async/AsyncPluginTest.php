<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Plugin\Async;

use WPRemoteMediaExt\Guzzle\Plugin\Async\AsyncPlugin;
use WPRemoteMediaExt\Guzzle\Http\Message\RequestInterface;
use WPRemoteMediaExt\Guzzle\Http\Message\RequestFactory;
use WPRemoteMediaExt\Guzzle\Http\Curl\CurlHandle;
use WPRemoteMediaExt\Guzzle\Http\Exception\CurlException;
use WPRemoteMediaExt\Guzzle\Common\Event;
use WPRemoteMediaExt\Guzzle\Http\Client;

/**
 * @covers WPRemoteMediaExt\Guzzle\Plugin\Async\AsyncPlugin
 */
class AsyncPluginTest extends \Guzzle\Tests\GuzzleTestCase
{
    public function testSubscribesToEvents()
    {
        $events = AsyncPlugin::getSubscribedEvents();
        $this->assertArrayHasKey('request.before_send', $events);
        $this->assertArrayHasKey('request.exception', $events);
        $this->assertArrayHasKey('curl.callback.progress', $events);
    }

    public function testEnablesProgressCallbacks()
    {
        $p = new AsyncPlugin();
        $request = RequestFactory::getInstance()->create('PUT', 'http://www.example.com');
        $event = new Event(array(
            'request' => $request
        ));
        $p->onBeforeSend($event);
        $this->assertEquals(true, $request->getCurlOptions()->get('progress'));
    }

    public function testAddsTimesOutAfterSending()
    {
        $p = new AsyncPlugin();
        $request = RequestFactory::getInstance()->create('PUT', 'http://www.example.com');
        $handle = CurlHandle::factory($request);
        $event = new Event(array(
            'request'     => $request,
            'handle'      => $handle->getHandle(),
            'uploaded'    => 10,
            'upload_size' => 10,
            'downloaded'  => 0
        ));
        $p->onCurlProgress($event);
    }

    public function testEnsuresRequestIsSet()
    {
        $p = new AsyncPlugin();
        $event = new Event(array(
            'uploaded'    => 10,
            'upload_size' => 10,
            'downloaded'  => 0
        ));
        $p->onCurlProgress($event);
    }

    public function testMasksCurlExceptions()
    {
        $p = new AsyncPlugin();
        $request = RequestFactory::getInstance()->create('PUT', 'http://www.example.com');
        $e = new CurlException('Error');
        $event = new Event(array(
            'request'   => $request,
            'exception' => $e
        ));
        $p->onRequestTimeout($event);
        $this->assertEquals(RequestInterface::STATE_COMPLETE, $request->getState());
        $this->assertEquals(200, $request->getResponse()->getStatusCode());
        $this->assertTrue($request->getResponse()->hasHeader('X-Guzzle-Async'));
    }

    public function testEnsuresIntegration()
    {
        $this->getServer()->flush();
        $this->getServer()->enqueue("HTTP/1.1 204 FOO\r\nContent-Length: 4\r\n\r\ntest");
        $client = new Client($this->getServer()->getUrl());
        $request = $client->post('/', null, array(
            'foo' => 'bar'
        ));
        $request->getEventDispatcher()->addSubscriber(new AsyncPlugin());
        $request->send();
        $this->assertEquals('', $request->getResponse()->getBody(true));
        $this->assertTrue($request->getResponse()->hasHeader('X-Guzzle-Async'));
        $received = $this->getServer()->getReceivedRequests(true);
        $this->assertEquals('POST', $received[0]->getMethod());
    }
}
