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
            <h3>LinkIt App Value Mapping</h3>
            <?php
                $orderStatuses = wc_get_order_statuses();
                $orderStatusesKeys = array_keys($orderStatuses);
            ?>
            <li>
                <label for="job-type-meta">Job Type Meta</label>
                <input
                        type="text"
                        name="linkit_job_type_meta"
                        id="latitude-meta"
                        value="<?php echo esc_attr( get_option('linkit_job_type_meta')) ?>"
                />
            </li>

            <li>
                <label for="send-picker">Picker stage</label>
                <select id="send-picker" name="linkit_send_picker" >
                    <?php
                    for ($i = 0; $i < sizeof($orderStatusesKeys); $i++) {
                        ?>
                        <option value="<?php echo $orderStatusesKeys[$i] ?>" <?php echo esc_attr( get_option('linkit_send_picker')) == $orderStatusesKeys[$i] ? 'selected' : '' ?>>
                            <?php echo $orderStatuses[$orderStatusesKeys[$i]] ?>
                        </option>
                        <?php
                    }
                    ?>
                </select>
            </li>
            <li>
                <label for="send-driver">Driver stage</label>
                <select id="send-driver" name="linkit_send_driver" >
                    <?php
                    for ($i = 0; $i < sizeof($orderStatusesKeys); $i++) {
                        ?>
                        <option value="<?php echo $orderStatusesKeys[$i] ?>" <?php echo esc_attr( get_option('linkit_send_driver')) == $orderStatusesKeys[$i] ? 'selected' : '' ?>>
                            <?php echo $orderStatuses[$orderStatusesKeys[$i]] ?>
                        </option>
                        <?php
                    }
                    ?>
                </select>
            </li>
            <li>
                <label for="cancel">Cancel Job</label>
                <select id="cancel" name="linkit_cancel" >
                    <?php
                    for ($i = 0; $i < sizeof($orderStatusesKeys); $i++) {
                        ?>
                        <option value="<?php echo $orderStatusesKeys[$i] ?>" <?php echo esc_attr( get_option('linkit_cancel')) == $orderStatusesKeys[$i] ? 'selected' : '' ?>>
                            <?php echo $orderStatuses[$orderStatusesKeys[$i]] ?>
                        </option>
                        <?php
                    }
                    ?>
                </select>
            </li>
            <li>
                <label for="finish">Finish Job</label>
                <select id="finish" name="linkit_finish" >
                    <?php
                    for ($i = 0; $i < sizeof($orderStatusesKeys); $i++) {
                        ?>
                        <option value="<?php echo $orderStatusesKeys[$i] ?>" <?php echo esc_attr( get_option('linkit_finish')) == $orderStatusesKeys[$i] ? 'selected' : '' ?>>
                            <?php echo $orderStatuses[$orderStatusesKeys[$i]] ?>
                        </option>
                        <?php
                    }
                    ?>
                </select>
            </li>
            <li>
                <?php submit_button(); ?>
            </li>
        </ul>
    </form>
</div>
