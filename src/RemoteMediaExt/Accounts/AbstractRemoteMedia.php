<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Accounts;

abstract class AbstractRemoteMedia
{
    protected $account;
    protected $type = 'embed'; //raw video, raw image, embed
    protected $metadata;

    public function __construct($metadata = array())
    {
        $this->metadata = $metadata;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getAccount()
    {
        return $this->account;
    }

    public function setAccount(AbstractRemoteAccount $account)
    {
        $this->account = $account;

        return $this;
    }

    public function toEditorHtml($jsattachment)
    {
        $html = '';
        if ($this->type == 'image') {

            $align = !empty($jsattachment['align']) ? $jsattachment['align'] : 'none';
            $size  = !empty($jsattachment['image-size']) ? $jsattachment['image-size'] : 'medium';
            $alt   = !empty($jsattachment['image_alt']) ? $jsattachment['image_alt'] : $jsattachment['title'];

            $hwstring = '';
            //If width and heigth are set
            if (!empty($jsattachment['width']) && !empty($jsattachment['height'])) {
                list($constrainedWidth, $constrainedHeight) = image_constrain_size_for_editor(intval($jsattachment['width']), intval($jsattachment['height']), $size, 'edit');
                $hwstring = image_hwstring($constrainedWidth, $constrainedHeight);
            } else {
                $size = 'full';
            }

            $title = $jsattachment['title'] ? 'title="'.esc_attr($jsattachment['title']).'" ' : '';
            $class = 'align' . esc_attr($align) .' size-' . esc_attr($size) . ' wp-remote-'.$jsattachment['remotetype'].' wp-service-'.$jsattachment['subtype'].'-'.$jsattachment['id'];

            if (!empty($jsattachment['url'])) {
                $html.= '<a href="'.esc_attr($jsattachment['url']).'"  class="inpost">';
            }
            
            $html.= '<img src="'.esc_attr($jsattachment['imgurl']).'" ';
            $html.= 'alt="'.esc_attr($alt).'" ';
            $html.=  $title;
            $html.=  $hwstring;
            $html.= 'class="' . $class . '" />';
            if (!empty($jsattachment['url'])) {
                $html.= '</a>';
            }

            return $html;
        }

        if (!empty($this->metadata['url'])) {
            $html =  '[embed]'.$this->metadata['url'].'[/embed]';
        }

        return $html;
    }

    /*
    * Inspired from media.php -> wp_prepare_attachment_for_js
    */
    public function getImageSizes($width, $height, $url)
    {
        $sizes = array();

        /** This filter is documented in wp-admin/includes/media.php */
        $possible_sizes = apply_filters(
            'image_size_names_choose',
            array(
                'thumbnail' => __('Thumbnail'),
                'medium'    => __('Medium'),
                'large'     => __('Large'),
                'full'      => __('Full Size'),
            )
        );
        unset($possible_sizes['full']);

        foreach ($possible_sizes as $size => $label) {
            // We have the actual image size, but might need to further constrain it if content_width is narrower.
            // Thumbnail, medium, and full sizes are also checked against the site's height/width options.
            list($constrainedWidth, $constrainedHeight) = image_constrain_size_for_editor($width, $height, $size, 'edit');

            $sizes[$size] = array(
                'height'      => $constrainedHeight,
                'width'       => $constrainedWidth,
                'url'         => $url,
                'orientation' => $height > $width ? 'portrait' : 'landscape',
            );
        }
        $sizes['full'] = array(
            'height'      => $height,
            'width'       => $width,
            'url'         => $url,
            'orientation' => $height > $width ? 'portrait' : 'landscape',
        );

        return $sizes;
    }

    public function getBasicAttachment()
    {
        $attachment = array(
            'type'        => "remote",
            'remotetype'  => $this->type,
            'remotedata'  => array()
        );
        $mediaAccount = $this->getAccount();
        if (!is_null($mediaAccount)) {
            $attachment['accountId'] = (int) $mediaAccount->getId();
        }
        return $attachment;
    }

    /**
     * Prepares a media object for JS, where it is expected
     * to be JSON-encoded and fit into an Attachment model.
     *
     * @return array Array of attachment details.
     */
    abstract public function toMediaManagerAttachment();
}
