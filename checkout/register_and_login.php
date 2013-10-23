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

die('not in use');

require( dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) . '/wp-load.php' );

$user_name		= $_REQUEST['tcp_new_user_name'];
$user_pass		= $_REQUEST['tcp_new_user_pass'];
$user_pass_2	= $_REQUEST['tcp_repeat_user_pass'];
$redirect_to	= $_REQUEST['tcp_redirect_to'];
$user_email		= $_REQUEST['tcp_new_user_email'];
if ( $user_pass != $user_pass_2 ) {
	$tcp_register_error = __( 'Password incorrect', 'tcp' );
} elseif ( ! is_email( $user_email ) ) {
	$tcp_register_error = __( 'Invalid email', 'tcp' );
} else {
	$sanitized_user_login = sanitize_user( $user_name );
	$user_id = wp_create_user( $sanitized_user_login, $user_pass, $user_email );
	if ( is_wp_error( $user_id ) ) {
		$tcp_register_error = $user_id->get_error_message();
	} else {
		do_action( 'tcp_register_and_login', $user_name, $_REQUEST);
		$user = wp_signon( array(
			'user_login'		=> $user_name,
			'user_password'	=> $user_pass,
			'remember'	=> false,
		), false );
		if ( is_wp_error( $user ) ) $tcp_register_error = $user->get_error_message();
	}
}
if ( isset( $tcp_register_error ) )
	wp_redirect( add_query_arg( 'tcp_register_error', urlencode( $tcp_register_error ), $redirect_to ) );
else
	wp_redirect( $redirect_to );
?>