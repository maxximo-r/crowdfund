<?php

class CrwdfndMiscUtils {

	public static function create_mandatory_wp_pages() {
		$settings = CrwdfndSettings::get_instance();

		//Create join us page
		$crwdfnd_join_page_content  = '<p style="color:red;font-weight:bold;">This page and the content has been automatically generated for you to give you a basic idea of how a "Join Us" page should look like. You can customize this page however you like it by editing this page from your WordPress page editor.</p>';
		$crwdfnd_join_page_content .= '<p style="font-weight:bold;">If you end up changing the URL of this page then make sure to update the URL value in the settings menu of the plugin.</p>';
		$crwdfnd_join_page_content .= '<p style="border-top:1px solid #ccc;padding-top:10px;margin-top:10px;"></p>
			<strong>Free Membership</strong>
			<br />
			You get unlimited access to free membership content
			<br />
			<em><strong>Price: Free!</strong></em>
			<br /><br />Link the following image to go to the Registration Page if you want your visitors to be able to create a free membership account<br /><br />
			<img title="Join Now" src="' . CROWDFUND_ME_URL . '/images/join-now-button-image.gif" alt="Join Now Button" width="277" height="82" />
			<p style="border-bottom:1px solid #ccc;padding-bottom:10px;margin-bottom:10px;"></p>';
		$crwdfnd_join_page_content .= '<p><strong>You can register for a Free Membership or pay for one of the following membership options</strong></p>';
		$crwdfnd_join_page_content .= '<p style="border-top:1px solid #ccc;padding-top:10px;margin-top:10px;"></p>
			[ ==> Insert Payment Button For Your Paid Membership Levels Here <== ]
			<p style="border-bottom:1px solid #ccc;padding-bottom:10px;margin-bottom:10px;"></p>';

		$crwdfnd_join_page = array(
			'post_title'     => 'Join Us',
			'post_name'      => 'membership-join',
			'post_content'   => $crwdfnd_join_page_content,
			'post_parent'    => 0,
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		);

		$join_page_obj = get_page_by_path( 'membership-join' );
		if ( ! $join_page_obj ) {
			$join_page_id = wp_insert_post( $crwdfnd_join_page );
		} else {
			$join_page_id = $join_page_obj->ID;
			if ( $join_page_obj->post_status == 'trash' ) { //For cases where page may be in trash, bring it out of trash
				wp_update_post(
					array(
						'ID'          => $join_page_obj->ID,
						'post_status' => 'publish',
					)
				);
			}
		}
		$crwdfnd_join_page_permalink = get_permalink( $join_page_id );
		$settings->set_value( 'join-us-page-url', $crwdfnd_join_page_permalink );

		//Create registration page
		$crwdfnd_rego_page = array(
			'post_title'     => CrwdfndUtils::_( 'Registration' ),
			'post_name'      => 'membership-registration',
			'post_content'   => '[crwdfnd_registration_form]',
			'post_parent'    => $join_page_id,
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		);
		$rego_page_obj  = get_page_by_path( 'membership-registration' );
		if ( ! $rego_page_obj ) {
			$rego_page_id = wp_insert_post( $crwdfnd_rego_page );
		} else {
			$rego_page_id = $rego_page_obj->ID;
			if ( $rego_page_obj->post_status == 'trash' ) { //For cases where page may be in trash, bring it out of trash
				wp_update_post(
					array(
						'ID'          => $rego_page_obj->ID,
						'post_status' => 'publish',
					)
				);
			}
		}
		$crwdfnd_rego_page_permalink = get_permalink( $rego_page_id );
		$settings->set_value( 'registration-page-url', $crwdfnd_rego_page_permalink );

		//Create login page
		$crwdfnd_login_page = array(
			'post_title'     => CrwdfndUtils::_( 'Member Login' ),
			'post_name'      => 'membership-login',
			'post_content'   => '[crwdfnd_login_form]',
			'post_parent'    => 0,
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		);
		$login_page_obj  = get_page_by_path( 'membership-login' );
		if ( ! $login_page_obj ) {
			$login_page_id = wp_insert_post( $crwdfnd_login_page );
		} else {
			$login_page_id = $login_page_obj->ID;
			if ( $login_page_obj->post_status == 'trash' ) { //For cases where page may be in trash, bring it out of trash
				wp_update_post(
					array(
						'ID'          => $login_page_obj->ID,
						'post_status' => 'publish',
					)
				);
			}
		}
		$crwdfnd_login_page_permalink = get_permalink( $login_page_id );
		$settings->set_value( 'login-page-url', $crwdfnd_login_page_permalink );

		//Create profile page
		$crwdfnd_profile_page = array(
			'post_title'     => CrwdfndUtils::_( 'Profile' ),
			'post_name'      => 'membership-profile',
			'post_content'   => '[crwdfnd_profile_form]',
			'post_parent'    => $login_page_id,
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		);
		$profile_page_obj  = get_page_by_path( 'membership-profile' );
		if ( ! $profile_page_obj ) {
			$profile_page_id = wp_insert_post( $crwdfnd_profile_page );
		} else {
			$profile_page_id = $profile_page_obj->ID;
			if ( $profile_page_obj->post_status == 'trash' ) { //For cases where page may be in trash, bring it out of trash
				wp_update_post(
					array(
						'ID'          => $profile_page_obj->ID,
						'post_status' => 'publish',
					)
				);
			}
		}
		$crwdfnd_profile_page_permalink = get_permalink( $profile_page_id );
		$settings->set_value( 'profile-page-url', $crwdfnd_profile_page_permalink );

		//Create reset page
		$crwdfnd_reset_page = array(
			'post_title'     => CrwdfndUtils::_( 'Password Reset' ),
			'post_name'      => 'password-reset',
			'post_content'   => '[crwdfnd_reset_form]',
			'post_parent'    => $login_page_id,
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		);
		$reset_page_obj  = get_page_by_path( 'password-reset' );
		if ( ! $profile_page_obj ) {
			$reset_page_id = wp_insert_post( $crwdfnd_reset_page );
		} else {
			$reset_page_id = $reset_page_obj->ID;
			if ( $reset_page_obj->post_status == 'trash' ) { //For cases where page may be in trash, bring it out of trash
				wp_update_post(
					array(
						'ID'          => $reset_page_obj->ID,
						'post_status' => 'publish',
					)
				);
			}
		}
		$crwdfnd_reset_page_permalink = get_permalink( $reset_page_id );
		$settings->set_value( 'reset-page-url', $crwdfnd_reset_page_permalink );

		$settings->save(); //Save all settings object changes
	}

