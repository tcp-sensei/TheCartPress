<?php
/**
 * This file is part of TheCartPress.
 * 
 * TheCartPress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TheCartPress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TheCartPress.  If not, see <http://www.gnu.org/licenses/>.
 */

class TCPFreeProducts extends TCP_Plugin {

	function getTitle() {
		return 'Free Products';
	}

	function getIcon() {
		return plugins_url( 'thecartpress/images/free.png' );
	}

	function getDescription() {
		return 'Free Products. <br>Author: <a href="http://thecartpress.com" target="_blank">TheCartPress team</a>';
	}

	function getCheckoutMethodLabel( $instance, $shippingCountry = '', $shoppingCart = false ) {
		$data = tcp_get_payment_plugin_data( get_class( $this ), $instance );
		$title = isset( $data['title'] ) ? $data['title'] : __( 'Free products', 'tcp' );
		return tcp_string( 'TheCartPress', 'shi_TCPFreeProducts-title', $title );
	}

	function sendPurchaseMail() {
		return false;
	}

	function isApplicable( $shippingCountry, $shoppingCart, $data ) {
		return $shoppingCart->getTotal() == 0;
	}

	function showPayForm( $instance, $shippingCountry, $shoppingCart, $order_id = 0 ) {
		if ( $shoppingCart->getTotal() == 0 ) {
			$url	= add_query_arg( 'order_id', $order_id, tcp_get_the_checkout_ok_url() );
			$data	= tcp_get_payment_plugin_data( get_class( $this ), $instance, $order_id );
			require_once( TCP_DAOS_FOLDER . 'Orders.class.php' );
			Orders::editStatus( $order_id, $data['new_status'] );
			require_once( TCP_CHECKOUT_FOLDER . 'ActiveCheckout.class.php' );
			ActiveCheckout::sendMails( $order_id ); ?>
			<script>window.location.href = '<?php echo $url; ?>';</script><?php
		} else {
			wp_die( __( 'Access deny', 'tcp' ) );
		}
	}
}
?>
