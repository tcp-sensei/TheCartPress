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
 * along with This program. If not, see <http://www.gnu.org/licenses/>.
 */
require_once( TCP_DAOS_FOLDER . 'Orders.class.php' );
require_once( TCP_DAOS_FOLDER . 'OrdersDetails.class.php' );
require_once( TCP_DAOS_FOLDER . 'OrdersCosts.class.php' );

/**
 * Shows an Order
 * It's used in the cart area (into the checkout), in the print page and in the email page
 */
class OrderPage {

	static function show( $order_id, $see_comment = true, $echo = true, $see_address = true, $see_full = false ) {
		require_once( TCP_CLASSES_FOLDER . 'CartTable.class.php' );
		require_once( TCP_CLASSES_FOLDER . 'CartSourceDB.class.php' );
		$cart_table = new TCPCartTable( );
		return $cart_table->show( new TCP_CartSourceDB( $order_id, $see_address, $see_full, true, $see_comment ), $echo );
	}
}
?>
