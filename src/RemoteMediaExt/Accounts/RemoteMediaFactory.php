<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Accounts;

class RemoteMediaFactory
{
    public static function createFromAccountid($accountId, $metadata = array())
    {
        $account = RemoteAccountFactory::create($accountId);
        $baseNamespace = $account->getServiceNamespace();
        $rmclass = $baseNamespace.'\\RemoteMedia';
        if (class_exists($rmclass)) {
            return new $rmclass($metadata);
        }
        return null;
    }

    public static function create($subtype, $metadata = array())
    {
        $rmclass = 'WPRemoteMediaExt\\RemoteMediaExt\\Accounts\\'.ucfirst($subtype).'\\RemoteMedia';
        if (class_exists($rmclass)) {
            return new $rmclass($metadata);
        }

        return null;
    }
}
