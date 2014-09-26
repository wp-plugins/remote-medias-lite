<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Accounts;

class RemoteMediaFactory
{
    public static function create($subtype, $metadata = array())
    {
        $rmclass = 'WPRemoteMediaExt\\RemoteMediaExt\\Accounts\\'.ucfirst($subtype).'\\RemoteMedia';
        if (class_exists($rmclass)) {
            return new $rmclass($metadata);
        }

        return null;
    }
}
