<?php CrowdFundMe::enqueue_validation_scripts(); ?>
<div class="wrap" id="crwdfnd-level-page">
<form action="" method="post" name="crwdfnd-edit-level" id="crwdfnd-edit-level" class="validate crwdfnd-validate-form"<?php do_action('level_edit_form_tag');?>>
<input name="action" type="hidden" value="editlevel" />
<?php wp_nonce_field( 'edit_crwdfndlevel_admin_end', '_wpnonce_edit_crwdfndlevel_admin_end' ) ?>
<h2><?php echo  CrwdfndUtils::_('Edit membership level'); ?></h2>
<p>
    <?php
    echo CrwdfndUtils::_('You can edit details of a selected membership level from this interface. ');
    echo CrwdfndUtils::_('You are currently editing: '). stripslashes($alias);
    ?>
</p>
<table class="form-table">
    <tbody>
	<tr>
		<th scope="row"><label for="alias"><?php echo  CrwdfndUtils::_('Membership Level Name'); ?> <span class="description"><?php echo  CrwdfndUtils::_('(required)'); ?></span></label></th>
		<td><input class="regular-text validate[required]" name="alias" type="text" id="alias" value="<?php echo stripslashes($alias);?>" aria-required="true" /></td>
	</tr>
	<tr class="form-field form-required">
		<th scope="row"><label for="role"><?php echo  CrwdfndUtils::_('Default WordPress Role'); ?> <span class="description"><?php echo  CrwdfndUtils::_('(required)'); ?></span></label></th>
		<td><select  class="regular-text" name="role"><?php wp_dropdown_roles( $role ); ?></select></td>
	</tr>
    <tr>
        <th scope="row"><label for="subscription_period"><?php echo  CrwdfndUtils::_('Access Duration'); ?> <span class="description"><?php echo  CrwdfndUtils::_('(required)'); ?></span></label>
        </th>
        <td>
                <p><input type="radio" <?php echo  checked(CrwdfndMembershipLevel::NO_EXPIRY,$subscription_duration_type,false)?> value="<?php echo  CrwdfndMembershipLevel::NO_EXPIRY?>" name="subscription_duration_type" /> <?php echo  CrwdfndUtils::_('No Expiry (Access for this level will not expire until cancelled)')?></p>
                <p><input type="radio" <?php echo  checked(CrwdfndMembershipLevel::DAYS,$subscription_duration_type,false)?> value="<?php echo  CrwdfndMembershipLevel::DAYS ?>" name="subscription_duration_type" /> <?php echo  CrwdfndUtils::_('Expire After')?>
                    <input type="text" value="<?php echo  checked(CrwdfndMembershipLevel::DAYS,$subscription_duration_type,false)? $subscription_period: "";?>" name="subscription_period_<?php echo  CrwdfndMembershipLevel::DAYS ?>"> <?php echo  CrwdfndUtils::_('Days (Access expires after given number of days)')?></p>

                <p><input type="radio" <?php echo  checked(CrwdfndMembershipLevel::WEEKS,$subscription_duration_type,false)?> value="<?php echo  CrwdfndMembershipLevel::WEEKS?>" name="subscription_duration_type" /> <?php echo  CrwdfndUtils::_('Expire After')?>
                    <input type="text" value="<?php echo  checked(CrwdfndMembershipLevel::WEEKS,$subscription_duration_type,false)? $subscription_period: "";?>" name="subscription_period_<?php echo  CrwdfndMembershipLevel::WEEKS ?>"> <?php echo  CrwdfndUtils::_('Weeks (Access expires after given number of weeks)')?></p>

                <p><input type="radio" <?php echo  checked(CrwdfndMembershipLevel::MONTHS,$subscription_duration_type,false)?> value="<?php echo  CrwdfndMembershipLevel::MONTHS?>" name="subscription_duration_type" /> <?php echo  CrwdfndUtils::_('Expire After')?>
                    <input type="text" value="<?php echo  checked(CrwdfndMembershipLevel::MONTHS,$subscription_duration_type,false)? $subscription_period: "";?>" name="subscription_period_<?php echo  CrwdfndMembershipLevel::MONTHS?>"> <?php echo  CrwdfndUtils::_('Months (Access expires after given number of months)')?></p>

                <p><input type="radio" <?php echo  checked(CrwdfndMembershipLevel::YEARS,$subscription_duration_type,false)?> value="<?php echo  CrwdfndMembershipLevel::YEARS?>" name="subscription_duration_type" /> <?php echo  CrwdfndUtils::_('Expire After')?>
                    <input type="text" value="<?php echo  checked(CrwdfndMembershipLevel::YEARS,$subscription_duration_type,false)? $subscription_period: "";?>" name="subscription_period_<?php echo  CrwdfndMembershipLevel::YEARS?>"> <?php echo  CrwdfndUtils::_('Years (Access expires after given number of years)')?></p>

                <p><input type="radio" <?php echo  checked(CrwdfndMembershipLevel::FIXED_DATE,$subscription_duration_type,false)?> value="<?php echo  CrwdfndMembershipLevel::FIXED_DATE?>" name="subscription_duration_type" /> <?php echo  CrwdfndUtils::_('Fixed Date Expiry')?>
                    <input type="text" class="crwdfnd-date-picker" value="<?php echo  checked(CrwdfndMembershipLevel::FIXED_DATE,$subscription_duration_type,false)? $subscription_period: "";?>" name="subscription_period_<?php echo  CrwdfndMembershipLevel::FIXED_DATE?>" id="subscription_period_<?php echo  CrwdfndMembershipLevel::FIXED_DATE?>"> <?php echo  CrwdfndUtils::_('(Access expires on a fixed date)')?></p>
        </td>
    </tr>
            <tr>
            <th scope="row">
                <label for="email_activation"><?php echo  CrwdfndUtils::_('Email Activation'); ?></label>
            </th>
            <td>
                <input name="email_activation" type="checkbox" value="1" <?php checked($email_activation);?>>
            </td>
	</tr>
    <?php echo  apply_filters('crwdfnd_admin_edit_membership_level_ui', '', $id);?>
</tbody>
</table>
<?php submit_button(CrwdfndUtils::_('Save Membership Level '), 'primary', 'editcrwdfndlevel', true, array( 'id' => 'editcrwdfndlevelsub' ) ); ?>
</form>
</div>
<script>
jQuery(document).ready(function($){
    $('.crwdfnd-date-picker').datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true, yearRange: "-100:+100"});
});
</script>
