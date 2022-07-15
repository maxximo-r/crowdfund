<?php

function render_save_edit_pp_smart_checkout_button_interface($bt_opts, $is_edit_mode = false) {
    ?>


    <div class="postbox">
        <h3 class="hndle"><label for="title"><?php echo CrwdfndUtils::_('PayPal Smart Checkout Button Configuration'); ?></label></h3>
        <div class="inside">

            <form id="smart_checkout_button_config_form" method="post">
                <input type="hidden" name="button_type" value="<?php echo $bt_opts['button_type']; ?>">
                <?php if (!$is_edit_mode) { ?>
                    <input type="hidden" name="crwdfnd_button_type_selected" value="1">
                <?php } ?>

                <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">
                    <?php if ($is_edit_mode) { ?>
                        <tr valign="top">
                            <th scope="row"><?php echo CrwdfndUtils::_('Button ID'); ?></th>
                            <td>
                                <input type="text" size="10" name="button_id" value="<?php echo $bt_opts['button_id']; ?>" readonly required />
                                <p class="description">This is the ID of this payment button. It is automatically generated for you and it cannot be changed.</p>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Button Title'); ?></th>
                        <td>
                            <input type="text" size="50" name="button_name" value="<?php echo ($is_edit_mode ? $bt_opts['button_name'] : ''); ?>" required />
                            <p class="description">Give this membership payment button a name. Example: Gold membership payment</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Membership Level'); ?></th>
                        <td>
                            <select id="membership_level_id" name="membership_level_id">
                                <?php echo ($is_edit_mode ? CrwdfndUtils::membership_level_dropdown($bt_opts['membership_level_id']) : CrwdfndUtils::membership_level_dropdown()); ?>
                            </select>
                            <p class="description">Select the membership level this payment button is for.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Payment Amount'); ?></th>
                        <td>
                            <input type="text" size="6" name="payment_amount" value="<?php echo ($is_edit_mode ? $bt_opts['payment_amount'] : ''); ?>" required />
                            <p class="description">Enter payment amount. Example values: 10.00 or 19.50 or 299.95 etc (do not put currency symbol).</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Payment Currency'); ?></th>
                        <td>
                            <select id="payment_currency" name="payment_currency">
                                <option value="USD" <?php echo (isset($bt_opts['payment_currency']) && $bt_opts['payment_currency'] == 'USD') ? 'selected="selected"' : ''; ?>>US Dollars ($)</option>
                                <option value="EUR" <?php echo (isset($bt_opts['payment_currency']) && $bt_opts['payment_currency'] == 'EUR') ? 'selected="selected"' : ''; ?>>Euros (€)</option>
                                <option value="GBP" <?php echo (isset($bt_opts['payment_currency']) && $bt_opts['payment_currency'] == 'GBP') ? 'selected="selected"' : ''; ?>>Pounds Sterling (£)</option>
                            </select>
                            <p class="description">Select the currency for this payment button.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th colspan="2"><div class="crwdfnd-grey-box"><?php echo CrwdfndUtils::_('PayPal Smart Checkout API Credentials (you can get this from your PayPal account)'); ?></div></th>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Live Client ID'); ?></th>
                        <td>
                            <input type="text" size="100" name="pp_smart_checkout_live_id" value="<?php echo ($is_edit_mode ? $bt_opts['pp_smart_checkout_live_id'] : ''); ?>" required/>
                            <p class="description">Enter your live Client ID.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Live Secret'); ?></th>
                        <td>
                            <input type="text" size="100" name="pp_smart_checkout_live_sec" value="<?php echo ($is_edit_mode ? $bt_opts['pp_smart_checkout_live_sec'] : ''); ?>" required/>
                            <p class="description">Enter your live Secret.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Sandbox Client ID'); ?></th>
                        <td>
                            <input type="text" size="100" name="pp_smart_checkout_test_id" value="<?php echo ($is_edit_mode ? $bt_opts['pp_smart_checkout_test_id'] : ''); ?>" required/>
                            <p class="description">Enter your sandbox Client ID.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Sandbox Secret'); ?></th>
                        <td>
                            <input type="text" size="100" name="pp_smart_checkout_test_sec" value="<?php echo ($is_edit_mode ? $bt_opts['pp_smart_checkout_test_sec'] : ''); ?>" required/>
                            <p class="description">Enter your sandbox Secret.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th colspan="2"><div class="crwdfnd-grey-box"><?php echo CrwdfndUtils::_('Button Appearance Settings'); ?></div></th>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php _e("Size", "crowd-fund"); ?></th>
                        <td>
                            <select name="pp_smart_checkout_btn_size">
                                <option value="medium"<?php echo (isset($bt_opts['pp_smart_checkout_btn_size']) && $bt_opts['pp_smart_checkout_btn_size'] === 'medium') ? ' selected' : ''; ?>><?php _e("Medium", "crowd-fund"); ?></option>
                                <option value="large"<?php echo (isset($bt_opts['pp_smart_checkout_btn_size']) && $bt_opts['pp_smart_checkout_btn_size'] === 'large') ? ' selected' : ''; ?>><?php _e("Large", "crowd-fund"); ?></option>
                                <option value="responsive"<?php echo (isset($bt_opts['pp_smart_checkout_btn_size']) && $bt_opts['pp_smart_checkout_btn_size'] === 'responsive') ? ' selected' : ''; ?>><?php _e("Repsonsive", "crowd-fund"); ?></option>
                            </select>
                            <p class="description"><?php _e("Select button size.", "crowd-fund"); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e("Color", "crowd-fund"); ?></th>
                        <td>
                            <select name="pp_smart_checkout_btn_color">
                                <option value="gold"<?php echo (isset($bt_opts['pp_smart_checkout_btn_color']) && $bt_opts['pp_smart_checkout_btn_color'] === 'gold') ? ' selected' : ''; ?>><?php _e("Gold", "crowd-fund"); ?></option>
                                <option value="blue"<?php echo (isset($bt_opts['pp_smart_checkout_btn_color']) && $bt_opts['pp_smart_checkout_btn_color'] === 'blue') ? ' selected' : ''; ?>><?php _e("Blue", "crowd-fund"); ?></option>
                                <option value="silver"<?php echo (isset($bt_opts['pp_smart_checkout_btn_color']) && $bt_opts['pp_smart_checkout_btn_color'] === 'silver') ? ' selected' : ''; ?>><?php _e("Silver", "crowd-fund"); ?></option>
                                <option value="black"<?php echo (isset($bt_opts['pp_smart_checkout_btn_color']) && $bt_opts['pp_smart_checkout_btn_color'] === 'black') ? ' selected' : ''; ?>><?php _e("Black", "crowd-fund"); ?></option>
                            </select>
                            <p class="description"><?php _e("Select button color.", "crowd-fund"); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e("Shape", "crowd-fund"); ?></th>
                        <td>
                            <p><label><input type="radio" name="pp_smart_checkout_btn_shape" value="rect"<?php echo (isset($bt_opts['pp_smart_checkout_btn_shape']) && $bt_opts['pp_smart_checkout_btn_shape'] === 'rect' || empty($bt_opts['pp_smart_checkout_btn_shape'])) ? ' checked' : ''; ?>> <?php _e("Rectangular", "crowd-fund"); ?></label></p>
                            <p><label><input type="radio" name="pp_smart_checkout_btn_shape" value="pill"<?php echo (isset($bt_opts['pp_smart_checkout_btn_shape']) && $bt_opts['pp_smart_checkout_btn_shape'] === 'pill') ? ' checked' : ''; ?>> <?php _e("Pill", "crowd-fund"); ?></label></p>
                            <p class="description"><?php _e("Select button shape.", "crowd-fund"); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e("Layout", "crowd-fund"); ?></th>
                        <td>
                            <p><label><input type="radio" name="pp_smart_checkout_btn_layout" value="vertical"<?php echo (isset($bt_opts['pp_smart_checkout_btn_layout']) && $bt_opts['pp_smart_checkout_btn_layout'] === 'vertical' || empty($bt_opts['pp_smart_checkout_btn_layout'])) ? ' checked' : ''; ?>> <?php _e("Vertical", "crowd-fund"); ?></label></p>
                            <p><label><input type="radio" name="pp_smart_checkout_btn_layout" value="horizontal"<?php echo (isset($bt_opts['pp_smart_checkout_btn_layout']) && $bt_opts['pp_smart_checkout_btn_layout'] === 'horizontal') ? ' checked' : ''; ?>> <?php _e("Horizontal", "crowd-fund"); ?></label></p>
                            <p class="description"><?php _e("Select button layout.", "crowd-fund"); ?></p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th colspan="2"><div class="crwdfnd-grey-box"><?php echo CrwdfndUtils::_('Additional Settings'); ?></div></th>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php _e("Payment Methods", "crowd-fund"); ?></th>
                        <td>
                            <p><label><input type="checkbox" name="pp_smart_checkout_payment_method_credit" value="1"<?php echo (!empty($bt_opts['pp_smart_checkout_payment_method_credit']) ) ? ' checked' : ''; ?>> <?php _e("PayPal Credit", "crowd-fund"); ?></label></p>
                            <p><label><input type="checkbox" name="pp_smart_checkout_payment_method_elv" value="1"<?php echo (!empty($bt_opts['pp_smart_checkout_payment_method_elv']) ) ? ' checked' : ''; ?>> <?php _e("ELV", "crowd-fund"); ?></label></p>
                            <p class="description"><?php _e("Select payment methods that could be used by customers. Note that payment with cards is always enabled.", "crowd-fund"); ?></p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th colspan="2"><div class="crwdfnd-grey-box"><?php echo CrwdfndUtils::_('The following details are optional'); ?></div></th>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Return URL'); ?></th>
                        <td>
                            <input type="text" size="100" name="return_url" value="<?php echo ($is_edit_mode ? $bt_opts['return_url'] : ''); ?>" />
                            <p class="description">This is the URL the user will be redirected to after a successful payment. Enter the URL of your Thank You page here.</p>
                        </td>
                    </tr>

                </table>

                <p class="submit">
                    <?php wp_nonce_field('crwdfnd_admin_add_edit_pp_smart_checkout_btn','crwdfnd_admin_add_edit_pp_smart_checkout_btn') ?>
                    <input type="submit" name="crwdfnd_pp_smart_checkout_<?php echo ($is_edit_mode ? 'edit' : 'save'); ?>_submit" class="button-primary" value="<?php echo CrwdfndUtils::_('Save Payment Data'); ?>" >
                </p>

            </form>

        </div>
    </div>
    <?php
}

add_action('crwdfnd_create_new_button_for_pp_smart_checkout', 'crwdfnd_create_new_pp_smart_checkout_button');

function crwdfnd_create_new_pp_smart_checkout_button() {

    $bt_opts = array(
        'button_type' => sanitize_text_field($_REQUEST['button_type']),
    );

    render_save_edit_pp_smart_checkout_button_interface($bt_opts);
}

add_action('crwdfnd_edit_payment_button_for_pp_smart_checkout', 'crwdfnd_edit_pp_smart_checkout_button');

function crwdfnd_edit_pp_smart_checkout_button() {

    //Retrieve the payment button data and present it for editing.

    $button_id = sanitize_text_field($_REQUEST['button_id']);
    $button_id = absint($button_id);

    $button = get_post($button_id); //Retrieve the CPT for this button
    //$button_image_url = get_post_meta($button_id, 'button_image_url', true);

    $bt_opts = array(
        'button_id' => $button_id,
        'button_type' => sanitize_text_field($_REQUEST['button_type']),
        'button_name' => $button->post_title,
        'membership_level_id' => get_post_meta($button_id, 'membership_level_id', true),
        'payment_amount' => get_post_meta($button_id, 'payment_amount', true),
        'payment_currency' => get_post_meta($button_id, 'payment_currency', true),
        'pp_smart_checkout_live_id' => get_post_meta($button_id, 'pp_smart_checkout_live_id', true),
        'pp_smart_checkout_live_sec' => get_post_meta($button_id, 'pp_smart_checkout_live_sec', true),
        'pp_smart_checkout_test_id' => get_post_meta($button_id, 'pp_smart_checkout_test_id', true),
        'pp_smart_checkout_test_sec' => get_post_meta($button_id, 'pp_smart_checkout_test_sec', true),
        'pp_smart_checkout_btn_size' => get_post_meta($button_id, 'pp_smart_checkout_btn_size', true),
        'pp_smart_checkout_btn_color' => get_post_meta($button_id, 'pp_smart_checkout_btn_color', true),
        'pp_smart_checkout_btn_shape' => get_post_meta($button_id, 'pp_smart_checkout_btn_shape', true),
        'pp_smart_checkout_btn_layout' => get_post_meta($button_id, 'pp_smart_checkout_btn_layout', true),
        'pp_smart_checkout_payment_method_credit' => get_post_meta($button_id, 'pp_smart_checkout_payment_method_credit', true),
        'pp_smart_checkout_payment_method_elv' => get_post_meta($button_id, 'pp_smart_checkout_payment_method_elv', true),
        'return_url' => get_post_meta($button_id, 'return_url', true),
    );

    render_save_edit_pp_smart_checkout_button_interface($bt_opts, true);
}

/*
 * Process submission and save the new or edit PayPal Smart Checkout payment button data
 */

add_action('crwdfnd_create_new_button_process_submission', 'crwdfnd_save_edit_pp_smart_checkout_button_data');
add_action('crwdfnd_edit_payment_button_process_submission', 'crwdfnd_save_edit_pp_smart_checkout_button_data');

//I've merged two (save and edit events) into one

function crwdfnd_save_edit_pp_smart_checkout_button_data() {

    $btn_size = sanitize_text_field($_POST['pp_smart_checkout_btn_size']);
    $btn_color = sanitize_text_field($_POST['pp_smart_checkout_btn_color']);
    $btn_shape = sanitize_text_field($_POST['pp_smart_checkout_btn_shape']);
    $btn_layout = sanitize_text_field($_POST['pp_smart_checkout_btn_layout']);
    $pm_credit = sanitize_text_field($_POST['pp_smart_checkout_payment_method_credit']);
    $pm_elv = sanitize_text_field($_POST['pp_smart_checkout_payment_method_elv']);

    if (isset($_REQUEST['crwdfnd_pp_smart_checkout_save_submit'])) {
        //This is a PayPal Smart Checkout button save event.

        check_admin_referer( 'crwdfnd_admin_add_edit_pp_smart_checkout_btn', 'crwdfnd_admin_add_edit_pp_smart_checkout_btn' );

        $button_id = wp_insert_post(
                array(
                    'post_title' => sanitize_text_field($_REQUEST['button_name']),
                    'post_type' => 'crwdfnd_payment_button',
                    'post_content' => '',
                    'post_status' => 'publish'
                )
        );

        $button_type = sanitize_text_field($_REQUEST['button_type']);
        add_post_meta($button_id, 'button_type', $button_type);
        add_post_meta($button_id, 'membership_level_id', sanitize_text_field($_REQUEST['membership_level_id']));
        add_post_meta($button_id, 'payment_amount', trim(sanitize_text_field($_REQUEST['payment_amount'])));
        add_post_meta($button_id, 'payment_currency', trim(sanitize_text_field($_REQUEST['payment_currency'])));

        add_post_meta($button_id, 'pp_smart_checkout_live_id', trim(sanitize_text_field($_REQUEST['pp_smart_checkout_live_id'])));
        add_post_meta($button_id, 'pp_smart_checkout_live_sec', trim(sanitize_text_field($_REQUEST['pp_smart_checkout_live_sec'])));
        add_post_meta($button_id, 'pp_smart_checkout_test_id', trim(sanitize_text_field($_REQUEST['pp_smart_checkout_test_id'])));
        add_post_meta($button_id, 'pp_smart_checkout_test_sec', trim(sanitize_text_field($_REQUEST['pp_smart_checkout_test_sec'])));

        add_post_meta($button_id, 'pp_smart_checkout_btn_size', $btn_size);
        add_post_meta($button_id, 'pp_smart_checkout_btn_color', $btn_color);
        add_post_meta($button_id, 'pp_smart_checkout_btn_shape', $btn_shape);
        add_post_meta($button_id, 'pp_smart_checkout_btn_layout', $btn_layout);

        add_post_meta($button_id, 'pp_smart_checkout_payment_method_credit', $pm_credit);
        add_post_meta($button_id, 'pp_smart_checkout_payment_method_elv', $pm_elv);

        add_post_meta($button_id, 'return_url', trim(sanitize_text_field($_REQUEST['return_url'])));

        //Redirect to the manage payment buttons interface
        $url = admin_url() . 'admin.php?page=crowdfund_me_payments&tab=payment_buttons';
        CrwdfndMiscUtils::redirect_to_url($url);
    }

    if (isset($_REQUEST['crwdfnd_pp_smart_checkout_edit_submit'])) {
        //This is a PayPal Smart Checkout button edit event.

        check_admin_referer( 'crwdfnd_admin_add_edit_pp_smart_checkout_btn', 'crwdfnd_admin_add_edit_pp_smart_checkout_btn' );

        $button_id = sanitize_text_field($_REQUEST['button_id']);
        $button_id = absint($button_id);
        $button_type = sanitize_text_field($_REQUEST['button_type']);
        $button_name = sanitize_text_field($_REQUEST['button_name']);

        $button_post = array(
            'ID' => $button_id,
            'post_title' => $button_name,
            'post_type' => 'crwdfnd_payment_button',
        );
        wp_update_post($button_post);

        update_post_meta($button_id, 'button_type', $button_type);
        update_post_meta($button_id, 'membership_level_id', sanitize_text_field($_REQUEST['membership_level_id']));
        update_post_meta($button_id, 'payment_amount', trim(sanitize_text_field($_REQUEST['payment_amount'])));
        update_post_meta($button_id, 'payment_currency', trim(sanitize_text_field($_REQUEST['payment_currency'])));

        update_post_meta($button_id, 'pp_smart_checkout_live_id', trim(sanitize_text_field($_REQUEST['pp_smart_checkout_live_id'])));
        update_post_meta($button_id, 'pp_smart_checkout_live_sec', trim(sanitize_text_field($_REQUEST['pp_smart_checkout_live_sec'])));
        update_post_meta($button_id, 'pp_smart_checkout_test_id', trim(sanitize_text_field($_REQUEST['pp_smart_checkout_test_id'])));
        update_post_meta($button_id, 'pp_smart_checkout_test_sec', trim(sanitize_text_field($_REQUEST['pp_smart_checkout_test_sec'])));

        update_post_meta($button_id, 'pp_smart_checkout_btn_size', $btn_size);
        update_post_meta($button_id, 'pp_smart_checkout_btn_color', $btn_color);
        update_post_meta($button_id, 'pp_smart_checkout_btn_shape', $btn_shape);
        update_post_meta($button_id, 'pp_smart_checkout_btn_layout', $btn_layout);

        update_post_meta($button_id, 'pp_smart_checkout_payment_method_credit', $pm_credit);
        update_post_meta($button_id, 'pp_smart_checkout_payment_method_elv', $pm_elv);

        update_post_meta($button_id, 'return_url', trim(sanitize_text_field($_REQUEST['return_url'])));

        echo '<div id="message" class="updated fade"><p>Payment button data successfully updated!</p></div>';
    }
}
