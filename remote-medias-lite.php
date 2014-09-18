<?php
/*
Plugin Name: Remote Medias Library Extension
Plugin URI: http://onecodeshop.com/
Description: This plugins seamlessly integrates 3rd party medias APIs to WP media manager
Version: 1.0.0
Author: Louis-Michel Raynauld
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

        parent::__construct(__FILE__, 'Remote Medias Extension', 'remote-medias-lite');

        $this->setReqWpVersion("3.5");
        $this->setReqWPMsg(sprintf(__('%s Requirements failed. WP version must at least %s', 'remote-medias-lite'), $this->getName(), $this->reqWPVersion));
        $this->setReqPhpVersion("5.3.3");
        $this->setReqPHPMsg(sprintf(__('%s Requirements failed. PHP version must at least %s', 'remote-medias-lite'), $this->getName(), $this->reqPHPVersion));
               
        $this->setMainFeature(FRemoteMediaExt::getInstance());
        parent::init();
    }
}

$pwebremotemediasext = PRemoteMedias::getInstance();
$pwebremotemediasext->register();
