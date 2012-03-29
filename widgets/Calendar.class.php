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

class TCPCalendar extends  WP_Widget {

	function TCPCalendar() {
			$widget_settings = array(
			'classname'		=> 'tcp_calendar',
			'description'	=> __( 'Allow to create post type calendars', 'tcp' ),
		);
		$control_settings = array(
			'width'		=> 300,
			'id_base'	=> 'tcp_calendar-widget'
		);
		$this->WP_Widget( 'tcp_calendar-widget', 'TCP Calendar', $widget_settings, $control_settings );
	}

	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( $title ) echo $before_title, $title, $after_title;
		$args['post_type'] = $instance['post_type'];
		tcp_get_calendar( $args );
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		tcp_delete_get_calendar_cache();
		$instance = $old_instance;
		$instance['title']		= strip_tags( $new_instance['title'] );
		$instance['post_type']	= strip_tags( $new_instance['post_type'] );
		return $instance;
	}

	function form( $instance ) {
		$defaults = array(
			'title'	=> __( 'Calendar', 'tcp' ),
		);
		$instance = wp_parse_args( ( array ) $instance, $defaults );?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'tcp' )?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( 'Post type', 'tcp' )?>:</label>
		<select name="<?php echo $this->get_field_name( 'post_type' ); ?>" id="<?php echo $this->get_field_id( 'post_type' ); ?>" class="widefat">
		<?php foreach( get_post_types( array( 'show_in_nav_menus' => true ) ) as $post_type ) : 
			if ( $post_type != 'tcp_product_option' ) :
				$obj_type = get_post_type_object( $post_type ); ?>
			<option value="<?php echo $post_type;?>"<?php selected( $instance['post_type'], $post_type ); ?>><?php echo $obj_type->labels->singular_name; ?></option>
			<?php endif;
		endforeach;?>
		</select>
		</p><?php
	}
}
?>
