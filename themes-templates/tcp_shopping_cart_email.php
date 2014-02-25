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

//$source is type of TCP_ICartSource
if ( ! isset( $source ) ) return; ?>
	<table id="tcp_order_id" width="100%" cellpading="0" cellspacing="0">
		<tr valign="top">
			<th class="tcp_order_id_row" scope="row" style="text-align: left; width:160px;"><?php _e( 'Order ID', 'tcp' ); ?>:</th>
			<td class="tcp_order_id_value tcp_order_id"><?php echo $source->get_order_id(); ?></td>
		</tr>
		<tr valign="top">
			<th class="tcp_order_id_row" scope="row" style="text-align: left; width:160px;"><?php _e( 'Created at', 'tcp' ); ?>:</th>
			<td class="tcp_order_id_value tcp_created_at"><?php echo $source->get_created_at(); ?></td>
		</tr>
	</table>
	<?php if ( strlen( $source->get_shipping_firstname() ) > 0 && strlen( $source->get_shipping_lastname() ) > 0 ) : ?>
		<table id="shipping_billing_info" width="80%" cellpading="0" cellspacing="0" >
			<tr valign="top">
				<th class="shipping_info" style="text-align: left"><h3><?php _e( 'Shipping address', 'tcp' ); ?></h3></th>
				<th class="billing_info" style="text-align: left"><h3><?php _e( 'Billing address', 'tcp' ); ?></h3></th>
			</tr>
			<tr valign="top">
				<td class="shipping_info">
					<?php echo $source->get_shipping_firstname(); ?> <?php echo $source->get_shipping_lastname(); ?>
				</td>
				<td class="billing_info">
					<?php echo $source->get_billing_firstname();?> <?php echo $source->get_billing_lastname(); ?>
				</td>
			</tr>
		<?php if ( strlen( $source->get_shipping_company() ) > 0 || strlen( $source->get_billing_company() ) > 0 ) : ?>
			<tr valign="top">
				<td class="shipping_info">
					<?php if ( strlen( $source->get_shipping_company() ) > 0 ) : ?>
						<?php echo $source->get_shipping_company(); ?>
					<?php endif; ?>&nbsp;
				</td>
				<td class="billing_info">
					<?php if ( strlen( $source->get_billing_company() ) > 0 ) : ?>
						<?php echo $source->get_billing_company(); ?>
					<?php endif; ?>&nbsp;
				</td>
			</tr>
		<?php endif; ?>
		<?php if ( strlen( $source->get_billing_tax_id_number() ) > 0 ) : ?>
			<tr valign="top">
				<td class="shipping_info">
					&nbsp;
				</td>
				<td class="billing_info">
					<?php if ( strlen( $source->get_billing_tax_id_number() ) > 0 ) : ?>
					<?php echo $source->get_billing_tax_id_number(); ?>
					<?php endif; ?>&nbsp;
				</td>
			</tr>
		<?php endif; ?>
			<tr valign="top">
				<td class="shipping_info">
					<?php echo $source->get_shipping_street(); ?><br/>
				</td>
				<td class="billing_info">
					<?php echo $source->get_billing_street(); ?>
				</td>
			</tr>
			<tr valign="top">
				<td class="shipping_info">
					<?php $out = array();
					if ( strlen( $source->get_shipping_postcode() ) > 0 ) $out[] = $source->get_shipping_postcode();
					if ( strlen( $source->get_shipping_city() ) > 0 ) $out[] = $source->get_shipping_city();
					echo implode( ', ', $out ); ?>
				</td>
				<td class="billing_info">
					<?php $out = array();
					if ( strlen( $source->get_billing_postcode() ) > 0 ) $out[] = $source->get_billing_postcode();
					if ( strlen( $source->get_billing_city() ) > 0 ) $out[] = $source->get_billing_city();
					echo implode( ', ', $out ); ?>
				</td>
			</tr>
			<tr valign="top">
				<td class="shipping_info">
					<?php $out = array();
					if ( strlen( $source->get_shipping_region() ) > 0 ) $out[] = $source->get_shipping_region();
					if ( strlen( $source->get_shipping_country() ) > 0 ) $out[] = $source->get_shipping_country();
					echo implode( ', ', $out ); ?>
				</td>
				<td class="billing_info">
					<?php $out = array();
					if ( strlen( $source->get_billing_region() ) > 0 ) $out[] = $source->get_billing_region();
					if ( strlen( $source->get_billing_country() ) > 0 ) $out[] = $source->get_billing_country();
					echo implode( ', ', $out ); ?>
				</td>
			</tr>
			<tr valign="top">
				<td class="shipping_info">
					<?php $telephone = $source->get_shipping_telephone_1();
					if ( strlen( $source->get_shipping_telephone_2() ) > 0 ) $telephone .= ' - ' . $source->get_shipping_telephone_2(); ?>
					<?php if ( strlen( $telephone ) > 0 ) : _e( 'Telephones', 'tcp' ); ?>: <?php echo $telephone; ?><br/><?php endif; ?>
				</td>
				<td class="billing_info">
					<?php $telephone = $source->get_billing_telephone_1();
					if ( strlen( $source->get_billing_telephone_2() ) > 0 ) $telephone .= ' - ' . $source->get_billing_telephone_2(); ?>
					<?php if ( strlen( $telephone ) > 0 ) : _e( 'Telephones', 'tcp' ); ?>: <?php echo $telephone; ?><br/><?php endif; ?>
				</td>
			</tr>
			<tr valign="top">
				<td class="shipping_info">
					<?php if ( strlen( $source->get_shipping_fax() ) > 0 ) : _e( 'Fax', 'tcp' ); ?>: <?php echo $source->get_shipping_fax(); ?><?php endif; ?>
				</td>
				<td class="billing_info">
					<?php if ( strlen( $source->get_billing_fax() ) > 0 ) : _e( 'Fax', 'tcp' ); ?>: <?php echo $source->get_billing_fax(); ?><?php endif; ?>
				</td>
			</tr>
			<tr valign="top">
				<td class="shipping_info">
					<?php if ( strlen( $source->get_shipping_email() ) > 0 ) : echo $source->get_shipping_email(); ?><?php endif; ?>
				</td>
				<td class="billing_info">
					<?php //if ( strlen( $source->get_billing_email() ) > 0 ) echo $source->get_billing_email(), '<br/>'; ?>
					<?php $user_data = get_userdata( $source->get_customer_id() );
					if ( $user_data ) printf( __( '%s&lt;%s&gt; (registered)', 'tcp' ), $user_data->user_nicename, $user_data->user_email );
					else printf( __( '%s (unregistered)', 'tcp' ), $source->get_billing_email() ); ?>
				</td>
			</tr>
		</table>
	<?php else : ?>
		<table style="margin-top:24px;" id="shipping_billing_info" width="100%" cellpading="0" cellspacing="0">
			<tr valign="top">
				<th class="billing_info" style="text-align: left"><h3><?php _e( 'Shipping and Billing address', 'tcp' ); ?></h3></th>
			</tr>
			<tr valign="top">
				<td class="billing_info">
					<?php echo $source->get_billing_firstname();?> <?php echo $source->get_billing_lastname(); ?>
				</td>
			</tr>
		<?php if ( strlen( $source->get_billing_company() ) > 0 ) : ?>
			<tr valign="top">
				<td class="billing_info">
					<?php echo $source->get_billing_company(); ?>
				</td>
			</tr>
		<?php endif; ?>
		<?php if ( strlen( $source->get_billing_tax_id_number() ) > 0 ) : ?>
			<tr valign="top">
				<td class="billing_info">
					<?php echo $source->get_billing_tax_id_number(); ?>
				</td>
			</tr>
		<?php endif; ?>
			<tr valign="top">
				<td class="billing_info">
					<?php echo $source->get_billing_street(); ?>
				</td>
			</tr>
			<tr valign="top">
				<td class="billing_info">
					<?php echo $source->get_billing_postcode(); ?>, <?php echo $source->get_billing_city(); ?>
				</td>
			</tr>
			<tr valign="top">
				<td class="billing_info">
					<?php echo $source->get_billing_region(); ?>, <?php echo $source->get_billing_country(); ?>
				</td>
			</tr>
			<tr valign="top">
				<td class="billing_info">
					<?php $telephone = $source->get_billing_telephone_1();
					if ( strlen( $source->get_billing_telephone_2() ) > 0 ) $telephone .= ' - ' . $source->get_billing_telephone_2(); ?>
					<?php if ( strlen( $telephone ) > 0 ) : _e( 'Telephones', 'tcp' ); ?>: <?php echo $telephone; ?><br/><?php endif; ?>
				</td>
			</tr>
			<tr valign="top">
				<td class="billing_info">
					<?php if ( strlen( $source->get_billing_fax() ) > 0 ) : _e( 'Fax', 'tcp' ); ?>: <?php echo $source->get_billing_fax(); ?><?php endif; ?>
				</td>
			</tr>

			<tr valign="top">
				<td class="billing_info">
					<?php if ( strlen( $source->get_billing_email() ) > 0 ) : echo $source->get_billing_email(); ?><br/><?php endif; ?>
				</td>
			</tr>
		</table>
	<?php endif; ?>


	<table style="margin-top:24px;" id="tcp_status" width="100%" cellpading="0" cellspacing="0">
		<tr valign="top">
			<th class="tcp_status_row" scope="row" style="text-align: left; width:160px;"><?php _e( 'Payment method', 'tcp' ); ?>: </th>
			<td class="tcp_status_value tcp_payment_method" ><?php echo $source->get_payment_name(); ?></td>
		</tr>
	<?php $notice = $source->get_payment_notice();
	$notice = apply_filters( 'tcp_shopping_cart_email_payment_notice', $notice, $source );
	if ( $notice ) : ?>
		<tr valign="top">
			<th class="tcp_status_row" scope="row" style="text-align: left; width:160px;"><?php _e( 'Payment notice', 'tcp' ); ?>: </th>
			<td class="tcp_payment_notice"><?php echo $notice; ?></td>
		</tr>
	<?php endif; ?>
		<tr valign="top">
			<th class="tcp_status_row" scope="row" style="text-align: left; width:160px;"><?php _e( 'Shipping method', 'tcp' ); ?>: </th>
			<td class="tcp_status_value tcp_shipping_method"><?php echo $source->get_shipping_method(); ?></td>
		</tr>
	<?php $notice = $source->get_shipping_notice();
	$notice = apply_filters( 'tcp_shopping_cart_email_shipping_notice', $notice, $source );
	if ( $notice ) : ?>
		<tr valign="top">
			<th class="tcp_status_row" scope="row" style="text-align: left; width:160px;"><?php _e( 'Shipping notice', 'tcp' ); ?>: </th>
			<td class="tcp_shipping_notice"><?php echo $notice; ?></td>
		</tr>
	<?php endif; ?>
		<tr valign="top">
			<th class="tcp_status_row" scope="row" style="text-align: left; width:160px;"><?php _e( 'Status', 'tcp' ); ?>: </th>
			<td class="tcp_status_value tcp_status tcp_status_<?php echo $source->get_status(); ?>"><?php echo tcp_get_status_label( $source->get_status() ); ?></td>
		</tr>
	</table>

