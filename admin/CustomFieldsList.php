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

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function tcp_create_id( $post_type, $label ) {
	//$internal_id = 'tcp_' . str_replace ( ' ' , '_' , $label );
	$internal_id = sanitize_key( $label );
	$i = 0;
	while ( tcp_exists_custom_field_def( $post_type, $internal_id ) ) {
		$internal_id = $internal_id . '_' . $i++;
	}
	return $internal_id;
}

if ( isset( $_REQUEST['post_type'] ) ) {
	$post_type =  $_REQUEST['post_type'];
} else {
	$post_type = post_type_exists( 'tcp_product' ) ? 'tcp_product' : 'post';
}

if ( isset( $_REQUEST['tcp_save_custom_field'] ) ) {
	$label = isset( $_REQUEST['label'] ) ? trim( $_REQUEST['label'] ) : '';
	if ( strlen( $label ) > 0 ) {
		$id = tcp_create_id( $post_type, $label );
		$type = isset( $_REQUEST['type'] ) ? $_REQUEST['type'] : TCP_CUSTOM_FIELD_TYPE_TEXT;
		$values = isset( $_REQUEST['values'] ) ? $_REQUEST['values'] : 0;
		$public = isset( $_REQUEST['public'] );
		$desc = isset( $_REQUEST['desc'] ) ? $_REQUEST['desc'] : '';
		tcp_add_custom_field_def( $post_type, $id, $label, $type, $values, $desc, $public ); ?>
		<div id="message" class="updated"><p>
			<?php _e( 'Custom field saved', 'tcp' );?>
		</p></div><?php
	} else {?>
		<div id="message" class="error"><p>
			<?php _e( 'Label field must be completed', 'tcp' );?>
		</p></div><?php
	}
} elseif ( isset( $_REQUEST['tcp_modify_custom_field'] ) ) {
	$custom_field_id = isset( $_REQUEST['custom_field_id'] ) ? trim( $_REQUEST['custom_field_id'] ) : -1;//array index
	$label = isset( $_REQUEST['label'] ) ? trim( $_REQUEST['label'] ) : '';
	if ( strlen( $label ) > 0 ) {
		//tcp_delete_custom_field_def( $post_type, $custom_field_id );
		$internal_id = isset( $_REQUEST['internal_id'] ) ? $_REQUEST['internal_id'] : 'internal_id';
		$type = isset( $_REQUEST['type'] ) ? $_REQUEST['type'] : TCP_CUSTOM_FIELD_TYPE_TEXT;
		$values = isset( $_REQUEST['values'] ) ? $_REQUEST['values'] : 0;
		$public = isset( $_REQUEST['public'] );
		$desc = isset( $_REQUEST['desc'] ) ? $_REQUEST['desc'] : '';
		tcp_update_custom_field_def( $post_type, $internal_id, $label, $type, $values, $desc, $public ); ?>
		<div id="message" class="updated"><p>
			<?php _e( 'Custom field saved', 'tcp' );?>
		</p></div><?php
	}
} elseif ( isset( $_REQUEST['tcp_delete_custom_field'] ) ) {
	$id = isset( $_REQUEST['custom_field_id'] ) ? trim( $_REQUEST['custom_field_id'] ) : -1;
	if ( $id > -1 ) {
		$custom_fields = tcp_get_custom_fields_def( $post_type );
		if ( isset( $custom_fields[$id] ) && isset( $custom_fields[$id]['id'] ) ) {
			$custom_field_id = $custom_fields[$id]['id'];
			tcp_delete_custom_field_def( $post_type, $id );
			global $wpdb;
			$wpdb->query( $wpdb->prepare( 'delete from ' . $wpdb->prefix . 'postmeta where meta_key = %s', $custom_field_id ) );?>
			<div id="message" class="updated"><p>
				<?php _e( 'Custom field deleted', 'tcp' );?>
			</p></div><?php
		}
	}
} ?>
<div class="wrap">

<h2><?php _e( 'Custom Fields', 'tcp' );?></h2>
<div class="clear"></div>

<form method="post">
<p>
	<label><?php _e( 'Post type', 'tcp');?>: <select name="post_type" id="post_type">
	<?php foreach( get_post_types( array( 'show_in_nav_menus' => true ), object ) as $type ) : ?>
		<option value="<?php echo $type->name;?>"<?php selected( $post_type, $type->name ); ?>><?php echo $type->labels->name; ?></option>
	<?php endforeach;?>
	</select>
	</label>
	<input type="submit" id="tcp_filter" name="tcp_filter" value="<?php _e( 'filter', 'tcp' );?>" class="button-secondary"/>
	<p class="description"><?php _e( 'This filter allows to create different Custom fields for different Post Types.', 'tcp' ); ?></p>
