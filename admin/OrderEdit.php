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

require_once( TCP_CLASSES_FOLDER	. 'OrderPage.class.php' );

$order_id	= isset( $_REQUEST['order_id'] ) ? $_REQUEST['order_id'] : '';
$status		= isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : '';
$paged		= isset( $_REQUEST['paged'] ) ? $_REQUEST['paged'] : '';
$new_status = isset( $_REQUEST['new_status'] ) ? $_REQUEST['new_status'] : '';

if ( isset( $_REQUEST['tcp_order_edit'] ) && current_user_can( 'tcp_edit_orders' ) ) {
	try {
		Orders::edit( $order_id, $new_status, $_REQUEST['code_tracking'],  $_REQUEST['comment'], $_REQUEST['comment_internal'] );
	do_action( 'tcp_admin_order_editor_save', $order_id ); ?>
	<div id="message" class="updated">
		<p><?php _e( 'Order saved', 'tcp' ); ?></p>
	</div>
	<?php } catch ( Exception $e ) { ?>
		<div id="message" class="error">
			<p><?php echo $e->getMessage(); ?></p>
		</div>
	<?php }
	} elseif ( isset( $_REQUEST['tcp_order_delete'] ) && current_user_can( 'tcp_edit_orders' ) ) {
	Orders::delete( $order_id );
	do_action( 'tcp_admin_order_editor_delete', $order_id ); ?>
	<div id="message" class="updated">
		<p><?php _e( 'Order deleted', 'tcp' ); ?></p>
	</div>
	<p><a href="<?php echo TCP_ADMIN_PATH; ?>OrdersListTable.php&status=<?php echo $status?>&paged=<?php echo $paged?>"><?php _e( 'return to the list', 'tcp' ); ?></a></p>
	<?php return;
}
$order = Orders::get( $order_id );

if ( isset( $_REQUEST['send_email'] ) ) :
	require_once( TCP_CHECKOUT_FOLDER . 'ActiveCheckout.class.php' );
	if ( $_REQUEST['send_email'] != 'merchant' ) ActiveCheckout::sendMails( $order_id, '', true );
	else ActiveCheckout::sendOrderMails( $order_id, '', false, true ); ?>
	<div id="message" class="updated">
		<p><?php _e( 'Mail sent', 'tcp' ); ?></p>
	</div>
<?php endif; ?>
<style>
#shipping_info {
	width: 50%;
	float: left;
}
th {
	font-weight: bold;
}
th, td {
	text-align: left;
}
</style>

<?php

add_meta_box( 'tcp_order_id_metabox'			, __( 'Order ID', 'tcp' ), 'tcp_order_id_metabox' , 'tcp-order-edit', 'side', 'default' );
if ( strlen( $order->shipping_firstname ) &&  strlen( $order->shipping_lastname ) )
	add_meta_box( 'tcp_order_shipping_metabox'	, __( 'Shipping Address', 'tcp' ), 'tcp_order_shipping_metabox' , 'tcp-order-edit', 'side', 'default' );
add_meta_box( 'tcp_order_billing_metabox'		, __( 'Billing Address', 'tcp' ), 'tcp_order_billing_metabox' , 'tcp-order-edit', 'side', 'default' );
add_meta_box( 'tcp_order_details_metabox'		, __( 'Order details', 'tcp' ), 'tcp_order_details_metabox' , 'tcp-order-edit', 'normal', 'default' );
add_meta_box( 'tcp_order_setup_metabox'			, __( 'Order Setup', 'tcp' ), 'tcp_order_setup_metabox' , 'tcp-order-edit', 'normal', 'default' );

do_action( 'tcp_order_edit_metaboxes', $order_id, $order );

function tcp_order_id_metabox() {
	global $order_id, $order; ?>
<table id="tcp_order_id" width="100%" cellpading="0" cellspacing="0">
	<tr valign="top">
		<th class="tcp_order_id_row" scope="row"><?php _e( 'Order ID', 'tcp' ); ?>:</th>
		<td class="tcp_order_id_value tcp_order_id"><?php echo $order_id; ?></td>
	</tr>
	<tr valign="top">
		<th class="tcp_order_id_row" scope="row"><?php _e( 'Created at', 'tcp' ); ?>:</th>
		<td class="tcp_order_id_value tcp_created_at"><?php echo $order->created_at; ?></td>
	</tr>
	<tr>
		<th><?php _e( 'Status', 'tcp' ); ?>: </th>
		<?php $order_status = tcp_get_order_status(); ?>
		<td class="tcp_status_<?php echo $order->status; ?>"><?php echo $order_status[$order->status]['name']; ?></td>
	</tr>
	<?php do_action( 'tcp_order_id_metabox', $order_id, $order ); ?>
</table>
<?php }

