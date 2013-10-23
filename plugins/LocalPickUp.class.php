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

if ( !class_exists( 'TCPLocalPickUp' ) ) {

class TCPLocalPickUp extends TCP_Plugin {

	function getTitle() {
		return 'LocalPickUp';
	}

	function getDescription() {
		return 'Local pick-Up.<br>Author: <a href="http://thecartpress.com" target="_blank">TheCartPress team</a>';
	}

	function getCheckoutMethodLabel( $instance, $shippingCountry = '', $shoppingCart = false ) {
		$data = tcp_get_shipping_plugin_data( get_class( $this ), $instance );
		if ( isset( $data['title'] ) ) {
			//return tcp_string( 'TheCartPress', 'pay_TCPPayPal-title', $data['title'] );
			return tcp_string( 'TheCartPress', apply_filters( 'tcp_plugin_data_get_option_translatable_key', 'shi_TCPLocalPickUp-title-' . $instance ), $data['title'] );
		} else {
			return __( 'Local pick up', 'tcp' );
		}
		//return tcp_string( 'TheCartPress', 'shi_TCPLocalPickUp-title', $title );
	}
}
} // class_exists check
