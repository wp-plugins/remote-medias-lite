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

    protected $fieldSet;
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

    public function getFieldSet()
    {
        foreach ($this->fieldSet as $field) {
            $field->attr('value', esc_attr($this->account->get($this->getSlug().'_'.$field->attr('id'))));
        }
        return $this->fieldSet;
    }
    
    abstract public function validate();
    abstract public function getUserInfo();
    abstract public function getUserMedias();
    abstract public function getUserAttachments();
}
