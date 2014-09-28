<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Accounts;

use WPRemoteMediaExt\Guzzle\Service\Client;

abstract class AbstractRemoteClient extends Client
{
    /**
    * Factory method to create a new RemoteClient
    *
    * @param array|Collection $config Configuration data
    *
    * @return self
    */
}
