<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Service\Command\LocationVisitor\Response;

use WPRemoteMediaExt\Guzzle\Service\Description\Parameter;
use WPRemoteMediaExt\Guzzle\Http\Message\Response;
use WPRemoteMediaExt\Guzzle\Service\Command\LocationVisitor\Response\BodyVisitor as Visitor;

/**
 * @covers WPRemoteMediaExt\Guzzle\Service\Command\LocationVisitor\Response\BodyVisitor
 */
class BodyVisitorTest extends AbstractResponseVisitorTest
{
    public function testVisitsLocation()
    {
        $visitor = new Visitor();
        $param = new Parameter(array('location' => 'body', 'name' => 'foo'));
        $visitor->visit($this->command, $this->response, $param, $this->value);
        $this->assertEquals('Foo', (string) $this->value['foo']);
    }
}