<table style="margin-top:24px;" id="tcp_shopping_cart_table" class="tcp_shopping_cart_table" width="100%" cellpading="0" cellspacing="0">
<thead>
	<tr class="tcp_cart_title_row" style="background-color:#636363; color:#f0f0f0;">
		<?php if ( $source->see_thumbnail() ) : ?>
			<th class="tcp_cart_thumbnail">&nbsp;</th>
		<?php endif; ?>
		<th class="tcp_cart_name" style="text-align: left; padding:4px 4px 4px 4px;"><?php _e( 'Name', 'tcp' ); ?></th>
		<th class="tcp_cart_price" style="text-align: left; padding:4px 4px 4px 4px;"><?php _e( 'Price', 'tcp' ); ?></th>
		<th class="tcp_cart_units" style="text-align: left; padding:4px 4px 4px 4px;"><?php _e( 'Units', 'tcp' ); ?></th>
		<?php if ( $source->see_sku() ) : ?><th class="tcp_cart_sku" style="text-align: left; padding:4px 4px 4px 4px;"><?php _e( 'SKU', 'tcp' ); ?></th><?php endif; ?>
		<?php if ( $source->see_weight() ) : ?><th class="tcp_cart_weight" style="text-align: left; padding:4px 4px 4px 4px;"><?php _e( 'Weight', 'tcp' ); ?></th><?php endif; ?>
		<?php if ( $source->see_tax() ) : ?><th class="tcp_cart_tax" style="text-align: left; padding:4px 4px 4px 4px;"><?php _e( 'Tax', 'tcp' ); ?></th><?php endif; ?>
		<th class="tcp_cart_total" style="text-align: right; padding:4px 4px 4px 4px;"><?php _e( 'Total', 'tcp' ); ?></th>
	</tr>
