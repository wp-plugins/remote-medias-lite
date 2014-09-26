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
        parent::__construct(__('Vimeo Basic'), 'vimeo');

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
            'name' => 'account_meta['.$this->getSlug().'][remote_user_id]',
            'desc' => __("Insert the Vimeo User ID for this library", 'remote-medias-lite'),
        );
        $this->fieldSet->addField($field);
    }

    public function validate()
    {

      $params = array(
          'user_id' => $this->account->get('remote_user_id'),
          'request' => 'info'
      );
      $command = $this->client->getCommand('UserRequest', $params);
      $response = $this->client->execute($command);

      return $command->getResponse()->isSuccessful();
    }

    public function getUserInfo()
    {

        $params = array(
            'user_id' => $this->account->get('remote_user_id'),
            'request' => 'info'
        );
        $command = $this->client->getCommand('UserRequest', $params);
        $response = $this->client->execute($command);

        return $response;
    }

    public function getUserMedias()
    {
        $params = array(
            'user_id' => $this->account->get('remote_user_id'),
            'request' => 'videos'
        );
        $command = $this->client->getCommand('UserRequest', $params);
        $response = $this->client->execute($command);

        return $response;
    }

    public function getUserAttachments()
    {
        $response = $this->GetUserMedias();
        $medias = $response->getAll();

        $attachments = array();

        foreach($medias as $i => $media) {
            $remoteMedia = new RemoteMedia($media);
            $remoteMedia->setAccount($this->getAccount());
            $attachments[$i] = $remoteMedia->toMediaManagerAttachment();
        }
        return $attachments;
    }
}