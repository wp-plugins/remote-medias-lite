<div class="rml-status-box">
<div class="rml-status-minor">
    <div class="rml-status-actions">
        <div class="misc-pub-section">
            <label for="account_status"><?php _e('Account Status:','remote-medias-lite'); ?></label>
            
            <?php
            $workingStyle = "";
            $unknownStyle = "";
            $invalidStyle = "";
            $status = $account->get('isValid');
            //working
            if ($status === true) {
                $workingStyle = '';
                $unknownStyle = 'style="display: none"';
                $invalidStyle = 'style="display: none"';
            //unknown
            } elseif (is_null($status)) {
                $workingStyle = 'style="display: none"';
                $unknownStyle = '';
                $invalidStyle = 'style="display: none"';
            //invalid
            } else {
                $workingStyle = 'style="display: none"';
                $unknownStyle = 'style="display: none"';
                $invalidStyle = '';
            }
            ?>
            <span class="rml-status-field working" <?php echo $workingStyle; ?>><?php _e('Working', 'remote-medias-lite'); ?></span>
            <span class="rml-status-field unknown" <?php echo $unknownStyle; ?>><?php _e('Unknown', 'remote-medias-lite'); ?></span>
            <span class="rml-status-field notvalid" <?php echo $invalidStyle; ?>><?php _e('Invalid', 'remote-medias-lite'); ?></span>
        </div>
    </div>
</div>
<div class="rml-actions">
    <div class="rml-validation-actions">
        <span id="query_msg"></span>
        <div id="rmlloading" class="spinner ocs-inline-loading"></div>
        <a href="#" class="button button-large action_query_test"><?php _e('Validate', 'remote-medias-lite'); ?></a>
    </div>
    <div class="clear"></div>
</div>
</div>