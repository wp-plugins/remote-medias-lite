<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Ajax;

use WPRemoteMediaExt\RemoteMediaExt\Accounts\RemoteAccountFactory;

use WPRemoteMediaExt\WPCore\WPajaxCall;

class AjaxQueryValidation extends WPajaxCall
{

    public function __construct()
    {
        parent::__construct('rmlQueryTest', 'rmedias-query-test', true, true);
        $this->jsvar = 'rmlQueryTestParams';
    }

    public function getScriptParams()
    {
        $params = parent::getScriptParams();

        $params['status'] = array();
        $params['status']['unknown'] = __('Unknown', 'remote-medias-lite');
        $params['status']['invalid'] = __('Invalid', 'remote-medias-lite');
        $params['status']['authProcessing'] = __('Authenticating', 'remote-medias-lite');
        $params['status']['enabled'] = __('Enabled', 'remote-medias-lite');
        $params['status']['authNeeded'] = __('Authenticate Now', 'remote-medias-lite');
        $params['status']['authfailed'] = __('Failed Auth', 'remote-medias-lite');
        $params['button'] = array();
        $params['button']['reauth'] = __('Re-authenticate', 'remote-medias-lite');

        return $params;
    }

    public function end($data)
    {
        echo json_encode($data);
        die();
    }

    public function callback($data)
    {
        $return = array(
            'error' => false,
            'validate' => false,
            'authneeded' => false,
            'authurl' => ''
        );

        $data    = $_POST['account'];
        $account = RemoteAccountFactory::createFromService(stripslashes(esc_attr($data['service_class'])));

        if (is_null($account)) {
            $return['error'] = true;
            $return['msg'] = 'Service type class '.stripslashes(esc_attr($data['service_class'])).' unknown';
            $this->end($return);
        }

        unset($data['service_class']);

        if (empty($_POST['post_id'])) {
            $return['error'] = true;
            $return['msg']   = 'post_id parameter missing';
            $this->end($return);
        }

        $account->setId(absint($_POST['post_id']));

        //Apply all attributes to account
        foreach ($data as $name => $value) {
            $account->set(esc_attr($name), esc_attr($value));
        }

        $return['authneeded'] = $account->isAuthNeeded();
        //If no auth needed validate credentials
        if ($return['authneeded'] === false) {

            $return['validate'] = $account->validate();
            $this->end($return);
        }

        try {
            //If auth needed, get Auth URL
            $return['authurl'] = $account->getAuthUrl();
        } catch (ClientErrorResponseException $e) {
            $return['error'] = true;
            $return['msg']   = 'invalid remote library parmeters';
        } catch (InvalidAuthParamException $e) {
            $return['error'] = true;
            $return['msg']   = 'invalid remote library parmeters';
        } catch (\Exception $e) {
            error_log('RML could not get Auth Url:'.$e->getMessage());
            $return['error'] = true;
            $return['msg']   = 'post_id parameter missing';
        }
        
        $this->end($return);
    }
}
