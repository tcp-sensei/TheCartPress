<?php
/**
 * Top Sellers
 *
 * Widget to display the top sellers products
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with This program. If not, see <http://www.gnu.org/licenses/>.
 */

class TCPTopSellers {

	function __construct() {
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
		add_action( 'tcp_checkout_create_order_insert_detail', array( $this, 'tcp_checkout_create_order_insert_detail' ), 10, 4 );
		add_shortcode( 'tcp_total_sales', array( &$this, 'tcp_total_sales' ) );
	}

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
		$n = tcp_get_the_total_sales( $post_id );
		$n += $ordersDetails['qty_ordered'];
		update_post_meta( $post_id, 'tcp_total_sales', $n++ );
	}

	function tcp_total_sales( $atts ) {
		extract( shortcode_atts( array( 'post_id' => 0 ), $atts ) );
		return tcp_get_the_total_sales( $post_id );
	}

}

new TCPTopSellers();

function tcp_get_the_total_sales( $post_id = 0 ) {
	if ( $post_id == 0 ) $post_id = get_the_ID();
	$post_id = tcp_get_default_id( $post_id );
	require_once( TCP_DAOS_FOLDER . 'OrdersDetails.class.php' );
	$total_sales = OrdersDetails::get_product_total_sales( $post_id );
	return apply_filters( 'tcp_get_the_total_sales', $total_sales, $post_id );
	//return (int)get_post_meta( $post_id, 'tcp_total_sales', true );
}

function tcp_the_total_sales( $post_id = 0, $echo = true ) {
	$sales = tcp_get_total_sales( $post_id );
	if ( $echo ) echo $sales;
	else return $sales;
}
?>