function tcp_order_shipping_metabox() {
	global $order_id, $order; ?>
<table id="shipping_billing_info" width="100%" cellpading="0" cellspacing="0">
	<tr valign="top">
		<td class="shipping_info">
			<?php echo $order->shipping_firstname; ?> <?php echo $order->shipping_lastname; ?>
		</td>
	</tr>
<?php if ( strlen( $order->shipping_company ) > 0 || strlen( $order->billing_company ) > 0 ) : ?>
	<tr valign="top">
		<td class="shipping_info">
			<?php if ( strlen( $order->shipping_company ) > 0 ) : ?>
				<?php echo $order->shipping_company; ?>
			<?php endif; ?>&nbsp;
		</td>
	</tr>
<?php endif; ?>
<?php if ( strlen( $order->billing_tax_id_number ) > 0 ) : ?>
	<tr valign="top">
		<td class="shipping_info">
			&nbsp;
		</td>
	</tr>
<?php endif; ?>
	<tr valign="top">
		<td class="shipping_info">
			<?php echo $order->shipping_street; ?><br/>
		</td>
	</tr>
	<tr valign="top">
		<td class="shipping_info">
			<?php $out = array();
			if ( strlen( $order->shipping_postcode ) > 0 ) $out[] = $order->shipping_postcode;
			if ( strlen( $order->shipping_city ) > 0 ) $out[] = $order->shipping_city;
			echo implode( ', ', $out ); ?><br/>
		</td>
	</tr>
	<tr valign="top">
		<td class="shipping_info">
			<?php $out = array();
			if ( strlen( $order->shipping_region ) > 0 ) $out[] = $order->shipping_region;
			if ( strlen( $order->shipping_country ) > 0 ) $out[] = $order->shipping_country;
			echo implode( ', ', $out ); ?><br/>
		</td>
	</tr>
	<tr valign="top">
		<td class="shipping_info">
			<?php $telephone = $order->shipping_telephone_1;
			if ( strlen( $order->shipping_telephone_2 ) > 0 ) $telephone .= ' - ' . $order->shipping_telephone_2; ?>
			<?php if ( strlen( $telephone ) > 0 ) : _e( 'Telephones', 'tcp' ); ?>: <?php echo $telephone; ?><br/><?php endif; ?>
		</td>
	</tr>
	<tr valign="top">
		<td class="shipping_info">
			<?php if ( strlen( $order->shipping_fax ) > 0 ) : _e( 'Fax', 'tcp' ); ?>: <?php echo $order->shipping_fax; ?><?php endif; ?>
		</td>
	</tr>
	<tr valign="top">
		<td class="shipping_info">
			<?php if ( strlen( $order->shipping_email ) > 0 ) : echo $order->shipping_email; ?><?php endif; ?>
		</td>
	</tr>
	<?php do_action( 'tcp_order_shipping_metabox', $order_id, $order ); ?>
</table>
<?php }

