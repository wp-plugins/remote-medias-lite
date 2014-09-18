<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Accounts;

class RemoteServiceFactory
{
    static $classes = array();

    public static function create($class)
    {
        if (!class_exists($class)) {
            return null;
        }

        $service = new $class();
        if ($service instanceof AbstractRemoteService) {
            self::addClass($service->getSlug(), get_class($service));

            return $service;
        }

        return null;
        
    }

    public static function addClass($type,$class)
    {
        self::$classes[$type] = $class;
    }

    public static function getClass($type)
    {
        if (isset(self::$classes[$type])) {
            return self::$classes[$type];
        }

        return null;
    }
}
