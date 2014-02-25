<?php
/**
 * WishList
 *
 * Allows manage a WishList for registered, and logged, users
 *
 * @package TheCartPress
 * @subpackage Modules
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
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'TCPWishList' ) ) :

class TCPWishList {

	function __construct() {
		add_action( 'tcp_init'		, array( $this, 'tcp_init' ) );
		add_action( 'tcp_admin_init', array( $this, 'tcp_admin_init' ) );
	}

	function tcp_init( $thecartpress ) {
		if ( $thecartpress->get_setting( 'enabled_wish_list', false ) ) {
			if ( is_admin() ) {
				add_action( 'widgets_init'	, array( $this, 'widgets_init' ) );
			 } else {
				add_action( 'tcp_init'		, array( $this, 'wp_head' ), 99 );
			}
		}
	}

	function tcp_admin_init( $thecartpress ) {
		add_action( 'tcp_main_settings_after_page'	, array( $this, 'tcp_main_settings_after_page' ) );
		add_filter( 'tcp_main_settings_action'		, array( $this, 'tcp_main_settings_action' ) );
		add_action( 'tcp_admin_menu'				, array( $this, 'tcp_admin_menu' ) );
	}

	function wp_head() {
		$this->check_for_shopping_cart_actions();
	}

	private function check_for_shopping_cart_actions() {
		if ( isset( $_REQUEST['tcp_add_to_wish_list'] ) ) {
			unset( $_REQUEST['tcp_add_to_wish_list'] );
			if ( !isset( $_REQUEST['tcp_post_id'] ) ) return;
			if ( !is_array( $_REQUEST['tcp_post_id'] ) ) $_REQUEST['tcp_post_id'] = (array)$_REQUEST['tcp_post_id'];
			if ( !is_array( $_REQUEST['tcp_count'] ) ) $_REQUEST['tcp_count'] = (array)$_REQUEST['tcp_count'];
			do_action( 'tcp_before_add_wish_list', $_REQUEST['tcp_post_id'] );

			//The Wishlist is stored in the shopping cart
			$shoppingCart = TheCartPress::getShoppingCart();
			for( $i = 0; $i < count( $_REQUEST['tcp_post_id'] ); $i++ ) {
				$count = isset( $_REQUEST['tcp_count'][$i] ) ? (int)$_REQUEST['tcp_count'][$i] : 0;
				if ( $count > 0 ) {
					$post_id = isset( $_REQUEST['tcp_post_id'][$i] ) ? $_REQUEST['tcp_post_id'][$i] : 0;
					$post_id = tcp_get_default_id( $post_id, get_post_type( $post_id ) );
					$post_id = apply_filters( 'tcp_add_wish_list', $post_id, $i );
					$shoppingCart->addWishList( $post_id );
				}
			}
			do_action( 'tcp_add_to_wish_list', $_REQUEST['tcp_post_id'] );
		} elseif ( isset( $_REQUEST['tcp_remove_wish_list'] ) ) {
			$shoppingCart = TheCartPress::getShoppingCart();
			$shoppingCart->deleteWishList();
			do_action( 'tcp_delete_wish_list' );
		} elseif ( isset( $_REQUEST['tcp_remove_from_wish_list'] ) ) {
			$post_id = isset( $_REQUEST['tcp_wish_list_post_id'] ) ? $_REQUEST['tcp_wish_list_post_id'] : 0;
			if ( $post_id > 0 ) {
				$shoppingCart = TheCartPress::getShoppingCart();
				$shoppingCart->deleteWishListItem( $post_id );
				do_action( 'tcp_delete_wish_list_item', $post_id );
			}
		} elseif ( isset( $_REQUEST['tcp_copy_wish_list_to_shopping_cart'] ) ) {
			$shoppingCart = TheCartPress::getShoppingCart();
			$wishList = $shoppingCart->getWishList();
			$i = 0;
			foreach( $wishList as $post_id => $qty ) {
				//$unit_price = tcp_get_the_product_price( $post_id );
				$count = 1;
				$post_id = tcp_get_default_id( $post_id, get_post_type( $post_id ) );
				$unit_price = tcp_get_the_price( $post_id );
				$unit_price	= apply_filters( 'tcp_price_to_add_to_shoppingcart', $unit_price, $post_id );
				$unit_weight = tcp_get_the_weight( $post_id );
				$args = compact( 'i', 'post_id', 'count', 'unit_price', 'unit_weight' );
				$args = apply_filters( 'tcp_add_item_shopping_cart', $args );
				extract( $args );
				$shoppingCart->add( $post_id, 0, 0, 1, $unit_price, $unit_weight );
				$i++;
			}
			//do_action( 'tcp_copy_wish_list_to_shopping_cart' );
			do_action( 'tcp_add_shopping_cart', array_keys ( $wishList ) );
			wp_redirect( tcp_get_the_shopping_cart_url() );
		}
	}

	function tcp_main_settings_after_page() {
		global $thecartpress;
		$enabled_wish_list = $thecartpress->get_setting( 'enabled_wish_list', false ); ?>

<h3><?php _e( 'WishList', 'tcp' ); ?></h3>

<div class="postbox">
<div class="inside">
<table class="form-table">
<tbody>
<tr valign="top">
	<th scope="row">
		<label for="enabled_wish_list"><?php _e( 'Enabled Wish List', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="checkbox" id="enabled_wish_list" name="enabled_wish_list" value="yes" <?php checked( true, $enabled_wish_list ); ?> />
	</td>
</tr>
</tbody>
</table>
</div>
</div><!-- .postbox --> <?php
	}
	
	function tcp_main_settings_action( $settings ) {
		$settings['enabled_wish_list'] = isset( $_POST['enabled_wish_list'] ) ? $_POST['enabled_wish_list'] == 'yes' : false;
		return $settings;
	}

	function tcp_admin_menu( $thecartpress ) {
		if ( ! $thecartpress->get_setting( 'enabled_wish_list', false ) ) return;
		$base = $thecartpress->get_base();
		add_submenu_page( $base, __( 'Wish List', 'tcp' ), __( 'My Wish List', 'tcp' ), 'tcp_edit_wish_list', TCP_ADMIN_FOLDER . 'WishList.php' );
	}

	function tcp_the_add_to_cart_button( $out, $post_id ) {
		global $thecartpress;
		if ( !$thecartpress->get_setting( 'enabled_wish_list', false ) ) return $out;
		$shoppingCart = TheCartPress::getShoppingCart();
		if ( ! $shoppingCart->isInWishList( $post_id ) ) {
			ob_start(); ?>
			<input type="hidden" value="" name="tcp_new_wish_list_item" id="tcp_new_wish_list_item_<?php echo $post_id; ?>" />
			<button type="submit" name="tcp_add_to_wish_list" class="tcp_add_to_wish_list <?php tcp_the_buy_button_color(); ?> <?php tcp_the_buy_button_size(); ?>" id="tcp_add_wish_list_<?php echo $post_id; ?>"
			onclick="jQuery('#tcp_new_wish_list_item_<?php echo $post_id; ?>').val('<?php echo $post_id; ?>');jQuery('#tcp_frm_<?php echo $post_id; ?>').attr('action', '');"><?php _e( 'Add to Wish list', 'tcp' ); ?></button>
			<?php do_action( 'tcp_buy_button_add_to_wish_list', $post_id );
			$out .= ob_get_clean();
		}
		return apply_filters( 'tcp_wishlist_the_add_to_cart_button', $out, $post_id );
	}

	function widgets_init() {
		require_once( TCP_WIDGETS_FOLDER . 'WishListWidget.class.php' );
		register_widget( 'WishListWidget' );
	}
}

$GLOBALS['wish_list'] = new TCPWishList();
endif; // class_exists check