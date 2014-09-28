<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Accounts;

use WPRemoteMediaExt\WPCore\admin\WPmetabox;

use WPRemoteMediaExt\WPCore\View;
use WPRemoteMediaExt\WPCore\admin\WPmetaboxLoader;

class MetaBoxServiceLoader extends WPmetaboxLoader
{
    protected $metabox;

    public function __construct(WPmetabox $metabox)
    {
        parent::__construct($metabox, new MetaBoxSaveAccount());
    }
}
