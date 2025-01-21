<?php

function ccf_render_settings_page() {
    ?>
    <div class="wrap">
        <h1>Custom Contact Form Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('ccf_settings_group');
            do_settings_sections('ccf_settings');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Email Recipient</th>
                    <td>
                        <input type="email" name="ccf_email_recipient"
                               value="<?php echo esc_attr(get_option('ccf_email_recipient')); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">HubSpot API Token</th>
                    <td>
                        <input type="text" name="ccf_hubspot_api_token"
                               value="<?php echo esc_attr(get_option('ccf_hubspot_api_token')); ?>" />
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

add_action('admin_init', function () {
    register_setting('ccf_settings_group', 'ccf_email_recipient');
    register_setting('ccf_settings_group', 'ccf_hubspot_api_token');
});