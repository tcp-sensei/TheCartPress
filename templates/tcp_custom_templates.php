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

function tcp_get_custom_templates() {
	$themes = get_themes();
	$theme = get_current_theme();
	$templates = $themes[$theme]['Template Files'];
	$custom_templates = array();
	if ( is_array( $templates ) ) {
		$base = array( trailingslashit( get_template_directory() ), trailingslashit( get_stylesheet_directory() ) );
		foreach ( $templates as $template ) {
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
?>
