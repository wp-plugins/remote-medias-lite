<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Accounts;

abstract class AbstractAuthService extends AbstractRemoteService
{
    public function __construct($name, $slug)
    {
        parent::__construct($name, $slug);

        add_action('template_redirect', array($this, 'authCallback'));
    }

    abstract public function authCallback();
    abstract public function getAuthUrl();
}
