<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Library;

use WPRemoteMediaExt\WPCore\WPfilter;

class MediaStrings extends WPfilter
{

    protected $slug;
    protected $name;

    public function __construct($slug, $name)
    {
        parent::__construct('media_view_strings', 10, 2);

        $this->slug = $slug;
        $this->name = $name;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function getName()
    {
        return $this->name;
    }

    public function action()
    {
        $strings = func_get_arg(0);
        // $post    = func_get_arg(1);

        $strings[$this->slug] = $this->name;

        return $strings;
    }
}
