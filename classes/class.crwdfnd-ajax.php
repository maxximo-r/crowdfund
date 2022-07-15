<?php
/**
 * Handles various AJAX calls
 */

class CrwdfndAjax {

    public static function validate_email_ajax() {
        global $wpdb;
        $field_value = isset($_GET['fieldValue']) ? sanitize_text_field($_GET['fieldValue']) : '';
        $field_id = isset($_GET['fieldId']) ? sanitize_text_field($_GET['fieldId']) : '';
        $member_id = isset($_GET['member_id']) ? sanitize_text_field($_GET['member_id']) : '';
        if (!check_ajax_referer( 'crwdfnd-rego-form-ajax-nonce', 'nonce', false )) {
            echo '[ "' . esc_attr($field_id) .  '",false, "'.CrwdfndUtils::_('Nonce check failed. Please reload the page.').'" ]' ;
            exit;
        }
        if (!is_email($field_value)){
            echo '[ "' . esc_attr($field_id) .  '",false, "'.CrwdfndUtils::_('Invalid Email Address').'" ]' ;
            exit;
        }
        $table = $wpdb->prefix . "crwdfnd_members_tbl";
        $query = $wpdb->prepare("SELECT member_id FROM $table WHERE email = %s AND user_name != ''", $field_value);
        $db_id = $wpdb->get_var($query) ;
        $exists = ($db_id > 0) && $db_id != $member_id;
        echo '[ "' . esc_attr($field_id) . (($exists) ? '",false, "&chi;&nbsp;'.CrwdfndUtils::_('Already taken').'"]' : '",true, "&radic;&nbsp;'.CrwdfndUtils::_('Available'). '"]');
        exit;
    }

    public static function validate_user_name_ajax() {
        global $wpdb;
        $field_value = isset($_GET['fieldValue']) ? sanitize_text_field($_GET['fieldValue']) : '';
        $field_id = isset($_GET['fieldId']) ? sanitize_text_field($_GET['fieldId']) : '';
        if (!check_ajax_referer( 'crwdfnd-rego-form-ajax-nonce', 'nonce', false )) {
            echo '[ "' . esc_attr($field_id) .  '",false, "'.CrwdfndUtils::_('Nonce check failed. Please reload the page.').'" ]' ;
            exit;
        }
        if (!CrwdfndMemberUtils::is_valid_user_name($field_value)){
            echo '[ "' . esc_attr($field_id) . '",false,"&chi;&nbsp;'. CrwdfndUtils::_('Name contains invalid character'). '"]';
            exit;
        }
        $table = $wpdb->prefix . "crwdfnd_members_tbl";
        $query = $wpdb->prepare("SELECT COUNT(*) FROM $table WHERE user_name = %s", $field_value);
        $exists = $wpdb->get_var($query) > 0;
        echo '[ "' . esc_attr($field_id) . (($exists) ? '",false,"&chi;&nbsp;'. CrwdfndUtils::_('Already taken'). '"]' :
            '",true,"&radic;&nbsp;'.CrwdfndUtils::_('Available'). '"]');
        exit;
    }

}
