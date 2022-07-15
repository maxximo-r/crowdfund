<?php
/* * ************************************************
 * PayPal Smart Checkout button shortcode handler
 * *********************************************** */
add_filter('crwdfnd_payment_button_shortcode_for_pp_smart_checkout', 'crwdfnd_render_pp_smart_checkout_button_sc_output', 10, 2);

function crwdfnd_render_pp_smart_checkout_button_sc_output($button_code, $args) {

    $button_id = isset($args['id']) ? $args['id'] : '';
    if (empty($button_id)) {
        return '<p class="crwdfnd-red-box">Error! crwdfnd_render_pp_smart_checkout_button_sc_output() function requires the button ID value to be passed to it.</p>';
    }

    //Check new_window parameter
    $button_text = (isset($args['button_text'])) ? $args['button_text'] : CrwdfndUtils::_('Buy Now');
    $billing_address = isset($args['billing_address']) ? '1' : '';
    ; //By default don't show the billing address in the checkout form.
    $item_logo = ''; //Can be used to show an item logo or thumbnail in the checkout form.

    $settings = CrwdfndSettings::get_instance();
    $button_cpt = get_post($button_id); //Retrieve the CPT for this button
    $item_name = htmlspecialchars($button_cpt->post_title);

    $membership_level_id = get_post_meta($button_id, 'membership_level_id', true);
    //Verify that this membership level exists (to prevent user paying for a level that has been deleted)
    if (!CrwdfndUtils::membership_level_id_exists($membership_level_id)) {
        return '<p class="crwdfnd-red-box">Error! The membership level specified in this button does not exist. You may have deleted this membership level. Edit the button and use the correct membership level.</p>';
    }

    //Payment amount and currency
    $payment_amount = get_post_meta($button_id, 'payment_amount', true);
    if (!is_numeric($payment_amount)) {
        return '<p class="crwdfnd-red-box">Error! The payment amount value of the button must be a numeric number. Example: 49.50 </p>';
    }
    $payment_amount = round($payment_amount, 2); //round the amount to 2 decimal place.
    $payment_amount_formatted = number_format($payment_amount, 2, '.', '');
    $payment_currency = get_post_meta($button_id, 'payment_currency', true);

    //Create the items_list for passing to PayPal API
    $items_list = "{name: '".$item_name."', quantity: '1', price: '".$payment_amount."', currency: '".$payment_currency."'}";
    
    //Return, cancel, notifiy URLs
    $return_url = get_post_meta($button_id, 'return_url', true);
    if (empty($return_url)) {
        $return_url = CROWDFUND_ME_SITE_HOME_URL;
    }
    //User's IP address
    $user_ip = CrwdfndUtils::get_user_ip_address();
    $_SESSION['crwdfnd_payment_button_interaction'] = $user_ip;

    //Custom field data
    $custom_field_value = 'subsc_ref=' . $membership_level_id;
    $custom_field_value .= '&user_ip=' . $user_ip;
    if (CrwdfndMemberUtils::is_member_logged_in()) {
        $member_id = CrwdfndMemberUtils::get_logged_in_members_id();
        $custom_field_value .= '&crwdfnd_id=' . $member_id;
        $member_first_name = CrwdfndMemberUtils::get_member_field_by_id($member_id, 'first_name');
        $member_last_name = CrwdfndMemberUtils::get_member_field_by_id($member_id, 'last_name');
        $member_email = CrwdfndMemberUtils::get_member_field_by_id($member_id, 'email');
    }
    $custom_field_value = apply_filters('crwdfnd_custom_field_value_filter', $custom_field_value);

    //Sandbox settings
    $sandbox_enabled = $settings->get_value('enable-sandbox-testing');

    if ($sandbox_enabled) {
        $mode = "sandbox";
    } else {
        $mode = "production";
    }

    $btn_layout = get_post_meta($button_id, 'pp_smart_checkout_btn_layout', true);
    $btn_layout = empty($btn_layout) ? 'vertical' : $btn_layout;
    $btn_size = get_post_meta($button_id, 'pp_smart_checkout_btn_size', true);
    $btn_size = empty($btn_size) ? 'medium' : $btn_size;
    $btn_shape = get_post_meta($button_id, 'pp_smart_checkout_btn_shape', true);
    $btn_shape = empty($btn_shape) ? 'rect' : $btn_shape;
    $btn_color = get_post_meta($button_id, 'pp_smart_checkout_btn_color', true);
    $btn_color = empty($btn_color) ? 'gold' : $btn_color;

    $pm_str = '';

    $pm_credit = get_post_meta($button_id, 'pp_smart_checkout_payment_method_credit', true);
    $pm_str .= empty($pm_credit) ? '' : ', paypal.FUNDING.CREDIT';
    $pm_elv = get_post_meta($button_id, 'pp_smart_checkout_payment_method_elv', true);
    $pm_str .= empty($pm_elv) ? '' : ', paypal.FUNDING.ELV';

    $uniqid = uniqid(); // Get unique ID to ensure several buttons can be added to one page without conflicts

    $output = '';
    ob_start();
    $ppCheckoutJs = '<script src="https://www.paypalobjects.com/api/checkout.js" data-version-4></script>';
    //check if checkout.js was already included
    //including it several times on one page causes JS fatal error
    if (!defined('CRWDFND-PP-SMART-CHECKOUT-SCRIPT-INCLUDED')) {
        //it wasn't. Let's include it and define an indicator that it is included now
        define('CRWDFND-PP-SMART-CHECKOUT-SCRIPT-INCLUDED', 1);
        echo $ppCheckoutJs;
    }
    ?>
    <div class="crwdfnd-button-wrapper">
    <?php
    //apply filter to output additional form fields
    $coupon_input = '';
    $coupon_input = apply_filters('crwdfnd_payment_form_additional_fields', $coupon_input, $button_id, $uniqid);
    if (!empty($coupon_input)) {
        echo $coupon_input;
    }
    $nonce=wp_create_nonce( 'crwdfnd-pp-smart-checkout-ajax-nonce-'.$uniqid );
    ?>
        <div class="crwdfnd-pp-smart-checkout-btn-<?php echo $uniqid; ?>"></div>
        <input type="hidden" id="crwdfnd-pp-smart-checkout-amount-<?php echo $uniqid; ?>" name="item_price" value="<?php echo $payment_amount;?>">
        <input type="hidden" id="crwdfnd-pp-smart-checkout-custom-<?php echo $uniqid; ?>" name="custom" value="<?php echo $custom_field_value; ?>">
        <script>
            paypal.Button.render({

                env: '<?php echo $mode; ?>',
                style: {
                    layout: '<?php echo esc_js($btn_layout); ?>',
                    size: '<?php echo esc_js($btn_size); ?>',
                    shape: '<?php echo esc_js($btn_shape); ?>',
                    color: '<?php echo esc_js($btn_color); ?>'
                },
                funding: {
                    allowed: [paypal.FUNDING.CARD<?php echo $pm_str; ?>],
                    disallowed: []
                },
                client: {
                    sandbox: '<?php echo esc_js(get_post_meta($button_id, 'pp_smart_checkout_test_id', true)); ?>',
                    production: '<?php echo esc_js(get_post_meta($button_id, 'pp_smart_checkout_live_id', true)); ?>'
                },
                validate: function (actions) {
                    //			    wpspsc_pp_actions = actions;
                    //			    wpspsc_pp_actions.disable();
                },
                onClick: function () {
                },
                payment: function (data, actions) {
                    var amount = document.getElementById('crwdfnd-pp-smart-checkout-amount-<?php echo $uniqid; ?>').value;
                    return actions.payment.create({
                        payment: {
                            transactions: [{
                                    amount: {total: amount, currency: '<?php echo $payment_currency; ?>'},
                                    item_list: {
                                        items: [<?php echo $items_list; ?>]
                                    }
                                }]
                        },
                        meta: {partner_attribution_id: 'TipsandTricks_SP'}
                    });
                },
                commit: true,
                onError: function (error) {
                    console.log(error);
                    alert('<?php echo esc_js(__("Error occurred during PayPal Smart Checkout process.", "crowd-fund")); ?>\n\n' + error);
                },
                onAuthorize: function (data, actions) {
                    var paymentBtnCont = jQuery('.crwdfnd-pp-smart-checkout-btn-<?php echo $uniqid; ?>');
                    var paymentBtnSpinner = paymentBtnCont.siblings('.crwdfnd-pp-sc-spinner-cont');
                    paymentBtnCont.hide();
                    paymentBtnSpinner.css('display', 'inline-block');
                    return actions.payment.execute().then(function (data) {
                        var custom = document.getElementById('crwdfnd-pp-smart-checkout-custom-<?php echo $uniqid; ?>').value;
                        data.custom_field = custom;
                        data.button_id = '<?php echo esc_js($button_id); ?>';
                        data.item_name = '<?php echo esc_js($item_name); ?>';
                        jQuery.post('<?php echo esc_js(admin_url('admin-ajax.php')); ?>',
                                {action: 'crwdfnd_process_pp_smart_checkout', crwdfnd_pp_smart_checkout_payment_data: data, nonce: '<?php echo $nonce?>', uniqid: '<?php echo $uniqid?>', custom: custom})
                                .done(function (result) {
                                    if (result.success) {
                                        window.location.href = '<?php echo esc_js($return_url); ?>';
                                    } else {
                                        console.log(result);
                                        alert(result.errMsg)
                                        paymentBtnCont.show();
                                        paymentBtnSpinner.hide();
                                    }
                                }
                                )
                                .fail(function (result) {
                                    console.log(result);
                                    paymentBtnCont.show();
                                    paymentBtnSpinner.hide();
                                    alert('<?php echo esc_js(__("HTTP error occurred during payment process:", "crowd-fund")); ?>' + ' ' + result.status + ' ' + result.statusText);
                                });
                    }
                    );
                }
            }, '.crwdfnd-pp-smart-checkout-btn-<?php echo $uniqid; ?>');

        </script>
        <style>
            @keyframes crwdfnd-pp-sc-spinner {
                to {transform: rotate(360deg);}
            }
            .crwdfnd-pp-sc-spinner {
                margin: 0 auto;
                text-indent: -9999px;
                vertical-align: middle;
                box-sizing: border-box;
                position: relative;
                width: 60px;
                height: 60px;
                border-radius: 50%;
                border: 5px solid #ccc;
                border-top-color: #0070ba;
                animation: crwdfnd-pp-sc-spinner .6s linear infinite;
            }
            .crwdfnd-pp-sc-spinner-cont {
                width: 100%;
                text-align: center;
                margin-top:10px;
                display: none;
            }
        </style>
        <div class="crwdfnd-pp-sc-spinner-cont">
            <div class="crwdfnd-pp-sc-spinner"></div>
        </div>
    </div>
    <?php
    $output .= ob_get_clean();

    return $output;
}
