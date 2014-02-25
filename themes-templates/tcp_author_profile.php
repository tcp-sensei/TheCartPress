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
<div class="tcpf">

	<div class="media">
	
		<a class="pull-left" href="#">
			<!-- class="media-object"-->
			<?php echo get_avatar( $current_user->ID, $size = '100' );  ?>
		</a>
		<div class="media-body">

			<ul class="tcp-profile-group">
				<li class="tcp-profile-author-name media-heading">
					<?php printf( '<a href="%s">%s</a>', get_author_posts_url( $current_user->ID ), $current_user->display_name ); ?> <small>(<?php echo tcp_get_current_user_role_title( $current_user ); ?>)</small>
				</li>

				<li class="tcp-profile-last-login">
					<small>
						<?php printf( __( 'Last login: %s', 'tcp' ), tcp_get_the_last_login( $current_user->ID ) ); ?>
					</small>
				</li>
			
				<?php if ( strlen( $current_user->description ) > 0 ) : ?>
				<li class="tcp-profile-description">
					<?php echo $current_user->description; ?>
				</li>
				<?php endif; ?>

				<?php if ( $user_level > 8 ) : ?>
				<li class="tcp-profile-link">
					<?php if ( function_exists( 'bp_loggedin_user_link' ) ) : ?>
						<a href="<?php bp_loggedin_user_link(); ?>"><span class="glyphicon glyphicon-user"></span> <?php _e( 'Profile', 'tcp' ); ?></a>
					<?php else : ?>
						<a href="<?php bloginfo('wpurl') ?>/wp-admin/profile.php"><span class="glyphicon glyphicon-user"></span> <?php _e( 'Profile', 'tcp' ); ?></a>
					<?php endif; ?>
				</li>
				<?php endif; ?>


				<?php global $wpmu_version;
				if ( !empty( $wpmu_version ) || $user_level > 8 ) : ?>
				<li class="tcp-blog-admin">
					<a href="<?php bloginfo( 'wpurl' ); ?>/wp-admin/"><span class="glyphicon glyphicon-pencil"></span> <?php _e( 'blog admin', 'tcp'); ?></a>
				</li>
				<?php endif; ?>

				<?php do_action( 'tcp_author_profile_bottom', $current_user ); ?>

				<li class="tcp-log-out">
					<?php $redirect = get_permalink(); ?>
					<a id="wp-logout" href="<?php echo wp_logout_url( $redirect ) ?>"><span class="glyphicon glyphicon-log-out"></span> <?php _e( 'Log Out' ); ?></a>
				</li>
			</ul>

		</div>
	</div><!-- .media-list -->
</div><!-- .tcpf -->