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

/**
 * Parent class for all boxes for the checkout
 */
class TCPCheckoutBox {

	function __construct() {
	}

	function get_title() {
	}

	function get_class() {
		return '';
	}

	function show_config_settings() {
		return false;
	}

	function save_config_settings() {
	}

	function delete_config_settings() {
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
}
?>
