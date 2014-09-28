<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Library;

use WPRemoteMediaExt\WPCore\WPhook;

class MediaLibrarySection implements WPhook
{
    protected $strings = array();

    protected $title;
    protected $content;

    public function __construct($view, $slug, $title)
    {
        $this->title    = new MediaStrings($slug, $title);
        $this->content  = new MediaTemplate($view);
    }

    public function register()
    {
        $this->title->register();
        $this->content->register();
    }

    public function remove()
    {
        $this->title->remove();
        $this->content->remove();
    }
}
