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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

if ( !function_exists( 'ad_selected_multiple' ) ) {
	function ad_selected_multiple( $values, $value, $echo = true ) {
		if ( in_array( $value, $values ) ) {
			if ( $echo ) {
				echo ' selected="true"';
			} else {
				return ' selected="true"';
			}
		}
	}
}

$post_type = isset( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : '';

if ( isset( $_REQUEST['save_post_type'] ) ) {
	$post_type = str_replace( ' ' , '_', $post_type );
	$post_type_def = array(
		'name'				=> isset( $_REQUEST['name'] ) ? $_REQUEST['name'] : __( 'Post type Name', 'tcp' ),
		'desc'				=> isset( $_REQUEST['desc'] ) ? $_REQUEST['desc'] : '',
		'activate'			=> isset( $_REQUEST['activate'] ),
		'singular_name'		=> isset( $_REQUEST['singular_name'] ) ? $_REQUEST['singular_name'] : __( 'Singular name', 'tcp' ),
		'add_new'			=> isset( $_REQUEST['add_new'] ) ? $_REQUEST['add_new'] : __( 'Add New', 'tcp' ),
		'add_new_item'		=> isset( $_REQUEST['add_new_item'] ) ? $_REQUEST['add_new_item'] : __( 'Add New', 'tcp' ),
		'edit_item'			=> isset( $_REQUEST['edit_item'] ) ? $_REQUEST['edit_item'] : __( 'Edit', 'tcp' ),
		'new_item'			=> isset( $_REQUEST['new_item'] ) ? $_REQUEST['new_item'] : __( 'Add New', 'tcp' ),
		'view_item'			=> isset( $_REQUEST['view_item'] ) ? $_REQUEST['view_item'] : __( 'View', 'tcp' ),
		'search_items'		=> isset( $_REQUEST['search_items'] ) ? $_REQUEST['search_items'] : __( 'Search', 'tcp' ),
		'not_found'			=> isset( $_REQUEST['not_found'] ) ? $_REQUEST['not_found'] : __( 'Not found', 'tcp' ),
		'not_found_in_trash'=> isset( $_REQUEST['not_found_in_trash'] ) ? $_REQUEST['not_found_in_trash'] : __( 'Not found in Trash:', 'tcp' ),
//		'parent_item_colon'	=> isset( $_REQUEST['parent_item_colon'] ) ? $_REQUEST['parent_item_colon'] : __( 'Parent Category:', 'tcp' ),
		'public'			=> isset( $_REQUEST['public'] ),
		'show_ui'			=> isset( $_REQUEST['show_ui'] ),
		'show_in_menu'		=> isset( $_REQUEST['show_in_menu'] ),
		'can_export'		=> isset( $_REQUEST['can_export'] ),
		'show_in_nav_menus'	=> isset( $_REQUEST['show_in_nav_menus'] ),
//		'hierarchical'		=>
//		'capability_type'	=> 'post',
		'query_var'			=> isset( $_REQUEST['query_var'] ),
		'supports'			=> isset( $_REQUEST['supports'] ) ? $_REQUEST['supports'] : array( 'title', 'excerpt', 'editor', ),
		'rewrite'			=> isset( $_REQUEST['rewrite'] ) && strlen( $_REQUEST['rewrite'] ) > 0 ? $_REQUEST['rewrite'] : false,
		'has_archive'		=> isset( $_REQUEST['rewrite'] ) && strlen( $_REQUEST['rewrite'] ) > 0 ? true : false,
//		'has_archive'		=> isset( $_REQUEST['has_archive'] ) && strlen( $_REQUEST['has_archive'] ) > 0 ? $_REQUEST['has_archive'] : false,
		//TheCartPress support
		'is_saleable'		=> isset( $_REQUEST['is_saleable'] ),
	);
	//$post_types = tcp_get_custom_post_types();
	tcp_update_custom_post_type( $post_type, $post_type_def );
	update_option( 'tcp_rewrite_rules', true ); ?>
	<div id="message" class="updated"><p>
		<?php printf( __( 'Post type saved. See <a href="edit.php?post_type=%s">%s</a>', 'tcp' ), $post_type, $post_type_def['name'] );?>
	</p></div><?php
	unset( $post_type_def );
} elseif ( strlen( $post_type ) > 0 ) {
	$post_type_def = tcp_get_custom_post_type( $post_type );
	if ( $post_type_def !== false ) {
		$name				= isset( $post_type_def['name'] ) ? $post_type_def['name'] : __( 'Post type Name', 'tcp' );
		$desc				= isset( $post_type_def['desc'] ) ? $post_type_def['desc'] : '';
		$activate			= isset( $post_type_def['activate'] ) ? $post_type_def['activate'] : false;
		$singular_name		= isset( $post_type_def['singular_name'] ) ? $post_type_def['singular_name'] : __( 'Singular name', 'tcp' );
		$add_new			= isset( $post_type_def['add_new'] ) ? $post_type_def['add_new'] : __( 'Add New', 'tcp' );
		$add_new_item		= isset( $post_type_def['add_new_item'] ) ? $post_type_def['add_new_item'] : __( 'Add New', 'tcp' );
		$edit_item			= isset( $post_type_def['edit_item'] ) ? $post_type_def['edit_item'] : __( 'Edit', 'tcp' );
		$new_item			= isset( $post_type_def['new_item'] ) ? $post_type_def['new_item'] : __( 'Add New', 'tcp' );
		$view_item			= isset( $post_type_def['view_item'] ) ? $post_type_def['view_item'] : __( 'View', 'tcp' );
		$search_items		= isset( $post_type_def['search_items'] ) ? $post_type_def['search_items'] : __( 'Search', 'tcp' );
		$not_found			= isset( $post_type_def['not_found'] ) ? $post_type_def['not_found'] : __( 'Not found', 'tcp' );
		$not_found_in_trash = isset( $post_type_def['not_found_in_trash'] ) ? $post_type_def['not_found_in_trash'] : __( 'Not found in Trash:', 'tcp' );
//		$parent_item_colon	= isset( $post_type_def['parent_item_colon'] ) ? $post_type_def['parent_item_colon'] : __( 'Parent Category:', 'tcp' );
		$public				= isset( $post_type_def['public'] ) ? $post_type_def['public'] : false;
		$show_ui			= isset( $post_type_def['show_ui'] ) ? $post_type_def['show_ui'] : false;
		$show_in_menu		= isset( $post_type_def['show_in_menu'] ) ? $post_type_def['show_in_menu'] : false;
		$can_export			= isset( $post_type_def['can_export'] ) ? $post_type_def['can_export'] : false;
		$show_in_nav_menus	= isset( $post_type_def['show_in_nav_menus'] ) ? $post_type_def['show_in_nav_menus'] : false;
//		$capability_type'	= 'post',
		$query_var			= isset( $post_type_def['query_var'] ) ? $post_type_def['query_var'] : false;
		$supports			= isset( $post_type_def['supports'] ) ? $post_type_def['supports'] : array( 'title', 'editor', );
		$rewrite			= isset( $post_type_def['rewrite'] ) && strlen( $post_type_def['rewrite'] ) > 0 ? $post_type_def['rewrite'] : false;
		//$has_archive		= isset( $post_type_def['has_archive'] ) ? isset( $post_type_def['rewrite'] ) && strlen( $post_type_def['rewrite'] ) > 0 ? $post_type_def['rewrite'] : false : false;
		$has_archive		= isset( $post_type_def['has_archive'] ) ? $post_type_def['has_archive'] : false;
		$is_saleable		= isset( $post_type_def['is_saleable'] ) ? $post_type_def['is_saleable'] : false;
	}
}

if ( ! isset( $post_type_def ) ) {
	$name				= isset( $_REQUEST['name'] ) ? $_REQUEST['name'] : __( 'Post type Name', 'tcp' );
	$activate			= isset( $_REQUEST['activate'] );
	$desc				= isset( $_REQUEST['desc'] ) ? $_REQUEST['desc'] : '';
	$singular_name		= isset( $_REQUEST['singular_name'] ) ? $_REQUEST['singular_name'] : __( 'Singular name', 'tcp' );
	$add_new			= isset( $_REQUEST['add_new'] ) ? $_REQUEST['add_new'] : __( 'Add New', 'tcp' );
	$add_new_item		= isset( $_REQUEST['add_new_item'] ) ? $_REQUEST['add_new_item'] : __( 'Add New', 'tcp' );
	$edit_item			= isset( $_REQUEST['edit_item'] ) ? $_REQUEST['edit_item'] : __( 'Edit', 'tcp' );
	$new_item			= isset( $_REQUEST['new_item'] ) ? $_REQUEST['new_item'] : __( 'Add New', 'tcp' );
	$view_item			= isset( $_REQUEST['view_item'] ) ? $_REQUEST['view_item'] : __( 'View', 'tcp' );
	$search_items		= isset( $_REQUEST['search_items'] ) ? $_REQUEST['search_items'] : __( 'Search', 'tcp' );
	$not_found			= isset( $_REQUEST['not_found'] ) ? $_REQUEST['not_found'] : __( 'Not found', 'tcp' );
	$not_found_in_trash = isset( $_REQUEST['not_found_in_trash'] ) ? $_REQUEST['not_found_in_trash'] : __( 'Not found in Trash:', 'tcp' );
//	$parent_item_colon	= isset( $_REQUEST['parent_item_colon'] ) ? $_REQUEST['parent_item_colon'] : __( 'Parent Category:', 'tcp' );
	$public				= isset( $_REQUEST['public'] );
	$show_ui			= isset( $_REQUEST['show_ui'] );
	$show_in_menu		= isset( $_REQUEST['show_in_menu'] );
	$can_export			= isset( $_REQUEST['can_export'] );
	$show_in_nav_menus	= isset( $_REQUEST['show_in_nav_menus'] );
//	$capability_type'	= 'post',
	$query_var			= isset( $_REQUEST['query_var'] );
	$supports			= isset( $_REQUEST['supports'] ) ? $_REQUEST['supports'] : array( 'title', 'excerpt', 'editor', );
	$rewrite			= isset( $_REQUEST['rewrite'] ) && strlen( $_REQUEST['rewrite'] ) > 0 ? $_REQUEST['rewrite'] : false;
	$has_archive		= isset( $_REQUEST['has_archive'] ) ? isset( $_REQUEST['rewrite'] ) && strlen( $_REQUEST['rewrite'] ) > 0 ? $_REQUEST['rewrite'] : false : false;
	//$has_archive		= isset( $_REQUEST['has_archive'] ) && strlen( $_REQUEST['has_archive'] ) > 0 ? $_REQUEST['has_archive'] : false;
	$is_saleable		= isset( $_REQUEST['is_saleable'] );
}
?>
<div class="wrap">
<h2><?php _e( 'Post type', 'tcp' );?></h2>
<ul class="subsubsub">
	<li><a href="<?php echo TCP_ADMIN_PATH; ?>PostTypeList.php"><?php _e( 'Return to the list', 'tcp' );?></a></li>
</ul>
<div class="clear"></div>

<form method="post">
	<?php if ( strlen( $post_type ) > 0 ) : ?>
	<input type="hidden" name="edit" value="yes" />
	<?php endif; ?>

	<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<label for="name"><?php _e( 'Post type name', 'tcp' );?>:<span class="compulsory">(*)</span></label>
		</th>
		<td>
			<input type="text" id="name" name="name" value="<?php echo $name;?>" size="20" maxlength="50" />
			<?php //tcp_show_error_msg( $error_taxo, 'name' );?>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="post_type"><?php _e( 'Post type Id', 'tcp' );?>:<span class="compulsory">(*)</span>
			<br /><span class="description"><?php _e( 'No blank spaces', 'tcp' );?></span></label>
		</th>
		<td>
			<input type="text" id="post_type" name="post_type" value="<?php echo $post_type;?>" size="20" maxlength="50"
			<?php if ( strlen( $post_type ) > 0 ) : ?> readonly="true" <?php endif; ?>
			 />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="activate"><?php _e( 'Activated', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="checkbox" id="activate" name="activate" value="y" <?php checked( $activate );?> />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="desc"><?php _e( 'Description', 'tcp' );?>:</label>
		</th>
		<td>
			<textarea id="desc" name="desc" cols="40" rows="4"><?php echo $desc;?></textarea>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="singular_name"><?php _e( 'Singular name', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="text" id="singular_name" name="singular_name" value="<?php echo $singular_name;?>" size="20" maxlength="50" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="add_new"><?php _e( 'Add new', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="text" id="add_new" name="add_new" value="<?php echo $add_new;?>" size="20" maxlength="50" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="add_new_item"><?php _e( 'Add new item', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="text" id="add_new_item" name="add_new_item" value="<?php echo $add_new_item;?>" size="20" maxlength="50" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="edit_item"><?php _e( 'Edit item', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="text" id="edit_item" name="edit_item" value="<?php echo $edit_item;?>" size="20" maxlength="50" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="new_item"><?php _e( 'New item', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="text" id="new_item" name="new_item" value="<?php echo $new_item;?>" size="20" maxlength="50" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="view_item"><?php _e( 'View item', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="text" id="view_item" name="view_item" value="<?php echo $view_item;?>" size="20" maxlength="50" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="search_items"><?php _e( 'Search items', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="text" id="search_items" name="search_items" value="<?php echo $search_items;?>" size="20" maxlength="50" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="not_found"><?php _e( 'Not found', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="text" id="not_found" name="not_found" value="<?php echo $not_found;?>" size="20" maxlength="50" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="not_found_in_trash"><?php _e( 'Not found in Trash', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="text" id="not_found_in_trash" name="not_found_in_trash" value="<?php echo $not_found_in_trash;?>" size="20" maxlength="50" />
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<label for="public"><?php _e( 'Public', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="checkbox" id="public" name="public" value="y" <?php checked( $public );?> />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="show_ui"><?php _e( 'Show UI', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="checkbox" id="show_ui" name="show_ui" value="y" <?php checked( $show_ui );?> />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="show_in_menu"><?php _e( 'Show in menu', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="checkbox" id="show_in_menu" name="show_in_menu" value="y" <?php checked( $show_in_menu );?> />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="can_export"><?php _e( 'Can be exported', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="checkbox" id="can_export" name="can_export" value="y" <?php checked( $can_export );?> />
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<label for="show_in_nav_menus"><?php _e( 'Show in nav menus', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="checkbox" id="show_in_nav_menus" name="show_in_nav_menus" value="y" <?php checked( $show_in_nav_menus );?> />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="query_var"><?php _e( 'Query var', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="checkbox" id="query_var" name="query_var" value="yes" <?php checked( $query_var != false );?> />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="supports"><?php _e( 'Support', 'tcp' );?>:</label>
		</th>
		<td>
			<select id="supports" name="supports[]" multiple="true" size="8" style="height: auto" />
				<option value="title" <?php ad_selected_multiple( $supports, 'title' );?>><?php _e( 'Title', 'tcp' );?></option>
				<option value="editor" <?php ad_selected_multiple( $supports, 'editor' );?>><?php _e( 'Editor', 'tcp' );?></option>
				<option value="author" <?php ad_selected_multiple( $supports, 'author' );?>><?php _e( 'Author', 'tcp' );?></option>
				<option value="thumbnail" <?php ad_selected_multiple( $supports, 'thumbnail' );?>><?php _e( 'Thumbnail', 'tcp' );?></option>
				<option value="excerpt" <?php ad_selected_multiple( $supports, 'excerpt' );?>><?php _e( 'Excerpt', 'tcp' );?></option>
				<option value="trackbacks" <?php ad_selected_multiple( $supports, 'trackbacks' );?>><?php _e( 'Trackbacks', 'tcp' );?></option>
				<option value="custom-fields" <?php ad_selected_multiple( $supports, 'custom-fields' );?>><?php _e( 'Custom fields', 'tcp' );?></option>
				<option value="comments" <?php ad_selected_multiple( $supports, 'comments' );?>><?php _e( 'Comments', 'tcp' );?></option>
				<option value="revisions" <?php ad_selected_multiple( $supports, 'revisions' );?>><?php _e( 'Revisions', 'tcp' );?></option>
				<option value="page-attributes" <?php ad_selected_multiple( $supports, 'page-attributes' );?>><?php _e( 'Page attributes', 'tcp' );?></option>
			</select>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="rewrite"><?php _e( 'Rewrite', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="text" id="rewrite" name="rewrite" value="<?php echo $rewrite;?>" size="20" maxlength="50" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="has_archive"><?php _e( 'Has archive', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="checkbox" id="has_archive" name="has_archive" value="yes" <?php checked( $has_archive !== false );?> />
			<!--<input type="text" id="has_archive" name="has_archive" value="<?php echo $has_archive;?>" size="20" maxlength="50" />-->
		</td>
	</tr>
	<?php global $thecartpress;
	$disable_ecommerce = $thecartpress->get_setting( 'disable_ecommerce', false );
	//$disable_ecommerce = isset( $thecartpress->settings['disable_ecommerce'] ) ? $thecartpress->settings['disable_ecommerce'] : false;
	if ( ! $disable_ecommerce ) : ?>
	<tr valign="top">
		<th scope="row">
			<label for="is_saleable"><?php _e( 'Is saleable', 'tcp' );?>:</label>
		</th>
		<td>
			<input type="checkbox" id="is_saleable" name="is_saleable" <?php checked( $is_saleable );?> value="yes" />
		</td>
	</tr>
	<?php endif; ?>
	</table>

	<p class="submit">
		<input type="submit" name="save_post_type" id="save_post_type" value="<?php _e( 'Save' , 'tcp' );?>" class="button-primary" />
	</p>
</form>
