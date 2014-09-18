<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Service\Command\LocationVisitor\Response;

use WPRemoteMediaExt\Guzzle\Service\Description\Parameter;
use WPRemoteMediaExt\Guzzle\Http\Message\Response;
use WPRemoteMediaExt\Guzzle\Service\Command\LocationVisitor\Response\ReasonPhraseVisitor as Visitor;

/**
 * @covers WPRemoteMediaExt\Guzzle\Service\Command\LocationVisitor\Response\ReasonPhraseVisitor
 */
class ReasonPhraseVisitorTest extends AbstractResponseVisitorTest
{
    public function testVisitsLocation()
    {
        $visitor = new Visitor();
        $param = new Parameter(array('location' => 'reasonPhrase', 'name' => 'phrase'));
        $visitor->visit($this->command, $this->response, $param, $this->value);
        $this->assertEquals('OK', $this->value['phrase']);
    }
}
