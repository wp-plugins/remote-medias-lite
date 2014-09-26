<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Accounts\Flickr;

use WPRemoteMediaExt\RemoteMediaExt\Accounts\AbstractRemoteAccount;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\AbstractRemoteService;
use WPRemoteMediaExt\RemoteMediaExt\Library\MediaTemplate;
use WPRemoteMediaExt\WPCore\View;
use WPRemoteMediaExt\WPForms\FieldSet;

class Service extends AbstractRemoteService
{
    public function __construct()
    {
          parent::__construct(__('Flickr Basic'), 'flickr');

          $client = Client::factory();
          $this->setClient($client);
    }

    public function init()
    {
        if (is_admin()) {

            $this->mediaSettings = array('uploadTemplate' => 'media-upload-flickr-upgrade');
            $this->hook(
                new MediaTemplate(
                    new View(
                        $this->getViewsPath().'admin/media-upload-flickr.php',
                        array()
                    )
                )
            );

            //FieldSets need to be initialized early because they hook needed JS and CSS for fields added
            $this->initFieldSet();

        }
    }

    public function initFieldSet()
    {
        $this->fieldSet = new FieldSet();
        $field = array(
            'label' => __("Flickr User NSID", 'remote-medias-lite'),
            'type' => 'Text',
            'class' => $this->getSlug(),
            'id' => 'remote_user_id',
            'placeholder' => 'XXXXXXXX@NXX',
            'name' => 'account_meta['.$this->getSlug().'][remote_user_id]',
            'desc' => __("Insert the Flickr User NSID for this library", 'remote-medias-lite'),
        );
        $this->fieldSet->addField($field);
    }

    public function validate()
    {
        $params = array(
            'user_id' => $this->account->get('remote_user_id'),
        );
        $command = $this->client->getCommand('UserPublicPhotos', $params);
        $response = $this->client->execute($command);

        return $command->getResponse()->isSuccessful();
    }

    public function getUserInfo()
    {

        // $params = array(
        //     'user_id' => $account->get('remote_user_id'),
        //     'request' => 'info'
        // );
        // $command = $this->client->getCommand('UserRequest',$params);
        // $response = $this->client->execute($command);

        return false;
    }

    public function getUserMedias()
    {
        $params = array(
            'user_id' => $this->account->get('remote_user_id'),
        );
        $command = $this->client->getCommand('UserPublicPhotos', $params);
        $response = $this->client->execute($command);

        $medias = array();

        if ($command->getResponse()->isSuccessful()) {
            $data = unserialize($response->getBody());

            if (!empty($data['items'])) {
                $medias = $data['items'];
            }
        }

        return $medias;
    }

    public function getUserAttachments()
    {
        $medias = $this->GetUserMedias();

        $attachments = array();

        foreach ($medias as $i => $media) {
            $remoteMedia = new RemoteMedia($media);
            $remoteMedia->setAccount($this->getAccount());
            $attachments[$i] = $remoteMedia->toMediaManagerAttachment();
        }
        return $attachments;
    }
}