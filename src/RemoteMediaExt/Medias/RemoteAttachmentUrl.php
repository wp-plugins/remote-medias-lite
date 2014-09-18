<?php
namespace easyvideoupload\pweb\wpvideouploads\RemoteMedia;

use easyvideoupload\pweb\wp_core\WPfilter;

class RemoteAttachmentUrl extends WPfilter
{

  public function __construct()
  {
    parent::__construct('wp_get_attachment_url',10,2);

  }

  public function action()
  {
    $url = func_get_arg(0);
    $id  = func_get_arg(1);

    $mime_type = get_post_mime_type($id);
    if(substr($mime_type,0,6) === "remote")
    {
      //TODO get from REMOTE MEDIA
      $video_id = get_post_meta( $id, '_wp_attached_file', true);
      return 'http://vimeo.com/'.$video_id;
    }


    return $url;
  }

}