<?php

class CrwdfndAccessControl {

    private $lastError;
    private $moretags;
    private static $_this;

    private function __construct(){
        $this->lastError = '';
        $this->moretags  = array();
    }

    public static function get_instance(){
        self::$_this = empty(self::$_this)? new CrwdfndAccessControl():self::$_this;
        return self::$_this;
    }

    public function can_i_read_post($post){
        if (!is_a($post, 'WP_Post')) {
            return true;
        }

        $id = $post->ID;
        $this->lastError = '';
        $auth = CrwdfndAuth::get_instance();


        $protected = CrwdfndProtection::get_instance();
        if (!$protected->is_protected($id)){
            return true;
        }

        if(!$auth->is_logged_in()){
            //This user is not logged into the site. No access to this protected post.
            $text = CrwdfndUtils::_('You need to login to view this content. ') . CrwdfndMiscUtils::get_login_link();
            $error_msg = '<div class="crwdfnd-post-not-logged-in-msg">'.$text.'</div>';
            $this->lastError = apply_filters('crwdfnd_not_logged_in_post_msg', $error_msg);
            return false;
        }

        //Check if the account is expired
        if ($auth->is_expired_account()){
            //This user's account is expired. No access to this post. Show account expiry message.
            $text = CrwdfndUtils::_('Your account has expired. ') .  CrwdfndMiscUtils::get_renewal_link();
            $error_msg = '<div class="crwdfnd-post-account-expired-msg crwdfnd-yellow-box">'.$text.'</div>';
            $this->lastError = apply_filters('crwdfnd_account_expired_msg', $error_msg);
            return false;
        }

        //Check older post protection addon settings (if being used on this site).
        $protect_older_posts = apply_filters('crwdfnd_should_protect_older_post', false, $id);
        if ($protect_older_posts){
            $text = CrwdfndUtils::_('This content can only be viewed by members who joined on or before ') . CrwdfndUtils::get_formatted_and_translated_date_according_to_wp_settings($post->post_date);
            $error_msg = '<div class="crwdfnd-post-older-post-msg">'.$text.'</div>';
            $this->lastError = apply_filters ('crwdfnd_restricted_post_msg_older_post', $error_msg);
            return false;
        }

        //Check if this user's membership level has access to this post
        $permission = CrwdfndPermission::get_instance($auth->get('membership_level'));
        if($permission->is_permitted($id)) {
            return true;
        } else {
            $text = CrwdfndUtils::_('This content is not permitted for your membership level.');
            $error_msg = '<div class="crwdfnd-post-no-access-msg">'.$text.'</div>';
            $this->lastError = apply_filters ('crwdfnd_restricted_post_msg', $error_msg);
            return false;
        }

        //All checks have passed. Show this post to the user.
        return true;
    }

    public function can_i_read_comment($comment){
        if (!is_a($comment, 'WP_Comment')) {
            return true;
        }

        $id = $comment->comment_ID;
        $post_id = $comment->comment_post_ID;
        $post = get_post($post_id);
        $this->lastError = '';
        $auth = CrwdfndAuth::get_instance();


        $protected = CrwdfndProtection::get_instance();
        if (!$protected->is_protected($post_id)){
            return true;
        }


        if(!$auth->is_logged_in()){
            //User is not logged-in. Not allowed to see this protected comment.
            $error_msg = '<div class="crwdfnd-comment-not-logged-in">' . CrwdfndUtils::_("You need to login to view this content. ") . '</div>';
            $this->lastError = apply_filters('crwdfnd_not_logged_in_comment_msg', $error_msg);
            return false;
        }

        //Check if member account is expired.
        if ($auth->is_expired_account()){
            //This user's account is expired. Not allowed to see this comment. Show account expiry notice also.
            $text = CrwdfndUtils::_('Your account has expired. ') .  CrwdfndMiscUtils::get_renewal_link();
            $error_msg = '<div class="crwdfnd-comment-account-expired-msg crwdfnd-yellow-box">'.$text.'</div>';
            $this->lastError = apply_filters('crwdfnd_account_expired_msg', $error_msg);
            return false;
        }

        //Check if older post protection addon is active and protection according to it's settings.
        $protect_older_posts = apply_filters('crwdfnd_should_protect_older_post', false, $post_id);
        if ($protect_older_posts){
            //This comment is protected due to the older post protection addon settings configuration.
            $text = CrwdfndUtils::_('This content can only be viewed by members who joined on or before ') . CrwdfndUtils::get_formatted_and_translated_date_according_to_wp_settings($post->post_date);
            $error_msg = '<div class="crwdfnd-comment-older-post-msg">'.$text.'</div>';
            $this->lastError = apply_filters ('crwdfnd_restricted_comment_older_post', $error_msg);
            return false;
        }

        //Check if this member can view this comment based on his membership level
        $permission = CrwdfndPermission::get_instance($auth->get('membership_level'));
        if(!$permission->is_permitted($post_id)) {
            //This member's membership level doesn't have access to this comment's post. Not allowed to see this comment.
            $error_msg = '<div class="crwdfnd-comment-no-access-msg">' . CrwdfndUtils::_('This content is not permitted for your membership level.').'</div>';
            $this->lastError = apply_filters ('crwdfnd_restricted_comment_msg', $error_msg);
            return false;
        }

        //All checks have passed at this stage. Show this comment to this user.
        return true;
    }

