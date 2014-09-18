<?php

namespace WPRemoteMediaExt\Guzzle\Service\Command\LocationVisitor\Request;

use WPRemoteMediaExt\Guzzle\Http\Message\RequestInterface;
use WPRemoteMediaExt\Guzzle\Service\Command\CommandInterface;
use WPRemoteMediaExt\Guzzle\Service\Description\Parameter;

/**
 * Visitor used to change the location in which a response body is saved
 */
class ResponseBodyVisitor extends AbstractRequestVisitor
{
    public function visit(CommandInterface $command, RequestInterface $request, Parameter $param, $value)
    {
        $request->setResponseBody($value);
    }
}
