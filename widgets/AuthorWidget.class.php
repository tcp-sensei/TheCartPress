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
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'TCPAuthorWidget' ) ) :

require_once( TCP_WIDGETS_FOLDER . 'TCPParentWidget.class.php' );

class TCPAuthorWidget extends TCPParentWidget {
	function TCPAuthorWidget() {
		parent::__construct( 'tcpauthorprofile', __( 'Allow to display Author Profile', 'tcp' ), 'TCP Author Profile' );
	}

	function widget( $args, $instance ) {
		if ( !parent::widget( $args, $instance ) ) return;
		extract( $args );
		echo $before_widget;
		$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : false );
		if ( $title ) echo $before_title, $title, $after_title;
		global $post;
		if ( ! empty( $post ) ) {
			$current_user = new WP_User( $post->post_author );
		} else {
			$current_user = get_query_var( 'author_name' ) ? get_user_by( 'slug', get_query_var( 'author_name' ) ) : get_userdata( get_query_var( 'author' ) );
			if ( $current_user === false ) {
				$current_user = get_the_author();
				$current_user = get_user_by( 'login', $current_user );
			}
		}
		tcp_author_profile( $current_user );
		echo $after_widget;
	}
}
endif;