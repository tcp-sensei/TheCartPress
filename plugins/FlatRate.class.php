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

class FlatRateShipping extends TCP_Plugin {

	function getTitle() {
		return 'FlatRate';
	}

	function getDescription() {
		return 'Calculate the shipping cost by a flat or percentual formula.<br>Author: <a href="http://thecartpress.com" target="_blank">TheCartPress team</a>';
	}

	function getCheckoutMethodLabel( $instance, $shippingCountry, $shoppingCart ) {
		$data = tcp_get_shipping_plugin_data( get_class( $this ), $instance );
		$title = isset( $data['title'] ) ? $data['title'] : '';
		$cost = tcp_get_the_shipping_cost_to_show( $this->getCost( $instance, $shippingCountry, $shoppingCart ) );
		return sprintf( __( '%s. Cost: %s', 'tcp' ), $title, tcp_format_the_price( $cost ) );
	}

	function showEditFields( $data ) {
		$calculate_by = isset( $data['calculate_by'] ) ? $data['calculate_by'] : 'per'; ?>
		<tr valign="top">
		<th scope="row">
			<label for="calculate_by"><?php _e( 'Calculate by', 'tcp' );?>:</label>
		</th><td>
		<?php $script = 'if ( jQuery(this).val() == \'fix\' ) {
			jQuery(\'.tcp_fixed_cost\').show();
			jQuery(\'.tcp_type\').show();
			jQuery(\'.tcp_percentage\').hide();
		} else {
			jQuery(\'.tcp_fixed_cost\').hide();
			jQuery(\'.tcp_type\').hide();
			jQuery(\'.tcp_percentage\').show();
		}';
		$script = apply_filters( 'tcp_shipping_flat_rate_calculate_by_script', $script );
		$calculate_by_methods = array(
			'per'	=> __( 'Percentage', 'tcp' ),
			'fix'	=> __( 'Fix', 'tcp' )
		);
		$calculate_by_methods = apply_filters( 'tcp_shipping_flat_rate_calculate_by_methods', $calculate_by_methods ); ?>
			<select id="calculate_by" name="calculate_by" onchange="<?php echo $script; ?>">
			<?php foreach( $calculate_by_methods as $key => $value ) : ?>
				<option value="<?php echo $key; ?>" <?php selected( $key, $calculate_by );?>><?php echo $value; ?></option>
			<?php endforeach; ?>
			</select>
		</td></tr>

		<tr valign="top" class="tcp_fixed_cost" <?php if ( $calculate_by != 'fix' ) : ?>style="display: none;"<?php endif; ?> >
		<th scope="row">
			<label for="fixed_cost"><?php _e( 'Fixed cost', 'tcp' );?>:</label>
		</th><td>
			<input type="text" id="fixed_cost" name="fixed_cost" value="<?php echo isset( $data['fixed_cost'] ) ? $data['fixed_cost'] : 0;?>" size="8" maxlength="13"/><?php tcp_the_currency();?>
		</td></tr>

		<tr valign="top" class="tcp_percentage" <?php if ( $calculate_by != 'per' ) : ?>style="display: none;"<?php endif; ?>>
		<th scope="row">
			<label for="percentage"><?php _e( 'Percentage', 'tcp' );?>:</label>
		</th><td>
			<input type="text" id="percentage" name="percentage" value="<?php echo isset( $data['percentage'] ) ? $data['percentage'] : 0;?>" size="3" maxlength="5"/>%
		</td></tr>

		<tr valign="top" class="tcp_type" <?php if ( $calculate_by != 'fix' ) : ?>style="display: none;"<?php endif; ?>>
		<th scope="row">
			<label for="calculate_type"><?php _e( 'Type', 'tcp' );?>:</label>
		</th><td>
			<select id="calculate_type" name="calculate_type">
				<option value="by_order" <?php selected( 'by_order', isset( $data['calculate_type'] ) ? $data['calculate_type'] : '' );?>><?php _e( 'By order', 'tcp' );?></option>
				<option value="by_article" <?php selected( 'by_article', isset( $data['calculate_type'] ) ? $data['calculate_type'] : '' );?>><?php _e( 'By article', 'tcp' );?></option>
			</select>
		</td></tr>
		<?php do_action( 'tcp_shipping_flat_rate_edit_fields', $calculate_by );
	}

	function saveEditFields( $data ) {
		$data['calculate_by']	= isset( $_REQUEST['calculate_by'] ) ? $_REQUEST['calculate_by'] : '';
		$data['fixed_cost']		= isset( $_REQUEST['fixed_cost'] ) ? (float)$_REQUEST['fixed_cost'] : '0';
		$data['percentage']		= isset( $_REQUEST['percentage'] ) ? (float)$_REQUEST['percentage'] : '0';
		$data['calculate_type']	= isset( $_REQUEST['calculate_type'] ) ? $_REQUEST['calculate_type'] : '';
		do_action( 'tcp_shipping_flat_rate_save_edit_fields', $data );
		return $data;
	}

	function getCost( $instance, $shippingCountry, $shoppingCart ) {
		$data = tcp_get_shipping_plugin_data( get_class( $this ), $instance );
		if ( $data['calculate_by'] == 'fix' ) {
			if ( $data['calculate_type'] == 'by_order' ) {
				$total = $data['fixed_cost'];
			} else {//'by_article'
				$total = $data['fixed_cost'] * $shoppingCart->getCount();
			}
		} else if ( $data['calculate_by'] == 'per' ) {
			$total = $shoppingCart->getTotalForShipping() - $shoppingCart->getCartDiscountsTotal();
			$total = $total * $data['percentage'] / 100;
		} else {
			$total = 0;
		}
		return apply_filters( 'tcp_shipping_flat_rate_get_cost', $total, $data, $shippingCountry, $shoppingCart );
	}
}
?>
