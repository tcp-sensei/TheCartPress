<?php
/**
 * Custom Fields
 *
 * Allows to create custom fields to products
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
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'TCPCustomFields' ) ) :

define( 'TCP_CUSTOM_FIELD_TYPE_TEXT'			, 'string' );
define( 'TCP_CUSTOM_FIELD_TYPE_TEXT_MULTILINE'	, 'string-multiline' );
define( 'TCP_CUSTOM_FIELD_TYPE_NUMBER'			, 'number' );
define( 'TCP_CUSTOM_FIELD_TYPE_LIST'			, 'list' );
define( 'TCP_CUSTOM_FIELD_TYPE_RADIO'			, 'radio' );
define( 'TCP_CUSTOM_FIELD_TYPE_CHECK'			, 'check' );
define( 'TCP_CUSTOM_FIELD_TYPE_FILE'			, 'upload' );
define( 'TCP_CUSTOM_FIELD_TYPE_IMAGE'			, 'image' );
define( 'TCP_CUSTOM_FIELD_TYPE_EMAIL'			, 'email' );

class TCPCustomFields {

	static function init() {
		add_action( 'tcp_admin_menu'	, array( __CLASS__, 'tcp_admin_menu' ), 40 );
		add_action( 'admin_init'		, array( __CLASS__, 'registerMetaBox' ), 99 );
		if ( is_admin() ) {
			add_action('post_edit_form_tag', array( __CLASS__, 'add_edit_form_multipart_encoding' ) );
		}
	}

	static function add_edit_form_multipart_encoding() {
		echo ' enctype="multipart/form-data"';
	}

	static function tcp_admin_menu() {
		$base = thecartpress()->get_base_tools();
		add_submenu_page( $base, __( 'Custom fields', 'tcp' ), __( 'Custom fields', 'tcp' ), 'tcp_edit_products', TCP_ADMIN_FOLDER . 'CustomFieldsList.php' );
	}
	
	static function registerMetaBox() {
		//add_action( 'post_edit_form_tag', array( __CLASS__, 'post_edit_form_tag' ) );
		$post_types = get_post_types();
		$post_types = apply_filters( 'tcp_valid_post_types_for_custom_fields_metabox', $post_types );
		foreach( $post_types as $post_type ) {
			add_meta_box( 'tcp-custom-fields', __( 'Custom fields', 'tcp' ), array( __CLASS__, 'show' ), $post_type, 'normal', 'high' );
		}
		add_action( 'save_post'		, array( __CLASS__, 'save_post' ), 1, 2 );
		add_action( 'delete_post'	, array( __CLASS__, 'delete_post' ) );
	}

	/*static function post_edit_form_tag() {
		echo ' enctype="multipart/form-data"';
	}*/

	static function show() { 
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

	static function save_post( $post_id, $post ) {
		if ( ! wp_verify_nonce( isset( $_POST['tcp_custom_noncename'] ) ? $_POST['tcp_custom_noncename'] : '', 'tcp_custom_noncename' ) ) return array( $post_id, $post );
		if ( ! current_user_can( 'edit_post', $post_id ) ) return array( $post_id, $post );
		tcp_save_custom_fields( $post_id, $post->post_type );
		return array( $post_id, $post );
	}

	static function delete_post( $post_id ) {
		if ( !current_user_can( 'edit_post', $post_id ) ) return $post_id;
		tcp_delete_custom_fields( $post_id );
		return $post_id;
	}
}

TCPCustomFields::init();

/**
 * initial array ( 'post_type' => array( array( id, label, type, values, public), ... ), ... )
 */
function tcp_get_custom_fields_def( $post_type = TCP_PRODUCT_POST_TYPE ) {
	$custom_fields = get_option( 'tcp_custom_fields', array() );
	return isset( $custom_fields[$post_type] ) ? $custom_fields[$post_type] : array();
}

/**
 * Deletes definitions
 * @since 1.2.7
 */
