<?php

add_action('crwdfnd_create_new_button_for_pp_subscription', 'crwdfnd_create_new_pp_subscription_button');

function crwdfnd_create_new_pp_subscription_button() {
    ?>

    <form id="pp_button_config_form" method="post">

        <div class="postbox">
            <h3 class="hndle"><label for="title"><?php echo CrwdfndUtils::_('PayPal Subscription Button Configuration'); ?></label></h3>
            <div class="inside">

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
                        <th scope="row"><?php echo CrwdfndUtils::_('PayPal Email'); ?></th>
                        <td>
                            <input type="text" size="50" name="paypal_email" value="" required />
                            <p class="description">Enter your PayPal email address. The payment will go to this PayPal account.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Billing Amount Each Cycle'); ?></th>
                        <td>
                            <input type="text" size="6" name="billing_amount" value="" required />
                            <p class="description">Amount to be charged on every billing cycle. If used with a trial period then this amount will be charged after the trial period is over. Example values: 10.00 or 19.50 or 299.95 etc (do not put currency symbol).</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Billing Cycle'); ?></th>
                        <td>
                            <input type="text" size="4" name="billing_cycle" value="" required />
                            <select id="billing_cycle_term" name="billing_cycle_term">
                                <option value="D">Day(s)</option>
                                <option value="M">Month(s)</option>
                                <option value="Y">Year(s)</option>
                            </select>
                            <p class="description">Set the interval of the recurring payment. Example value: 1 Month (if you want to charge every month)</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Billing Cycle Count'); ?></th>
                        <td>
                            <input type="text" size="6" name="billing_cycle_count" value="" />
                            <p class="description">After how many cycles should billing stop. Leave this field empty (or enter 0) if you want the payment to continue until the subscription is canceled.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Re-attempt on Failure'); ?></th>
                        <td>
                            <input type="checkbox" name="billing_reattempt" value="1" />
                            <p class="description">When checked, the payment will be re-attempted two more times if the payment fails. After the third failure, the subscription will be canceled..</p>
                        </td>
                    </tr>

                </table>

            </div>
        </div><!-- end of main button configuration box -->

        <div class="postbox">
            <h3 class="hndle"><label for="title"><?php echo CrwdfndUtils::_('Trial Billing Details (Leave empty if you are not offering a trial period)'); ?></label></h3>
            <div class="inside">

                <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Trial Billing Amount'); ?></th>
                        <td>
                            <input type="text" size="6" name="trial_billing_amount" value="" />
                            <p class="description">Amount to be charged for the trial period. Enter 0 if you want to offer a free trial period.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Trial Billing Period'); ?></th>
                        <td>
                            <input type="text" size="4" name="trial_billing_cycle" value="" />
                            <select id="billing_cycle_term" name="trial_billing_cycle_term">
                                <option value="D">Day(s)</option>
                                <option value="M">Month(s)</option>
                                <option value="Y">Year(s)</option>
                            </select>
                            <p class="description">Length of the trial period</p>
                        </td>
                    </tr>

                </table>
            </div>
        </div><!-- end of trial billing details box -->

        <div class="postbox">
            <h3 class="hndle"><label for="title"><?php echo CrwdfndUtils::_('Optional Details'); ?></label></h3>
            <div class="inside">

                <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Return URL'); ?></th>
                        <td>
                            <input type="text" size="100" name="return_url" value="" />
                            <p class="description">This is the URL the user will be redirected to after a successful payment. Enter the URL of your Thank You page here.</p>
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
            </div>
        </div><!-- end of optional details box -->

        <p class="submit">
        <?php wp_nonce_field('crwdfnd_admin_add_edit_pp_subs_btn','crwdfnd_admin_create_pp_subs_btn') ?>
        <input type="submit" name="crwdfnd_pp_subscription_save_submit" class="button-primary" value="<?php echo CrwdfndUtils::_('Save Payment Data'); ?>" >
        </p>

    </form>

    <?php
}

/*
 * Process submission and save the new PayPal Subscription payment button data
 */
add_action('crwdfnd_create_new_button_process_submission', 'crwdfnd_save_new_pp_subscription_button_data');

