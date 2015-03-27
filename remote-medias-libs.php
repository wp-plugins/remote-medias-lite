<?php
/*
Plugin Name: Remote Medias Libraries
Plugin URI: http://onecodeshop.com/
Description: Integrates 3rd party medias to WP media manager
Version: 1.2.0
Author: Team OneCodeShop.com
Author URI: http://onecodeshop.com/
*/

function ocsRmlPhpNotice()
{
    echo '<div class="message error"><p>'.sprintf(__('%s <strong>Requirements failed.</strong> PHP version must <strong>at least %s</strong>. You are using version '. PHP_VERSION, 'remote-medias-lite'), 'Remote Media Libraries', '5.3.3').'</p></div>';
}

/*
 * Unfortunately, while grand daddy PHP 5.2 is still hanging around this check avoid PHP error upon PHP 5.2 activation. 
 */
if (version_compare(PHP_VERSION, '5.3.3', '>=')) {
    require 'bootstrap.php';
} else {
    add_action('admin_notices', 'ocsRmlPhpNotice');
}
