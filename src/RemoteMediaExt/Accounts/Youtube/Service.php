<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Accounts\Youtube;

use WPRemoteMediaExt\RemoteMediaExt\Accounts\AbstractRemoteAccount;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\AbstractRemoteService;
use WPRemoteMediaExt\RemoteMediaExt\Library\MediaTemplate;
use WPRemoteMediaExt\WPCore\View;
use WPRemoteMediaExt\WPForms\FieldSet;

class Service extends AbstractRemoteService
{
    protected $command = 'UserUploads';

    public function __construct()
    {
        parent::__construct(__('Youtube Basic'), 'youtube');

        $client = Client::factory();
        $this->setClient($client);
    }

    public function init()
    {
        if (is_admin()) {

            $this->mediaSettings = array('uploadTemplate' => 'media-upload-youtube-upgrade');
            $this->hook(new MediaTemplate(new View($this->getViewsPath().'admin/media-upload-youtube.php')));

            //FieldSets need to be initialized early because they hook needed JS and CSS for fields added
            $this->initFieldSet();
        }
    }

    public function initFieldSet()
    {
        $this->fieldSet = new FieldSet();
        $field = array(
            'label' => __("YouTube User ID", 'remote-medias-lite'),
            'type' => 'Text',
            'class' => $this->getSlug(),
            'id' => 'remote_user_id',
            'name' => 'account_meta['.$this->getSlug().'][remote_user_id]',
            'desc' => __("Insert the Youtube User ID for this library", 'remote-medias-lite'),
        );
        $this->fieldSet->addField($field);
        // $field = array(
        //     'label' => __("Feed Type", 'remote-medias-lite'),
        //     'type' => 'select',
        //     'class' => $this->getSlug(),
        //     'id' => 'youtubeFeedType',
        //     'name' => 'account_meta['.$this->getSlug().'][youtubeFeedType]',
        //     'options' => array(
        //         'uploaded' => __("Uploaded by this user", 'remote-medias-lite'),
        //         'favorites' => __("Favorites of this user", 'remote-medias-lite'),
        //     ),
        //     'desc' => __("Insert the Youtube User ID for this library", 'remote-medias-lite'),
        // );
        // $this->fieldSet->addField($field);
    }

    // public function 
    public function setAccount(AbstractRemoteAccount $account)
    {
        $this->account = $account;

        $this->command = 'UserUploads';
        $feedSelected = $this->account->get('youtubeFeedType', 'uploaded');
        if ($feedSelected == 'favorites') {
            $this->command = 'UserFavorites';
        }
    }
    public function validate()
    {
        $params = array(
            'user_id' => $this->account->get('remote_user_id'),
            'request' => 'info'
        );
        
        $command = $this->client->getCommand($this->command, $params);
        $response = $this->client->execute($command);

        return $command->getResponse()->isSuccessful();
    }

    public function getUserInfo()
    {
        $params = array(
            'user_id' => $this->account->get('remote_user_id')
        );
        $command = $this->client->getCommand($this->command, $params);
        $response = $this->client->execute($command);

        return $response;
    }

    public function getUserMedias()
    {
        $params = array(
            'user_id' => $this->account->get('remote_user_id')
        );
        $command = $this->client->getCommand($this->command, $params);
        $response = $this->client->execute($command);

        return $response;
    }

    public function getUserAttachments()
    {
        $response = $this->GetUserMedias();
        $medias = $response->getAll();

        $attachments = array();

        foreach ($medias['entry'] as $i => $media) {
            $remoteMedia = new RemoteMedia($media);
            $remoteMedia->setAccount($this->getAccount());
            $attachments[$i] = $remoteMedia->toMediaManagerAttachment();
        }
        return $attachments;
    }
}
