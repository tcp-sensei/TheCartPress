<?php
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

	function admin_menu() {
		global $thecartpress;
		if ( ! empty( $thecartpress ) ) {
			$base = $thecartpress->get_base_tools();
			add_submenu_page( $base, __( 'Custom templates', 'tcp' ), __( 'Custom templates', 'tcp' ), 'tcp_edit_products', TCP_ADMIN_FOLDER . 'CustomTemplatesList.php' );
		}
	}

	function single_template( $single_template ) {
		global $post;
		$template = tcp_get_custom_template( $post->ID );
		if ( $template ) return apply_filters( 'tcp_single_template', $template );
		$template = tcp_get_custom_template_by_post_type( $post->post_type );
		if ( $template ) return apply_filters( 'tcp_single_template', $template );
		return apply_filters( 'tcp_single_template', $single_template );
	}

	function taxonomy_template( $taxonomy_template ) {
		if ( function_exists( 'get_queried_object' ) ) {
			$term = get_queried_object();
			if ( $term ) {
				$template = tcp_get_custom_template_by_term( $term->term_id );
				if ( $template ) return apply_filters( 'tcp_taxonomy_template', $template );
			}
		}
		global $taxonomy;
		$template = tcp_get_custom_template_by_taxonomy( $taxonomy );
		if ( $template ) return apply_filters( 'tcp_taxonomy_template', $template );
		return apply_filters( 'tcp_taxonomy_template', $taxonomy_template );
	}

	function archive_template( $archive_template ) {
		//global $post_type;
		//$template = tcp_get_custom_template_by_post_type( $post_type );
		global $post;
		$template = tcp_get_custom_template_by_post_type( $post->post_type );
		if ( $template ) {
			return apply_filters( 'tcp_archive_template', $template );
		}
		return apply_filters( 'tcp_archive_template', $archive_template );
	}

	function __construct() {
		require_once( TCP_TEMPLATES_FOLDER . 'tcp_custom_templates.php' );
		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			require_once( TCP_METABOXES_FOLDER . 'CustomTemplateMetabox.class.php' );
		} else {
			add_filter( 'single_template', array( $this, 'single_template' ) );
			add_filter( 'taxonomy_template', array( $this, 'taxonomy_template' ) );
			add_filter( 'archive_template', array( $this, 'archive_template' ) );
		}
	}
}

new TCPCustomTemplates();
?>
