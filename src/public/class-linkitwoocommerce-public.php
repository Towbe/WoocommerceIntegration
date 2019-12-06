<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       linkit.link
 * @since      1.0.0
 *
 * @package    Linkitwoocommerce
 * @subpackage Linkitwoocommerce/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Linkitwoocommerce
 * @subpackage Linkitwoocommerce/public
 * @author     LinkIt <admin@linkit.link>
 */
class Linkitwoocommerce_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/linkitwoocommerce-public.css', array(), $this->version, 'all' );

	}

    /**
     * Tell wordpress what are the apis that can be accessed for this plugin
     *
     * @since 1.1.0
     */
    public function register_api() {

        register_rest_route('linkit/v1', '/next-step/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'handle_next_step'),
        ));
        register_rest_route('linkit/v1', '/picker-viewed/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'handle_picker_viewed'),
        ));
        register_rest_route('linkit/v1', '/next-step-delivery/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'handle_next_step_delivery'),
        ));
    }

    public function  handle_picker_viewed($data){
        $status = get_option('linkit_ongoing_picker');
        $order = wc_get_order($data['id']);

        if ( empty($order) ) {
            return;
        }

        $order->update_status($status);
    }

    /**
     * Hop to the next state of the order as per the configuration
     *
     * @since 1.1.0
     */
    public function handle_next_step($data) {
        $finish = get_option('linkit_next_step');
        $order = wc_get_order($data['id']);

        if ( empty($order) ) {
            return;
        }

        $order->update_status($finish);
    }

    /**
     * Hop to the next state of the order as per the configuration
     *
     * @since 1.1.0
     */
    public function handle_next_step_delivery($data) {
        $finish = get_option('linkit_next_step_delivery');
        $order = wc_get_order($data['id']);

        if ( empty($order) ) {
            return;
        }

        $order->update_status($finish);
    }

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/linkitwoocommerce-public.js', array( 'jquery' ), $this->version, false );

	}

}
