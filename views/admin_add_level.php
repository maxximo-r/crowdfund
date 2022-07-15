<?php CrowdFundMe::enqueue_validation_scripts(); ?>
<div class="wrap" id="crwdfnd-level-page">

<form action="" method="post" name="crwdfnd-create-level" id="crwdfnd-create-level" class="validate crwdfnd-validate-form">
<input name="action" type="hidden" value="createlevel" />
<h3><?php echo CrwdfndUtils::_('Add Membership Level'); ?></h3>
<p><?php echo CrwdfndUtils::_('Create new membership level.'); ?></p>
<?php wp_nonce_field( 'create_crwdfndlevel_admin_end', '_wpnonce_create_crwdfndlevel_admin_end' ) ?>
<table class="form-table">
    <tbody>
	<tr>
            <th scope="row"><label for="alias"><?php echo  CrwdfndUtils::_('Membership Level Name'); ?> <span class="description"><?php echo  CrwdfndUtils::_('(required)'); ?></span></label></th>
            <td><input class="regular-text validate[required]" name="alias" type="text" id="alias" value="" aria-required="true" /></td>
	</tr>
	<tr class="form-field form-required">
            <th scope="row"><label for="role"><?php echo  CrwdfndUtils::_('Default WordPress Role'); ?> <span class="description"><?php echo  CrwdfndUtils::_('(required)'); ?></span></label></th>
            <td><select  class="regular-text" name="role"><?php wp_dropdown_roles( 'subscriber' ); ?></select></td>
	</tr>
        <tr>
            <th scope="row"><label for="subscription_period"><?php echo  CrwdfndUtils::_('Access Duration'); ?> <span class="description"><?php echo  CrwdfndUtils::_('(required)'); ?></span></label>
            </th>
            <td>
                <p><input type="radio" checked="checked" value="<?php echo  CrwdfndMembershipLevel::NO_EXPIRY?>" name="subscription_duration_type" /> <?php echo  CrwdfndUtils::_('No Expiry (Access for this level will not expire until cancelled')?>)</p>
                <p><input type="radio" value="<?php echo  CrwdfndMembershipLevel::DAYS ?>" name="subscription_duration_type" /> <?php echo  CrwdfndUtils::_('Expire After')?>
                    <input type="text" value="" name="subscription_period_<?php echo  CrwdfndMembershipLevel::DAYS ?>"> <?php echo  CrwdfndUtils::_('Days (Access expires after given number of days)')?></p>
                <p><input type="radio" value="<?php echo  CrwdfndMembershipLevel::WEEKS?>" name="subscription_duration_type" /> <?php echo  CrwdfndUtils::_('Expire After')?>
                    <input type="text" value="" name="subscription_period_<?php echo  CrwdfndMembershipLevel::WEEKS ?>"> <?php echo  CrwdfndUtils::_('Weeks (Access expires after given number of weeks')?></p>
                <p><input type="radio"  value="<?php echo  CrwdfndMembershipLevel::MONTHS?>" name="subscription_duration_type" /> <?php echo  CrwdfndUtils::_('Expire After')?>
                    <input type="text" value="" name="subscription_period_<?php echo  CrwdfndMembershipLevel::MONTHS?>"> <?php echo  CrwdfndUtils::_('Months (Access expires after given number of months)')?></p>
                <p><input type="radio"  value="<?php echo  CrwdfndMembershipLevel::YEARS?>" name="subscription_duration_type" /> <?php echo  CrwdfndUtils::_('Expire After')?>
                    <input type="text" value="" name="subscription_period_<?php echo  CrwdfndMembershipLevel::YEARS?>"> <?php echo  CrwdfndUtils::_('Years (Access expires after given number of years)')?></p>
                <p><input type="radio" value="<?php echo  CrwdfndMembershipLevel::FIXED_DATE?>" name="subscription_duration_type" /> <?php echo  CrwdfndUtils::_('Fixed Date Expiry')?>
                    <input type="text" class="crwdfnd-date-picker" value="<?php echo  date('Y-m-d');?>" name="subscription_period_<?php echo  CrwdfndMembershipLevel::FIXED_DATE?>"> <?php echo  CrwdfndUtils::_('(Access expires on a fixed date)')?></p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="email_activation"><?php echo  CrwdfndUtils::_('Email Activation'); ?></label>
            </th>
            <td>
                <input name="email_activation" type="checkbox" value="1">
            </td>
	</tr>
        <?php echo  apply_filters('crwdfnd_admin_add_membership_level_ui', '');?>
</tbody>
</table>
<?php submit_button( CrwdfndUtils::_('Add New Membership Level '), 'primary', 'createcrwdfndlevel', true, array( 'id' => 'createcrwdfndlevelsub' ) ); ?>
</form>
</div>
<script>
jQuery(document).ready(function($){
    $('.crwdfnd-date-picker').datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true, yearRange: "-100:+100"});
});
</script>
