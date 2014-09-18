<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Http\Exception;

use WPRemoteMediaExt\Guzzle\Http\Message\Response;
use WPRemoteMediaExt\Guzzle\Http\Message\Request;
use WPRemoteMediaExt\Guzzle\Http\Exception\RequestException;
use WPRemoteMediaExt\Guzzle\Http\Exception\BadResponseException;

class ExceptionTest extends \Guzzle\Tests\GuzzleTestCase
{
    /**
     * @covers WPRemoteMediaExt\Guzzle\Http\Exception\RequestException
     */
    public function testRequestException()
    {
        $e = new RequestException('Message');
        $request = new Request('GET', 'http://www.guzzle-project.com/');
        $e->setRequest($request);
        $this->assertEquals($request, $e->getRequest());
    }

    /**
     * @covers WPRemoteMediaExt\Guzzle\Http\Exception\BadResponseException
     */
    public function testBadResponseException()
    {
        $e = new BadResponseException('Message');
        $response = new Response(200);
        $e->setResponse($response);
        $this->assertEquals($response, $e->getResponse());
    }

    /**
     * @covers WPRemoteMediaExt\Guzzle\Http\Exception\BadResponseException::factory
     */
    public function testCreatesGenericErrorExceptionOnError()
    {
        $request = new Request('GET', 'http://www.example.com');
        $response = new Response(307);
        $e = BadResponseException::factory($request, $response);
        $this->assertInstanceOf('WPRemoteMediaExt\Guzzle\Http\Exception\BadResponseException', $e);
    }

    /**
     * @covers WPRemoteMediaExt\Guzzle\Http\Exception\BadResponseException::factory
     */
    public function testCreatesClientErrorExceptionOnClientError()
    {
        $request = new Request('GET', 'http://www.example.com');
        $response = new Response(404);
        $e = BadResponseException::factory($request, $response);
        $this->assertInstanceOf('WPRemoteMediaExt\Guzzle\Http\Exception\ClientErrorResponseException', $e);
    }

    /**
     * @covers WPRemoteMediaExt\Guzzle\Http\Exception\BadResponseException::factory
     */
    public function testCreatesServerErrorExceptionOnServerError()
    {
        $request = new Request('GET', 'http://www.example.com');
        $response = new Response(503);
        $e = BadResponseException::factory($request, $response);
        $this->assertInstanceOf('WPRemoteMediaExt\Guzzle\Http\Exception\ServerErrorResponseException', $e);
    }
}
