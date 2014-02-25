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

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'TCPTemplateMetabox' ) ) :

class TCPTemplateMetabox {

	static function init() {
		add_action( 'admin_init'	, array( __CLASS__, 'register_metabox' ) );
		add_action( 'tcp_admin_menu', array( __CLASS__, 'tcp_admin_menu' ), 20 );
	}

	static function register_metabox() {
		add_meta_box( 'tcp-template-template', __( 'Notice points', 'tcp' ), array( __CLASS__, 'showTemplateMetabox' ), TemplateCustomPostType::$TEMPLATE, 'normal', 'high' );

		add_action( 'save_post'		, array( __CLASS__, 'save' ), 1, 2 );
		add_action( 'delete_post'	, array( __CLASS__, 'delete' ) );
	}

	static function tcp_admin_menu() {
		$base = thecartpress()->get_base_appearance();
		add_submenu_page( $base, __( 'Notices, eMails', 'tcp' ), __( 'Notices, eMails', 'tcp' ), 'tcp_edit_orders', 'edit.php?post_type=tcp_template' );
	}

	static function showTemplateMetabox() {
		global $post;
		if ( $post->post_type != TemplateCustomPostType::$TEMPLATE ) return;
		if ( !current_user_can( 'edit_post', $post->ID ) ) return;

		$lang = isset( $_REQUEST['lang'] ) ? $_REQUEST['lang'] : '';
		$source_lang = isset( $_REQUEST['source_lang'] ) ? $_REQUEST['source_lang'] : '';
		$is_translation = $lang != $source_lang;
		$post_id = tcp_get_default_id( $post->ID, TemplateCustomPostType::$TEMPLATE );
		if ( $is_translation && $post_id == $post->ID) {
			_e( 'After saving the title and content, you will be able to edit these relations.', 'tcp' );
			return;
		}

		$template_class = get_post_meta( $post_id, 'tcp_template_class' );
		wp_nonce_field( 'tcp_template_noncename', 'tcp_template_noncename' );
		global $tcp_template_classes;?>
<div class="clear"></div>
<ul>
<?php foreach( $tcp_template_classes as $class => $description ) : ?>
	<li>
	<input type="checkbox" id="tcp_<?php echo $class;?>" name="tcp_template_class[]" value="<?php echo $class;?>" <?php tcp_checked_multiple( $template_class, $class );?>/> <label for="tcp_<?php echo $class;?>"><?php echo $class;?></label>
	<?php if ( strlen( $description ) > 0 ) echo '<p class="description">', $description, '</p>'; ?>
	</li>
<?php endforeach;?>
</ul>
<?php do_action( 'tcp_template_metabox_custom_fields', $post_id );
	}

	static function save( $post_id, $post ) {
		if ( ! wp_verify_nonce( isset( $_POST['tcp_template_noncename'] ) ? $_POST['tcp_template_noncename'] : '', 'tcp_template_noncename' ) ) return array( $post_id, $post );
		if ( $post->post_type != TemplateCustomPostType::$TEMPLATE ) return array( $post_id, $post );
		if ( ! current_user_can( 'edit_post', $post_id ) ) return array( $post_id, $post );

		$post_id = tcp_get_default_id( $post_id, TemplateCustomPostType::$TEMPLATE );
		$tcp_template_class = isset( $_REQUEST['tcp_template_class'] ) ? $_REQUEST['tcp_template_class'] : array();
		delete_post_meta( $post_id, 'tcp_template_class' );
		foreach( $tcp_template_class as $class )
			add_post_meta( $post_id, 'tcp_template_class', $class );
		//update_post_meta( $post_id, , $tcp_template_class );
/*		$translations = tcp_get_all_translations( $post_id, $post->post_type );
		if ( is_array( $translations ) && count( $translations ) > 0 )
			foreach( $translations as $translation )
				if ( $translation->element_id != $post_id )
					update_post_meta( $translation->element_id, 'tcp_template_class', $tcp_template_class );*/
		do_action( 'tcp_template_metabox_save', $post );
	}

	static function delete( $post_id ) {
		$post = get_post( $post_id );
		if ( $post->post_type != TemplateCustomPostType::$TEMPLATE ) return;
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;
		$post_id = tcp_get_default_id( $post_id, TemplateCustomPostType::$TEMPLATE );
		delete_post_meta( $post_id, 'tcp_template_class' );
/*		$translations = tcp_get_all_translations( $post_id, $post->post_type ) );
		if ( is_array( $translations ) && count( $translations ) > 0 )
			foreach( $translations as $translation )
				if ( $translation->element_id != $post_id )
					delete_post_meta( $translation->element_id, 'tcp_template_class' );*/
		do_action( 'tcp_template_metabox_delete', $post );
	}
}

TCPTemplateMetabox::init();
endif; // class_exists check