<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Accounts\Dailymotion;

use WPRemoteMediaExt\RemoteMediaExt\Accounts\AbstractRemoteMedia;

/*
* Dailymotion Media Class
*/
class RemoteMedia extends AbstractRemoteMedia
{

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
            $attachment = array(
                'id'          => $this->metadata['id'],
                'title'       => $this->metadata['title'],
                'filename'    => $this->metadata['title'],
                'url'         => $this->metadata['url'],
                'link'        => $this->metadata['url'],
                'alt'         => '',
                'author'      => $this->metadata['owner'],
                'description' => $this->metadata['description'],
                'caption'     => "", //limit word count
                'name'        => $this->metadata['title'],
                'status'      => 'inherit',
                'uploadedTo'  => 0,
                'date'        => $this->metadata['created_time'],
                'modified'    => $this->metadata['modified_time'],
                'menuOrder'   => 0,
                'mime'        => 'remote/dailymotion',
                'subtype'     => "dailymotion",
                'icon'        => $this->metadata['thumbnail_120_url'],
                'dateFormatted' => mysql2date(get_option('date_format'), $this->metadata['modified_time']),
                'nonces'      => array(
                    'update' => false,
                    'delete' => false,
                ),
                'editLink'   => false,
            )
        );
        
        $attachment['remotedata'] = $this->metadata;

        return $attachment;
    }
}
