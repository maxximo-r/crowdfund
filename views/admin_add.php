<?php CrowdFundMe::enqueue_validation_scripts(); ?>
<div class="wrap" id="crwdfnd-profile-page" type="add">
<style>#crwdfnd-create-user input {position: relative;}</style>
    <form action="" method="post" name="crwdfnd-create-user" id="crwdfnd-create-user" class="validate crwdfnd-validate-form"<?php do_action('user_new_form_tag'); ?>>
        <input name="action" type="hidden" value="createuser" />
        <?php wp_nonce_field('create_crwdfnduser_admin_end', '_wpnonce_create_crwdfnduser_admin_end') ?>
        <h3><?php echo CrwdfndUtils::_('Add Member') ?></h3>
        <p><?php echo CrwdfndUtils::_('Create a brand new user and add it to this site.'); ?></p>
        <table class="form-table">
            <tbody>
                <tr class="form-required crwdfnd-admin-add-username">
                    <th scope="row"><label for="user_name"><?php echo CrwdfndUtils::_('Username'); ?> <span class="description"><?php echo CrwdfndUtils::_('(required)'); ?></span></label></th>
                    <td><input class="regular-text validate[required,custom[noapostrophe],custom[CRWDFNDUserName],minSize[4],ajax[ajaxUserCall]]" name="user_name" type="text" id="user_name" value="<?php echo esc_attr(stripslashes($user_name)); ?>" aria-required="true" /></td>
                </tr>
                <tr class="form-required crwdfnd-admin-add-email">
                    <th scope="row"><label for="email"><?php echo CrwdfndUtils::_('E-mail'); ?> <span class="description"><?php echo CrwdfndUtils::_('(required)'); ?></span></label></th>
                    <td><input name="email" autocomplete="off" class="regular-text validate[required,custom[email],ajax[ajaxEmailCall]]" type="text" id="email" value="<?php echo esc_attr($email); ?>" /></td>
                </tr>
                <tr class="form-required crwdfnd-admin-add-password">
                    <th scope="row"><label for="password"><?php echo CrwdfndUtils::_('Password'); ?> <span class="description"><?php _e('(twice, required)', 'crowd-fund'); ?></span></label></th>
                    <td><input class="regular-text"  name="password" type="password" id="pass1" autocomplete="off" />
                        <br />
                        <input class="regular-text" name="password_re" type="password" id="pass2" autocomplete="off" />
                        <br />
                        <div id="pass-strength-result"><?php echo CrwdfndUtils::_('Strength indicator'); ?></div>
                        <p class="description indicator-hint"><?php echo CrwdfndUtils::_('Hint: The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ &amp; ).'); ?></p>
                    </td>
                </tr>
                <tr class="crwdfnd-admin-add-account-state">
                    <th scope="row"><label for="account_state"><?php echo CrwdfndUtils::_('Account Status'); ?></label></th>
                    <td><select class="regular-text" name="account_state" id="account_state">
                            <?php echo CrwdfndUtils::account_state_dropdown('active'); ?>
                        </select>
                    </td>
                </tr>
                <tr class="crwdfnd-admin-edit-membership-level">
                    <th scope="row"><label for="membership_level"><?php echo CrwdfndUtils::_('Membership Level'); ?></label></th>
                    <td><select class="regular-text" name="membership_level" id="membership_level">
                            <?php foreach ($levels as $level): ?>
                                <option <?php echo ($level['id'] == $membership_level) ? "selected='selected'" : ""; ?> value="<?php echo $level['id']; ?>"> <?php echo $level['alias'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <?php include('admin_member_form_common_part.php'); ?>
            </tbody>
        </table>
        <?php include('admin_member_form_common_js.php'); ?>
        <?php submit_button(CrwdfndUtils::_('Add New Member '), 'primary', 'createcrwdfnduser', true, array('id' => 'createcrwdfndusersub')); ?>
    </form>
</div>
