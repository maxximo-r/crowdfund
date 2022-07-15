<?php

class CrwdfndInitTimeTasks {

	public function __construct() {

	}

	public function do_init_tasks() {

		//Set up localisation. First loaded ones will override strings present in later loaded file.
		//Allows users to have a customized language in a different folder.
		$locale = apply_filters( 'plugin_locale', get_locale(), 'crowd-fund' );
		load_textdomain( 'crowd-fund', WP_LANG_DIR . "/crowd-fund-$locale.mo" );
		load_plugin_textdomain( 'crowd-fund', false, CROWDFUND_ME_DIRNAME . '/languages/' );

		if ( ! isset( $_COOKIE['crwdfnd_session'] ) ) { // give a unique ID to current session.
			$uid                     = md5( microtime() );
			$_COOKIE['crwdfnd_session'] = $uid; // fake it for current session/
			if ( ! headers_sent() ) {
				setcookie( 'crwdfnd_session', $uid, 0, '/' );
			}
		}

		//Crete the custom post types
		$this->create_post_type();

		//Do frontend-only init time tasks
		if ( ! is_admin() ) {
			CrwdfndAuth::get_instance();

			$this->check_and_handle_auto_login();
			$this->verify_and_delete_account();

			$crwdfnd_logout = filter_input( INPUT_GET, 'crwdfnd-logout' );
			if ( ! empty( $crwdfnd_logout ) ) {
				CrwdfndAuth::get_instance()->logout();
				$redirect_url = apply_filters( 'crwdfnd_after_logout_redirect_url', CROWDFUND_ME_SITE_HOME_URL );
				wp_redirect( trailingslashit( $redirect_url ) );
				exit( 0 );
			}
			$this->process_password_reset();
			$this->register_member();
			$this->check_and_do_email_activation();
			$this->edit_profile();
			CrwdfndCommentFormRelated::check_and_restrict_comment_posting_to_members();
		} else {
			//Do admin side init time tasks
			if ( current_user_can( CRWDFND_MANAGEMENT_PERMISSION ) ) {
				//Admin dashboard side stuff
				$this->admin_init();
			}
		}
	}

	public function admin_init() {
		$createcrwdfnduser = filter_input( INPUT_POST, 'createcrwdfnduser' );
		if ( ! empty( $createcrwdfnduser ) ) {
			CrwdfndAdminRegistration::get_instance()->register_admin_end();
		}
		$editcrwdfnduser = filter_input( INPUT_POST, 'editcrwdfnduser' );
		if ( ! empty( $editcrwdfnduser ) ) {
			$id = filter_input( INPUT_GET, 'member_id', FILTER_VALIDATE_INT );
			CrwdfndAdminRegistration::get_instance()->edit_admin_end( $id );
		}
		$createcrwdfndlevel = filter_input( INPUT_POST, 'createcrwdfndlevel' );
		if ( ! empty( $createcrwdfndlevel ) ) {
			CrwdfndMembershipLevel::get_instance()->create_level();
		}
		$editcrwdfndlevel = filter_input( INPUT_POST, 'editcrwdfndlevel' );
		if ( ! empty( $editcrwdfndlevel ) ) {
			$id = filter_input( INPUT_GET, 'id' );
			CrwdfndMembershipLevel::get_instance()->edit_level( $id );
		}
		$update_category_list = filter_input( INPUT_POST, 'update_category_list' );
		if ( ! empty( $update_category_list ) ) {
			include_once 'class.crwdfnd-category-list.php';
			CrwdfndCategoryList::update_category_list();
		}
		$update_post_list = filter_input( INPUT_POST, 'update_post_list' );
		if ( ! empty( $update_post_list ) ) {
			include_once 'class.crwdfnd-post-list.php';
			CrwdfndPostList::update_post_list();
		}
	}

