<?php
/**
 * Advanced comunications
 *
 * Adds email features to Orders edit, allowing to send, and saved, predefined emails
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.	If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'TCPAdvancedCommunication' ) ) {

define( 'TCP_EMAIL_POST_TYPE', 'tcp_email' );

class TCPAdvancedCommunication {

	static $order_id = false;
	static $order = false;
	
	static function init() {
		add_action( 'init'						, array( __CLASS__, 'register_post_type' ) );
		add_action( 'admin_init'				, array( __CLASS__, 'admin_init' ) );
		add_action( 'tcp_order_edit_metaboxes'	, array( __CLASS__, 'tcp_order_edit_metaboxes' ), 10, 2 );
	}

	static function register_post_type() {
		$labels = array(
			'name'					=> __( 'Emails', 'tcp' ),
			'singular_name' 		=> __( 'Email', 'tcp' ),
			'add_new'				=> __( 'Add New', 'tcp' ),
			'add_new_item'			=> __( 'Add New Email', 'tcp' ),
			'edit_item'				=> __( 'Edit Email', 'tcp' ),
			'new_item'				=> __( 'New Email', 'tcp' ),
			'all_items'				=> __( 'All Emails', 'tcp' ),
			'view_item'				=> __( 'View Email', 'tcp' ),
			'search_items'			=> __( 'Search Emails', 'tcp' ),
			'not_found'				=>	__( 'No Emails found', 'tcp' ),
			'not_found_in_trash'	=> __( 'No Emails found in Trash',  'tcp' ),
			'parent_item_colon'		=> '',
			'menu_name'				=> __( 'Emails', 'tcp' ),
		);
		$args = array(
			'labels'				=> $labels,
			'public'				=> false,
			'publicly_queryable'	=> false,
			'show_ui'				=> false,
			'show_in_menu'			=> false,
			'query_var'				=> true,
			'rewrite'				=> false,
			'capability_type'		=> 'page',
			'exclude_from_search'	=> true,
			'has_archive'			=> false, 
			'hierarchical'			=> false,
			'menu_position'			=> null,
			'supports'				=> array( 'title', 'editor', 'author', 'thumbnail' ),
		); 
		register_post_type( TCP_EMAIL_POST_TYPE, $args );
	}

	static function admin_init() {
		add_action( 'wp_ajax_tcp_advanced_comm', array( __CLASS__, 'tcp_advanced_comm' ) );
	}

	static function tcp_advanced_comm() {
		switch( $_REQUEST['to_do'] ) {
		case 'tcp_get_email_text' :
			TCPAdvancedCommunication::tcp_get_email_text();
			break;
		case 'tcp_send_email' :
			TCPAdvancedCommunication::tcp_send_email();
			break;
		case 'tcp_save_email' :
			TCPAdvancedCommunication::tcp_save_email();
			break;
		case 'tcp_get_notices' :
			TCPAdvancedCommunication::tcp_get_notices();
			break;
		case 'tcp_remove_notice' :
			TCPAdvancedCommunication::tcp_remove_notice();
			break;
		}
	}

	static function tcp_order_edit_metaboxes( $order_id, $order ) {
		TCPAdvancedCommunication::$order_id = $order_id;
		TCPAdvancedCommunication::$order = $order;
		
		add_meta_box( 'tcp_order_notification_metabox'	, __( 'Notice Manager', 'tcp' ), array( __CLASS__, 'tcp_orders_notificaton_metabox' ) , 'tcp-order-edit', 'normal', 'default' );
		add_meta_box( 'tcp_order_notifications_metabox'	, __( 'Notifications', 'tcp' ), array( __CLASS__, 'tcp_orders_notificatons_metabox' ) , 'tcp-order-edit', 'side', 'default' );
	}

	static function tcp_orders_notificaton_metabox() {
		$order_id = TCPAdvancedCommunication::$order_id;
		$order = TCPAdvancedCommunication::$order; ?>
<p>
	<label for="tcp_email_to_send"><?php _e( 'Emails', 'tcp' ); ?>: </label>
	<?php $emails = get_posts( array( 'post_type' => 'tcp_template', 'numberposts' => -1, 'fields' => 'ids' ) );
	if ( is_array( $emails ) && count( $emails ) > 0 ) : ?>
		<select id="tcp-email-to-send">
			<option value=""><?php _e( 'Free text', 'tcp-advanced' ); ?></option>
		<?php foreach( $emails as $post_id ) : ?>
			<option value="<?php echo $post_id; ?>"><?php echo get_the_title( $post_id); ?></option>
		<?php endforeach; ?>
		</select><?php tcp_the_feedback_image( 'tcp-load-text-feedback' ); ?>
		<span class="description"><?php _e( 'Create more notices visiting ', 'tcp' ); ?> <a href="edit.php?post_type=tcp_template"><?php _e( 'Notices administrator', 'tcp' ); ?></a></span>
		<script>
		jQuery( '#tcp-email-to-send' ).change( function ( event ) {
			var feedback = jQuery( '.tcp-load-text-feedback' );
			feedback.show();
			jQuery.ajax( {
				async : true,
				type : "POST",
				url : "<?php echo admin_url( 'admin-ajax.php' ); ?>",
				data : {
					action : 'tcp_advanced_comm',
					to_do : 'tcp_get_email_text',
					order_id : '<?php echo $order_id; ?>',
					post_id : jQuery( '#tcp-email-to-send' ).val(),
				},
				success : function(response) {
					feedback.hide();
					if ( typeof( tinymce ) == 'object' ) {
						tinyMCE.activeEditor.setContent( response );
					}
				},
				error : function(response) {
					feedback.hide();
				},
			});
			event.stopPropagation();
			return false;
		} );
		</script>
		<div class="tcp-email-subject">
			<label><?php _e( 'Subject', 'tcp' ); ?>: <input type="text" name="tcp_notice_subject" id="tcp_notice_subject" maxlength="255" class="widefat" value="<?php printf( __( 'Order from %s, Order ID: %s', 'tcp' ), htmlentities( get_bloginfo( 'name' ) ), $order_id ); ?>" />
			<label><?php _e( 'Send a copy to me', 'tcp' ); ?> <input type="checkbox" name="tcp_copy_to_me" id="tcp_copy_to_me" value="yes"/></label>
		</div>
		<div class="tcp-email-modify-text">
			<?php wp_editor( '', 'tcp-text-to-send' ); ?> 
		</div><!-- .tcp_email_modify_text -->
		<p>
			<input type="button" name="tcp-send-email" id="tcp-send-email" value="<?php _e( 'Save & Send', 'tcp' ); ?>" class="button-primary"/>
			<?php tcp_the_feedback_image( 'tcp-send-email-feedback' ); ?>
			<span id="tcp-sending" style="display: none;"><?php _e( 'Sending...', 'tcp' ); ?></span>
			<span id="tcp-error-sending" style="display: none;"><?php _e( 'Error sending', 'tcp' ); ?></span>
			<input type="button" name="tcp-save-email" id="tcp-save-email" value="<?php _e( 'Save', 'tcp' ); ?>" class="button-primary"/>
			<?php tcp_the_feedback_image( 'tcp-save-email-feedback' ); ?>
			<span id="tcp-saved" style="display: none;"><?php _e( 'Saved...', 'tcp' ); ?></span>
		</p>
		<script>
		jQuery( '#tcp-send-email' ).click( function ( event ) {
			var feedback = jQuery( '.tcp-send-email-feedback' );
			var tcp_copy_to_me = jQuery( '#tcp_copy_to_me' ).attr( 'checked' );
			feedback.show();
			jQuery.ajax( {
				async : true,
				type : "POST",
				url : "<?php echo admin_url( 'admin-ajax.php' ); ?>",
				data : {
					action : 'tcp_advanced_comm',
					to_do : 'tcp_send_email',
					order_id : '<?php echo $order_id; ?>',
					subject : jQuery( '#tcp_notice_subject' ).val(),
					copy_to_me : tcp_copy_to_me,
					text : tinymce.activeEditor.getContent(),
				},
				success : function( response ) {
					feedback.hide();
					if ( response == 'OK' ) jQuery( '#tcp-sending' ).show( 800).delay( 2000 ).hide( 800 );
					else jQuery( '#tcp-error-sending' ).show( 400).delay( 1000 ).hide( 400 );
					tcp_load_notices( <?php echo $order_id; ?> );
				},
				error : function( response ) {
					feedback.hide();
				},
			} );
			event.stopPropagation();
			return false;
		} );
		jQuery( '#tcp-save-email' ).click( function( event ) {
			var feedback = jQuery( '.tcp-save-email-feedback' );
			feedback.show();
			jQuery.ajax( {
				async : true,
				type : "POST",
				url : "<?php echo admin_url( 'admin-ajax.php' ); ?>",
				data : {
					action : 'tcp_advanced_comm',
					to_do : 'tcp_save_email',
					order_id : '<?php echo $order_id; ?>',
					subject : jQuery( '#tcp_notice_subject' ).val(),
					text : tinymce.activeEditor.getContent(),
				},
				success : function( response ) {
					feedback.hide();
					jQuery( '#tcp-saved' ).show( 800).delay( 2000 ).hide( 800 );
					tcp_load_notices( <?php echo $order_id; ?> );
				},
				error : function( response ) {
					feedback.hide();
				},
			} );
			event.stopPropagation();
			return false;
		} );
		</script>
	<?php else : ?>
		<p class="description"><?php _e( 'No Notices/Emails templates.', 'tcp' ); ?> <a href="edit.php?post_type=tcp_template"><?php _e( 'Create Notices', 'tcp' ); ?></a></p>
	<?php endif; ?>
</p>
	<?php }

	static function tcp_send_email() {
		$text		= isset( $_REQUEST['text'] ) ? $_REQUEST['text'] : false;
		$order_id	= isset( $_REQUEST['order_id'] ) ? $_REQUEST['order_id'] : false;
		$subject	= isset( $_REQUEST['subject'] ) ? $_REQUEST['subject'] : sprintf( __( 'Order ID: %s', 'tcp' ), $order_id );
		$copy_to_me = isset( $_REQUEST['copy_to_me'] ) ? $_REQUEST['copy_to_me'] : false;
		require_once( TCP_DAOS_FOLDER . 'Orders.class.php' );
		$order		= Orders::get( $order_id );
		$to			= isset( $_REQUEST['to'] ) ? $_REQUEST['to'] : $order->billing_email;
		global $thecartpress;
		$from		= $thecartpress->get_setting( 'from_email', 'no-response@thecartpress.com' );
		$headers	= 'MIME-Version: 1.0' . "\r\n";
		$headers	.= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers	.= 'From: ' . get_bloginfo( 'name' ) . ' <' . $from . ">\r\n";
		if ( $copy_to_me ) {
			global $thecartpress;
			$bcc = $thecartpress->get_setting( 'emails', false );
			if ( $bcc !== false ) $headers .= 'Bcc: ' . $bcc . "\r\n";
		}
		TCPAdvancedCommunication::tcp_save_email( $subject );
		if ( wp_mail( $to, $subject, $text, $headers ) ) {
			die( 'OK' );
		} else {
			die( 'error sending' );
		}
	}

	static function tcp_save_email( $title = '' ) {
		$text		= isset( $_REQUEST['text'] ) ? $_REQUEST['text'] : false;
		$order_id	= isset( $_REQUEST['order_id'] ) ? $_REQUEST['order_id'] : false;
		$created_at	= current_time( 'mysql' );
		$subject	= isset( $_REQUEST['subject'] ) ? $_REQUEST['subject'] : $title;
		$notice		= array(
			'post_type'		=> TCP_EMAIL_POST_TYPE,
			'post_title'	=> $subject,
			'post_content'	=> $text,
			'post_status'	=> 'publish',
			'post_parent'	=> $order_id,
		);
		$post_id = wp_insert_post( $notice );
		return $post_id;
	}

	static function tcp_remove_notice() {
		$post_id = isset( $_REQUEST['post_id'] ) ? $_REQUEST['post_id'] : false;
		if ( $post_id === false ) die( 'Error, no post id param' );
		if ( wp_delete_post( $post_id, true ) )	die( 'OK' );
		else die( 'Error deleting notice' );
	}

	static function tcp_get_email_text() {
		$post_id = isset( $_REQUEST['post_id'] ) ? $_REQUEST['post_id'] : false;
		$order_id = isset( $_REQUEST['order_id'] ) ? $_REQUEST['order_id'] : false;
		if ( $post_id !== false && $order_id !== false ) {
			require_once( TCP_DAOS_FOLDER . 'Orders.class.php' );
			TCPAdvancedCommunication::$order_id = $order_id;
			TCPAdvancedCommunication::$order = Orders::get( $order_id );
			$content = apply_filters( 'the_content', get_post_field( 'post_content', $post_id ) );
			$content = preg_replace_callback( '/\{(.*?)\}/', array( __CLASS__, 'tcp_email_get_value' ), $content );
			die( $content );
		} else {
			die( 'error:' . $post_id . ',' . $order_id );
		}
	}

	static function tcp_email_get_value( $keys ) {
		if ( isset( TCPAdvancedCommunication::$order->$keys[1] ) ) {
			return TCPAdvancedCommunication::$order->$keys[1];
		} elseif ( $keys[1] == 'site-title' ) {
			return get_bloginfo();
		} elseif ( $keys[1] == 'site-url' ) {
			return home_url();
		} elseif ( $keys[1] == 'total' ) {
			return tcp_format_the_price( Orders::getTotal( TCPAdvancedCommunication::$order_id ) );
		} else {
			return sprintf( '(unknow %s)', $keys[1] );
		}
	}

	static function tcp_orders_notificatons_metabox() {
		$order_id = TCPAdvancedCommunication::$order_id;
		$order = TCPAdvancedCommunication::$order; ?>
<ul id="tcp-notifications">
</ul>

<li id="tcp-notification" style="display: none">
	<a href="#" class="tcp-saved-notice-title"></a> | <a href="#" class="tcp-saved-notice-remove"><span><?php _e( 'remove', 'tcp' ); ?></span></a>
	<div class="tcp-saved-notice-content" style="display: none"></div>
</li>

<script>
function tcp_load_notices( order_id ) {
	var ul = jQuery( '#tcp-notifications' );
	var feedback = jQuery( '.tcp-get-notices-feedback' );
	feedback.show();
	jQuery.ajax( {
		async : true,
		type : "POST",
		url : "<?php echo admin_url( 'admin-ajax.php' ); ?>",
		data : {
			action : 'tcp_advanced_comm',
			to_do : 'tcp_get_notices',
			order_id : order_id,
		},
		success : function( response ) {
			feedback.hide();
			ul.empty();
			var alternate = false;
			var notices = jQuery.parseJSON( response );
			for( var i in notices ) {
				var notice = notices[i];
				var item = jQuery( 'li#tcp-notification' ).clone().attr( 'id', '' );
				item.find( '.tcp-saved-notice-title' ).attr( 'title', notice['created_at'] ).html( notice['title'] );
				item.find( '.tcp-saved-notice-remove' ).attr( 'rel-data', notice['post_id'] );
				item.find( '.tcp-saved-notice-content' ).html( notice['content'] );
				if ( alternate ) item.addClass( 'alternate' );
				alternate = ! alternate;
				ul.append( item.show() );
			}
		},
		error : function( response ) {
			feedback.hide();
		},
	} );
}

jQuery().ready( function () {
	jQuery( '#tcp-notifications' ).on( 'click', '.tcp-saved-notice-title', function( event ) {
		jQuery( this ).parent().find( '.tcp-saved-notice-content' ).toggle( 200 );
		event.stopPropagation();
		return false;
	} );

	jQuery( '#tcp-notifications' ).on( 'click', '.tcp-saved-notice-remove', function( event ) {
		if ( confirm( '<?php _e( 'Do you really want to remove this notice?', 'tcp' ); ?>' ) ) {
			var remove = jQuery( this );
			var post_id = remove.attr( 'rel-data');
			var feedback = jQuery( '.tcp-get-notices-feedback' );
			feedback.show();
			jQuery.ajax( {
				async : true,
				type : "POST",
				url : "<?php echo admin_url( 'admin-ajax.php' ); ?>",
				data : {
					action : 'tcp_advanced_comm',
					to_do : 'tcp_remove_notice',
					post_id : post_id,
				},
				success : function( response ) {
					feedback.hide();
					if ( response == 'OK' ) remove.parent().remove();
				},
				error : function( response ) {
					feedback.hide();
				},
			} );
		}
		event.stopPropagation();
		return false;
	} );
	tcp_load_notices( <?php echo $order_id; ?> );
} );
</script>
	<?php }

	static function tcp_get_notices() {
		$order_id = isset( $_REQUEST['order_id'] ) ? $_REQUEST['order_id'] : false;
		if ( $order_id === false ) die( 'Error, no order id.' );
		$results = array();
		$args = array(
			'post_type'		=> TCP_EMAIL_POST_TYPE,
			'post_parent'	=> $order_id,
			'posts_per_page'=> -1,
			'fields'		=> 'ids',
		);
		$posts = get_posts( $args );
		foreach( $posts as $post_id ) {
			$post = get_post( $post_id );
			$results[$post_id] = array(
				'post_id'		=> $post_id,
				'title'			=> $post->post_title,
				'created_at'	=> $post->post_date,
				'author'		=> $post->post_author,
				'content'		=> $post->post_content,
			);
		}
		die( json_encode( $results ) );
	}
}

TCPAdvancedCommunication::init();
} // class_exists check