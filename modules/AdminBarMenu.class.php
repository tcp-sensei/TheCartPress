<?php
/**
 * Admin Bar Menu
 *
 * Adds an Ecommerce Bar Menu in the Admin Bar, only for administrators
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.	If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'TCPAdminBarMenu' ) ) {

class TCPAdminBarMenu {

	static function customize_admin_bar() {
		if ( ! is_super_admin() || ! is_admin_bar_showing() ) return;
		global $wp_admin_bar;
		$wp_admin_bar->add_menu( array(
			'id' => 'tcp-custom-menu',
			'title' => __( 'eCommerce', 'tcp' ),
			'href' => false
		) );
		$wp_admin_bar->add_menu( array(
			'id' => 'tcp-orders-menu',
			'parent' => 'tcp-custom-menu',
			'title' => __( 'Orders', 'tcp' ),
			'href' => admin_url('admin.php?page=thecartpress/admin/OrdersListTable.php'),
		) );
		$wp_admin_bar->add_menu( array(
			'id' => 'tcp-products-menu',
			'parent' => 'tcp-custom-menu',
			'title' => __( 'Products', 'tcp' ),
			'href' => admin_url('edit.php?post_type=tcp_product'),
		) );
		do_action( 'tcp_customize_admin_bar', $wp_admin_bar );
		$wp_admin_bar->add_group( array(
			'id' => 'tcp-sites',
			'parent' => 'tcp-custom-menu',
		) );
		$wp_admin_bar->add_menu( array(
			'id' => 'thecartpress-page',
			'parent' => 'tcp-sites',
			'title' => __( 'TheCartPress site', 'tcp' ),
			'href' => 'http://thecartpress.com',
			'meta' => array( 'target'=>'_blank' )
		) );
		$wp_admin_bar->add_menu( array(
			'id' => 'community-thecartpress-page',
			'parent' => 'tcp-sites',
			'title' => __( 'Support', 'tcp' ),
			'href' => 'http://community.thecartpress.com',
			'meta' => array( 'target'=>'_blank' )
		) );
		$wp_admin_bar->add_menu( array(
			'id' => 'extend-thecartpress-page',
			'parent' => 'tcp-sites',
			'title' => __( 'Extend', 'tcp' ),
			'href' => 'http://extend.thecartpress.com',
			'meta' => array( 'target'=>'_blank' )
		) );
	}
}

add_action( 'admin_bar_menu', array( 'TCPAdminBarMenu', 'customize_admin_bar' ), 35 );
} // class_exists check