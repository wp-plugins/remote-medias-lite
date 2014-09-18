<?php
/*
 * This file is part of WPCore project.
 *
 * (c) Louis-Michel Raynauld <louismichel@pweb.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WPRemoteMediaExt\WPCore;

/**
 * WP shortcode
 *
 * @author Louis-Michel Raynauld <louismichel@pweb.ca>
 */
class WPshortcode extends WPaction
{
    protected $slug;
    protected $view;

    public function __construct($shortcode, View $view = null)
    {
        $this->slug = $shortcode;
        $this->view = $view;

        parent::__construct('init');
    }

    public function action()
    {
        add_shortcode($this->slug, array($this, 'callback'));
    }

    public function callback()
    {
        if (is_null($this->view)) {
            return '';
        }
        
        return $this->view->getContent();
    }
}
