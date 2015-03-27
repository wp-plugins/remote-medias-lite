<?php
namespace WPRemoteMediaExt\RemoteMediaExt;

use WPRemoteMediaExt\RemoteMediaExt\Accounts as RemoteService;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\AbstractRemoteService;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\AbstractRemoteAccount;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\RemoteServiceFactory;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\MetaBoxService;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\MetaBoxServiceLoader;

use WPRemoteMediaExt\RemoteMediaExt\Ajax\AjaxQueryValidation;
use WPRemoteMediaExt\RemoteMediaExt\Ajax\AjaxSendRemoteToEditor;
use WPRemoteMediaExt\RemoteMediaExt\Ajax\AjaxQueryAttachments;
use WPRemoteMediaExt\RemoteMediaExt\Ajax\AjaxUserInfo;

use WPRemoteMediaExt\RemoteMediaExt\Library\MediaArraySettings;
use WPRemoteMediaExt\RemoteMediaExt\Library\MediaTemplate;
use WPRemoteMediaExt\RemoteMediaExt\Library\MediaSettings;

use WPRemoteMediaExt\WPCore\admin\WPfeaturePointer;
use WPRemoteMediaExt\WPCore\admin\WPfeaturePointerLoader;
use WPRemoteMediaExt\WPCore\View;

use WPRemoteMediaExt\WPCore\WPfeature;
use WPRemoteMediaExt\WPCore\WPscriptAdmin;
use WPRemoteMediaExt\WPCore\WPstyleAdmin;

use WPRemoteMediaExt\WPForms\FieldSet;

class FRemoteMediaExt extends WPfeature
{
    public static $instance;

    protected $version = '1.2.0';
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

        $this->accountPostType = new AccountPostType();

        $this->hook($this->accountPostType);

        //Hook Vimeo Support
        $service = new RemoteService\Vimeo\Service();
        $service->setBasePath($this->getBasePath());
        $service->setAccountPostType($this->accountPostType);
        $this->hook($service);
        $this->addRemoteService($service);

        //Hook Youtube Support
        $service = new RemoteService\Youtube\Service();
        $service->setBasePath($this->getBasePath());
        $service->setAccountPostType($this->accountPostType);
        $this->hook($service);
        $this->addRemoteService($service);

        //Hook Dailymotion Support
        $service = new RemoteService\Dailymotion\Service();
        $service->setBasePath($this->getBasePath());
        $service->setAccountPostType($this->accountPostType);
        $this->hook($service);
        $this->addRemoteService($service);

        //Hook Flickr Support
        $service = new RemoteService\Flickr\Service();
        $service->setBasePath($this->getBasePath());
        $service->setAccountPostType($this->accountPostType);
        $this->hook($service);
        $this->addRemoteService($service);

        //Hook Instagram Support
        $service = new RemoteService\Instagram\Service();
        $service->setBasePath($this->getBasePath());
        $service->setAccountPostType($this->accountPostType);
        $this->hook($service);
        $this->addRemoteService($service);

        //Hook ajax service for accounts validation
        $this->ajaxQueryValidation = new AjaxQueryValidation();
        $this->hook($this->ajaxQueryValidation);

        //Hook ajax service for accounts attachments fetching
        $this->ajaxqueryAttachments = new AjaxQueryAttachments();
        $this->hook($this->ajaxqueryAttachments);

        //Hook ajax service for accounts send to editor action
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
        $this->initPointers();

        $this->initMetaboxes();

        //MediaArraySettings
        $this->hook($this->serviceSetting);

        //Add Media List in MediaLibrary
        $this->hook(new MediaSettings('remoteMediaAccounts'));

