<?php
namespace WPRemoteMediaExt;

class PluginAutoloader
{
    private static $loader;

    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        $vendorDir = __DIR__.'/vendor';

        if (!class_exists('\Composer\Autoload\ClassLoader')) {
            require $vendorDir . '/composer/ClassLoader.php';
        }
        $loaderClass = __NAMESPACE__.'\WPCore\WPpluginLoader';
        if (!class_exists($loaderClass)) {
            require $vendorDir.'/loumray/wpcore/src/WPpluginLoader.php';
        }

        self::$loader = $loader = new $loaderClass;

        $map = require $vendorDir . '/composer/autoload_namespaces.php';
        foreach ($map as $namespace => $path) {
            $loader->add(__NAMESPACE__.'\\'.$namespace, $path);
        }

        $map = require $vendorDir . '/composer/autoload_psr4.php';
        foreach ($map as $namespace => $path) {
            //If namespace already start with wrapper namespace
            if (strpos($namespace, __NAMESPACE__) === 0) {
                $loader->addPsr4($namespace, $path);
            } else {
                $loader->addPsr4(__NAMESPACE__.'\\'.$namespace, $path);
            }
        }

        $classMap = require $vendorDir . '/composer/autoload_classmap.php';
        if ($classMap) {
            $loader->addClassMap($classMap);
        }
        $loader->register(true);

        return $loader;
    }
}
return PluginAutoloader::getLoader();
