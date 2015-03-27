<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Ajax;

use WPRemoteMediaExt\WPCore\WPajaxCall;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\RemoteMediaFactory;

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
        $jsattachment = wp_unslash($_POST['attachment']);
        $html = "";

        if (empty($jsattachment['accountId']) ||
            empty($jsattachment['remotedata'])
        ) {
            wp_send_json_error();
        }

        $accountId = absint($jsattachment['accountId']);
        
        $media = RemoteMediaFactory::createFromAccountid($accountId, $jsattachment['remotedata']);

        if (is_null($media)) {
            if (empty($jsattachment['url'])) {
                wp_send_json_error();
            }

            $html = '[embed]'.$jsattachment['url'].'[/embed]';
            wp_send_json_success($html);
        }

        unset($jsattachment['remotedata']);
        $html = $media->toEditorHtml($jsattachment);

        wp_send_json_success($html);
    }
}
