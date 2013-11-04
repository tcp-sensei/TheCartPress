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

require_once( TCP_DAOS_FOLDER . 'Orders.class.php' );

class OrdersSummaryDashboard {

	function __construct() {
		if ( current_user_can( 'tcp_edit_orders' ) || current_user_can( 'tcp_edit_order' ) )
			wp_add_dashboard_widget( 'tcp_orders_resume', __( 'Orders Summary', 'tcp' ), array( $this, 'show' ) );
	}

	function show() {
		if ( current_user_can( 'tcp_edit_orders' ) ) {
			$customer_id = -1;
		} else {
			global $current_user;
			get_currentuserinfo();
			$customer_id = $current_user->ID;
		} ?>
<div class="table table_content">
	<table style="width:100%"><tbody>
	<?php $order_status_list = tcp_get_order_status();
	foreach ( $order_status_list as $order_status ) : 
		if ( $order_status['show_in_dashboard'] ) : ?>
	<tr class="first">
		<td class="first b"><a href="<?php echo TCP_ADMIN_PATH, 'OrdersListTable.php&status=', $order_status['name']; ?>"><?php echo Orders::getCountOrdersByStatus( $order_status['name'], $customer_id );?></a></td>
		<td class="t tcp_status_<?php echo $order_status['name']; ?>"><a href="<?php echo TCP_ADMIN_PATH, 'OrdersListTable.php&status=', $order_status['name']; ?>"><?php echo $order_status['label']; ?></a></td>
	</tr>
		<?php endif;
	endforeach; ?>
	</tbody></table>
</div>
<div class="table_last_orders">
<h4><?php _e( 'Latest orders', 'tcp' ); ?></h4>
	<?php if ( current_user_can( 'tcp_edit_orders' ) ) {
		$orders = Orders::getLastOrders( 5 );
	} else {
		$current_user = wp_get_current_user();
		$orders = Orders::getLastOrders( 5, '', $current_user->ID );
	}
	if ( is_array( $orders ) && count ( $orders ) > 0 ) : 
		$all_status = tcp_get_order_status(); ?>
		<table class="last_orders" style="width:100%">
		<thead>
		<tr>
		<th><?php _e( 'Id', 'tcp' ); ?></th>
		<th><?php _e( 'Status', 'tcp' ); ?></th>
		<th><?php _e( 'Date', 'tcp' ); ?></th>
		<th><?php _e( 'Customer', 'tcp' ); ?></th>
		<th><?php _e( 'Total', 'tcp' ); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php 
		$alternate = true;
		foreach( $orders as $order ) : ?>
			<tr class="<?php if ( $alternate ) : ?> tcp_alternate<?php endif; $alternate = ! $alternate;?>">
			<td class="tcp_id"><a href="<?php echo TCP_ADMIN_PATH;?>OrderEdit.php&order_id=<?php echo $order->order_id;?>"><?php echo $order->order_id; ?></a></td>
			<td class="tcp_status tcp_status_<?php echo $all_status[$order->status]['name']; ?>"><a href="<?php echo TCP_ADMIN_PATH;?>OrderEdit.php&order_id=<?php echo $order->order_id;?>"><?php echo isset( $all_status[$order->status]['label'] ) ? $all_status[$order->status]['label'] : '&nbsp;'; ?></a></td>
			<td class="tcp_date"><?php echo date( 'M d' , strtotime( $order->created_at) ); ?></td>
			<td class="tcp_email"><?php echo $order->billing_email; ?></td>
			<td class="tcp_price"><?php echo tcp_format_the_price( Orders::getTotal( $order->order_id ) ); ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php endif; ?>
</div>
<div class="tcp_dashboard_links">
	<p><a class="tcp_link_to_tcp" href="http://thecartpress.com/" target="_blank" title="<?php _e( 'link to TheCartPress site', 'tcp'); ?>"><?php _e( 'Visit TheCartPress site', 'tcp'); ?></a>
	| <a class="tcp_link_to_tcp" href="http://community.thecartpress.com/forums/" target="_blank" title="<?php _e( 'link to TheCartPress community', 'tcp'); ?>"><?php _e( 'Visit our community', 'tcp'); ?></a>
	| <a class="tcp_link_to_tcp" href="http://extend.thecartpress.com/forums/" target="_blank" title="<?php _e( 'link to TheCartPress extend site', 'tcp'); ?>"><?php _e( 'Visit the Extend site', 'tcp'); ?></a></p>
</div>
	<?php }
}

new OrdersSummaryDashboard();
?>
