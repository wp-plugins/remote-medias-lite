<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Accounts;

use WPRemoteMediaExt\WPCore\admin\WPadminNotice;
use WPRemoteMediaExt\WPCore\admin\WPmetabox;
use WPRemoteMediaExt\WPCore\admin\WPSaveMetabox;

class MetaBoxSaveAccount extends WPSaveMetabox
{
    public function action()
    {
        $post_id = func_get_arg(0);
        $post    = func_get_arg(1);

        $verify = parent::action($post_id, $post);
        if (!$verify) {
            return;
        }

        $accountID   = absint($_POST['post_ID']);
        $accountType = sanitize_text_field($_POST['account_meta']['remote_account_type']);

        $account = RemoteAccountFactory::create($accountID);

        //Update attributes
        foreach ($_POST['account_meta'] as $key => $value) {
            //if type specific data
            if (is_array($value)) {
                foreach ($value as $typedata => $typevalue) {
                    $account->set($key.'_'.$typedata, sanitize_text_field($typevalue));
                }
            } else {
                $account->set($key, sanitize_text_field($value));
            }
        }

        $account->set('remote_user_id', sanitize_text_field($_POST['account_meta'][$accountType]['remote_user_id']));
        $account->set('service_class', sanitize_text_field($_POST['account_meta'][$accountType]['service_class']));

        $account->save();

        $isValid = $account->get('isValid');
        if (!$isValid) {
            $_REQUEST['rmlmsg'] = 1;
        } elseif (isset($_REQUEST['rmlmsg'])) {
            unset($_REQUEST['rmlmsg']);
        }
    }
}
