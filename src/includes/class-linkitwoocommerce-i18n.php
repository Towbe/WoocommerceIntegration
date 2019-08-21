<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       linkit.link
 * @since      1.0.0
 *
 * @package    Linkitwoocommerce
 * @subpackage Linkitwoocommerce/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Linkitwoocommerce
 * @subpackage Linkitwoocommerce/includes
 * @author     LinkIt <admin@linkit.link>
 */
class Linkitwoocommerce_i18n {
    /**
     * The domain in which the translations are saved
     *
     * @var string $domain The domain of the translations
     */
    protected $domain;

    public function __construct(){
        $this->domain = 'linkitwoocommerce';
    }

    /**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			$this->domain,
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

	public function load_textdomain() {
        $mo_file = dirname(dirname(plugin_basename( __FILE__ ))) . '/languages/' . $this->domain . '-' . get_locale() . '.mo';
        load_textdomain($this->domain, $mo_file);
    }



}
