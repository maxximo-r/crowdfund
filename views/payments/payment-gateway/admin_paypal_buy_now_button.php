<?php

add_action('crwdfnd_create_new_button_for_pp_buy_now', 'crwdfnd_create_new_pp_buy_now_button');

function crwdfnd_create_new_pp_buy_now_button() {
    ?>
    <div class="postbox">
        <h3 class="hndle"><label for="title"><?php echo CrwdfndUtils::_('PayPal Buy Now Button Configuration'); ?></label></h3>
        <div class="inside">

            <form id="pp_button_config_form" method="post">
                <input type="hidden" name="button_type" value="<?php echo sanitize_text_field($_REQUEST['button_type']); ?>">
                <input type="hidden" name="crwdfnd_button_type_selected" value="1">

                <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Button Title'); ?></th>
                        <td>
                            <input type="text" size="50" name="button_name" value="" required />
                            <p class="description">Give this membership payment button a name. Example: Gold membership payment</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Membership Level'); ?></th>
                        <td>
                            <select id="membership_level_id" name="membership_level_id">
                                <?php echo CrwdfndUtils::membership_level_dropdown(); ?>
                            </select>
                            <p class="description">Select the membership level this payment button is for.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Payment Amount'); ?></th>
                        <td>
                            <input type="text" size="6" name="payment_amount" value="" required />
                            <p class="description">Enter payment amount. Example values: 10.00 or 19.50 or 299.95 etc (do not put currency symbol).</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Payment Currency'); ?></th>
                        <td>
                            <select id="payment_currency" name="payment_currency">
                                <option selected="selected" value="USD">US Dollars ($)</option>
                                <option value="EUR">Euros (€)</option>
                                <option value="GBP">Pounds Sterling (£)</option>
                            </select>
                            <p class="description">Select the currency for this payment button.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Return URL'); ?></th>
                        <td>
                            <input type="text" size="100" name="return_url" value="" />
                            <p class="description">This is the URL the user will be redirected to after a successful payment. Enter the URL of your Thank You page here.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('PayPal Email'); ?></th>
                        <td>
                            <input type="text" size="50" name="paypal_email" value="" required />
                            <p class="description">Enter your PayPal email address. The payment will go to this PayPal account.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Button Image URL'); ?></th>
                        <td>
                            <input type="text" size="100" name="button_image_url" value="" />
                            <p class="description">If you want to customize the look of the button using an image then enter the URL of the image.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Custom Checkout Page Logo Image'); ?></th>
                        <td>
                            <input type="text" size="100" name="checkout_logo_image_url" value="" />
                            <p class="description">Specify an image URL if you want to customize the paypal checkout page with a custom logo/image. The image URL must be a "https" URL.</p>
                        </td>
                    </tr>

                </table>

                <p class="submit">
                    <?php wp_nonce_field('crwdfnd_admin_add_edit_pp_buy_now_btn','crwdfnd_admin_create_pp_buy_now_btn') ?>
                    <input type="submit" name="crwdfnd_pp_buy_now_save_submit" class="button-primary" value="<?php echo CrwdfndUtils::_('Save Payment Data'); ?>" >
                </p>

            </form>

        </div>
    </div>
    <?php
}


add_action('crwdfnd_create_new_button_process_submission', 'crwdfnd_save_new_pp_buy_now_button_data');

function crwdfnd_save_new_pp_buy_now_button_data() {
    if (isset($_REQUEST['crwdfnd_pp_buy_now_save_submit'])) {

        check_admin_referer( 'crwdfnd_admin_add_edit_pp_buy_now_btn', 'crwdfnd_admin_create_pp_buy_now_btn' );
        //Save the button data
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
        add_post_meta($button_id, 'payment_currency', sanitize_text_field($_REQUEST['payment_currency']));
        add_post_meta($button_id, 'return_url', trim(sanitize_text_field($_REQUEST['return_url'])));
        add_post_meta($button_id, 'paypal_email', trim(sanitize_email($_REQUEST['paypal_email'])));
        add_post_meta($button_id, 'button_image_url', trim(sanitize_text_field($_REQUEST['button_image_url'])));
        add_post_meta($button_id, 'checkout_logo_image_url', trim(sanitize_text_field($_REQUEST['checkout_logo_image_url'])));
        $url = admin_url() . 'admin.php?page=crowdfund_me_payments&tab=payment_buttons';
        CrwdfndMiscUtils::redirect_to_url($url);
    }
}

add_action('crwdfnd_edit_payment_button_for_pp_buy_now', 'crwdfnd_edit_pp_buy_now_button');

