<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Medias;

use WPRemoteMediaExt\WPCore\WPfilter;

class RemoteInsertToEditor extends WPfilter
{

  public function __construct()
  {
    parent::__construct('media_send_to_editor',10,3);
  }

  public function action()
  {
    $html = func_get_arg(0);
    $id   = func_get_arg(1);
    $attachment = func_get_arg(2);

    var_dump($attachment);
    var_dump($_POST);
    var_dump($_REQUEST);
    $mime_type = get_post_mime_type($id);
    if(substr($mime_type,0,6) === "remote")
    {
      //TODO get from REMOTE MEDIA
//       $html = '[embed width="123" height="456"]'.$attachment->url.'[/embed]';
//       $html = '[embed]'.$attachment['url'].'[/embed]';
      //TODO Note: using embed shortcode makes wp javascript to override it when visual editor is on
      // see media-editor.js line 425

      // for now using the url directly
      $html = $attachment['url'];
    }

    return $html;
  }

}