<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Ajax;

use WPRemoteMediaExt\Guzzle\Http\Exception\ClientErrorResponseException;
use WPRemoteMediaExt\Guzzle\Http\Exception\CurlException;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\Vimeo\VimeoClient;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\RemoteAccountFactory;
use WPRemoteMediaExt\WPCore\WPajaxCall;

class AjaxQueryAttachments extends WPajaxCall
{

  public function __construct()
  {
    parent::__construct('query-remote-attachments', 'media-remote-ext', true, true);
    $this->jsvar = 'rmlQueryAttachmentsParams';
  }

  public function callback($data)
  {
    $accountID = 0;
    if(isset($_REQUEST['query']['account_id']))
    {
      $accountID = esc_attr($_REQUEST['query']['account_id']);
    }


    $account = RemoteAccountFactory::create($accountID);
    $service = $account->getService();

    $return = array();
    $response = array();
    $return['data'] = array();
    try {
      $return['data'] = $service->getUserAttachments($account);

    } catch(ClientErrorResponseException $e) {
      $return['success'] = true;
      $return['statuscode'] = $e->getResponse()->getStatusCode();
      $return['msg']        = $e->getResponse()->getError();
      wp_send_json($return);
    } catch(CurlException $e) {
      $return['success'] = false;
      $return['statuscode'] = $e->getErrorNo();
      $return['msg']        = $e->getError();
      wp_send_json($return);
    } catch(\Exception $e) {
      $return['success'] = false;
      $return['statuscode'] = $e->getResponse()->getStatusCode();
      $return['msg']        = $e->getResponse()->getReasonPhrase();
      wp_send_json($return);
    }

    wp_send_json_success( $return['data'] );
  }
}