	public static function redirect_to_url( $url ) {
		if ( empty( $url ) ) {
			return;
		}
		$url = apply_filters( 'crwdfnd_redirect_to_url', $url );

		if ( ! preg_match( '/http/', $url ) ) {//URL value is incorrect
			echo '<p>Error! The URL value you entered in the plugin configuration is incorrect.</p>';
			echo '<p>A URL must always have the "http" keyword in it.</p>';
			echo '<p style="font-weight: bold;">The URL value you currently configured is: <br />' . $url . '</p>';
			echo '<p>Here are some examples of correctly formatted URL values for your reference: <br />http://www.example.com<br/>http://example.com<br />https://www.example.com</p>';
			echo '<p>Find the field where you entered this incorrect URL value and correct the mistake then try again.</p>';
			exit;
		}
		if ( ! headers_sent() ) {
			header( 'Location: ' . $url );
		} else {
			echo '<meta http-equiv="refresh" content="0;url=' . $url . '" />';
		}
		exit;
	}

	public static function show_temporary_message_then_redirect( $msg, $redirect_url, $timeout = 5 ) {
		$timeout       = absint( $timeout );
		$redirect_html = sprintf( '<meta http-equiv="refresh" content="%d; url=\'%s\'" />', $timeout, $redirect_url );
		$redir_msg     = CrwdfndUtils::_( 'You will be automatically redirected in a few seconds. If not, please %s.' );
		$redir_msg     = sprintf( $redir_msg, '<a href="' . $redirect_url . '">' . CrwdfndUtils::_( 'click here' ) . '</a>' );

		$msg   = $msg . '<br/><br/>' . $redir_msg . $redirect_html;
		$title = CrwdfndUtils::_( 'Action Status' );
		wp_die( $msg, $title );
	}

