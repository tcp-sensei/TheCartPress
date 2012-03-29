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

require_once( dirname( __FILE__) . '/CustomListWidget.class.php' );

class WishListWidget extends CustomListWidget {

	function WishListWidget() {
		parent::CustomListWidget( 'tcpwishlist', __( 'Allow to display Wish List', 'tcp' ), 'TCP Wish List' );
	}

	function widget( $args, $instance ) {
		$shoppingCart = TheCartPress::getShoppingCart();
		$wishList = $shoppingCart->getWishList();
		$ids = array_keys( $wishList );
		if ( count( $ids ) == 0 ) return;
		$loop_args = array(
			'post__in'			=> $ids,
			'post_type'			=> 'tcp_product', //TODO only for tcp_products?
			'posts_per_page'	=> $instance['limit'],
		);
		add_action( 'tcp_after_loop_tcp_grid', array( $this, 'tcp_after_loop_tcp_grid' ) );
		add_action( 'tcp_after_loop_tcp_grid_item', array( $this, 'tcp_after_loop_tcp_grid_item' ) );
		parent::widget( $args, $loop_args, $instance );
		remove_action( 'tcp_after_loop_tcp_grid', array( $this, 'tcp_after_loop_tcp_grid' ) );
		remove_action( 'tcp_after_loop_tcp_grid_item', array( $this, 'tcp_after_loop_tcp_grid_item' ) );
	}

	function tcp_after_loop_tcp_grid() { ?>
		<form method="post">
		<input type="submit" name="tcp_remove_wish_list" class="tcp_remove_wish_list" value="<?php _e( 'delete all', 'tcp' );?>" />
		<!--<input type="submit" name="tcp_buy_wish_list" value="<?php _e( 'buy all', 'tcp' );?>" />-->
		</form><?php
	}

	function tcp_after_loop_tcp_grid_item( $post_id ) { ?>
		<form method="post">
		<input type="hidden" name="tcp_wish_list_post_id" value="<?php echo $post_id;?>" />
		<input type="submit" name="tcp_remove_from_wish_list" class="tcp_remove_from_wish_list" value="<?php _e( 'delete', 'tcp' );?>" />
		</form><?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		return parent::update( $new_instance, $instance );
	}

	function form( $instance ) {
		$defaults = array(
			'title'	=> __( 'Wish List', 'tcp' ),
		);
		$instance = wp_parse_args( ( array ) $instance, $defaults ); ?>
		<div id="particular">
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'tcp' )?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		</div><?php
		parent::form( $instance );
		parent::show_post_type_form( $instance );
	}
}
?>
