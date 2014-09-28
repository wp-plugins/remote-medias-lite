<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Accounts\Vimeo;

use WPRemoteMediaExt\RemoteMediaExt\Accounts\AbstractRemoteMedia;

/*
* Vimeo Media Class
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
            array(
                'id'          => $this->metadata['id'],
                'title'       => $this->metadata['title'],
                'filename'    => $this->metadata['title'],
                'url'         => $this->metadata['url'],
                'link'        => $this->metadata['url'],
                'alt'         => '',
                'author'      => $this->metadata['user_name'],
                'description' => $this->metadata['description'],
                'caption'     => "", //limit word count
                'name'        => $this->metadata['title'],
                'status'      => 'inherit',
                'uploadedTo'  => 0,
                'date'        => strtotime($this->metadata['upload_date']) * 1000,
                'modified'    => strtotime($this->metadata['upload_date']) * 1000,
                'menuOrder'   => 0,
                'mime'        => 'remote/vimeo',
                'subtype'     => "vimeo",
                'icon'        => $this->metadata['thumbnail_medium'],
                'dateFormatted' => mysql2date(get_option('date_format'), $this->metadata['upload_date']),
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
