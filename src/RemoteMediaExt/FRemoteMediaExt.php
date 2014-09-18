<?php
namespace WPRemoteMediaExt\RemoteMediaExt;

use WPRemoteMediaExt\RemoteMediaExt\Ajax\AjaxQueryValidation;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\Dailymotion\ServiceDailymotionSimple;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\Youtube\ServiceYoutubeSimple;
use WPRemoteMediaExt\RemoteMediaExt\Library\MediaArraySettings;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\RemoteAccount;
use WPRemoteMediaExt\RemoteMediaExt\Library\MediaTemplate;
use WPRemoteMediaExt\RemoteMediaExt\Ajax\AjaxSendRemoteToEditor;
use WPRemoteMediaExt\RemoteMediaExt\Medias\RemoteInsertToEditor;
use WPRemoteMediaExt\RemoteMediaExt\Library\MediaSettings;
use WPRemoteMediaExt\RemoteMediaExt\Ajax\AjaxQueryAttachments;
use WPRemoteMediaExt\RemoteMediaExt\Library\MediaLibrarySection;
use WPRemoteMediaExt\RemoteMediaExt\Ajax\AjaxUserInfo;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\RemoteServiceFactory;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\RemoteAccountFactory;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\MetaBoxService;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\MetaBoxServiceLoader;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\AbstractRemoteService;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\Vimeo\ServiceVimeoSimple;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\AbstractRemoteAccount;
use WPRemoteMediaExt\WPCore\WPscriptAdmin;
use WPRemoteMediaExt\WPCore\WPstyleAdmin;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\MetaBoxVideoSettings;
use WPRemoteMediaExt\WPCore\View;
use WPRemoteMediaExt\WPCore\WPposttype;
use WPRemoteMediaExt\WPCore\WPfeature;
use WPRemoteMediaExt\WPCore\admin\WPfeaturePointer;
use WPRemoteMediaExt\WPCore\admin\WPfeaturePointerLoader;

class FRemoteMediaExt extends WPfeature
{
    public static $instance;

    protected $version = '1.0.0';
    protected $accountPostType;
    protected $remoteServices = array();

    protected $serviceSetting;
    protected $fPointerAccounts;
    protected $fPointerMediaManager;

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct()
    {
        parent::__construct('feature-remote-medias', 'feature-remote-medias');
    }