	public static function get_current_page_url() {
		$pageURL = 'http';

                if ( isset( $_SERVER['SCRIPT_URI'] ) && ! empty( $_SERVER['SCRIPT_URI'] ) ) {
			$pageURL = $_SERVER['SCRIPT_URI'];
                        $pageURL = str_replace(':443', '', $pageURL);//remove any port number from the URL value (some hosts include the port number with this).
			$pageURL = apply_filters( 'crwdfnd_get_current_page_url_filter', $pageURL );
			return $pageURL;
		}

		if ( isset( $_SERVER['HTTPS'] ) && ( $_SERVER['HTTPS'] == 'on' ) ) {
			$pageURL .= 's';
		}
		$pageURL .= '://';
		if ( isset( $_SERVER['SERVER_PORT'] ) && ( $_SERVER['SERVER_PORT'] != '80' ) && ( $_SERVER['SERVER_PORT'] != '443' ) ) {
			$pageURL .= ltrim( $_SERVER['SERVER_NAME'], '.*' ) . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
		} else {
			$pageURL .= ltrim( $_SERVER['SERVER_NAME'], '.*' ) . $_SERVER['REQUEST_URI'];
		}

		$pageURL = apply_filters( 'crwdfnd_get_current_page_url_filter', $pageURL );

		return $pageURL;
	}

	/*
	 * This is an alternative to the get_current_page_url() function. It needs to be tested on many different server conditions before it can be utilized
	 */
	public static function get_current_page_url_alt() {
		$url_parts          = array();
		$url_parts['proto'] = 'http';

		if ( isset( $_SERVER['SCRIPT_URI'] ) && ! empty( $_SERVER['SCRIPT_URI'] ) ) {
			return $_SERVER['SCRIPT_URI'];
		}

		if ( isset( $_SERVER['HTTPS'] ) && ( $_SERVER['HTTPS'] == 'on' ) ) {
			$url_parts['proto'] = 'https';
		}

		$url_parts['port'] = '';
		if ( isset( $_SERVER['SERVER_PORT'] ) && ( $_SERVER['SERVER_PORT'] != '80' ) && ( $_SERVER['SERVER_PORT'] != '443' ) ) {
			$url_parts['port'] = $_SERVER['SERVER_PORT'];
		}

		$url_parts['domain'] = ltrim( $_SERVER['SERVER_NAME'], '.*' );
		$url_parts['uri']    = $_SERVER['REQUEST_URI'];

		$url_parts = apply_filters( 'crwdfnd_get_current_page_url_alt_filter', $url_parts );

		$pageURL = sprintf( '%s://%s%s%s', $url_parts['proto'], $url_parts['domain'], ! empty( $url_parts['port'] ) ? ':' . $url_parts['port'] : '', $url_parts['uri'] );

		return $pageURL;
	}

	/*
	 * Returns just the domain name. Something like example.com
	 */

