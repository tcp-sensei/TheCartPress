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
 * along with This program.  If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'LastVisitedWidget' ) ) {

require_once( TCP_WIDGETS_FOLDER . 'CustomListWidget.class.php' );

class LastVisitedWidget extends CustomListWidget {

	function LastVisitedWidget() {
		parent::CustomListWidget( 'tcplastvisited', __( 'Allow to create a Last Visited List', 'tcp' ), 'TCP Last visited List' );
		add_action( 'wp_footer', array( $this, 'annotate_last_visited' ) );
	}

	function widget( $args, $instance ) {
		$shoppingCart = TheCartPress::getShoppingCart();
		$visitedPosts = $shoppingCart->getVisitedPosts();
		$ids = array_keys( $visitedPosts );
		if ( count( $ids ) == 0 ) return;
		$id_to_remove = array_search( get_the_ID(), $ids );
		if ( $id_to_remove ) unset( $ids[$id_to_remove] );
		$ids = array_reverse( $ids );
		$limit = isset( $instance['limit'] ) ? $instance['limit'] : -1;
		if ( $limit > 0 && $limit < count( $ids ) ) $ids = array_slice( $ids, 0, $limit );
		$loop_args = array(
			'post__in'			=> $ids,
			'post_type'			=> tcp_get_saleable_post_types(), //TCP_PRODUCT_POST_TYPE,
			'posts_per_page'	=> $limit
		);
		$instance['loop_args'] = $loop_args;
		parent::widget( $args, $instance );
	}

	function form( $instance ) {
		if ( ! isset( $instance['title'] ) ) $instance['title'] = __( 'Last Visited', 'tcp' );
		parent::form( $instance );
		parent::show_post_type_form( $instance );
	}

	function annotate_last_visited() {
		if ( is_single() && ! is_page() ) {
			global $post;
			if ( tcp_is_saleable_post_type( $post->post_type ) ) {
				do_action( 'tcp_visited_product', $post );
				$shoppingCart = TheCartPress::getShoppingCart();
				$shoppingCart->addVisitedPost( $post->ID );
				//$shoppingCart->deleteVisitedPost();
			}
		}
	}
}
} // class_exists check