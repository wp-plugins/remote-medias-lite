<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Accounts\Dailymotion;

use WPRemoteMediaExt\RemoteMediaExt\Accounts\AbstractRemoteAccount;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\AbstractRemoteService;
use WPRemoteMediaExt\RemoteMediaExt\Library\MediaTemplate;
use WPRemoteMediaExt\WPCore\View;
use WPRemoteMediaExt\WPForms\FieldSet;

class Service extends AbstractRemoteService
{
    public function __construct()
    {
        parent::__construct(__('Dailymotion Basic'), 'dailymotion');

        $client = Client::factory();
        $this->setClient($client);
    }

    public function init()
    {
        if (is_admin()) {

            $this->mediaSettings = array('uploadTemplate' => 'media-upload-dailymotion-upgrade');
            $this->hook(new MediaTemplate(new View($this->getViewsPath().'admin/media-upload-dailymotion.php')));

            //FieldSets need to be initialized early because they hook needed JS and CSS for fields added
            $this->initFieldSet();
        }
    }

    public function initFieldSet()
    {
        $this->fieldSet = new FieldSet();
        $field = array(
            'label' => __("Dailymotion User ID", 'remote-medias-lite'),
            'type' => 'Text',
            'class' => $this->getSlug(),
            'id' => 'remote_user_id',
            'name' => 'account_meta['.$this->getSlug().'][remote_user_id]',
            'desc' => __("Insert the Dailymotion User ID for this library", 'remote-medias-lite'),
        );

        $this->fieldSet->addField($field);
    }

    public function validate()
    {

        $params = array(
            'user_id' => $this->account->get('remote_user_id'),
            'fields'  => 'id,screenname,status,username'
        );
        $command = $this->client->getCommand('UserRequest',$params);
        $response = $this->client->execute($command);

        $response = $response->getAll();

        if ($command->getResponse()->isSuccessful() &&
           $response['status'] === 'active'
        ) {
            return true;
        }
        return false;
    }

    public function getUserInfo()
    {

        $params = array(
            'user_id' => $this->account->get('remote_user_id'),
            'fields'  => 'id,title,description,created_time,modified_time,owner,thumbnail_120_url,thumbnail_url,url'
        );
        $command = $this->client->getCommand('UserRequest',$params);
        $response = $this->client->execute($command);

        return $response;
    }

    public function getUserMedias()
    {
        $params = array(
            'user_id' => $this->account->get('remote_user_id'),
            'fields'  => 'id,title,description,created_time,modified_time,owner,thumbnail_120_url,thumbnail_url,url'
        );
        $command = $this->client->getCommand('UserVideosRequest',$params);
        $response = $this->client->execute($command);

        return $response;
    }

    public function getUserAttachments()
    {
        $response = $this->GetUserMedias();
        $medias = $response->getAll();

        $attachments = array();

        foreach ($medias['list'] as $i => $media) {
            $remoteMedia = new RemoteMedia($media);
            $remoteMedia->setAccount($this->getAccount());
            $attachments[$i] = $remoteMedia->toMediaManagerAttachment();
        }
        return $attachments;
    }
}
