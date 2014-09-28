<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Library;

use WPRemoteMediaExt\WPCore\View;
use WPRemoteMediaExt\WPCore\WPaction;

class MediaTemplate extends WPaction
{
    protected $view;

    public function __construct(View $view)
    {
        $this->view = $view;

        parent::__construct('print_media_templates');
    }

    public function action()
    {
        $this->view->show();
    }
}
