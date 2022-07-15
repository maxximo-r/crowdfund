<?php
CrowdFundMe::enqueue_validation_scripts(array('ajaxEmailCall' => array('extraData' => '&action=crwdfnd_validate_email&member_id=' . filter_input(INPUT_GET, 'member_id', FILTER_SANITIZE_NUMBER_INT))));
$settings = CrwdfndSettings::get_instance();
$force_strong_pass = $settings->get_value('force-strong-passwords');
if (!empty($force_strong_pass)) {
    $pass_class = "validate[required,custom[strongPass],minSize[8]]";
} else {
    $pass_class = "";
}
$user_name = apply_filters('crwdfnd_registration_form_set_username', $user_name);
?>
<div class="crwdfnd-registration-widget-form">
    <form id="crwdfnd-registration-form" class="crwdfnd-validate-form" name="crwdfnd-registration-form" method="post" action="">
        <input type ="hidden" name="level_identifier" value="<?php echo $level_identifier ?>" />
        <table>
            <tr class="crwdfnd-registration-username-row" <?php apply_filters('crwdfnd_registration_form_username_tr_attributes', ''); ?>>
                <td><label for="user_name"><?php echo CrwdfndUtils::_('Username') ?></label></td>
                <td><input type="text" id="user_name" class="validate[required,custom[noapostrophe],custom[CRWDFNDUserName],minSize[4],ajax[ajaxUserCall]]" value="<?php echo esc_attr($user_name); ?>" size="50" name="user_name" <?php apply_filters('crwdfnd_registration_form_username_input_attributes', ''); ?>/></td>
            </tr>
            <tr class="crwdfnd-registration-email-row">
                <td><label for="email"><?php echo CrwdfndUtils::_('Email') ?></label></td>
                <td><input type="text" autocomplete="off" id="email" class="validate[required,custom[email],ajax[ajaxEmailCall]]" value="<?php echo esc_attr($email); ?>" size="50" name="email" /></td>
            </tr>
            <tr class="crwdfnd-registration-password-row">
                <td><label for="password"><?php echo CrwdfndUtils::_('Password') ?></label></td>
                <td><input type="password" autocomplete="off" id="password" class="<?php echo $pass_class; ?>" value="" size="50" name="password" /></td>
            </tr>
            <tr class="crwdfnd-registration-password-retype-row">
                <td><label for="password_re"><?php echo CrwdfndUtils::_('Repeat Password') ?></label></td>
                <td><input type="password" autocomplete="off" id="password_re" value="" size="50" name="password_re" /></td>
            </tr>
            <tr class="crwdfnd-registration-firstname-row" <?php apply_filters('crwdfnd_registration_form_firstname_tr_attributes', ''); ?>>
                <td><label for="first_name"><?php echo CrwdfndUtils::_('First Name') ?></label></td>
                <td><input type="text" id="first_name" value="<?php echo esc_attr($first_name); ?>" size="50" name="first_name" /></td>
            </tr>
            <tr class="crwdfnd-registration-lastname-row" <?php apply_filters('crwdfnd_registration_form_lastname_tr_attributes', ''); ?>>
                <td><label for="last_name"><?php echo CrwdfndUtils::_('Last Name') ?></label></td>
                <td><input type="text" id="last_name" value="<?php echo esc_attr($last_name); ?>" size="50" name="last_name" /></td>
            </tr>
            <tr class="crwdfnd-registration-membership-level-row" <?php apply_filters('crwdfnd_registration_form_membership_level_tr_attributes', ''); ?>>
                <td><label for="membership_level"><?php echo CrwdfndUtils::_('Membership Level') ?></label></td>
                <td>
                    <?php
                    echo $membership_level_alias;

                    echo '<input type="hidden" value="' . $membership_level . '" size="50" name="membership_level" id="membership_level" />';

                    $crwdfnd_p_key = get_option('crwdfnd_private_key_one');
                    if (empty($crwdfnd_p_key)) {
                        $crwdfnd_p_key = uniqid('', true);
                        update_option('crwdfnd_private_key_one', $crwdfnd_p_key);
                    }
                    $crwdfnd_level_hash = md5($crwdfnd_p_key . '|' . $membership_level);
                    echo '<input type="hidden" name="crwdfnd_level_hash" value="' . $crwdfnd_level_hash . '" />';
                    ?>
                </td>
            </tr>
            <?php
            apply_filters('crwdfnd_registration_form_before_terms_and_conditions', '');

            $terms_enabled = $settings->get_value('enable-terms-and-conditions');
            if (!empty($terms_enabled)) {
                $terms_page_url = $settings->get_value('terms-and-conditions-page-url');
                ?>
                <tr>
                    <td colspan="2" style="text-align: center;">
                        <label><input type="checkbox" id="accept_terms" name="accept_terms" class="validate[required]" value="1"> <?php echo CrwdfndUtils::_('I accept the ') ?> <a href="<?php echo $terms_page_url; ?>" target="_blank"><?php echo CrwdfndUtils::_('Terms and Conditions') ?></a></label>
                    </td>
                </tr>
                <?php
            }

            $pp_enabled = $settings->get_value('enable-privacy-policy');
            if (!empty($pp_enabled)) {
                $pp_page_url = $settings->get_value('privacy-policy-page-url');
                ?>
                <tr>
                    <td colspan="2" style="text-align: center;">
                        <label><input type="checkbox" id="accept_pp" name="accept_pp" class="validate[required]" value="1"> <?php echo CrwdfndUtils::_('I agree to the ') ?> <a href="<?php echo $pp_page_url; ?>" target="_blank"><?php echo CrwdfndUtils::_('Privacy Policy') ?></a></label>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>

        <div class="crwdfnd-before-registration-submit-section" align="center"><?php echo apply_filters('crwdfnd_before_registration_submit_button', ''); ?></div>

        <div class="crwdfnd-registration-submit-section" align="center">
            <input type="submit" value="<?php echo CrwdfndUtils::_('Register') ?>" class="crwdfnd-registration-submit" name="crwdfnd_registration_submit" />
        </div>

        <input type="hidden" name="action" value="custom_posts" />

    </form>
</div>
