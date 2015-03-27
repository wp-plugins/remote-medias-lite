<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Accounts\Instagram;

use WPRemoteMediaExt\RemoteMediaExt\Accounts\AbstractRemoteMedia;

/*
* Instagram Media Class
*/
class RemoteMedia extends AbstractRemoteMedia
{

    public function __construct($metadata = array())
    {
        $this->metadata = $metadata;

        $this->type = 'image';
    }

    /**
     * Prepares a media object for JS, where it is expected
     * to be JSON-encoded and fit into an Attachment model.
     *
     * @return array Array of attachment details.
     */
    public function toMediaManagerAttachment()
    {
        $attachment = array_merge(
            $this->getBasicAttachment(),
            array(
                'id'          => $this->metadata->id,
                'title'       => $this->metadata->code,
                'filename'    => $this->metadata->code,
                'url'         => $this->metadata->link,
                'link'        => $this->metadata->link,
                'alt'         => '',
                'author'      => $this->metadata->user->full_name,
                'description' => isset($this->metadata->caption->text) ? $this->metadata->caption->text : '',
                'caption'     => "", //limit word count
                'name'        => $this->metadata->code,
                'status'      => 'inherit',
                'uploadedTo'  => 0,
                'date'        => strtotime($this->metadata->created_time) * 1000,
                'modified'    => strtotime($this->metadata->created_time) * 1000,
                'menuOrder'   => 0,
                'mime'        => 'remote/instagram',
                'subtype'     => $this->metadata->type,
                'icon'        => $this->metadata->images->thumbnail->url,
                'dateFormatted' => mysql2date(get_option('date_format'), $this->metadata->created_time),
                'nonces'      => array(
                    'update' => false,
                    'delete' => false,
                ),
                'editLink'   => false,
            )
        );
        
        if ($this->metadata->type === 'image') {
            $attachment['url'] = $this->metadata->images->standard_resolution->url;
            
            $attachment['width'] = intval($this->metadata->images->standard_resolution->width);
            $attachment['height'] = intval($this->metadata->images->standard_resolution->height);

            $attachment['sizes'] = $this->getImageSizes(
                $attachment['width'],
                $attachment['height'],
                $attachment['url']
            );
        }

        $attachment['remotedata'] = $this->metadata;

        return $attachment;
    }
}
