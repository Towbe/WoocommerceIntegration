<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              linkit.link
 * @since             1.0.0
 * @package           Linkitwoocommerce
 *
 * @wordpress-plugin
 * Plugin Name:       Linkit Woocommerce
 * Plugin URI:        linkit.link
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            LinkIt
 * Author URI:        linkit.link
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       linkitwoocommerce
 * Domain Path:       /languages
 */


 function handlePost(){




   $inputJSON = file_get_contents('php://input');
   $obj=json_decode($inputJSON);
   if ($obj !== false && $obj !== null) {
      $s =  (string) $obj->id ;
      $post = array(
        "post_title" => $s . " has been picked",
      );

     wp_insert_post($post,true);
   }



}

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'LINKITWOOCOMMERCE_VERSION', '1.2.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-linkitwoocommerce-activator.php
 */
function activate_linkitwoocommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-linkitwoocommerce-activator.php';
	Linkitwoocommerce_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-linkitwoocommerce-deactivator.php
 */
function deactivate_linkitwoocommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-linkitwoocommerce-deactivator.php';
	Linkitwoocommerce_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_linkitwoocommerce' );
register_deactivation_hook( __FILE__, 'deactivate_linkitwoocommerce' );
add_action('init', 'handlePost');
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-linkitwoocommerce.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_linkitwoocommerce() {

	$plugin = new Linkitwoocommerce();
	$plugin->run();

}
run_linkitwoocommerce();
