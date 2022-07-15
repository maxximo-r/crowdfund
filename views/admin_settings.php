
<form action="options.php" method="POST">
    <input type="hidden" name="tab" value="<?php echo $current_tab;?>" />
    <?php settings_fields('crwdfnd-settings-tab-' . $current_tab); ?>
    <?php do_settings_sections('crowdfund_me_settings'); ?>
    <?php submit_button(); ?>
</form>
