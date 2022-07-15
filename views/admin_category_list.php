

<form id="category_list_form" method="post">
    <input type="hidden" name="crwdfnd_category_prot_update_nonce" value="<?php echo wp_create_nonce('crwdfnd_category_prot_update_nonce_action'); ?>" />

    <p class="crwdfnd-select-box-left">
        <label for="membership_level_id"><?php CrwdfndUtils::e('Membership Level:'); ?></label>
        <select id="membership_level_id" name="membership_level_id">
            <option <?php echo $category_list->selected_level_id == 1 ? "selected" : "" ?> value="1"><?php echo CrwdfndUtils::_('General Protection'); ?></option>
            <?php echo CrwdfndUtils::membership_level_dropdown($category_list->selected_level_id); ?>
        </select>
    </p>
    <p class="crwdfnd-select-box-left">
        <input type="submit" class="button-primary" name="update_category_list" value="<?php CrwdfndUtils::e('Update'); ?>">
    </p>
        <?php $category_list->prepare_items(); ?>
        <?php $category_list->display(); ?>
</form>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#membership_level_id').change(function() {
            $('#category_list_form').submit();
        });
    });
</script>
