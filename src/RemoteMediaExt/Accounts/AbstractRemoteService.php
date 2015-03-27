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

        return $this;
    }

    public function setClient(AbstractRemoteClient $client)
    {
        $this->client = $client;

        return $this;
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
            $value = $this->account->get($this->getSlug().'_'.$field->attr('id'));
            $default = $field->attr('default');
            if (is_null($value) && !is_null($default)) {
                $value = $default;
            }
            $field->attr('value', esc_attr($value));
        }
        return $this->fieldSet;
    }
    
    abstract public function validate();
    abstract public function getUserInfo();
    abstract public function getUserMedias();
    abstract public function getUserAttachments();
}
