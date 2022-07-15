<?php

include(CROWDFUND_ME_PATH . 'ipn/crwdfnd_handle_subsc_ipn.php');

class CrwdfndBraintreeBuyNowIpnHandler {

    public function __construct() {

        $this->handle_braintree_ipn();
    }

    public function handle_braintree_ipn() {
        CrwdfndLog::log_simple_debug("Braintree Buy Now IPN received. Processing request...", true);

        require_once(CROWDFUND_ME_PATH . 'lib/braintree/lib/autoload.php');

        $button_id = filter_input(INPUT_POST, 'item_number', FILTER_SANITIZE_NUMBER_INT);
        $button_title = sanitize_text_field($_POST['item_name']);
        $payment_amount = sanitize_text_field($_POST['item_price']);

        $button_cpt = get_post($button_id);
        if (!$button_cpt) {
            CrwdfndLog::log_simple_debug("Fatal Error! Failed to retrieve the payment button post object for the given button ID: " . $button_id, false);
            wp_die("Fatal Error! Payment button (ID: " . $button_id . ") does not exist. This request will fail.");
        }

        $membership_level_id = get_post_meta($button_id, 'membership_level_id', true);

        $true_payment_amount = get_post_meta($button_id, 'payment_amount', true);
        $true_payment_amount = apply_filters('crwdfnd_payment_amount_filter',$true_payment_amount,$button_id);
        if ($payment_amount != $true_payment_amount) {
            $error_msg = 'Fatal Error! Received payment amount (' . $payment_amount . ') does not match with the original amount (' . $true_payment_amount . ')';
            CrwdfndLog::log_simple_debug($error_msg, false);
            wp_die($error_msg);
        }

        $settings = CrwdfndSettings::get_instance();
        $sandbox_enabled = $settings->get_value('enable-sandbox-testing');
        if ($sandbox_enabled) {
            CrwdfndLog::log_simple_debug("Sandbox payment mode is enabled. Using sandbox enviroment.", true);
            $braintree_env = "sandbox";
        } else {
            $braintree_env = "production";
        }

        try {
            Braintree_Configuration::environment($braintree_env);
            Braintree_Configuration::merchantId(get_post_meta($button_id, 'braintree_merchant_acc_id', true));
            Braintree_Configuration::publicKey(get_post_meta($button_id, 'braintree_public_key', true));
            Braintree_Configuration::privateKey(get_post_meta($button_id, 'braintree_private_key', true));

            $braintree_merc_acc_name = get_post_meta($button_id, 'braintree_merchant_acc_name', true);

            $nonce = sanitize_text_field($_POST['payment_method_nonce']);

            $result = Braintree_Transaction::sale([
                        'amount' => $payment_amount,
                        'paymentMethodNonce' => $nonce,
                        'channel' => 'TipsandTricks_SP',
                        'options' => [
                            'submitForSettlement' => True
                        ],
                        'merchantAccountId' => $braintree_merc_acc_name,
            ]);
        } catch (Exception $e) {
            CrwdfndLog::log_simple_debug("Braintree library error occurred: " . get_class($e) . ", button ID: " . $button_id, false);
            wp_die('Braintree library error occurred: ' . get_class($e));
        }

        if (!$result->success) {
            CrwdfndLog::log_simple_debug("Braintree transaction error occurred: " . $result->transaction->status . ", button ID: " . $button_id, false);
            wp_die("Braintree transaction error occurred: " . $result->transaction->status);
        } else {

            CrwdfndLog::log_simple_debug("Braintree Buy Now charge successful.", true);

            $txn_id = $result->transaction->id;

            $custom = sanitize_text_field($_POST['custom']);
            $custom_var = CrwdfndTransactions::parse_custom_var($custom);
            $crwdfnd_id = isset($custom_var['crwdfnd_id']) ? $custom_var['crwdfnd_id'] : '';

            $ipn_data = array();
            $ipn_data['mc_gross'] = $payment_amount;
            $ipn_data['first_name'] = sanitize_text_field($_POST['first_name']);
            $ipn_data['last_name'] = sanitize_text_field($_POST['last_name']);
            $ipn_data['payer_email'] = filter_input(INPUT_POST, 'member_email', FILTER_SANITIZE_EMAIL);
            $ipn_data['membership_level'] = $membership_level_id;
            $ipn_data['txn_id'] = $txn_id;
            $ipn_data['subscr_id'] = $txn_id;
            $ipn_data['crwdfnd_id'] = $crwdfnd_id;
            $ipn_data['ip'] = $custom_var['user_ip'];
            $ipn_data['custom'] = $custom;
            $ipn_data['gateway'] = 'braintree';
            $ipn_data['status'] = 'completed';

            $ipn_data['address_street'] = '';
            $ipn_data['address_city'] = '';
            $ipn_data['address_state'] = '';
            $ipn_data['address_zipcode'] = '';
            $ipn_data['country'] = '';

            crwdfnd_handle_subsc_signup_stand_alone($ipn_data, $membership_level_id, $txn_id, $crwdfnd_id);

            CrwdfndTransactions::save_txn_record($ipn_data);
            CrwdfndLog::log_simple_debug('Transaction data saved.', true);


            do_action('crwdfnd_braintree_ipn_processed', $ipn_data);

            do_action('crwdfnd_payment_ipn_processed', $ipn_data);

            $return_url = get_post_meta($button_id, 'return_url', true);
            if (empty($return_url)) {
                $return_url = CROWDFUND_ME_SITE_HOME_URL;
            }
            CrwdfndLog::log_simple_debug("Redirecting customer to: " . $return_url, true);
            CrwdfndLog::log_simple_debug("End of Braintree Buy Now IPN processing.", true, true);
            CrwdfndMiscUtils::redirect_to_url($return_url);
        }
    }

}

$crwdfnd_braintree_buy_ipn = new CrwdfndBraintreeBuyNowIpnHandler();