function tcp_delete_custom_fields_def( $post_type = TCP_PRODUCT_POST_TYPE ) {
	$custom_fields = get_option( 'tcp_custom_fields', array() );
	unset( $custom_fields[$post_type] );
	update_option( 'tcp_custom_fields', $custom_fields );
}
/**
 * @since 1.2.7
 */
function tcp_get_custom_field_def( $field_id, $post_type = TCP_PRODUCT_POST_TYPE ) {
	$custom_fields = tcp_get_custom_fields_def( $post_type );
	foreach( $custom_fields as $custom_field ) {
		if ( $custom_field['id'] == $field_id ) return $custom_field;
	}
	return false;
}

function tcp_get_custom_fields( $post_id, $post_type = false ) {
	if ( $post_type === false ) $post_type = get_post_type( $post_id );
	$defs = tcp_get_custom_fields_def( $post_type );
	$fields = array();
	foreach( $defs as $def ) {
		if ( $def['type'] == TCP_CUSTOM_FIELD_TYPE_LIST || $def['type'] == TCP_CUSTOM_FIELD_TYPE_RADIO ) {
			$value = get_post_meta( $post_id, $def['label'], true );
			if ( isset( $def['values'][$value] ) ) {
				$values = explode( ',', $def['values'] );
				$value = isset( $values[$value] ) ? $values[$value] : 0;
			}
		} elseif ( $def['type'] == TCP_CUSTOM_FIELD_TYPE_CHECK ) {
		
		} else {
			$value = get_post_meta( $post_id, $def['values'], true );
		}
		$fields[] = array(
			'id' => $def['id'],
			'label' => tcp_string( 'TheCartPress', 'custom_field_' . $def['id'] . '-label', $def['label'] ),
			'type' => $def['type'],//string, number, list, upload
			'values' => $def['values'],
			'desc' => $def['desc'],
			'value' => $value,
			'public' => isset( $def['public'] ) ? $def['public'] : true,
		);
	}
	return $fields;
}

function tcp_add_custom_field_def( $post_type, $id, $label, $type = TCP_CUSTOM_FIELD_TYPE_TEXT, $values = 0, $desc = '', $public = true ) {
	$custom_fields =  get_option( 'tcp_custom_fields', array() );
	$custom_fields[$post_type][] = array (
		'id' => $id,
		'label' => $label,
		'type' => $type,//string, number, list, upload
		'values' => $values,
		'desc' => $desc,
		'public' => $public,
	);
	update_option( 'tcp_custom_fields', $custom_fields );
	tcp_register_string( 'TheCartPress', 'custom_field_' . $id . '-label', $label );
	tcp_register_string( 'TheCartPress', 'custom_field_' . $id . '-desc', $desc ); 
}

function tcp_update_custom_field_def( $post_type, $id, $label, $type = TCP_CUSTOM_FIELD_TYPE_TEXT, $values = 0, $desc = '', $public = true ) {
	$custom_fields =  get_option( 'tcp_custom_fields', array() );
	foreach( $custom_fields[$post_type] as $key => $custom_field )
		if ( $custom_field['id'] == $id ) {
			$custom = array(
				'id' => $id,
				'label' => $label,
				'type' => $type,
				'values' => $values,
				'desc' => $desc,
				'public' => $public,
			);
			$custom_fields[$post_type][$key] = $custom;
			tcp_register_string( 'TheCartPress', 'custom_field_' . $id . '-label', $label );
			tcp_register_string( 'TheCartPress', 'custom_field_' . $id . '-desc', $desc ); 
			break;
		}
	update_option( 'tcp_custom_fields', $custom_fields );
}

