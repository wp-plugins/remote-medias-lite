<?php
namespace easyvideoupload\pweb\wpvideouploads\RemoteMedia;

use easyvideoupload\pweb\wpvideouploads\RemoteMedia;
use easyvideoupload\pweb\wp_core\WPaction;

class RemoteDelete extends WPaction
{

  public function __construct()
  {
    parent::__construct('delete_attachment',10,1);

  }

  public function action()
  {
    $postID = func_get_arg(0);
    $remoteMedia = new RemoteMedia($postID);
    if($remoteMedia->isRemote())
    {
      $return = $remoteMedia->remoteDelete();
    }

    return $post_id;
  }

}