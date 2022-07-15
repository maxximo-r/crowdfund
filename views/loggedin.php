<?php
$auth = CrwdfndAuth::get_instance();
?>
<div class="crwdfnd-login-widget-logged">
    <div class="crwdfnd-logged-username">
        <div class="crwdfnd-logged-username-label crwdfnd-logged-label"><?php echo CrwdfndUtils::_('Logged in as') ?></div>
        <div class="crwdfnd-logged-username-value crwdfnd-logged-value"><?php echo $auth->get('user_name'); ?></div>
    </div>
    <div class="crwdfnd-logged-status">
        <div class="crwdfnd-logged-status-label crwdfnd-logged-label"><?php echo CrwdfndUtils::_('Account Status') ?></div>
        <div class="crwdfnd-logged-status-value crwdfnd-logged-value"><?php echo CrwdfndUtils::_(ucfirst($auth->get('account_state'))); ?></div>
    </div>
    <div class="crwdfnd-logged-membership">
        <div class="crwdfnd-logged-membership-label crwdfnd-logged-label"><?php echo CrwdfndUtils::_('Membership') ?></div>
        <div class="crwdfnd-logged-membership-value crwdfnd-logged-value"><?php echo $auth->get('alias'); ?></div>
    </div>
    <div class="crwdfnd-logged-expiry">
        <div class="crwdfnd-logged-expiry-label crwdfnd-logged-label"><?php echo CrwdfndUtils::_('Account Expiry') ?></div>
        <div class="crwdfnd-logged-expiry-value crwdfnd-logged-value"><?php echo $auth->get_expire_date(); ?></div>
    </div>
    <?php
    $edit_profile_page_url = CrwdfndSettings::get_instance()->get_value('profile-page-url');
    if (!empty($edit_profile_page_url)) {
        echo '<div class="crwdfnd-edit-profile-link">';
        echo '<a href="' . $edit_profile_page_url . '">' . CrwdfndUtils::_("Edit Profile") . '</a>';
        echo '</div>';
    }
    ?>
    <div class="crwdfnd-logged-logout-link">
        <a href="?crwdfnd-logout=true"><?php echo CrwdfndUtils::_('Logout') ?></a>
    </div>
</div>