function tcp_delete_custom_field_def( $post_type, $id ) {
	$custom_fields =  get_option( 'tcp_custom_fields', array() );
	if ( isset( $custom_fields[$post_type][$id] ) ) unset( $custom_fields[$post_type][$id] );
	update_option( 'tcp_custom_fields', $custom_fields );
	tcp_unregister_string( 'TheCartPress', 'custom_field_' . $id . '-label' );
	tcp_unregister_string( 'TheCartPress', 'custom_field_' . $id . '-desc' ); 
}

function tcp_exists_custom_field_def( $post_type, $id ) {
	$custom_fields = get_option( 'tcp_custom_fields', array() );
	if ( isset( $custom_fields[$post_type] ) ) {
		$custom_fields = $custom_fields[$post_type];
		if ( is_array( $custom_fields ) && count( $custom_fields ) > 0 ) {
			foreach( $custom_fields as $field ) {
				if ( $field['id'] == $id ) return true;
			}
		}
	}
	return false;
}

function tcp_edit_custom_fields( $post_id, $post_type = false ) {
	if ( $post_type === false ) $post_type = get_post_type( $post_id );
	$custom_fields = tcp_get_custom_fields( $post_id, $post_type );
	if ( is_array( $custom_fields ) && count( $custom_fields ) > 0 ) : ?>
		<?php foreach( $custom_fields as $custom_field ) :
		$value = get_post_meta( $post_id, $custom_field['id'], true ); ?>
		<tr valign="top">
			<th scope="row"><label for="<?php echo $custom_field['id']; ?>"><?php echo tcp_string( 'TheCartPress', 'custom_field_' . $custom_field['id'] . '-label', $custom_field['label'] ); ?>:</label></th>
			<td>
			<?php if ( $custom_field['type'] == TCP_CUSTOM_FIELD_TYPE_LIST ) : ?>
				<select name="<?php echo $custom_field['id']; ?>" id="<?php echo $custom_field['id']; ?>">
				<?php $poss_values = explode( ',', $custom_field['values'] );
				foreach( $poss_values as $poss_value ) : ?>
					<option value="<?php echo $poss_value; ?>" <?php selected( $value, $poss_value ); ?>><?php echo $poss_value; ?></option>
				<?php endforeach; ?>
				</select>
			<?php elseif ( $custom_field['type'] == TCP_CUSTOM_FIELD_TYPE_RADIO ) : ?>
				<ul class="tcp-custom-filed-radio-list">
				<?php $poss_values = explode( ',', $custom_field['values'] );
				foreach( $poss_values as $poss_value ) : ?>
				<li><input type="radio" name="<?php echo $custom_field['id']; ?>" id="<?php echo $custom_field['id']; ?>-<?php echo $poss_value; ?>" value="<?php echo $poss_value; ?>" <?php checked( $value, $poss_value ); ?> /> <?php echo $poss_value; ?></li>
				<?php endforeach; ?>
				</ul>
			<?php else : ?>
				<?php if ( $custom_field['type'] == TCP_CUSTOM_FIELD_TYPE_FILE ) : ?>
					<?php if ( isset( $value['url'] ) ) : ?>
					<?php _e( 'File', 'tcp' ); ?>: <a href="<?php echo $value['url']; ?>" target="_blank"><?php echo $value['type']; ?></a>
					<br/><label><?php _e( 'Remove file', 'tcp' ); ?>: <input type="checkbox" name="<?php echo $custom_field['id']?>-remove" value="yes" /></label>
					<?php endif; ?>
					<p><?php _e( 'If you upload a new file, existing one will be deleted', 'tcp' ); ?></p>
					<input name="<?php echo $custom_field['id']; ?>" id="<?php echo $custom_field['id']; ?>" class="regular-text" type="file" style="width:20em" />
				<?php else : ?>
					<input name="<?php echo $custom_field['id']; ?>" id="<?php echo $custom_field['id']; ?>" value="<?php echo htmlspecialchars( $value ); ?>" class="regular-text" type="text" style="width:20em" />
				<?php endif; ?>				
			<?php endif; ?>
			<?php $desc = tcp_string( 'TheCartPress', 'custom_field_' . $custom_field['id'] . '-desc', isset( $custom_field['desc'] ) ? $custom_field['desc'] : '' );
			if ( strlen( $desc ) > 0 ) : ?>
				<br/><span class="description"><?php echo $desc; ?></span>
			<?php endif; ?>
			</td>
		</tr>
		<?php endforeach; ?>
	<?php else : ?>
	<tr>
	<th><?php printf( __( 'No custom fields defined. Visit <a href="%s">Custom Fields Manager</a> to create custom fields.', 'tcp' ), add_query_arg( 'page', 'thecartpress/admin/CustomFieldsList.php', get_admin_url() . 'admin.php' ) ); ?></th>
	</tr>
	<?php endif;
}

