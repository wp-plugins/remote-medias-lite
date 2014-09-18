<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Accounts\Vimeo;

use WPRemoteMediaExt\RemoteMediaExt\Accounts\AbstractRemoteAccount;

use WPRemoteMediaExt\RemoteMediaExt\Library\MediaTemplate;

use WPRemoteMediaExt\RemoteMediaExt\Library\MediaSettings;
use WPRemoteMediaExt\WPCore\WPscriptAdmin;
use WPRemoteMediaExt\WPCore\hooks\AdminScript;
use WPRemoteMediaExt\WPCore\View;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\MetaBoxService;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\MetaBoxServiceLoader;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\AbstractRemoteService;

class ServiceVimeoSimple extends AbstractRemoteService
{
  public function __construct()
  {
    parent::__construct(__('Vimeo Basic'), 'vimeo');

    $client = VimeoClient::factory();
    $this->setClient($client);
  }

  public function init()
  {
    if(is_admin())
    {
//       $this->mediaSettings = array('uploadTemplate' => 'none');

      //
      $metabox = new MetaBoxService(
          new View($this->getViewsPath().'admin/metaboxes/vimeo-simple-settings.php'),
          'remote_media_vimeo_settings',
          sprintf(__('%1$s - Settings','remote-medias-lite'),$this->getName()),
          $this->accountPostType->getSlug(),
          'normal',
          'default'
      );
      $metabox->setService($this);
      $this->hook(new MetaBoxServiceLoader($metabox));

      $this->addScript(new WPscriptAdmin(array('post.php' => array('post_type'=>$this->accountPostType->getSlug()), 'post-new.php' => array('post_type'=>$this->accountPostType->getSlug())), 'rmedias-query-test',$this->getJsUrl().'media-remote-query-test.js'));

      $this->mediaSettings = array('uploadTemplate' => 'media-upload-vimeo-upgrade');
      $this->hook(new MediaTemplate(new View($this->getViewsPath().'admin/media-upload-vimeo.php')));

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
        'user_id' => $account->get('remote_user_id'),
        'request' => 'info'
    );
    $command = $this->client->getCommand('UserRequest',$params);
    $response = $this->client->execute($command);

    return $response;
  }

  public function getUserMedias(AbstractRemoteAccount $account)
  {
    $params = array(
        'user_id' => $account->get('remote_user_id'),
        'request' => 'videos'
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

    foreach($medias as $i => $media)
    {
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
        'author'      => $media['user_name'],
        'description' => $media['description'],
        'caption'     => "", //limit word count
        'name'        => $media['title'],
        'status'      => 'inherit',
        'uploadedTo'  => 0,
        'date'        => strtotime( $media['upload_date'] ) * 1000,
        'modified'    => strtotime( $media['upload_date'] ) * 1000,
        'menuOrder'   => 0,
        'mime'        => 'remote/vimeo',
        'type'        => "remote",
        'subtype'     => "vimeo",
        'icon'        => $media['thumbnail_medium'],
        'dateFormatted' => mysql2date( get_option('date_format'), $media['upload_date'] ),
        'nonces'      => array(
            'update' => false,
            'delete' => false,
        ),
        'editLink'   => false,
    );

    return $attachment;
  }
}