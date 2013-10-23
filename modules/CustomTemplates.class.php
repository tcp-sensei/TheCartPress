<?php
/**
 * Custom Template
 *
 * Allows to set templates to the different WordPress parts of a site: archives, single, taxonomyes, terms, etc.
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

class TCPCustomTemplates {

	function __construct() {
		if ( is_admin() ) new TCPCustomTemplateMetabox();
		add_filter( 'single_template'	, array( __CLASS__, 'single_template' ) );
		add_filter( 'page_template'		, array( __CLASS__, 'single_template' ) );
		add_filter( 'taxonomy_template'	, array( __CLASS__, 'taxonomy_template' ) );
		add_filter( 'category_template'	, array( __CLASS__, 'taxonomy_template' ) );
		add_filter( 'archive_template'	, array( __CLASS__, 'archive_template' ) );
	}

	static function single_template( $single_template ) {
		global $post;
		$template = tcp_get_custom_template( $post->ID );
		if ( $template && file_exists( $template ) ) return apply_filters( 'tcp_single_template', $template );

		$template = tcp_get_custom_template_by_post_type( $post->post_type );
		if ( $template && file_exists( $template ) ) return apply_filters( 'tcp_single_template', $template );

		return apply_filters( 'tcp_single_template', $single_template );
	}

	static function taxonomy_template( $taxonomy_template ) {
		if ( function_exists( 'get_queried_object' ) ) {
			$term = get_queried_object();
			if ( $term ) {
			$taxonomy = $term->taxonomy;
				$template = tcp_get_custom_template_by_term( $term->term_id );
				if ( $template && file_exists( $template ) ) return apply_filters( 'tcp_taxonomy_template', $template );
			}
		}
		if ( empty( $taxonomy ) ) global $taxonomy;
		$template = tcp_get_custom_template_by_taxonomy( $taxonomy );
		if ( $template && file_exists( $template ) ) return apply_filters( 'tcp_taxonomy_template', $template );
		return apply_filters( 'tcp_taxonomy_template', $taxonomy_template );
	}

	static function archive_template( $archive_template ) {
		global $post;
		if ( ! $post ) return;
		//$template = tcp_get_custom_template_by_post_type( $post->post_type );
		$template = tcp_get_custom_archive_by_post_type( $post->post_type );
		if ( $template && file_exists( $template ) ) return apply_filters( 'tcp_archive_template', $template );
		return apply_filters( 'tcp_archive_template', $archive_template );
	}
}

new TCPCustomTemplates();

class TCPCustomTemplateMetabox {

	function register_metabox() {
		$saleable_post_types = tcp_get_saleable_post_types();
		if ( is_array( $saleable_post_types ) && count( $saleable_post_types ) )
			foreach( $saleable_post_types as $post_type )
				add_meta_box( 'tcp-custom-templates', __( 'Custom templates', 'tcp' ), array( $this, 'show' ), $post_type, 'side' );
		add_action( 'save_post'		, array( $this, 'save' ), 1, 2 );
		add_action( 'delete_post'	, array( $this, 'delete' ) );
	}

	function show() {
		global $post;
		$post_id = tcp_get_default_id( $post->ID, get_post_type( $post->ID ) );
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;

		$templates			= tcp_get_custom_templates();
		$custom_template	= tcp_get_custom_template( $post_id );
		if ( is_array( $templates ) && count( $templates ) > 0 ) : ?>
			<?php wp_nonce_field( 'tcp_ct_noncename', 'tcp_ct_noncename' );?>
		<p>
			<label for="tcp_custom_template"><?php _e( 'Custom Template', 'tcp' ); ?></label>:
			<select name="tcp_custom_template" id="tcp_custom_template">
				<option value="" <?php selected( ! $custom_template );?>><?php _e( 'Default Template', 'tcp' ); ?></option>
				<?php foreach( $templates as $template => $file_name ) : ?>
				<option value="<?php echo $template;?>" <?php selected( $custom_template, $template );?>><?php echo $file_name;?></option>
				<?php endforeach;?>
			</select>
		</p>
		<!--<p>
			<?php $custom_template = tcp_get_custom_template_by_post_type( $post->post_type );
			$post_type = get_post_type_object( $post->post_type );
			if ( $post_type ) $post_type_name = $post_type->labels->name;?>
			<label for="tcp_custom_post_type_template"><?php printf( __( 'Custom Template for <strong>%s</strong>', 'tcp' ), $post_type_name ); ?></label>:
			<select name="tcp_custom_post_type_template" id="tcp_custom_post_type_template">
				<option value="" <?php selected( ! $custom_template );?>><?php _e( 'Default Template', 'tcp' ); ?></option>
				<?php foreach( $templates as $template => $file_name ) : ?>
				<option value="<?php echo $template;?>" <?php selected( $custom_template, $template );?>><?php echo $file_name;?></option>
				<?php endforeach;?>
			</select>
		</p>-->
		<?php else : ?>
			<p><?php _e( 'No templates', 'tcp' );?></p>
		<?php endif;
	}

	function save( $post_id, $post ) {
		if ( ! wp_verify_nonce( isset( $_POST['tcp_ct_noncename'] ) ? $_POST['tcp_ct_noncename'] : '', 'tcp_ct_noncename' ) ) return array( $post_id, $post );
		if ( ! current_user_can( 'edit_post', $post_id ) ) return array( $post_id, $post );
		$post_id = tcp_get_default_id( $post_id, $post->post_type );
		$template = isset( $_POST['tcp_custom_template'] ) ? $_POST['tcp_custom_template'] : '';
		tcp_set_custom_template( $post_id, $template );
		//$template = isset( $_POST['tcp_custom_post_type_template'] ) ? $_POST['tcp_custom_post_type_template'] : false;
		//if ( $template !== false ) tcp_set_custom_template_by_post_type( $post->post_type, $template );
		do_action( 'tcp_custom_template_metabox_save', $post );
	}

	function delete( $post_id ) {
		$post = get_post( $post_id );
		if ( ! wp_verify_nonce( isset( $_POST['tcp_noncename'] ) ? $_POST['tcp_noncename'] : '', 'tcp_ct_noncename' ) ) return $post_id;
		if ( ! current_user_can( 'edit_post', $post_id ) ) return $post_id;
		$post_id = tcp_get_default_id( $post_id, $post->post_type );
		tcp_set_custom_template( $post_id );
		tcp_set_custom_template_by_post_type( $post->post_type );
		do_action( 'tcp_custom_template_metabox_delete', $post );
	}

	function __construct() {
		add_action( 'admin_init', array( $this, 'register_metabox' ) );
	}
}

function tcp_get_custom_templates() {
	if ( function_exists( 'wp_get_themes' ) ) {//WP 3.4+
		$theme = wp_get_theme();
		$templates = array_flip( $theme->get_page_templates() );
		foreach( $templates as $id => $template ) {
			if ( is_file( $theme->get_template_directory() . '/' . $template ) ) {
				$templates[$id] = $theme->get_template_directory() . '/' . $template;
			} elseif ( is_file( $theme->get_stylesheet_directory() . '/' . $template ) ) {
				$templates[$id] = $theme->get_stylesheet_directory() . '/' . $template;
			}
		}
	} else {
		$themes = get_themes();
		$theme = get_current_theme();
		$templates = $themes[$theme]['Template Files'];
	}
	$custom_templates = array();
	if ( is_array( $templates ) ) {
		$base = array( trailingslashit( get_template_directory() ), trailingslashit( get_stylesheet_directory() ) );
		foreach( $templates as $template ) {
			$basename = str_replace( $base, '', $template );
			$template_data = implode( '', file( $template ));
			if ( preg_match( '|Template Name:(.*)$|mi', $template_data, $name ) ) {
				$name = _cleanup_header_comment( $name[1] );
				$custom_templates[$template] = $name;
			}
		}
	}
	return $custom_templates;
}

function tcp_get_custom_template( $post_id ) {
	return get_post_meta( $post_id, 'tcp_custom_template', true );
}

function tcp_set_custom_template( $post_id, $template = '' ) {
	delete_post_meta( $post_id, 'tcp_custom_template' );
	if ( ! $template || $template == '' ) return;
	update_post_meta( $post_id, 'tcp_custom_template', $template );
}

function tcp_set_custom_template_by_post_type( $post_type, $template = '' ) {
	$templates = get_option( 'tcp_post_type_templates', false );
	if ( $templates ) {
		if ( ! $template || $template == '') {
			if ( isset( $templates[$post_type] ) ) unset( $templates[$post_type] );
		} else {
			$templates[$post_type] = $template;
		}
		update_option( 'tcp_post_type_templates', $templates );
	} else {
		update_option( 'tcp_post_type_templates', array( $post_type => $template ) );
	}
}

function tcp_get_custom_template_by_post_type( $post_type ) {
	$templates = get_option( 'tcp_post_type_templates', false );
	if ( $templates && is_array( $templates ) ) return isset( $templates[$post_type] ) ? $templates[$post_type] : false;
}

function tcp_set_custom_archive_by_post_type( $post_type, $archive = '') {
	$templates = get_option( 'tcp_post_type_archives', false );
	if ( $templates ) {
		if ( ! $archive || $archive == '') {
			if ( isset( $templates[$post_type] ) ) unset( $templates[$post_type] );
		} else {
			$templates[$post_type] = $archive;
		}
		update_option( 'tcp_post_type_archives', $templates );
	} else {
		update_option( 'tcp_post_type_archives', array( $post_type => $templates ) );
	}
}

function tcp_get_custom_archive_by_post_type( $post_type ) {
	$archives = get_option( 'tcp_post_type_archives', false );
	if ( $archives && is_array( $archives ) ) return isset( $archives[$post_type] ) ? $archives[$post_type] : false;
}

function tcp_set_custom_template_by_taxonomy( $taxonomy, $template = '' ) {
	$templates = get_option( 'tcp_taxonomy_templates', false );
	if ( $templates ) {
		if ( ! $template || $template == '') {
			if ( isset( $templates[$taxonomy] ) ) unset( $templates[$taxonomy] );
		} else {
			$templates[$taxonomy] = $template;
		}
		update_option( 'tcp_taxonomy_templates', $templates );
	} else {
		update_option( 'tcp_taxonomy_templates', array( $taxonomy => $template ) );
	}
}

function tcp_get_custom_template_by_taxonomy( $taxonomy ) {
	$templates = get_option( 'tcp_taxonomy_templates', false );
	if ( is_array( $templates ) && count( $templates ) > 0 ) {
		if ( isset( $templates[$taxonomy] ) ) return $templates[$taxonomy];
	}
	return false;
}

function tcp_set_custom_template_by_term( $term_id, $template = '' ) {
	$templates = get_option( 'tcp_taxonomy_term_templates', false );
	if ( $templates ) {
		if ( ! $template || $template == '') {
			if ( isset( $templates[$term_id] ) ) unset( $templates[$term_id] );
		} else {
			$templates[$term_id] = $template;
		}
		update_option( 'tcp_taxonomy_term_templates', $templates );
	} else {
		update_option( 'tcp_taxonomy_term_templates', array( $term_id => $template ) );
	}
}

function tcp_get_custom_template_by_term( $term_id ) {
	$templates = get_option( 'tcp_taxonomy_term_templates', false );
	if ( is_array( $templates ) && count( $templates ) > 0 ) {
		if ( isset( $templates[$term_id] ) ) return $templates[$term_id];
	}
	return false;
}

//Archives, taxonomies and singles
function tcp_get_custom_files( $prefix ) {
	$archives = array();
	$theme = wp_get_theme();
	$dir = $theme->get_stylesheet_directory();
	$folder = dir( $dir );
	while ( false !== ( $file = $folder->read() ) )
		if ( substr( $file, 0, strlen( $prefix ) ) == $prefix )
			$archives[$dir . '/' . $file] = $file;
	$folder->close();
	if ( $dir != $theme->get_template_directory() ) {
		$dir = $theme->get_template_directory();
		$folder = dir( $dir );
		while ( false !== ( $file = $folder->read() ) )
			if ( substr( $file, 0, strlen( $prefix ) ) == $prefix )
				$archives[$dir . '/' . $file] = $file . ' (' . __( 'parent theme', 'tcp' ) . ')';
		$folder->close();
	}
	return $archives;
}
?>