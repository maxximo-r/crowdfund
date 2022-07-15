<?php

require_once 'crwdfnd_handle_subsc_ipn.php';

class crwdfnd_smart_checkout_ipn_handler {

	public $ipn_log = false;
	public $ipn_log_file;
	public $ipn_response;
	public $ipn_data     = array();
	public $fields       = array();
	public $sandbox_mode = false;

	public function __construct() {
		$this->paypal_url   = 'https://www.paypal.com/cgi-bin/webscr';
		$this->ipn_log_file = 'ipn_handle_debug_crwdfnd.log';
		$this->ipn_response = '';
	}

	public function crwdfnd_validate_and_create_membership() {
		$error_msg = '';

		$gross_total      = $this->ipn_data['mc_gross'];
		$transaction_type = $this->ipn_data['txn_type'];
		$txn_id           = $this->ipn_data['txn_id'];
		$payment_status   = $this->ipn_data['payment_status'];


		if ( ! empty( $payment_status ) ) {
			if ( 'Denied' == $payment_status ) {
				$this->debug_log( 'Payment status for this transaction is DENIED. You denied the transaction... most likely a cancellation of an eCheque. Nothing to do here.', false );
				return false;
			}
			if ( 'Canceled_Reversal' == $payment_status ) {
				$this->debug_log( 'This is a dispute closed notification in your favour. The plugin will not do anyting.', false );
				return true;
			}
			if ( 'Completed' != $payment_status && 'Processed' != $payment_status && 'Refunded' != $payment_status && 'Reversed' != $payment_status ) {
				$error_msg .= 'Funds have not been cleared yet. Transaction will be processed when the funds clear!';
				$this->debug_log( $error_msg, false );
				$this->debug_log( wp_json_encode( $this->ipn_data ), false );
				return false;
			}
		}


		if ( 'new_case' == $transaction_type ) {
			$this->debug_log( 'This is a dispute case. Nothing to do here.', true );
			return true;
		}

		$custom                   = urldecode( $this->ipn_data['custom'] );
		$this->ipn_data['custom'] = $custom;
		$customvariables          = CrwdfndTransactions::parse_custom_var( $custom );


		if ( $gross_total < 0 ) {

			$this->debug_log( 'This is a refund notification. Refund amount: ' . $gross_total, true );
			crwdfnd_handle_subsc_cancel_stand_alone( $this->ipn_data, true );
			return true;
		}
		if ( isset( $this->ipn_data['reason_code'] ) && 'refund' == $this->ipn_data['reason_code'] ) {
			$this->debug_log( 'This is a refund notification. Refund amount: ' . $gross_total, true );
			crwdfnd_handle_subsc_cancel_stand_alone( $this->ipn_data, true );
			return true;
		}

		if ( ( 'subscr_signup' == $transaction_type ) ) {
			$this->debug_log( 'Subscription signup IPN received... (handled by the subscription IPN handler)', true );

			$subsc_ref = $customvariables['subsc_ref'];

			if ( ! empty( $subsc_ref ) ) {
				$this->debug_log( 'Found a membership level ID. Creating member account...', true );
				$crwdfnd_id = $customvariables['crwdfnd_id'];
				crwdfnd_handle_subsc_signup_stand_alone( $this->ipn_data, $subsc_ref, $this->ipn_data['subscr_id'], $crwdfnd_id );

			}
			return true;
		} elseif ( ( 'subscr_cancel' == $transaction_type ) || ( 'subscr_eot' == $transaction_type ) || ( 'subscr_failed' == $transaction_type ) ) {

			$this->debug_log( 'Subscription cancellation IPN received... (handled by the subscription IPN handler)', true );
			crwdfnd_handle_subsc_cancel_stand_alone( $this->ipn_data );
			return true;
		} else {
			$cart_items = array();
			$this->debug_log( 'Transaction Type: Buy Now/Subscribe', true );
			$item_number = $this->ipn_data['item_number'];
			$item_name   = $this->ipn_data['item_name'];
			$quantity    = $this->ipn_data['quantity'];
			$mc_gross    = $this->ipn_data['mc_gross'];
			$mc_currency = $this->ipn_data['mc_currency'];

			$current_item = array(
				'item_number' => $item_number,
				'item_name'   => $item_name,
				'quantity'    => $quantity,
				'mc_gross'    => $mc_gross,
				'mc_currency' => $mc_currency,
			);

			array_push( $cart_items, $current_item );
		}

		$counter = 0;
		foreach ( $cart_items as $current_cart_item ) {
			$cart_item_data_num      = $current_cart_item['item_number'];
			$cart_item_data_name     = trim( $current_cart_item['item_name'] );
			$cart_item_data_quantity = $current_cart_item['quantity'];
			$cart_item_data_total    = $current_cart_item['mc_gross'];
			$cart_item_data_currency = $current_cart_item['mc_currency'];
			if ( empty( $cart_item_data_quantity ) ) {
				$cart_item_data_quantity = 1;
			}
			$this->debug_log( 'Item Number: ' . $cart_item_data_num, true );
			$this->debug_log( 'Item Name: ' . $cart_item_data_name, true );
			$this->debug_log( 'Item Quantity: ' . $cart_item_data_quantity, true );
			$this->debug_log( 'Item Total: ' . $cart_item_data_total, true );
			$this->debug_log( 'Item Currency: ' . $cart_item_data_currency, true );

			// Get the button id.
			$pp_hosted_button    = false;
			$button_id           = $cart_item_data_num; // Button id is the item number.
			$membership_level_id = get_post_meta( $button_id, 'membership_level_id', true );
			if ( ! CrwdfndUtils::membership_level_id_exists( $membership_level_id ) ) {
				$this->debug_log( 'This payment button was not created in the plugin. This is a paypal hosted button.', true );
				$pp_hosted_button = true;
			}


			$check_price = true;
			$msg         = '';
			$msg         = apply_filters( 'crwdfnd_before_price_check_filter', $msg, $current_cart_item );
			if ( ! empty( $msg ) && 'price-check-override' == $msg ) {
				$check_price = false;
				$this->debug_log( 'Price and currency check has been overridden by an addon/extension.', true );
			}
			if ( $check_price && ! $pp_hosted_button ) {

				$button_type = get_post_meta( $button_id, 'button_type', true );
				if ( 'pp_smart_checkout' == $button_type ) {
					$expected_amount = ( get_post_meta( $button_id, 'payment_amount', true ) ) * $cart_item_data_quantity;
					$expected_amount = round( $expected_amount, 2 );
					$expected_amount = apply_filters( 'crwdfnd_payment_amount_filter', $expected_amount, $button_id );
					$received_amount = $cart_item_data_total;
				} else {
					$this->debug_log( 'Error! Unexpected button type: ' . $button_type, false );
					return false;
				}

				if ( $received_amount < $expected_amount ) {

					$this->debug_log( 'Expected amount: ' . $expected_amount, true );
					$this->debug_log( 'Received amount: ' . $received_amount, true );
					$this->debug_log( 'Price check failed. Amount received is less than the amount expected. This payment will not be processed.', false );
					return false;
				}
			}


			$subsc_ref = $customvariables['subsc_ref'];
			$this->debug_log( 'Membership payment paid for membership level ID: ' . $subsc_ref, true );
			if ( ! empty( $subsc_ref ) ) {
				$crwdfnd_id = '';
				if ( isset( $customvariables['crwdfnd_id'] ) ) {
					$crwdfnd_id = $customvariables['crwdfnd_id'];
				}
				if ( 'smart_checkout' == $transaction_type ) {
					$this->debug_log( 'Transaction type: web_accept. Creating member account...', true );
					crwdfnd_handle_subsc_signup_stand_alone( $this->ipn_data, $subsc_ref, $this->ipn_data['txn_id'], $crwdfnd_id );
				}
			} else {
				$this->debug_log( 'Membership level ID is missing in the payment notification! Cannot process this notification.', false );
			}
			$counter++;
		}

		$this->debug_log( 'Saving transaction data to the database table.', true );
		$this->ipn_data['gateway'] = 'pp_smart_checkout';
		$this->ipn_data['status']  = $this->ipn_data['payment_status'];
		CrwdfndTransactions::save_txn_record( $this->ipn_data, $cart_items );
		$this->debug_log( 'Transaction data saved.', true );


		do_action( 'crwdfnd_pp_smart_checkout_ipn_processed', $this->ipn_data );

		do_action( 'crwdfnd_payment_ipn_processed', $this->ipn_data );

		return true;
	}