	public function create_post_type() {
		//The payment button data for membership levels will be stored using this CPT
		register_post_type(
			'crwdfnd_payment_button',
			array(
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => false,
				'query_var'          => false,
				'rewrite'            => false,
				'capability_type'    => 'page',
				'has_archive'        => false,
				'hierarchical'       => false,
				'supports'           => array( 'title', 'editor' ),
			)
		);

		//Transactions will be stored using this CPT in parallel with crwdfnd_payments_tbl DB table
		$args = array(
			'supports'            => array( '' ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => false,
			'can_export'          => false,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'post',
		);
		register_post_type( 'crwdfnd_transactions', $args );
	}

	private function verify_and_delete_account() {
		include_once CROWDFUND_ME_PATH . 'classes/class.crwdfnd-members.php';
		$delete_account = filter_input( INPUT_GET, 'crwdfnd_delete_account' );
		if ( empty( $delete_account ) ) {
			return;
		}
		$password = filter_input( INPUT_POST, 'account_delete_confirm_pass', FILTER_UNSAFE_RAW );

		$auth = CrwdfndAuth::get_instance();
		if ( ! $auth->is_logged_in() ) {
			return;
		}
		if ( empty( $password ) ) {
			CrwdfndUtils::account_delete_confirmation_ui();
		}

		$nonce_field = filter_input( INPUT_POST, 'account_delete_confirm_nonce' );
		if ( empty( $nonce_field ) || ! wp_verify_nonce( $nonce_field, 'crwdfnd_account_delete_confirm' ) ) {
			CrwdfndUtils::account_delete_confirmation_ui( CrwdfndUtils::_( 'Sorry, Nonce verification failed.' ) );
		}
		if ( $auth->match_password( $password ) ) {
			$auth->delete();
			wp_safe_redirect( get_home_url() );
			exit( 0 );
		} else {
			CrwdfndUtils::account_delete_confirmation_ui( CrwdfndUtils::_( "Sorry, Password didn't match." ) );
		}
	}

	public function process_password_reset() {
		$message          = '';
		$crwdfnd_reset       = filter_input( INPUT_POST, 'crwdfnd-reset' );
		$crwdfnd_reset_email = filter_input( INPUT_POST, 'crwdfnd_reset_email', FILTER_UNSAFE_RAW );
		if ( ! empty( $crwdfnd_reset ) ) {
			CrwdfndFrontRegistration::get_instance()->reset_password( $crwdfnd_reset_email );
		}
	}

	private function register_member() {
		$registration = filter_input( INPUT_POST, 'crwdfnd_registration_submit' );
		if ( ! empty( $registration ) ) {
			CrwdfndFrontRegistration::get_instance()->register_front_end();
		}
	}

	private function check_and_do_email_activation() {
		$email_activation = filter_input( INPUT_GET, 'crwdfnd_email_activation', FILTER_SANITIZE_NUMBER_INT );
		if ( ! empty( $email_activation ) ) {
			CrwdfndFrontRegistration::get_instance()->email_activation();
		}
		//also check activation email resend request
		$email_activation_resend = filter_input( INPUT_GET, 'crwdfnd_resend_activation_email', FILTER_SANITIZE_NUMBER_INT );
		if ( ! empty( $email_activation_resend ) ) {
			CrwdfndFrontRegistration::get_instance()->resend_activation_email();
		}
	}

	private function edit_profile() {
		$crwdfnd_editprofile_submit = filter_input( INPUT_POST, 'crwdfnd_editprofile_submit' );
		if ( ! empty( $crwdfnd_editprofile_submit ) ) {
			CrwdfndFrontRegistration::get_instance()->edit_profile_front_end();
		}
	}

	public function check_and_handle_auto_login() {

		if ( isset( $_REQUEST['crwdfnd_auto_login'] ) && $_REQUEST['crwdfnd_auto_login'] == '1' ) {
			//Handle the auto login
			CrwdfndLog::log_simple_debug( 'Handling auto login request...', true );

			$enable_auto_login = CrwdfndSettings::get_instance()->get_value( 'auto-login-after-rego' );
			if ( empty( $enable_auto_login ) ) {
				CrwdfndLog::log_simple_debug( 'Auto login after registration feature is disabled in settings.', true );
				return;
			}

			//Check auto login nonce value
			$auto_login_nonce = isset( $_REQUEST['crwdfnd_auto_login_nonce'] ) ? $_REQUEST['crwdfnd_auto_login_nonce'] : '';
			if ( ! wp_verify_nonce( $auto_login_nonce, 'crwdfnd-auto-login-nonce' ) ) {
				CrwdfndLog::log_simple_debug( 'Error! Auto login nonce verification check failed!', false );
				wp_die( 'Auto login nonce verification check failed!' );
			}

			//Perform the login
			$auth         = CrwdfndAuth::get_instance();
			$user         = apply_filters( 'crwdfnd_user_name', filter_input( INPUT_GET, 'crwdfnd_user_name' ) );
			$user         = sanitize_user( $user );
			$encoded_pass = filter_input( INPUT_GET, 'crwdfnd_encoded_pw' );
			$pass         = base64_decode( $encoded_pass );
			$auth->login( $user, $pass );
			CrwdfndLog::log_simple_debug( 'Auto login request completed for: ' . $user, true );
		}
	}

}
