<?php
class Tryst_Domain_Invoice_Admin_Meta{
	private static $invoice, $member;
	/**
	* Adds a metabox to the right side of the screen under the â€œPublishâ€ box
	*/
	public static function add_invoice_request_metaboxes() {
		global $post, $tryst_plugin;
		try{
			//loads the invoice on editor page
			self::$invoice = new \Tryst\Invoice($post->ID);
			self::$member = self::$invoice->getMember();
		} catch(\Exception $e){
			echo '<script>console.log('.$e->getMessage().')</script>';
		}
		//date
		add_meta_box(
		'guide',
		__('Guide', 'tryst'),
		['Tryst_Domain_Invoice_Admin_Meta', 'field_guide'],
		'invoice',
		'normal',
		'default'
		);
		//date
		add_meta_box(
		'amount',
		__('Amount', 'tryst-invoice'),
		['Tryst_Domain_Invoice_Admin_Meta', 'field_amount'],
		'invoice',
		'normal',
		'default'
		);
		//date
		add_meta_box(
		'message',
		__('Message', 'tryst-invoice'),
		['Tryst_Domain_Invoice_Admin_Meta', 'field_message'],
		'invoice',
		'normal',
		'default'
		);
	}
	//domain
	public static function field_guide( ) {
		?>
		<?php 
			$options = get_option('tryst_option');
			$emails = $options['guide_list'];
		?>
		<select name="invoice[guide]" id="guide" class="form-control">
			<option value="">Selecione o tipo</option>
			<?php 
			foreach($emails as $e => $setor): 
			if(self::$invoice->getMeta('guide') == $e)
			echo '<option value="'.$e.'" selected>'.$setor.'</option>';
			else
			echo '<option value="'.$e.'">'.$setor.'</option>';
			endforeach; 
			?>
		</select>
		<?php }
		public static function field_amount( ) {
			?>
			<input type="tel" class="form-control regular-text f-invoice" name="invoice[amount]" id="amount" value="<?php echo !empty(self::$invoice) ? self::$invoice->getMeta('amount') : ''; ?>">         
			<?php }
			public static function field_message( ) {
				?>
				<textarea class="form-control regular-text f-invoice" name="invoice[message]" id="message"><?php echo !empty(self::$invoice) ? self::$invoice->getMeta('message') : ''; ?></textarea>  
				<?php }
			}