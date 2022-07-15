<?php

class CrwdfndTransfer {

    public static $default_fields = array(
        'first_name' => '', 'last_name' => '',
        'user_name' => '', 'email' => '',
        'password' => '',
        'phone' => '', 'account_state' => '',
        'member_since' => '', 'subscription_starts' => '',
        'address_street' => '', 'address_city' => '',
        'address_state' => '', 'address_zipcode' => '',
        'company_name' => '', 'country' => '',
        'gender' => 'not specified',
        'membership_level' => '2');
    
    public static $default_level_fields = array(
        'alias' => '', 'role' => '',
        'subscription_period' => '', 'subscription_duration_type' => CrwdfndMembershipLevel::NO_EXPIRY);
    
    public static $admin_messages = array();
    private static $_this;

    private function __construct() {
        $this->message = get_option('crwdfnd-messages');
    }

    public static function get_instance() {
        self::$_this = empty(self::$_this) ? new CrwdfndTransfer() : self::$_this;
        return self::$_this;
    }

    public function get($key) {
        $messages = new CrwdfndMessages();
        return $messages->get($key);
    }

    public function set($key, $value) {
        $messages = new CrwdfndMessages();
        $messages->set($key, $value);
    }
    
    /*** Deprecated function - exists only for backwards compatibility ***/
    public static function get_real_ip_addr() {
        return CrwdfndUtils::get_user_ip_address();
    }
}
