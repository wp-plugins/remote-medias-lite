<?php

namespace WPRemoteMediaExt\RemoteMediaExt\Accounts;

use WPRemoteMediaExt\WPCore\admin\WPmetabox;

use WPRemoteMediaExt\WPCore\View;

/**
 * WP metabox
 *
 * @author Louis-Michel Raynauld <louismichel@pweb.ca>
 */

class MetaBoxService extends WPmetabox
{

    protected $service;

    public function __construct(
        View $view,
        $mbId,
        $title,
        $post_type,
        $context = 'advanced',
        $priority = 'default',
        $callback_args = null
    ) {

        parent::__construct(
            $mbId,
            $title,
            $post_type,
            $context,
            $priority,
            null,
            $callback_args
        );

        $this->setView($view);

    }

    public function setService(AbstractRemoteService $service)
    {
        $this->service = $service;
    }

    public function action()
    {
        // $postType = func_get_arg(0);
        // $post     = func_get_arg(1);
        // $account = RemoteAccountFactory::create($post->ID);

        parent::action();
    }


    public function view($post, $metabox)
    {
        $data = $this->view->getData();
        $data['post'] = $post;
        $data['metabox'] = $metabox;
        $data['account'] = RemoteAccountFactory::create($post->ID);
        $data['account']->fetch();

        $data['hidden_nonce'] = wp_nonce_field($this->getNonceAction(), $this->getNonceName(), true, false);

        $this->view->setData($data);
        $this->view->show();
    }
}
