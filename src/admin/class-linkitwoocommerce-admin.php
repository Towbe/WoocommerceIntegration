<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       linkit.link
 * @since      1.0.0
 *
 * @package    Linkitwoocommerce
 * @subpackage Linkitwoocommerce/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, attaches the hooks
 *
 * @package    Linkitwoocommerce
 * @subpackage Linkitwoocommerce/admin
 * @author     LinkIt <admin@linkit.link>
 */
class Linkitwoocommerce_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Linkitwoocommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Linkitwoocommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/linkitwoocommerce-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Linkitwoocommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Linkitwoocommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/linkitwoocommerce-admin.js', array( 'jquery' ), $this->version, false );

	}

    /**
     * Creates the pages in the admin area
     *
     * @since 1.0.0
     */
	public function create_admin_menu() {
	    add_menu_page("LinkIt Configuration", "LinkIt Configuration", "administrator", "linkit-config", array($this, "display_admin_page"));
    }

    /**
     * Renders the admin page
     *
     * @since 1.0.0
     */
    public function display_admin_page() {
        require_once plugin_dir_path( __FILE__ ) . 'partials/linkitwoocommerce-admin-display.php';
    }

    /**
     * Tells wordpress what are the settings that can be edited
     *
     * @since 1.0.0
     */
    public function register_settings() {
        register_setting('LinkIt', 'linkit_api_key');
        register_setting('LinkIt', 'linkit_latitude_meta');
        register_setting('LinkIt', 'linkit_longitude_meta');
    }

    /**
     * Display the information about the job
     */
    public function display_job_info() {
        require_once plugin_dir_path( __FILE__ ) . 'partials/linkitwoocommerce-admin-jobinfo-display.php';
    }

}