function crwdfnd_save_new_pp_subscription_button_data() {
    if (isset($_REQUEST['crwdfnd_pp_subscription_save_submit'])) {
        //This is a PayPal subscription button save event. Process the submission.

        check_admin_referer( 'crwdfnd_admin_add_edit_pp_subs_btn', 'crwdfnd_admin_create_pp_subs_btn' );

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
        add_post_meta($button_id, 'payment_currency', sanitize_text_field($_REQUEST['payment_currency']));
        add_post_meta($button_id, 'return_url', trim(sanitize_text_field($_REQUEST['return_url'])));
        add_post_meta($button_id, 'paypal_email', trim(sanitize_email($_REQUEST['paypal_email'])));
        add_post_meta($button_id, 'button_image_url', trim(sanitize_text_field($_REQUEST['button_image_url'])));
        add_post_meta($button_id, 'checkout_logo_image_url', trim(sanitize_text_field($_REQUEST['checkout_logo_image_url'])));

        //Subscription billing details
        add_post_meta($button_id, 'billing_amount', sanitize_text_field($_REQUEST['billing_amount']));
        add_post_meta($button_id, 'billing_cycle', sanitize_text_field($_REQUEST['billing_cycle']));
        add_post_meta($button_id, 'billing_cycle_term', sanitize_text_field($_REQUEST['billing_cycle_term']));
        add_post_meta($button_id, 'billing_cycle_count', sanitize_text_field($_REQUEST['billing_cycle_count']));
        add_post_meta($button_id, 'billing_reattempt', isset($_REQUEST['billing_reattempt']) ? '1' : '');

        //Trial billing details
        add_post_meta($button_id, 'trial_billing_amount', sanitize_text_field($_REQUEST['trial_billing_amount']));
        add_post_meta($button_id, 'trial_billing_cycle', sanitize_text_field($_REQUEST['trial_billing_cycle']));
        add_post_meta($button_id, 'trial_billing_cycle_term', sanitize_text_field($_REQUEST['trial_billing_cycle_term']));

        //Redirect to the edit interface of this button with $button_id
        $url = admin_url() . 'admin.php?page=crowdfund_me_payments&tab=edit_button&button_id=' . $button_id . '&button_type=' . $button_type;
        CrwdfndMiscUtils::redirect_to_url($url);
    }
}

add_action('crwdfnd_edit_payment_button_for_pp_subscription', 'crwdfnd_edit_pp_subscription_button');

function crwdfnd_edit_pp_subscription_button() {
    //Retrieve the payment button data and present it for editing.

    $button_id = sanitize_text_field($_REQUEST['button_id']);
    $button_id = absint($button_id);
    $button_type = sanitize_text_field($_REQUEST['button_type']);

    $button = get_post($button_id); //Retrieve the CPT for this button

    $membership_level_id = get_post_meta($button_id, 'membership_level_id', true);
    //$payment_amount = get_post_meta($button_id, 'payment_amount', true);
    $payment_currency = get_post_meta($button_id, 'payment_currency', true);
    $return_url = get_post_meta($button_id, 'return_url', true);
    $paypal_email = get_post_meta($button_id, 'paypal_email', true);
    $button_image_url = get_post_meta($button_id, 'button_image_url', true);
    $checkout_logo_image_url = get_post_meta($button_id, 'checkout_logo_image_url', true);

    //Subscription billing details
    $billing_amount = get_post_meta($button_id, 'billing_amount', true);
    $billing_cycle = get_post_meta($button_id, 'billing_cycle', true);
    $billing_cycle_term = get_post_meta($button_id, 'billing_cycle_term', true);
    $billing_cycle_count = get_post_meta($button_id, 'billing_cycle_count', true);
    $billing_reattempt = get_post_meta($button_id, 'billing_reattempt', true);

    //Trial billing details
    $trial_billing_amount = get_post_meta($button_id, 'trial_billing_amount', true);
    $trial_billing_cycle = get_post_meta($button_id, 'trial_billing_cycle', true);
    $trial_billing_cycle_term = get_post_meta($button_id, 'trial_billing_cycle_term', true);
    ?>
    <form id="pp_button_config_form" method="post">

        <div class="postbox">
            <h3 class="hndle"><label for="title"><?php echo CrwdfndUtils::_('PayPal Subscription Button Configuration'); ?></label></h3>
            <div class="inside">

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
                        <th scope="row"><?php echo CrwdfndUtils::_('PayPal Email'); ?></th>
                        <td>
                            <input type="text" size="50" name="paypal_email" value="<?php echo $paypal_email; ?>" required />
                            <p class="description">Enter your PayPal email address. The payment will go to this PayPal account.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Billing Amount Each Cycle'); ?></th>
                        <td>
                            <input type="text" size="6" name="billing_amount" value="<?php echo $billing_amount; ?>" required />
                            <p class="description">Amount to be charged on every billing cycle. If used with a trial period then this amount will be charged after the trial period is over. Example values: 10.00 or 19.50 or 299.95 etc (do not put currency symbol).</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Billing Cycle'); ?></th>
                        <td>
                            <input type="text" size="4" name="billing_cycle" value="<?php echo $billing_cycle; ?>" required />
                            <select id="billing_cycle_term" name="billing_cycle_term">
                                <option value="D" <?php echo ($billing_cycle_term == 'D') ? 'selected="selected"' : ''; ?>>Day(s)</option>
                                <option value="M" <?php echo ($billing_cycle_term == 'M') ? 'selected="selected"' : ''; ?>>Month(s)</option>
                                <option value="Y" <?php echo ($billing_cycle_term == 'Y') ? 'selected="selected"' : ''; ?>>Year(s)</option>
                            </select>
                            <p class="description">Set the interval of the recurring payment. Example value: 1 Month (if you want to charge every month)</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Billing Cycle Count'); ?></th>
                        <td>
                            <input type="text" size="6" name="billing_cycle_count" value="<?php echo $billing_cycle_count; ?>" />
                            <p class="description">After how many cycles should billing stop. Leave this field empty (or enter 0) if you want the payment to continue until the subscription is canceled.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Re-attempt on Failure'); ?></th>
                        <td>
                            <input type="checkbox" name="billing_reattempt" value="1" <?php if ($billing_reattempt != '') {
                                    echo ' checked="checked"';
                                } ?> />
                            <p class="description">When checked, the payment will be re-attempted two more times if the payment fails. After the third failure, the subscription will be canceled..</p>
                        </td>
                    </tr>

                </table>

            </div>
        </div><!-- end of main button configuration box -->

        <div class="postbox">
            <h3 class="hndle"><label for="title"><?php echo CrwdfndUtils::_('Trial Billing Details (Leave empty if you are not offering a trial period)'); ?></label></h3>
            <div class="inside">

                <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Trial Billing Amount'); ?></th>
                        <td>
                            <input type="text" size="6" name="trial_billing_amount" value="<?php echo $trial_billing_amount; ?>" />
                            <p class="description">Amount to be charged for the trial period. Enter 0 if you want to offer a free trial period.</p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Trial Billing Period'); ?></th>
                        <td>
                            <input type="text" size="4" name="trial_billing_cycle" value="<?php echo $trial_billing_cycle; ?>" />
                            <select id="billing_cycle_term" name="trial_billing_cycle_term">
                                <option value="D" <?php echo ($trial_billing_cycle_term == 'D') ? 'selected="selected"' : ''; ?>>Day(s)</option>
                                <option value="M" <?php echo ($trial_billing_cycle_term == 'M') ? 'selected="selected"' : ''; ?>>Month(s)</option>
                                <option value="Y" <?php echo ($trial_billing_cycle_term == 'Y') ? 'selected="selected"' : ''; ?>>Year(s)</option>
                            </select>
                            <p class="description">Length of the trial period</p>
                        </td>
                    </tr>

                </table>
            </div>
        </div><!-- end of trial billing details box -->

        <div class="postbox">
            <h3 class="hndle"><label for="title"><?php echo CrwdfndUtils::_('Optional Details'); ?></label></h3>
            <div class="inside">

                <table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">

                    <tr valign="top">
                        <th scope="row"><?php echo CrwdfndUtils::_('Return URL'); ?></th>
                        <td>
                            <input type="text" size="100" name="return_url" value="<?php echo $return_url; ?>" />
                            <p class="description">This is the URL the user will be redirected to after a successful payment. Enter the URL of your Thank You page here.</p>
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
            </div>
        </div><!-- end of optional details box -->

        <p class="submit">
            <?php wp_nonce_field('crwdfnd_admin_add_edit_pp_subs_btn','crwdfnd_admin_edit_pp_subs_btn') ?>
            <input type="submit" name="crwdfnd_pp_subscription_save_submit" class="button-primary" value="<?php echo CrwdfndUtils::_('Save Payment Data'); ?>" >
        </p>

    </form>

    <?php
}

