<?php
/**
 * Ajax features
 *
 * Adds ajax features to the store: to Add button, to Remove, Update button in Shopping Cart and to Checkout process
 *
 * @package TheCartPress
 * @subpackage Modules
 */

/**
 * This file is part of TheCartPress.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'TCPAjax' ) ) {

class TCPAjax {
	function __construct() {
		add_action( 'tcp_init'	, array( $this, 'tcp_init' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}

	function tcp_init() {
		if ( thecartpress()->get_setting( 'activate_ajax', true ) ) {
			add_action( 'wp_footer'									, 'tcp_states_footer_scripts' );

			add_action( 'wp_ajax_tcp_shopping_cart_actions'			, array( $this, 'tcp_shopping_cart_actions' ) );
			add_action( 'wp_ajax_nopriv_tcp_shopping_cart_actions'	, array( $this, 'tcp_shopping_cart_actions' ) );
			add_action( 'wp_ajax_tcp_checkout'						, array( $this, 'tcp_checkout' ) );
			add_action( 'wp_ajax_nopriv_tcp_checkout'				, array( $this, 'tcp_checkout' ) );

			add_action( 'wp_print_scripts'							, array( $this, 'wp_enqueue_scripts' ) );
			add_filter( 'tcp_the_add_to_cart_button'				, array( $this, 'tcp_the_add_to_cart_button' ), 10, 2 );
			add_filter( 'tcp_the_add_to_cart_items_in_the_cart'		, array( $this, 'tcp_the_add_to_cart_items_in_the_cart' ), 99, 2 );
			add_action( 'wp_head'									, array( $this, 'wp_head' ) );
			add_filter( 'tcp_get_shopping_cart_summary'				, array( $this, 'tcp_get_shopping_cart_summary' ), 10, 2 );
			add_action( 'tcp_get_shopping_cart_widget'				, array( $this, 'tcp_get_shopping_cart_widget' ) );
			add_action( 'ecommerce_twentyeleven_header_total'		, array( $this, 'ecommerce_twentyeleven_header_total' ) );
			add_action( 'tcp_shopping_cart_after'					, array( $this, 'tcp_shopping_cart_after' ) );
			////add_filter( 'tcp_ckeckout_current_title'				, array( $this, 'tcp_ckeckout_current_title' ), 10, 2 );
			//add_filter( 'tcp_show_box_back_continue'				, array( $this, 'tcp_ckeckout_current_title' ), 10, 2 );
			//add_action( 'tcp_show_box'								, array( $this, 'tcp_show_box' ) );
			add_filter( 'tcp_get_the_thumbnail_with_permalink'		, array( $this, 'tcp_get_the_thumbnail_with_permalink' ), 10, 3 );
			add_filter( 'tcp_get_the_thumbnail'						, array( $this, 'tcp_get_the_thumbnail_with_permalink' ), 10, 3 );
		}
	}

	function admin_init() {
		add_action( 'tcp_main_settings_after_page'	, array( $this, 'tcp_main_settings_after_page' ) );
		add_filter( 'tcp_main_settings_action'	, array( $this, 'tcp_main_settings_action' ) );
	}

	function wp_enqueue_scripts() {
		wp_enqueue_script( 'query-ui-draggable' );
		wp_enqueue_script( 'jquery-ui-droppable' );
	}

	function tcp_get_the_thumbnail_with_permalink( $image, $post_id, $args ) {
		ob_start(); ?>
		<script type="text/javascript">
		jQuery(document).ready(function () {
			jQuery('.tcp_image_<?php echo $post_id; ?>').draggable({
				helper: 'clone',
				scope: 'products',
				create: function(event, ui) {
//					ui.helper.css('z-index', 999);
				}
			});
		});
		</script>
		<?php return $image . ob_get_clean();
	}

	function tcp_main_settings_after_page() {
		global $thecartpress;
		$activate_ajax = $thecartpress->get_setting( 'activate_ajax', true ); ?>
<h3><?php _e( 'Ajax', 'tcp' ); ?></h3>

<div class="postbox">
<div class="inside">
<table class="form-table">
<tbody>
<tr valign="top">
	<th scope="row">
		<label for="activate_ajax"><?php _e( 'Activate ajax', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="checkbox" id="activate_ajax" name="activate_ajax" value="yes" <?php checked( true, $activate_ajax ); ?> />
		<span class="description"><?php _e( 'Activate Ajax in the buy buttons, Shopping Cart and Checkout pages.', 'tcp' ); ?></span>
	</td>
</tr>
</tbody>
</table>
</div>
</div><!-- .postbox --> <?php
	}

	function tcp_main_settings_action( $settings ) {
		$settings['activate_ajax'] = isset( $_POST['activate_ajax'] ) ? $_POST['activate_ajax'] == 'yes' : false;
		return $settings;
	}

	function tcp_ckeckout_current_title( $title, $step ) { 
		return $title . '<img src="' . admin_url( 'images/loading.gif' ) . '" class="tcp_checkout_feedback" style="display: none;" />';
	}

	function tcp_show_box( $step ) { ?>
<script>
jQuery('#tcp_continue').click(function(event) {
	var feedback = jQuery('.tcp_checkout_feedback');
	var form = jQuery(this).closest('form');
	data = 'action=tcp_checkout&tcp_continue=&' + form.serialize();
	feedback.show();
	jQuery.ajax({
		async	: true,
		type    : "POST",
		url		: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
		data	: data,
		success : function(response) {
			feedback.hide();
			jQuery('#checkout').replaceWith(response);
		},
		error	: function(response) {
			feedback.hide();
		},
	});
	event.stopPropagation();
	return false;
});

jQuery('#tcp_back').click(function(event) {
	var feedback = jQuery('.tcp_checkout_feedback');
	var form = jQuery(this).closest('form');
	data = 'action=tcp_checkout&tcp_back=&' + form.serialize();
	feedback.show();
	jQuery.ajax({
		async	: true,
		type    : "POST",
		url		: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
		data	: data,
		success : function(response) {
			feedback.hide();
			jQuery('#checkout').replaceWith(response);
		},
		error	: function(response) {
			feedback.hide();
		},
	});
	event.stopPropagation();
	return false;
});

jQuery( 'h3.tcp_ckeckout_step a' ).click( function( event ) {
	var step = jQuery( this ).attr( 'tcp_step' );
	var feedback = jQuery( '.tcp_feedback' );
	data = 'action=tcp_checkout&step=' + step;
	feedback.show();
	jQuery.ajax({
		async	: true,
		type    : 'POST',
		url		: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
		data	: data,
		success : function( response ) {
			feedback.hide();
			jQuery( '#checkout' ).replaceWith( response );
		},
		error	: function( response ) {
			feedback.hide();
		},
	} );
	event.stopPropagation();
	return false;
} );
</script><?php
	}

	function tcp_shopping_cart_after() { ?>
<script>

//if ( ! jQuery.isFunction(window.tcp_listener_shopping_cart)) {
	tcpDispatcher.add('tcp_listener_shopping_cart', 0);

	function tcp_listener_shopping_cart() {
		jQuery.ajax({
			async	: true,
			type    : "GET",
			url		: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
			data	: {
				action	: 'tcp_shopping_cart_actions',
				to_do	: 'tcp_shopping_cart_page',
			},
			success : function(response) {
				var div = jQuery('.tcp_shopping_cart_page').replaceWith(response);
			},
			error	: function(response) {
			},
		});
	}
//}
</script><?php
	}

	function ecommerce_twentyeleven_header_total() { ?>
<img src="<?php echo admin_url( 'images/loading.gif' ); ?>" class="tcp_feedback" style="display: none;" />

<script>

//if ( ! jQuery.isFunction(window.tcp_listener_ecommerce_twentyeleven_header_total)) {
	tcpDispatcher.add('tcp_listener_ecommerce_twentyeleven_header_total', 0);

	function tcp_listener_ecommerce_twentyeleven_header_total() {
		var widget = jQuery('#ecommerce_twentyeleven_header_total');
		widget.find('.tcp_feedback').show();
		jQuery.ajax({
			async	: true,
			type    : "GET",
			url		: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
			data	: {
				action	: 'tcp_shopping_cart_actions',
				to_do	: 'get_total'
			},
			success : function(response) {
				widget.find('.tcp_feedback').hide();
				response += '<img src="<?php echo admin_url( 'images/loading.gif' ); ?>" class="tcp_feedback" style="display: none;" />';
				widget.html(response);
			},
			error : function(response) {
				widget.find('.tcp_feedback').hide();
			}
		});
	}
//}
</script><?php
	}

	function tcp_get_shopping_cart_widget( $args ) {
		$widget_id = isset( $args['widget_id'] ) ? 'tcp_' . str_replace( '-', '_', $args['widget_id'] ) : 'tcp_shopping_cart_detail'; ?>
<img src="<?php echo admin_url( 'images/loading.gif' ); ?>" class="tcp_feedback" style="display: none;" />

<script>

//if ( ! jQuery.isFunction(window.tcp_listener_<?php echo $widget_id; ?>)) {
	tcpDispatcher.add('tcp_listener_<?php echo $widget_id; ?>', 0);

	function tcp_listener_<?php echo $widget_id; ?>() {
		var widget = jQuery('#<?php echo $widget_id; ?>');
		widget.find('.tcp_feedback').show();
		//jQuery.getJSON( 
		jQuery.ajax({
			async	: true,
			type    : "GET",
			url		: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
			data	: {
				action	: 'tcp_shopping_cart_actions',
				to_do	: 'get_detail',
				args	: encodeURIComponent('<?php echo json_encode( $args ); ?>'),
			},
			success : function(response) {
				widget.find('.tcp_feedback').hide();
				widget.html(response);
			},
			error	: function(response) {
				widget.find('.tcp_feedback').hide();
			},
		});
	}

	jQuery(document).ready(function () {
		jQuery('#<?php echo $widget_id; ?>').droppable({
			scope: 'products',
			drop: function( event, ui ) {
				var ids = ui.draggable.attr('class');
				ids = ids.split(' ');
				for(i in ids) {
					if ( ids[i].substr(0, 10) == 'tcp_image_') {
						ids = ids[i].substr(10);
						data = 'action=tcp_shopping_cart_actions&to_do=add&tcp_add_to_shopping_cart=';
						data += '&tcp_count[]=1';
						data += '&tcp_post_id[]=' + ids;
						jQuery.ajax({
							async	: true,
							type    : "POST",
							url		: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
							data	: data,
							success : function(response) {
								tcpDispatcher.fire(0);
							},
						});
						break;
					}
				}
				ui.helper.remove();
			}
		});
	});
//}
</script><?php
	}

	function tcp_get_shopping_cart_summary( $out, $args ) {
		ob_start();
		$widget_id = isset( $args['widget_id'] ) ? 'tcp_' . str_replace( '-', '_', $args['widget_id'] ) : 'shopping_cart_summary'; ?>
<img src="<?php echo admin_url( 'images/loading.gif' ); ?>" class="tcp_feedback" style="display: none;" />
<script>
tcpDispatcher.add( 'tcp_listener_<?php echo $widget_id; ?>', 0 );

function tcp_listener_<?php echo $widget_id; ?>(){
	var widget = jQuery('#<?php echo $widget_id; ?>');
	widget.find('.tcp_feedback').show();
	jQuery.getJSON(
		'<?php echo admin_url( 'admin-ajax.php' ); ?>',
		{
			action : 'tcp_shopping_cart_actions',
			to_do : 'get_summary',
			args : encodeURIComponent('<?php echo json_encode( $args ); ?>'),
		}
	).done( function(response) {
		widget.find('.tcp_feedback').hide();
		widget.html(response);
	} ).fail( function(response) {
		widget.find('.tcp_feedback').hide();
	} );
}
jQuery(document).ready(function () {
	jQuery('#<?php echo $widget_id; ?>').droppable( {
		drop: function( event, ui ) {
			var ids = ui.draggable.attr( 'class' );
			ids = ids.split( ' ' );
			for( i in ids ) {
				if ( ids[i].substr( 0, 10 ) == 'tcp_image_') {
					ids = ids[i].substr( 10 );
					data = 'action=tcp_shopping_cart_actions&to_do=add&tcp_add_to_shopping_cart=';
					data += '&tcp_count[]=1';
					data += '&tcp_post_id[]=' + ids;
					jQuery.ajax( {
						async : true,
						type : "POST",
						url : "<?php echo admin_url( 'admin-ajax.php' ); ?>",
						data : data,
						success : function( response ) {
							tcpDispatcher.fire( 0 );
						},
					} );
					break;
				}
			}
			ui.helper.remove();
		}
	} );
} );
</script><?php
		$out = $out . ob_get_clean();
		return $out;
	}

	/**
	 * Defines the tcpdispatcher for ajax access to ShoppingCart
	 */
	function wp_head() { 
		if ( is_feed() ) return; ?>
<script>
function TCPDispatcher() {
	this.listeners = new Array();

	this.add = function(callback, post_id) {
		var listener = new Array(callback, post_id);
		this.listeners.push(listener);
	}

	this.fire = function(post_id) {
		for(i in this.listeners) {
			var listener = this.listeners[i];
			if ( post_id == 0 || listener[1] == 0 || post_id == listener[1] ) {
				window[listener[0]]();
			}
		}
	}
}

var tcpDispatcher = new TCPDispatcher();

jQuery(document).on( 'click', '.tcp_add_to_shopping_cart', function( event ) {
	var post_id = jQuery( this ).attr( 'target' );
	var feedback = jQuery( this ).next( '#tcp_buy_button_feedback_' + post_id );
	var form = jQuery( this ).closest( 'form' );
	if ( jQuery( this ).attr( 'class' ).indexOf( '_GROUPED' ) == -1)  {
		var tcp_count = form.find('#tcp_count_' + post_id);
		var val = tcp_count.val();
		if ( val == 0 ) val = 1;
		form.find( '.tcp_count' ).val( 0 );
		tcp_count.val( val );
	} else {
		post_id = 0;
	}
	data = 'action=tcp_shopping_cart_actions&to_do=add&tcp_add_to_shopping_cart=&' + form.serialize();
	feedback.show();

	jQuery.getJSON( "<?php echo admin_url( 'admin-ajax.php' ); ?>", data ).done( function( response ) {
		feedback.hide();
		tcpDispatcher.fire( post_id );
	} ).fail( function (error ) {
		feedback.hide();
		tcpDispatcher.fire( post_id );
	} );
	return false;
} );

jQuery( document ).on( 'click', '.tcp_delete_shopping_cart', function( event ) {
	var feedback = jQuery(this).closest( '.tcp_feedback' );
	feedback.show();
	jQuery.getJSON(
		"<?php echo admin_url( 'admin-ajax.php' ); ?>",
		{
			action : 'tcp_shopping_cart_actions',
			to_do : 'delete_shopping_cart',
			tcp_delete_shopping_cart : '',
		}
	).done( function( response ) {
		feedback.hide();
		tcpDispatcher.fire(0);
	} ).fail( function( response ) {
		feedback.hide();
		tcpDispatcher.fire( post_id );
	} );
	/*jQuery.ajax( {
		async : true,
		type : "GET",
		url : "<?php echo admin_url( 'admin-ajax.php' ); ?>",
		data : {
			action : 'tcp_shopping_cart_actions',
			to_do : 'delete_shopping_cart',
			tcp_delete_shopping_cart : '',
		},
		success : function( response ) {
			feedback.hide();
			tcpDispatcher.fire(0);
		},
		error : function( response ) {
			feedback.hide();
			tcpDispatcher.fire(post_id);
		},
	} );*/
	return false;
});

jQuery(document).on('click', '.tcp_delete_item_shopping_cart', function(event) {
	var feedback = jQuery(this).closest('.tcp_feedback');
	var form = jQuery(this).closest('form');
	data = 'action=tcp_shopping_cart_actions&to_do=delete_item&tcp_delete_item_shopping_cart=&' + form.serialize();
	
	jQuery.getJSON( "<?php echo admin_url( 'admin-ajax.php' ); ?>", data ).done( function( response ) {
		feedback.hide();
		tcpDispatcher.fire( 0 );
	} ).fail( function( response ) {
		feedback.hide();
	} );
	/*jQuery.ajax({
		async : true,
		type : "POST",
		url : "<?php echo admin_url( 'admin-ajax.php' ); ?>",
		data : data,
		success : function(response) {
			feedback.hide();
			tcpDispatcher.fire(0);
		},
		error : function( response ) {
			feedback.hide();
		},
	} );*/
	event.stopPropagation();
	return false;
});

jQuery(document).on('click', '.tcp_modify_item_shopping_cart', function(event) {
	var feedback = jQuery(this).closest('.tcp_feedback');
	var form = jQuery(this).closest('form');
	data = 'action=tcp_shopping_cart_actions&to_do=modify_item&tcp_modify_item_shopping_cart=&' + form.serialize();
	jQuery.getJSON(
		"<?php echo admin_url( 'admin-ajax.php' ); ?>",
		data
	).done( function( response ) {
		feedback.hide();
		tcpDispatcher.fire( 0 );
	} ).fail( function( response ) {
		feedback.hide();
	} );
/*	jQuery.ajax({
		async : true,
		type : "POST",
		url : "<?php echo admin_url( 'admin-ajax.php' ); ?>",
		data : data,
		success : function(response) {
			feedback.hide();
			tcpDispatcher.fire(0);
		},
		error : function(response) {
			feedback.hide();
		},
	});*/
	event.stopPropagation();
	return false;
});
</script><?php
	}

	function tcp_the_add_to_cart_button( $out, $post_id ) { 
		ob_start(); ?>
<img src="<?php echo admin_url( 'images/loading.gif' ); ?>" class="tcp_feedback" id="tcp_buy_button_feedback_<?php echo $post_id; ?>" style="display: none;" /><?php
		return $out . ob_get_clean();
	}

	function tcp_the_add_to_cart_items_in_the_cart( $out, $post_id ) {
		ob_start(); ?>
<script type="text/javascript">
tcpDispatcher.add( 'tcp_items_in_the_cart_<?php echo $post_id; ?>', <?php echo $post_id; ?> );

function tcp_items_in_the_cart_<?php echo $post_id; ?>() {
	jQuery.getJSON(
		"<?php echo admin_url( 'admin-ajax.php' ); ?>",
		{
			action : 'tcp_shopping_cart_actions',
			to_do : 'get_items_in_the_cart',
			post_id : <?php echo $post_id; ?>,
		} ).done( function( response ) {
			var tcp_added_product_title = jQuery( '.tcp_added_product_title_<?php echo $post_id; ?>' );
			tcp_added_product_title.replaceWith( response );
			tcp_added_product_title.show();
			<?php if ( tcp_is_downloadable( $post_id ) ) : ?>
			jQuery( '#tcp_add_to_shopping_cart_<?php echo $post_id; ?>' ).hide();
			<?php endif; ?>
		} ).fail( function( error ) {
			//alert( error.responseText );
		} );
}
</script>
		<?php $out = $out . ob_get_clean();
		return $out;
	}

	function tcp_shopping_cart_actions() {
		switch( $_REQUEST['to_do'] ) {
		case 'add' :
		case 'delete_shopping_cart' :
		case 'delete_item' :
		case 'modify_item' :
			/*global $thecartpress;
			$thecartpress->check_for_shopping_cart_actions();
			TheCartPress::saveShoppingCart();*/
			//exit( "1" );
			tcp_return_jsonp( "1" );
		case 'get_summary' :
			$args = (array)json_decode( urldecode( $_REQUEST['args'] ) );
			tcp_return_jsonp( tcp_get_shopping_cart_summary( $args, false ) );
		case 'get_detail' :
			$args = (array)json_decode( urldecode( $_REQUEST['args'] ) );
			exit( tcp_get_shopping_cart_detail( $args, false ) );
		case 'get_total' :
			exit( tcp_the_total() );
		case 'tcp_shopping_cart_page' :
			require_once( TCP_SHORTCODES_FOLDER . 'ShoppingCartPage.class.php' );
			exit( TCPShoppingCartPage::show() );
		case 'get_items_in_the_cart' :
			$post_id = $_REQUEST['post_id'];
			tcp_return_jsonp( tcp_the_add_to_cart_items_in_the_cart( $post_id, false ) );
		}
	}

	function tcp_checkout() {
		require_once( TCP_CHECKOUT_FOLDER . 'ActiveCheckout.class.php' );
		$activeCheckout = new ActiveCheckout();
		global $wp_query;
		$wp_query->in_the_loop = true;
		exit( $activeCheckout->show() );
	}
}

new TCPAjax();
} // class_exists check