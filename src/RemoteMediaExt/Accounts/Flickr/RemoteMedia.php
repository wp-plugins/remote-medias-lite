<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Accounts\Flickr;

use WPRemoteMediaExt\RemoteMediaExt\Accounts\AbstractRemoteMedia;

/*
* Flickr Media Class
*
* Based on these data examples:
*
* Video data example
*
Array
(
    [title] => Title of media
    [url] => https://www.flickr.com/photos/AUTHOR_ALIAS/XXXXXXX/
    [description] => 
    [description_raw] => 
    [m_url] => https://farm6.staticflickr.com/XXXXXXX/XXXXXXX_473087a1ca_m.jpg
    [t_url] => https://farm6.staticflickr.com/XXXXXXX/XXXXXXX_473087a1ca_s.jpg
    [l_url] => https://farm6.staticflickr.com/XXXXXXX/XXXXXXX_473087a1ca.jpg
    [photo_xml] => 
    [date] => 1410914067
    [date_taken] => 2014-09-16T17:34:29-08:00
    [date_taken_nice] => September 16th, 2014
    [guid] => /photo/XXXXXXX
    [author_name] => AUTHOR_ALIAS
    [author_url] => https://www.flickr.com/people/AUTHOR_ALIAS/
    [author_nsid] => XXXXXXX@XXX
    [author_icon] => https://farm4.staticflickr.com/3707/buddyicons/XXXXXXX@XXX.jpg?XXXXXXX#XXXXXXX@XXX
    [photo_url] => https://farm6.staticflickr.com/XXXXXXX/XXXXXXX_473087a1ca_b.jpg
    [thumb_url] => https://farm6.staticflickr.com/XXXXXXX/XXXXXXX_473087a1ca_s.jpg
    [height] => 576
    [width] => 1024
    [l_width] => 500
    [tags] => morning summer storm night dark early timelapse video illinois
    [tagsa] => Array()
    [photo_mime] => image/jpeg
    [is_video] => 1
    [player_url] => https://www.flickr.com/photos/AUTHOR_ALIAS/XXXXXXX/
    [video_w] => 640
    [video_h] => 360
    [video_duration] => 36
    [video_swf_url] => https://www.flickr.com/apps/video/stewart.swf?v=XXXXXXX&photo_id=XXXXXXX
    [tags_list] => Array()
)
*
*
* Photo data example
*
*
Array
(
    [title] => Title of media
    [url] => https://www.flickr.com/photos/AUTHOR_ALIAS/XXXXXXX/
    [description] =>
    [description_raw] => 
    [m_url] => https://farm4.staticflickr.com/XXXXXXX/XXXXXXX_f5b02038aa_m.jpg
    [t_url] => https://farm4.staticflickr.com/XXXXXXX/XXXXXXX_f5b02038aa_s.jpg
    [l_url] => https://farm4.staticflickr.com/XXXXXXX/XXXXXXX_f5b02038aa.jpg
    [photo_xml] => 
    [date] => 1410385976
    [date_taken] => 2014-09-09T05:06:05-08:00
    [date_taken_nice] => September 9th, 2014
    [guid] => /photo/XXXXXXX
    [author_name] => AUTHOR_ALIAS
    [author_url] => https://www.flickr.com/people/AUTHOR_ALIAS/
    [author_nsid] => XXXXXXX@XXX
    [author_icon] => https://farm4.staticflickr.com/XXXX/buddyicons/XXXXXXX@XXX.jpg?XXXXXXX#XXXXXXX@XXX
    [photo_url] => https://farm4.staticflickr.com/XXXXXXX/XXXXXXX_f5b02038aa_b.jpg
    [thumb_url] => https://farm4.staticflickr.com/XXXXXXX/XXXXXXX_f5b02038aa_s.jpg
    [height] => 678
    [width] => 1024
    [l_width] => 500
    [tags] => tag1 blue autumn fall night clouds dark early illinois
    [tagsa] => Array()
    [photo_mime] => image/jpeg
    [tags_list] => Array()
)
*/
class RemoteMedia extends AbstractRemoteMedia
{
    public function __construct($metadata = array())
    {
        $this->metadata = $metadata;

        $this->type = 'image';
        if (isset($this->metadata['is_video']) &&
            absint($this->metadata['is_video']) > 0
        ) {
            $this->type = 'embed';
        }
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
                'id'          => basename($this->metadata['guid']),
                'title'       => $this->metadata['title'],
                'filename'    => $this->metadata['title'],
                'url'         => $this->metadata['url'] ,
                'link'        => $this->metadata['url'],
                'alt'         => $this->metadata['title'],
                'author'      => $this->metadata['author_name'],
                'description' => trim($this->metadata['description']),
                'caption'     => '',
                'name'        => $this->metadata['title'],
                'status'      => 'inherit',
                'uploadedTo'  => 0,
                'date'        => strtotime($this->metadata['date_taken']) * 1000,
                'modified'    => strtotime($this->metadata['date_taken']) * 1000,
                'menuOrder'   => 0,
                'mime'        => 'remote/flickr',
                'subtype'     => "flickr",
                'icon'        => $this->metadata['m_url'],
                'dateFormatted' => mysql2date(get_option('date_format'), $this->metadata['date_taken']),
                'nonces'      => array(
                    'update' => false,
                    'delete' => false,
                ),
                'editLink'   => false,
            )
        );

        if ($this->type === 'image') {
            $attachment['url'] = $this->metadata['photo_url'];
            
            $attachment['width'] = intval($this->metadata['width']);
            $attachment['height'] = intval($this->metadata['height']);

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
