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
 * @since 1.2.5
 */
function tcp_get_current_user_role( $current_user = false ) {
	$roles = tcp_get_current_user_roles( $current_user );
	$role = array_shift( $roles );
	return $role;
}

/**
 * @since 1.2.6
 */
function tcp_get_current_user_roles( $current_user = false) {
	if ( $current_user === false ) $current_user = wp_get_current_user();
	return $current_user->roles;
}

/**
 * @since 1.2.5
 */
function tcp_get_current_user_role_title( $current_user = false ) {
	global $wp_roles;
	$role = tcp_get_current_user_role( $current_user );
	return isset( $wp_roles->role_names[$role] ) ? translate_user_role( $wp_roles->role_names[$role] ) : false;
}

/**
 * @since 1.2.6
 */
function tcp_get_user_roles( $user_id = false, $only_first = false ) {
	if ( $user_id === false ) return tcp_get_current_user_roles();
	$user = new WP_User( $user_id );
	if ( ! empty( $user->roles ) && is_array( $user->roles ) ) {
		if ( $only_first ) return $user->roles[0];
		else return $user->roles;
	}
	return false;
}

/**
 * @since 1.2.6
 */
function tcp_get_user_role( $user_id = false ) {
	if ( $user_id === false ) return tcp_get_current_user_role();
	return tcp_get_user_roles( $user_id, true );
}

/**
 * @since 1.2.6
 */
function tcp_is_user_locked( $user_id ) {
	return (bool)get_user_meta( $user_id, 'tcp_locked', true );
}

/**
 * @since 1.2.6
 */
function tcp_set_user_locked( $user_id, $locked = true ) {
	update_user_meta( $user_id, 'tcp_locked', (bool)$locked );
	tcp_set_user_locked_date( $user_id );
	return (bool)$locked;
}

/**
 * @since 1.2.6
 */
function tcp_get_user_locked_date( $user_id ) {
	return (int)get_user_meta( $user_id, 'tcp_locked_date', true );
}

/**
 * @since 1.2.6
 */
function tcp_set_user_locked_date( $user_id, $time = false ) {
	if ( ! $time ) $time = time();
	update_user_meta( $user_id, 'tcp_locked_date', $time );
	return $time;
}

/**
 * @since 1.2.6
 */
function tcp_delete_user_locked( $user_id ) {
	delete_user_meta( $user_id, 'tcp_locked' );
	tcp_delete_user_locked_date( $user_id );
}

/**
 * @since 1.2.6
 */
function tcp_delete_user_locked_date( $user_id ) {
	delete_user_meta( $user_id, 'tcp_locked_date' );
}
?>
