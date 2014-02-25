<?php
/**
 * Cart Table
 *
 * Allows to create an Order Summary, to print or email
 *
 * @package TheCartPress
 * @subpackage Classes
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

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'TCPCartTable' ) ) :

/**
 * Shows a Cart table.
 */
class TCPCartTable {

	static function show( $source, $echo = true, $email = false ) {
		ob_start();
		if ( $email ) {
			$template = 'tcp_shopping_cart_email.php';
		} else {
			$template = 'tcp_shopping_cart.php';
		}
		$located = locate_template( $template );
		if ( strlen( $located ) == 0 ) $located = TCP_THEMES_TEMPLATES_FOLDER . $template;
		if ( $email ) {
			$located = apply_filters( 'tcp_cart_table_email_template', $located, $source );
		} else {
			$located = apply_filters( 'tcp_cart_table_template', $located, $source );
		}
		require( $located );
		$out = ob_get_clean();
		if ( $echo ) echo $out;
		else return $out;
	}
}
endif; // class_exists check