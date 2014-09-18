<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Accounts\Youtube;

use WPRemoteMediaExt\RemoteMediaExt\Accounts\AbstractRemoteAccount;

use WPRemoteMediaExt\RemoteMediaExt\Library\MediaTemplate;

use WPRemoteMediaExt\RemoteMediaExt\Library\MediaSettings;

use WPRemoteMediaExt\WPCore\WPscriptAdmin;

use WPRemoteMediaExt\WPCore\hooks\AdminScript;

use WPRemoteMediaExt\WPCore\View;

use WPRemoteMediaExt\RemoteMediaExt\Accounts\MetaBoxService;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\MetaBoxServiceLoader;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\AbstractRemoteService;

class ServiceYoutubeSimple extends AbstractRemoteService
{
  public function __construct()
  {
    parent::__construct(__('Youtube Basic'), 'youtube');

    $client = YoutubeClient::factory();
    $this->setClient($client);
  }

  public function init()
  {
    if(is_admin())
    {
      $metabox = new MetaBoxService(
          new View($this->getViewsPath().'admin/metaboxes/youtube-simple-settings.php'),
          'remote_media_youtube_settings',
          sprintf(__('%1$s - Settings','remote-medias-lite'),$this->getName()),
          $this->accountPostType->getSlug(),
          'normal',
          'default'
      );
      $metabox->setService($this);
      $this->hook(new MetaBoxServiceLoader($metabox));

      $this->addScript(new WPscriptAdmin(array('post.php' => array('post_type'=>$this->accountPostType->getSlug()), 'post-new.php' => array('post_type'=>$this->accountPostType->getSlug())), 'rmedias-query-test',$this->getJsUrl().'media-remote-query-test.js'));

      $this->mediaSettings = array('uploadTemplate' => 'media-upload-youtube-upgrade');
      $this->hook(new MediaTemplate(new View($this->getViewsPath().'admin/media-upload-youtube.php')));
    }
  }

  public function validate(AbstractRemoteAccount $account)
  {

    $params = array(
        'user_id' => $account->get('remote_user_id'),
        'request' => 'info'
    );
    $command = $this->client->getCommand('UserRequest',$params);
    $response = $this->client->execute($command);

    return $command->getResponse()->isSuccessful();
  }

  public function getUserInfo(AbstractRemoteAccount $account)
  {

    $params = array(
        'user_id' => $account->get('remote_user_id')
    );
    $command = $this->client->getCommand('UserRequest',$params);
    $response = $this->client->execute($command);

    return $response;
  }

  public function getUserMedias(AbstractRemoteAccount $account)
  {
    $params = array(
        'user_id' => $account->get('remote_user_id')
    );
    $command = $this->client->getCommand('UserRequest',$params);
    $response = $this->client->execute($command);

    return $response;
  }

  public function getUserAttachments(AbstractRemoteAccount $account)
  {
    $response = $this->GetUserMedias($account);
    $medias = $response->getAll();

    $attachments = array();

    foreach($medias['entry'] as $i => $media)
    {
      $attachments[$i] = $this->createAttachment($media);
    }
    return $attachments;
  }

  public function createAttachment($media)
  {
    $youtubeid = basename($media['id']);
    $attachment = array(
        'id'          => $youtubeid,
        'title'       => $media['title'],
        'filename'    => $media['title'],
        'url'         => "http://www.youtube.com/watch?v=$youtubeid",
        'link'        => "http://www.youtube.com/watch?v=$youtubeid",
        'alt'         => '',
        'author'      => $media['author']['name'],
        'description' => $media['content'],
        'caption'     => "", //limit word count
        'name'        => $media['title'],
        'status'      => 'inherit',
        'uploadedTo'  => 0,
        'date'        => strtotime( $media['updated'] ) * 1000,
        'modified'    => strtotime( $media['updated'] ) * 1000,
        'menuOrder'   => 0,
        'mime'        => 'remote/youtube',
        'type'        => "remote",
        'subtype'     => "youtube",
        'icon'        => "http://img.youtube.com/vi/$youtubeid/1.jpg",
        'dateFormatted' => mysql2date( get_option('date_format'), $media['updated'] ),
        'nonces'      => array(
            'update' => false,
            'delete' => false,
        ),
        'editLink'   => false,
    );

    return $attachment;
  }
}