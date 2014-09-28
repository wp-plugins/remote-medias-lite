<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Library;

use WPRemoteMediaExt\WPCore\WPfilter;

class MediaArraySettings extends WPfilter
{
    protected $slug;
    protected $setting = array();

    public function __construct($slug)
    {
        parent::__construct('media_view_settings', 10, 2);

        $this->slug = $slug;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function addSetting($index, $value)
    {
        $this->setting[$index] = $value;
    }

    public function removeSetting($index)
    {
        unset($this->setting[$index]);
    }
    public function getSetting()
    {
        return $this->setting;
    }

    public function action()
    {
        $settings = func_get_arg(0);
        // $post    = func_get_arg(1);

        $settings[$this->slug] = $this->setting;

        return $settings;
    }
}
