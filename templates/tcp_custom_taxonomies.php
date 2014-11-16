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

/*
 * API for custom post types and taxonomies
 */

/**
 * Returns the custom taxonomies
 * @since 1.1.6
 */
function tcp_get_custom_taxonomies( $post_type = '' ) {
	$taxonomies = get_option( 'tcp-taxonomies-generator', array() );
	if ( $post_type == '' ) {
		return $taxonomies;
	} else {
		$result = array();
		foreach( $taxonomies as $id => $taxonomy ) {
			if ( ( is_array( $taxonomy['post_type'] ) && in_array( $post_type, $taxonomy['post_type'] ) ) || $taxonomy['post_type'] == $post_type ) {
				$result[$id] = $taxonomy;
			}
		}
		return $result;
	}
}

/**
 * Sets the custom taxonomy definitions
 * @since 1.1.7
 */
function tcp_set_custom_taxonomies( $taxonomy_defs ) {
	update_option( 'tcp-taxonomies-generator', $taxonomy_defs );
}

/**
 * Returns a custom taxonomy by id
 * @since 1.1.6
 */
function tcp_get_custom_taxonomy( $taxonomy_id ) {
	$taxonomies = tcp_get_custom_taxonomies();
	return isset( $taxonomies[$taxonomy_id] ) ? $taxonomies[$taxonomy_id] : false;
}

/**
 * Returns true if a custom taxonomy exists
 * @since 1.1.7
 */
function tcp_exist_custom_taxonomy( $taxonomy_id ) {
	$taxonomies = tcp_get_custom_taxonomies();
	return isset( $taxonomies[$taxonomy_id] );
}

/**
 * Updates a custom taxonomy definition
 *
 * @since 1.1.7
 */
function tcp_update_custom_taxonomy( $taxonomy_id, $taxonomy_def ) {
	$taxonomy_defs = tcp_get_custom_taxonomies();
	$taxonomy_defs[$taxonomy_id] = $taxonomy_def;
	tcp_set_custom_taxonomies( $taxonomy_defs );
	/*
	tcp_register_string( 'TheCartPress', 'custom_tax_' . $taxonomy_def['post_type'] . '_' . $taxonomy_id . '-name', $taxonomy_def['name'] );
	tcp_register_string( 'TheCartPress', 'custom_tax_' . $taxonomy_def['post_type'] . '_' . $taxonomy_id . '-singular_name', $taxonomy_def['singular_name'] );
	tcp_register_string( 'TheCartPress', 'custom_tax_' . $taxonomy_def['post_type'] . '_' . $taxonomy_id . '-search_items', $taxonomy_def['search_items'] );
	tcp_register_string( 'TheCartPress', 'custom_tax_' . $taxonomy_def['post_type'] . '_' . $taxonomy_id . '-all_items', $taxonomy_def['all_items'] );
	tcp_register_string( 'TheCartPress', 'custom_tax_' . $taxonomy_def['post_type'] . '_' . $taxonomy_id . '-parent_item', $taxonomy_def['parent_item'] );
	tcp_register_string( 'TheCartPress', 'custom_tax_' . $taxonomy_def['post_type'] . '_' . $taxonomy_id . '-parent_item_colon', $taxonomy_def['parent_item_colon'] );
	tcp_register_string( 'TheCartPress', 'custom_tax_' . $taxonomy_def['post_type'] . '_' . $taxonomy_id . '-edit_item', $taxonomy_def['edit_item'] );
	tcp_register_string( 'TheCartPress', 'custom_tax_' . $taxonomy_def['post_type'] . '_' . $taxonomy_id . '-update_item', $taxonomy_def['update_item'] );
	tcp_register_string( 'TheCartPress', 'custom_tax_' . $taxonomy_def['post_type'] . '_' . $taxonomy_id . '-add_new_item', $taxonomy_def['add_new_item'] );
	tcp_register_string( 'TheCartPress', 'custom_tax_' . $taxonomy_def['post_type'] . '_' . $taxonomy_id . '-new_item_name', $taxonomy_def['new_item_name'] );
	*/
	$post_types = $taxonomy_def['post_type'];
	if ( ! is_array( $post_types ) ) {
		$post_types = array( $post_types );
	}
	foreach ( $post_types as $post_type ) {
		tcp_register_string( 'TheCartPress', 'custom_tax_' . $post_type . '_' . $taxonomy_id . '-name', $taxonomy_def['name'] );
		tcp_register_string( 'TheCartPress', 'custom_tax_' . $post_type . '_' . $taxonomy_id . '-singular_name', $taxonomy_def['singular_name'] );
		tcp_register_string( 'TheCartPress', 'custom_tax_' . $post_type . '_' . $taxonomy_id . '-search_items', $taxonomy_def['search_items'] );
		tcp_register_string( 'TheCartPress', 'custom_tax_' . $post_type . '_' . $taxonomy_id . '-all_items', $taxonomy_def['all_items'] );
		tcp_register_string( 'TheCartPress', 'custom_tax_' . $post_type . '_' . $taxonomy_id . '-parent_item', $taxonomy_def['parent_item'] );
		tcp_register_string( 'TheCartPress', 'custom_tax_' . $post_type . '_' . $taxonomy_id . '-parent_item_colon', $taxonomy_def['parent_item_colon'] );
		tcp_register_string( 'TheCartPress', 'custom_tax_' . $post_type . '_' . $taxonomy_id . '-edit_item', $taxonomy_def['edit_item'] );
		tcp_register_string( 'TheCartPress', 'custom_tax_' . $post_type . '_' . $taxonomy_id . '-update_item', $taxonomy_def['update_item'] );
		tcp_register_string( 'TheCartPress', 'custom_tax_' . $post_type . '_' . $taxonomy_id . '-add_new_item', $taxonomy_def['add_new_item'] );
		tcp_register_string( 'TheCartPress', 'custom_tax_' . $post_type . '_' . $taxonomy_id . '-new_item_name', $taxonomy_def['new_item_name'] );
	}
}