function tcp_order_billing_metabox() {
	global $order_id, $order; ?>
<table id="billing_info" width="100%" cellpading="0" cellspacing="0">
	<tr valign="top">
		<td class="billing_info">
			<?php echo $order->billing_firstname;?> <?php echo $order->billing_lastname; ?>
		</td>
	</tr>
<?php if ( strlen( $order->shipping_company ) > 0 || strlen( $order->billing_company ) > 0 ) : ?>
	<tr valign="top">
		<td class="billing_info">
			<?php if ( strlen( $order->billing_company ) > 0 ) : ?>
				<?php echo $order->billing_company; ?>
			<?php endif; ?>&nbsp;
		</td>
	</tr>
<?php endif; ?>
<?php if ( strlen( $order->billing_tax_id_number ) > 0 ) : ?>
	<tr valign="top">
		<td class="billing_info">
			<?php if ( strlen( $order->billing_tax_id_number ) > 0 ) : ?>
			<?php echo $order->billing_tax_id_number; ?>
			<?php endif; ?>&nbsp;
		</td>
	</tr>
<?php endif; ?>
	<tr valign="top">
		<td class="billing_info">
			<?php echo $order->billing_street; ?>
		</td>
	</tr>
	<tr valign="top">
		<td class="billing_info">
			<?php $out = array();
			if ( strlen( $order->billing_postcode ) > 0 ) $out[] = $order->billing_postcode;
			if ( strlen( $order->billing_city ) > 0 ) $out[] = $order->billing_city;
			echo implode( ', ', $out ); ?>
		</td>
	</tr>
	<tr valign="top">
		<td class="billing_info">
			<?php $out = array();
			if ( strlen( $order->billing_region ) > 0 ) $out[] = $order->billing_region;
			if ( strlen( $order->billing_country ) > 0 ) $out[] = $order->billing_country;
			echo implode( ', ', $out ); ?>
		</td>
	</tr>
	<tr valign="top">
		<td class="billing_info">
			<?php $telephone = $order->billing_telephone_1;
			if ( strlen( $order->billing_telephone_2 ) > 0 ) $telephone .= ' - ' . $order->billing_telephone_2; ?>
			<?php if ( strlen( $telephone ) > 0 ) : _e( 'Telephones', 'tcp' ); ?>: <?php echo $telephone; ?><br/><?php endif; ?>
		</td>
	</tr>
	<tr valign="top">
		<td class="billing_info">
			<?php if ( strlen( $order->billing_fax ) > 0 ) : _e( 'Fax', 'tcp' ); ?>: <?php echo $order->billing_fax; ?><?php endif; ?>
		</td>
	</tr>
	<tr valign="top">
		<td class="billing_info">
			<?php if ( strlen( $order->billing_email ) > 0 ) : echo $order->billing_email; ?><br/><?php endif; ?>
		</td>
	</tr>
	<?php do_action( 'tcp_order_billing_metabox', $order_id, $order ); ?>
</table>
<?php }

function tcp_order_details_metabox() {
	global $order_id, $order;
	$orderpage = OrderPage::show( $order_id, array( 'see_sku' => true, 'see_address' => false ), false );//, true );
	$orderpage = str_replace( '<table class="tcp_details"', '<table class="tcp_shopping_cart_table"', $orderpage );
	echo $orderpage;
}

