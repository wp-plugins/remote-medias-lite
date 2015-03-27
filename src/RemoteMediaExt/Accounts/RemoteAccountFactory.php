<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Accounts;

class RemoteAccountFactory
{
    public static function create($rmID)
    {
        return new RemoteAccount($rmID);
    }

    public static function createFromService($serviceClass)
    {
        $service = RemoteServiceFactory::create($serviceClass);
        if (!is_null($service)) {
            $newaccount = new RemoteAccount();
            $newaccount->setService($service);
            $newaccount->setType(RemoteServiceFactory::getType($serviceClass));

            return $newaccount;
        }

        return null;
    }
}
