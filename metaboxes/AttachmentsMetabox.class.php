<?php
/**
 * Attachment Metabox
 *
 * Adds a quick view of the attacahments files
 *
 * @package TheCartPress
 * @subpackage Metaboxes
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

if ( ! class_exists( 'TCPAttachmentsMetabox' ) ) {

class TCPAttachmentsMetabox {

	static function init() {
		wp_enqueue_script( 'jquery-ui-sortable' );

		add_action( 'admin_init'				, array( __CLASS__, 'register_metabox' ), 20 );
		add_action( 'wp_ajax_tcp_attachment_save'	, array( __CLASS__, 'tcp_attachment_save' ) );
	}

	static function register_metabox() {
		foreach( tcp_get_saleable_post_types() as $post_type )
			add_meta_box( 'tcp-images', __( 'Attachments' ), array( __CLASS__, 'showImagesMetabox' ), $post_type, 'normal', 'core' );
		//add_action( 'save_post', array( __CLASS__, 'save' ), 1, 2 );
		//add_action( 'delete_post', array( __CLASS__, 'delete' ) );
	}

	static function tcp_attachment_save() {
		$todo = isset( $_POST['todo'] ) ? $_POST['todo'] : false;
		if ( $todo == 'order' ) {
			$post_ids = isset( $_POST['post_id'] ) ? $_POST['post_id'] : false;
			$post = array();
			if ( is_array( $post_ids ) && count( $post_ids > 0 ) ) foreach ( $post_ids as $id => $post_id ) {
				$post['ID'] = $post_id;
				$post['menu_order'] = $id;
				wp_update_post( $post );
			}
		} elseif ( $todo == 'title' || $todo == 'caption' || $todo == 'description' ) {
			$post_id = isset( $_POST['post_id'] ) ? $_POST['post_id'] : false;
			$value = isset( $_POST['value'] ) ? $_POST['value'] : false;
			$post = array();
			$post['ID'] = $post_id;
			if ( $todo == 'title' ) $post['post_title'] = $value;
			elseif ( $todo == 'caption' ) $post['post_excerpt'] = $value;
			elseif ( $todo == 'description' ) $post['post_content'] = $value;
			wp_update_post( $post );
		} elseif ( $todo == 'alt' ) {
			$post_id = isset( $_POST['post_id'] ) ? $_POST['post_id'] : false;
			$value = isset( $_POST['value'] ) ? $_POST['value'] : false;
			update_post_meta( $post_id, '_wp_attachment_image_alt', $value );
		} elseif ( $todo == 'delete-image' ) {
			$post_id = isset( $_POST['post_id'] ) ? $_POST['post_id'] : false;
			wp_delete_attachment( $post_id, true );
		} elseif ( $todo == 'delete-from-post' ) {
			$post_id = isset( $_POST['post_id'] ) ? $_POST['post_id'] : false;
			wp_delete_post( $post_id, true );
		}
	}

	static function showImagesMetabox() {
		global $post;
		if ( ! tcp_is_saleable_post_type( $post->post_type ) ) return;
		if ( ! current_user_can( 'edit_post', $post->ID ) ) return;
		$args = array( 
			'post_parent' => $post->ID,
			'post_type' => 'attachment',
			'post_mime_type' => 'image',
			'output' => 'ARRAY_N',
			'orderby' => 'menu_order',
			'order' => 'ASC',
		);
		$attachments = get_children( $args );
		if ( $attachments ) {
			tcp_the_feedback_image( 'tcp-attachment-feedback', __( 'Saving...', 'tcp' ) ); ?>
			<div class="tcp-attachments">
			<?php foreach( $attachments as $attachment_id => $attachment ) { 
				$alt = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );
				$caption = $attachment->post_excerpt;
				$description = $attachment->post_content;
				$title = $attachment->post_title;
				$attachment_meta_data = wp_get_attachment_metadata( $attachment_id ); ?>
				<div post_id="<?php echo $attachment_id; ?>" class="tcp-attachment clearfix">
					<div class="tcp-attachment-image">
						<a href="<?php echo get_attachment_link( $attachment_id ); ?>" target="_blank"><?php echo wp_get_attachment_image( $attachment_id, array( 64, 64 ), false ); ?></a>
						<div class="tcp-attachment-width-height"><?php echo $attachment_meta_data['width']; ?>x<?php echo $attachment_meta_data['height']; ?></div>
					</div><!-- .tcp-attachment-image -->
					<div class="tcp-attachment-image-fields" style="margin-left: 8em;">
						<div class="tcp-attachment-title">
							<label for="tcp-attachment-title-<?php echo $attachment_id; ?>" class="tcp-label"><?php _e( 'Title', 'tcp' ); ?></label>
							<input type="text" value="<?php echo $title; ?>" name="tcp-attachment-title" id="tcp-attachment-title-<?php echo $attachment_id; ?>" size="30" post_id="<?php echo $attachment_id; ?>" field="title" />
							<?php tcp_the_feedback_image( 'tcp-attachment-title-' . $attachment_id . '-feedback', __( 'Saving...', 'tcp' ) ); ?>
							<a href="#" class="tcp-attachment-details" post_id="<?php echo $attachment_id; ?>"><?php _e( 'Details', 'tcp' ); ?></a>
							<!--| <a href="#" class="tcp-attachment-delete-from-post" post_id="<?php echo $attachment_id; ?>"><?php _e( 'Delete from post', 'tcp' ); ?></a>-->
							| <a href="#" class="tcp-attachment-delete-image" post_id="<?php echo $attachment_id; ?>"><?php _e( 'Delete image', 'tcp' ); ?></a>
						</div>
						<div class="tcp-attachment-more-fields tcp-attachment-more-fields-<?php echo $attachment_id; ?>" style="display:none;">
							<div class="tcp-attachment-caption">
								<label for="tcp-attachment-caption-<?php echo $attachment_id; ?>" class="tcp-label"><?php _e( 'Caption', 'tcp' ); ?></label>
								<input type="text" value="<?php echo $caption; ?>" name="tcp-attachment-caption" id="tcp-attachment-caption-<?php echo $attachment_id; ?>" size="30" post_id="<?php echo $attachment_id; ?>" field="caption"/>
								<?php tcp_the_feedback_image( 'tcp-attachment-caption-' . $attachment_id . '-feedback', __( 'Saving...', 'tcp' ) ); ?>
							</div>
							<div class="tcp-attachment-alt">
								<label for="tcp-attachment-alt-<?php echo $attachment_id; ?>" class="tcp-label"><?php _e( 'Alternate text', 'tcp' ); ?></label>
								<input type="text" value="<?php echo $alt; ?>" name="tcp-attachment-alt" id="tcp-attachment-alt-<?php echo $attachment_id; ?>" size="30" post_id="<?php echo $attachment_id; ?>" field="alt"/>
								<?php tcp_the_feedback_image( 'tcp-attachment-alt-' . $attachment_id . '-feedback', __( 'Saving...', 'tcp' ) ); ?>
							</div>
							<div class="tcp-attachment-description">
								<label for="tcp-attachment-description-<?php echo $attachment_id; ?>" class="tcp-label"><?php _e( 'Description', 'tcp' ); ?></label>
								<textarea name="tcp-attachment-description" id="tcp-attachment-description-<?php echo $attachment_id; ?>" rows="2" cols="30" post_id="<?php echo $attachment_id; ?>" field="description"><?php echo $description; ?></textarea>
								<?php tcp_the_feedback_image( 'tcp-attachment-description-' . $attachment_id . '-feedback', __( 'Saving...', 'tcp' ) ); ?>
							</div><!-- .tcp-attachment-more-fields -->
						</div>
					</div><!-- .tcp-attachment-image-fields -->
				</div><!-- .tcp-attachment -->
			<?php } ?>
			</div><!-- .tcp-attachments -->
<script>
jQuery( function() {
	jQuery( '.tcp-attachment-details' ).on( 'click', function( event ) {
		var post_id = jQuery( this ).attr( 'post_id' );
		if ( ! jQuery( '.tcp-attachment-more-fields-' + post_id ).is( ':visible' ) ) {
			jQuery( '.tcp-attachment-more-fields' ).hide();
			jQuery( '.tcp-attachment-more-fields-' + post_id ).show( 100 );
		} else {
			jQuery( '.tcp-attachment-more-fields-' + post_id ).hide( 100 );
		}
		event.preventDefault();
		return false;
	} );
	jQuery( '.tcp-attachments' ).sortable( {
		//placeholder: 'tcp-attachment-highlight',
		opacity: 0.5,
		items: '.tcp-attachment',
		axis: 'y',
		start: function(event, ui) {
		},
		stop: function(event, ui) {
			var post_ids = new Array();
			var orders = new Array();
			jQuery( '.tcp-attachments .tcp-attachment' ).each( function(i, li) {
				post_ids.push( jQuery( this ).attr( 'post_id' ) );
			} );
			var feedback = jQuery( '.tcp-attachment-feedback' );
			feedback.show();
			jQuery.ajax( {
				async : true,
				type : "POST",
				url : "<?php echo admin_url( 'admin-ajax.php' ); ?>",
				data : {
					action : 'tcp_attachment_save',
					todo : 'order',
					post_id : post_ids,
				},
				success : function(response) {
					feedback.hide();
				},
				error : function(response) {
					feedback.hide();
				},
			} );
		}
	} );
	jQuery( '.tcp-attachment-image-fields' ).on( 'focusout', 'input', function(event) { tcp_attachment_update( this ); } );
	jQuery( '.tcp-attachment-image-fields' ).on( 'focusout', 'textarea', function(event) { tcp_attachment_update( this ); } );

	jQuery( '.tcp-attachment-delete-from-post' ).on( 'click', function(event) { 
		var post_id = jQuery( this ).attr( 'post_id' );
		var feedback = jQuery( '.tcp-attachment-feedback' );
		feedback.show();
		jQuery.ajax( {
			async : true,
			type : "POST",
			url : "<?php echo admin_url( 'admin-ajax.php' ); ?>",
			data : {
				action : 'tcp_attachment_save',
				todo : 'delete-from-post',
				post_id : post_id,
			},
			success : function(response) {
				feedback.hide();
				jQuery( ".tcp-attachment[post_id='" + post_id + "']" ).remove();
			},
			error : function(response) {
				feedback.hide();
			},
		} );
		event.preventDefault();
		return false;
	} );
	jQuery( '.tcp-attachment-delete-image' ).on( 'click', function(event) { 
		var post_id = jQuery( this ).attr( 'post_id' );
		var feedback = jQuery( '.tcp-attachment-feedback' );
		feedback.show();
		jQuery.ajax( {
			async : true,
			type : "POST",
			url : "<?php echo admin_url( 'admin-ajax.php' ); ?>",
			data : {
				action : 'tcp_attachment_save',
				todo : 'delete-image',
				post_id : post_id,
			},
			success : function(response) {
				feedback.hide();
				jQuery( ".tcp-attachment[post_id='" + post_id + "']" ).remove();
			},
			error : function(response) {
				feedback.hide();
			},
		} );
		event.preventDefault();
		return false;
	} );

} );

function tcp_attachment_update( target ) {
	var target = jQuery( target );
	var post_id = target.attr( 'post_id' );
	var field = target.attr( 'field' );
	var value = target.val();
	var id = target.attr( 'id' );
	var feedback = jQuery( '.' + id + '-feedback' );
	if ( feedback ) feedback.show();
	jQuery.ajax( {
		async : true,
		type : "POST",
		url : "<?php echo admin_url( 'admin-ajax.php' ); ?>",
		data : {
			action : 'tcp_attachment_save',
			todo : field,
			post_id : post_id,
			value : value,
		},
		success : function(response) {
			feedback.hide();
		},
		error : function(response) {
			feedback.hide();
		},
	} );
}
</script>
			<?php do_action( 'tcp_images_metabox', $post );
		} else {
			_e( 'No attachments', 'tcp' );
		}
	}
}

add_action( 'tcp_init', 'TCPAttachmentsMetabox::init' );
} // class_exists check