function tcp_display_custom_fields( $post_id = 0 ) {
	if ( $post_id == 0 ) $post_id = get_the_ID();
	$post_type = get_post_type( $post_id );
	$custom_fields = tcp_get_custom_fields( $post_id, $post_type );
	if ( is_array( $custom_fields ) && count( $custom_fields ) > 0 ) : 
		$par = true; ?>
		<table>
		<tbody>
		<?php foreach( $custom_fields as $custom_field ) :
			$public = isset( $custom_field['public'] ) ? $custom_field['public'] : true;
			if ( $public ) :
				$value = get_post_meta( $post_id, $custom_field['id'], true );
				if ( is_array( $value ) && isset( $value['url'] ) ) : ?>
				<tr valign="top" <?php if ( $par ) echo 'class="tcp_odd"'; $par = !$par; ?>>
					<th scope="row"><label for="<?php echo $custom_field['id']; ?>"><?php echo tcp_string( 'TheCartPress', 'custom_field_' . $custom_field['id'] . '-label', $custom_field['label'] ); ?>:</label></th>
					<td><?php tcp_display_custom_field_file( $custom_field, $value ); ?></td>
				</tr>
				<?php elseif ( strlen( $value ) > 0 ) : ?>
				<tr valign="top" <?php if ( $par ) echo 'class="tcp_odd"'; $par = !$par; ?>>
					<th scope="row"><label for="<?php echo $custom_field['id']; ?>"><?php echo tcp_string( 'TheCartPress', 'custom_field_' . $custom_field['id'] . '-label', $custom_field['label'] ); ?>: </label></th>
					<td><?php echo htmlspecialchars( $value ); ?></td>
				</tr>
				<?php endif; ?>
			<?php endif; ?>
		<?php endforeach; ?>
		</tbody>
		</table>
	<?php endif;
}

function tcp_save_custom_fields( $post_id, $post_type = false ) {
	if ( $post_type === false ) $post_type = get_post_type( $post_id );
	$custom_fields = tcp_get_custom_fields( $post_id, $post_type );
	if ( is_array( $custom_fields ) && count( $custom_fields ) > 0 ) {
		foreach( $custom_fields as $custom_field ) {
			if ( $custom_field['type'] == TCP_CUSTOM_FIELD_TYPE_FILE ) {
				if ( isset( $_FILES[$custom_field['id']] ) && strlen( $_FILES[$custom_field['id']]['tmp_name'] ) > 0 ) {
					$upload = get_post_meta( $post_id, $custom_field['id'], true );
					if ( isset( $upload['file'] ) ) unlink( $upload['file'] );
					$upload = wp_handle_upload( $_FILES[$custom_field['id']], array( 'test_form' => false ) );
					if ( isset( $upload['error'] ) && '0' != $upload['error'] ) wp_die( 'There was an error uploading your file. ' );
					// $upload = array( 'file' => (string) The local path to the uploaded file,
					//'url' => (string) The public URL for the uploaded file,
					//'type' => (string) The MIME type )
					$upload = apply_filters( 'tcp_save_custom_field', $upload, $post_id, $custom_field );
					update_post_meta( $post_id, $custom_field['id'], $upload );
				} elseif ( isset( $_REQUEST[$custom_field['id'] . '-remove'] ) ) {
					$upload = get_post_meta( $post_id, $custom_field['id'], true );
					$upload = apply_filters( 'tcp_delete_custom_field', $upload, $post_id, $custom_field );
					if ( isset( $upload['file'] ) ) @ unlink( $upload['file'] );
					delete_post_meta( $post_id, $custom_field['id'] );
				}
			} else {
				$value = isset( $_POST[$custom_field['id']] ) ? $_POST[$custom_field['id']] : '';
				update_post_meta( $post_id, $custom_field['id'], $value );
			}
		}
	}
}

