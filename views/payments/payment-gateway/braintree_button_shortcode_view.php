<?php

add_filter('crwdfnd_payment_button_shortcode_for_braintree_buy_now', 'crwdfnd_render_braintree_buy_now_button_sc_output', 10, 2);

function crwdfnd_render_braintree_buy_now_button_sc_output($button_code, $args)
{

    $button_id = isset($args['id']) ? $args['id'] : '';
    if (empty($button_id)) {
        return '<p class="crwdfnd-red-box">Error! crwdfnd_render_braintree_buy_now_button_sc_output() function requires the button ID value to be passed to it.</p>';
    }


    $class = isset($args['class']) ? $args['class'] : '';


    $window_target = isset($args['new_window']) ? 'target="_blank"' : '';
    $button_text = (isset($args['button_text'])) ? $args['button_text'] : CrwdfndUtils::_('Buy Now');
    $billing_address = isset($args['billing_address']) ? '1' : '';;
    $item_logo = '';

    $settings = CrwdfndSettings::get_instance();
    $button_cpt = get_post($button_id);
    $item_name = htmlspecialchars($button_cpt->post_title);

    $membership_level_id = get_post_meta($button_id, 'membership_level_id', true);

    if (!CrwdfndUtils::membership_level_id_exists($membership_level_id)) {
        return '<p class="crwdfnd-red-box">Error! The membership level specified in this button does not exist. You may have deleted this membership level. Edit the button and use the correct membership level.</p>';
    }


    $payment_amount = get_post_meta($button_id, 'payment_amount', true);
    if (!is_numeric($payment_amount)) {
        return '<p class="crwdfnd-red-box">Error! The payment amount value of the button must be a numeric number. Example: 49.50 </p>';
    }
    $payment_amount = round($payment_amount, 2);
    $payment_currency = get_post_meta($button_id, 'currency_code', true);

    $payment_amount_formatted = CrwdfndMiscUtils::format_money($payment_amount,$payment_currency);


    $return_url = get_post_meta($button_id, 'return_url', true);
    if (empty($return_url)) {
        $return_url = CROWDFUND_ME_SITE_HOME_URL;
    }
    $notify_url = CROWDFUND_ME_SITE_HOME_URL . '/?crwdfnd_process_braintree_buy_now=1';


    $user_ip = CrwdfndUtils::get_user_ip_address();
    $_SESSION['crwdfnd_payment_button_interaction'] = $user_ip;


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


    $sandbox_enabled = $settings->get_value('enable-sandbox-testing');

    if ($sandbox_enabled) {
        $braintree_env = "sandbox";
    } else {
        $braintree_env = "production";
    }

    require_once(CROWDFUND_ME_PATH . 'lib/braintree/lib/autoload.php');

    try {
        Braintree_Configuration::environment($braintree_env);
        Braintree_Configuration::merchantId(get_post_meta($button_id, 'braintree_merchant_acc_id', true));
        Braintree_Configuration::publicKey(get_post_meta($button_id, 'braintree_public_key', true));
        Braintree_Configuration::privateKey(get_post_meta($button_id, 'braintree_private_key', true));
        $clientToken = Braintree_ClientToken::generate();
    } catch (Exception $e) {
        $e_class = get_class($e);
        $ret = 'Braintree Pay Now button error: ' . $e_class;
        if ($e_class == "Braintree\Exception\Authentication")
            $ret .= "<br />API keys are incorrect. Double-check that you haven't accidentally tried to use your sandbox keys in production or vice-versa.";
        return $ret;
    }

    $uniqid = uniqid();

    /* === Braintree Buy Now Button Form === */
    $output = '';
    $output .= '<div class="crwdfnd-button-wrapper crwdfnd-braintree-buy-now-wrapper">';
    $output .= "<form id='crwdfnd-braintree-payment-form-" . $uniqid . "' action='" . $notify_url . "' METHOD='POST'> ";
    $output .= '<div id="crwdfnd-form-cont-' . $uniqid . '" class="crwdfnd-braintree-form-container crwdfnd-form-container-' . $button_id . '" style="display:none;"></div>';
    $output .= '<div id="crwdfnd-braintree-additional-fields-container-' . $uniqid . '" class="crwdfnd-braintree-additional-fields-container crwdfnd-braintree-additional-fields-container-' . $button_id . '" style="display:none;">';
    $output .= '<p><input type="text" name="first_name" placeholder="First Name" value="' . (isset($member_first_name) ? $member_first_name : '') . '" required></p>';
    $output .= '<p><input type="text" name="last_name" placeholder="Last Name" value="' . (isset($member_last_name) ? $member_last_name : '') . '" required></p>';
    $output .= '<p><input type="text" name="member_email" placeholder="Email" value="' . (isset($member_email) ? $member_email : '') . '" required></p>';

    $coupon_input = '';
    $coupon_input = apply_filters('crwdfnd_payment_form_additional_fields', $coupon_input, $button_id, $uniqid);
    if (!empty($coupon_input)) {
        $output .= $coupon_input;
    }
    $output .= '<div id="crwdfnd-braintree-amount-container-' . $uniqid . '" class="crwdfnd-braintree-amount-container"><p>' . $payment_amount_formatted.'</p></div>';
    $output .= '</div>';
    $output .= '<button id="crwdfnd-show-form-btn-' . $uniqid . '" class="crwdfnd-braintree-pay-now-button crwdfnd-braintree-show-form-button-' . $button_id . ' ' . $class . '" type="button" onclick="crwdfnd_braintree_show_form_' . $uniqid . '();"><span>' . $button_text . '</span></button>';
    $output .= '<button id="crwdfnd-submit-form-btn-' . $uniqid . '" class="crwdfnd-braintree-pay-now-button crwdfnd-braintree-submit-form-button-' . $button_id . ' ' . $class . '" type="submit" style="display: none;"><span>' . $button_text . '</span></button>';
    $output .= '<script src="https://js.braintreegateway.com/js/braintree-2.32.1.min.js"></script>';
    ob_start();
    ?>
    <script>
        function crwdfnd_braintree_show_form__uniqid_() {
            document.getElementById('crwdfnd-show-form-btn-_uniqid_').style.display = "none";
            document.getElementById('crwdfnd-submit-form-btn-_uniqid_').style.display = "block";
            document.getElementById('crwdfnd-form-cont-_uniqid_').style.display = "block";
            var clientToken = '_token_';
            braintree.setup(clientToken, 'dropin', {
                container: 'crwdfnd-form-cont-_uniqid_',
                onReady: function(obj) {
                    document.getElementById('crwdfnd-braintree-additional-fields-container-_uniqid_').style.display = "block";
                },
                onPaymentMethodReceived: function(obj) {
                    document.getElementById('crwdfnd-submit-form-btn-_uniqid_').disabled = true;
                    var client = new braintree.api.Client({
                        clientToken: clientToken
                    });
                    if (obj.type !== 'CreditCard') {
                        document.getElementById('crwdfnd-braintree-nonce-field-_uniqid_').value = obj.nonce;
                        document.getElementById('crwdfnd-braintree-payment-form-_uniqid_').submit();
                        return true;
                    }
                    var form = document.getElementById('crwdfnd-braintree-payment-form-_uniqid_');
                    var amount = form.querySelector('[name="item_price"]').value;
                    client.verify3DS({
                        amount: amount,
                        creditCard: obj.nonce
                    }, function(err, response) {
                        if (!err) {
                            document.getElementById('crwdfnd-braintree-nonce-field-_uniqid_').value = response.nonce;
                            document.getElementById('crwdfnd-braintree-payment-form-_uniqid_').submit();
                        } else {
                            alert(err.message);
                            document.getElementById('crwdfnd-submit-form-btn-_uniqid_').disabled = false;
                            return false;
                        }
                    });
                }
            });
        }
    </script>
    <?php

    $scr = ob_get_clean();
    $scr = str_replace(array('_uniqid_', '_token_', '_amount_'), array($uniqid, $clientToken, $payment_amount), $scr);
    $output .= $scr;

    $output .= '<input type="hidden" name="item_number" value="' . $button_id . '" />';
    $output .= '<input type="hidden" id="crwdfnd-braintree-nonce-field-' . $uniqid . '" name="payment_method_nonce" value="" />';
    $output .= "<input type='hidden' value='{$item_name}' name='item_name' />";
    $output .= "<input type='hidden' value='{$payment_amount}' name='item_price' />";
    $output .= "<input type='hidden' value='{$payment_currency}' name='currency_code' />";
    $output .= "<input type='hidden' value='{$custom_field_value}' name='custom' />";


    $output .= apply_filters('crwdfnd_braintree_payment_form_additional_fields', '');

    $output .= "</form>";
    $output .= '</div>';

    return $output;
}