add_action('crwdfnd_edit_payment_button_process_submission', 'crwdfnd_edit_pp_subscription_button_data');

function crwdfnd_edit_pp_subscription_button_data() {
    if (isset($_REQUEST['crwdfnd_pp_subscription_save_submit'])) {
        //This is a PayPal subscription button edit event. Process the submission.

        check_admin_referer( 'crwdfnd_admin_add_edit_pp_subs_btn', 'crwdfnd_admin_edit_pp_subs_btn' );

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
        update_post_meta($button_id, 'payment_currency', sanitize_text_field($_REQUEST['payment_currency']));
        update_post_meta($button_id, 'return_url', trim(sanitize_text_field($_REQUEST['return_url'])));
        update_post_meta($button_id, 'paypal_email', trim(sanitize_email($_REQUEST['paypal_email'])));
        update_post_meta($button_id, 'button_image_url', trim(sanitize_text_field($_REQUEST['button_image_url'])));
        update_post_meta($button_id, 'checkout_logo_image_url', trim(sanitize_text_field($_REQUEST['checkout_logo_image_url'])));

        //Subscription billing details
        update_post_meta($button_id, 'billing_amount', sanitize_text_field($_REQUEST['billing_amount']));
        update_post_meta($button_id, 'billing_cycle', sanitize_text_field($_REQUEST['billing_cycle']));
        update_post_meta($button_id, 'billing_cycle_term', sanitize_text_field($_REQUEST['billing_cycle_term']));
        update_post_meta($button_id, 'billing_cycle_count', sanitize_text_field($_REQUEST['billing_cycle_count']));
        update_post_meta($button_id, 'billing_reattempt', isset($_REQUEST['billing_reattempt']) ? '1' : '');

        //Trial billing details
        update_post_meta($button_id, 'trial_billing_amount', sanitize_text_field($_REQUEST['trial_billing_amount']));
        update_post_meta($button_id, 'trial_billing_cycle', sanitize_text_field($_REQUEST['trial_billing_cycle']));
        update_post_meta($button_id, 'trial_billing_cycle_term', sanitize_text_field($_REQUEST['trial_billing_cycle_term']));

        echo '<div id="message" class="updated fade"><p>Payment button data successfully updated!</p></div>';
    }
}
