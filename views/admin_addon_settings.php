<form action="" method="POST">
    <input type="hidden" name="tab" value="<?php echo $current_tab; ?>" />
    <?php do_action('crwdfnd_addon_settings_section');
    wp_nonce_field('crwdfnd_addon_settings_section','crwdfnd_addon_settings_section_save_settings');
    submit_button(CrwdfndUtils::_('Save Changes'), 'primary', 'crwdfnd-addon-settings'); ?>
</form>
