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
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'TCPNavigationTreeShortcode' ) ) :

/**
 * @since 1.2.6
 */
class TCPNavigationTreeShortcode {

	function __construct() {
		add_shortcode( 'tcp_navigation_tree', array( &$this, 'tcp_navigation_tree' ) );
	}

	/**
	 * $atts:
	 * show_count = "true/false"
	 * hide_empty = "true/false"
	 * taxonomy = "tcp_product_category" (by default)
	 */
	function tcp_navigation_tree( $atts ) {
		return tcp_get_taxonomy_tree( $atts, false );
	}
}

new TCPNavigationTreeShortcode();

endif; // class_exists check