	public function create_ipn_from_smart_checkout( $data ) {

                $address_street = $data['payer']['payer_info']['shipping_address']['line1'];
                if ( isset ( $data[ 'payer' ][ 'payer_info' ][ 'shipping_address' ][ 'line2' ] )){
                    $address_street .= ", " . $data[ 'payer' ][ 'payer_info' ][ 'shipping_address' ][ 'line2' ];
                }

		$ipn['custom']              = $data['custom_field'];
		$ipn['item_number']         = $data['button_id'];
		$ipn['item_name']           = $data['item_name'];
		$ipn['pay_id']              = $data['id'];
		$ipn['create_time']         = $data['create_time'];
		$ipn['txn_id']              = $data['transactions'][0]['related_resources'][0]['sale']['id'];
		$ipn['reason_code']         = ! empty( $data['transactions'][0]['related_resources'][0]['sale']['reason_code'] ) ? $data['transactions'][0]['related_resources'][0]['sale']['reason_code'] : '';
		$ipn['txn_type']            = 'smart_checkout';
		$ipn['payment_status']      = ucfirst( $data['transactions'][0]['related_resources'][0]['sale']['state'] );
		$ipn['transaction_subject'] = '';
		$ipn['mc_currency']         = $data['transactions'][0]['amount']['currency'];
		$ipn['mc_gross']            = $data['transactions'][0]['amount']['total'];
		$ipn['quantity']            = 1;
		$ipn['receiver_email']      = get_option( 'cart_paypal_email' );

		$ipn['first_name']      = $data['payer']['payer_info']['first_name'];
		$ipn['last_name']       = $data['payer']['payer_info']['last_name'];
		$ipn['payer_email']     = $data['payer']['payer_info']['email'];
		$ipn['address_street']  = $address_street;
		$ipn['address_city']    = $data['payer']['payer_info']['shipping_address']['city'];
		$ipn['address_state']   = $data['payer']['payer_info']['shipping_address']['state'];
		$ipn['address_zip']     = $data['payer']['payer_info']['shipping_address']['postal_code'];
		$ipn['address_country'] = $data['payer']['payer_info']['shipping_address']['country_code'];

		$this->ipn_data = $ipn;
		return true;
	}

