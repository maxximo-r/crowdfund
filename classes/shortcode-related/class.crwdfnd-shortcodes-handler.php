<?php

class CrwdfndShortcodesHandler {

	public function __construct() {
		//Register all the shortcodes here
		add_shortcode( 'crwdfnd_payment_button', array( &$this, 'crwdfnd_payment_button_sc' ) );
		add_shortcode( 'crwdfnd_thank_you_page_registration', array( &$this, 'crwdfnd_ty_page_rego_sc' ) );

		add_shortcode( 'crwdfnd_show_expiry_date', array( &$this, 'crwdfnd_show_expiry_date_sc' ) );

		add_shortcode( 'crwdfnd_mini_login', array( &$this, 'crwdfnd_show_mini_login_sc' ) );

		add_shortcode( 'crwdfnd_paypal_subscription_cancel_link', array( &$this, 'crwdfnd_pp_cancel_subs_link_sc' ) );
	}

	public function crwdfnd_payment_button_sc( $args ) {
		extract(
			shortcode_atts(
				array(
					'id'          => '',
					'button_text' => '',
					'new_window'  => '',
					'class'       => '',
				),
				$args
			)
		);

		if ( empty( $id ) ) {
			return '<p class="crwdfnd-red-box">Error! You must specify a button ID with this shortcode. Check the usage documentation.</p>';
		}

		$button_id = $id;
		//$button = get_post($button_id); //Retrieve the CPT for this button
		$button_type = get_post_meta( $button_id, 'button_type', true );
		if ( empty( $button_type ) ) {
			$error_msg  = '<p class="crwdfnd-red-box">';
			$error_msg .= 'Error! The button ID (' . $button_id . ') you specified in the shortcode does not exist. You may have deleted this payment button. ';
			$error_msg .= 'Go to the Manage Payment Buttons interface then copy and paste the correct button ID in the shortcode.';
			$error_msg .= '</p>';
			return $error_msg;
		}

		include_once( CROWDFUND_ME_PATH . 'views/payments/payment-gateway/paypal_button_shortcode_view.php' );
		include_once( CROWDFUND_ME_PATH . 'views/payments/payment-gateway/braintree_button_shortcode_view.php' );
		include_once( CROWDFUND_ME_PATH . 'views/payments/payment-gateway/paypal_smart_checkout_button_shortcode_view.php' );

		$button_code = '';
		$button_code = apply_filters( 'crwdfnd_payment_button_shortcode_for_' . $button_type, $button_code, $args );

		$output  = '';
		$output .= '<div class="crwdfnd-payment-button">' . $button_code . '</div>';

		return $output;
	}

	public function crwdfnd_ty_page_rego_sc( $args ) {
		$output   = '';
		$settings = CrwdfndSettings::get_instance();

		//If user is logged in then the purchase will be applied to the existing profile
		if ( CrwdfndMemberUtils::is_member_logged_in() ) {
			$username = CrwdfndMemberUtils::get_logged_in_members_username();
			$output  .= '<div class="crwdfnd-ty-page-registration-logged-in crwdfnd-yellow-box">';
			$output  .= '<p>' . CrwdfndUtils::_( 'Your membership profile will be updated to reflect the payment.' ) . '</p>';
			$output  .= CrwdfndUtils::_( 'Your profile username: ' ) . $username;
			$output  .= '</div>';
			return $output;
		}

		$output     .= '<div class="crwdfnd-ty-page-registration">';
		$member_data = CrwdfndUtils::get_incomplete_paid_member_info_by_ip();
		if ( $member_data ) {
			//Found a member profile record for this IP that needs to be completed
			$reg_page_url      = $settings->get_value( 'registration-page-url' );
			$rego_complete_url = add_query_arg(
				array(
					'member_id' => $member_data->member_id,
					'code'      => $member_data->reg_code,
				),
				$reg_page_url
			);
			$output           .= '<div class="crwdfnd-ty-page-registration-link crwdfnd-yellow-box">';
			$output           .= '<p>' . CrwdfndUtils::_( 'Click on the following link to complete the registration.' ) . '</p>';
			$output           .= '<p><a href="' . $rego_complete_url . '">' . CrwdfndUtils::_( 'Click here to complete your paid registration' ) . '</a></p>';
			$output           .= '</div>';
		} else {
			//Nothing found. Check again later.
			$output .= '<div class="crwdfnd-ty-page-registration-link crwdfnd-yellow-box">';
			$output .= CrwdfndUtils::_( 'If you have just made a membership payment then your payment is yet to be processed. Please check back in a few minutes. An email will be sent to you with the details shortly.' );
			$output .= '</div>';
		}

		$output .= '</div>'; //end of .crwdfnd-ty-page-registration

		return $output;
	}

	public function crwdfnd_show_expiry_date_sc( $args ) {
		$output = '<div class="crwdfnd-show-expiry-date">';
		if ( CrwdfndMemberUtils::is_member_logged_in() ) {
			$auth        = CrwdfndAuth::get_instance();
			$expiry_date = $auth->get_expire_date();
			$output     .= CrwdfndUtils::_( 'Expiry: ' ) . $expiry_date;
		} else {
			$output .= CrwdfndUtils::_( 'You are not logged-in as a member' );
		}
		$output .= '</div>';
		return $output;
	}

