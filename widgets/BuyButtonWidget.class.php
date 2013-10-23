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

class BuyButtonWidget extends WP_Widget {
	function BuyButtonWidget() {
		$widget = array(
			'classname'		=> 'buybutton',
			'description'	=> __( 'Use this widget to add a buy button', 'tcp' ),
		);
		$control = array(
			'width'		=> 300,
			'id_base'	=> 'buybutton-widget',
		);
		$this->WP_Widget( 'buybutton-widget', 'TCP Buy Button', $widget, $control );
	}

	function widget( $args, $instance ) {
	if ( ! is_single() ) return;
		global $post;
		if ( $post ) {
			extract( $args );
			$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : ' ' );
			echo $before_widget;
			if ( $title ) echo $before_title, $title, $after_title;
			tcp_the_buy_button( $post->ID );
			echo $after_widget;
		}
	}
}
?>