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

/**
 * $name_id, is a name to add to URL, so it must be valid text to create a valid URL
 */
function tcp_register_checkout_box( $path, $class_name, $name_id = '' ) {
	if ( $name_id == '' ) $name_id = $class_name;
	global $tcp_checkout_boxes;
	$tcp_checkout_boxes[$class_name] = array(
		'path'	=> $path,
		'name'	=> $name_id,
	);
}

/**
 * Returns billing postcode
 *
 * @since 1.2.0
 */
function tcp_get_billing_postcode() {
	if ( isset( $_SESSION['tcp_checkout']['billing']['selected_billing_address'] ) ) {
		if ( $_SESSION['tcp_checkout']['billing']['selected_billing_address'] == 'new' ) {
			$billing_postcode = $_SESSION['tcp_checkout']['billing']['billing_postcode'];
		} else { //if ( $_SESSION['tcp_checkout']['billing']['selected_billing_address'] == 'Y' ) {
			require_once( dirname( dirname( __FILE__ ) ) . '/daos/Addresses.class.php' );
			$billing_address = Addresses::get( $_SESSION['tcp_checkout']['billing']['selected_billing_id'] );
			$billing_postcode = $billing_address->postcode;
		}
		return $billing_postcode;
	} else {
		return '';
	}
}

/**
 * Returns billing email
 *
 * @since 1.3.6
 */
function tcp_get_billing_email() {
	if ( isset( $_SESSION['tcp_checkout']['billing']['selected_billing_address'] ) ) {
		if ( $_SESSION['tcp_checkout']['billing']['selected_billing_address'] == 'new' ) {
			$billing_email = $_SESSION['tcp_checkout']['billing']['billing_email'];
		} else { //if ( $_SESSION['tcp_checkout']['billing']['selected_billing_address'] == 'Y' ) {
			require_once( dirname( dirname( __FILE__ ) ) . '/daos/Addresses.class.php' );
			$billing_address = Addresses::get( $_SESSION['tcp_checkout']['billing']['selected_billing_id'] );
			$billing_email = $billing_address->email;
		}
		return $billing_email;
	} else {
		return '';
	}
}

/**
 * Returns billing data
 * (!) address or session
 *
 * @since 1.3.6
 */
function tcp_get_billing_data() {
	if ( isset( $_SESSION['tcp_checkout']['billing']['selected_billing_address'] ) ) {
		if ( $_SESSION['tcp_checkout']['billing']['selected_billing_address'] == 'new' ) {
			$billing_data = $_SESSION['tcp_checkout']['billing'];
		} else { //if ( $_SESSION['tcp_checkout']['billing']['selected_billing_address'] == 'Y' ) {
			require_once( dirname( dirname( __FILE__ ) ) . '/daos/Addresses.class.php' );
			$billing_data = Addresses::get( $_SESSION['tcp_checkout']['billing']['selected_billing_id'] );
		}
		return $billing_data;
	} else {
		return '';
	}
}


/**
 * Returns shipping postcode
 *
 * @since 1.2.0
 */
function tcp_get_shipping_postcode() {
	if ( isset( $_SESSION['tcp_checkout']['shipping']['selected_shipping_address'] ) ) {
		if ( $_SESSION['tcp_checkout']['shipping']['selected_shipping_address'] == 'new' ) {
			$shipping_postcode = $_SESSION['tcp_checkout']['shipping']['shipping_postcode'];
		} elseif ( $_SESSION['tcp_checkout']['shipping']['selected_shipping_address'] == 'BIL' ) {
			if ( isset( $_SESSION['tcp_checkout']['billing']['selected_billing_address'] ) && $_SESSION['tcp_checkout']['billing']['selected_billing_address'] == 'new' ) {
				$shipping_postcode = $_SESSION['tcp_checkout']['billing']['billing_postcode'];
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
