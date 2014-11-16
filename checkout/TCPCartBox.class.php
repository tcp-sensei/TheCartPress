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

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'TCPCartBox' ) ) :

require_once( TCP_CHECKOUT_FOLDER . 'TCPCheckoutBox.class.php' );

class TCPCartBox extends TCPCheckoutBox {
	function get_title() {
		return __( 'Cart', 'tcp' );
	}

	function get_class() {
		return 'cart_layer';
	}

	function get_name() {
		return 'cart';
	}

	function before_action() {
		return apply_filters( 'tcp_before_cart_box', 0 );
	}

	function after_action() {
		$_SESSION['tcp_checkout']['cart']['comment'] = isset( $_REQUEST['comment'] ) ? $_REQUEST['comment'] : '';
		$settings	= get_option( 'tcp_' . get_class( $this ), array() );
		$see_notice	= isset( $settings['see_notice'] ) ? $settings['see_notice'] : false;
		if ( $see_notice ) {
			if ( ! isset( $_REQUEST['legal_notice_accept'] ) || strlen( $_REQUEST['legal_notice_accept'] ) == 0 ) {
				$this->errors['legal_notice_accept'] = __( 'You must accept the conditions!!', 'tcp' );
				return apply_filters( 'tcp_after_notice_box', false );
			}
		}
		do_action( 'tcp_after_cart_box_action' );
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
		}
		$settings = get_option( 'tcp_' . get_class( $this ), array() ); ?>
		<div id="cart_layer_info" class="checkout_info clearfix">
			<?php do_action( 'tcp_checkout_cart_before', $settings );
			$this->show_order_cart( $shipping_country, $settings );
			do_action( 'tcp_checkout_cart_after', $settings ); ?>
			<div class="tcp_go_to_shopping_cart">
				<a href="<?php tcp_the_shopping_cart_url(); ?>"><?php _e( 'Shopping Cart', 'tcp' ); ?></a>
			</div><!-- .tcp_go_to_shopping_cart -->
			<?php $see_comment = isset( $settings['see_comment'] ) ? $settings['see_comment'] : true;
			if ( $see_comment ) :
				if ( isset( $_REQUEST['comment'] ) ) {
					$comment = $_REQUEST['comment'];
				} elseif ( isset( $_SESSION['tcp_checkout']['cart']['comment'] ) ) {
					$comment = $_SESSION['tcp_checkout']['cart']['comment'];
				} else {
					$comment = '';
				} ?>
			<div class="tcp_comment">
				<label for="comment"><?php echo apply_filters( 'tcp_checkout_cart_comment_label', __( 'Comments:', 'tcp' ) ); ?></label>
				<p>
					<textarea id="comment" name="comment" maxlength="255" class="form-control" rows="3"><?php echo $comment; ?></textarea>
				</p>
			</div><!-- .tcp_comment -->
			<?php endif;
		$see_notice = isset( $settings['see_notice'] ) ? $settings['see_notice'] : false;
		if ( $see_notice ) {
			$this->show_notice_area();
		} ?>
		</div><!-- cart_layer_info -->
		<?php return true;
	}

	function show_config_settings() {
		$settings		= get_option( 'tcp_' . get_class( $this ), array() );
		$see_price		= isset( $settings['see_price'] ) ? $settings['see_price'] : true;
		$see_sku		= isset( $settings['see_sku'] ) ? $settings['see_sku'] : true;
		$see_units		= isset( $settings['see_units'] ) ? $settings['see_units'] : true;
		$see_weight		= isset( $settings['see_weight'] ) ? $settings['see_weight'] : true;
		$see_tax		= isset( $settings['see_tax'] ) ? $settings['see_tax'] : true;
		$see_tax_detail	= isset( $settings['see_tax_detail'] ) ? $settings['see_tax_detail'] : true;
		$see_comment	= isset( $settings['see_comment'] ) ? $settings['see_comment'] : true;
		$see_total		= isset( $settings['see_total'] ) ? $settings['see_total'] : true;
		$see_notice		= isset( $settings['see_notice'] ) ? $settings['see_notice'] : true; ?>
<table class="form-table">
<tbody>
<tr valign="top">
	<th scope="row"><label for="see_weight"><?php _e( 'Display Weight column', 'tcp' );?>:</label></th>
	<td><input type="checkbox" name="see_weight" id="see_weight" value="yes" <?php checked( $see_weight );?>/></td>
</tr>
<tr valign="top">
	<th scope="row"><label for="see_price"><?php _e( 'Display Price column', 'tcp' );?>:</label></th>
	<td><input type="checkbox" name="see_price" id="see_price" value="yes" <?php checked( $see_price );?>/></td>
</tr>
<tr valign="top">
	<th scope="row"><label for="see_tax"><?php _e( 'Display Tax column', 'tcp' );?>:</label></th>
	<td><input type="checkbox" name="see_tax" id="see_tax" value="yes" <?php checked( $see_tax );?>/></td>
</tr>
<tr valign="top">
	<th scope="row"><label for="see_tax_detail"><?php _e( 'Display Tax detail', 'tcp' );?>:</label></th>
	<td><input type="checkbox" name="see_tax_detail" id="see_tax_detail" value="yes" <?php checked( $see_tax_detail );?>/></td>
</tr>
<tr valign="top">
	<th scope="row"><label for="see_sku"><?php _e( 'Display SKU column', 'tcp' );?>:</label></th>
	<td><input type="checkbox" name="see_sku" id="see_sku" value="yes" <?php checked( $see_sku );?>/></td>
</tr>
<tr valign="top">
	<th scope="row"><label for="see_sku"><?php _e( 'Display Units column', 'tcp' );?>:</label></th>
	<td><input type="checkbox" name="see_units" id="see_units" value="yes" <?php checked( $see_units );?>/></td>
</tr>
<tr valign="top">
	<th scope="row"><label for="see_comment"><?php _e( 'Display Comment', 'tcp' );?>:</label></th>
	<td><input type="checkbox" name="see_comment" id="see_comment" value="yes" <?php checked( $see_comment );?>/></td>
</tr>
<tr valign="top">
	<th scope="row"><label for="see_total"><?php _e( 'Display Total column', 'tcp' );?>:</label></th>
	<td><input type="checkbox" name="see_total" id="see_total" value="yes" <?php checked( $see_total );?>/></td>
</tr>
<tr valign="top">
	<th scope="row"><label for="see_notice"><?php _e( 'Display Notice', 'tcp' );?>:</label></th>
	<td><input type="checkbox" name="see_notice" id="see_notice" value="yes" <?php checked( $see_notice );?>/></td>
</tr>
<?php do_action( 'tcp_checkout_show_config_settings', $settings ); ?>
</tbody>
</table>
		<?php return true;
	}

	function save_config_settings() {
		$settings = array(
			'see_price'		 => isset( $_REQUEST['see_price'] ) ? $_REQUEST['see_price'] == 'yes' : false,
			'see_units'		 => isset( $_REQUEST['see_units'] ) ? $_REQUEST['see_units'] == 'yes' : false,
			'see_weight'	 => isset( $_REQUEST['see_weight'] ) ? $_REQUEST['see_weight'] == 'yes' : false,
			'see_tax'		 => isset( $_REQUEST['see_tax'] ) ? $_REQUEST['see_tax'] == 'yes' : false,
			'see_tax_detail' => isset( $_REQUEST['see_tax_detail'] ) ? $_REQUEST['see_tax_detail'] == 'yes' : false,
			'see_sku'		 => isset( $_REQUEST['see_sku'] ) ? $_REQUEST['see_sku'] == 'yes' : false,
			'see_comment'	 => isset( $_REQUEST['see_comment'] ) ? $_REQUEST['see_comment'] == 'yes' : false,
			'see_total'		 => isset( $_REQUEST['see_total'] ) ? $_REQUEST['see_total'] == 'yes' : false,
			'see_notice'	 => isset( $_REQUEST['see_notice'] ) ? $_REQUEST['see_notice'] == 'yes' : false,
		);
		$settings = apply_filters( 'tcp_cart_box_config_settings', $settings );
		update_option( 'tcp_' . get_class( $this ), $settings );
		return true;
	}

	protected function show_order_cart( $shipping_country, $args = array() ) {
		do_action( 'tcp_checkout_create_order_cart', $args );
		$see_price	= isset( $args['see_price'] ) ? $args['see_price'] : true;
		$see_units	= isset( $args['see_units'] ) ? $args['see_units'] : true;
		$see_sku	= isset( $args['see_sku'] ) ? $args['see_sku'] : true;
		global $thecartpress;
		if ( $thecartpress ) $see_weight = $thecartpress->get_setting( 'use_weight', true );
		if ( $see_weight ) $see_weight	= isset( $args['see_weight'] ) ? $args['see_weight'] : true;
		$see_tax		= isset( $args['see_tax'] ) ? $args['see_tax'] : true;
		$see_tax_detail	= isset( $args['see_tax_detail'] ) ? $args['see_tax_detail'] : true;
		$see_total		= isset( $args['see_total'] ) ? $args['see_total'] : true;
		$shoppingCart	= apply_filters( 'tcp_checkout_show_order_cart_get_shopping_cart', TheCartPress::getShoppingCart() ); ?>
<table id="tcp_shopping_cart_table" class="tcp_shopping_cart_table">
<thead>
	<tr class="tcp_cart_title_row">
		<th class="tcp_cart_name"><?php _e( 'Name', 'tcp' ); ?></th>
	<?php if ( $see_price ) : ?>
		<th class="tcp_cart_price"><?php _e( 'Price', 'tcp' ); ?></th>
	<?php endif; ?>
	<?php if ( $see_sku ) : ?>
		<th class="tcp_cart_sku"><?php _e( 'SKU', 'tcp' ); ?></th>
	<?php endif; ?>
	<?php if ( $see_tax ) : ?>
		<th class="tcp_cart_tax"><?php _e( 'Tax', 'tcp' ); ?></th>
	<?php endif; ?>
	<?php if ( $see_units ) : ?>
		<th class="tcp_cart_units"><?php _e( 'Units', 'tcp' ); ?></th>
	<?php endif; ?>
	<?php if ( $see_weight ) : ?>
		<th class="tcp_cart_weight"><?php _e( 'Weight', 'tcp' ); ?></th>
	<?php endif; ?>
	<?php if ( $see_total ) : ?>
		<th class="tcp_cart_price" style="text-align:right"><?php _e( 'Total', 'tcp' ); ?></th>
	<?php endif; ?>
	</tr>
</thead>
<tbody>
<?php $i = 0;
$decimals = tcp_get_decimal_currency();
$table_amount_without_tax = 0;
$table_amount_with_tax = 0;
foreach( $shoppingCart->getItems() as $item ) :
	$tax = $item->getTax();	//$tax = tcp_get_the_tax( $item->getPostId() );
	if ( ! tcp_is_display_prices_with_taxes() ) {
		$discount = round( $item->getDiscount() / $item->getUnits(), $decimals );
	} else {
		$discount = 0;
	}
	$unit_price_without_tax = tcp_get_the_price_without_tax( $item->getPostId(), $item->getUnitPrice() );
	$unit_price_without_tax = round( $unit_price_without_tax - $discount, $decimals );

	$tax_amount_per_unit = $unit_price_without_tax * $tax / 100;
	$tax_amount_per_unit = round( $tax_amount_per_unit, $decimals );
	$tax_amount = round( $tax_amount_per_unit * $item->getUnits(), $decimals );
	$line_price_without_tax = round( $unit_price_without_tax * $item->getUnits(), $decimals );
	$line_price_without_tax = apply_filters( 'tcp_checkout_cart_row_price', $line_price_without_tax, $item );

	$line_price_with_tax = $line_price_without_tax + $tax_amount;

	$table_amount_without_tax += $line_price_without_tax;
	$table_amount_with_tax += $line_price_with_tax; ?>
	<tr class="tcp_cart_product_row<?php if ( $i++ & 1 == 1 ) :?> tcp_par<?php endif; ?>">
		<?php $title = tcp_get_the_title( tcp_get_current_id( $item->getPostId() ), tcp_get_current_id( $item->getOption1Id() ), tcp_get_current_id( $item->getOption2Id() ) );
		$title = apply_filters( 'tcp_cart_box_title_item', $title, $item ); ?>
		<td class="tcp_cart_name">
			<?php echo $title; ?>
		</td>
		<?php if ( $see_price ) : ?>				
		<td class="tcp_cart_unit_price">
			<?php if ( $discount > 0 ) : ?>
				<?php printf( __('%s (Discount %s)', 'tcp' ), tcp_format_the_price( $unit_price_without_tax ), tcp_format_the_price( $discount ) ); ?>
			<?php else : ?>
				<?php echo tcp_format_the_price( $unit_price_without_tax ); ?>
			<?php endif; ?>
		</td>
		<?php endif; ?>
		<?php if ( $see_sku ) : ?>
		<td class="tcp_cart_sku">
			<?php echo tcp_get_the_sku( $item->getPostId(), $item->getOption1Id(), $item->getOption2Id() ); //tcp_get_the_sku( $item->getPostId() ); ?>
		</td>
		<?php endif; ?>
		<?php if ( $see_tax ) : ?>
		<td class="tcp_cart_tax">
			<?php echo tcp_format_the_price( $tax_amount_per_unit ); ?>
			<?php if ( $see_tax_detail ) : ?>&nbsp;(<?php echo tcp_number_format( $tax, $decimals ); ?>%)<?php endif; ?>
		</td>
		<?php endif; ?>
		<?php if ( $see_units ) : ?>
		<td class="tcp_cart_units">
			<?php echo tcp_number_format( $item->getCount(), 0 ); ?>
		</td>
		<?php endif; ?>
		<?php if ( $see_weight ) : ?>
		<td class="tcp_cart_weight">
			<?php echo tcp_number_format( $item->getWeight(), $decimals ); ?>&nbsp;<?php echo tcp_get_the_unit_weight(); ?>
		</td>
		<?php endif; ?>
		<?php if ( $see_total ) : ?>
		<td class="tcp_cart_row_total" style="text-align:right">
			<?php if ( $see_tax ) : ?>
				<?php echo tcp_format_the_price( $line_price_with_tax ); ?>
			<?php else : ?>
				<?php echo tcp_format_the_price( $line_price_without_tax ); ?>
			<?php endif; ?>
		</td>
		<?php endif; ?>
	</tr>
<?php endforeach;
$colspan_1 = 0;
if ( $see_weight ) $colspan_1++;
if ( $see_units ) $colspan_1++;
if ( $see_sku ) $colspan_1++;
$colspan_2 = 0;
if ( $see_price ) $colspan_2++;
if ( $see_tax ) $colspan_2++;
if ( tcp_is_display_prices_with_taxes() ) {
	$discount = $shoppingCart->getAllDiscounts();
} else {
	$discount = $shoppingCart->getCartDiscountsTotal();
}
if ( $see_total && $discount > 0 ) : ?>
	<tr id="discount" class="tcp_cart_discount_row<?php if ( $i++ & 1 == 1 ) : ?> tcp_par<?php endif; ?>">
		<?php if ( $colspan_1 > 0 ) { ?><td colspan="<?php echo $colspan_1; ?>">&nbsp;</td><?php } ?>
		<td class="tcp_cart_discount"><?php _e( 'Discounts', 'tcp' ); ?></td>
		<?php if ( $colspan_2 > 0 ) { ?><td colspan="<?php echo $colspan_2; ?>">&nbsp;</td><?php } ?>
		<?php if ( $see_total ) : ?>
		<td class="tcp_cart_row_total">-<?php echo tcp_format_the_price( $discount ); ?></td>
		<?php endif; ?>
	</tr>
<?php endif;
$shoppingCart->deleteOtherCost( ShoppingCart::$OTHER_COST_SHIPPING_ID, true );
if ( isset( $_SESSION['tcp_checkout']['shipping_methods']['shipping_method_id'] ) ) { //sending
	if ( !$shoppingCart->isFreeShipping() ) {
		$smi = $_SESSION['tcp_checkout']['shipping_methods']['shipping_method_id'];
		$smi = explode( '#', $smi );
		$class = $smi[0];
		$instance = $smi[1];
		$shipping_method = new $class();
		$shipping_cost = $shipping_method->getCost( $instance, $shipping_country, $shoppingCart );
		$shoppingCart->addOtherCost( ShoppingCart::$OTHER_COST_SHIPPING_ID, $shipping_cost, __( 'Shipping cost', 'tcp' ) );
	}
} else {
	do_action( 'tcp_checkout_cart_box_shipping_cost', $shipping_country, $shoppingCart );
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
	$shoppingCart->deleteOtherCost( ShoppingCart::$OTHER_COST_PAYMENT_ID, true );
}
do_action( 'tcp_checkout_calculate_other_costs', $shoppingCart );
if ( $shoppingCart->isFreeShipping() ) : ?>
	<tr class="tcp_cart_free_shipping<?php if ( $i++ & 1 == 1 ) :?> tcp_par<?php endif; ?>">
		<td class="tcp_cost_tcp_free_shipping"><?php _e( 'Free shipping', 'tcp' ); ?></td>
		<td colspan="<?php echo $colspan_1 + $colspan_2 + 2; ?>">&nbsp;</td>
	</tr>
<?php endif;
$costs = $shoppingCart->getOtherCosts();
asort( $costs, SORT_STRING );
foreach( $costs as $cost_id => $cost ) :
	$cost_without_tax = tcp_get_the_shipping_cost_without_tax( $cost->getCost() );
	$tax = tcp_get_the_shipping_tax();
	$tax_amount = $cost_without_tax * $tax / 100;

	$cost_with_tax = $cost_without_tax + $tax_amount;
	$cost_with_tax = round( $cost_with_tax, $decimals );
	$table_amount_with_tax += $cost_with_tax;

	$cost_without_tax = round( $cost_without_tax, $decimals );
	$table_amount_without_tax += $cost_without_tax;
	if ( $see_total ) : ?>
	<tr class="tcp_cart_other_costs_row<?php if ( $i++ & 1 == 1 ) :?> tcp_par<?php endif; ?>">
	<td <?php if ( $colspan_1 > 0 ) { ?> colspan="<?php echo $colspan_1 + 1; ?>"<?php } ?>
	class="tcp_cost_' . $cost_id . '"><?php echo $cost->getDesc(); ?></td>
	<?php if ( $see_price ) { ?>
		<td class="tcp_cart_unit_price"><?php echo tcp_format_the_price( $cost_without_tax ); ?></td>
	<?php } ?>
	<?php if ( $see_tax ) { ?>
		<td class="tcp_cart_tax"><?php echo tcp_format_the_price( $tax_amount ); ?>
	<?php if ( $see_tax_detail ) { ?>&nbsp;(<?php echo tcp_number_format( $tax, 0 ); ?>%)<?php } ?>
		</td>
	<?php } ?>
	<?php if ( $see_total ) { ?>
		<td class="tcp_cart_row_total" style="text-align:right"><?php echo tcp_format_the_price( $cost_with_tax ); ?></td>
	<?php } ?>
	</tr>
	<?php endif; ?>
<?php endforeach; ?>
<?php $table_amount_with_tax -= $discount;
$total = apply_filters( 'tcp_checkout_set_total', $table_amount_with_tax );
do_action( 'tcp_checkout_before_total', $args ); ?>
<?php if ( $see_total ) : ?>
	<tr id="total" class="tcp_cart_total_row<?php if ( $i++ & 1 == 1 ) :?> tcp_par<?php endif; ?>">
		<?php if ( $colspan_1 > 0 ) : ?><td colspan="<?php echo $colspan_1; ?>">&nbsp;</td><?php endif; ?>
		<td class="tcp_cart_total_title"><?php _e( 'Total', 'tcp'); ?></td>
		<?php if ( $colspan_2 > 0 ) : ?><td colspan="<?php echo $colspan_2; ?>">&nbsp;</td><?php endif; ?>
		<td class="tcp_cart_total" ><span id="total"><?php echo tcp_format_the_price( $total ); ?></span></td>
	</tr>
<?php endif; ?>
</tbody>
</table>
<?php do_action( 'tcp_checkout_after_order_cart', $args );
tcp_do_template( 'tcp_checkout_order_cart' );
	}

	/**
	 * Notice Area
	 */
	function show_notice_area() {
		$legal_notice_accept = isset( $_REQUEST['legal_notice_accept'] ) ? $_REQUEST['legal_notice_accept'] : ''; ?>
<?php do_action( 'tcp_checkout_before_notice_cart' ); ?>
<div id="legal_notice_layer_info">
	<?php global $thecartpress;
	$legal_notice = tcp_do_template( 'tcp_checkout_notice', false );
	if ( strlen( $legal_notice ) == 0 ) $legal_notice = $thecartpress->get_setting( 'legal_notice', '' );
	if ( strlen( $legal_notice ) > 0 ) : ?>
		<p id="legal_notice"><?php echo tcp_string( 'TheCartPress', 'legal notice', $legal_notice ); ?></p>
		<label>
		<input type="checkbox" id="legal_notice_accept" name="legal_notice_accept" value="Y" />
		<?php _e( 'Accept conditions', 'tcp' );?>
		</label>
		<?php if ( isset( $this->errors['legal_notice_accept'] ) ) : ?>
			<p class="bg-danger"><?php echo $this->errors['legal_notice_accept'];?></p>
		<?php endif;?>
	<?php else : ?>
		<input type="hidden" name="legal_notice_accept" value="Y" />
		<p><?php _e( 'When you click on the \'continue\' button the order will be created and if you have chosen an external payment method the system will show a button to go to the external web (usually your bank\'s payment gateway)','tcp' );?></p>
	<?php endif;?>
</div> <!-- legal_notice_layer_info-->
<?php do_action( 'tcp_checkout_after_notice_cart' );
	}
}
endif; // class_exists check