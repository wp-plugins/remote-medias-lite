<?php
use \WPRemoteMediaExt\RemoteMediaExt\Accounts\AbstractRemoteAccount;

?>
<div id="rml-status-box" class="rml-status-box">
<div class="rml-status-minor">
    <div class="rml-status-actions">
        <div class="misc-pub-section">
            <label for="account_status"><?php _e('Account Status:', 'remote-medias-lite'); ?></label>
            <?php
            $status        = $account->getStatus();
            $statusDisplay = $account->getStatusDisplay();
            if ($status === AbstractRemoteAccount::STATUS_AUTHNEEDED) {
                $statusDisplay = '<a class="action_query_test" href="#">'.$statusDisplay.'</a>';
            }
            
            ?>
            <span id="rmlaccountstatus" class="rml-status-field"><?php echo $statusDisplay; ?></span>
        </div>
    </div>
</div>
<div class="rml-actions">
    <div class="rml-validation-actions">
        <span id="query_msg"></span>
        <div id="rmlloading" class="spinner ocs-inline-loading"></div>
        <?php
        $buttonText = __('Validate', 'remote-medias-lite');
        // if ($account->isAuthNeeded() && $account->isEnabled()) {
        //     $buttonText = __('Re-authenticate', 'remote-medias-lite');
        // }
        ?>
        <a id="rmlstatusbutton" href="#" class="button button-large action_query_test"><?php echo $buttonText; ?></a>
    </div>
    <div class="clear"></div>
</div>
</div>