function tcp_delete_custom_fields( $post_id, $post_type = false ) {
	if ( $post_type === false ) $post_type = get_post_type( $post_id );
	$custom_fields = tcp_get_custom_fields( $post_id, $post_type );
	if ( is_array( $custom_fields ) && count( $custom_fields ) > 0 ) {
		foreach( $custom_fields as $custom_field ) {
			if ( $custom_field['type'] == TCP_CUSTOM_FIELD_TYPE_FILE ) {
				$upload = get_post_meta( $post_id, $custom_field['id'], true );
				$upload = apply_filters( 'tcp_delete_custom_field', $upload, $post_id, $custom_field );
				if ( isset( $upload['file'] ) ) @ unlink( $upload['file'] );
				delete_post_meta( $post_id, $custom_field['id'] );
			} else {
				delete_post_meta( $post_id, $custom_field['id'] );
			}
		}
	}
}

/**
 * since 1.2.7
 */
function tcp_get_meta_values( $meta_key = '', $post_type = TCP_PRODUCT_POST_TYPE, $status = 'publish' ) {
	if( empty( $meta_key ) ) return;
	global $wpdb;
	$res = $wpdb->get_col( $wpdb->prepare( "
		SELECT pm.meta_value FROM {$wpdb->postmeta} pm
		LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
		WHERE pm.meta_key = '%s'
		AND p.post_status = '%s'
		AND p.post_type = '%s'
		GROUP BY pm.meta_value", $meta_key, $status, $post_type ) );
	return $res;
}

function tcp_display_custom_field_file( $custom_field_def, $upload, $echo = true ) {
	ob_start();
	if ( strpos( $upload['type'], 'image' ) !== false ) : ?>
	<img class="tcp-custom-field" src="<?php echo $upload['url']; ?>" />
	<?php else : ?>
	<a href="<?php echo $upload['url']; ?>" target="_blank"><?php _e( 'file...', 'tcp' ); ?></a>
	<?php endif;
	$out = apply_filters( 'tcp_display_custom_field_file', ob_get_clean(), $custom_field_def, $upload );
	if ( $echo ) echo $out;
	else return $out;
}

/**
 * @since 1.2.7
 */
function tcp_display_custom_field( $custom_field_id, $post_id = 0 ) {
	if ( $post_id === 0 ) $post_id = get_the_ID();
	$post_type = get_post_type( $post_id );
	$def = tcp_get_custom_field_def( $custom_field_id, $post_type );
	$value = get_post_meta( $post_id, $def['id'], true );
	$label = tcp_string( 'TheCartPress', 'custom_field_' . $def['id'] . '-label', $def['label'] );
	if ( is_array( $value ) ) {
		if ( isset( $value['url'] ) ) {
			$value = tcp_display_custom_field_file( $def, $value, false );
		} else {
			$value = __( 'No value', 'tcp' );
		}
	} elseif ( strlen( $value ) > 0 ) {
		$value = htmlspecialchars( $value );
	} else {
		$value = __( 'No value', 'tcp' );
	}
	$field = array(
		'label' => $label,
		'value' => $value,
	);
	return apply_filters( 'tcp_display_custom_field', $field, $custom_field_id, $post_id );
}
endif; // class_exists check