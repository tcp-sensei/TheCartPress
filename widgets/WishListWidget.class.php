<?php
/**
 * Wish List widget
 *
 * Allows to display the list of products into the wishlist
 *
 * @package TheCartPress
 * @subpackage Widgets
 */

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

if ( !class_exists( 'WishListWidget' ) ) :

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
			'post_type'			=> tcp_get_saleable_post_types(), //'tcp_product', //TODO only for tcp_products?
			'posts_per_page'	=> $instance['limit'],
		);
		$instance['loop_args'] = $loop_args;
		//$instance['post_type'] = TCP_PRODUCT_POST_TYPE;
		$instance['post_type'] = tcp_get_saleable_post_types();

		//to add the remove and copy buttons
		add_action( 'tcp_after_loop_wishlist', array( $this, 'tcp_after_loop_wishlist' ) );
		add_action( 'tcp_after_loop_tcp_grid_item', array( $this, 'tcp_after_loop_tcp_grid_item' ) );
		parent::widget( $args, $instance );
		remove_action( 'tcp_after_loop_wishlist', array( $this, 'tcp_after_loop_wishlist' ) );
		remove_action( 'tcp_after_loop_tcp_grid_item', array( $this, 'tcp_after_loop_tcp_grid_item' ) );
	}

	function tcp_after_loop_tcp_grid_item( $post_id ) { ?>
<form method="post">
	<input type="hidden" name="tcp_wish_list_post_id" value="<?php echo $post_id;?>" />
	<button type="submit" name="tcp_remove_from_wish_list" id="tcp_remove_from_wish_list"
	 class="tcp_remove_from_item_wish_list <?php tcp_the_buy_button_color(); ?> <?php tcp_the_buy_button_size(); ?>"
	 title="<?php _e( 'Remove this item', 'tcp' );?>"><?php _e( 'Remove', 'tcp' );?></button>
	<script>
	jQuery( '#tcp_remove_from_wish_list' ).click( function() {
		return confirm( '<?php _e( 'Do you really want to remove this item? ', 'tcp' ); ?>' );
	} );
	</script>
</form><?php
	}

	function tcp_after_loop_wishlist() { ?>
<form method="post" class="tcp-wishlist-buttons">
	<button type="submit" name="tcp_remove_wish_list" id="tcp_remove_wish_list"
	 class="tcp_remove_all_wish_list <?php tcp_the_buy_button_color(); ?> <?php tcp_the_buy_button_size(); ?>"
	 title="<?php _e( 'Remove all items', 'tcp' );?>"><?php _e( 'Remove all', 'tcp' );?></button>
	<button type="submit" name="tcp_copy_wish_list_to_shopping_cart"
	 class="tcp_add_all_to_shopping_cart <?php tcp_the_buy_button_color(); ?> <?php tcp_the_buy_button_size(); ?>"
	 title="<?php _e( 'Add all items into cart', 'tcp' );?>"><?php _e( 'Add all', 'tcp' );?></button>
	<script>
	jQuery( '#tcp_remove_wish_list' ).click( function() {
		return confirm( '<?php _e( 'Do you really want to remove all items? ', 'tcp' ); ?>' );
	} );
	</script>
</form><?php
	}

	function form( $instance ) {
		if ( !isset( $instance['title'] ) ) $instance['title'] = __( 'Wish List', 'tcp');
		parent::form( $instance );
		parent::show_post_type_form( $instance );
	}
}
endif; // class_exists check