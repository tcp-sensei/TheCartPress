<?php
/**
 * Cart Table
 *
 * Allows to create an order summary from the data base, to print or email
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
 * along with This program. If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'OrderPage' ) ) :
/**
 * Shows an Order
 * It's used in the cart area (into checkout), in print pages and in email pages
 */
class OrderPage {

	/**
	 * Prints an order
	 * @param order_id
	 * @param args $defaults = array(
	 *		'see_address'		=> true,
	 *		'see_sku'			=> false,
	 *		'see_weight'		=> true,
	 *		'see_tax'			=> true,
	 *		'see_comment'		=> true,
	 *		'see_other_costs'	=> true,
	 *		'see_thumbnail'		=> false
	 *	);
	 * @param email, to load a different template
	 */
	static function show( $order_id, $args = array(), $echo = true, $email = false ) {
		$current_user = wp_get_current_user();
		if ( $current_user->ID == 0 ) {
			global $thecartpress;
			if ( $order_id != $thecartpress->getShoppingCart()->getOrderId() ) {
				return ;
			}
		} elseif ( ! current_user_can( 'tcp_edit_orders' ) ) {
			$thecartpress = dirname( dirname( __FILE__ ) );
			require_once( $thecartpress . '/daos/Orders.class.php');
			if ( ! Orders::is_owner( $order_id, $current_user->ID ) ) {
				return;
			}
		}
		require_once( TCP_CLASSES_FOLDER . 'CartTable.class.php' );
		require_once( TCP_CLASSES_FOLDER . 'CartSourceDB.class.php' );
		$cart_table = new TCPCartTable();
		$source = new TCP_CartSourceDB( $order_id, $args );
		return $cart_table->show( $source, $echo, $email );
	}
}
endif; // class_exists check