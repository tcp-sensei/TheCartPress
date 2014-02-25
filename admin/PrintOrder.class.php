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

if ( ! class_exists( 'TCPPrintOrder' ) ) :

class TCPPrintOrder {

	function __construct() {
		add_action( 'wp_ajax_tcp_print_order'		, array( __CLASS__, 'tcp_print_order' ) );
		add_action( 'wp_ajax_nopriv_tcp_print_order', array( __CLASS__, 'tcp_print_order' ) );
	}

	static function tcp_print_order() {
		$order_id = isset( $_REQUEST['order_id'] ) ? $_REQUEST['order_id'] : 0;	
		die( TCPPrintOrder::printOrder( $order_id ) );
	}

	static function printOrder( $order_id ) {
		$current_user = wp_get_current_user();
		if ( $current_user->ID == 0 ) {
			global $thecartpress;
			if ( $order_id != $thecartpress->getShoppingCart()->getOrderId() ) return;
		} elseif ( ! current_user_can( 'tcp_edit_orders' ) ) {
			require_once( TCP_DAOS_FOLDER . 'Orders.class.php' );
			if ( !Orders::is_owner( $order_id, $current_user->ID ) ) return;
		}
		$template = locate_template( 'tcp_print_order.php' );
		$template = apply_filters( 'tcp_get_print_order_template', $template, $order_id );
		ob_start();
		if ( file_exists( $template ) ) {
			include( $template );
		} else {
			$template = TCP_THEMES_TEMPLATES_FOLDER . 'tcp_print_order.php';
			if ( file_exists( $template ) ) include( $template );
		}
		return ob_get_clean();
	}
}

new TCPPrintOrder();
endif; // class_exists check