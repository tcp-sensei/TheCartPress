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

class CheckoutWidget extends WP_Widget {

	function CheckoutWidget() {
		$widget_settings = array(
			'classname'		=> 'checkout',
			'description'	=> __( 'Allow to view the Checkout info (for debugging purpose)', 'tcp' ),
		);
		$control_settings = array(
			'width'		=> 300,
			'id_base'	=> 'checkout-widget'
		);
		$this->WP_Widget( 'checkout-widget', 'TCP Checkout', $widget_settings, $control_settings );
	}

	function widget( $args, $instance ) {
		if ( is_page( get_option( 'tcp_checkout_page_id' ) ) ) {
			extract( $args );
			$title = apply_filters( 'widget_title', $instance['title'] );
			echo $before_widget;
			if ( isset( $_SESSION['tcp_checkout'] ) ) {
				if ( $title ) echo $before_title, $title, $after_title;
				?><pre><?php var_dump( $_SESSION['tcp_checkout'] );?></pre><?php
			}
			echo $after_widget;
		}
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}

	function form( $instance ) {
		$defaults = array(
			'title'	=> 'Checkout',
		);
		$instance = wp_parse_args( ( array ) $instance, $defaults );?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'tcp' )?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p><?php
	}
}
?>