	public function validate_ipn_smart_checkout() {

		if ( $this->sandbox_mode ) {
			$client_id = get_post_meta( $this->ipn_data['item_number'], 'pp_smart_checkout_test_id', true );
			$secret    = get_post_meta( $this->ipn_data['item_number'], 'pp_smart_checkout_test_sec', true );
			$api_base  = 'https://api.sandbox.paypal.com';
		} else {
			$client_id = get_post_meta( $this->ipn_data['item_number'], 'pp_smart_checkout_live_id', true );
			$secret    = get_post_meta( $this->ipn_data['item_number'], 'pp_smart_checkout_live_sec', true );
			$api_base  = 'https://api.paypal.com';
		}

		$wp_request_headers = array(
			'Accept'        => 'application/json',

			'Authorization' => 'Basic ' . base64_encode( $client_id . ':' . $secret ), // phpcs:ignore
		);

		$res = wp_remote_request(
			$api_base . '/v1/oauth2/token',
			array(
				'method'  => 'POST',
				'headers' => $wp_request_headers,
				'body'    => 'grant_type=client_credentials',
			)
		);

		$code = wp_remote_retrieve_response_code( $res );

		if ( 200 !== $code ) {

			$body = wp_remote_retrieve_body( $res );

			return sprintf( __( 'Error occured during payment verification. Error code: %1$d. Message: %2$s', 'crowd-fund' ), $code, $body );
		}

		$body = wp_remote_retrieve_body( $res );
		$body = json_decode( $body );

		$token = $body->access_token;

		$wp_request_headers = array(
			'Accept'        => 'application/json',
			'Authorization' => 'Bearer ' . $token,
		);

		$res = wp_remote_request(
			$api_base . '/v1/payments/payment/' . $this->ipn_data['pay_id'],
			array(
				'method'  => 'GET',
				'headers' => $wp_request_headers,
			)
		);

		$code = wp_remote_retrieve_response_code( $res );

		if ( 200 !== $code ) {

			$body = wp_remote_retrieve_body( $res );

			return sprintf( __( 'Error occured during payment verification. Error code: %1$d. Message: %2$s', 'crowd-fund' ), $code, $body );
		}

		$body = wp_remote_retrieve_body( $res );
		$body = json_decode( $body );


		if ( $body->transactions[0]->amount->total === $this->ipn_data['mc_gross'] &&
				$body->transactions[0]->amount->currency === $this->ipn_data['mc_currency'] ) {

			return true;
		} else {


			return sprintf( __( 'Payment check failed: invalid amount received. Expected %1$s %2$s, got %3$s %4$s.', 'crowd-fund' ), $this->ipn_data['mc_gross'], $this->ipn_data['mc_currency'], $body->transactions[0]->amount->total, $body->transactions[0]->amount->currency );
		}
	}

