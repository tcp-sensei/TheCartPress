<?php
/**
 * Copy Order
 *
 * Adds a copy to Shopping cart button to the FrontEnd module.
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

class TCPCopyOrder {
	static function initModule() {
		add_action( 'tcp_init'							, array( __CLASS__, 'tcp_init' ) );
		//Front end support
		add_filter( 'tcp_front_end_orders_columns'		, array( __CLASS__, 'tcp_front_end_orders_columns' ) );
		add_action( 'tcp_front_end_orders_cells'		, array( __CLASS__, 'tcp_front_end_orders_cells' ) );
		add_action( 'tcp_front_end_orders_order_view'	, array( __CLASS__, 'tcp_front_end_orders_cells' ) );
		if ( is_admin() ) {
			add_action( 'tcp_admin_order_submit_area'	, array( __CLASS__, 'tcp_admin_order_submit_area' ) );
		}
	}

	static function tcp_init() {
		if ( isset( $_REQUEST['tcp_copy_order_to_shopping_cart'] ) ) {
			$order_id = isset( $_REQUEST['tcp_copy_order_order_id'] ) ? $_REQUEST['tcp_copy_order_order_id'] : 0;
			$current_user = wp_get_current_user();
			require_once( TCP_DAOS_FOLDER . 'Orders.class.php' );
			if ( Orders::is_owner( $order_id, $current_user->ID ) ) {
				$shoppingCart = TheCartPress::getShoppingCart();
				$shoppingCart->deleteAll();
				$details = OrdersDetails::getDetails( $order_id );
				foreach( $details as $detail ) {
					$unit_price = tcp_get_the_product_price( $detail->post_id );
					//$unit_price = tcp_get_the_price( $detail->post_id, false );
					$unit_weight = tcp_get_the_weight( $detail->post_id );
					if ( $detail->option_1_id > 0 ) $unit_price += tcp_get_the_price( $detail->option_1_id );
					if ( $detail->option_2_id > 0 ) $unit_price += tcp_get_the_price( $detail->option_2_id );
					$shoppingCart->add( $detail->post_id, $detail->option_1_id, $detail->option_2_id, $detail->qty_ordered, $unit_price, $unit_weight );
				}
				do_action( 'tcp_add_shopping_cart' );
				wp_redirect( tcp_get_the_shopping_cart_url() );
			}
		}
	}

	static function tcp_admin_order_submit_area( $order_id ) { ?>
		<input type="hidden" name="tcp_copy_order_order_id" value="<?php echo $order_id; ?>" />
		<button name="tcp_copy_order_to_shopping_cart" type="submit" class="btn btn-success"><?php _e( 'Copy to Shopping Cart', 'tcp' ); ?></button>
	<?php }

	static function tcp_front_end_orders_columns( $cols ) {
		$cols[] = __( 'Actions', 'tcp-fe' );
		return $cols;
	}

	static function tcp_front_end_orders_cells( $order_id) { ?>
		<td class="tcp_copy_order">
		<form method="post" action="<?php tcp_the_shopping_cart_url(); ?>">
		<?php TCPCopyOrder::tcp_admin_order_submit_area( $order_id ); ?>
		</form>
		</td>
	<?php }
}

TCPCopyOrder::initModule();
?>