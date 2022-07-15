<tr class="crwdfnd-admin-edit-access-starts">
    <th scope="row"><label for="subscription_starts"><?php echo CrwdfndUtils::_('Access Starts') ?> </label></th>
    <td><input class="regular-text" name="subscription_starts" type="text" id="subscription_starts" value="<?php echo esc_attr($subscription_starts); ?>" /></td>
</tr>
<tr class="crwdfnd-admin-edit-first-name">
    <th scope="row"><label for="first_name"><?php echo CrwdfndUtils::_('First Name') ?> </label></th>
    <td><input class="regular-text" name="first_name" type="text" id="first_name" value="<?php echo esc_attr($first_name); ?>" /></td>
</tr>
<tr class="crwdfnd-admin-edit-last-name">
    <th scope="row"><label for="last_name"><?php echo CrwdfndUtils::_('Last Name') ?> </label></th>
    <td><input class="regular-text" name="last_name" type="text" id="last_name" value="<?php echo esc_attr($last_name); ?>" /></td>
</tr>
<tr class="crwdfnd-admin-edit-gender">
    <th scope="row"><label for="gender"><?php echo CrwdfndUtils::_('Gender'); ?></label></th>
    <td><select class="regular-text" name="gender" id="gender">
            <?php echo CrwdfndUtils::gender_dropdown($gender) ?>
        </select>
    </td>
</tr>
<tr class="crwdfnd-admin-edit-phone">
    <th scope="row"><label for="phone"><?php echo CrwdfndUtils::_('Phone') ?> </label></th>
    <td><input class="regular-text" name="phone" type="text" id="phone" value="<?php echo esc_attr($phone); ?>" /></td>
</tr>
<tr class="crwdfnd-admin-edit-address-street">
    <th scope="row"><label for="address_street"><?php echo CrwdfndUtils::_('Street') ?> </label></th>
    <td><input class="regular-text" name="address_street" type="text" id="address_street" value="<?php echo esc_attr($address_street); ?>" /></td>
</tr>
<tr class="crwdfnd-admin-edit-address-city">
    <th scope="row"><label for="address_city"><?php echo CrwdfndUtils::_('City') ?> </label></th>
    <td><input class="regular-text" name="address_city" type="text" id="address_city" value="<?php echo esc_attr($address_city); ?>" /></td>
</tr>
<tr class="crwdfnd-admin-edit-address-state">
    <th scope="row"><label for="address_state"><?php echo CrwdfndUtils::_('State') ?> </label></th>
    <td><input class="regular-text" name="address_state" type="text" id="address_state" value="<?php echo esc_attr($address_state); ?>" /></td>
</tr>
<tr class="crwdfnd-admin-edit-address-zipcode">
    <th scope="row"><label for="address_zipcode"><?php echo CrwdfndUtils::_('Zipcode') ?> </label></th>
    <td><input class="regular-text" name="address_zipcode" type="text" id="address_zipcode" value="<?php echo esc_attr($address_zipcode); ?>" /></td>
</tr>
<tr class="crwdfnd-admin-edit-address-country">
    <th scope="row"><label for="country"><?php echo CrwdfndUtils::_('Country') ?> </label></th>
    <td><select class="regular-text" id="country" name="country"><?php echo CrwdfndMiscUtils::get_countries_dropdown($country) ?></select></td>
</tr>
<tr class="crwdfnd-admin-edit-company">
    <th scope="row"><label for="company_name"><?php echo CrwdfndUtils::_('Company') ?></label></th>
    <td><input name="company_name" type="text" id="company_name" class="regular-text" value="<?php echo esc_attr($company_name); ?>" /></td>
</tr>
<tr class="crwdfnd-admin-edit-member-since">
    <th scope="row"><label for="member_since"><?php echo CrwdfndUtils::_('Member Since') ?> </label></th>
    <td><input class="regular-text" name="member_since" type="text" id="member_since" value="<?php echo esc_attr($member_since); ?>" /></td>
</tr>
