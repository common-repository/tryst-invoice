<?php

class Tryst_Invoice_Admin_Meta{
	
	private static $invoice, $member;
	
	public static function getInvoice(){
		return self::$invoice;
	}
	
	// Register Custom Post Type
	public static function invoice_post_type() {
		
		$labels = array(
		'name'                  => _x( 'Boleto', 'Post Type General Name', 'tryst-invoice' ),
		'singular_name'         => _x( 'Boleto', 'Post Type Singular Name', 'tryst-invoice' ),
		'menu_name'             => __( 'Tryst Boleto', 'tryst-invoice' ),
		'name_admin_bar'        => __( 'Boleto', 'tryst-invoice' ),
		'archives'              => __( 'Item Archives', 'tryst-invoice' ),
		'attributes'            => __( 'Item Attributes', 'tryst-invoice' ),
		'parent_item_colon'     => __( 'Parent Item:', 'tryst-invoice' ),
		'all_items'             => __( 'Todos os Boletos', 'tryst-invoice' ),
		'add_new_item'          => __( 'Novo Boleto', 'tryst-invoice' ),
		'add_new'               => __( 'Novo', 'tryst-invoice' ),
		'new_item'              => __( 'New', 'tryst-invoice' ),
		'edit_item'             => __( 'Edit Boleto', 'tryst-invoice' ),
		'update_item'           => __( 'Update', 'tryst-invoice' ),
		'view_item'             => __( 'Visualizar', 'tryst-invoice' ),
		'view_items'            => __( 'Visualizar Boleto', 'tryst-invoice' ),
		'search_items'          => __( 'Pesquisar Boleto', 'tryst-invoice' ),
		'not_found'             => __( 'Not Found', 'tryst-invoice' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'tryst-invoice' ),
		'featured_image'        => __( 'Featured Image', 'tryst-invoice' ),
		'set_featured_image'    => __( 'Set featured image', 'tryst-invoice' ),
		'remove_featured_image' => __( 'Remove featured image', 'tryst-invoice' ),
		'use_featured_image'    => __( 'Use as featured image', 'tryst-invoice' ),
		'insert_into_item'      => __( 'Insert into item', 'tryst-invoice' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'tryst-invoice' ),
		'items_list'            => __( 'Items list', 'tryst-invoice' ),
		'items_list_navigation' => __( 'Items list navigation', 'tryst-invoice' ),
		'filter_items_list'     => __( 'Filter items list', 'tryst-invoice' )
		);
		$args = array(
		'label'                 => __( 'Invoice', 'tryst-invoice' ),
		'description'           => __( 'Register invoices to keep control of dates & times', 'tryst-invoice' ),
		'labels'                => $labels,
		'taxonomies'			=> [],
		'supports'              => ['title', 'custom-fields'],
		'hierarchical'          => true,
		'public'                => false,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
		'menu_icon' 			=> 'dashicons-media-document' 
		);
		
		register_post_type( 'invoice', $args );
		
	}
	
	/**
	* Adds a metabox to the right side of the screen under the â€œPublishâ€ box
	*/
	public static function add_invoice_request_metaboxes() {

		
		global $post;
		
			//loads the invoice on editor page
			self::$invoice = new Tryst\Invoice($post->ID);
			self::$member = self::$invoice->getMember();
		
				//date
				add_meta_box(
			'member',
			__('Member', 'tryst'),
			['Tryst_Invoice_Admin_Meta', 'member_link'],
			'invoice',
			'normal',
			'default'
		);
		
	


		
		
	}

	/**
	* Output the HTML for the metabox.
	*/
	public static function member_link() {
		global $post;
		

			//loads the invoice on editor page
			self::$invoice = new Tryst\Invoice($post->ID);
			self::$member = self::$invoice->getMember();
		
			
		// Nonce field to validate form request came from current site
		wp_nonce_field( plugin_basename( __FILE__ ), 'invoice_nonce' );

		$member = self::$member;
		
		if(empty($member))
		echo '<input type="text" name="invoice[user_id]" class="regular-text" placeholder="'.__('Member ID', 'tryst-invoice').'">';
		else {
			echo '<input type="hidden" name="invoice[user_id]" value="'.$member->getUser()->ID.'">';
			echo '<a id="member_name" href="'. site_url().'/wp-admin/user-edit.php?user_id='.$member->getUser()->ID.'">'.$member->getName().'</a>';
		}
	
		
	}
	
	
	/**
	* Save post metadata when a post is saved.
	*
	* @param int $post_id The post ID.
	* @param post $post The post object.
	* @param bool $update Whether this is an existing post being updated or not.
	*/
	public static function save_custom_meta_data($id, $post = null, $update = null ) {
		/*
		* In production code, $slug should be set only once in the plugin,
		* preferably as a class property, rather than in each function that needs it.
		*/
		$post_type = get_post_type($id);
		
		// If this isn't a 'invoice' post, don't update it.
		if ( "invoice" != $post_type ) return;
		
		/* --- security verification --- */
		if(!wp_verify_nonce($_POST['invoice_nonce'], plugin_basename(__FILE__))) {
			return $id;
		} // end if	

		$data = $_POST['invoice'];
		
		self::$invoice = new \Tryst\Invoice($id, $data);
		self::$invoice->save();
	}
	
}

add_action( 'save_post', ['Tryst_Invoice_Admin_Meta', 'save_custom_meta_data']);