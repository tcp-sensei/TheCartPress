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

$tcp_checkout_boxes = array();

function tcp_register_checkout_box( $path, $class_name ) {
	global $tcp_checkout_boxes;
	$tcp_checkout_boxes[$class_name] = $path;
	//require_once( dirname( __FILE__ ) . '/TCPCheckoutManager.class.php' );
	
}

/*function tcp_remove_checkout_box( $class_name ) {
	global $tcp_checkout_boxes;
	unset( $tcp_checkout_boxes[$class_name] );
}*/

function tcp_get_shipping_postcode() {
	if ( isset( $_SESSION['tcp_checkout']['shipping']['selected_shipping_address'] ) ) {
		if ( $_SESSION['tcp_checkout']['shipping']['selected_shipping_address'] == 'new' ) {
			$shipping_postcode = $_SESSION['tcp_checkout']['shipping']['shipping_postcode'];
		} elseif ( $_SESSION['tcp_checkout']['shipping']['selected_shipping_address'] == 'BIL' ) {
			if ( isset( $_SESSION['tcp_checkout']['billing']['selected_billing_address'] ) == 'new' ) {
				$shipping_postcode = $_SESSION['tcp_checkout']['billing']['billing_postcode'];;
			} else {
				require_once( dirname( dirname( __FILE__ ) ) .'/daos/Addresses.class.php' );
				$shipping_address = Addresses::get( $_SESSION['tcp_checkout']['billing']['selected_billing_id'] );
				$shipping_postcode = $shipping_address->postcode;
			}
		} else {//selected_shipping_address == Y
			require_once( dirname( dirname( __FILE__ ) ) .'/daos/Addresses.class.php' );
			$shipping_address = Addresses::get( $_SESSION['tcp_checkout']['shipping']['selected_shipping_id'] );
			$shipping_postcode = $shipping_address->postcode;
		}
		return $shipping_postcode;
	} else {
		return '';
	}
}
?>
