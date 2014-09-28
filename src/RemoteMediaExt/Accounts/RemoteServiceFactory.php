<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Accounts;

class RemoteServiceFactory
{
    public static $classes = array();

    public static function create($class)
    {
        $class = self::className($class);

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

    public static function className($class)
    {
        switch ($class) {
            case '\WPRemoteMediaExt\RemoteMediaExt\Accounts\Dailymotion\ServiceDailymotionSimple':
                $class = '\WPRemoteMediaExt\RemoteMediaExt\Accounts\Dailymotion\Service';
                break;
            case '\WPRemoteMediaExt\RemoteMediaExt\Accounts\Vimeo\ServiceVimeoSimple':
                $class = '\WPRemoteMediaExt\RemoteMediaExt\Accounts\Vimeo\Service';
                break;
            case '\WPRemoteMediaExt\RemoteMediaExt\Accounts\Youtube\ServiceYoutubeSimple':
                $class = '\WPRemoteMediaExt\RemoteMediaExt\Accounts\Youtube\Service';
                break;
            default:
                return $class;
        }

        return $class;
    }
    public static function getDbClassName($classname)
    {
        return str_replace('\\', '_', $classname);
    }

    public static function retrieveClassName($dbclassname)
    {
        $retrievedClass = str_replace('_', '\\', $dbclassname);
        $retrievedClass = self::className($retrievedClass);
        return $retrievedClass;
    }

    public static function addClass($type, $class)
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
