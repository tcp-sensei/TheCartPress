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
 * along with This program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Shows a Cart table.
 */
class TCPCartTable {

	static function show( $source, $echo = true ) {
		ob_start();
		if ( $source->see_address() ) : ?>
			<div id="tcp_order_id">
			<span class="tcp_order_id_row"><?php _e( 'Order ID', 'tcp' ); ?>: <span class="tcp_order_id_value tcp_order_id"><?php echo $source->get_order_id(); ?></span></span>
			<br/>
			<span class="tcp_order_id_row"><?php _e( 'Create at', 'tcp' ); ?>: <span class="tcp_order_id_value tcp_created_at"><?php echo $source->get_created_at(); ?></span></span>
			</div>
			<?php if ( $source->get_shipping_firstname() == "" ) {
				$style = 'style="display:none"';
			} else {
				$style = '';//'style="padding-bottom:1em;"';
			} ?>
			<div id="shipping_info" <?php echo $style; ?>>
			<h3><?php _e( 'Shipping address', 'tcp' ); ?></h3>
			<?php echo $source->get_shipping_firstname(); ?> <?php echo $source->get_shipping_lastname(); ?><br />
			<?php if ( strlen( $source->get_shipping_company() ) > 0 ) : echo $source->get_shipping_company(); ?><br /><?php endif; ?>
			<?php echo $source->get_shipping_street(); ?><br/>
			<?php echo $source->get_shipping_postcode() . ', ' . $source->get_shipping_city(); ?><br/>
			<?php echo $source->get_shipping_region() . ', ' . $source->get_shipping_country(); ?><br/>
			<?php $telephone = $source->get_shipping_telephone_1();
			if ( strlen( $source->get_shipping_telephone_2() ) > 0 ) $telephone .= ' - ' . $source->get_shipping_telephone_2();
			if ( strlen( $telephone ) > 0 ) : _e( 'Telephones', 'tcp' ); ?>: <?php echo $telephone; ?><br/><?php endif; ?>
			<?php if ( strlen( $source->get_shipping_fax() ) > 0 ) : _e( 'Fax', 'tcp' ); ?>: <?php echo $source->get_shipping_fax(); ?><br/><?php endif; ?>
			<?php if ( strlen( $source->get_shipping_email() ) > 0 ) : echo $source->get_shipping_email(); ?><br/><?php endif; ?>
			</div><!-- #shipping_info-->

			<div id="billing_info">
			<h3><?php _e( 'Billing address', 'tcp' ); ?></h3>
			<?php echo $source->get_billing_firstname();?> <?php echo $source->get_billing_lastname(); ?><br/>
			<?php if ( strlen( $source->get_billing_company() ) > 0 ) : echo $source->get_billing_company(); ?><br/><?php endif; ?>
			<?php echo $source->get_billing_street(); ?><br/>
			<?php echo $source->get_billing_postcode(); ?>, <?php echo $source->get_billing_city(); ?><br/>
			<?php echo $source->get_billing_region(); ?>, <?php echo $source->get_billing_country(); ?><br/>
			<?php $telephone = $source->get_billing_telephone_1();
			if ( strlen( $source->get_billing_telephone_2() ) > 0 ) $telephone .= ' - ' . $source->get_billing_telephone_2();
			if ( strlen( $telephone ) > 0 ) : _e( 'Telephones', 'tcp' ); ?>: <?php echo $telephone; ?><br/><?php endif; ?>
			<?php if ( strlen( $source->get_billing_fax() ) > 0 ) : _e( 'Fax', 'tcp' ); ?>: <?php echo $source->get_billing_fax(); ?><br/><?php endif; ?>
			<?php if ( strlen( $source->get_billing_email() ) > 0 ) : echo $source->get_billing_email(); ?><br/><?php endif; ?>
			</div><!-- #billing_info -->
			<div id="tcp_status">
			<span class="tcp_status_row"><?php _e( 'Payment method', 'tcp' ); ?>: <span class="tcp_status_value tcp_payment_method" ><?php echo $source->get_payment_method(); ?></span></span><br/>
			<span class="tcp_status_row"><?php _e( 'Shipping method', 'tcp' ); ?>: <span class="tcp_status_value tcp_shipping_method"><?php echo $source->get_shipping_method(); ?></span></span><br/>
			<span class="tcp_status_row"><?php _e( 'Status', 'tcp' ); ?>: <span class="tcp_status_value tcp_status tcp_status_<?php echo $source->get_status(); ?>"><?php echo $source->get_status(); ?></span></span>
			</div>
		<?php endif; ?>
		<table id="tcp_shopping_cart_table" class="tcp_shopping_cart_table">
		<thead>
		<tr class="tcp_cart_title_row">
		<?php if ( $source->see_full() ) : ?><th class="tcp_cart_id"><?php _e( 'Id.', 'tcp' ); ?></th><?php endif; ?>
		<?php if ( $source->see_thumbnail() ) : ?><th class="tcp_cart_thumbnail">&nbsp;</th><?php endif; ?>
		<th class="tcp_cart_name"><?php _e( 'Name', 'tcp' ); ?></th>
		<th class="tcp_cart_price"><?php _e( 'Price', 'tcp' ); ?></th>
		<th class="tcp_cart_units"><?php _e( 'Units', 'tcp' ); ?></th>
		<?php if ( $source->see_full() ) : ?><th class="tcp_cart_sku"><?php _e( 'Sku', 'tcp' ); ?></th><?php endif; ?>
		<?php if ( $source->see_full() ) : ?><th class="tcp_cart_weight"><?php _e( 'Weight', 'tcp' ); ?></th><?php endif; ?>
		<th class="tcp_cart_total"><?php _e( 'Total', 'tcp' ); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php if ( $source->has_order_details() ) {
			global $thecartpress;
			$stock_management	= $thecartpress->get_setting('stock_management',  false );
			$i = 0;
			$total_tax = 0;
			$total = 0;
			foreach( $source->get_orders_details() as $order_detail ) : ?>
				<tr class="tcp_cart_product_row <?php if ( $i++ & 1 == 1 ) : ?> par<?php endif; ?>">
				<?php if ( $source->see_full() ) : ?><td class="tcp_cart_id"><?php echo $order_detail->get_post_id(); ?></td><?php endif; ?>
				<?php if ( $source->see_thumbnail() ) : ?>
					<td class="tcp_cart_thumbnail">
					<?php $size = apply_filters( 'tcp_get_shopping_cart_image_size', array( 32, 32 ) );
					echo tcp_get_the_thumbnail( $order_detail->get_post_id(), $order_detail->get_option_1_id(), $order_detail->get_option_2_id(), $size ); ?>
					</td>
				<?php endif; ?>
				<td class="tcp_cart_name">
				<?php $name = $order_detail->get_name();
				if ( $source->see_product_link() ) {
					$name = '<a href="' . tcp_get_permalink( tcp_get_current_id( $order_detail->get_post_id(), get_post_type( $order_detail->get_post_id() ) ) ). '">' . $name . '</a>';
				}
				echo apply_filters( 'tcp_cart_table_title_order_detail', $name, $order_detail->get_post_id() ); ?>
				</td>
				<td class="tcp_cart_price"><?php echo tcp_format_the_price( $order_detail->get_price() ); ?>
				<?php if ( $order_detail->get_discount() > 0 ) : ?>
					&nbsp;<span class="tcp_cart_discount"><?php  printf( __( 'Discount %s', 'tcp' ), tcp_format_the_price( $order_detail->get_discount() / $order_detail->get_qty_ordered() ) ); ?></span>
				<?php endif; ?>
				</td>
				<td class="tcp_cart_units">
				<?php if ( ! $source->is_editing_units() ) :
					echo tcp_number_format( $order_detail->get_qty_ordered(), 0 );
				else : ?>
					<form method="post">
					<input type="hidden" name="tcp_post_id" value="<?php echo $order_detail->get_post_id();?>" />
					<input type="hidden" name="tcp_option_1_id" value="<?php echo $order_detail->get_option_1_id(); ?>" />
					<input type="hidden" name="tcp_option_2_id" value="<?php echo $order_detail->get_option_2_id(); ?>" />
					<?php if ( ! tcp_is_downloadable( $order_detail->get_post_id() ) ) : ?>
						<input type="text" name="tcp_count" value="<?php echo $order_detail->get_qty_ordered(); ?>" size="2" maxlength="4" class="tcp_count"/>
						<input type="submit" name="tcp_modify_item_shopping_cart" class="tcp_modify_item_shopping_cart" value="<?php _e( 'Modify', 'tcp' ); ?>" />
					<?php else : ?>
						1&nbsp;
					<?php endif; ?>
					<input type="submit" name="tcp_delete_item_shopping_cart" class="tcp_delete_item_shopping_cart" value="<?php _e( 'Delete', 'tcp' ); ?>" />
					<?php do_action( 'tcp_cart_units', $order_detail ); ?>
					</form>
				<?php endif; ?>
				</td>
				<?php if ( $source->see_full() ) : ?><td class="tcp_cart_sku"><?php echo $order_detail->get_sku(); ?></td><?php endif; ?>
				<?php if ( $source->see_full() ) : ?><td class="tcp_cart_weight"><?php echo tcp_number_format( $order_detail->get_weight(), 0 ); ?>&nbsp;<?php echo tcp_get_the_unit_weight(); ?></td><?php endif; ?>
				<?php $price = $order_detail->get_price() * $order_detail->get_qty_ordered() - $order_detail->get_discount();
				$tax = round( $order_detail->get_price() * ( $order_detail->get_tax() / 100 ), tcp_get_decimal_currency() ) * $order_detail->get_qty_ordered();
				$total_tax += $tax;
				$total += $price; ?>
				<td class="tcp_cart_total"><?php echo tcp_format_the_price( $price ); ?></td>
				</tr>
			<?php endforeach;
		} ?>
		<tr class="tcp_cart_subtotal_row">
		<?php $colspan = 3;
		if ( $source->see_full() ) $colspan += 3;
		if ( $source->see_thumbnail() ) $colspan ++; ?>
		<td colspan="<?php echo $colspan; ?>" class="tcp_cart_subtotal_title"><?php _e( 'Subtotal', 'tcp' ); ?></td>
		<td class="tcp_cart_subtotal"><?php echo tcp_format_the_price( $total ); ?></td>
		</tr>
		<?php $discount = $source->get_discount();
		if ( $discount > 0 ) : ?>
			<tr class="tcp_cart_discount_row<?php if ( $i++ & 1 == 1 ) : ?> tcp_par<?php endif; ?>">
			<td colspan="<?php echo $colspan; ?>" class="tcp_cart_discount_title"><?php _e( 'Discount', 'tcp' ); ?></td>
			<td class="tcp_cart_discount">-<?php echo tcp_format_the_price( $discount ); ?></td>
			</tr>
			<?php $total = $total - $discount; ?>
		<?php endif;
		if ( $source->see_other_costs() ) {
			if ( $source->has_orders_costs() ) {
				foreach( $source->get_orders_costs() as $order_cost ) : ?>
					<tr class="tcp_cart_other_costs_row">
					<td colspan="<?php echo $colspan; ?>" class="tcp_cart_other_costs_title"><?php echo $order_cost->get_description(); ?></td>
					<td class="tcp_cart_other_costs"><?php echo tcp_format_the_price( $order_cost->get_cost() ); ?></td>
					<?php $tax = $order_cost->get_cost() * ( $order_cost->get_tax() / 100 );
					$total_tax += $tax;
					$total += $order_cost->get_cost(); ?>
					</tr>
				<?php endforeach;
			}
		}
		if ( $source->see_tax_summary() && $total_tax > 0 ) : ?>
			<tr class="tcp_cart_tax_row">
			<td colspan="<?php echo $colspan;?>" class="tcp_cart_tax_title"><?php _e( 'Taxes', 'tcp' ); ?></td>
			<td class="tcp_cart_tax"><?php echo tcp_format_the_price( $total_tax ); ?></td>
			</tr>
		<?php else :
			$total_tax = 0;
		endif;
		$total += $total_tax; ?>
		<tr class="tcp_cart_total_row">
		<td colspan="<?php echo $colspan; ?>" class="tcp_cart_total_title"><?php _e( 'Total', 'tcp' ); ?></td>
		<td class="tcp_cart_total"><?php echo tcp_format_the_price( $total ); ?></td>
		</tr>
		</tbody></table>
		<?php if ( $source->see_comment() && strlen( $source->get_comment() ) > 0 ) : ?><p><?php echo $source->get_comment(); ?></p><?php endif;
		$out = ob_get_clean();
		if ( $echo ) echo $out;
		else return $out;
	}
}
?>
