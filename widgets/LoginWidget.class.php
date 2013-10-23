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

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'TCPLoginWidget' ) ) {

class TCPLoginWidget extends WP_Widget {

	function TCPLoginWidget() {
		$widget_settings = array(
			'classname'		=> 'tcp_login',
			'description'	=> __( 'Allow to display a login form', 'tcp' ),
		);
		$control_settings = array(
			'width'		=> 300,
			'id_base'	=> 'tcp_login-widget'
		);
		$this->WP_Widget( 'tcp_login-widget', 'TCP Login Form', $widget_settings, $control_settings );
	}

	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( $title ) echo $before_title, $title, $after_title;
		$args = array();
		if ( strlen( $instance['redirect'] ) > 0 ) $args['redirect'] = $instance['redirect'];
		$args = apply_filters( 'tcp_login_form_widget_args', $args, $instance );
		tcp_login_form( $args );
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		tcp_delete_get_calendar_cache();
		$instance = $old_instance;
		$instance['title']		= strip_tags( $new_instance['title'] );
		$instance['redirect']	= strip_tags( $new_instance['redirect'] );
		return $instance;
	}

	function form( $instance ) {
		$defaults = array(
			'title'	=> __( 'Login Form', 'tcp' ),
		);
		$instance = wp_parse_args( ( array ) $instance, $defaults );?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'tcp' )?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( isset( $instance['title'] ) ? $instance['title'] : '' ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'redirect' ); ?>"><?php _e( 'Redirect', 'tcp' )?>:</label>
			<input type="text" name="<?php echo $this->get_field_name( 'redirect' ); ?>" id="<?php echo $this->get_field_id( 'redirect' ); ?>" class="widefat" value="<?php echo esc_attr( isset( $instance['redirect'] ) ? $instance['redirect'] : '' ); ?>">
		</p><?php
	}
}

} // class_exists check