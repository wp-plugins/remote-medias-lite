=== Remote Media Libraries ===
Contributors: loumray
Tags: youtube, vimeo, vimeo pro, dailymotion, flickr, API, Media Manager, Media Library Extension, Integration, direct upload, private videos, remote medias libraries, media explorer, embeded
Requires at least: 3.5
Tested up to: 4.2-beta1
Stable tag: 1.1.2
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Remote Media Libraries (RML) gives you access to third parties media libraries directly from the Wordpress Media Library.

== Description ==

Remote Media Libraries (RML) gives you access to your favorite content from Youtube, Vimeo, Dailymotion, Flickr and Instagram directly into the media library. The RML plugin makes it possible to navigate, search, and inserts remote media into you posts and pages. This will increased your page load time and also save on server bandwidth. 

You can create as many account as you want and they will show in the left sidebar of the Wordpress media library.  

= Currently Integrated Services =

* Youtube
* Vimeo
* Dailymotion
* Flickr
* Instagram

= Vote for the next integration =

We need your feedback to know which will be the next integration added to RML. Please take 1 minute to fill out this quick survey:

<a href="http://onecodeshop.com/vote-for-the-next-integration/">Next integration survey »</a>

= RML Pro versions =

RML Pro versions let you access premium features:

* Albums/lists filtering
* Unlimited amount of media in the library
* Secure private content
* Upload from Wordpress media library to your remote service
* And more ...

More informations on RML pro versions:

* <a href="http://onecodeshop.com/youtube-pro-remote-media-libraries/">RML Youtube Pro</a>
* <a href="http://onecodeshop.com/flickr-pro-remote-media-libraries/">RML Flickr Pro</a>
* <a href="http://onecodeshop.com/instagram-pro-remote-media-libraries/">RML Instagram Pro</a>


= Upcoming Services =

* Amazon S3 Services
* Amazon Cloud Front
* Vimeo Pro (Upload capability)
* DropBox
* Tumblr
* Photobucket
* Facebook
* Twitter
* Cloud files
* Pixabay
* <a href="http://onecodeshop.com/vote-for-the-next-integration/">Vote for the next integration »</a>

= Follow us! =

* <a href="http://onecodeshop.com">onecodeshop.com</a>
* <a href="https://facebook.com/onecodeshop">facebook.com/onecodeshop</a>
* <a href="https://twitter.com/onecodeshop">twitter.com/onecodeshop</a>

= Buy us a coffee! =

We do work hard to bring you the best of the RML plugin. As normal programmers and designers we live on coffee, support us and buy us a coffee, you will make us happy :) Humm coffee ...

<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=P9N89H4AJ5PCL">Buy us a coffee »</a>

== Changelog ==
= 1.2.0 =
* Initial support of instagram
* Update media library attachment template in media library to match WP behavior for local medias.
* Added missing image size in  display settings
* Fixed a bug that occured in the case that Youtube do not return any author
* Makes plugin main script parsable by PHP 5.2 to fail gracefully and display a notice.
* Extend plugin to support APIs that need authentication

= 1.1.2 =
* Fixed a bug causing bad edit links on re-activated Link Manager admin links page

= 1.1.1 =
* Added Media Manager attachement sidebar for mobile
* Now using WP native image attachements sidebar for remote images

= 1.1.0 =
* Added Flickr Basic Support for photos & videos
* Improved Remote Libraries settings panels
* Fix a bug that occured if you activated on an unsupported version of PHP.

= 1.0.0 =
* Initial Release.

== Upgrade Notice ==
= 1.2.0 =
This version add instagram support and multiple fixes.

= 1.1.1 =
Improved support for remote medias images in Media Manager

= 1.1.0 =
Added Flickr Basic Support & Improved Remote Libraries Admin Section

== Installation ==
= Minimum Requirements =

* WordPress 3.5 or greater
* PHP version 5.3.3 or greater
* PHP cURl module intsalled

= Manual installation =

1. Download the plugin file to your computer and unzip it
2. Using an FTP program, or your hosting control panel, upload the unzipped plugin folder to your WordPress installation’s wp-content/plugins/ directory.
3. Activate the plugin from the Plugins menu within the WordPress admin.

= Configuration =

1. Go to Medias -> Remote Librairies
2. Click Add New to Add an external medias source like Youtube, Vimeo, Dailymotion or Flickr.
3. Insert a Title, Select the remote service you would like to query.
4. Insert your Service reserve ID, make sure it is valid, and publish it.
5. A new section identified by the title you entered will be added in the Media Manager for each published and valid remote library.
6. Go to a post or page, click Add Media and select the library.  The content of the remote library should be available to insert into your post.  

== Screenshots ==
1. Adding remote libraries to media manager
2. Validate your remote libraries settings
3. Insert remote images with your wanted size setting, just like any other local images.
4. Inserting multiple medium size images at once
5. Inserting a large size image
6. Direct Vimeo videos in media manager
7. Embeds appears in editor since WP 4.0 