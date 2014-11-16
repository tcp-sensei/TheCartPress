<?php
/**
 * Checkout Box
 *
 * The Parent of all checkout Boxes, also called "Steps"
 *
 * @package TheCartPress
 * @subpackage Checkout
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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'TCPCheckoutBox' ) ) :

/**
 * Parent class for all boxes for the checkout
 */
class TCPCheckoutBox {

	function __construct() {}

	function get_title() {
		return '';
	}

	function get_class() {
		return '';
	}

	function get_name() {
		return get_class( $this );
	}

	/**
	 * To hide the box in the checkout
	 *
	 * @since 1.2.9
	 */
	function is_hidden() {
		return false;
	}

	function show_config_settings() {
		echo '<p>', __( 'No config settings', 'tcp' ), '</p>';
		return false;
	}

	function save_config_settings() {}

	function delete_config_settings() {}

	/**
	 * Recovery box will be a box to get the execution after a checkout ko
	 * Payment methods will be the recovery box
	 * @since 1.2.8
	 */
	function isRecoveryBox() {
		return false;
	}
	
	/**
	 * Returns true if the box needs a form tag encapsulating it
	 */
	function is_form_encapsulated() {
		return true;
	}

	/**
	 *@return possible values: -1 jump to the step - 1, 0 -> No jump, 1 jump to step + 1
	 */
	function before_action() {
		return 0;
	}

	function after_action() {
		return true;
	}

	function show() {
		return '';
	}

	/**
	 * Outputs any message associated with an id. It's for forms.
	 *
	 * @since 1.3.2
	 */ 
	protected function showErrorMsg( $id ) {
		return '';
	}
}
endif; // class_exists check