<?php

namespace WPRemoteMediaExt\Guzzle\Service\Command;

use WPRemoteMediaExt\Guzzle\Http\Message\RequestInterface;
use WPRemoteMediaExt\Guzzle\Service\Command\CommandInterface;

/**
 * Translates command options and operation parameters into a request object
 */
interface RequestSerializerInterface
{
    /**
     * Create a request for a command
     *
     * @param CommandInterface $command Command that will own the request
     *
     * @return RequestInterface
     */
    public function prepare(CommandInterface $command);
}