    public function init()
    {
        $this->serviceSetting = new MediaArraySettings('remoteServiceSettings');

        $args = array(
            'labels' => array(
                'name' => __('Remote Libraries', 'remote-medias-lite'),
                'singular_label' => __('Remote Library', 'remote-medias-lite'),
                'add_new' => _x('Add New', 'Remote Library', 'remote-medias-lite'),
                'add_new_item' => _x('Add New Remote Library', 'Remote Library', 'remote-medias-lite'),
                'edit_item' => _x('Edit Remote Library', 'Remote Library', 'remote-medias-lite'),
                'new_item' => _x('New Remote Library', 'Remote Library', 'remote-medias-lite'),
                'view_item' => _x('View Remote Library', 'Remote Library', 'remote-medias-lite'),
                'search_items' => _x('Search Remote Libraries', 'Remote Library', 'remote-medias-lite'),
                'not_found' => _x('No Remote Library found', 'Remote Library', 'remote-medias-lite'),
                'not_found_in_trash' => _x('No Remote Library found in Trash', 'Remote Library', 'remote-medias-lite'),
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => 'upload.php',
            'capability_type' => 'page',
            'hierarchical' => true,
            'supports' => array('title')
        );

        $this->accountPostType = new WPposttype('rmlaccounts', $args);
        $this->hook($this->accountPostType);

        $service = new ServiceVimeoSimple();
        $service->setBasePath($this->getBasePath());
        $service->setBaseUrl($this->getBaseUrl());
        $service->setAccountPostType($this->accountPostType);
        $service->init();
        $this->addRemoteService($service);
        $this->hook($service);
        $service = new ServiceYoutubeSimple();
        $service->setBasePath($this->getBasePath());
        $service->setBaseUrl($this->getBaseUrl());
        $service->setAccountPostType($this->accountPostType);
        $service->init();
        $this->addRemoteService($service);
        $this->hook($service);
        $service = new ServiceDailymotionSimple();
        $service->setBasePath($this->getBasePath());
        $service->setBaseUrl($this->getBaseUrl());
        $service->setAccountPostType($this->accountPostType);
        $service->init();
        $this->addRemoteService($service);
        $this->hook($service);

        //TODO move into services?
        $this->ajaxQueryValidation = new AjaxQueryValidation();
        $this->hook($this->ajaxQueryValidation);

        $this->ajaxqueryAttachments = new AjaxQueryAttachments();
        $this->hook($this->ajaxqueryAttachments);
        $this->ajaxSendRemoteToEditor = new AjaxSendRemoteToEditor();
        $this->hook($this->ajaxSendRemoteToEditor);

        if (is_admin()) {
            $this->initAdmin();
        } else {
            $this->initTheme();
        }
    }

    public function initAdmin()
    {
        $this->fPointerAccounts = new WPfeaturePointer(
            'rml_accounts_v'.str_replace('.', '', $this->version),
            '<h3>'.__('New Menu Added', 'remote-medias-lite').'</h3>'.
            '<p>'.sprintf(__('Add %sremote medias accounts%s here and access any medias directly from your media manager!', 'remote-medias-lite'), '<a href="'.$this->accountPostType->getAdminUrl().'">', '</a>').'</p>',
            '#menu-media',
            array(
                'edge' => 'left',
                'align' => 'center'
            )
        );
        $this->fPointerMediaManager = new WPfeaturePointer(
            'rml_media_v'.str_replace('.', '', $this->version),
            '<h3>'.__('Media Manager Extended', 'remote-medias-lite').'</h3>'.
            '<p>'.sprintf(__('You can now access medias of %sremote accounts%s directly from the media manager. Check it out!', 'remote-medias-lite'), '<a href="'.$this->accountPostType->getAdminUrl().'">', '</a>').'</p>',
            '.insert-media',
            array(
                'edge' => 'left',
                'align' => 'center'
            ),
            array('post', 'page')
        );
        $fpl = new WPfeaturePointerLoader($this->getJsUrl(), 'pointersRML');
        $fpl->addPointer($this->fPointerMediaManager);
        $fpl->addPointer($this->fPointerAccounts);
        $this->hook($fpl);

        $metabox = new MetaBoxService(
            new View($this->getViewsPath().'admin/metaboxes/account-settings.php'),
            'remote_media_account_settings',
            __('Account Settings', 'remote-medias-lite'),
            $this->accountPostType->getSlug(),
            'normal',
            'high'
        );
        $this->hook(new MetaBoxServiceLoader($metabox));

        $this->hook($this->serviceSetting);

        //Add Media List in MediaLibrary
        $this->hook(new MediaSettings('remoteMediaAccounts'));

        $this->hook(new MediaTemplate(new View($this->getViewsPath().'admin/media-upload-default.php')));
        $this->hook(new MediaTemplate(new View($this->getViewsPath().'admin/media-remote-attachment.php')));
        $this->addScript(new WPscriptAdmin(array('post.php' => array(), 'post-new.php' => array()), 'media-remote-ext', $this->getJsUrl().'media-remote-ext.min.js', $this->getJsUrl().'media-remote-ext.js', array('media-editor','media-views')));
        $this->addScript(new WPscriptAdmin(array('post.php' => array('post_type' => $this->accountPostType->getSlug()), 'post-new.php' => array('post_type' => $this->accountPostType->getSlug())), 'media-remote-account', $this->getJsUrl().'rml-account.min.js', $this->getJsUrl().'rml-account.js', array()));
        $this->addStyle(new WPstyleAdmin(array(), 'media-remote-admin-css', $this->getCssUrl().'media-remote-admin.min.css', $this->getCssUrl().'media-remote-admin.css'));
    }

    public function initTheme()
    {

    }

    public function uninstall()
    {
        $uid = get_current_user_id();
        $this->fPointerAccounts->clearDismissed($uid);
        $this->fPointerMediaManager->clearDismissed($uid);
    }

    public function addRemoteService(AbstractRemoteService $service)
    {
        $this->remoteServices[$service->getSlug()] = $service;
        $this->serviceSetting->addSetting($service->getSlug(), $service->getSettings());
        RemoteServiceFactory::addClass($service->getSlug(), get_class($service));
    }

    public function getRemoteServices()
    {
        return $this->remoteServices;
    }

    public function getAccountPostType()
    {
        return $this->accountPostType;
    }
}
