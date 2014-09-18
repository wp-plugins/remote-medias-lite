<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Accounts;

class RemoteAccountFactory
{
    //TODO create by id and create by type
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

            return $newaccount;
        }

        return null;
    }
}
