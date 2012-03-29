<?php
/**
 * This file is part of TheCartPress.
 * 
 * TheCartPress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TheCartPress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TheCartPress.  If not, see <http://www.gnu.org/licenses/>.
 */

function tcp_duplicate_translatable_post( $post_id ) {
	$post = get_post( $post_id );
	$post_id = tcp_get_default_id( $post_id, $post->post_type );
	$translations = tcp_get_all_translations( $post_id, $post->post_type );
	$new_post_id = 0;
	if ( is_array( $translations ) && count( $translations ) ) {
		foreach( $translations as $translation ) {
			$post = get_post( $translation->element_id );
			if ( $post ) {
				unset( $post->ID );
				$new_id = wp_insert_post( $post );	
				if ( $new_post_id == 0 ) $new_post_id = $new_id;
				tcp_add_translation( $post_id, $new_post_id, $translation->language_code, $post->post_type );
			}
		}
	} else {
		unset( $post->ID );
		$new_post_id = wp_insert_post( $post );
		return $new_post_id;
	}
}

require_once( dirname(dirname( __FILE__ ) ) . '/daos/RelEntities.class.php' );

$post_id = isset( $_REQUEST['post_id'] ) ? (int)$_REQUEST['post_id'] : 0;

if ( $post_id == 0 ) exit( 'Error copying product!' );

$new_post_id = tcp_duplicate_translatable_post( $post_id );
//TODO duplicate all post meta
$rels = RelEntities::select( $post_id, 'GROUPED' );
foreach( $rels as $rel ) {
	RelEntities::insert( $new_post_id, $rel->id_to, $rel->rel_type, $rel->list_order, $rel->units );
}
do_action( 'tcp_copying_product', $post_id, $new_post_id );
?>
