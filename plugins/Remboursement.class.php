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

if ( ! class_exists( 'TCPRemboursement' ) ) {

class TCPRemboursement extends TCP_Plugin {

	function getTitle() {
		return __( 'Cash on delivery', 'tcp' );
	}

	function getIcon() {
		return plugins_url( 'thecartpress/images/cash-on-delivery.png' );
	}

	function getDescription() {
		return __( 'Cash on delivery payment method.<br>Author: <a href="http://thecartpress.com" target="_blank">TheCartPress team</a>', 'tcp' );
	}

	function showEditFields( $data, $instance = 0 ) {?>
		<tr valign="top">
			<th scope="row">
				<label for="notice"><?php _e( 'Notice', 'tcp' );?>:</label>
			</th><td>
				<textarea id="notice" name="notice" cols="40" rows="4" maxlength="500"><?php echo isset( $data['notice'] ) ? $data['notice'] : '';?></textarea>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="percentage"><?php _e( 'Percentage', 'tcp' );?>:</label>
			</th><td>
				<input type="text" id="percentage" name="percentage" size="5" maxlength="8" value="<?php echo isset( $data['percentage'] ) ? $data['percentage'] : '';?>" />
				<br /><span class="description"><?php _e( 'Leave this field to blank (or zero) to use the fixed value', 'tcp' );?></span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="fix"><?php _e( 'Amount', 'tcp' );?>:</label>
			</th><td>
				<input type="text" id="fix" name="fix" size="5" maxlength="8" value="<?php echo isset( $data['fix'] ) ? $data['fix'] : '';?>" />
			</td>
		</tr><?php
	}

	function saveEditFields( $data, $instance = 0 ) {
		$data['notice']		= isset( $_REQUEST['notice'] ) ? $_REQUEST['notice'] : '';
		//tcp_register_string( 'TheCartPress', 'pay_TCPRemboursement-notice', $data['notice'] );
		tcp_register_string( 'TheCartPress', apply_filters( 'tcp_plugin_data_get_option_translatable_key', 'pay_TCPRemboursement-notice-' . $instance ), $data['notice'] );
		$data['percentage']	= isset( $_REQUEST['percentage'] ) ? (float)$_REQUEST['percentage'] : '0';
		$data['fix']		= isset( $_REQUEST['fix'] ) ? (float)$_REQUEST['fix'] : '0';
		return $data;
	}

	function sendPurchaseMail() {
		return false;
	}

	function getCheckoutMethodLabel( $instance, $shippingCountry = '', $shoppingCart = false ) {
		$data	= tcp_get_payment_plugin_data( get_class( $this ), $instance );
		$title	= isset( $data['title'] ) ? $data['title'] : '';
		$title	= tcp_string( 'TheCartPress', apply_filters( 'tcp_plugin_data_get_option_translatable_key', 'pay_TCPRemboursement-title-' . $instance ), $title );
		$cost	= tcp_get_the_shipping_cost_to_show( $this->getCost( $instance, $shippingCountry, $shoppingCart ) );
		//return sprintf( __( '%s. Cost: %s', 'tcp' ), $title, tcp_format_the_price( $cost ) );
		ob_start(); ?>
		<?php if ( $cost > 0 ) printf( __( '%s, Cost: %s', 'tcp' ), $title, tcp_format_the_price( $cost ) );
		else echo $title; ?>
		<?php if ( false && strlen( trim( $data['notice'] ) ) > 0 ) : ?>
			<p><?php //echo tcp_string( 'TheCartPress', 'pay_TCPRemboursement-notice', $data['notice'] ); ?>
			<?php echo tcp_string( 'TheCartPress', apply_filters( 'tcp_plugin_data_get_option_translatable_key', 'pay_TCPRemboursement-notice-' . $instance ), $data['notice'] ); ?></p>
			pay_TCPRemboursement
		<?php endif;
		return ob_get_clean();
	}

	function getCost( $instance, $shippingCountry, $shoppingCart = false ) {
		$data = tcp_get_payment_plugin_data( get_class( $this ), $instance );
		$percentage	= isset( $data['percentage'] ) ? $data['percentage'] : 0;
		if ( $percentage > 0 ) {
			if ( $shoppingCart === false ) $shoppingCart = TheCartPress::getShoppingCart();
			return $shoppingCart->getTotal() * $percentage / 100;
		} else {
			$fix = isset( $data['fix'] ) ? $data['fix'] : 0;
			return $fix;
		}
	}

	function getNotice( $instance, $shippingCountry, $shoppingCart, $order_id = 0 ) {
		$data = tcp_get_payment_plugin_data( get_class( $this ), $instance );
		return isset( $data['notice'] ) ? $data['notice'] : '';
	}

	function showPayForm( $instance, $shippingCountry, $shoppingCart, $order_id = 0 ) {
		global $thecartpress;
		$data	= tcp_get_payment_plugin_data( get_class( $this ), $instance, $order_id );
		$title	= isset( $data['title'] ) ? $data['title'] : '';
		$redirect = true;
		$cost	= $this->getCost( $instance, $shippingCountry, $shoppingCart );
		$url	= add_query_arg( 'tcp_checkout', 'ok', tcp_get_the_checkout_url() );
		$url	= add_query_arg( 'order_id', $order_id, $url );
		if ( $cost > 0 ) printf( __( '%s, Cost: %s', 'tcp' ), $title, tcp_format_the_price( $cost ) );
		else echo $title;
		$additional = $this->getNotice( $instance, $shippingCountry, $shoppingCart, $order_id ); ?>
		<p><?php echo $additional; ?></p>
		<p><input type="button" class="tcp_pay_button" id="tcp_remboursement" value="<?php _e( 'Finish', 'tcp' );?>" onclick="window.location.href='<?php echo $url; ?>';"/></p>
		<?php $new_status = isset( $data['new_status'] ) ? $data['new_status'] : Orders::$ORDER_PROCESSING;
		Orders::editStatus( $order_id, $new_status, 'no-id' );
		require_once( TCP_CHECKOUT_FOLDER . 'ActiveCheckout.class.php' );
		ActiveCheckout::sendMails( $order_id, $additional );
		if ( $redirect ) : ?>
		<script type="text/javascript">
		//jQuery().ready( function() {
			jQuery( '#tcp_remboursement' ).click();
		//} );
		</script><?php endif;
	}
}
} // class_exists check