/**
 * Removes a custom taxonomy definition
 *
 * @since 1.1.7
 */
function tcp_delete_custom_taxonomy( $taxonomy_id ) {
	$taxonomy_defs = tcp_get_custom_taxonomies();

	//Unregistering translation strings
	/*
	tcp_unregister_string( 'TheCartPress', 'custom_tax_' . $taxonomy_def['post_type'] . '_' . $taxonomy_id . '-name' );
	tcp_unregister_string( 'TheCartPress', 'custom_tax_' . $taxonomy_def['post_type'] . '_' . $taxonomy_id . '-singular_name' );
	tcp_unregister_string( 'TheCartPress', 'custom_tax_' . $taxonomy_def['post_type'] . '_' . $taxonomy_id . '-search_items' );
	tcp_unregister_string( 'TheCartPress', 'custom_tax_' . $taxonomy_def['post_type'] . '_' . $taxonomy_id . '-all_items' );
	tcp_unregister_string( 'TheCartPress', 'custom_tax_' . $taxonomy_def['post_type'] . '_' . $taxonomy_id . '-parent_item' );
	tcp_unregister_string( 'TheCartPress', 'custom_tax_' . $taxonomy_def['post_type'] . '_' . $taxonomy_id . '-parent_item_colon' );
	tcp_unregister_string( 'TheCartPress', 'custom_tax_' . $taxonomy_def['post_type'] . '_' . $taxonomy_id . '-edit_item' );
	tcp_unregister_string( 'TheCartPress', 'custom_tax_' . $taxonomy_def['post_type'] . '_' . $taxonomy_id . '-update_item' );
	tcp_unregister_string( 'TheCartPress', 'custom_tax_' . $taxonomy_def['post_type'] . '_' . $taxonomy_id . '-add_new_item' );
	tcp_unregister_string( 'TheCartPress', 'custom_tax_' . $taxonomy_def['post_type'] . '_' . $taxonomy_id . '-new_item_name' );
	*/
	$taxonomy_def = $taxonomy_defs[$taxonomy_id];
	$post_types = $taxonomy_def['post_type'];
	if ( ! is_array( $post_types ) ) {
		$post_types = array( $post_types );
	}
	foreach ( $post_types as $post_type ) {
		tcp_unregister_string( 'TheCartPress', 'custom_tax_' . $post_type . '_' . $taxonomy_id . '-name' );
		tcp_unregister_string( 'TheCartPress', 'custom_tax_' . $post_type . '_' . $taxonomy_id . '-singular_name' );
		tcp_unregister_string( 'TheCartPress', 'custom_tax_' . $post_type . '_' . $taxonomy_id . '-search_items' );
		tcp_unregister_string( 'TheCartPress', 'custom_tax_' . $post_type . '_' . $taxonomy_id . '-all_items' );
		tcp_unregister_string( 'TheCartPress', 'custom_tax_' . $post_type . '_' . $taxonomy_id . '-parent_item' );
		tcp_unregister_string( 'TheCartPress', 'custom_tax_' . $post_type . '_' . $taxonomy_id . '-parent_item_colon' );
		tcp_unregister_string( 'TheCartPress', 'custom_tax_' . $post_type . '_' . $taxonomy_id . '-edit_item' );
		tcp_unregister_string( 'TheCartPress', 'custom_tax_' . $post_type . '_' . $taxonomy_id . '-update_item' );
		tcp_unregister_string( 'TheCartPress', 'custom_tax_' . $post_type . '_' . $taxonomy_id . '-add_new_item' );
		tcp_unregister_string( 'TheCartPress', 'custom_tax_' . $post_type . '_' . $taxonomy_id . '-new_item_name' );
	}

	// Removing the taxonmy definition
	unset( $taxonomy_defs[$taxonomy_id] );
	$terms = get_terms( $taxonomy_id, array( 'number' => -1, 'hide_empty' => false, 'fields' => 'ids' ) );
	foreach( $terms as $term ) {
		wp_delete_term( $term, $taxonomy_id );
	}
	tcp_set_custom_taxonomies( $taxonomy_defs );

}