        $this->hook(new MediaTemplate(new View($this->getViewsPath().'admin/media-remote-attachment.php')));
        $this->hook(new MediaTemplate(new View($this->getViewsPath().'admin/media-friendly-banner.php')));
        $this->addScript(new WPscriptAdmin(array('post.php' => array(), 'post-new.php' => array()), 'jquery-cookie', $this->getJsUrl().'jquery-cookie.min.js', $this->getJsUrl().'jquery-cookie.js', array('jquery'), $this->version));
        $this->addScript(new WPscriptAdmin(array('post.php' => array(), 'post-new.php' => array()), 'media-remote-ext', $this->getJsUrl().'media-remote-ext.min.js', $this->getJsUrl().'media-remote-ext.js', array('media-editor','media-views'), $this->version));
        $this->addStyle(new WPstyleAdmin(array(), 'media-remote-admin-css', $this->getCssUrl().'media-remote-admin.min.css', $this->getCssUrl().'media-remote-admin.css', array(), $this->version));
    }
    
    public function initTheme()
    {

    }

    public function initPointers()
    {
        //New Menu Feature Pointer
        $this->fPointerAccounts = new WPfeaturePointer(
            'rml_accounts_v100',
            '<h3>'.__('New Menu Added', 'remote-medias-lite').'</h3>'.
            '<p>'.sprintf(__('Add %sremote medias accounts%s here and access any medias directly from your media manager!', 'remote-medias-lite'), '<a href="'.$this->accountPostType->getAdminUrl().'">', '</a>').'</p>',
            '#menu-media',
            array(
                'edge' => 'left',
                'align' => 'center'
            )
        );

        //New Media Manager Extension Applied Feature Pointer
        $this->fPointerMediaManager = new WPfeaturePointer(
            'rml_media_v100',
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
    }

    public function initMetaboxes()
    {
        $this->addScript(new WPscriptAdmin(array('post.php' => array('post_type' => $this->accountPostType->getSlug()), 'post-new.php' => array('post_type' => $this->accountPostType->getSlug())), 'rmedias-query-test', $this->getJsUrl().'media-remote-query-test.min.js', $this->getJsUrl().'media-remote-query-test.js', array(), $this->version));
        $this->addScript(new WPscriptAdmin(array('post.php' => array('post_type' => $this->accountPostType->getSlug()), 'post-new.php' => array('post_type' => $this->accountPostType->getSlug())), 'media-remote-account', $this->getJsUrl().'rml-account.min.js', $this->getJsUrl().'rml-account.js', array(), $this->version));
        
        //Main metabox for Account Service selection
        $metabox = new MetaBoxService(
            new View(
                $this->getViewsPath().'admin/metaboxes/account-settings.php',
                array('fRemoteMediaExt' => $this) //view data
            ),
            'rml_service_selection',
            __('Service Selection', 'remote-medias-lite'),
            $this->accountPostType->getSlug(),
            'normal',
            'high'
        );
        $this->hook(new MetaBoxServiceLoader($metabox));

        $metabox = new MetaBoxService(
            new View(
                $this->getViewsPath().'admin/metaboxes/basic-settings.php'
            ),
            'rml_account_settings',
            __('Account Settings', 'remote-medias-lite'),
            $this->accountPostType->getSlug(),
            'normal',
            'default'
        );
        $this->hook(new MetaBoxServiceLoader($metabox));

        //Main metabox for Account Status and Action buttons
        $metabox = new MetaBoxService(
            new View($this->getViewsPath().'admin/metaboxes/status-actions.php'),
            'remote_media_actions',
            __('Status & Actions', 'remote-medias-lite'),
            $this->accountPostType->getSlug(),
            'side', //'normal', 'advanced', or 'side'
            'high' //'high', 'core', 'default' or 'low'
        );
        $this->hook(new MetaBoxServiceLoader($metabox));
    }

    public function getBasicFieldSet(AbstractRemoteAccount $account)
    {
        $fieldSet = new FieldSet();

        $services = array();
        foreach ($this->getRemoteServices() as $service) {
            $services[$service->getSlug()] = $service->getName();
        }

        $field = array(
            'label' => __("Remote Service", 'remote-medias-lite'),
            'type' => 'Select',
            'id' => 'remote_media_type',
            'name' => 'account_meta[remote_account_type]',
            'class' => 'all',
            'options' => $services,
            'value' => $account->get('type'),
            'desc' => __("Choose the type of service you want to connect.", 'remote-medias-lite'),
        );
        $fieldSet->addField($field);

        return $fieldSet;
    }

    public function uninstall()
    {
        $uid = get_current_user_id();

        if (is_null($this->fPointerAccounts) ||
            is_null($this->fPointerMediaManager)
        ) {
            $this->initPointers();
        }

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
