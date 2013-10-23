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
?>
<ul>
	<li class="tcp-profile-avatar">
		<?php echo get_avatar( $current_user->ID, $size = '100' );  ?>
	</li>
	<li class="tcp-profile-name">
		<?php printf( '<a href="%s">%s</a>', get_author_posts_url( $current_user->ID ), $current_user->display_name ); ?> (<?php echo tcp_get_current_user_role_title( $current_user ); ?>)
	<li class="tcp-profile-last-login">
		<?php printf( __( 'Last login: %s', 'tcp' ), tcp_get_the_last_login( $current_user->ID ) ); ?>
	</li>
<?php if ( strlen( $current_user->description ) > 0 ) : ?>
	<li class="tcp-profile-description">
		<?php echo $current_user->description; ?>
	</li>
<?php endif; ?>
	<li class="tcp-profile-profile-link">
<?php if ( $user_level > 8 ) : ?>
	<?php if ( function_exists( 'bp_loggedin_user_link' ) ) : ?>
		<a href="<?php bp_loggedin_user_link(); ?>"><?php echo strtolower( __( 'Profile' ) ); ?></a>
	<?php else : ?>
		<a href="<?php bloginfo('wpurl') ?>/wp-admin/profile.php"><?php echo strtolower( __( 'Profile' ) ); ?></a>
	<?php endif; ?>
<?php endif; ?>
	</li>
	<li>
		<?php $redirect = get_permalink(); ?>
		<a id="wp-logout" href="<?php echo wp_logout_url( $redirect ) ?>"><?php echo strtolower( __( 'Log Out' ) ); ?></a>
	</li>
<?php global $wpmu_version;
if ( ! empty( $wpmu_version ) || $user_level > 8 ) : ?>
	<li>
		<a href="<?php bloginfo( 'wpurl' ); ?>/wp-admin/"><?php _e( 'blog admin', 'tcp'); ?></a>
	</li>
<?php endif; ?>
</ul>
