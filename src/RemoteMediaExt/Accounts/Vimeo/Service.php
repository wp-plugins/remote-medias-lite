<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Accounts\Vimeo;

use WPRemoteMediaExt\RemoteMediaExt\Accounts\AbstractRemoteAccount;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\AbstractRemoteService;
use WPRemoteMediaExt\RemoteMediaExt\Library\MediaTemplate;
use WPRemoteMediaExt\WPCore\View;
use WPRemoteMediaExt\WPForms\FieldSet;

class Service extends AbstractRemoteService
{
    public function __construct()
    {
        parent::__construct(__('Vimeo', 'remote-medias-lite'), 'vimeo');

        $client = Client::factory();
        $this->setClient($client);
    }

    public function init()
    {
        if (is_admin()) {

            $this->mediaSettings = array('uploadTemplate' => 'media-upload-vimeo-upgrade');
            $this->hook(new MediaTemplate(new View($this->getViewsPath().'admin/media-upload-vimeo.php')));

            //FieldSets need to be initialized early because they hook needed JS and CSS for fields added
            $this->initFieldSet();
        }
    }

    public function initFieldSet()
    {
        $this->fieldSet = new FieldSet();
        $field = array(
            'label' => __("Vimeo User ID", 'remote-medias-lite'),
            'type' => 'Text',
            'class' => $this->getSlug(),
            'id' => 'remote_user_id',
            'name' => 'account_meta['.$this->getSlug().'][vimeo_remote_user_id]',
            'desc' => __("Insert the Vimeo User ID for this library", 'remote-medias-lite'),
        );
        $this->fieldSet->addField($field);
    }

    public function validate()
    {

        $params = array(
            'user_id' => $this->account->get('vimeo_remote_user_id'),
            'request' => 'info'
        );
        $command = $this->client->getCommand('UserRequest', $params);
        $response = $this->client->execute($command);

        return $command->getResponse()->isSuccessful();
    }

    public function getUserInfo()
    {

        $params = array(
            'user_id' => $this->account->get('vimeo_remote_user_id'),
            'request' => 'info'
        );
        $command = $this->client->getCommand('UserRequest', $params);
        $response = $this->client->execute($command);

        return $response;
    }

    /*
    * From https://developer.vimeo.com/apis/simple
    * Simple API responses include up to 20 items per page.
    *
    * By adding the ?page parameter to the URL, you can retrieve up to 3 pages 
    * of data. If you need more than the maximum of 60 items, you must use the 
    * New API.
    */
    public function getUserMedias()
    {
        $params = array(
            'user_id' => $this->account->get('vimeo_remote_user_id'),
            'request' => 'videos'
        );
        $command = $this->client->getCommand('UserRequest', $params);
        $response = $this->client->execute($command);

        return $response;
    }

    public function getUserAttachments()
    {
        $perpage = 40;
        $searchTerm = '';

        if (isset($_POST['query']['posts_per_page'])) {
            $perpage = absint($_POST['query']['posts_per_page']);
        }
        if (isset($_POST['query']['s'])) {
            $searchTerm = sanitize_text_field($_POST['query']['s']);
        }

        //Vimeo return 20 max items per page
        $response = $this->GetUserMedias();
        $medias = $response->getAll();

        $attachments = array();

        foreach ($medias as $i => $media) {
            $remoteMedia = new RemoteMedia($media);
            $remoteMedia->setAccount($this->getAccount());
            $attachments[] = $remoteMedia->toMediaManagerAttachment();
        }
        
        return $attachments;
    }
}