/**
 * Creates a custom taxonomy
 * @param $taxonomy_def:
 *		name				=> 'Category Name', 'tcp' ),
 *		activate			=> true/false,
 *		label				=> 'Label',
 *		singular_label		=> 'Singular label',
 *		singular_name		=> 'Singular name',
 *		search_items		=> 'Search Categories',
 *		all_items			=> 'All Categories',
 *		parent_item			=> 'Parent Category',
 *		parent_item_colon	=> 'Parent Category:',
 *		edit_item			=> 'Edit Category',
 *		update_item			=> 'Update Category',
 *		add_new_item		=> 'Add New Category',
 *		new_item_name		=> 'New Category Name',
 *		desc				=> string,
 *		hierarchical		=> true/false,
 *		rewrite				=> string/false,
 *		...
 * @since 1.1.7
 */
function tcp_create_custom_taxonomy( $taxonomy_id, $taxonomy_def ) {
	$taxonomy_defs = tcp_get_custom_post_types();
	if ( isset( $taxonomy_defs[$taxonomy_id] ) ) return false;
	$taxonomy_def['id'] = $taxonomy_id;
	tcp_update_custom_taxonomy( $taxonomy_id, $taxonomy_def );
}

//// Post Types ////

/**
 * Returns all post type definitions
 * @since 1.1.7
 */
function tcp_get_custom_post_types() {
	return get_option( 'tcp-posttypes-generator', array() );
}

/**
 * Returns true if a custom taxonomy exists
 * @since 1.1.7
 */
function tcp_exist_custom_post_type( $post_type ) {
	$post_types = tcp_get_custom_post_types();
	return isset( $post_types[$post_type] );
}

/**
 * Returns a post type definition
 * @since 1.1.7
 */
function tcp_get_custom_post_type( $post_type ) {
	$post_type_defs = tcp_get_custom_post_types();
	return isset( $post_type_defs[$post_type] ) ? $post_type_defs[$post_type] : false;
}

/**
 * Sets post type definitions
 * @since 1.1.7
 */
function tcp_set_custom_post_types( $post_type_defs ) {
	update_option( 'tcp-posttypes-generator', $post_type_defs );
}

/**
 * Updates a post type definition. It's also used for add a new defintion.
 * @since 1.1.7
 */