	public function debug_log( $message, $success, $end = false ) {
		CrwdfndLog::log_simple_debug( $message, $success, $end );
	}

}

function crwdfnd_pp_smart_checkout_ajax_hanlder() {


	$uniqid = filter_input( INPUT_POST, 'uniqid', FILTER_SANITIZE_STRING );

	if ( ! check_ajax_referer( 'crwdfnd-pp-smart-checkout-ajax-nonce-' . $uniqid, 'nonce', false ) ) {
		wp_send_json(
			array(
				'success' => false,
				'errMsg'  => __(
					'Nonce check failed. Please reload the page.',
					'crowd-fund'
				),
			)
		);
		exit;
	}

	$data = filter_input( INPUT_POST, 'crwdfnd_pp_smart_checkout_payment_data', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

	if ( empty( $data ) ) {
		wp_send_json(
			array(
				'success' => false,
				'errMsg'  => __(
					'Empty payment data received.',
					'crowd-fund'
				),
			)
		);
	}

	$ipn_handler_instance = new crwdfnd_smart_checkout_ipn_handler();

	$ipn_data_success = $ipn_handler_instance->create_ipn_from_smart_checkout( $data );

	if ( true !== $ipn_data_success ) {

		wp_send_json(
			array(
				'success' => false,
				'errMsg'  => $ipn_data_success,
			)
		);
	}

	$settings      = CrwdfndSettings::get_instance();
	$debug_enabled = $settings->get_value( 'enable-debug' );
	if ( ! empty( $debug_enabled ) ) {
		$debug_log                          = 'log.txt';
		$ipn_handler_instance->ipn_log      = true;
		$ipn_handler_instance->ipn_log_file = $debug_log;
	}

	$sandbox_enabled = $settings->get_value( 'enable-sandbox-testing' );
	if ( ! empty( $sandbox_enabled ) ) {
		$ipn_handler_instance->paypal_url   = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		$ipn_handler_instance->sandbox_mode = true;
	}

	$ip = filter_input( INPUT_SERVER, 'REMOTE_ADDR', FILTER_SANITIZE_STRING );

	$ipn_handler_instance->debug_log( 'Paypal Smart Checkout Class Initiated by ' . $ip, true );

	$res = $ipn_handler_instance->validate_ipn_smart_checkout();

	if ( true !== $res ) {
		wp_send_json(
			array(
				'success' => false,
				'errMsg'  => $res,
			)
		);
	}

	$ipn_handler_instance->debug_log( 'Creating product Information to send.', true );

	if ( ! $ipn_handler_instance->crwdfnd_validate_and_create_membership() ) {
		$ipn_handler_instance->debug_log( 'IPN product validation failed.', false );
		wp_send_json(
			array(
				'success' => false,
				'errMsg'  => __(
					'IPN product validation failed. Check the debug log for more details.',
					'crowd-fund'
				),
			)
		);
	}

	$ipn_handler_instance->debug_log( 'Paypal class finished.', true, true );

	wp_send_json( array( 'success' => true ) );
}
