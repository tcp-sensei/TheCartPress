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

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'FreeTrans' ) ) :

class FreeTrans extends TCP_Plugin {

	function getTitle() {
		return __( 'Free Trans', 'tcp' );
	}

	function getDescription() {
		return sprintf( __( 'Free transport for orders with cost greater than an editable minimum. <br>Author: <a href="%s" target="_blank">TheCartPress team</a>', 'tcp' ), 'http://thecartpress.com' );
	}

	function getIcon() {
		return plugins_url( 'images/freedelivery.png', __FILE__ );
	}

	function showEditFields( $data, $instance = 0 ) {?>
		<tr valign="top">
			<th scope="row">
				<label for="minimum"><?php _e( 'Minimum amount', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input type="text" id="minimum" name="minimun" value="<?php echo isset( $data['minimun'] ) ? $data['minimun'] : 0; ?>" size="13" maxlength="13"/>
			</td>
		</tr>
		<?php
	}

	function saveEditFields( $data, $instance = 0 ) {
		$data['minimun'] = isset( $_REQUEST['minimun'] ) ? $_REQUEST['minimun'] : '0';
		return $data;
	}

	function isApplicable( $shippingCountry, $shoppingCart, $data ) {
		$minimum_amount = isset( $data['minimun'] ) ? $data['minimun'] : 0;
		return $shoppingCart->getTotal() >= $minimum_amount;
	}

	function getCheckoutMethodLabel( $instance, $shippingCountry = '', $shoppingCart = false ) {
		$data = tcp_get_shipping_plugin_data( get_class( $this ), $instance );
		if ( isset( $data['title'] ) ) {
			//return tcp_string( 'TheCartPress', 'shi_FreeTrans-title', $title );
			return tcp_string( 'TheCartPress', apply_filters( 'tcp_plugin_data_get_option_translatable_key', 'shi_FreeTrans-title-' . $instance ), $data['title'] );
		} else {
			return __( 'Free transport', 'tcp' );
		}
	}
}
endif; // class_exists check