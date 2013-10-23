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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require( dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) . '/wp-load.php' );

if ( isset( $_REQUEST['tcp_submit'] ) ) {
	$creds = array();
	$creds['user_login']	= $_REQUEST['tcp_log'];
	$creds['user_password'] = $_REQUEST['tcp_pwd'];
	$creds['remember']		= isset( $_REQUEST['tcp_rememberme'] );
	$user = wp_signon( $creds, false );
	if ( is_wp_error( $user ) ) $tcp_register_error = $user->get_error_message();
}
$redirect_to = isset( $_REQUEST['tcp_redirect_to'] ) ? $_REQUEST['tcp_redirect_to'] : '';
if ( strrpos( $redirect_to, ',') !== false ) {
//$redirect_to = role:redirect,role_2:redirect_2
	$redirects = explode( ',', $redirect_to );
	$role = tcp_get_current_user_role();
	foreach( $redirects as $redirect ) {
		$role_redirect = explode( ':', $redirect );
		if ( $role == $role_redirect[0] ) {
			$redirect_to = $role[1];
			break;
		}
	}
}

if ( isset( $tcp_register_error ) ) {
	wp_redirect( add_query_arg( 'tcp_register_error', urlencode( $tcp_register_error ), $redirect_to ) );
} else {
	wp_redirect( $redirect_to );
}
?>