	public static function get_home_url_without_http_and_www() {
		$site_url = get_site_url();
		$parse    = parse_url( $site_url );
		$site_url = $parse['host'];
		$site_url = str_replace( 'https://', '', $site_url );
		$site_url = str_replace( 'http://', '', $site_url );
		if ( preg_match( '/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $site_url, $regs ) ) {
			$site_url = $regs['domain'];
		}
		return $site_url;
	}

	public static function replace_dynamic_tags( $msg_body, $member_id, $additional_args = '' ) {
		$settings    = CrwdfndSettings::get_instance();
		$user_record = CrwdfndMemberUtils::get_user_by_id( $member_id );

		$password = '';
		$reg_link = '';
		if ( ! empty( $additional_args ) ) {
			$password = isset( $additional_args['password'] ) ? $additional_args['password'] : $password;
			$reg_link = isset( $additional_args['reg_link'] ) ? $additional_args['reg_link'] : $reg_link;
		}
		$login_link = $settings->get_value( 'login-page-url' );

		//Construct the primary address value
		$primary_address = '';
		if ( ! empty( $user_record->address_street ) && ! empty( $user_record->address_city ) ) {
			//An address value is present.
			$primary_address .= $user_record->address_street;
			$primary_address .= "\n" . $user_record->address_city;
			if ( ! empty( $user_record->address_state ) ) {
				$primary_address .= ' ' . $user_record->address_state;
			}
			if ( ! empty( $user_record->address_zipcode ) ) {
				$primary_address .= ' ' . $user_record->address_zipcode;
			}
			if ( ! empty( $user_record->country ) ) {
				$primary_address .= "\n" . $user_record->country;
			}
		}

		$membership_level_name = CrwdfndMembershipLevelUtils::get_membership_level_name_of_a_member( $member_id );
		//Format some field values
		$member_since_formatted = CrwdfndUtils::get_formatted_date_according_to_wp_settings( $user_record->member_since );
		$subsc_starts_formatted = CrwdfndUtils::get_formatted_date_according_to_wp_settings( $user_record->subscription_starts );

		//Define the replacable tags
		$tags = array(
			'{member_id}',
			'{user_name}',
			'{first_name}',
			'{last_name}',
			'{membership_level}',
			'{membership_level_name}',
			'{account_state}',
			'{email}',
			'{phone}',
			'{member_since}',
			'{subscription_starts}',
			'{company_name}',
			'{password}',
			'{login_link}',
			'{reg_link}',
			'{primary_address}',
		);

		//Define the values
		$vals = array(
			$member_id,
			$user_record->user_name,
			$user_record->first_name,
			$user_record->last_name,
			$user_record->membership_level,
			$membership_level_name,
			$user_record->account_state,
			$user_record->email,
			$user_record->phone,
			$member_since_formatted,
			$subsc_starts_formatted,
			$user_record->company_name,
			$password,
			$login_link,
			$reg_link,
			$primary_address,
		);

		$msg_body = str_replace( $tags, $vals, $msg_body );
		return $msg_body;
	}

	public static function get_login_link() {
		$login_url  = CrwdfndSettings::get_instance()->get_value( 'login-page-url' );
		$joinus_url = CrwdfndSettings::get_instance()->get_value( 'join-us-page-url' );
		if ( empty( $login_url ) || empty( $joinus_url ) ) {
			return '<span style="color:red;">CrowdFund Me is not configured correctly. The login page or the join us page URL is missing in the settings configuration. '
					. 'Please contact <a href="mailto:' . get_option( 'admin_email' ) . '">Admin</a>';
		}

		//Create the login/protection message
		$filtered_login_url = apply_filters( 'crwdfnd_get_login_link_url', $login_url ); //Addons can override the login URL value using this filter.
		$login_msg          = '';
		$login_msg         .= CrwdfndUtils::_( 'Please' ) . ' <a class="crwdfnd-login-link" href="' . $filtered_login_url . '">' . CrwdfndUtils::_( 'Login' ) . '</a>. ';
		$login_msg         .= CrwdfndUtils::_( 'Not a Member?' ) . ' <a href="' . $joinus_url . '">' . CrwdfndUtils::_( 'Join Us' ) . '</a>';

		return $login_msg;
	}

	public static function get_renewal_link() {
		$renewal = CrwdfndSettings::get_instance()->get_value( 'renewal-page-url' );
		if ( empty( $renewal ) ) {
			//No renewal page is configured so don't show any renewal page link. It is okay to have no renewal page configured.
			return '';
		}
		return CrwdfndUtils::_( 'Please' ) . ' <a class="crwdfnd-renewal-link" href="' . $renewal . '">' . CrwdfndUtils::_( 'renew' ) . '</a> ' . CrwdfndUtils::_( ' your account to gain access to this content.' );
	}

	public static function compare_url( $url1, $url2 ) {
		$url1 = trailingslashit( strtolower( $url1 ) );
		$url2 = trailingslashit( strtolower( $url2 ) );
		if ( $url1 == $url2 ) {
			return true;
		}

		$url1 = parse_url( $url1 );
		$url2 = parse_url( $url2 );

		$components = array( 'scheme', 'host', 'port', 'path' );

		foreach ( $components as $key => $value ) {
			if ( ! isset( $url1[ $value ] ) && ! isset( $url2[ $value ] ) ) {
				continue;
			}

			if ( ! isset( $url2[ $value ] ) ) {
				return false;
			}
			if ( ! isset( $url1[ $value ] ) ) {
				return false;
			}

			if ( $url1[ $value ] != $url2[ $value ] ) {
				return false;
			}
		}

		if ( ! isset( $url1['query'] ) && ! isset( $url2['query'] ) ) {
			return true;
		}

		if ( ! isset( $url2['query'] ) ) {
			return false;
		}
		if ( ! isset( $url1['query'] ) ) {
			return false;
		}

		return strpos( $url1['query'], $url2['query'] ) || strpos( $url2['query'], $url1['query'] );
	}

	public static function is_crwdfnd_admin_page() {
		if ( isset( $_GET['page'] ) && ( stripos( $_GET['page'], 'crowdfund_me' ) !== false ) ) {
			//This is an admin page of the CRWDFND plugin
			return true;
		}
		return false;
	}

	public static function check_user_permission_and_is_admin( $action_name ) {
		//Check we are on the admin end
		if ( ! is_admin() ) {
			//Error! This is not on the admin end. This can only be done from the admin side
			wp_die( CrwdfndUtils::_( 'Error! This action (' . $action_name . ') can only be done from admin end.' ) );
		}

		//Check user has management permission
		if ( ! current_user_can( CRWDFND_MANAGEMENT_PERMISSION ) ) {
			//Error! Only management users can do this
			wp_die( CrwdfndUtils::_( 'Error! This action (' . $action_name . ') can only be done by an user with management permission.' ) );
		}
	}

	public static function format_raw_content_for_front_end_display( $raw_content ) {
		$formatted_content = wptexturize( $raw_content );
		$formatted_content = convert_smilies( $formatted_content );
		$formatted_content = convert_chars( $formatted_content );
		$formatted_content = wpautop( $formatted_content );
		$formatted_content = shortcode_unautop( $formatted_content );
		$formatted_content = prepend_attachment( $formatted_content );
		$formatted_content = capital_P_dangit( $formatted_content );
		$formatted_content = do_shortcode( $formatted_content );
                $formatted_content = do_blocks( $formatted_content );

                $formatted_content = apply_filters('crwdfnd_format_raw_content_for_front_end_display', $formatted_content);

		return $formatted_content;
	}

	public static function get_countries_dropdown( $country = '' ) {
		$countries = array(
			'Afghanistan',
			'Albania',
			'Algeria',
			'Andorra',
			'Angola',
			'Antigua and Barbuda',
			'Argentina',
			'Armenia',
			'Aruba',
			'Australia',
			'Austria',
			'Azerbaijan',
			'Bahamas',
			'Bahrain',
			'Bangladesh',
			'Barbados',
			'Belarus',
			'Belgium',
			'Belize',
			'Benin',
			'Bhutan',
			'Bolivia',
			'Bonaire',
			'Bosnia and Herzegovina',
			'Botswana',
			'Brazil',
			'Brunei',
			'Bulgaria',
			'Burkina Faso',
			'Burundi',
			'Cambodia',
			'Cameroon',
			'Canada',
			'Cape Verde',
			'Central African Republic',
			'Chad',
			'Chile',
			'China',
			'Colombia',
			'Comoros',
			'Congo (Brazzaville)',
			'Congo',
			'Costa Rica',
			"Cote d\'Ivoire",
			'Croatia',
			'Cuba',
			'Curacao',
			'Cyprus',
			'Czech Republic',
			'Denmark',
			'Djibouti',
			'Dominica',
			'Dominican Republic',
			'East Timor (Timor Timur)',
			'Ecuador',
			'Egypt',
			'El Salvador',
			'Equatorial Guinea',
			'Eritrea',
			'Estonia',
                        'Eswatini',
			'Ethiopia',
			'Fiji',
			'Finland',
			'France',
                        'French Polynesia',
			'Gabon',
			'Gambia, The',
			'Georgia',
			'Germany',
			'Ghana',
			'Greece',
			'Grenada',
			'Guatemala',
			'Guinea',
			'Guinea-Bissau',
			'Guyana',
			'Haiti',
			'Honduras',
			'Hong Kong',
			'Hungary',
			'Iceland',
			'India',
			'Indonesia',
			'Iran',
			'Iraq',
			'Ireland',
			'Israel',
			'Italy',
			'Jamaica',
			'Japan',
			'Jordan',
			'Kazakhstan',
			'Kenya',
			'Kiribati',
			'Korea, North',
			'Korea, South',
			'Kuwait',
			'Kyrgyzstan',
			'Laos',
			'Latvia',
			'Lebanon',
			'Lesotho',
			'Liberia',
			'Libya',
			'Liechtenstein',
			'Lithuania',
			'Luxembourg',
			'Macedonia',
			'Madagascar',
			'Malawi',
			'Malaysia',
			'Maldives',
			'Mali',
			'Malta',
			'Marshall Islands',
			'Mauritania',
			'Mauritius',
			'Mexico',
			'Micronesia',
			'Moldova',
			'Monaco',
			'Mongolia',
			'Montenegro',
			'Morocco',
			'Mozambique',
			'Myanmar',
			'Namibia',
			'Nauru',
			'Nepa',
			'Netherlands',
			'New Zealand',
			'Nicaragua',
			'Niger',
			'Nigeria',
			'Norway',
			'Oman',
			'Pakistan',
			'Palau',
                        'Palestine',
			'Panama',
			'Papua New Guinea',
			'Paraguay',
			'Peru',
			'Philippines',
			'Poland',
			'Portugal',
			'Qatar',
			'Romania',
			'Russia',
			'Rwanda',
			'Saint Kitts and Nevis',
			'Saint Lucia',
			'Saint Vincent',
			'Samoa',
			'San Marino',
			'Sao Tome and Principe',
			'Saudi Arabia',
			'Senegal',
			'Serbia',
			'Seychelles',
			'Sierra Leone',
			'Singapore',
			'Slovakia',
			'Slovenia',
			'Solomon Islands',
			'Somalia',
			'South Africa',
			'Spain',
			'Sri Lanka',
			'Sudan',
			'Suriname',
			'Swaziland',
			'Sweden',
			'Switzerland',
			'Syria',
			'Taiwan',
			'Tajikistan',
			'Tanzania',
			'Thailand',
			'Togo',
			'Tonga',
			'Trinidad and Tobago',
			'Tunisia',
			'Turkey',
			'Turkmenistan',
			'Tuvalu',
			'Uganda',
			'Ukraine',
			'United Arab Emirates',
			'United Kingdom',
			'United States of America',
			'Uruguay',
			'Uzbekistan',
			'Vanuatu',
			'Vatican City',
			'Venezuela',
			'Vietnam',
			'Yemen',
			'Zambia',
			'Zimbabwe',
		);
		//let's try to "guess" country name
		$curr_lev      = -1;
		$guess_country = '';
		foreach ( $countries as $country_name ) {
			similar_text( strtolower( $country ), strtolower( $country_name ), $lev );
			if ( $lev >= $curr_lev ) {
				//this is closest match so far
				$curr_lev      = $lev;
				$guess_country = $country_name;
			}
			if ( $curr_lev == 100 ) {
				//exact match
				break;
			}
		}
		if ( $curr_lev <= 80 ) {
			// probably bad guess
			$guess_country = '';
		}
		$countries_dropdown = '';
		//let's add "(Please select)" option
		$countries_dropdown .= "\r\n" . '<option value=""' . ( $country == '' ? ' selected' : '' ) . '>' . CrwdfndUtils::_( '(Please Select)' ) . '</option>';
		if ( $guess_country == '' && $country != '' ) {
			//since we haven't guessed the country name, let's add current value to the options
			$countries_dropdown .= "\r\n" . '<option value="' . $country . '" selected>' . $country . '</option>';
		}
		if ( $guess_country != '' ) {
			$country = $guess_country;
		}
		foreach ( $countries as $country_name ) {
			$countries_dropdown .= "\r\n" . '<option value="' . $country_name . '"' . ( strtolower( $country_name ) == strtolower( $country ) ? ' selected' : '' ) . '>' . $country_name . '</option>';
		}
		return $countries_dropdown;
	}

	public static function get_button_type_name( $button_type ) {
		$btnTypesNames = array(
			'pp_buy_now'              => CrwdfndUtils::_( 'PayPal Buy Now' ),
			'pp_subscription'         => CrwdfndUtils::_( 'PayPal Subscription' ),
			'pp_smart_checkout'       => CrwdfndUtils::_( 'PayPal Smart Checkout' ),
			'braintree_buy_now'       => CrwdfndUtils::_( 'Braintree Buy Now' ),
		);

		$button_type_name = $button_type;

		if ( array_key_exists( $button_type, $btnTypesNames ) ) {
			$button_type_name = $btnTypesNames[ $button_type ];
		}

		return $button_type_name;
	}

	public static function format_money( $amount, $currency = false ) {
		$formatted = number_format( $amount, 2 );
		if ( $currency ) {
			$formatted .= ' ' . $currency;
		}
		return $formatted;
	}


	public static function mail( $email, $subject, $email_body, $headers ) {
		$settings     = CrwdfndSettings::get_instance();
		$html_enabled = $settings->get_value( 'email-enable-html' );
		if ( ! empty( $html_enabled ) ) {
			$headers   .= "Content-Type: text/html; charset=UTF-8\r\n";
			$email_body = nl2br( $email_body );
		}
		wp_mail( $email, $subject, $email_body, $headers );
	}


}