</thead>
<tbody>
<?php $total_tax = 0;
$total = 0;
if ( $source->has_order_details() ) :
	$i = 0;
	foreach( $source->get_orders_details() as $order_detail ) : ?>
	<tr style="padding: 4px 4px 4px 4px;" class="tcp_cart_product_row <?php if ( $i++ & 1 == 1 ) : ?> par<?php endif; ?>">
		<?php if ( $source->see_thumbnail() ) : ?>
		<td class="tcp_cart_thumbnail" style="padding:4px 4px 4px 4px;">
		<?php $size = apply_filters( 'tcp_get_shopping_cart_image_size', array( 32, 32 ) );
		echo tcp_get_the_thumbnail( $order_detail->get_post_id(), $order_detail->get_option_1_id(), $order_detail->get_option_2_id(), $size ); ?>
		</td>
		<?php endif; ?>
		<td class="tcp_cart_name" style="padding:4px 4px 4px 4px;">
		<?php $name = tcp_get_the_title( $order_detail->get_post_id(), $order_detail->get_option_1_id(), $order_detail->get_option_2_id() );
		if ( strlen( $name ) == 0 ) $name = $order_detail->get_name();
		$name = apply_filters( 'tcp_cart_table_title_item', $name, $order_detail );
		if ( $source->see_product_link() ) {
			$name = '<a href="' . tcp_get_permalink( tcp_get_current_id( $order_detail->get_post_id(), get_post_type( $order_detail->get_post_id() ) ) ). '">' . $name . '</a>';
		} ?>
		<?php echo apply_filters( 'tcp_cart_table_title_order_detail', $name, $order_detail->get_post_id() ); ?>
		</td>
		<td class="tcp_cart_price" style="padding:4px 4px 4px 4px;"><?php echo tcp_format_the_price( $order_detail->get_price() ); ?>
			<?php if ( $order_detail->get_discount() > 0 ) : ?>
			&nbsp;<span class="tcp_cart_discount"><?php  printf( __( '(-%s)', 'tcp' ), tcp_format_the_price( $order_detail->get_discount() / $order_detail->get_qty_ordered() ) ); ?></span>
			<?php endif; ?>
		</td>
		<td class="tcp_cart_units" style="padding:4px 4px 4px 4px;">
		<?php if ( ! $source->is_editing_units() ) : ?>
			<?php echo tcp_number_format( $order_detail->get_qty_ordered(), 0 ); ?>
		<?php else : ?>
			<form method="post">
				<input type="hidden" name="tcp_post_id" value="<?php echo $order_detail->get_post_id();?>" />
				<input type="hidden" name="tcp_option_1_id" value="<?php echo $order_detail->get_option_1_id(); ?>" />
				<input type="hidden" name="tcp_option_2_id" value="<?php echo $order_detail->get_option_2_id(); ?>" />
				<?php do_action( 'tcp_get_shopping_cart_hidden_fields', $order_detail ); ?>
				<?php ob_start(); ?>
				<input type="number" name="tcp_count" value="<?php echo $order_detail->get_qty_ordered(); ?>" size="2" maxlength="4" class="tcp_count" min="0" step="1"/>
				<input type="submit" name="tcp_modify_item_shopping_cart" class="tcp_modify_item_shopping_cart" value="<?php _e( 'Modify', 'tcp' ); ?>" />
				<?php echo apply_filters( 'tcp_shopping_cart_page_units', ob_get_clean(), $order_detail ); ?>
				<input type="submit" name="tcp_delete_item_shopping_cart" class="tcp_delete_item_shopping_cart" value="<?php _e( 'Delete', 'tcp' ); ?>" />
				<?php do_action( 'tcp_cart_units', $order_detail ); ?>
			</form>
		<?php endif; ?>
		</td>
		<?php if ( $source->see_sku() ) : ?>
			<td class="tcp_cart_sku" style="padding:4px 4px 4px 4px;"><?php echo $order_detail->get_sku(); ?></td>
		<?php endif; ?>
		<?php if ( $source->see_weight() ) : ?>
			<td class="tcp_cart_weight" style="padding:4px 4px 4px 4px;"><?php echo tcp_number_format( $order_detail->get_weight() ); ?>&nbsp;<?php echo tcp_get_the_unit_weight(); ?></td>
		<?php endif; ?>
		<?php $decimals	= tcp_get_decimal_currency();
		if ( ! $source->is_discount_applied() ) {
			$discount = round( $order_detail->get_discount() / $order_detail->get_qty_ordered(), $decimals );
		} else {
			$discount = 0;
		}
		$price = $order_detail->get_price() - $discount;
		$price = round( $price, $decimals );
		$tax = $price * $order_detail->get_tax() / 100;
		$tax = round( $tax, $decimals );
		$total_tax += $tax * $order_detail->get_qty_ordered();
		$price = round( $price * $order_detail->get_qty_ordered(), $decimals );
		$price = apply_filters( 'tcp_shopping_cart_row_price', $price, $order_detail );
		$total += $price; ?>
		<?php if ( $source->see_tax() ) : ?>
			<td class="tcp_cart_tax" style="padding:4px 4px 4px 4px;"><?php echo tcp_format_the_price( $tax ); ?></td>
		<?php endif; ?>
		<td class="tcp_cart_total" style="text-align: right; padding:4px 4px 4px 4px;">
			<?php echo tcp_format_the_price( $price ); ?>
		</td>
	</tr>
	<?php endforeach; ?>
