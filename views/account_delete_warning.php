
<header class="entry-header">
    <?php echo  CrwdfndUtils::_('Delete Account'); ?>
</header>
<?php if (!empty($msg)) echo '<p>' . $msg . '</p>'; ?>
<p style="color:red;">
    <?php echo  CrwdfndUtils::_('You are about to delete an account. This will delete user data associated with this account. '); ?>
    <?php echo  CrwdfndUtils::_('It will also delete the associated WordPress user account.'); ?>
    <?php echo  CrwdfndUtils::_('(NOTE: for safety, we do not allow deletion of any associated WordPress account with administrator role).'); ?>
    <?php echo  CrwdfndUtils::_('Continue?'); ?>
</p>
<form method="post">
    <p><?php echo  CrwdfndUtils::_('Password: '); ?><input name="account_delete_confirm_pass" type="password"></p>
    <p><input type="submit" name="confirm" value="<?php echo  CrwdfndUtils::_('Confirm Account Deletion'); ?>" /> </p>
    <?php wp_nonce_field('crwdfnd_account_delete_confirm', 'account_delete_confirm_nonce'); ?>
</form>