<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       linkit.link
 * @since      1.0.0
 *
 * @package    Linkitwoocommerce
 * @subpackage Linkitwoocommerce/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">
    <h1> LinkIt Integration </h1>


    <form method="post" action="options.php">
        <?php settings_fields('LinkIt') ?>
        <?php do_settings_sections('LinkIt') ?>
        <ul>
            <li>
                <h3>Api Key</h3>
                <input
                        type="text"
                        name="linkit_api_key"
                        id="apikey"
                        value="<?php echo esc_attr( get_option('linkit_api_key') );?>"
                        style="width: 500px"
                />
            </li>
            <h3>Client GPS metadata</h3>
            <li>
                <label for="latitude-meta">Client Latitude Meta</label>
                <input
                        type="text"
                        name="linkit_latitude_meta"
                        id="latitude-meta"
                        value="<?php echo esc_attr( get_option('linkit_latitude_meta')) ?>"
                />
            </li>
            <li>
                <label for="longitude-meta">Client Latitude Meta</label>
                <input
                        type="text"
                        name="linkit_longitude_meta"
                        id="longitude-meta"
                        value="<?php echo esc_attr( get_option('linkit_longitude_meta')) ?>"
                />
            </li>
            <li>
                <label for="geohash-meta">Client Geohash Meta</label>
                <input
                        type="text"
                        name="linkit_geohash_meta"
                        id="geohash-meta"
                        value="<?php echo esc_attr( get_option('linkit_geohash_meta')) ?>"
                />
            </li>
            <li>
                <?php submit_button(); ?>
            </li>
        </ul>
    </form>
</div>
