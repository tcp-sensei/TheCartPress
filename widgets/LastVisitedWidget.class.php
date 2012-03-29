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

require_once( TCP_WIDGETS_FOLDER . 'CustomListWidget.class.php' );

class LastVisitedWidget extends CustomListWidget {

	function LastVisitedWidget() {
		parent::CustomListWidget( 'tcplastvisited', __( 'Allow to create a Last Visited List', 'tcp' ), 'TCP Last visited List' );
	}

	function widget( $args, $instance ) {
		$shoppingCart = TheCartPress::getShoppingCart();
		$visitedPosts = $shoppingCart->getVisitedPosts();
		$ids = array_keys( $visitedPosts );
		if ( count( $ids ) == 0 ) return;
		$loop_args = array(
			'post__in'			=> $ids,
			'post_type'			=> tcp_get_saleable_post_types(), //TCP_PRODUCT_POST_TYPE,
			'posts_per_page'	=> $instance['limit'],
		);
		parent::widget( $args, $loop_args, $instance );
	}

	function form( $instance ) {
		parent::form( $instance, __( 'Last Visited', 'tcp' ) );
		parent::show_post_type_form( $instance );
	}
}
?>
