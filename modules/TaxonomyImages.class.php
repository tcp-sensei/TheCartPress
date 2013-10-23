<?php
/**
 * Taxonomy Images
 *
 * Allows to add an image to taxonomy
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

if ( ! class_exists( 'TCPTaxonomyImages' ) ) {

class TCPTaxonomyImages {

	function __construct() {
		add_action( 'admin_init'				, array( $this, 'admin_init' ) );
		add_shortcode( 'tcp_the_taxonomy_list'	, array( $this, 'tcp_the_taxonomy_list_shortcode' ) );
	}

	function admin_init() {
		add_action( 'admin_print_scripts-edit-tags.php'			, array( $this, 'admin_print_scripts_edit_tags' ) );
		add_action( 'admin_print_styles-edit-tags.php'			, array( $this, 'admin_print_styles_edit_tags' ) );
		add_action( 'admin_print_scripts-media-upload-popup'	, array( $this, 'admin_print_scripts_media_upload_popup' ) );
		$taxonomies = get_taxonomies( array( 'public' => true ), 'names' );
		foreach ( $taxonomies as $taxonomy ) {
			add_filter( 'manage_edit-' . $taxonomy . '_columns'		, array( $this, 'manages_edit_columns' ) );
			add_filter( 'manage_' . $taxonomy . '_custom_column'	, array( $this, 'manage_custom_columns' ), 20, 3 );
			//add_action( $taxonomy . '_edit_form_fields', array( &$this, 'edit_form_fields' ), 10, 2 );
		}
		add_action( 'wp_ajax_tcp_taxonomy_image_add'	, array( $this, 'tcp_taxonomy_image_add' ) );
		add_action( 'wp_ajax_tcp_taxonomy_image_remove'	, array( $this, 'tcp_taxonomy_image_remove' ) );
		add_filter( 'attachment_fields_to_edit'			, array( $this, 'attachment_fields_to_edit' ), 20, 2 );
	}

	function tcp_the_taxonomy_list_shortcode( $atts ) {
		return tcp_the_taxonomy_list( $atts, '', '', false );
	}

	function admin_print_scripts_edit_tags() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_script( 'media-upload' );
		$this->admin_print_scripts_media_upload_popup();
	}

	function admin_print_styles_edit_tags() {
		wp_enqueue_style( 'thickbox' );
	}

	function admin_print_scripts_media_upload_popup() { ?>
<script>
	var tcp_ajax_url = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
	var tcp_icon_gray_image = '<?php echo plugins_url( 'images/tcp_icon_gray.png', dirname( __FILE__ ) ); ?>';
</script><?php
		wp_enqueue_script( 'tcp_taxonomy_images',  plugins_url( 'thecartpress/js/tcp_taxonomy_image.js' ) );
	}

	function manages_edit_columns( $defaults ) {
		return array( 'tcp_taxonomy_image' => __( 'Image', 'tcp' ) ) + $defaults;
	}
	
	function manage_custom_columns( $row, $column_name, $term_id ) {
		if ( 'tcp_taxonomy_image' == $column_name ) {
			global $taxonomy;
			return $row . $this->get_taxonomy_image_edit_options( $term_id, $taxonomy );
		}
		return $row;
	}

	function get_taxonomy_image_edit_options( $term_id, $taxonomy ) {
		$term = get_term( $term_id, $taxonomy );
		$attachment_id = tcp_get_taxonomy_image_id( $term_id );
		ob_start(); ?>
		<a class="thickbox tcp_taxonomy_image_thumbnail" id="tcp_image-<?php echo $term_id; ?>" href="<?php echo admin_url( 'media-upload.php' ); ?>?type=image&tab=library&post_id=0&term_id=<?php echo $term_id; ?>&TB_iframe=true" title="<?php _e( 'Edit', 'tcp' ); ?>"><?php echo tcp_get_taxonomy_image( $term_id, true ); ?></a>
		<div id="tcp_taxonomy_image-<?php echo $term_id; ?>" class="tcp_taxonomy_image hide-if-no-js">
		<a class="control upload thickbox" href="<?php echo admin_url( 'media-upload.php' ); ?>?type=image&tab=type&post_id=0&term_id=<?php echo $term_id; ?>&TB_iframe=true"><?php _e( 'Upload', 'tcp' ); ?></a>
		<a class="control tcp_remove" href="#" id="tcp_remove-<?php echo $term_id; ?>" rel="<?php echo( $term_id ); ?>"><?php _e( 'Delete', 'tcp' ); ?></a>
		<script type="text/javascript">
		<?php if ( $attachment_id == 0 ) : ?>
			jQuery( '#tcp_remove-<?php echo $term_id; ?>' ).hide();
		<?php else : ?>
			jQuery( '.tcp_taxonomy_image #tcp_remove-<?php echo $term_id; ?>' ).live( 'click', function () {
				//var term_id = jQuery( this ).attr( 'rel' );
				jQuery.ajax( {
					url			: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
					type		: 'POST',
					data		: {
						'action'	: 'tcp_taxonomy_image_remove',
						'term_id'	: '<?php echo $term_id; ?>', //term_id,
					},
					cache: false,
					success: function ( response ) {
						if ( response == '1' ) {
							jQuery( '#tcp_remove-<?php echo $term_id; ?>' ).hide();
							jQuery( '#tcp_image-<?php echo $term_id; ?>' ).html( '<img src="<?php echo plugins_url( 'images/tcp_icon_gray.png', dirname( __FILE__ ) ); ?>" />' );
						}
					}
				} );
				return false;
			} );
		<?php endif; ?>
		</script>
		</div>
		<?php return ob_get_clean();
	}

	function tcp_taxonomy_image_add() {
		$term_id = isset( $_POST['term_id'] ) ? $_POST['term_id'] : false;
		$image_id = isset( $_POST['image_id'] ) ? $_POST['image_id'] : false;
		if ( $term_id !== false && $image_id !== false ) {
			tcp_set_taxonomy_image( $term_id, $image_id );
		}
		die('1');
	}

	function tcp_taxonomy_image_remove() {
		$term_id = isset( $_POST['term_id'] ) ? $_POST['term_id'] : false;
		if ( $_POST['term_id'] !== false ) {
			tcp_delete_taxonomy_image( $term_id );
		}
		die('1');
	}
	function attachment_fields_to_edit( $fields, $post ) {
		if ( isset( $fields['image-size'] ) && isset( $post->ID ) && isset( $_GET['term_id'] ) ) {
			ob_start(); ?>
		<div class="tcp_taxonomy_image" id="tcp_taxonomy_image-<?php //echo $image_id; ?>">
			<span class="button tcp_add_taxonomy_image" termid="<?php echo $_GET['term_id']; ?>" imageid="<?php echo $post->ID; ?>"><?php _e( 'Assign to Term', 'tcp' ); ?></span>
			<span class="button tcp_remove_taxonomy_image" termid="<?php echo $_GET['term_id']; ?>" imageid="<?php echo $post->ID; ?>"><?php _e( 'Remove from term ', 'tcp' ); ?></span>
		</div><?php
			$fields['image-size']['extra_rows']['tcp_taxonomy_image_plugin_button']['html'] = ob_get_clean();
		}
		return $fields;
	}
}

new TCPTaxonomyImages();

function tcp_set_taxonomy_image( $term_id, $attachment_id ) {
	$images = get_option( 'tcp_taxonomy_images', array() );
	$images[$term_id] = $attachment_id;
	update_option( 'tcp_taxonomy_images', $images );
}

function tcp_get_taxonomy_image( $term_id, $default = false ) {
	$attachment_id = tcp_get_taxonomy_image_id( $term_id );
	if ( $attachment_id > 0 ) {
		return wp_get_attachment_image( $attachment_id );
	} elseif ( $default) {
		return '<img class="tcp_taxonomy_image_' . $term_id . '" src="' . plugins_url( 'images/tcp_icon_gray.png', dirname( __FILE__ ) ) .'" />';
	} else {
		return false;
	}
}

function tcp_get_taxonomy_image_id( $term_id ) {
	$images = get_option( 'tcp_taxonomy_images', array() );
	if ( isset( $images[$term_id] ) ) return $images[$term_id];
	return false;
}

function tcp_delete_taxonomy_image( $term_id ) {
	$images = get_option( 'tcp_taxonomy_images', array() );
	unset( $images[$term_id] );
	update_option( 'tcp_taxonomy_images', $images );
}

function tcp_the_taxonomy_list( $args = array(), $before = '', $after = '', $echo = true ) {
	$defaults = array(
		'child_of'	=> 0,
		'parent'	=> '',
		'orderby'	=> 'name',
		'order'		=> 'ASC',
		'hide_empty'=> 1,
		'taxonomy'	=> TCP_PRODUCT_CATEGORY,
	);
	$args = wp_parse_args( $args, $defaults );
	$terms = get_terms( $args['taxonomy'], $args );
	ob_start();
	foreach ( $terms as $term ) : ?>
		<?php echo $before; ?>
		<a href="<?php echo get_term_link( $term->slug, $term->taxonomy ); ?>" title="<?php echo $term->name; ?>"><?php echo tcp_get_taxonomy_image( $term->term_id ); ?></a>
		<?php echo $after; ?>
	<?php endforeach;
	$out = ob_get_clean();
	if ( $echo ) echo $out;
	else return $out;
}
} // class_exists check