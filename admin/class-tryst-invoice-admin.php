<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.matteus.dev
 * @since      1.0.0
 *
 * @package    Tryst_Invoice
 * @subpackage Tryst_Invoice/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Tryst_Invoice
 * @subpackage Tryst_Invoice/admin
 * @author     Matteus Barbosa <contato@matteus.dev>
 */
class Tryst_Invoice_Admin {

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
		 * defined in Tryst_Invoice_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Tryst_Invoice_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tryst-invoice-admin.css', array(), $this->version, 'all' );

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
		 * defined in Tryst_Invoice_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Tryst_Invoice_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tryst-invoice-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function run(){
		global $tryst_plugin, $post;
	

		if ( !post_type_exists( 'invoice' ) ) {
			//class-tryst-admin-meta.php
			add_action( 'init', ['Tryst_Invoice_Admin_Meta', 'invoice_post_type'], 0 );
		}	
			//if(get_post_type($post->ID) == "invoice")
			add_action( 'add_meta_boxes', ['Tryst_Invoice_Admin_Meta', 'add_invoice_request_metaboxes']);

					
		//add extra domain fields
		if(!empty($tryst_plugin) && !empty($tryst_plugin->getNamespace())){
			$domain_class = "Tryst_Domain_Invoice_Admin_Meta";
			
			if(class_exists($domain_class)){

				//if(get_post_type($post->ID) == "invoice")
				add_action( 'add_meta_boxes', [$domain_class, 'add_invoice_request_metaboxes']);

			}
		} 

	
	}

}
