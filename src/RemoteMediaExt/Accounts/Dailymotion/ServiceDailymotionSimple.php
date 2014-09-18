<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Accounts\Dailymotion;

use WPRemoteMediaExt\RemoteMediaExt\Accounts\AbstractRemoteAccount;

use WPRemoteMediaExt\RemoteMediaExt\Library\MediaTemplate;

use WPRemoteMediaExt\RemoteMediaExt\Library\MediaSettings;

use WPRemoteMediaExt\WPCore\WPscriptAdmin;

use WPRemoteMediaExt\WPCore\hooks\AdminScript;

use WPRemoteMediaExt\WPCore\View;

use WPRemoteMediaExt\RemoteMediaExt\Accounts\MetaBoxService;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\MetaBoxServiceLoader;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\AbstractRemoteService;

class ServiceDailymotionSimple extends AbstractRemoteService
{
    public function __construct()
    {
        parent::__construct(__('Dailymotion Basic'), 'dailymotion');

        $client = DailymotionClient::factory();
        $this->setClient($client);
    }

    public function init()
    {
        if (is_admin()) {
            $metabox = new MetaBoxService(
                new View($this->getViewsPath().'admin/metaboxes/dailymotion-simple-settings.php'),
                'remote_media_dailymotion_settings',
                sprintf(__('%1$s - Settings','remote-medias-lite'),$this->getName()),
                $this->accountPostType->getSlug(),
                'normal',
                'default'
            );
            // $metabox->setService($this);
            $this->hook(new MetaBoxServiceLoader($metabox));

            $this->addScript(new WPscriptAdmin(array('post.php' => array('post_type'=>$this->accountPostType->getSlug()), 'post-new.php' => array('post_type'=>$this->accountPostType->getSlug())), 'rmedias-query-test',$this->getJsUrl().'media-remote-query-test.js'));

            $this->mediaSettings = array('uploadTemplate' => 'media-upload-dailymotion-upgrade');
            $this->hook(new MediaTemplate(new View($this->getViewsPath().'admin/media-upload-dailymotion.php')));
        }
    }

    public function validate(AbstractRemoteAccount $account)
    {

        $params = array(
            'user_id' => $account->get('remote_user_id'),
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

    public function getUserInfo(AbstractRemoteAccount $account)
    {

        $params = array(
            'user_id' => $account->get('remote_user_id'),
            'fields'  => 'id,title,description,created_time,modified_time,owner,thumbnail_120_url,thumbnail_url,url'
        );
        $command = $this->client->getCommand('UserRequest',$params);
        $response = $this->client->execute($command);

        return $response;
    }

    public function getUserMedias(AbstractRemoteAccount $account)
    {
        $params = array(
            'user_id' => $account->get('remote_user_id'),
            'fields'  => 'id,title,description,created_time,modified_time,owner,thumbnail_120_url,thumbnail_url,url'
        );
        $command = $this->client->getCommand('UserVideosRequest',$params);
        $response = $this->client->execute($command);

        return $response;
    }

    public function getUserAttachments(AbstractRemoteAccount $account)
    {
        $response = $this->GetUserMedias($account);
        $medias = $response->getAll();

        $attachments = array();

        foreach ($medias['list'] as $i => $media) {
          $attachments[$i] = $this->createAttachment($media);
        }
        return $attachments;
    }

    public function createAttachment($media)
    {

        $attachment = array(
            'id'          => $media['id'],
            'title'       => $media['title'],
            'filename'    => $media['title'],
            'url'         => $media['url'],
            'link'        => $media['url'],
            'alt'         => '',
            'author'      => $media['owner'],
            'description' => $media['description'],
            'caption'     => "", //limit word count
            'name'        => $media['title'],
            'status'      => 'inherit',
            'uploadedTo'  => 0,
            'date'        => $media['created_time'],
            'modified'    => $media['modified_time'],
            'menuOrder'   => 0,
            'mime'        => 'remote/dailymotion',
            'type'        => "remote",
            'subtype'     => "dailymotion",
            'icon'        => $media['thumbnail_120_url'],
            'dateFormatted' => mysql2date( get_option('date_format'), $media['modified_time'] ),
            'nonces'      => array(
                'update' => false,
                'delete' => false,
            ),
            'editLink'   => false,
        );

        return $attachment;
    }
}