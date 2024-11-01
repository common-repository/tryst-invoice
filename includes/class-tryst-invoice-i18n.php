<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.matteus.dev
 * @since      1.0.0
 *
 * @package    Tryst_Invoice
 * @subpackage Tryst_Invoice/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Tryst_Invoice
 * @subpackage Tryst_Invoice/includes
 * @author     Matteus Barbosa <contato@matteus.dev>
 */
class Tryst_Invoice_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'tryst-invoice',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
