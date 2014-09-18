<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Medias;

use easyvideoupload\pweb\wpvideouploads\AbstractRemoteAccount;

/*
 * properties:
*
* post id
* attachment type
* account client
* api fields
*  - title
*  -
*
* method:
* is_remote
* get
*
* delete
*
* fetch
* sync
*/

class RemoteMedia
{
  protected $accountID;
  protected $remoteAccount;

  protected $postID;
  protected $remoteID;

  protected $filename;

  public function __construct($postID)
  {
    $this->postID = $postID;

    if($this->isRemote())
    {
      $this->fetch();
    }
  }

  public function setAccount(AbstractRemoteAccount $account)
  {
    $this->remoteAccount = $account;
  }

  public function isRemote()
  {
    $mime_type = get_post_mime_type($this->postID);
    if(substr($mime_type,0,6) === "remote")
    {
      return true;
    }

    return false;
  }

  public function fetch()
  {
    $metadata = wp_get_attachment_metadata($this->postID);
    print_r($metadata);
    var_dump($metadata);

    $this->filename = get_post_meta($this->postID,'_wp_attached_file',true);
    $metadata = get_post_meta($this->postID);
    var_dump($metadata);
  }

  public function remoteDelete()
  {
    $params = array('video_id' => $this->remoteID);
    $command = $this->client->getCommand('DeleteVideo',$params);
    $return = $this->client->execute($command);
    return $this->client->getVimeoObject($return);
  }

}