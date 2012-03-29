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

/**
 * initial array ( 'post_type' => array( array( id, label, type, values, order), ... ), ... )
 */
function tcp_get_custom_fields_def( $post_type ) {
	$custom_fields = get_option( 'tcp_custom_fields', array() );
	return isset( $custom_fields[$post_type] ) ? $custom_fields[$post_type] : array();
}

function tcp_get_custom_fields( $post_id, $post_type = false ) {
	if ( ! $post_type ) $post_type = get_post_type( $post_id );
	$defs = tcp_get_custom_fields_def( $post_type );
	$fields = array();
	foreach( $defs as $def ) {
		$value = get_post_meta( $post_id, $def['label'], true );
		if ( $def['type'] == 'list' ) {
			if ( isset( $def['values'][$value] ) ) {
				$values = explode( ',', $def['values'] );
				$value = isset( $values[$value] ) ? $values[$value] : 0;
			}
		} else {
			$value = get_post_meta( $post_id, $def['values'], true );
		}
		$fields[] = array(
			'id'		=> $def['id'],
			'label'		=> $def['label'],//TODO multilingual
			'type'		=> $def['type'],
			'values'	=> $def['values'],
			'desc'		=> $def['desc'],
			'value'		=> $value,
		);
	}
	return $fields;
}

function tcp_add_custom_field_def( $post_type, $id, $label, $type, $values = 0, $desc = '' ) {
	$custom_fields =  get_option( 'tcp_custom_fields', array() );
	$custom_fields[$post_type][] = array (
		'id'		=> $id,
		'label'		=> $label,
		'type'		=> $type,
		'values'	=> $values,
		'desc'		=> $desc,
	);
	update_option( 'tcp_custom_fields', $custom_fields );
}

function tcp_delete_custom_field_def( $post_type, $id ) {
	$custom_fields =  get_option( 'tcp_custom_fields', array() );
	if ( isset( $custom_fields[$post_type][$id] ) ) unset( $custom_fields[$post_type][$id] );
	update_option( 'tcp_custom_fields', $custom_fields );
}

function tcp_exists_custom_field_def( $post_type, $id ) {
	$custom_fields = get_option( 'tcp_custom_fields', array() );
	if ( isset( $custom_fields[$post_type] ) ) {
		$custom_fields = $custom_fields[$post_type];
		if ( is_array( $custom_fields ) && count( $custom_fields ) > 0 )
		 	foreach( $custom_fields as $field )
				if ( $field['id'] == $id ) return true;
	}
	return false;
}

function tcp_edit_custom_fields( $post_id, $post_type = false ) {
	if ( $post_type === false ) $post_type = get_post_type( $post_id );
	$custom_fields = tcp_get_custom_fields( $post_id, $post_type );
	if ( is_array( $custom_fields ) && count( $custom_fields ) > 0 ) : ?>
		<!--<tr><th colspan="2"><h4><?php _e( 'Custom fields', 'tcp' ); ?></h4></td></tr>-->
	<?php foreach( $custom_fields as $custom_field ) :
		$value = get_post_meta( $post_id, $custom_field['id'], true ); ?>
		<tr valign="top">
			<th scope="row"><label for="<?php echo $custom_field['id']; ?>"><?php echo $custom_field['label']; ?>:</label></th>
			<td>
			<?php if ( $custom_field['type'] == 'list' ) :?>
				<select name="<?php echo $custom_field['id']; ?>" id="<?php echo $custom_field['id']; ?>">
				<?php 
				$poss_values = explode( ',', $custom_field['values'] );
				foreach( $poss_values as $poss_value ) :?>
					<option value="<?php echo $poss_value; ?>" <?php selected( $value, $poss_value ); ?>><?php echo $poss_value; ?></option>
				<?php endforeach; ?>
				</select>
			<?php else :?>
				<input name="<?php echo $custom_field['id']; ?>" id="<?php echo $custom_field['id']; ?>" value="<?php echo htmlspecialchars( $value ); ?>" class="regular-text" type="text<?php //echo $custom_field['type'] == 'number' ? 'number' : 'text'; ?>" style="width:20em">
			<?php endif; ?>
			<?php if ( isset( $custom_field['desc'] ) && strlen( $custom_field['desc'] ) > 0 ) : ?>
				<br/><span class="description"><?php echo $custom_field['desc']; ?></span>
			<?php endif; ?>
			</td>
		</tr>
	<?php endforeach; ?>
	<?php else : ?>
	<tr><th><?php _e( 'No custom fields', 'tcp' ); ?></th></tr>
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
		$value = get_post_meta( $post_id, $custom_field['id'], true ); 
		if ( strlen( $value ) > 0 ) : ?>
		<tr valign="top" <?php if ( $par ) echo 'class="tcp_odd"'; $par = !$par; ?>>
			<th scope="row"><label for="<?php echo $custom_field['id']; ?>"><?php echo $custom_field['label']; ?>:</label></th>
			<td><?php echo htmlspecialchars( $value ); ?></td>
		</tr>
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
			$value = isset( $_POST[$custom_field['id']] ) ? $_POST[$custom_field['id']] : '';
			update_post_meta( $post_id, $custom_field['id'], $value );
		}
	}
}

function tcp_delete_custom_fields( $post_id, $post_type = false ) {
	if ( $post_type === false ) $post_type = get_post_type( $post_id );
	$custom_fields = tcp_get_custom_fields( $post_id, $post_type );
	if ( is_array( $custom_fields ) && count( $custom_fields ) > 0 ) {
		foreach( $custom_fields as $custom_field ) {
			delete_post_meta( $post_id, $custom_field['id'] );
		}
	}
}
?>
