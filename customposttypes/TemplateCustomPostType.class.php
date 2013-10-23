<?php
/**
 * Template Post Type
 *
 * Notices, emails, adverstisment
 * This class defines the post type 'tcp_template'.
 *
 * @package TheCartPress
 * @subpackage Classes
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
if ( !defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'TemplateCustomPostType' ) ) {

class TemplateCustomPostType {
	public static $TEMPLATE = 'tcp_template';
	
	static function initPlugin() {
		add_action( 'init', array( __CLASS__, 'init' ) );
	}
	
	static function init() {
		//global $thecartpress;
		$labels = array(
			'name'					=> _x( 'Notices', 'post type general name', 'tcp' ),
			'singular_name'			=> _x( 'Notice', 'post type singular name', 'tcp' ),
			'add_new'				=> _x( 'Add New', 'product', 'tcp' ),
			'add_new_item'			=> __( 'Add New', 'tcp' ),
			'edit_item'				=> __( 'Edit Notice', 'tcp' ),
			'new_item'				=> __( 'New Notice', 'tcp' ),
			'view_item'				=> __( 'View Notice', 'tcp' ),
			'search_items'			=> __( 'Search Notices', 'tcp' ),
			'not_found'				=> __( 'No notices found', 'tcp' ),
			'not_found_in_trash'	=> __( 'No notices found in Trash', 'tcp' ),
			'parent_item_colon'		=> '',
		);
		$register = array (
			'label'				=> __( 'Notices', 'tcp' ),
			'singular_label'	=> __( 'Notice', 'tcp' ),
			'labels'			=> $labels,
			'public'			=> true,
			'show_ui'			=> true,
			'show_in_menu'		=> false,
			'can_export'		=> true,
			'show_in_nav_menus'	=> false,
			'_builtin'			=> false, // It's a custom post type, not built in! (http://kovshenin.com/archives/extending-custom-post-types-in-wordpress-3-0/)
			'_edit_link'		=> 'post.php?post=%d',
			'capability_type'	=> 'post',
			'hierarchical'		=> false, //allways false
			'query_var'			=> true,
			'supports'			=> array( 'title', 'excerpt', 'editor' ), //, 'thumbnail', 'comments' ),
			//'taxonomies'		=> array( ProductCustomPostType::$PRODUCT_CATEGORY ), // Permalinks format
			//'rewrite'			=> array( 'slug' => isset( $thecartpress->settings['notice_rewrite'] ) ? $thecartpress->settings['notice_rewrite'] : 'notices' ),
			//'has_archive'		=> isset( $thecartpress->settings['notice_rewrite'] ) && $thecartpress->settings['notice_rewrite'] != '' ? $thecartpress->settings['notice_rewrite'] : 'notices',
		);
		register_post_type( TemplateCustomPostType::$TEMPLATE, $register );
	}
}

TemplateCustomPostType::initPlugin();
} // class_exists check