function tcp_update_custom_post_type( $post_type, $post_type_def ) {
	$post_type_defs = tcp_get_custom_post_types();
	$post_type_defs[$post_type] = $post_type_def;
	tcp_set_custom_post_types( $post_type_defs );
	tcp_register_string( 'TheCartPress', 'custom_post_type_' . $post_type . '_' . $post_type . '-name', $post_type_def['name'] );
	tcp_register_string( 'TheCartPress', 'custom_post_type_' . $post_type . '_' . $post_type . '-singular_name', $post_type_def['singular_name'] );
	tcp_register_string( 'TheCartPress', 'custom_post_type_' . $post_type . '_' . $post_type . '-add_new', $post_type_def['add_new'] );
	tcp_register_string( 'TheCartPress', 'custom_post_type_' . $post_type . '_' . $post_type . '-add_new_item', $post_type_def['add_new_item'] );
	tcp_register_string( 'TheCartPress', 'custom_post_type_' . $post_type . '_' . $post_type . '-edit_item', $post_type_def['edit_item'] );
	tcp_register_string( 'TheCartPress', 'custom_post_type_' . $post_type . '_' . $post_type . '-new_item', $post_type_def['new_item'] );
	tcp_register_string( 'TheCartPress', 'custom_post_type_' . $post_type . '_' . $post_type . '-view_item', $post_type_def['view_item'] );
	tcp_register_string( 'TheCartPress', 'custom_post_type_' . $post_type . '_' . $post_type . '-search_items', $post_type_def['search_items'] );
	tcp_register_string( 'TheCartPress', 'custom_post_type_' . $post_type . '_' . $post_type . '-not_found', $post_type_def['not_found'] );
	tcp_register_string( 'TheCartPress', 'custom_post_type_' . $post_type . '_' . $post_type . '-not_found_in_trash', $post_type_def['not_found_in_trash'] );
}

/**
 * Removes a post type definition
 * @since 1.1.7
 */
function tcp_delete_custom_post_type( $post_type ) {
	$post_type_defs = tcp_get_custom_post_types();
	unset( $post_type_defs[$post_type] );
	tcp_set_custom_post_types( $post_type_defs );
	tcp_unregister_string( 'TheCartPress', 'custom_post_type_' . $post_type . '_' . $post_type . '-name' );
	tcp_unregister_string( 'TheCartPress', 'custom_post_type_' . $post_type . '_' . $post_type . '-singular_name' );
	tcp_unregister_string( 'TheCartPress', 'custom_post_type_' . $post_type . '_' . $post_type . '-add_new' );
	tcp_unregister_string( 'TheCartPress', 'custom_post_type_' . $post_type . '_' . $post_type . '-add_new_item' );
	tcp_unregister_string( 'TheCartPress', 'custom_post_type_' . $post_type . '_' . $post_type . '-edit_item' );
	tcp_unregister_string( 'TheCartPress', 'custom_post_type_' . $post_type . '_' . $post_type . '-new_item' );
	tcp_unregister_string( 'TheCartPress', 'custom_post_type_' . $post_type . '_' . $post_type . '-view_item' );
	tcp_unregister_string( 'TheCartPress', 'custom_post_type_' . $post_type . '_' . $post_type . '-search_items' );
	tcp_unregister_string( 'TheCartPress', 'custom_post_type_' . $post_type . '_' . $post_type . '-not_found' );
	tcp_unregister_string( 'TheCartPress', 'custom_post_type_' . $post_type . '_' . $post_type . '-not_found_in_trash' );
}

/**
 * Creates a custom post type definition
 * @param post_type_def is an array with the next keys:
 *		'name'				=> 'Post type Name',
 *		'desc'				=> string,
 *		'activate'			=> true/false,
 *		'singular_name'		=> 'Singular name',
 *		'add_new'			=> 'Add New',
 *		'add_new_item'		=> 'Add New',
 *		'edit_item'			=> 'Edit',
 *		'new_item'			=> 'Add New',
 *		'view_item'			=> 'View',
 *		'search_items'		=> 'Search',
 *		'not_found'			=> 'Not found',
 *		'not_found_in_trash'=> 'Not found in Trash:',
 *		'public'			=> true/false,
 *		'show_ui'			=> true/false,
 *		'publicly_queryable'=> true/false,
 *		'exclude_from_search'=> true/false,
 *		'show_in_menu'		=> true/false,
 *		'can_export'		=> true/false,
 *		'show_in_nav_menus'	=> true/false,
 *		'query_var'			=> true/false,
 *		'supports'			=> array( 'title', 'excerpt', 'editor', ... ),
 *		'rewrite'			=> string,
 *		'has_archive'		=> string/true/false,
 *		'is_saleable'		=> true/false,
 *		'menu_icon'			=> url
 * @since 1.1.7
 */
function tcp_create_custom_post_type( $post_type, $post_type_def = array() ) {
	$post_type_defs = tcp_get_custom_post_types();
	if ( isset( $post_type_defs[$post_type] ) ) return false;
	$post_type_def['id'] = $post_type;
	tcp_update_custom_post_type( $post_type, $post_type_def );
}