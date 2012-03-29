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

class TCPWishList {

	function wp_head() {
		if ( isset( $_REQUEST['tcp_add_to_wish_list'] ) ) {
			$tcp_new_wish_list_item = isset( $_REQUEST['tcp_new_wish_list_item'] ) ? $_REQUEST['tcp_new_wish_list_item'] : 0;
			if ( $tcp_new_wish_list_item > 0 ) {
				$shoppingCart = TheCartPress::getShoppingCart();
				$shoppingCart->addWishList( $tcp_new_wish_list_item );
				do_action( 'tcp_add_wish_list', $tcp_new_wish_list_item );
			}
		} elseif ( isset( $_REQUEST['tcp_remove_from_wish_list'] ) ) {
			$post_id = isset( $_REQUEST['tcp_wish_list_post_id'] ) ? $_REQUEST['tcp_wish_list_post_id'] : 0;
			if ( $post_id > 0 ) {
				$shoppingCart = TheCartPress::getShoppingCart();
				$shoppingCart->deleteWishListItem( $post_id );
				do_action( 'tcp_delete_wish_list_item', $post_id );
			}
		} elseif ( isset( $_REQUEST['tcp_remove_wish_list'] ) ) {
			$shoppingCart = TheCartPress::getShoppingCart();
			$shoppingCart->deleteWishList();
			do_action( 'tcp_delete_wish_list' );
		}
	}

	function admin_init() {
		$tcp_settings_page = TCP_ADMIN_FOLDER . 'Settings.class.php';
		add_settings_field( 'enabled_wish_list', __( 'Enabled Wish list', 'tcp' ), array( $this, 'show_enabled_wish_list' ), $tcp_settings_page , 'tcp_main_section' );
	}
	
	function show_enabled_wish_list() {
		global $thecartpress;
		$enabled_wish_list = $thecartpress->get_setting( 'enabled_wish_list' ); ?>
		<input type="checkbox" id="enabled_wish_list" name="tcp_settings[enabled_wish_list]" value="yes" <?php checked( true, $enabled_wish_list ); ?> /><?php
	}

	function tcp_validate_settings( $input ) {
		$input['enabled_wish_list'] = isset( $input['enabled_wish_list'] ) ? $input['enabled_wish_list'] == 'yes' : false;
		return $input;
	}

	function admin_menu() {
		global $thecartpress;
		if ( ! $thecartpress->get_setting( 'enabled_wish_list', false ) ) return;
		$base = $thecartpress->get_base();
		add_submenu_page( $base, __( 'WishList', 'tcp' ), __( 'My wish List', 'tcp' ), 'tcp_edit_wish_list', TCP_ADMIN_FOLDER . 'WishList.php' );
	}

	function tcp_the_add_to_cart_button( $out, $post_id ) {
		global $thecartpress;
		if ( ! $thecartpress->get_setting( 'enabled_wish_list', false ) ) return $out;
		$shoppingCart = TheCartPress::getShoppingCart();
		if ( ! $shoppingCart->isInWishList( $post_id ) ) : 
			ob_start(); ?>
			<input type="hidden" value="" name="tcp_new_wish_list_item" id="tcp_new_wish_list_item_<?php echo $post_id; ?>" />
			<input type="submit" name="tcp_add_to_wish_list" class="tcp_add_to_wish_list" id="tcp_add_wish_list_<?php echo $post_id; ?>" value="<?php _e( 'Add to Wish list', 'tcp' ); ?>"
			onclick="jQuery('#tcp_new_wish_list_item_<?php echo $post_id; ?>').val('<?php echo $post_id; ?>');jQuery('#tcp_frm_<?php echo $post_id; ?>').attr('action', '');" />
			<?php do_action( 'tcp_buy_button_add_to_wish_list', $post_id );
			$out .= ob_get_clean();
		endif;
		return $out;
	}

	function widgets_init() {
		require_once( TCP_WIDGETS_FOLDER . 'WishListWidget.class.php' );
		register_widget( 'WishListWidget' );
	}

	function __construct() {
		if ( is_admin() ) {
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_filter( 'tcp_validate_settings', array( $this, 'tcp_validate_settings' ) );
		}
		global $thecartpress;
		if ( $thecartpress ) {
			if ( $thecartpress->get_setting( 'enabled_wish_list', false ) ) {
				if ( is_admin() ) {
					add_action( 'widgets_init', array( $this, 'widgets_init' ) );
				} else {
					//add_filter( 'tcp_the_add_to_cart_button', array( $this, 'tcp_the_add_to_cart_button' ), 10, 2 );
					add_action( 'wp_head', array( $this, 'wp_head' ) );
				}
			}
		}
	}
}

$wish_list = new TCPWishList();
?>
