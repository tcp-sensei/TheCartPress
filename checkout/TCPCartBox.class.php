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
require_once( dirname( __FILE__ ) . '/TCPCheckoutBox.class.php' );

class TCPCartBox extends TCPCheckoutBox {
	function get_title() {
		return __( 'Cart', 'tcp' );
	}

	function get_class() {
		return 'cart_layer';
	}
	
	function before_action() {
		return apply_filters( 'tcp_before_cart_box', 0 );
	}

	function after_action() {
		$comment = array(
			'comment' => isset( $_REQUEST['comment'] ) ? $_REQUEST['comment'] : 0,
		);
		$_SESSION['tcp_checkout']['cart'] = $comment;
		do_action( 'tcp_after_cart_box' );
		return apply_filters( 'tcp_after_cart_box', true );
	}

	function show() {
		$shipping_country = '';
		$selected_shipping_address = isset( $_SESSION['tcp_checkout']['shipping']['selected_shipping_address'] ) ? $_SESSION['tcp_checkout']['shipping']['selected_shipping_address'] : false;
		if ( $selected_shipping_address == 'new' ) {
			$shipping_country = $_SESSION['tcp_checkout']['shipping']['shipping_country_id'];
		} elseif ( $selected_shipping_address == 'BIL' ) {
			$selected_billing_address = isset( $_SESSION['tcp_checkout']['billing']['selected_billing_address'] ) ? $_SESSION['tcp_checkout']['billing']['selected_billing_address'] : false;
			if ( $selected_billing_address == 'new' ) {
				$shipping_country = $_SESSION['tcp_checkout']['billing']['billing_country_id'];
			} else { //if ( $selected_billing_address == 'Y' ) {
				$shipping_country = Addresses::getCountryId( $_SESSION['tcp_checkout']['billing']['selected_billing_id'] );
			}		
		} elseif ( $selected_shipping_address == 'Y' ) {
			$shipping_country = Addresses::getCountryId( $_SESSION['tcp_checkout']['shipping']['selected_shipping_id'] );
		}?>
		<div id="cart_layer_info" class="checkout_info clearfix">
			<?php $settings = get_option( 'tcp_' . get_class( $this ), array() ); ?>
		 	<?php do_action( 'tcp_checkout_cart_before', $settings );
			$this->show_order_cart( $shipping_country, $settings );
		 	do_action( 'tcp_checkout_cart_after' );
		 	if ( isset( $_REQUEST['comment'] ) ) {
				$comment = $_REQUEST['comment'];
			} elseif ( isset( $_SESSION['tcp_checkout']['cart']['comment'] ) ) {
				$comment = $_SESSION['tcp_checkout']['cart']['comment'];
			} else {
				$comment = '';
			}?>
		 	<div class="tcp_go_to_shopping_cart"><a href="<?php tcp_the_shopping_cart_url(); ?>"><?php _e( 'Shopping Cart', 'tcp' ); ?></a></div><!-- .tcp_go_to_shopping_cart -->
			<div class="tcp_comment"><label for="comment"><?php _e( 'Comments:', 'tcp' ); ?></label><br />
			<textarea id="comment" name="comment" cols="40" rows="3" maxlength="255"><?php echo $comment; ?></textarea></div><!-- .tcp_comment -->
		</div><!-- cart_layer_info --><?php
		return true;
	}

	function show_config_settings() {
		$settings	= get_option( 'tcp_' . get_class( $this ), array() );
		$see_weight	= isset( $settings['see_weight'] ) ? $settings['see_weight'] : true;
		?><tr valign="top">
			<th scope="row"><label for="see_weight"><?php _e( 'Display weight column', 'tcp' );?>:</label></th>
			<td><input type="checkbox" name="see_weight" id="see_weight" value="yes" <?php checked( $see_weight );?>/></td>
		</tr><?php
		do_action( 'tcp_checkout_show_config_settings', $settings );
		return true;
	}

	function save_config_settings() {
		$settings = array(
			'see_weight'	=> isset( $_REQUEST['see_weight'] ) ? $_REQUEST['see_weight'] == 'yes' : false,
		);
		$settings = apply_filters( 'tcp_cart_box_config_settings', $settings );
		update_option( 'tcp_' . get_class( $this ), $settings );
		return true;
	}

