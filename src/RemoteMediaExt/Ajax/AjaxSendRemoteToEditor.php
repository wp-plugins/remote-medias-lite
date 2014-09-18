<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Ajax;

use WPRemoteMediaExt\WPCore\WPajaxCall;

class AjaxSendRemoteToEditor extends WPajaxCall
{

    public function __construct()
    {
        parent::__construct('send-remote-attachment-to-editor', 'media-remote-ext', true, true);
        $this->jsvar = 'rmlSendToEditorParams';
        $this->nonceQueryVar = 'nonce';
    }

    public function callback($data)
    {
        $html = $type = $subtype = "";

        $id = 0;
        // print_r($_REQUEST);
        //type should be 'remote'
        if (isset($_REQUEST['attachment']['id'])) {
            $id = $_REQUEST['attachment']['id'];
        }
        //type should be 'remote'
        if (isset($_REQUEST['attachment']['type'])) {
          $type = $_REQUEST['attachment']['type'];
        }
        if (isset($_REQUEST['attachment']['subtype'])) {
          $subtype = $_REQUEST['attachment']['subtype'];
        }
        if (isset($_REQUEST['attachment']['url'])) {
          $html = ' [embed]'.$_REQUEST['attachment']['url'].'[/embed]';
        }

        wp_send_json_success( $html );
    }
}