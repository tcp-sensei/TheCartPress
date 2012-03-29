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

class TCPCustomTemplateMetabox {

	function register_metabox() {
		$saleable_post_types = tcp_get_saleable_post_types();
		if ( is_array( $saleable_post_types ) && count( $saleable_post_types ) )
			foreach( $saleable_post_types as $post_type )
				add_meta_box( 'tcp-custom-templates', __( 'Custom templates', 'tcp' ), array( $this, 'show' ), $post_type, 'side' );
		add_action( 'save_post', array( $this, 'save' ), 1, 2 );
		add_action( 'delete_post', array( $this, 'delete' ) );
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
		</p><p>
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
		</p>
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
		$template = isset( $_POST['tcp_custom_post_type_template'] ) ? $_POST['tcp_custom_post_type_template'] : '';
		tcp_set_custom_template_by_post_type( $post->post_type, $template );
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

new TCPCustomTemplateMetabox();
?>