<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Accounts;

class RemoteAccount extends AbstractRemoteAccount
{
    public function __construct($raId = null, $type = null)
    {
        parent::__construct($raId, $type);
    }
}
