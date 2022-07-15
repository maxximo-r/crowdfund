<div class="crwdfnd-pw-reset-widget-form">
    <form id="crwdfnd-pw-reset-form" name="crwdfnd-reset-form" method="post" action="">
        <div class="crwdfnd-pw-reset-widget-inside">
            <div class="crwdfnd-pw-reset-email crwdfnd-margin-top-10">
                <label for="crwdfnd_reset_email" class="crwdfnd_label crwdfnd-pw-reset-email-label"><?php echo CrwdfndUtils::_('Email Address') ?></label>
            </div>
            <div class="crwdfnd-pw-reset-email-input crwdfnd-margin-top-10">
                <input type="text" name="crwdfnd_reset_email" class="crwdfnd-text-field crwdfnd-pw-reset-text" id="crwdfnd_reset_email"  value="" size="60" />
            </div>
            <div class="crwdfnd-before-login-submit-section crwdfnd-margin-top-10"><?php echo apply_filters('crwdfnd_before_pass_reset_form_submit_button', ''); ?></div>
            <div class="crwdfnd-pw-reset-submit-button crwdfnd-margin-top-10">
                <input type="submit" name="crwdfnd-reset" class="crwdfnd-pw-reset-submit" value="<?php echo CrwdfndUtils::_('Reset Password'); ?>" />
            </div>
        </div>
    </form>
</div>
