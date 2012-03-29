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

class TCPSelectCountryWidget extends WP_Widget {
	function TCPSelectCountryWidget() {
		$widget = array(
			'classname'		=> 'tcpselectcountry',
			'description'	=> __( 'Use this widget to allow customers to select theirs countries', 'tcp' ),
		);
		$control = array(
			'width'		=> 300,
			'id_base'	=> 'tcpselectcountry-widget',
		);
		$this->WP_Widget( 'tcpselectcountry-widget', 'TCP Select Country', $widget, $control );
	}

	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : ' ' );
		echo $before_widget;
		if ( $title ) echo $before_title, $title, $after_title;
		$instance['widget_id'] = $widget_id;
		require_once( TCP_CLASSES_FOLDER . 'CountrySelection.class.php' );
		TCPCountrySelection::show();
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance = apply_filters( 'tcp_select_country_widget_update', $instance, $new_instance );
		return $instance;
	}

	function form( $instance ) {
		$defaults = array(
			'title'	=> __( 'Select your country', 'tcp' ),
		);
		$instance = wp_parse_args( ( array ) $instance, $defaults ); ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'tcp' )?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<?php do_action( 'tcp_select_country_widget_form', $this, $instance );
	}
}

?>