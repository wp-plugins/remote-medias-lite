<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Ajax;

use WPRemoteMediaExt\Guzzle\Http\Exception\ClientErrorResponseException;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\Vimeo\VimeoClient;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\RemoteAccountFactory;
use WPRemoteMediaExt\WPCore\WPajaxCall;

class AjaxUserInfo extends WPajaxCall
{

  public function __construct()
  {
    parent::__construct('remotemediasUserInfo', 'rmedias-user-info', true, true);
    // $this->jsvar = 'fields_params';
    $this->jsvar = 'rmlUserInfoParams';
  }

  // public function init()
  // {

  //   $theme_params = array(
  //       'ajax_url' 						 => admin_url( 'admin-ajax.php' ),
  //   );

  //   wp_localize_script( $this->jsHandle, 'fields_params', $theme_params );
  // }

  public function callback($data)
  {

    $return = array('error' => false);

    $account = RemoteAccountFactory::create(esc_attr($_POST['post_id']));
    $service = $account->getService();

    $response = array();
    try
    {
      $response = $service->GetUserInfo(esc_attr($_POST['user_id']));
      $return['data'] = $response->getAll();
    }
    catch(ClientErrorResponseException $e)
    {
      $return['error'] = true;
      $return['statuscode'] = $e->getResponse()->getStatusCode();
      $return['msg']        = $e->getResponse()->getReasonPhrase();
    }
    catch(\Exception $e)
    {
      $return['error'] = true;
      $return['statuscode'] = $e->getCode();
      $return['msg'] = $e->getMessage();
    }

    echo htmlspecialchars(json_encode($return), ENT_NOQUOTES);
    die(); // this is required to return a proper result
  }
}