<?php
/*
Plugin Name: Remote Medias Libraries
Plugin URI: http://onecodeshop.com/
Description: Integrates 3rd party medias to WP media manager
Version: 1.1.1
Author: Team OneCodeShop.com
Author URI: http://onecodeshop.com/
*/
namespace WPRemoteMediaExt;

use WPRemoteMediaExt\WPCore\admin\WPadminNotice;
use WPRemoteMediaExt\WPCore\View;
use WPRemoteMediaExt\WPCore\WPpluginTextDomain;
use WPRemoteMediaExt\RemoteMediaExt\FRemoteMediaExt;
use WPRemoteMediaExt\WPCore\WPplugin;

require 'autoload.php';

class PRemoteMedias extends WPplugin
{

    public static $instance;

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct()
    {
        load_plugin_textdomain('remote-medias-lite', false, dirname(plugin_basename(__FILE__)).'/lang');

        parent::__construct(__FILE__, 'Remote Media Libraries', 'remote-medias-lite');

        $this->setReqWpVersion("3.5");
        $this->setReqWPMsg(sprintf(__('%s <strong>Requirements failed.</strong> WP version must <strong>at least %s</strong>. You are using version '.$GLOBALS['wp_version'], 'remote-medias-lite'), $this->getName(), $this->reqWPVersion));
        $this->setReqPhpVersion("5.3.3");
        $this->setReqPHPMsg(sprintf(__('%s <strong>Requirements failed.</strong> PHP version must <strong>at least %s</strong>. You are using version '. PHP_VERSION, 'remote-medias-lite'), $this->getName(), $this->reqPHPVersion));
               
        $this->setMainFeature(FRemoteMediaExt::getInstance());
        parent::init();
    }
}

$pwebremotemediasext = PRemoteMedias::getInstance();
$pwebremotemediasext->register();