function tcp_order_setup_metabox() {
	global $order_id, $order;
	do_action( 'tcp_admin_order_top', $order_id );
	if ( $order ) : ?>
<div>
<form method="post" name="frm">
	<input type="hidden" name="status" value="<?php echo $status = $order->status; ?>" />
	<input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />

	<table class="form-table">
	<tbody>
		<?php do_action( 'tcp_admin_order_before_editor', $order_id, $order ); ?>
		<tr valign="top">
			<th scope="col">
				<label style="font-weight:bold;"><?php _e( 'User email', 'tcp' ); ?></label>
			</th>
			<td>
				<?php $user_data = get_userdata( $order->customer_id );
				if ( $user_data ) printf( __( '%s&lt;%s&gt; (registered)', 'tcp' ), $user_data->user_nicename, $user_data->user_email );
				else printf( __( '%s (unregistered)', 'tcp' ), $order->billing_email ); ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="col">
				<label style="font-weight:bold;"><?php _e( 'Shipping method', 'tcp' ); ?></label>
			</th>
			<th scope="col">
				<label style="font-weight:bold;"><?php _e( 'Payment method', 'tcp' ); ?></label>
			</th>
		</tr>
		<tr valign="top">
			<td><?php echo $order->shipping_method; ?></td>
			<td><?php echo $order->payment_name; ?></td>
		</tr>
		<tr valign="top">
			<th scope="col">
				<label style="font-weight:bold;"><?php _e( 'Transaction id', 'tcp' ); ?>:</label>
			</th>
			<th scope="col">
				<label style="font-weight:bold;">IP:</label>
			</th>
		</tr>
		<tr valign="top">
			<td><?php echo $order->transaction_id; ?></td>
			<td><?php echo $order->ip; ?></td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="new_status"  style="font-weight:bold;"><?php _e( 'Status', 'tcp' ); ?>:</label>
			</th>
			<td>
				<select class="postform" id="new_status" name="new_status">
				<?php $order_status_list = tcp_get_order_status();
				foreach ( $order_status_list as $order_status ) : ?>
					<option value="<?php echo $order_status['name'];?>"<?php selected( $order_status['name'], $order->status ); ?>><?php echo $order_status['label']; ?></option>
				<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="code_tracking"  style="font-weight:bold;"><?php _e( 'Code tracking', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input name="code_tracking" id="code_tracking" type="text" size="20" maxlength="50" value="<?php echo $order->code_tracking; ?>" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="comment"  style="font-weight:bold;"><?php _e( 'Customer\'s comment', 'tcp' ); ?>:</label>
			</th>
			<td>
				<textarea valign="top" name="comment" id="comment" rows="5" cols="40" maxlength="250"><?php echo $order->comment; ?></textarea>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="comment_internal"  style="font-weight:bold;"><?php _e( 'Internal comment', 'tcp' ); ?>:</label>
			</th>
			<td>
				<textarea valign="top" name="comment_internal" id="comment_internal" rows="5" cols="40" maxlength="250"><?php echo $order->comment_internal; ?></textarea>
			</td>
		</tr>
		<?php do_action( 'tcp_admin_order_after_editor', $order_id, $order ); ?>
		<tr>
			<th colspan="2" class="_submit" style="text-align: right;">
				<input name="tcp_order_edit" value="<?php _e( 'Save', 'tcp' ); ?>" type="submit" class="button-primary" />
				<?php do_action( 'tcp_admin_order_submit_area', $order_id ); ?>
				<?php if ( tcp_is_order_status_valid_for_deleting( $order->status ) ) : ?>
					<a href="#" onclick="jQuery('#delete_order').show();return false;" class="delete"><?php _e( 'Delete', 'tcp' ); ?></a>
					<div id="delete_order" style="display:none; border: 1px dotted orange; padding: 2px">
						<input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />
						<p><?php _e( 'Do you really want to delete this order?', 'tcp' ); ?></p>
						<input name="tcp_order_delete" value="<?php _e( 'Yes', 'tcp' ); ?>" type="submit" class="button-secondary" />
						<a href="" onclick="jQuery('#delete_order').hide();return false;"><?php _e( 'No, I don\'t' , 'tcp' ); ?></a>
					</div>
				<?php endif; ?>
			</td>
		</tr><!-- .submit -->
	</tbody>
	</table>
</form>
</div>
	<?php endif;
}
?>

<div class="wrap">

	<?php screen_icon( 'tcp-order' ); ?><h2><?php _e( 'Order', 'tcp' ); ?></h2>

	<ul class="subsubsub">
		<li><a href="<?php echo TCP_ADMIN_PATH; ?>OrdersListTable.php&status=<?php echo $status; ?>&paged=<?php echo $paged; ?>"><?php _e( 'Return to the list', 'tcp' ); ?></a></li>
	<?php if ( $order && strlen( $order->billing_email ) > 0 ) : ?>
		<li>&nbsp;|&nbsp;</li>
		<li><a href="<?php echo add_query_arg( array( 'send_email' => 'billing' ), get_permalink() ); ?>"><?php _e( 'Send email to billing email', 'tcp' ); ?></a></li>
	<?php endif;?>
	<?php if ( $order && strlen( $order->shipping_email ) > 0 ) : ?>
		<li>&nbsp;|&nbsp;</li>
		<li><a href="<?php echo add_query_arg( array( 'send_email' => 'shipping' ), get_permalink() ); ?>"><?php _e( 'Send email to shipping email', 'tcp' ); ?></a></li>
	<?php endif;?>
	<?php if ( $order_id > 0 ) : ?>
		<li>&nbsp;|&nbsp;</li>
		<li><a href="<?php echo add_query_arg( 'action', 'tcp_print_order', add_query_arg( 'order_id', $order_id, admin_url( 'admin-ajax.php' ) ) ); ?>" target="_blank"><?php _e( 'Print', 'tcp' ); ?></a></li>
	<?php endif;?>
	<?php if ( $order_id > 0 && current_user_can( 'tcp_edit_products') ) : ?>
		<li>&nbsp;|&nbsp;</li>
		<li><a href="<?php echo add_query_arg( array( 'send_email' => 'merchant' ), get_permalink() ); ?>"><?php _e( 'Send email to me', 'tcp' ); ?></a></li>
	<?php endif; ?>
	</ul><!-- subsubsub -->

	<div class="clear"></div>

	<div id="poststuff">

		<div id="post-body" class="metabox-holder columns-2">

			<div id="postbox-container-1" class="postbox-container">
				<?php do_meta_boxes( 'tcp-order-edit', 'side', null ); ?>
			</div>

			<div id="postbox-container-2" class="postbox-container">
				<?php do_meta_boxes( 'tcp-order-edit', 'normal', null ); ?>
				<?php do_meta_boxes( 'tcp-order-edit', 'advanced', null ); ?>
			</div>

		</div><!-- /post-body -->

		<br class="clear" />

	</div><!-- /poststuff -->

</div><!-- wrap -->