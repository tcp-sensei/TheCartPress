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

require_once( TCP_WIDGETS_FOLDER . 'TCPParentWidget.class.php' );

class BREBPLogin extends TCPParentWidget {

	function BREBPLogin() {
		parent::__construct( 'bre_bp_login', __( 'Allow to create a buddyPress login', 'bre' ), 'TCP BP Login' );
	}

	function widget( $args, $instance ) {
		if ( ! parent::widget( $args, $instance ) ) return;
		extract( $args );
		echo $before_widget;
		$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : false );
		if ( $title ) echo $before_title, $title, $after_title;
?>Hola<?php
		echo $after_widget;

	}

	function update( $new_instance, $old_instance ) {
		$instance = parent::update( $new_instance, $old_instance );
		return $instance;
	}

	function form( $instance ) {
		parent::form( $instance, __( 'Login', 'tcp' ) );
	}
}

?>