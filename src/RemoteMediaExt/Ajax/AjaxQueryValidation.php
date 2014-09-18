<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Ajax;

use WPRemoteMediaExt\Guzzle\Http\Exception\ClientErrorResponseException;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\Vimeo\VimeoClient;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\RemoteAccountFactory;
use WPRemoteMediaExt\WPCore\WPajaxCall;

class AjaxQueryValidation extends WPajaxCall
{

    public function __construct()
    {
        parent::__construct('rmlQueryTest', 'rmedias-query-test', true, true);
        $this->jsvar = 'rmlQueryTestParams';
    }

    public function callback($data)
    {
        $return = array('error' => false);

        $return['validate'] = false;

        $account = RemoteAccountFactory::createFromService(stripslashes(esc_attr($_POST['service_class'])));

        if (!is_null($account)) {
            $account->set('remote_user_id', esc_attr($_POST['user_id']));
            $return['validate'] = $account->validate();
            $return['last_valid_query'] = $account->get('last_valid_query');
        } else {
            $return['error'] = true;
            $return['msq'] = 'Service type class '.stripslashes(esc_attr($_POST['service_class'])).' unknown';
        }
        

        echo htmlspecialchars(json_encode($return), ENT_NOQUOTES);
        die(); // this is required to return a proper result
    }
}
