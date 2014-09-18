<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Accounts;

use WPRemoteMediaExt\WPCore\WPfeature;

abstract class AbstractRemoteService extends WPfeature
{
  protected $slug;
  protected $name;

  protected $accountPostType;

  protected $client;
  protected $account;

  protected $mediaSettings = array();

  public function __construct($name, $slug)
  {
    parent::__construct($name, $slug);
  }

  public function setAccountPostType($postType)
  {
    $this->accountPostType = $postType;
  }

  public function setClient(AbstractRemoteClient $client)
  {
    $this->client = $client;
  }

  public function getClient()
  {
    return $this->client;
  }

  public function setAccount(AbstractRemoteAccount $account)
  {
    $this->account = $account;
  }

  public function getAccount()
  {
    return $this->account;
  }

  public function getSettings()
  {
    return $this->mediaSettings;
  }

  abstract public function validate(AbstractRemoteAccount $account);
  abstract public function getUserInfo(AbstractRemoteAccount $account);
  abstract public function getUserMedias(AbstractRemoteAccount $account);
  abstract public function getUserAttachments(AbstractRemoteAccount $account);
  abstract public function createAttachment($media);

}