	private function show_order_cart( $shipping_country, $args = array() ) {
		do_action( 'tcp_checkout_create_order_cart', $args );
		$shoppingCart = TheCartPress::getShoppingCart(); ?>
		<table id="tcp_shopping_cart_table" class="tcp_shopping_cart_table">
		<thead>
		<tr class="tcp_cart_title_row">
		<th class="tcp_cart_name"><?php _e( 'Name', 'tcp' ); ?></th>
		<th class="tcp_cart_unit_price"><?php _e( 'Price', 'tcp' ); ?></th>
		<th class="tcp_cart_units"><?php _e( 'Units', 'tcp' ); ?></th>
		<?php if ( ! isset( $args['see_weight'] ) || ( isset( $args['see_weight'] ) && $args['see_weight'] ) ) : ?>
		<th class="tcp_cart_weight"><?php _e( 'Weight', 'tcp' ); ?></th>
		<?php endif; ?>
		<th class="tcp_cart_price"><?php _e( 'Total', 'tcp' ); ?></th>
		</tr>
		</thead>
		<tbody><?php
		$i = 0;
		//$decimals = tcp_get_decimal_currency();
		$table_amount_without_tax = 0;
		$table_amount_with_tax = 0;
		foreach( $shoppingCart->getItems() as $item ) :
			$tax = tcp_get_the_tax( $item->getPostId() );
			if ( ! tcp_is_display_prices_with_taxes() ) $discount = $item->getDiscount() / $item->getUnits();
			else $discount = 0;
			$unit_price_without_tax = tcp_get_the_price_without_tax( $item->getPostId(), $item->getUnitPrice() ) - $discount;
			$tax_amount = $unit_price_without_tax * $tax / 100;
			$tax_amount = $tax_amount * $item->getUnits();
			$line_price_without_tax = $unit_price_without_tax * $item->getUnits();
			$line_price_with_tax = $line_price_without_tax + $tax_amount; 
			$table_amount_without_tax += $line_price_without_tax;
			$table_amount_with_tax += $line_price_with_tax; ?>
			<tr class="tcp_cart_product_row<?php if ( $i++ & 1 == 1 ) :?> tcp_par<?php endif; ?>">
				<td class="tcp_cart_name"><?php echo tcp_get_the_title( $item->getPostId(), $item->getOption1Id(), $item->getOption2Id() ); ?></td>
				<td class="tcp_cart_unit_price">
				<?php if ( $discount > 0 ) printf( __('%s (Discount %s)', 'tcp' ), tcp_format_the_price( $unit_price_without_tax ), tcp_format_the_price( $discount ) );
				else echo tcp_format_the_price( $unit_price_without_tax ); ?>
				</td>
				<td class="tcp_cart_units"><?php echo tcp_number_format( $item->getCount(), 0 ); ?></td>
				<?php if ( ! isset( $args['see_weight'] ) || ( isset( $args['see_weight'] ) && $args['see_weight'] ) ) : ?>
				<td class="tcp_cart_weight"><?php echo tcp_number_format( $item->getWeight(), 0 ); ?>&nbsp;<?php echo tcp_get_the_unit_weight(); ?></td>
				<?php endif; ?>
				<td>
				<?php echo tcp_format_the_price( $line_price_without_tax ); ?></td>
			</tr>
		<?php endforeach;
		if ( ! isset( $args['see_weight'] ) || ( isset( $args['see_weight'] ) && $args['see_weight'] ) ) $colspan = 4;
		else $colspan = 3;
		if ( tcp_is_display_prices_with_taxes() ) $discount = $shoppingCart->getAllDiscounts();
		else $discount = $shoppingCart->getCartDiscountsTotal();
		if ( $discount > 0 ) : ?>
			<tr id="discount" class="tcp_cart_discount_row<?php if ( $i++ & 1 == 1 ) : ?> tcp_par<?php endif; ?>">
			<td colspan="<?php echo $colspan; ?>" style="text-align:right"><?php _e( 'Discounts', 'tcp' ); ?></td>
			<td>-<?php echo tcp_format_the_price( $discount ); ?></td>
			</tr><?php
		endif;
		if ( isset( $_SESSION['tcp_checkout']['shipping_methods']['shipping_method_id'] ) ) { //sending
			if ( ! $shoppingCart->isFreeShipping() ) {
				$smi = $_SESSION['tcp_checkout']['shipping_methods']['shipping_method_id'];
				$smi = explode( '#', $smi );
				$class = $smi[0];
				$instance = $smi[1];
				$shipping_method = new $class();
				$shipping_cost = $shipping_method->getCost( $instance, $shipping_country, $shoppingCart );
				$shoppingCart->addOtherCost( ShoppingCart::$OTHER_COST_SHIPPING_ID, $shipping_cost, __( 'Shipping cost', 'tcp' ) );
			}
		} else {
			$shoppingCart->deleteOtherCost( ShoppingCart::$OTHER_COST_SHIPPING_ID );
		}
		if ( isset( $_SESSION['tcp_checkout']['payment_methods']['payment_method_id'] ) ) {
			$pmi = $_SESSION['tcp_checkout']['payment_methods']['payment_method_id'];
			$pmi = explode( '#', $pmi );
			$class = $pmi[0];
			$instance = $pmi[1];
			$payment_method = new $class();
			$payment_cost = $payment_method->getCost( $instance, $shipping_country, $shoppingCart );
			$shoppingCart->addOtherCost( ShoppingCart::$OTHER_COST_PAYMENT_ID, $payment_cost, __( 'Payment cost', 'tcp' ) );
		} else {
			$shoppingCart->deleteOtherCost( ShoppingCart::$OTHER_COST_PAYMENT_ID );
		}
		do_action( 'tcp_checkout_calculate_other_costs' );
		if ( $shoppingCart->isFreeShipping() ) : ?>
			<tr class="tcp_cart_free_shipping<?php if ( $i++ & 1 == 1 ) :?> tcp_par<?php endif; ?>">
			<td colspan="<?php echo $colspan; ?>" class="tcp_cost_tcp_free_shipping" style="text-align:right"><?php _e( 'Free shipping', 'tcp' ); ?></td>
			<td>&nbsp;</td>
			</tr>
		<?php endif;
		$costs = $shoppingCart->getOtherCosts();
		asort( $costs, SORT_STRING );
		foreach( $costs as $cost_id => $cost ) :
			$cost_without_tax = tcp_get_the_shipping_cost_without_tax( $cost->getCost() );
			$tax = tcp_get_the_shipping_tax();
			$tax_amount = $cost_without_tax * $tax / 100;
			//$tax_amount = round( $tax_amount, $decimals );
			$cost_with_tax = $cost_without_tax + $tax_amount;
			$table_amount_with_tax += $cost_with_tax;
			$table_amount_without_tax += $cost_without_tax;
			?>
			<tr class="tcp_cart_other_costs_row<?php if ( $i++ & 1 == 1 ) :?> tcp_par<?php endif; ?>">
			<td colspan="<?php echo $colspan; ?>" class="tcp_cost_' . $cost_id . '" style="text-align:right"><?php echo $cost->getDesc(); ?></td>
			<td><?php echo tcp_format_the_price( $cost_without_tax ); ?></td>
			</tr>
		<?php endforeach;
		$show_tax_summary = false;
		if ( $table_amount_without_tax == $table_amount_with_tax ) {
			$show_tax_summary = tcp_get_display_zero_tax_subtotal();
		} elseif ( tcp_is_display_full_tax_summary() ) {
			$show_tax_summary = true;
		}
		if ( $show_tax_summary ) : ?>
			<tr id="subtotal" class="tcp_cart_subtotal_row <?php if ( $i++ & 1 == 1 ) :?> tcp_par<?php endif; ?>">
				<td colspan="<?php echo $colspan; ?>" class="tcp_cart_subtotal_title"><?php _e( 'Taxes', 'tcp'); ?></td>
				<td class="tcp_cart_subtotal"><span id="subtotal"><?php echo tcp_format_the_price( $table_amount_with_tax - $table_amount_without_tax ); ?></span></td>
			</tr>
		<?php endif;
		if ( tcp_is_display_prices_with_taxes() ) $table_amount_with_tax -= $discount;
		$total = apply_filters( 'tcp_checkout_set_total', $table_amount_with_tax );
		do_action( 'tcp_checkout_before_total', $args ); ?>
		<tr id="total" class="tcp_cart_total_row<?php if ( $i++ & 1 == 1 ) :?> tcp_par<?php endif; ?>">
		<td colspan="<?php echo $colspan; ?>" class="tcp_cart_total_title"><?php _e( 'Total', 'tcp'); ?></td>
		<td class="tcp_cart_total"><span id="total"><?php echo tcp_format_the_price( $total ); ?></span></td>
		</tr>
		</tbody></table><?php
		do_action( 'tcp_checkout_after_order_cart', $args );
		tcp_do_template( 'tcp_checkout_order_cart' );
	}
}
?>