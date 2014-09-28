<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Library;

use WPRemoteMediaExt\WPCore\WPfilter;
use WPRemoteMediaExt\RemoteMediaExt\Accounts\RemoteAccountFactory;

class MediaSettings extends WPfilter
{

    protected $slug;
    protected $setting;

    public function __construct($slug)
    {
        parent::__construct('media_view_settings', 10, 2);

        $this->slug = $slug;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function getSetting()
    {
        return $this->setting;
    }

    public function action()
    {
        $settings = func_get_arg(0);
        // $post    = func_get_arg(1);

        $args = array(
            'post_type' => 'rmlaccounts',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'post_title',
            'order'   => 'ASC',
        );
        $accounts = get_posts($args);
        $this->setting = array();

        foreach ($accounts as $account) {
            $remoteAccount = RemoteAccountFactory::create($account->ID);

            if ($remoteAccount->isValid()) {
                $this->setting[] = array(
                    'id' => $account->ID,
                    'type' => $remoteAccount->get('type'),
                    'title' => 'Insert '.$account->post_title,
                );
            }
        }
        $settings[$this->slug] = $this->setting;

        return $settings;
    }
}
