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

class TCPTopSellers {

	function widgets_init() {
		global $thecartpress;
		if ( $thecartpress ) {
			$disable_ecommerce = $thecartpress->get_setting( 'disable_ecommerce', false );
			if ( ! $disable_ecommerce ) {
				require_once( TCP_WIDGETS_FOLDER . 'TopSellersWidget.class.php' );
				register_widget( 'TopSellersWidget' );
			}
		}
	}

	function tcp_checkout_create_order_insert_detail( $order_id, $orders_details_id, $post_id, $ordersDetails ) {
		$n = get_post_meta( $post_id, 'tcp_total_sales', true );
		$n += $ordersDetails['qty_ordered'];
		update_post_meta( $post_id, 'tcp_total_sales', $n++ );
	}

	function __construct() {
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
		add_action( 'tcp_checkout_create_order_insert_detail', array( $this, 'tcp_checkout_create_order_insert_detail' ), 10, 4 );
	}
}

new TCPTopSellers();
?>
