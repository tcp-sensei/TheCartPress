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
if ( !defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'OrdersListTable' ) ) {

require_once( TCP_CLASSES_FOLDER . 'OrderPage.class.php' );

class OrdersListTable extends WP_List_Table {

	function __construct() {
		parent::__construct( array(
			'plural' => 'Orders',
		) );
	}

	function ajax_user_can() {
		return false;
	}

	function no_items() {
		_e( 'No Orders found.', 'tcp' );
	}

	function prepare_items() {
		if ( ! is_user_logged_in() ) return;
		$status = isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : '';
		$search_by = isset( $_REQUEST['search_by'] ) ? $_REQUEST['search_by'] : '';
		$per_page = apply_filters( 'tcp_orders_per_page', 15 );
		$paged = $this->get_pagenum();
		if ( current_user_can( 'tcp_edit_orders' ) ) {
			//$search_by //TODO
			$this->items = Orders::getOrdersEx( $paged, $per_page, $status );
			$total_items = Orders::getCountOrdersByStatus( $status, $search_by );
		} else {
			global $current_user;
			get_currentuserinfo();
			//$search_by //TODO
			$this->items = Orders::getOrdersEx( $paged, $per_page, $status, $current_user->ID );
			$total_items = Orders::getCountOrdersByStatus( $status, $current_user->ID );
		}
		$total_pages = $total_items / $per_page;
		if ( $total_pages > (int)$total_pages ) {
			$total_pages = (int)$total_pages;
			$total_pages++;
		}
		$this->set_pagination_args( array(
			'total_items'	=> $total_items,
			'per_page'		=> $per_page,
			'total_pages'	=> $total_pages,
		) );
	}

	function get_table_classes() {
		return array( 'widefat', 'fixed', 'pages', 'tcp_orders'  );
	}

	function get_column_info() {
		$orders_columns = array();
		//$orders_columns['cb'] = '<input type="checkbox" />';
		$orders_columns['order_id'] = _x( 'ID', 'column name', 'tcp' );
		$orders_columns['created_at'] = _x( 'Date', 'column name', 'tcp' );
		$orders_columns['customer_id'] = _x( 'User', 'column name', 'tcp' );
		$orders_columns['status'] = _x( 'Status', 'column name', 'tcp' );
		$orders_columns['payment_name'] = _x( 'Payment', 'column name', 'tcp' );
		$orders_columns['shipping_method'] = _x( 'Shipping', 'column name', 'tcp' );
		$orders_columns['total'] = _x( 'Total', 'column name', 'tcp' );
		$orders_columns = apply_filters( 'tcp_manage_orders_columns', $orders_columns );
		return array( $orders_columns , array(), array() );
	}

	function column_cb( $item ) {
		?><input type="checkbox" name="order[]" value="<?php echo $item->order_id; ?>" /><?php
	}

	function column_total( $item ) {
		//$total = - $item->discount_amount;
		//$total = OrdersCosts::getTotalCost( $item->order_id, $total );
		//$total = tcp_format_the_price( OrdersDetails::getTotal( $item->order_id, $total ) );

		$total = Orders::getTotal( $item->order_id );
		$total = apply_filters( 'tcp_orders_list_column_total', $total, $item );
		echo tcp_format_the_price( $total, $item->order_currency_code );
	}

	function column_customer_id( $item ) {
		$user_data = get_userdata( $item->customer_id );
		if ( $user_data ) {
			echo $user_data->user_nicename, ' &lt;', $user_data->user_email, '&gt;';
		} else {
			echo '&lt;', $item->billing_email, '&gt;';
		}
	}

	function column_order_id( $item ) {
		echo $item->order_id;
		$actions = array();
		$status = isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : '';
		$paged = isset( $_REQUEST['paged'] ) ? $_REQUEST['paged'] : 0;
		$href = TCP_ADMIN_PATH . 'OrderEdit.php&order_id=' . $item->order_id . '&status=' . $status . '&paged=' . $paged;
		if ( current_user_can( 'tcp_edit_orders' ) )
			$actions['edit'] = '<a href="' . $href . '" title="' . esc_attr( __( 'Edit this order', 'tcp' ) ) . '">' . __( 'Edit', 'tcp' ) . '</a>';
		$actions['inline hide-if-no-js'] = '<a href="javascript:tcp_show_order_view(' . $item->order_id . ');" class="editinline" title="' . esc_attr( __( 'View this item inline' ) ) . '">' . __( 'View', 'tcp' ) . '</a>';
		//$url = plugins_url( 'admin/PrintOrder.php', dirname( __FILE__ ) );
		//$url = add_query_arg( 'sorder_id', $item->order_id, $url );
		//$actions['inline hide-if-no-js'] = '<a href="' . $url . '" onclick="return false;" class="thickbox" title="' . esc_attr( __( 'View this item inline' ) ) . '">' . __( 'View', 'tcp' ) . '</a>';
		
		echo $this->row_actions( $actions );
		$this->get_inline_data( $item->order_id );
	}

	function column_status( $item ) {
		echo tcp_get_status_label( $item->status );
	}

	function column_default( $item, $column_name ) {
		$out = isset( $item->$column_name ) ?  strip_tags( $item->$column_name ) : '???';
		$out = apply_filters( 'tcp_orders_list_columns', $out, $item, $column_name );
		echo $out;
	}
	
	function extra_tablenav( $which ) {
		if ( 'top' != $which ) return;
		$status = isset( $_REQUEST['status'] ) ? $_REQUEST['status'] : ''; ?>
		<label for="status"><?php _e( 'Status', 'tcp' );?>:</label>
		<select class="postform" id="status" name="status">
			<option value="" <?php selected( '', $status );?>><?php _e( 'all', 'tcp' );?></option>
		<?php $order_status_list = tcp_get_order_status();
		foreach ( $order_status_list as $order_status ) : ?>
			<option value="<?php echo $order_status['name'];?>"<?php selected( $order_status['name'], $status );?>><?php echo $order_status['label']; ?></option>
		<?php endforeach; ?>
		</select>
		<?php $search_by = isset( $_REQUEST['search_by'] ) ? $_REQUEST['search_by'] : ''; ?>
		<label><?php _e( 'Search by', 'tcp' ); ?>:<input type="text" name="search_by" value="<?php echo $search_by; ?>"/></label>
		<?php do_action( 'tcp_restrict_manage_orders' );
		submit_button( __( 'Filter' ), 'secondary', false, false, array( 'id' => 'order-query-submit' ) );
	}

	private function get_inline_data( $order_id ) { ?>
		<div class="hidden" id="inline_<?php echo $order_id; ?>">
		<?php $out = OrderPage::show( $order_id );
		echo apply_filters( 'tcp_orders_list_get_inline_data', $out, $order_id ); ?>
		</div><?php
 	}
}

class TCPOrdersList {
	function show( $echo = true ) {
		ob_start();
		$ordersListTable = new OrdersListTable();
		$ordersListTable->prepare_items(); ?>
<form id="posts-filter" method="get" action="">
<input type="hidden" name="page" value="<?php echo isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : 0; ?>" />
<div class="wrap">
	<?php screen_icon( 'tcp-orders-list' ); ?><h2><?php _e( 'Orders', 'tcp' );?></h2>
	<div class="clear"></div>
	<?php $ordersListTable->search_box( __( 'Search Orders', 'tcp' ), 'order' ); ?>
	<?php $ordersListTable->display(); ?>
</div>
</form>
		<?php $out = ob_get_clean();
		if ( $echo ) echo $out;
		return $out;
	}
}
} // class_exists check