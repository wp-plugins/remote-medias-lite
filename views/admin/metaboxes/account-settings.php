<?php

use WPRemoteMediaExt\RemoteMediaExt\FRemoteMediaExt;

use WPRemoteMediaExt\RemoteMediaExt\Accounts\RemoteAccountFactory;

//View Variables
//$hidden_nonce: this is a hidden input with a preloaded nonce value
//$post
//$metabox

$account = RemoteAccountFactory::create($post->ID);
$account->fetch();
?>
<div>
<?php
echo $hidden_nonce;
$rm = FRemoteMediaExt::getInstance();
?>

<label for="remote_media_type"><?php _e("Remote Service", 'remote-medias-lite' );?></label>
<select id="remote_media_type" name="account_meta[remote_account_type]">
<?php
foreach($rm->getRemoteServices() as $service)
{
  ?>
  <option value="<?php echo $service->getSlug();?>" <?php echo ( $account->get('type') == $service->getSlug() ? "selected=\"selected\"":"")?>><?php echo $service->getName();?></option>
  <?php
}
?>
</select>
<br />
</div>