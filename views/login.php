<?php
$auth = CrwdfndAuth::get_instance();
$setting = CrwdfndSettings::get_instance();
$password_reset_url = $setting->get_value('reset-page-url');
$join_url = $setting->get_value('join-us-page-url');

$label_username_or_email = __( 'Username or Email', 'crowd-fund' );
$crwdfnd_username_label = apply_filters('crwdfnd_login_form_set_username_label', $label_username_or_email);
?>
<div class="crwdfnd-login-widget-form">
    <form id="crwdfnd-login-form" name="crwdfnd-login-form" method="post" action="">
        <div class="crwdfnd-login-form-inner">
            <div class="crwdfnd-username-label">
                <label for="crwdfnd_user_name" class="crwdfnd-label"><?php echo CrwdfndUtils::_($crwdfnd_username_label) ?></label>
            </div>
            <div class="crwdfnd-username-input">
                <input type="text" class="crwdfnd-text-field crwdfnd-username-field" id="crwdfnd_user_name" value="" size="25" name="crwdfnd_user_name" />
            </div>
            <div class="crwdfnd-password-label">
                <label for="crwdfnd_password" class="crwdfnd-label"><?php echo CrwdfndUtils::_('Password') ?></label>
            </div>
            <div class="crwdfnd-password-input">
                <input type="password" class="crwdfnd-text-field crwdfnd-password-field" id="crwdfnd_password" value="" size="25" name="crwdfnd_password" />
            </div>
            <div class="crwdfnd-remember-me">
                <span class="crwdfnd-remember-checkbox"><input type="checkbox" name="rememberme" value="checked='checked'"></span>
                <span class="crwdfnd-rember-label"> <?php echo CrwdfndUtils::_('Remember Me') ?></span>
            </div>

            <div class="crwdfnd-before-login-submit-section"><?php echo apply_filters('crwdfnd_before_login_form_submit_button', ''); ?></div>

            <div class="crwdfnd-login-submit">
                <input type="submit" class="crwdfnd-login-form-submit" name="crwdfnd-login" value="<?php echo CrwdfndUtils::_('Login') ?>"/>
            </div>
            <div class="crwdfnd-forgot-pass-link">
                <a id="forgot_pass" class="crwdfnd-login-form-pw-reset-link"  href="<?php echo $password_reset_url; ?>"><?php echo CrwdfndUtils::_('Forgot Password?') ?></a>
            </div>
            <div class="crwdfnd-join-us-link">
                <a id="register" class="crwdfnd-login-form-register-link" href="<?php echo $join_url; ?>"><?php echo CrwdfndUtils::_('Join Us') ?></a>
            </div>
            <div class="crwdfnd-login-action-msg">
                <span class="crwdfnd-login-widget-action-msg"><?php echo apply_filters( 'crwdfnd_login_form_action_msg', $auth->get_message() ); ?></span>
            </div>
        </div>
    </form>
</div>
