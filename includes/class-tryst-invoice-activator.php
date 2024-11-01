<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.matteus.dev
 * @since      1.0.0
 *
 * @package    Tryst_Invoice
 * @subpackage Tryst_Invoice/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Tryst_Invoice
 * @subpackage Tryst_Invoice/includes
 * @author     Matteus Barbosa <contato@matteus.dev>
 */
class Tryst_Invoice_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		$options = get_option('tryst_option');

		$guide_list = [ get_option('admin_email') => get_option('blogname')];

		$options['guide_list'] = $guide_list;
		
		update_option('tryst_option', $options);
	}

}
