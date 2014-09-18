<?php
  use WPRemoteMediaExt\RemoteMediaExt\FRemoteMediaExt;
  $feature = FRemoteMediaExt::getInstance();
?>
<script type="text/html" id="tmpl-media-upload-dailymotion-upgrade">
  <div class="uploader-inline rm-upload-wrap">

		<div class="uploader-inline-content no-upload-message">

			<div class="upload-ui">
				<h3 class="upload-instructions drop-instructions"><?php _e('Upload videos directly to your DailyMotion account');?></h3>
        <br>
				<a href="http://www.onecodeshop.com/remote-media-pro-dailymotion" target="_blank" class="browser button button-hero" id="vimeo-pro-link" style="position: relative; z-index: 0; display: inline;"><?php _e('Get the extension and start uploading &raquo;');?></a>
			</div>

			<p class="no-mg-top"><strong><?php _e('Add our Remote Media PRO extension for DailyMotion to unlock premium features.','remote-medias-lite'); ?></strong></p>
			<p class="ocs-logo"><img width="165px" src="<?php echo $feature->getAssetsUrl().'img';?>/logo-dev.png" alt="One Code Shop" /></p>
		</div>
	</div>

</script>