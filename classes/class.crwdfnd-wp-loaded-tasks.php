<?php

class CrwdfndWpLoadedTasks {

	public function __construct() {

	}

	/*
	 * This is triggered after all plugins, themes and WP has loaded.
	 * It is triggered after init, plugins_loaded etc.
	 */
	public function do_wp_loaded_tasks() {
		$this->synchronise_crwdfnd_logout_for_wp_users();

		//IPN listener
		$this->crwdfnd_ipn_listener();

		//Cancel subscirption action listener
		$cancel_sub_action = filter_input( INPUT_POST, 'crwdfnd_do_cancel_sub', FILTER_SANITIZE_NUMBER_INT );

		if ( ! empty( $cancel_sub_action ) ) {
			$this->do_cancel_sub();
		}

	}

	/*
	 * Logs out the user from the crwdfnd session if they are logged out of the WP user session
	 */
	public function synchronise_crwdfnd_logout_for_wp_users() {
		if ( ! is_user_logged_in() ) {
			/* WP user is logged out. So logout the CRWDFND user (if applicable) */
			if ( CrwdfndMemberUtils::is_member_logged_in() ) {

				//Check if force WP user login sync is enabled or not
				$force_wp_user_sync = CrwdfndSettings::get_instance()->get_value( 'force-wp-user-sync' );
				if ( empty( $force_wp_user_sync ) ) {
					return '';
				}
				/* Force WP user login sync is enabled. */
				/* CRWDFND user is logged in the system. Log him out. */
				CrwdfndLog::log_auth_debug( 'synchronise_crwdfnd_logout_for_wp_users() - Force wp user login sync is enabled. ', true );
				CrwdfndLog::log_auth_debug( 'WP user session is logged out for this user. So logging out of the crwdfnd session also.', true );
				wp_logout();
			}
		}
	}

	/* Payment Gateway IPN listener */

	public function crwdfnd_ipn_listener() {

		//Listen and handle PayPal IPN
		$crwdfnd_process_ipn = filter_input( INPUT_GET, 'crwdfnd_process_ipn' );
		if ( $crwdfnd_process_ipn == '1' ) {
			include CROWDFUND_ME_PATH . 'ipn/crwdfnd_handle_pp_ipn.php';
			exit;
		}

		//Listen and handle Braintree Buy Now IPN
		$crwdfnd_process_braintree_buy_now = filter_input( INPUT_GET, 'crwdfnd_process_braintree_buy_now' );
		if ( $crwdfnd_process_braintree_buy_now == '1' ) {
			include CROWDFUND_ME_PATH . 'ipn/crwdfnd-braintree-buy-now-ipn.php';
			exit;
		}

		if ( wp_doing_ajax() ) {
			//Listen and handle smart paypal checkout IPN
			include CROWDFUND_ME_PATH . 'ipn/crwdfnd-smart-checkout-ipn.php';
			add_action( 'wp_ajax_crwdfnd_process_pp_smart_checkout', 'crwdfnd_pp_smart_checkout_ajax_hanlder' );
			add_action( 'wp_ajax_nopriv_crwdfnd_process_pp_smart_checkout', 'crwdfnd_pp_smart_checkout_ajax_hanlder' );
		}
	}

	private function do_cancel_sub() {

		function msg( $msg, $is_error = true ) {
			echo $msg;
			echo '<br><br>';
			echo CrwdfndUtils::_( 'You will be redirected to the previous page in a few seconds. If not, please <a href="">click here</a>.' );
			echo '<script>function toPrevPage(){window.location = window.location.href;}setTimeout(toPrevPage,5000);</script>';
			if ( ! $is_error ) {
				wp_die( '', CrwdfndUtils::_( 'Success!' ), array( 'response' => 200 ) );
			}
			wp_die();
		}

		$token = filter_input( INPUT_POST, 'crwdfnd_cancel_sub_token', FILTER_SANITIZE_STRING );
		if ( empty( $token ) ) {
			//no token
			msg( CrwdfndUtils::_( 'No token provided.' ) );
		}

		//check nonce
		$nonce = filter_input( INPUT_POST, 'crwdfnd_cancel_sub_nonce', FILTER_SANITIZE_STRING );
		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, $token ) ) {
			//nonce check failed
			msg( CrwdfndUtils::_( 'Nonce check failed.' ) );
		}

		if ( ! CrwdfndMemberUtils::is_member_logged_in() ) {
			//member not logged in
			msg( CrwdfndUtils::_( 'You are not logged in.' ) );
		}

		$member_id = CrwdfndMemberUtils::get_logged_in_members_id();

		$subs = new CRWDFND_Member_Subscriptions( $member_id );

		$sub = $subs->find_by_token( $token );

		if ( empty( $sub ) ) {
			//no subscription found
			return false;
		}

		$res = $subs->cancel( $sub['sub_id'] );

		if ( $res !== true ) {
			msg( $res );
		}

		msg( CrwdfndUtils::_( 'Subscription has been cancelled.' ), false );

	}

}
