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
		
class CustomFieldsMetabox {

	function registerMetaBox() {
		$post_types = get_post_types();
		foreach( $post_types as $post_type )
			add_meta_box( 'tcp-custom-fields', __( 'Custom fields', 'tcp' ), array( $this, 'show' ), $post_type, 'normal', 'high' );
		add_action( 'save_post', array( $this, 'save_post' ), 1, 2 );
		add_action( 'delete_post', array( $this, 'delete_post' ) );
	}

	function show() { 
		global $post; ?>
		<div class="form-wrap">
			<?php wp_nonce_field( 'tcp_custom_noncename', 'tcp_custom_noncename' );?>
			<table class="form-table">
			<tbody>
			<?php tcp_edit_custom_fields( $post->ID, $post->post_type ); ?>
			</tbody>
			</table>
		</div><?php
	}

	function save_post( $post_id, $post ) {
		if ( ! wp_verify_nonce( isset( $_POST['tcp_custom_noncename'] ) ? $_POST['tcp_custom_noncename'] : '', 'tcp_custom_noncename' ) ) return array( $post_id, $post );
		if ( ! current_user_can( 'edit_post', $post_id ) ) return array( $post_id, $post );
		tcp_save_custom_fields( $post_id, $post->post_type );
		return array( $post_id, $post );
	}

	function delete_post( $post_id ) {
		if ( !current_user_can( 'edit_post', $post_id ) ) return $post_id;
		tcp_delete_custom_fields( $post_id );
		return $post_id;
	}
}
?>
