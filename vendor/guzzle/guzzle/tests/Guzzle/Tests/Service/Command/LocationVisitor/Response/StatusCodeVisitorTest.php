<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Service\Command\LocationVisitor\Response;

use WPRemoteMediaExt\Guzzle\Service\Description\Parameter;
use WPRemoteMediaExt\Guzzle\Http\Message\Response;
use WPRemoteMediaExt\Guzzle\Service\Command\LocationVisitor\Response\StatusCodeVisitor as Visitor;

/**
 * @covers WPRemoteMediaExt\Guzzle\Service\Command\LocationVisitor\Response\StatusCodeVisitor
 */
class StatusCodeVisitorTest extends AbstractResponseVisitorTest
{
    public function testVisitsLocation()
    {
        $visitor = new Visitor();
        $param = new Parameter(array('location' => 'statusCode', 'name' => 'code'));
        $visitor->visit($this->command, $this->response, $param, $this->value);
        $this->assertEquals(200, $this->value['code']);
    }
}
