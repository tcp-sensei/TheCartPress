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
 
require_once( TCP_WIDGETS_FOLDER . 'CustomListWidget.class.php' );

class TopSellersWidget extends CustomListWidget {

	function TopSellersWidget() {
		parent::__construct( 'topsellers', __( 'Allow to display Top Sellers', 'tcp' ), 'TCP Top Sellers' );
	}

	function widget( $args, $instance ) {
		extract( $args );
		global $wp_query;
		$paged = isset( $wp_query->query_vars['paged'] ) ? $wp_query->query_vars['paged'] : 1;
		$loop_args = array(
			'post_type'			=> tcp_get_saleable_post_types(), //isset( $instance['post_type'] ) ? $instance['post_type'] : TCP_PRODUCT_POST_TYPE,
			'posts_per_page'	=> isset( $instance['limit'] ) ? $instance['limit'] : -1,
			'meta_key'			=> 'tcp_total_sales',
			'orderby'			=> 'meta_value_num',
			'order'				=> 'desc',
		);
		$see_pagination = isset( $instance['pagination'] ) ? $instance['pagination'] : false;
		if ( $see_pagination ) {
			$loop_args['paged'] = $paged;
		}
		$loop_args = apply_filters( 'tcp_top_sellers_widget', $loop_args, $instance );
		$instance['order_type'] = '';
		$instance['order_desc'] = '';
		parent::widget( $args, $loop_args, $instance );
	}

	function form( $instance, $title = '' ) {
		parent::form( $instance, __( 'Top Seller!s', 'tcp') );
		parent::show_post_type_form( $instance );
	}
}
?>
