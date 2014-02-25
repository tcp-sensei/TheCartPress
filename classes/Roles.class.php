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

/**
 * TheCartPress Roles
 *
 * Manages TheCartPress roles and capabilities
 *
 * @package TheCartPress
 * @subpackage Classes
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'TCPRoles' ) ) :
	
class TCPRoles {

	/**
	 * Adds the TheCartPress Roles structure:
	 * 	Customer:
	 * 		Susbcriber + tcp_read_orders + tcp_edit_addresses + tcp_downloadable_products
	 * 	Merchant:
	 * 		Editor + Customer + tcp_edit_product + ...
	 * 	Administrator:
	 * 		Administrator + Merchant
	 */
	function __construct() {
		add_role( 'customer', __( 'Customer', 'tcp' ) );
		$customer = get_role( 'customer' );
		$customer->add_cap( 'tcp_read_orders' );
		$customer->add_cap( 'tcp_edit_address' );
		$customer->add_cap( 'tcp_edit_wish_list' );
		$customer->add_cap( 'tcp_downloadable_products' );
		$subscriber = get_role( 'subscriber' );
		if ( $subscriber ) {
			$caps = (array)$subscriber->capabilities;
			foreach( $caps as $cap => $grant ) {
				if ( $grant ) $customer->add_cap( $cap );
			}
		}

		add_role( 'merchant', __( 'Merchant', 'tcp' ) );
		$merchant = get_role( 'merchant' );
		$merchant->add_cap( 'tcp_edit_product' );
		$merchant->add_cap( 'tcp_edit_products' );
		$merchant->add_cap( 'tcp_edit_others_products' );
		$merchant->add_cap( 'tcp_publish_products' );
		$merchant->add_cap( 'tcp_read_product' );
		$merchant->add_cap( 'tcp_delete_product' );
		$merchant->add_cap( 'tcp_edit_orders' );
		$merchant->add_cap( 'tcp_read_orders' );
		$merchant->add_cap( 'tcp_update_price' );
		$merchant->add_cap( 'tcp_update_stock' );
		$merchant->add_cap( 'tcp_checkout_editor' );
		$merchant->add_cap( 'tcp_edit_address' );
		$merchant->add_cap( 'tcp_edit_addresses' );
		$merchant->add_cap( 'tcp_edit_wish_list' );
		$merchant->add_cap( 'tcp_downloadable_products' );
		$merchant->add_cap( 'tcp_users_roles' );
		$merchant->add_cap( 'tcp_edit_settings' );
		$merchant->add_cap( 'tcp_edit_plugins' );
		$merchant->add_cap( 'tcp_edit_taxes' );
		$merchant->add_cap( 'tcp_shortcode_generator' );

		$administrator = get_role( 'administrator' );
		$caps = (array)$merchant->capabilities;
		foreach( $caps as $cap => $grant ) {
			if ( $grant ) {
				$administrator->add_cap( $cap );
				echo $cap, "\n";
			}
		}
		
		$editor = get_role( 'editor' );
		if ( $editor ) {
			$caps = (array)$editor->capabilities;
			foreach( $caps as $cap => $grant ) {
				if ( $grant ) {
					$merchant->add_cap( $cap );
				}
			}
		}
		
		/*$administrator->add_cap( 'tcp_edit_product' );
		$administrator->add_cap( 'tcp_edit_products' );
		$administrator->add_cap( 'tcp_edit_others_products' );
		$administrator->add_cap( 'tcp_publish_products' );
		$administrator->add_cap( 'tcp_read_product' );
		$administrator->add_cap( 'tcp_delete_product' );
		$administrator->add_cap( 'tcp_users_roles' );
		$administrator->add_cap( 'tcp_edit_orders' );
		$administrator->add_cap( 'tcp_read_orders' );
		$administrator->add_cap( 'tcp_edit_settings' );
		$administrator->add_cap( 'tcp_edit_plugins' );
		$administrator->add_cap( 'tcp_update_price' );
		$administrator->add_cap( 'tcp_update_stock' );
		$administrator->add_cap( 'tcp_checkout_editor' );
		$administrator->add_cap( 'tcp_downloadable_products' );
		$administrator->add_cap( 'tcp_edit_address' );
		$administrator->add_cap( 'tcp_edit_addresses' );
		$administrator->add_cap( 'tcp_edit_wish_list' );
		$administrator->add_cap( 'tcp_edit_taxes' );
		$administrator->add_cap( 'tcp_shortcode_generator' );*/
	}
}

$tcp_roles = new TCPRoles();
endif; // class_exists check