</p>

<h3><?php $post_type_object = get_post_type_object( $post_type ); printf( __( 'New Custom Field definition for "%s"', 'tcp' ), $post_type_object->labels->name ) ;?></h3>

<!--<input type="hidden" name="post_type" value="<?php echo $post_type;?>"/>-->
<div class="postbox">
	<div class="inside">
	<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<label for="label"><?php _e( 'Label', 'tcp' );?>: </label>
		</th>
		<td>
			<input type="text" name="label" id="label" size="20" />
		</td>
	</tr>
	<tr>
		<th>
			<label for="type"><?php _e( 'Type', 'tcp' );?>: </label>
		</th>
		<td>
			<select id="type" name="type">
				<option value="<?php echo TCP_CUSTOM_FIELD_TYPE_TEXT; ?>"><?php _e( 'Text', 'tcp' );?></option>
				<option value="<?php echo TCP_CUSTOM_FIELD_TYPE_NUMBER; ?>"><?php _e( 'Number', 'tcp' );?></option>
				<option value="<?php echo TCP_CUSTOM_FIELD_TYPE_LIST; ?>"><?php _e( 'Select List', 'tcp' );?></option>
				<option value="<?php echo TCP_CUSTOM_FIELD_TYPE_RADIO; ?>"><?php _e( 'Radio list', 'tcp' );?></option>
				<!--<option value="<?php echo TCP_CUSTOM_FIELD_TYPE_CHECK; ?>"><?php _e( 'Check box', 'tcp' );?></option>-->
				<option value="<?php echo TCP_CUSTOM_FIELD_TYPE_FILE; ?>"><?php _e( 'File', 'tcp' );?></option>
			</select>
		</td>
	</tr>
	<tr>
		<th>
			<label for="values"><?php _e( 'Possible values', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="text" id="values" name="values" size="40"/><p class="description"><?php _e( 'For fields of type \'List\', enter a list of possible values separated by comma', 'tcp' );?></p>
		</td>
	</tr>
	<tr>
		<th>
			<label for="public"><?php _e( 'Public', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="checkbox" id="public" name="public" />
		</td>
	</tr>
	<tr>
		<th>
			<label for="desc"><?php _e( 'Description', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="text" id="desc" name="desc" size="40"/>
		</td>
	</tr>
	</table>

	<p style="padding-left: 1em;"><input type="submit" name="tcp_save_custom_field" value="<?php _e( 'Save' , 'tcp' );?>" class="button-primary" /></p>
	</div><!-- .inside -->
</div><!-- .postbox -->
</form>

<h3><?php printf( __( 'Current Custom Fields defined for "%s"', 'tcp' ), $post_type_object->labels->name ) ;?></h3>

<table class="widefat fixed" cellspacing="0">
<thead>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Key', 'tcp' );?> (<?php _e( 'to get the custom field value', 'tcp' );?>)</th>
	<th scope="col" class="manage-column"><?php _e( 'Label', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Type', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Public', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' );?></th>
	<th scope="col" class="manage-column" style="width: 20%;">&nbsp;</th>
</tr>
</thead>

<tfoot>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Key', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Label', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Type', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Public', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' );?></th>
	<th scope="col" class="manage-column" style="width: 20%;">&nbsp;</th></tr>
</tfoot>
<tbody>
<?php
$custom_fields = tcp_get_custom_fields_def( $post_type );
if ( count( $custom_fields ) == 0 ) : ?>
	<tr>
	<td colspan="5"><?php printf( __( 'List of Custom Fields defined for %s is empty', 'tcp' ), $post_type );?></td>
	</tr>
<?php else :
	foreach( $custom_fields as $id => $field ) : ?>
	<tr>
		<td><?php echo $field['id'];?></td>
		<td><?php echo $field['label'];?></td>
		<td><?php echo $field['type'];?></td>
		<td><?php if ( isset( $field['public'] ) ? $field['public'] : true ) _e( 'Public', 'tcp' ); else _e( 'Non public', 'tcp' ); ?></th>
		<td><?php echo $field['desc'];?></td>
		<td style="width: 20%;">
		<a href="#" onclick="jQuery('.modify_custom_field').hide();jQuery('#modify_<?php echo $id;?>').show();return false;"><?php _e( 'edit', 'tcp' );?></a> |
		<a href="#" onclick="jQuery('.delete_custom_field').hide();jQuery('#delete_<?php echo $id;?>').show();return false;" class="delete"><?php _e( 'delete', 'tcp' );?></a>
		<div id="delete_<?php echo $id;?>" class="delete_custom_field" style="display:none; border: 1px dotted orange; padding: 2px">
			<form method="post">
				<input type="hidden" name="post_type" value="<?php echo $post_type;?>"/>
				<input type="hidden" name="custom_field_id" value="<?php echo $id;?>" />
				<p><?php _e( 'Do you really want to delete this custom field?', 'tcp' );?></p>
				<input type="submit" name="tcp_delete_custom_field" value="<?php _e( 'Yes' , 'tcp' );?>" class="button-secondary" /> |
				<a href="#" onclick="jQuery('#delete_<?php echo $id;?>').hide();"><?php _e( 'No, I don\'t' , 'tcp' );?></a>
			</form>
		</div>
		</td>
	</tr>
	<tr id="modify_<?php echo $id;?>" class="modify_custom_field" style="display: none;">
		<td colspan="4">
			<form method="post">
				<input type="hidden" name="post_type" value="<?php echo $post_type;?>" />
				<input type="hidden" name="custom_field_id" value="<?php echo $id;?>" />
				<input type="hidden" name="internal_id" value="<?php echo $field['id'];?>" />
				<p>
					<label for="label_<?php echo $id;?>"><?php _e( 'Label', 'tcp' );?></label>:<input type="text" id="label_<?php echo $id;?>" name="label" value="<?php echo $field['label'];?>" size="20" />
				</p>
				<p>
					<label for="name_<?php echo $id;?>"><?php _e( 'Type', 'tcp' );?></label>: <select id="name_<?php echo $id;?>" name="type">
						<option value="<?php echo TCP_CUSTOM_FIELD_TYPE_TEXT; ?>" <?php selected( $field['type'], TCP_CUSTOM_FIELD_TYPE_TEXT );?>><?php _e( 'Text', 'tcp' );?></option>
						<option value="<?php echo TCP_CUSTOM_FIELD_TYPE_NUMBER; ?>" <?php selected( $field['type'], TCP_CUSTOM_FIELD_TYPE_NUMBER );?>><?php _e( 'Number', 'tcp' );?></option>
						<option value="<?php echo TCP_CUSTOM_FIELD_TYPE_LIST; ?>" <?php selected( $field['type'], TCP_CUSTOM_FIELD_TYPE_LIST ); ?>><?php _e( 'List', 'tcp' );?></option>
						<option value="<?php echo TCP_CUSTOM_FIELD_TYPE_FILE; ?>" <?php selected( $field['type'], TCP_CUSTOM_FIELD_TYPE_FILE ); ?>><?php _e( 'Upload', 'tcp' );?></option>
					</select>
				</p>
				<p>
					<label for="values_<?php echo $id;?>"><?php _e( 'Possible values', 'tcp' );?></label>: 
					<input type="text" id="values_<?php echo $id;?>" name="values" value="<?php echo $field['values'];?>" size="40"/>
					<span class="description"><?php _e( 'For fields of type \'List\', enter a list of possible values separated by comma', 'tcp' );?></span>
				</p>
				<p>
					<label for="public_<?php echo $id;?>"><?php _e( 'Public', 'tcp' );?></label>: <input type="checkbox" id="public_<?php echo $id;?>" name="public" value="yes" <?php checked( isset( $field['public'] ) ? $field['public'] : true ); ?> /></p>
				<p>
				<p>
					<label for="desc_<?php echo $id;?>"><?php _e( 'Description', 'tcp' );?></label>: <input type="text" id="desc_<?php echo $id;?>" name="desc" value="<?php echo $field['desc'];?>" size="40"/></p>
				<p>
				<input type="submit" name="tcp_modify_custom_field" value="<?php _e( 'modify' , 'tcp' );?>" class="button-primary" /> |
				<a href="#" onclick="jQuery('#modify_<?php echo $id;?>').hide();"><?php _e( 'close' , 'tcp' );?></a></p>
			</form>
		</td>
	</tr>

	<?php endforeach;
endif;?>

</tbody>
</table>

</div> <!-- end wrap -->
