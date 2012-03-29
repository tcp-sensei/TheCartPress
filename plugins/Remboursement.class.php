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

class TCPRemboursement extends TCP_Plugin {

	function getTitle() {
		return 'Cash on delivery';
	}

	function getDescription() {
		return __( 'Cash on delivery payment method.<br>Author: <a href="http://thecartpress.com" target="_blank">TheCartPress team</a>', 'tcp' );
	}

	function showEditFields( $data ) {?>
		<tr valign="top">
			<th scope="row">
				<label for="notice"><?php _e( 'Notice', 'tcp' );?>:</label>
			</th><td>
				<textarea id="notice" name="notice" cols="40" rows="4" maxlength="500"><?php echo isset( $data['notice'] ) ? $data['notice'] : '';?></textarea>
			</td>
		</tr><tr valign="top">
			<th scope="row">
				<label for="percentage"><?php _e( 'Percentage', 'tcp' );?>:</label>
			</th><td>
				<input type="text" id="percentage" name="percentage" size="5" maxlength="8" value="<?php echo isset( $data['percentage'] ) ? $data['percentage'] : '';?>" />
				<br /><span class="description"><?php _e( 'Leave this field to blank (or zero) to use the fix value', 'tcp' );?></span>
			</td>
		</tr><tr valign="top">
			<th scope="row">
				<label for="fix"><?php _e( 'Fix', 'tcp' );?>:</label>
			</th><td>
				<input type="text" id="fix" name="fix" size="5" maxlength="8" value="<?php echo isset( $data['fix'] ) ? $data['fix'] : '';?>" />
			</td>
		</tr><?php
	}

	function saveEditFields( $data ) {
		$data['notice'] = isset( $_REQUEST['notice'] ) ? $_REQUEST['notice'] : '';
		$data['percentage'] = isset( $_REQUEST['percentage'] ) ? (float)$_REQUEST['percentage'] : '0';
		$data['fix'] = isset( $_REQUEST['fix'] ) ? (float)$_REQUEST['fix'] : '0';
		return $data;
	}

	function getCheckoutMethodLabel( $instance, $shippingCountry, $shoppingCart ) {
		$data = tcp_get_payment_plugin_data( get_class( $this ), $instance );
		$title = isset( $data['title'] ) ? $data['title'] : '';
		$cost = tcp_get_the_shipping_cost_to_show( $this->getCost( $instance, $shippingCountry, $shoppingCart ) );
		return sprintf( __( '%s. Cost: %s', 'tcp' ), $title, tcp_format_the_price( $cost ) );
	}

	function getCost( $instance, $shippingCountry, $shoppingCart ) {
		$data = tcp_get_payment_plugin_data( get_class( $this ), $instance );
		$percentage = isset( $data['percentage'] ) ? $data['percentage'] : 0;
		if ( $percentage > 0 )
			return $shoppingCart->getTotal() * $percentage / 100;
		else {
			$fix = isset( $data['fix'] ) ? $data['fix'] : 0;
			return $fix;
		}
	}

	function showPayForm( $instance, $shippingCountry, $shoppingCart, $order_id ) {
		global $thecartpress;
		$data = tcp_get_payment_plugin_data( get_class( $this ), $instance );
		$title = isset( $data['title'] ) ? $data['title'] : '';
		$cost = $this->getCost( $instance, $shippingCountry, $shoppingCart );
		$params = array(
			'tcp_checkout'	=> 'ok',
			'order_id'		=> $order_id,
		);?>
		<?php printf( __( '%s, Cost: %s', 'tcp' ), $title, tcp_format_the_price( $cost ) );?>
		<?php if ( strlen( trim( $data['notice'] ) ) > 0 ) : ?><p><?php echo $data['notice'];?></p><?php endif; ?>
		<p><input type="button" value="<?php _e( 'Finish', 'tcp' );?>" onclick="window.location.href = '<?php echo add_query_arg( $params, get_permalink() );?>';"/></p><?php
		require_once( TCP_DAOS_FOLDER . 'Orders.class.php' );
		Orders::editStatus( $order_id, $data['new_status'], 'no-id' );
		require_once( TCP_CHECKOUT_FOLDER . 'ActiveCheckout.class.php' );
		ActiveCheckout::sendMails( $order_id );
	}
}
?>