<?php

class CrwdfndSelfActionHandler {

    public function __construct() {
        //Register all the self action hooks the plugin needs to handle
        add_action('crwdfnd_front_end_registration_complete_fb', array(&$this, 'after_registration_callback'));//For the form builder
        add_action('crwdfnd_front_end_registration_complete_user_data', array(&$this, 'after_registration_callback'));

        add_action('crwdfnd_membership_level_changed', array(&$this, 'handle_membership_level_changed_action'));

        add_action('crwdfnd_payment_ipn_processed', array(&$this, 'handle_crwdfnd_payment_ipn_processed'));

        add_filter('crwdfnd_after_logout_redirect_url', array(&$this, 'handle_after_logout_redirection'));
        add_filter('crwdfnd_auth_cookie_expiry_value', array(&$this, 'handle_auth_cookie_expiry_value'));
    }

    public function handle_auth_cookie_expiry_value($expire){

        $logout_member_on_browser_close = CrwdfndSettings::get_instance()->get_value('logout-member-on-browser-close');
        if (!empty($logout_member_on_browser_close)) {
            //This feature is enabled.
            //Setting auth cookie expiry value to 0.
            $expire = apply_filters( 'crwdfnd_logout_on_close_auth_cookie_expiry_value', 0 );
        }

        return $expire;
    }

    public function handle_after_logout_redirection($redirect_url){
        $after_logout_url = CrwdfndSettings::get_instance()->get_value('after-logout-redirection-url');
        if(!empty($after_logout_url)){
            //After logout URL is being used. Override re-direct URL.
            $redirect_url = $after_logout_url;
        }
        return $redirect_url;
    }

    public function handle_crwdfnd_payment_ipn_processed($ipn_data){
        $ipn_forward_url = CrwdfndSettings::get_instance()->get_value('payment-notification-forward-url');
        if(!empty($ipn_forward_url)){
            CrwdfndLog::log_simple_debug("Payment Notification Forwarding is Enabled. Posting the payment data to URL: " . $ipn_forward_url, true);
            $response = wp_remote_post($ipn_forward_url, $ipn_data);
            if (is_wp_error($response)) {
                $error_message = $response->get_error_message();
                CrwdfndLog::log_simple_debug("There was an error posting the payment data. Error message: " . $error_message, true);
            }
        }
    }

    public function after_registration_callback($user_data){

        //Handle auto login after registration if enabled
        $enable_auto_login = CrwdfndSettings::get_instance()->get_value('auto-login-after-rego');
        if (!empty($enable_auto_login)){
            CrwdfndLog::log_simple_debug("Auto login after registration feature is enabled in settings. Performing auto login for user: " . $user_data['user_name'], true);
            $login_page_url = CrwdfndSettings::get_instance()->get_value('login-page-url');

            // Allow hooks to change the value of login_page_url
            $login_page_url = apply_filters('crwdfnd_after_reg_callback_login_page_url', $login_page_url);

            $encoded_pass = base64_encode($user_data['plain_password']);
            $crwdfnd_auto_login_nonce = wp_create_nonce('crwdfnd-auto-login-nonce');
            $arr_params = array(
                'crwdfnd_auto_login' => '1',
                'crwdfnd_user_name' => urlencode($user_data['user_name']),
                'crwdfnd_encoded_pw' => $encoded_pass,
                'crwdfnd_auto_login_nonce' => $crwdfnd_auto_login_nonce,
            );
            $redirect_page = add_query_arg($arr_params, $login_page_url);
            wp_redirect($redirect_page);
            exit(0);
        }

    }

    public function handle_membership_level_changed_action($args){
        $crwdfnd_id = $args['member_id'];
        $old_level = $args['from_level'];
        $new_level = $args['to_level'];
        CrwdfndLog::log_simple_debug('crwdfnd_membership_level_changed action triggered. Member ID: '.$crwdfnd_id.', Old Level: '.$old_level.', New Level: '.$new_level, true);

        //Check to see if the old and the new levels are the same or not.
        if(trim($old_level) == trim($new_level)){
            CrwdfndLog::log_simple_debug('The to (Level ID: '.$new_level.') and from (Level ID: '.$old_level.') values are the same. Nothing to do here.', true);
            return;
        }

        //Find record for this user
        CrwdfndLog::log_simple_debug('Retrieving user record for member ID: '.$crwdfnd_id, true);
        $resultset = CrwdfndMemberUtils::get_user_by_id($crwdfnd_id);
        if($resultset){
            //Found a record. Lets do some level update specific changes.
            //$emailaddress  = $resultset->email;
            //$account_status = $resultset->account_state;

            //Retrieve the new memberhsip level's details
            $level_row = CrwdfndUtils::get_membership_level_row_by_id($new_level);

            //Update the WP user role according to the new level's configuration (if applicable).
            $user_role = $level_row->role;
            $user_info = get_user_by('login', $resultset->user_name);
            $wp_user_id = $user_info->ID;
            CrwdfndLog::log_simple_debug('Calling user role update function.', true);
            CrwdfndMemberUtils::update_wp_user_role($wp_user_id, $user_role);
        }

    }

}