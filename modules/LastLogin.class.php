<?php
/**
 * Last Login
 *
 * Save info about the date of the last login of a customer. This date eill be displayed in the profile section of FrontEnd
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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'TCPLastLogin' ) ) {

class TCPLastLogin {

	function __construct() {
		add_action( 'wp_login',array( &$this, 'wp_login' ) );
	}

	function wp_login( $login ) {
		global $user_ID;
		$user = get_user_by('login', $login );
		update_user_meta( $user->ID, 'tcp_last_login', current_time( 'mysql' ) );
	}
}

new TCPLastLogin();

/**
 * @Since 1.2.0
 */
function tcp_get_the_last_login( $user_id = 0 ) {
	if ( $user_id == 0 ) {
		global $user_ID;
		$user_id = $user_ID;
	}
    $last_login = get_user_meta( $user_id, 'tcp_last_login', true );
    $date_format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
    return mysql2date( $date_format, $last_login, false );
}

/**
 * @Since 1.2.0
 */
function tcp_the_last_login( $user_id = 0, $echo = true ) {
	$out = tcp_get_the_last_login( $user_id );
    if ( $echo ) echo $out;
	else return $out;
}
} // class_exists check