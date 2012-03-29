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

class SortPanelWidget extends  WP_Widget {

	function SortPanelWidget() {
		$widget_settings = array(
			'classname'		=> 'sortpanel',
			'description'	=> __( 'Allow to create sort panels', 'tcp' ),
		);
		$control_settings = array(
			'width'		=> 300,
			'id_base'	=> 'sortpanel-widget'
		);
		$this->WP_Widget( 'sortpanel-widget', 'TCP Sort Panel', $widget_settings, $control_settings );
	}

	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( $title ) echo $before_title, $title, $after_title;
		tcp_the_sort_panel();
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}

	function form( $instance ) {
		$defaults = array(
			'title'	=> 'Sort Panel',
		);
		$instance = wp_parse_args( ( array ) $instance, $defaults );?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'tcp' )?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p><?php
	}
}
?>