	public function crwdfnd_show_mini_login_sc( $args ) {

		$login_page_url   = CrwdfndSettings::get_instance()->get_value( 'login-page-url' );
		$join_page_url    = CrwdfndSettings::get_instance()->get_value( 'join-us-page-url' );
		$profile_page_url = CrwdfndSettings::get_instance()->get_value( 'profile-page-url' );
		$logout_url       = CROWDFUND_ME_SITE_HOME_URL . '?crwdfnd-logout=true';

		$filtered_login_url = apply_filters( 'crwdfnd_get_login_link_url', $login_page_url ); //Addons can override the login URL value using this filter.

		$output = '<div class="crwdfnd_mini_login_wrapper">';

		//Check if the user is logged in or not
		$auth = CrwdfndAuth::get_instance();
		if ( $auth->is_logged_in() ) {
			//User is logged in
			$username = $auth->get( 'user_name' );
			$output  .= '<span class="crwdfnd_mini_login_label">' . CrwdfndUtils::_( 'Logged in as: ' ) . '</span>';
			$output  .= '<span class="crwdfnd_mini_login_username">' . $username . '</span>';
			$output  .= '<span class="crwdfnd_mini_login_profile"> | <a href="' . $profile_page_url . '">' . CrwdfndUtils::_( 'Profile' ) . '</a></span>';
			$output  .= '<span class="crwdfnd_mini_login_logout"> | <a href="' . $logout_url . '">' . CrwdfndUtils::_( 'Logout' ) . '</a></span>';
		} else {
			//User not logged in.
			$output .= '<span class="crwdfnd_mini_login_login_here"><a href="' . $filtered_login_url . '">' . CrwdfndUtils::_( 'Login Here' ) . '</a></span>';
			$output .= '<span class="crwdfnd_mini_login_no_membership"> | ' . CrwdfndUtils::_( 'Not a member? ' ) . '</span>';
			$output .= '<span class="crwdfnd_mini_login_join_now"><a href="' . $join_page_url . '">' . CrwdfndUtils::_( 'Join Now' ) . '</a></span>';
		}

		$output .= '</div>';

		$output = apply_filters( 'crwdfnd_mini_login_output', $output );

		return $output;
	}

	public function crwdfnd_pp_cancel_subs_link_sc( $args ) {
                //Shortcode parameters: ['anchor_text'], ['merchant_id']

		extract(
			shortcode_atts(
				array(
					'merchant_id' => '',
					'anchor_text' => '',
                                        'new_window' => '',
                                        'css_class' => '',
				),
				$args
			)
		);

		if ( empty( $merchant_id ) ) {
			return '<p class="crwdfnd-red-box">Error! You need to specify your secure PayPal merchant ID in the shortcode using the "merchant_id" parameter.</p>';
		}

		$output   = '';
		$settings = CrwdfndSettings::get_instance();

		//Check if the member is logged-in
		if ( CrwdfndMemberUtils::is_member_logged_in() ) {
			$user_id = CrwdfndMemberUtils::get_logged_in_members_id();
		}

		if ( ! empty( $user_id ) ) {
			//The user is logged-in

                        //Set the default window target (if it is set via the shortcode).
                        if ( empty( $new_window ) ) {
                            $window_target = '';
                        } else {
                            $window_target = ' target="_blank"';
                        }

                        //Set the CSS class (if it is set via the shortcode).
                        if ( empty( $css_class ) ) {
                            $link_css_class = '';
                        } else {
                            $link_css_class = ' class="' . $css_class . '"';
                        }

			//Set the default anchor text (if one is provided via the shortcode).
			if ( empty( $anchor_text ) ) {
				$anchor_text = CrwdfndUtils::_( 'Unsubscribe from PayPal' );
			}

			$output .= '<div class="crwdfnd-paypal-subscription-cancel-link">';
			$sandbox_enabled = $settings->get_value( 'enable-sandbox-testing' );
			if ( $sandbox_enabled ) {
				//Sandbox mode
				$output .= '<a href="https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_subscr-find&alias=' . $merchant_id . '" _fcksavedurl="https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_subscr-find&alias=' . $merchant_id . '" '. $window_target . $link_css_class .'>';
				$output .= $anchor_text;
				$output .= '</a>';
			} else {
				//Live mode
				$output .= '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_subscr-find&alias=' . $merchant_id . '" _fcksavedurl="https://www.paypal.com/cgi-bin/webscr?cmd=_subscr-find&alias=' . $merchant_id . '" '.$window_target . $link_css_class .'>';
				$output .= $anchor_text;
				$output .= '</a>';
			}
			$output .= '</div>';

		} else {
			//The user is NOT logged-in
			$output .= '<p>' . CrwdfndUtils::_( 'You are not logged-in as a member' ) . '</p>';
		}
		return $output;
	}
}
