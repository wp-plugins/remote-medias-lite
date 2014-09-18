<?php

//View Variables
//$hidden_nonce: this is a hidden input with a preloaded nonce value
//$post
//$metabox
//account
$accountTypeSlug = 'vimeo';
?>
<div class="rmlfields rmlfields-<?php echo $accountTypeSlug;?>">
<?php
echo $hidden_nonce;
?>
<input type="hidden" id="service_class" name="account_meta[<?php echo $accountTypeSlug;?>][service_class]" value="\WPRemoteMediaExt\RemoteMediaExt\Accounts\Vimeo\ServiceVimeoSimple" >

<label for="remote_user_id"><?php _e("Vimeo User ID", 'remote-medias-lite' );?></label>
<input type="text" id="remote_user_id" name="account_meta[<?php echo $accountTypeSlug;?>][remote_user_id]" value="<?php echo $account->get($accountTypeSlug.'_remote_user_id');?>" >
<a href="#" class="button action_query_test"><?php _e('Validate','remote-medias-lite'); ?></a>
<div id="rmlloading" class="spinner ocs-inline-loading"></div>
<span id="query_msg"></span>
</div>