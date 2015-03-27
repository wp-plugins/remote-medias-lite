<?php
//View Variables
//$hidden_nonce: this is a hidden input with a preloaded nonce value
//$post
//$metabox
//$account
echo $hidden_nonce;

$rml = \WPRemoteMediaExt\RemoteMediaExt\FRemoteMediaExt::getInstance();
$services = $rml->getRemoteServices();

foreach ($services as $service) {
    ?>
    <div class="rmlfields rmlfields-<?php echo $service->getSlug();?>">
        <input type="hidden" id="service_class" name="account_meta[<?php echo $service->getSlug();?>][service_class]" value="<?php echo get_class($service);?>" >
        <?php

        $service->setAccount($account);
        $fieldSet = $service->getFieldSet();
        
        $fieldSet->render();
        ?>
    </div>
    <?php
}
