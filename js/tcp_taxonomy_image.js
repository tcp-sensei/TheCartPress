jQuery().ready( function() {
	jQuery( '.tcp_add_taxonomy_image' ).live( 'click', function () {
		var button = jQuery( this );
		var term_id =  button.attr( 'termid' );
		var image_id = button.attr( 'imageid' );
		jQuery.ajax( {
			url			: tcp_ajax_url,
			type		: 'POST',
			data		: {
				'action'	: 'tcp_taxonomy_image_add',
				'term_id'	: term_id,
				'image_id'	: image_id,
			},
			cache: false,
			success: function ( response ) {
				if ( response == '1' ) {
					button.hide();
					button.next().show();
					jQuery( '#tcp_remove-' + term_id ).show();
					//jQuery( '#tcp_image-' + term_id ).html( '<img src="<?php echo plugins_url( 'images/tcp_icon_gray.png', dirname( __FILE__ ) ); ?>" />' );
				}
			}
		} );
		return false;
	} );
	jQuery( '.tcp_remove_taxonomy_image' ).live( 'click', function () {
		var button = jQuery( this );
		var term_id =  button.attr( 'termid' );
		jQuery.ajax( {
			url			: tcp_ajax_url,
			type		: 'POST',
			data		: {
				'action'	: 'tcp_taxonomy_image_remove',
				'term_id'	: term_id,
			},
			cache: false,
			success: function ( response ) {
				if ( response == '1' ) {
					button.hide();
					jQuery( '#tcp_remove-' + term_id ).hide();
					jQuery( '#tcp_image-' + term_id ).html( '<img src="' + tcp_icon_gray_image + '" />' );
				}
			}
		} );
		return false;
	} );
} );

//window.send_to_editor = function(html) {  
//alert( html );
	/*var image_url = jQuery('img',html).attr('src');
	jQuery('#logo_url').val(image_url);
	//tb_remove();
	jQuery('#upload_logo_preview img').attr('src',image_url);
	jQuery('#submit_options_form').trigger('click');*/
//}  