    public function filter_post($post,$content){
        if (!is_a($post, 'WP_Post')) {
            //This is not a WP_Post object. So we don't want to handle it in our plugin.
            return $content;
            //return CrwdfndUtils::_('Error! $post is not a valid WP_Post object.');
        }

        if(self::expired_user_has_access_to_this_page()) {
            return $content;//An expired user is viewing this page and it is a system page, so allow access.
        }

        if(CrwdfndUtils::is_first_click_free($content)) {
            return $content;//First click free is true, so allow access.
        }

        if($this->can_i_read_post($post)) {
            return $content;//This member has access to this post, so allow access.
        }

        //Check and apply more tag protection.
        $more_tag_protection_value = $this->check_and_apply_more_tag_protection($post, $content);
        if(!empty($more_tag_protection_value)){
            //More tag protection was found in the post. Return the modified $content.
            return $more_tag_protection_value;
        }

        //Return whatever the result is from calling the earlier protection check functions.
        return $this->lastError;
    }

    public function check_and_apply_more_tag_protection($post, $content){
        
        //Check if more tag protection is enabled.
        $moretag = CrwdfndSettings::get_instance()->get_value('enable-moretag');
        if (empty($moretag)){
            //More tag protection is disabled in this site. So return empty string.
            return '';
        } else {
            //More tag protection is enabled in this site. Need to check the post segments to see if there is content after more tag.
            $post_segments = explode( '<!--more-->', $post->post_content);
            if (count($post_segments) >= 2){
                //There is content after the more tag.
                $auth = CrwdfndAuth::get_instance();
                if(!$auth->is_logged_in()){
                    //User is not logged-in. Need to show the login message after the more tag.
                    $text = CrwdfndUtils::_("You need to login to view the rest of the content. ") . CrwdfndMiscUtils::get_login_link();
                    $error_msg = '<div class="crwdfnd-more-tag-not-logged-in crwdfnd-margin-top-10">' . $text . '</div>';
                    $more_tag_check_msg = apply_filters('crwdfnd_not_logged_in_more_tag_msg', $error_msg);
                } else {
                    //The user is logged in.
                    //Lets check if the user's account is expired.
                    if ($auth->is_expired_account()){
                        //This user's account is expired. Not allowed to see this post. Show account expiry notice also.
                        $text = CrwdfndUtils::_('Your account has expired. ') .  CrwdfndMiscUtils::get_renewal_link();
                        $error_msg = '<div class="crwdfnd-more-tag-account-expired-msg crwdfnd-yellow-box">'.$text.'</div>';
                        $more_tag_check_msg = apply_filters('crwdfnd_account_expired_more_tag_msg', $error_msg);
                    } else {
                        //At this stage, the user does not have permission to view the content after the more tag.
                        $text = CrwdfndUtils::_(" The rest of the content is not permitted for your membership level.");
                        $error_msg = '<div class="crwdfnd-more-tag-restricted-msg crwdfnd-margin-top-10">' . $text . '</div>';
                        $more_tag_check_msg = apply_filters ('crwdfnd_restricted_more_tag_msg', $error_msg);
                    }
                }

                $filtered_before_more_content = CrwdfndMiscUtils::format_raw_content_for_front_end_display($post_segments[0]);
                $new_post_content = $filtered_before_more_content . $more_tag_check_msg;
                return $new_post_content;

            }//End of segment count condition check.
        }//End of more tag enabled condition check.

        //More tag protection not applicable for this post. Return empty string.
        return '';
    }

    public function filter_comment($comment, $content){
        if($this->can_i_read_comment($comment)) {
            //This user has access to this comment.
            return $content;
        }
        return $this->lastError;
    }

    public function why(){
        return $this->lastError;
    }

    /*
     * This function checks if the current user is an expired user and has access to the system page content (if the current URL is a system page).
     */
    public static function expired_user_has_access_to_this_page(){
        $auth = CrwdfndAuth::get_instance();

        //Check if the user is logged-into the site.
        if(!$auth->is_logged_in()){
            //Anonymous user. No access. No need to check anything else.
            return false;
        }

        //Check if account is expired.
        if (!$auth->is_expired_account()){
            //This users account is not expired. No need to check anything else.
            return false;
        }

        /*** We have a expired member. Lets check if he is viewing a page that is a core system used URL. ***/
        if (self::is_current_url_a_system_page()){
            //Allow this expired user to view this post/page content since this is a core system page.
            return true;
        }

        //Not a system used page. So the expired user has no access to this page.
        return false;
    }

    /*
     * This function checks if the current page being viewed is one of the system used URLs
     */
    public static function is_current_url_a_system_page(){
        $current_page_url = CrwdfndMiscUtils::get_current_page_url();

        //Check if the current page is the membership renewal page.
        $renewal_url = CrwdfndSettings::get_instance()->get_value('renewal-page-url');
        if (empty($renewal_url)) {return false;}
        if (CrwdfndMiscUtils::compare_url($renewal_url, $current_page_url)) {return true;}

        //Check if the current page is the membership logn page.
        $login_page_url = CrwdfndSettings::get_instance()->get_value('login-page-url');
        if (empty($login_page_url)) {return false;}
        if (CrwdfndMiscUtils::compare_url($login_page_url, $current_page_url)) {return true;}

        //Check if the current page is the membership join page.
        $registration_page_url = CrwdfndSettings::get_instance()->get_value('registration-page-url');
        if (empty($registration_page_url)) {return false;}
        if (CrwdfndMiscUtils::compare_url($registration_page_url, $current_page_url)) {return true;}

        return false;
    }

}