function crwdfnd_edit_pp_buy_now_button() {

    //Retrieve the payment button data and present it for editing.

    $button_id = sanitize_text_field($_REQUEST['button_id']);
    $button_id = absint($button_id);
    $button_type = sanitize_text_field($_REQUEST['button_type']);

    $button = get_post($button_id); //Retrieve the CPT for this button

    $membership_level_id = get_post_meta($button_id, 'membership_level_id', true);
    $payment_amount = get_post_meta($button_id, 'payment_amount', true);
    $payment_currency = get_post_meta($button_id, 'payment_currency', true);
    $return_url = get_post_meta($button_id, 'return_url', true);
    $paypal_email = get_post_meta($button_id, 'paypal_email', true);
    $button_image_url = get_post_meta($button_id, 'button_image_url', true);
    $checkout_logo_image_url = get_post_meta($button_id, 'checkout_logo_image_url', true);

    ?>
    <div class="postbox">
        <h3 class="hndle"><label for="title"><?php echo CrwdfndUtils::_('PayPal Buy Now Button Configuration'); ?></label></h3>
        <div class="inside">

            <form id="pp_button_config_form" method="post">
                <input type="hidden" name="button_type" value="<?php echo $button_type; ?>">

                <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Button ID'); ?></th>
                        <td>
                            <input type="text" size="10" name="button_id" value="<?php echo $button_id; ?>" readonly required />
                            <p class="description">This is the ID of this payment button. It is automatically generated for you and it cannot be changed.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Button Title'); ?></th>
                        <td>
                            <input type="text" size="50" name="button_name" value="<?php echo $button->post_title; ?>" required />
                            <p class="description">Give this membership payment button a name. Example: Gold membership payment</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Membership Level'); ?></th>
                        <td>
                            <select id="membership_level_id" name="membership_level_id">
                                <?php echo CrwdfndUtils::membership_level_dropdown($membership_level_id); ?>
                            </select>
                            <p class="description">Select the membership level this payment button is for.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Payment Amount'); ?></th>
                        <td>
                            <input type="text" size="6" name="payment_amount" value="<?php echo $payment_amount; ?>" required />
                            <p class="description">Enter payment amount. Example values: 10.00 or 19.50 or 299.95 etc (do not put currency symbol).</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Payment Currency'); ?></th>
                        <td>
                            <select id="payment_currency" name="payment_currency">
                                <option value="USD" <?php echo ($payment_currency == 'USD') ? 'selected="selected"' : ''; ?>>US Dollars ($)</option>
                                <option value="EUR" <?php echo ($payment_currency == 'EUR') ? 'selected="selected"' : ''; ?>>Euros (€)</option>
                                <option value="GBP" <?php echo ($payment_currency == 'GBP') ? 'selected="selected"' : ''; ?>>Pounds Sterling (£)</option>
                            </select>
                            <p class="description">Select the currency for this payment button.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Return URL'); ?></th>
                        <td>
                            <input type="text" size="100" name="return_url" value="<?php echo $return_url; ?>" />
                            <p class="description">This is the URL the user will be redirected to after a successful payment. Enter the URL of your Thank You page here.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('PayPal Email'); ?></th>
                        <td>
                            <input type="text" size="50" name="paypal_email" value="<?php echo $paypal_email; ?>" required />
                            <p class="description">Enter your PayPal email address. The payment will go to this PayPal account.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Button Image URL'); ?></th>
                        <td>
                            <input type="text" size="100" name="button_image_url" value="<?php echo $button_image_url; ?>" />
                            <p class="description">If you want to customize the look of the button using an image then enter the URL of the image.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Custom Checkout Page Logo Image'); ?></th>
                        <td>
                            <input type="text" size="100" name="checkout_logo_image_url" value="<?php echo $checkout_logo_image_url; ?>" />
                            <p class="description">Specify an image URL if you want to customize the paypal checkout page with a custom logo/image. The image URL must be a "https" URL.</p>
                        </td>
                    </tr>

                </table>

                <p class="submit">
                <?php wp_nonce_field('crwdfnd_admin_add_edit_pp_buy_now_btn','crwdfnd_admin_edit_pp_buy_now_btn') ?>
                <input type="submit" name="crwdfnd_pp_buy_now_edit_submit" class="button-primary" value="<?php echo CrwdfndUtils::_('Save Payment Data'); ?>" >
                </p>

            </form>

        </div>
    </div>
    <?php
}


add_action('crwdfnd_edit_payment_button_process_submission', 'crwdfnd_edit_pp_buy_now_button_data');

function crwdfnd_edit_pp_buy_now_button_data() {
    if (isset($_REQUEST['crwdfnd_pp_buy_now_edit_submit'])) {

        check_admin_referer( 'crwdfnd_admin_add_edit_pp_buy_now_btn', 'crwdfnd_admin_edit_pp_buy_now_btn' );

        //Update and Save the edited payment button data
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
        update_post_meta($button_id, 'payment_currency', sanitize_text_field($_REQUEST['payment_currency']));
        update_post_meta($button_id, 'return_url', trim(sanitize_text_field($_REQUEST['return_url'])));
        update_post_meta($button_id, 'paypal_email', trim(sanitize_email($_REQUEST['paypal_email'])));
        update_post_meta($button_id, 'button_image_url', trim(sanitize_text_field($_REQUEST['button_image_url'])));
        update_post_meta($button_id, 'checkout_logo_image_url', trim(sanitize_text_field($_REQUEST['checkout_logo_image_url'])));

        echo '<div id="message" class="updated fade"><p>Payment button data successfully updated!</p></div>';
    }
}
