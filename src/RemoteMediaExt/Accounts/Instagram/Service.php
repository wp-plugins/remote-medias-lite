<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Accounts\Instagram;

use WPRemoteMediaExt\RemoteMediaExt\Accounts\AbstractRemoteAccount;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\AbstractRemoteService;
use WPRemoteMediaExt\RemoteMediaExt\Library\MediaTemplate;
use WPRemoteMediaExt\WPCore\View;
use WPRemoteMediaExt\WPForms\FieldSet;

class Service extends AbstractRemoteService
{
    public function __construct()
    {
        parent::__construct(__('Instagram', 'remote-medias-lite'), 'instagram');

        $client = Client::factory();
        $this->setClient($client);
    }

    public function init()
    {
        if (is_admin()) {

            $this->mediaSettings = array('uploadTemplate' => 'media-upload-instagram-upgrade');
            $this->hook(new MediaTemplate(new View($this->getViewsPath().'admin/media-upload-instagram.php')));

            //FieldSets need to be initialized early because they hook needed JS and CSS for fields added
            $this->initFieldSet();
        }
    }

    public function initFieldSet()
    {
        $this->fieldSet = new FieldSet();
        $field = array(
            'label' => __("Instagram Username", 'remote-medias-lite'),
            'type' => 'Text',
            'class' => $this->getSlug(),
            'id' => 'remote_user_id',
            'name' => 'account_meta['.$this->getSlug().'][instagram_remote_user_id]',
            'desc' => __("Insert the Instagram user for this library", 'remote-medias-lite'),
        );
        $this->fieldSet->addField($field);
    }

    public function validate()
    {
        $params = array(
            'username' => $this->account->get('instagram_remote_user_id'),
        );
        try {
            $command = $this->client->getCommand('UserRequest', $params);
            $response = $this->client->execute($command);
        } catch (\Exception $e) {
            return false;
        }

        if (!empty($response) && isset($response->items)) {
            return true;
        }

        return false;
    }

    public function getUserInfo()
    {

        return false;
    }

    public function getUserMedias()
    {
        $params = array(
            'username' => $this->account->get('instagram_remote_user_id'),
        );

        $command = $this->client->getCommand('UserRequest', $params);
        $response = $this->client->execute($command);

        if (!empty($response) &&
            isset($response->items)
        ) {
            return $response->items;
        }

        return array();
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

        session_start();

        // Instagram query return always 20 max items per page
        $medias = $this->GetUserMedias();

        $attachments = array();

        foreach ($medias as $media) {
            $remoteMedia = new RemoteMedia($media);
            $remoteMedia->setAccount($this->getAccount());
            $attachments[] = $remoteMedia->toMediaManagerAttachment();
        }

        return $attachments;
    }
}
