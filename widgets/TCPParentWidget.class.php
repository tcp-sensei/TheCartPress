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

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'TCPParentWidget' ) ) {
	
class TCPParentWidget extends WP_Widget {
	function TCPParentWidget( $name, $description, $title, $width = 300 ) {
		$widget_settings = array(
			'classname'		=> $name,
			'description'	=> $description,
		);
		$control_settings = array(
			'width'		=> $width,
			'id_base'	=> $name . '-widget'
		);
		$this->WP_Widget( $name . '-widget', $title, $widget_settings, $control_settings );
	}

	function widget( $args, $instance ) {
		$private = isset( $instance['private'] ) ? $instance['private'] : false;
		if ( $private ) {
			if ( ! is_user_logged_in() ) return false;
			$roles = isset( $instance['roles'] ) ? $instance['roles'] : array();
			if ( count( $roles ) > 0 ) {
				$user_car = false;
				foreach( $roles as $role ) {
					if ( current_user_can( $role ) ) {
						$user_car = true;
						break;
					}
				}
				if ( ! $user_car ) return false;
			}
		}
		if ( false && WP_DEBUG ) {
			var_dump($args);
			echo "\n\n<br><br>";
			var_dump($instance);
		}
		return apply_filters( 'tcp_private_widget', true, $args, $instance );
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['private'] = isset( $new_instance['private'] ) ? $new_instance['private'] == 'yes' : false;
		if ( ! isset( $new_instance['roles'] ) ) $instance['roles'] = array();
		elseif ( in_array( '', $new_instance['roles'] ) ) $instance['roles'] = array();
		else $instance['roles']	= $new_instance['roles'];
		return apply_filters( 'tcp_parent_widget_update', $instance, $new_instance );
	}

	function form( $instance ) {
		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$private = isset( $instance['private'] ) ? $instance['private'] : false;
		$roles = isset( $instance['roles'] ) ? $instance['roles'] : array(); ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'tcp' ); ?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'private' ); ?>" name="<?php echo $this->get_field_name( 'private' ); ?>" value="yes" <?php checked( $private ); ?> />
			<label for="<?php echo $this->get_field_id( 'private' ); ?>"><?php _e( 'Private', 'tcp' ); ?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'roles' ); ?>"><?php _e( 'Roles', 'tcp' ); ?>:</label>
			<select id="<?php echo $this->get_field_id( 'roles' ); ?>" name="<?php echo $this->get_field_name( 'roles' ); ?>[]" class="widefat" multiple size="4" style="height: auto">
				<option value=""><?php _e( 'All roles', 'tcp' ); ?></option>
				<?php global $wp_roles;
				if ( !isset( $wp_roles ) ) $wp_roles = new WP_Roles();
				foreach ( $wp_roles->role_names as $role => $name ) : ?>
				<option value="<?php echo $role; ?>" <?php tcp_selected_multiple( $roles, $role ); ?>><?php echo $name; ?></option>
				<?php endforeach; ?>
			</select>
			<p class="description"><?php _e( 'Notice: Roles are not hierarchical', 'tcp' ); ?></p>
		</p>
		<?php do_action( 'tcp_parent_widget_form', $this );
	}
}
} // class_exists check