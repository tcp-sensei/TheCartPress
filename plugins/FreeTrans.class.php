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

class FreeTrans extends TCP_Plugin {

	function getTitle() {
		return 'Free Trans';
	}

	function getDescription() {
		return 'Free transport for orders with cost greater than an editable minimun.<br>Author: <a href="http://thecartpress.com" target="_blank">TheCartPress team</a>';
	}

	function showEditFields( $data ) {?>
		<tr valign="top">
		<th scope="row">
			<label for="minimun"><?php _e( 'Minimun amount', 'tcp' );?>:</label>
		</th><td>
			<input type="text" id="minimun" name="minimun" value="<?php echo isset( $data['minimun'] ) ? $data['minimun'] : 0;?>" size="13" maxlength="13"/>
		</td></tr>
		<?php
	}

	function saveEditFields( $data ) {
		$data['minimun'] = isset( $_REQUEST['minimun'] ) ? $_REQUEST['minimun'] : '0';
		return $data;
	}

	function isApplicable( $shippingCountry, $shoppingCart, $data ) {
		$minimun_amount = $data['minimun'];
		return $shoppingCart->getTotal() > $minimun_amount;
	}

	function getCheckoutMethodLabel( $instance, $shippingCountry, $shoppingCart ) {
		$data = tcp_get_shipping_plugin_data( get_class( $this ), $instance );
		$title = isset( $data['title'] ) ? $data['title'] : __( 'Free transport', 'tcp' );
		return $title;
	}
}
?>
