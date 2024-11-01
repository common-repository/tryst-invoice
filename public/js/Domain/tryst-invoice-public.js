(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

     $(document).ready(function(){
        $('#contributor').on('change', function(){
            console.log($(this).val() );
            if($(this).val() == 'Empregado'){
                $('.w-company').addClass('d-none');
                $('input.w-company').val('');
                $('.w-employee').removeClass('d-none');
            } else {
                $('.w-company').removeClass('d-none');
				$('input.w-employee').val('');
                $('.w-employee').addClass('d-none');
            }
        });
     });

})( jQuery );
