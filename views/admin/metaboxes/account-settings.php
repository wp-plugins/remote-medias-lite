<?php

//View Variables
//$hidden_nonce: this is a hidden input with a preloaded nonce value
//$post
//$metabox
?>
<div>
<?php
echo $hidden_nonce;
$fieldSet = $fRemoteMediaExt->getBasicFieldSet($account);
$fieldSet->render();
?>
</div>