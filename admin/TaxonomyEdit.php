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

$taxonomy = isset( $_REQUEST['taxonomy'] ) ? $_REQUEST['taxonomy'] : '';
if ( empty( $taxonomy ) ) $taxonomy = isset( $_REQUEST['name'] ) ? $_REQUEST['name'] : '';

if ( isset( $_REQUEST['save_taxonomy'] ) ) {

	$taxonomy = sanitize_key( $taxonomy );
	$taxonomy_def = array(
		'post_type'			=> isset( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : 'post',
		'name'				=> isset( $_REQUEST['name'] ) ? $_REQUEST['name'] : __( 'Category Name', 'tcp' ),
		'activate'			=> isset( $_REQUEST['activate']),
		'label'				=> isset( $_REQUEST['label'] ) ? $_REQUEST['label'] : __( 'Label', 'tcp' ),
		//'singular_label'	=> isset( $_REQUEST['singular_label'] ) ? $_REQUEST['singular_label'] : __( 'Singular label', 'tcp' ),
		'singular_name'		=> isset( $_REQUEST['singular_name'] ) ? $_REQUEST['singular_name'] : __( 'Singular name', 'tcp' ),
		'search_items'		=> isset( $_REQUEST['search_items'] ) ? $_REQUEST['search_items'] : __( 'Search Categories', 'tcp' ),
		'all_items'			=> isset( $_REQUEST['all_items'] ) ? $_REQUEST['all_items'] : __( 'All Categories', 'tcp' ),
		'parent_item'		=> isset( $_REQUEST['parent_item'] ) ? $_REQUEST['parent_item'] : __( 'Parent Category', 'tcp' ),
		'parent_item_colon'	=> isset( $_REQUEST['parent_item_colon'] ) ? $_REQUEST['parent_item_colon'] : __( 'Parent Category:', 'tcp' ),
		'edit_item'			=> isset( $_REQUEST['edit_item'] ) ? $_REQUEST['edit_item'] : __( 'Edit Category', 'tcp' ),
		'update_item'		=> isset( $_REQUEST['update_item'] ) ? $_REQUEST['update_item'] : __( 'Update Category', 'tcp' ),
		'add_new_item'		=> isset( $_REQUEST['add_new_item'] ) ? $_REQUEST['add_new_item'] : __( 'Add New Category', 'tcp' ),
		'new_item_name'		=> isset( $_REQUEST['new_item_name'] ) ? $_REQUEST['new_item_name'] : __( 'New Category Name', 'tcp' ),
		'desc'				=> isset( $_REQUEST['desc'] ) ? $_REQUEST['desc'] : '',
		'query_var'			=> isset( $_REQUEST['query_var'] ),
		'hierarchical'		=> isset( $_REQUEST['hierarchical'] ),
		//'rewrite'			=> isset( $_REQUEST['rewrite'] ) ? strlen( $_REQUEST['rewrite'] ) > 0 ? array( 'slug' => $_REQUEST['rewrite'] ) : false : false,
		'rewrite'			=> isset( $_REQUEST['rewrite'] ) && strlen( $_REQUEST['rewrite'] ) > 0 ? $_REQUEST['rewrite'] : false,
	);
	tcp_update_custom_taxonomy( $taxonomy, $taxonomy_def );
	update_option( 'tcp_rewrite_rules', true ); ?>
	<div id="message" class="updated"><p><?php _e( 'Taxonomy saved', 'tcp' ); ?></p></div><?php
	unset( $taxonomy_def );
} elseif ( strlen( $taxonomy ) > 0 ) {
	$taxonomy_def = tcp_get_custom_taxonomy( $taxonomy );
	if ( $taxonomy_def !== false ) {
		$post_type			= isset( $taxonomy_def['post_type'] ) ? $taxonomy_def['post_type'] : 'post';
		$name				= isset( $taxonomy_def['name'] ) ? $taxonomy_def['name'] : __( 'Category name', 'tcp' );
		$name_id			= isset( $taxonomy_def['name_id'] ) ? $taxonomy_def['name_id'] : __( 'category-name', 'tcp' );
		$label				= isset( $taxonomy_def['label'] ) ? $taxonomy_def['label'] : __( 'Label', 'tcp' );
		$singular_label		= isset( $taxonomy_def['singular_label'] ) ? $taxonomy_def['singular_label'] : __( 'Singular label', 'tcp' );
		$activate			= isset( $taxonomy_def['activate'] ) ? $taxonomy_def['activate'] : false;
		$singular_name		= isset( $taxonomy_def['singular_name'] ) ? $taxonomy_def['singular_name'] : __( 'Singular name', 'tcp' );
		$search_items		= isset( $taxonomy_def['search_items'] ) ? $taxonomy_def['search_items'] : __( 'Search Categories', 'tcp' );
		$all_items			= isset( $taxonomy_def['all_items'] ) ? $taxonomy_def['all_items'] : __( 'All Categories', 'tcp' );
		$parent_item		= isset( $taxonomy_def['parent_item'] ) ? $taxonomy_def['parent_item'] : __( 'Parent Category:', 'tcp' );
		$parent_item_colon	= isset( $taxonomy_def['parent_item_colon'] ) ? $taxonomy_def['parent_item_colon'] : __( 'Parent Category:', 'tcp' );
		$edit_item			= isset( $taxonomy_def['edit_item'] ) ? $taxonomy_def['edit_item'] : __( 'Edit Category', 'tcp' );
		$update_item		= isset( $taxonomy_def['update_item'] ) ? $taxonomy_def['update_item'] : __( 'Update Category', 'tcp' );
		$add_new_item		= isset( $taxonomy_def['add_new_item'] ) ? $taxonomy_def['add_new_item'] : __( 'Add New Category', 'tcp' );
		$new_item_name		= isset( $taxonomy_def['new_item_name'] ) ? $taxonomy_def['new_item_name'] : __( 'New Category Name', 'tcp' );
		$desc				= isset( $taxonomy_def['desc'] ) ? $taxonomy_def['desc'] : '';
		$query_var			= isset( $taxonomy_def['query_var'] ) ? $taxonomy_def['query_var'] : true;
		$hierarchical		= isset( $taxonomy_def['hierarchical'] ) ? $taxonomy_def['hierarchical'] : false;
		$rewrite			= isset( $taxonomy_def['rewrite'] ) ? $taxonomy_def['rewrite'] : false;
	}
}
if ( ! isset( $taxonomy_def ) ) {
	$post_type			= isset( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : 'post';
	$name				= isset( $_REQUEST['name'] ) ? $_REQUEST['name'] : __( 'Category Name', 'tcp' );
	$name_id			= isset( $_REQUEST['name_id'] ) ? $_REQUEST['name_id'] : __( 'category-name', 'tcp' );
	$activate			= isset( $_REQUEST['activate']);
//	$label				= isset( $_REQUEST['label'] ) ? $_REQUEST['label'] : __( 'Label', 'tcp' );
//	$singular_label		= isset( $_REQUEST['singular_label'] ) ? $_REQUEST['singular_label'] : __( 'Singular label', 'tcp' );
	$singular_name		= isset( $_REQUEST['singular_name'] ) ? $_REQUEST['singular_name'] : __( 'Singular name', 'tcp' );
	$search_items		= isset( $_REQUEST['search_items'] ) ? $_REQUEST['search_items'] : __( 'Search Categories', 'tcp' );
	$all_items			= isset( $_REQUEST['all_items'] ) ? $_REQUEST['all_items'] : __( 'All Categories', 'tcp' );
	$parent_item		= isset( $_REQUEST['parent_item'] ) ? $_REQUEST['parent_item'] : __( 'Parent Category:', 'tcp' );
	$parent_item_colon	= isset( $_REQUEST['parent_item_colon'] ) ? $_REQUEST['parent_item_colon'] : __( 'Parent Category:', 'tcp' );
	$edit_item			= isset( $_REQUEST['edit_item'] ) ? $_REQUEST['edit_item'] : __( 'Edit Category', 'tcp' );
	$update_item		= isset( $_REQUEST['update_item'] ) ? $_REQUEST['update_item'] : __( 'Update Category', 'tcp' );
	$add_new_item		= isset( $_REQUEST['add_new_item'] ) ? $_REQUEST['add_new_item'] : __( 'Add New Category', 'tcp' );
	$new_item_name		= isset( $_REQUEST['new_item_name'] ) ? $_REQUEST['new_item_name'] : __( 'New Category Name', 'tcp' );
	$desc				= isset( $_REQUEST['desc'] ) ? $_REQUEST['desc'] : '';
	$query_var			= isset( $_REQUEST['query_var'] );
	$hierarchical		= isset( $_REQUEST['hierarchical'] );
	$rewrite			= isset( $_REQUEST['rewrite'] ) ? $_REQUEST['rewrite'] : false;
} ?>
<div class="wrap">
<h2><?php _e( 'Taxonomy', 'tcp' ); ?></h2>
<ul class="subsubsub">
	<li><a href="<?php echo TCP_ADMIN_PATH; ?>TaxonomyList.php"><?php _e( 'Return to the list', 'tcp' ); ?></a></li>
	<?php if ( strlen( $taxonomy ) > 0 ) : ?>
	<li> | </li>
	<li><a href="<?php echo TCP_ADMIN_PATH; ?>TaxonomyEdit.php"><?php _e( 'Add new taxonomy', 'tcp' ); ?></a></li>
	<li>|</li>
	<li><a href="edit-tags.php?taxonomy=<?php echo $taxonomy; ?>&post_type=<?php echo TCP_DYNAMIC_OPTIONS_POST_TYPE; ?>" title="<?php _e( 'add terms', 'tcp' ); ?>"><?php _e( 'Terms', 'tcp' ); ?></a></li>
	<?php endif; ?>
</ul>
<div class="clear"></div>

<form method="post">
	<?php if ( strlen( $taxonomy ) > 0 ) : ?>
	<input type="hidden" name="edit" value="yes" />
	<?php endif; ?>
	
	<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<label for="post_type"><?php _e( 'Post Type', 'tcp' ); ?>:</label>
		</th>
		<td>
			<select name="post_type[]" id="post_type" multiple="multiple" size="10" style="height: auto;">
			<?php //foreach( get_post_types( array( 'show_in_nav_menus' => true ), object ) as $type ) : ?>
			<?php foreach( get_post_types( '', object ) as $type ) : ?>
				<option value="<?php echo $type->name; ?>"<?php tcp_selected_multiple( $post_type, $type->name ); //selected( $post_type, $type->name ); ?>><?php echo $type->labels->name; ?></option>
			<?php endforeach; ?>
			</select>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<label for="name"><?php _e( 'Taxonomy name', 'tcp' ); ?>:<span class="compulsory">(*)</span></label>
		</th>
		<td>
			<input type="text" id="name" name="name" value="<?php echo $name; ?>" size="20" maxlength="50" />
			<p class="description"><?php _e( 'The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces)', 'tcp' ); ?></p>
			<?php //tcp_show_error_msg( $error_taxo, 'name' ); ?>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<label for="taxonomy"><?php _e( 'Taxonomy id', 'tcp' ); ?>:<span class="compulsory">(*)</span>
			<br /><span class="description"><?php _e( 'No blank spaces', 'tcp' ); ?></span></label>
		</th>
		<td>
			<input type="text" id="taxonomy" name="taxonomy" value="<?php echo $taxonomy; ?>" size="20" maxlength="50" />
			<?php if ( strlen( $taxonomy ) > 0 ) : ?><br /><span class="description"><?php _e( 'For an existing taxonomy, if this name is changed a new taxonomy will be created', 'tcp' ); ?></span><?php endif; ?>
		</td>
	</tr>
<!--	<tr valign="top">
		<th scope="row">
			<label for="label"><?php _e( 'Label', 'tcp' ); ?>:</label>
		</th>
		<td>
			<input type="text" id="label" name="label" value="<?php echo $label; ?>" size="20" maxlength="50" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="singular_label"><?php _e( 'Singular label', 'tcp' ); ?>:</label>
		</th>
		<td>
			<input type="text" id="singular_label" name="singular_label" value="<?php echo $singular_label; ?>" size="20" maxlength="50" />
		</td>
	</tr>-->

	<tr valign="top">
		<th scope="row">
			<label for="activate"><?php _e( 'Activated', 'tcp' ); ?>:</label>
		</th>
		<td>
			<input type="checkbox" id="activate" name="activate" value="y" <?php checked( $activate ); ?> />
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<label for="desc"><?php _e( 'Description', 'tcp' ); ?>:</label>
		</th>
		<td>
			<textarea id="desc" name="desc" cols="40" rows="4"><?php echo $desc; ?></textarea>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<label for="singular_name"><?php _e( 'Singular name', 'tcp' ); ?>:</label>
		</th>
		<td>
			<input type="text" id="singular_name" name="singular_name" value="<?php echo $singular_name; ?>" size="20" maxlength="50" />
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<label for="search_items"><?php _e( 'Search items', 'tcp' ); ?>:</label>
		</th>
		<td>
			<input type="text" id="search_items" name="search_items" value="<?php echo $search_items; ?>" size="20" maxlength="50" />
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<label for="all_items"><?php _e( 'All items', 'tcp' ); ?>:</label>
		</th>
		<td>
			<input type="text" id="all_items" name="all_items" value="<?php echo $all_items; ?>" size="20" maxlength="50" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="parent_item"><?php _e( 'Parent item', 'tcp' ); ?>:</label>
		</th>
		<td>
			<input type="text" id="parent_item" name="parent_item" value="<?php echo $parent_item; ?>" size="20" maxlength="50" />
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<label for="parent_item_colon"><?php _e( 'Parent item colon', 'tcp' ); ?>:</label>
		</th>
		<td>
			<input type="text" id="parent_item_colon" name="parent_item_colon" value="<?php echo $parent_item_colon; ?>" size="20" maxlength="50" />
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<label for="edit_item"><?php _e( 'Edit item', 'tcp' ); ?>:</label>
		</th>
		<td>
			<input type="text" id="edit_item" name="edit_item" value="<?php echo $edit_item; ?>" size="20" maxlength="50" />
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<label for="update_item"><?php _e( 'Update item', 'tcp' ); ?>:</label>
		</th>
		<td>
			<input type="text" id="update_item" name="update_item" value="<?php echo $update_item; ?>" size="20" maxlength="50" />
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<label for="add_new_item"><?php _e( 'Add new item', 'tcp' ); ?>:</label>
		</th>
		<td>
			<input type="text" id="add_new_item" name="add_new_item" value="<?php echo $add_new_item; ?>" size="20" maxlength="50" />
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<label for="new_item_name"><?php _e( 'New item name', 'tcp' ); ?>:</label>
		</th>
		<td>
			<input type="text" id="new_item_name" name="new_item_name" value="<?php echo $new_item_name; ?>" size="20" maxlength="50" />
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<label for="query_var"><?php _e( 'Query var', 'tcp' ); ?>:</label>
		</th>
		<td>
			<input type="checkbox" id="query_var" name="query_var" value="yes" <?php checked( $query_var != false ); ?> />
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<label for="hierarchical"><?php _e( 'Hierarchical', 'tcp' ); ?>:</label>
		</th>
		<td>
			<input type="checkbox" id="hierarchical" name="hierarchical" value="y" <?php checked( $hierarchical ); ?> />
			<p class="description"><?php _e( 'Is this taxonomy hierarchical (have descendants) like categories or not hierarchical like tags', 'tcp' ); ?></p>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<label for="rewrite"><?php _e( 'Rewrite', 'tcp' ); ?>:</label>
		</th>
		<td>
			<input type="text" id="rewrite" name="rewrite" value="<?php echo $rewrite; ?>" size="20" maxlength="50" />
			<p class="description"><?php _e( 'Set to false to prevent automatic URL rewriting a.k.a. "pretty permalinks".', 'tcp' ); ?></p>
		</td>
	</tr>
	</table>

	<p class="submit">
		<input type="submit" name="save_taxonomy" id="save_taxonomy" value="<?php _e( 'Save' , 'tcp' ); ?>" class="button-primary" />
	</p>
</form>
