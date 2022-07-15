<?php
$links = array();
if(isset($_REQUEST['crwdfnd_link_for'])){
    $link_for = isset($_POST['crwdfnd_link_for']) ? sanitize_text_field($_POST['crwdfnd_link_for']) : '';
    $member_id = filter_input(INPUT_POST, 'member_id', FILTER_SANITIZE_NUMBER_INT);
    $send_email = isset($_REQUEST['crwdfnd_reminder_email']) ? true : false;
    $links = CrwdfndUtils::get_registration_complete_prompt_link($link_for, $send_email, $member_id);
}

if(isset($_REQUEST['recreate-required-pages-submit'])){

    CrwdfndMiscUtils::create_mandatory_wp_pages();
    echo '<div class="crwdfnd-green-box">' . CrwdfndUtils::_('The required pages have been re-created.') . '</div>';
}
?>
<div id="poststuff">
    <div id="post-body">

        <div class="postbox">
            <h3 class="hndle"><label for="title"><?php echo CrwdfndUtils::_('Generate a Registration Completion link') ?></label></h3>
            <div class="inside">

                <p><strong><?php echo CrwdfndUtils::_('You can manually generate a registration completion link here and give it to your customer if they have missed the email that was automatically sent out to them after the payment.') ?></strong></p>

                <form action="" method="post">
                    <table>
                        <tr>
                            <?php echo CrwdfndUtils::_('Generate Registration Completion Link') ?>
                        <br /><input type="radio" value="one" name="crwdfnd_link_for" /><?php CrwdfndUtils::e('For a Particular Member ID'); ?>
                        <input type="text" name="member_id" size="5" value="" />
                        <br /><strong><?php echo CrwdfndUtils::_('OR') ?></strong>
                        <br /><input type="radio" checked="checked" value="all" name="crwdfnd_link_for" /> <?php echo CrwdfndUtils::_('For All Incomplete Registrations') ?>
                        </tr>
                        <tr>
                            <td>
                                <div class="crwdfnd-margin-top-10"></div>
                                <?php echo CrwdfndUtils::_('Send Registration Reminder Email Too') ?> <input type="checkbox" value="checked" name="crwdfnd_reminder_email">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="crwdfnd-margin-top-10"></div>
                                <input type="submit" name="submit" class="button-primary" value="<?php echo CrwdfndUtils::_('Submit') ?>" />
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <div class="crwdfnd-margin-top-10"></div>
                                <?php
                                if (!empty($links)) {
                                    echo '<div class="crwdfnd-green-box">' . CrwdfndUtils::_('Link(s) generated successfully. The following link(s) can be used to complete the registration.') . '</div>';
                                } else {
                                    echo '<div class="crwdfnd-grey-box">' . CrwdfndUtils::_('Registration completion links will appear below') . '</div>';
                                }
                                ?>
                                <div class="crwdfnd-margin-top-10"></div>
                                <?php foreach ($links as $key => $link) { ?>
                                    <input type="text" size="120" readonly="readonly" name="link[<?php echo $key ?>]" value="<?php echo $link; ?>"/><br/>
                                <?php } ?>

                                <?php
                                if (isset($_REQUEST['crwdfnd_reminder_email'])) {
                                    echo '<div class="crwdfnd-green-box">' . CrwdfndUtils::_('A prompt to complete registration email was also sent.') . '</div>';
                                }
                                ?>
                            </td>
                        </tr>

                    </table>
                </form>

            </div>
        </div>

        <div class="postbox">
            <h3 class="hndle"><label for="title"><?php echo CrwdfndUtils::_('Re-create the Required Pages') ?></label></h3>
            <div class="inside">
                <form action="" method="post" onsubmit="return confirm('Do you really want to re-create the pages?');">
                    <table>
                        <tr>
                            <td>
                                <div class="crwdfnd-margin-top-10"></div>
                                <input type="submit" name="recreate-required-pages-submit" class="button-primary" value="<?php echo CrwdfndUtils::_('Re-create the Required Pages') ?>" />
                            </td>
                        </tr>
                    </table>
                </form>

            </div>
        </div>

    </div>
</div>
