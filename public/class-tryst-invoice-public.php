<?php
/**
* The public-facing functionality of the plugin.
*
* @link       https://www.matteus.dev
* @since      1.0.0
*
* @package    Tryst_Invoice
* @subpackage Tryst_Invoice/public
*/
/**
* The public-facing functionality of the plugin.
*
* Defines the plugin name, version, and two examples hooks for how to
* enqueue the public-facing stylesheet and JavaScript.
*
* @package    Tryst_Invoice
* @subpackage Tryst_Invoice/public
* @author     Matteus Barbosa <contato@matteus.dev>
*/
class Tryst_Invoice_Public {
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

	public static $invoice;
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
		* defined in Tryst_Invoice_Loader as all of the hooks are defined
		* in that particular class.
		*
		* The Tryst_Invoice_Loader will then create the relationship
		* between the defined hooks and the functions defined in this
		* class.
		*/
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tryst-invoice-public.css', array(), $this->version, 'all' );
	}
	/**
	* Register the JavaScript for the public-facing side of the site.
	*
	* @since    1.0.0
	*/
	public function enqueue_scripts() {
		global $tryst_plugin;
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
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tryst-invoice-public.js', array( 'jquery' ), $this->version, false );
		if($tryst_plugin != null && !empty($tryst_plugin->getNamespace())){
			if(file_exists($tryst_plugin->getExtensionPath('invoice').'/public/js/Domain/tryst-invoice-public.js')){
				wp_enqueue_script( $this->plugin_name.'-extra', plugin_dir_url( __FILE__ ) . 'js/Domain/tryst-invoice-public.js', array( 'jquery' ), $this->version, false );
			}
		}
	}
	public function hook_head(){
		echo '<meta name="tryst_invoice_path" content="'.plugin_dir_url( __FILE__ ).'../'.'">';
	}
	private static function request_get($tryst_invoice_hash){
		global $tryst_plugin;
		$invoice = Tryst\Invoice::findByFormKey($tryst_invoice_hash);
		$member = $invoice->getMember();
		$post = get_post($invoice->getPost());
		setup_postdata( $post );
		include plugin_dir_path( __FILE__ ).'templates/form-invoice-request.php';
	}
	public static function request_post($atts = null){
		global $tryst_plugin, $post;
		if($_POST['security_code'] != $_POST['security_code_repeat'])
		return __('Invalid post. Please insert the security code as requested.');
		//member from Member extension
		if(!empty($tryst_plugin) && $tryst_plugin->isExtensionActive('member')){
			if(!empty($tryst_plugin->getNamespace())){
				if(file_exists($tryst_plugin->getExtensionPath('member').'/includes/Tryst/Domain/Member.php')){
					$member_domain_class = "\\Tryst\\Domain\\Member";											
				}
			}		
		}
		if(isset($member_domain_class) && class_exists($member_domain_class)){
			$member = new $member_domain_class(null, $_POST['member']);
		} else {					
			$member = new \Tryst\Member(null, $_POST['member']);
		}
		
		$member->save();
		
		if(file_exists($tryst_plugin->getExtensionPath('invoice').'/includes/Tryst/Domain/Invoice.php')){
			$invoice_domain_class = "\\Tryst\\Domain\\Invoice";											
		}
		if(isset($invoice_domain_class) && class_exists($invoice_domain_class)){
			$invoice = new $invoice_domain_class(null, $_POST['invoice'], $member);
		} else {
			$invoice = new \Tryst\Invoice(null, $_POST['invoice'], $member);
		}	
	
		$invoice->save();
		self::invoice_mail($invoice, 'request');
		//do_action( 'after_setup_theme', $member->getLogin(), $member->getPassword() );
		$alert_msg = __("Request sent", "tryst-invoice");
		echo '<script>alert("'.$alert_msg.'");location.href="'.get_page_link().'?tryst_invoice_hash='.$invoice->getFormKey().'"</script>';
	}
	public static function shortcode_request_form($atts = null) {
		global $tryst_plugin;
		ob_start();
		if(isset($_POST['invoice_mail_repeat']))
		return self::invoice_mail_repeat(sanitize_text_field($_POST['tryst_invoice_hash']));
		if(!empty($_POST['member']) && !empty($_POST['invoice'])){			
			self::request_post($atts);
		} 
		else {
			if(!empty($_GET['tryst_invoice_hash'])){
				$member = self::request_get(sanitize_text_field($_GET['tryst_invoice_hash']));
			} else {
				require plugin_dir_path( __FILE__ ).'templates/form-invoice-request.php';
			}		
		}
		return ob_get_clean();
	}
	private static function invoice_mail($invoice, $key){
		$mail = new Tryst_Email($invoice, $key);
		$mail->addRecipient($invoice->getMeta('guide'));
		$mail->addRecipient($invoice->getMember()->getEmail());
		return $mail->send();
	}
	public static function invoice_mail_repeat($hash){
		$invoice = Tryst\Invoice::findByFormKey($hash);
		self::invoice_mail($invoice, 'request');
		echo '<script>location.href="'.get_page_link().'?tryst_invoice_hash='.$invoice->getFormKey().'"</script>';
	}
	public function shortcodes_load(){
		add_shortcode( 'tryst_invoice_request_form', ['Tryst_Invoice_Public', 'shortcode_request_form'] );
	}
	public static function register_query_vars( $vars ) {
		$vars[] = 'tryst_invoice_hash';
		return $vars;
	}	
}
add_filter( 'query_vars', ['Tryst_Invoice_Public', 'register_query_vars'] );