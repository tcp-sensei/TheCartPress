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

class AttributesListWidget extends WP_Widget {

	function AttributesListWidget() {
		$widget_settings = array(
			'classname'		=> 'attributeslist',
			'description'	=> __( 'Allow to display attributes associated with a products/post', 'tcp' ),
		);
		$control_settings = array(
			'width'		=> 300,
			'id_base'	=> 'attributeslist-widget'
		);
		$this->WP_Widget( 'attributeslist-widget', 'TCP Attributes List', $widget_settings, $control_settings );
	}

	function widget( $args, $instance ) {
		if ( is_single() ) {
			extract( $args );
			$title = apply_filters( 'widget_title', $instance['title'] );
			echo $before_widget;
			if ( $title ) echo $before_title, $title, $after_title;
			tcp_attribute_list( $instance['taxonomies'] );
			echo $after_widget;
		}
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title']			= strip_tags( $new_instance['title'] );
		$instance['post_type']		= $new_instance['post_type'];
		$instance['taxonomies']		= $new_instance['taxonomies'][0] == '' ? false : $new_instance['taxonomies'];
		return $instance;
	}

	function form( $instance ) {
		$defaults = array(
			'title'			=> 'Attributes List',
			'post_type'		=> TCP_PRODUCT_POST_TYPE,
			'taxonomies'	=> array(),
		);
		$instance = wp_parse_args( (array)$instance, $defaults ); ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'tcp' )?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( 'Post type', 'tcp' )?>:</label>
			<select name="<?php echo $this->get_field_name( 'post_type' ); ?>" id="<?php echo $this->get_field_id( 'post_type' ); ?>" class="widefat">
			<?php foreach( get_post_types( array( 'show_in_nav_menus' => true ), object ) as $post_type ) : ?>
				<option value="<?php echo $post_type->name;?>"<?php selected( $instance['post_type'], $post_type->name ); ?>><?php echo $post_type->labels->name; ?></option>
			<?php endforeach; ?>
			</select>
			<span class="description"><?php _e( 'Press save to load the taxonomies', 'tcp' );?></span>
		</p>
		<p style="margin-top:0;">
			<label for="<?php echo $this->get_field_id( 'taxonomies' ); ?>"><?php _e( 'Taxonomies', 'tcp' )?>:</label>
			<select name="<?php echo $this->get_field_name( 'taxonomies' ); ?>[]" id="<?php echo $this->get_field_id( 'taxonomies' ); ?>" class="widefat" multiple size="8" style="height: auto">
				<option value="" <?php selected( $instance['taxonomies'], '' ); ?>><?php _e( 'all', 'tcp' );?></option>
			<?php foreach( get_object_taxonomies( $instance['post_type'] ) as $taxonomy ) : $tax = get_taxonomy( $taxonomy ); ?>
				<option value="<?php echo esc_attr( $taxonomy );?>" <?php tcp_selected_multiple( $instance['taxonomies'], $taxonomy ); ?>><?php echo esc_attr( $tax->labels->name );?></option>
			<?php endforeach;?>
			</select>
		</p><?php
	}
}
?>