<?php endif; ?>
	<tr class="tcp_cart_subtotal_row" style="background-color:#f0f0f0;">
	<?php $colspan = 3;
	if ( $source->see_weight() ) $colspan ++;
	if ( $source->see_sku() ) $colspan ++;
	if ( $source->see_tax() ) $colspan ++;
	if ( $source->see_thumbnail() ) $colspan ++; ?>
	<td colspan="<?php echo $colspan; ?>" class="tcp_cart_subtotal_title" style="text-align: right; padding: 24px 4px 4px 4px;"><?php _e( 'Subtotal', 'tcp' ); ?></td>
	<td class="tcp_cart_subtotal" style="text-align: right; padding: 24px 4px 4px 4px;"><?php echo tcp_format_the_price( $total ); ?></td>
	</tr>
	<?php $discount = $source->get_discount();
	if ( $discount > 0 ) : ?>
		<tr style="background-color:#f0f0f0;" class="tcp_cart_discount_row<?php if ( $i++ & 1 == 1 ) : ?> tcp_par<?php endif; ?>" >
		<td colspan="<?php echo $colspan; ?>" class="tcp_cart_discount_title" style="text-align: right; padding:4px 4px 4px 4px;"><?php _e( 'Discount', 'tcp' ); ?></td>
		<td class="tcp_cart_discount" style="text-align: right; padding:4px 4px 4px 4px;">-<?php echo tcp_format_the_price( $discount ); ?></td>
		</tr>
		<?php $total = $total - $discount; ?>
	<?php endif;
	if ( $source->see_other_costs() ) :
		if ( $source->has_orders_costs() ) :
			foreach( $source->get_orders_costs() as $order_cost ) : ?>
				<tr style="background-color:#f0f0f0;" class="tcp_cart_other_costs_row">
				<td colspan="<?php echo $colspan; ?>" class="tcp_cart_other_costs_title" style="text-align: right; padding:4px 4px 4px 4px;"><?php echo $order_cost->get_description(); ?></td>
				<td class="tcp_cart_other_costs" style="text-align: right; padding:4px 4px 4px 4px;"><?php echo tcp_format_the_price( $order_cost->get_cost() ); ?></td>
				<?php $tax = $order_cost->get_cost() * ( $order_cost->get_tax() / 100 );
				$total_tax += $tax;
				$total += $order_cost->get_cost(); ?>
				</tr>
			<?php endforeach;
		endif;
	endif;
	if ( $source->see_tax_summary() && $total_tax > 0 ) : ?>
		<tr style="background-color:#f0f0f0;" class="tcp_cart_tax_row">
		<td colspan="<?php echo $colspan;?>" class="tcp_cart_tax_title" style="text-align: right; padding:4px 4px 4px 4px;"><?php _e( 'Taxes', 'tcp' ); ?></td>
		<td class="tcp_cart_tax" style="text-align: right; padding:4px 4px 4px 4px;"><?php echo tcp_format_the_price( $total_tax ); ?></td>
		</tr>
	<?php $total += $total_tax; ?>
	<?php endif; ?>
	<tr style="text-transform:uppercase; background-color:#f0f0f0; font-weight:bold;" class="tcp_cart_total_row">
	<td colspan="<?php echo $colspan; ?>" class="tcp_cart_total_title" style="text-align: right; padding:4px 4px 24px 4px;"><?php _e( 'Total', 'tcp' ); ?></td>
	<td class="tcp_cart_total" style="text-align: right; padding:4px 4px 24px 4px;"><?php echo tcp_format_the_price( $total ); ?></td>
	</tr>
</tbody>
</table>
<?php if ( $source->see_comment() && strlen( $source->get_comment() ) > 0 ) : ?>
	<p class="tcp_comment"><?php echo $source->get_comment(); ?></p>
<?php endif; ?>