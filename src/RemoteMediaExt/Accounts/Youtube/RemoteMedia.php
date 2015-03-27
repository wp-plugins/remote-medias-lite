<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Accounts\Youtube;

use WPRemoteMediaExt\RemoteMediaExt\Accounts\AbstractRemoteMedia;

/*
* Youtube Media Class
*/
class RemoteMedia extends AbstractRemoteMedia
{

    public function __construct($metadata = array())
    {
        $this->metadata = $metadata;

        $this->metadata['youtubeid'] = basename($this->metadata['id']);
        $this->metadata['url'] = "https://www.youtube.com/watch?v=".$this->metadata['youtubeid'];
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
                'id'          => $this->metadata['youtubeid'],
                'title'       => $this->metadata['title'],
                'filename'    => $this->metadata['title'],
                'url'         => $this->metadata['url'],
                'link'        => $this->metadata['url'],
                'alt'         => '',
                'author'      => isset($this->metadata['author']) && isset($this->metadata['author']['name']) ? $this->metadata['author']['name'] : '',
                'description' => $this->metadata['content'],
                'caption'     => "", //limit word count
                'name'        => $this->metadata['title'],
                'status'      => 'inherit',
                'uploadedTo'  => 0,
                'date'        => strtotime($this->metadata['updated']) * 1000,
                'modified'    => strtotime($this->metadata['updated']) * 1000,
                'menuOrder'   => 0,
                'mime'        => 'remote/youtube',
                'subtype'     => "youtube",
                'icon'        => "http://img.youtube.com/vi/".$this->metadata['youtubeid']."/1.jpg",
                'dateFormatted' => mysql2date(get_option('date_format'), $this->metadata['updated']),
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
