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

require_once( 'OrdersDetails.class.php' );
require_once( 'OrdersCosts.class.php' );

class Orders {

	public static $ORDER_PENDING	= 'PENDING';
	public static $ORDER_PROCESSING	= 'PROCESSING';
	public static $ORDER_COMPLETED	= 'COMPLETED';
	public static $ORDER_CANCELLED	= 'CANCELLED';
	public static $ORDER_SUSPENDED	= 'SUSPENDED';

	static function createTable() {
		global $wpdb;
		$sql = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . 'tcp_orders` (
		  `order_id`				bigint(20) unsigned NOT NULL auto_increment,
		  `created_at`				datetime			NOT NULL,
		  `customer_id`				bigint(20) unsigned NOT NULL,
  		  `ip`						varchar(20)			NOT NULL,
		  `weight`					int(11)				NOT NULL default 0,
		  `shipping_method`			varchar(100)		NOT NULL,
		  `status`					varchar(50)			NOT NULL,
		  `order_currency_code`		char(3)				NOT NULL,
		  `shipping_amount`			decimal(13, 2)		NOT NULL default 0,
		  `discount_amount`			decimal(13, 2)		NOT NULL default 0,
		  `payment_name`			varchar(255)		NOT NULL,
  		  `payment_method`			varchar(100)		NOT NULL default \'\',
		  `payment_amount`			decimal(13, 2)		NOT NULL default 0,
		  `transaction_id`			varchar(250)		NOT NULL default \'\',
		  `comment`					varchar(250)		NOT NULL,
		  `comment_internal`		varchar(250)		NOT NULL default \'\',
		  `code_tracking`			varchar(50)			NOT NULL,
		  `shipping_firstname`		varchar(50)			NOT NULL,
		  `shipping_lastname`		varchar(100)		NOT NULL,
		  `shipping_company`		varchar(50)			NOT NULL,
		  `shipping_street`			varchar(100)		NOT NULL,
		  `shipping_city`			varchar(100)		NOT NULL,
		  `shipping_city_id`		char(4)				NOT NULL DEFAULT \'\',
		  `shipping_region`			varchar(100)		NOT NULL,
		  `shipping_region_id`		char(2)				NOT NULL DEFAULT \'\',
		  `shipping_postcode`		char(10)			NOT NULL,
 		  `shipping_country`		varchar(50)			NOT NULL,
		  `shipping_country_id`		char(2)				NOT NULL,
		  `shipping_telephone_1`	varchar(50)			NOT NULL,
		  `shipping_telephone_2`	varchar(50)			NOT NULL,
		  `shipping_fax`			varchar(50)			NOT NULL,
  		  `shipping_email`			varchar(255)		NOT NULL,
		  `billing_firstname`		varchar(50)			NOT NULL,
		  `billing_lastname`		varchar(100)		NOT NULL,
		  `billing_company`			varchar(50)			NOT NULL,
		  `billing_street`			varchar(100)		NOT NULL,
		  `billing_city`			varchar(100)		NOT NULL default 0,
		  `billing_city_id`			char(4)				NOT NULL DEFAULT \'\',
		  `billing_region`			varchar(100)		NOT NULL,
		  `billing_region_id`		char(2)				NOT NULL DEFAULT \'\',
		  `billing_postcode`		char(10)			NOT NULL,
 		  `billing_country`			varchar(50)			NOT NULL,
		  `billing_country_id`		char(2)				NOT NULL,
		  `billing_telephone_1`		varchar(50)			NOT NULL,
		  `billing_telephone_2`		varchar(50)			NOT NULL,
		  `billing_fax`				varchar(50)			NOT NULL,
  		  `billing_email`			varchar(255)		NOT NULL,
		  PRIMARY KEY  (`order_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
		$wpdb->query( $sql );
	}

	static function get( $order_id ) {
		global $wpdb;
		return $wpdb->get_row( $wpdb->prepare( 'select * from ' . $wpdb->prefix . 'tcp_orders where order_id = %d', $order_id ) );
	}

	static function getTotal( $order_id ) {
		$order = Orders::get( $order_id );
		if ( $order ) {
			$total = OrdersCosts::getTotalCost( $order_id, -$order->discount_amount );
			return OrdersDetails::getTotal( $order_id, $total );
		} else {
			return 0;
		}
	}

	static function delete( $order_id ) {
		global $wpdb;
		$wpdb->query( $wpdb->prepare( 'delete from ' . $wpdb->prefix . 'tcp_orders where order_id = %d' , $order_id ) );
	}

	static function is_owner( $order_id, $customer_id ) {
		global $wpdb;
		$count = $wpdb->get_var( $wpdb->prepare( 'select count(*) from ' . $wpdb->prefix . 'tcp_orders where order_id = %d and customer_id = %d', $order_id, $customer_id ) );
		return $count == 1;
	}

	/**
	 * Inserts an order.
	 *
	 * @param $order is an array with all the values of the table orders.
	 */
	static function insert( $order ) {
		global $wpdb;
		$wpdb->insert($wpdb->prefix . 'tcp_orders', array(
			'created_at'			=> $order['created_at'],
			'customer_id'			=> $order['customer_id'],
			'ip'					=> $order['ip'],
			'weight'				=> $order['weight'],
			'shipping_method'		=> $order['shipping_method'],
			'status'				=> $order['status'],
			'order_currency_code'	=> $order['order_currency_code'],
			'shipping_amount'		=> $order['shipping_amount'],
			'discount_amount'		=> $order['discount_amount'],
			'payment_name'			=> $order['payment_name'],
			'payment_method'		=> $order['payment_method'],
			'payment_amount'		=> $order['payment_amount'],
			'transaction_id'		=> $order['transaction_id'],
			'comment'				=> $order['comment'],
			'comment_internal'		=> $order['comment_internal'],
			'code_tracking'			=> $order['code_tracking'],
			'shipping_firstname'	=> $order['shipping_firstname'],
			'shipping_lastname'		=> $order['shipping_lastname'],
			'shipping_company'		=> $order['shipping_company'],
			'shipping_street'		=> $order['shipping_street'],
			'shipping_city'			=> $order['shipping_city'],
			'shipping_city_id'		=> $order['shipping_city_id'],
			'shipping_region'		=> $order['shipping_region'],
			'shipping_region_id'	=> $order['shipping_region_id'],
			'shipping_postcode'		=> Orders::removeSpaces( $order['shipping_postcode'] ),
			'shipping_country'		=> $order['shipping_country'],
			'shipping_country_id'	=> $order['shipping_country_id'],
			'shipping_telephone_1'	=> $order['shipping_telephone_1'],
			'shipping_telephone_2'	=> $order['shipping_telephone_2'],
			'shipping_fax'			=> $order['shipping_fax'],
			'shipping_email'		=> $order['shipping_email'],
			'billing_firstname'		=> $order['billing_firstname'],
			'billing_lastname'		=> $order['billing_lastname'],
			'billing_company'		=> $order['billing_company'],
			'billing_street'		=> $order['billing_street'],
			'billing_city'			=> $order['billing_city'],
			'billing_city_id'		=> $order['billing_city_id'],
			'billing_region'		=> $order['billing_region'],
			'billing_region_id'		=> $order['billing_region_id'],
			'billing_postcode'		=> Orders::removeSpaces( $order['billing_postcode'] ),
			'billing_country'		=> $order['billing_country'],
			'billing_country_id'	=> $order['billing_country_id'],
			'billing_telephone_1'	=> $order['billing_telephone_1'],
			'billing_telephone_2'	=> $order['billing_telephone_2'],
			'billing_fax'			=> $order['billing_fax'],
			'billing_email'			=> $order['billing_email'],
		), array('%s', '%d', '%s', '%d', '%s', '%s', '%s', '%f', '%f', '%s', '%s', '%f', '%s', '%s',
				 '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s',
				 '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s',
				 '%s', '%s', '%s', '%s')
		);
		return $wpdb->insert_id;
	}

/*	static function getCountPendingOrders( $customer_id = -1 ) {
		return Orders::getCountOrdersByStatus( Orders::$ORDER_PENDING, $customer_id );
	}

	static function getCountProcessingOrders( $customer_id = -1) {
		return Orders::getCountOrdersByStatus( Orders::$ORDER_PROCESSING, $customer_id );
	}

	static function getCountCompletedOrders( $customer_id = -1) {
		return Orders::getCountOrdersByStatus( Orders::$ORDER_COMPLETED, $customer_id );
	}

	static function getCountCancelledOrders( $customer_id = -1) {
		return Orders::getCountOrdersByStatus( Orders::$ORDER_CANCELLED, $customer_id );
	}

	static function getCountSuspendedOrders( $customer_id = -1) {
		return Orders::getCountOrdersByStatus( Orders::$ORDER_SUSPENDED, $customer_id );
	}*/

	static function getCountOrdersByStatus( $status = 'PENDING', $customer_id = -1 ) {
		global $wpdb;
		$sql = 'select count(*) from ' . $wpdb->prefix . 'tcp_orders where 1=1';
		if ( $status != '' ) $sql .= ' and status=%s';
		if ( ! empty( $customer_id ) && ( $customer_id > -1 ) ) $sql .= ' and customer_id = %d';
		$sql = $wpdb->prepare( $sql, $status, $customer_id );
		$sql = apply_filters( 'get_count_orders_by_status_sql', $sql, $status , $customer_id );
		return $wpdb->get_var( $sql );
	}

	/**
	 * Returns a join between orders and details orders
	 */
	static function getOrders( $status = 'PENDING', $customer_id = -1 ) {
		global $wpdb;
		$sql = 'select o.order_id, od.order_detail_id, shipping_firstname,
				shipping_lastname, created_at, customer_id, status, post_id, price, tax,
				qty_ordered, shipping_method, shipping_amount, discount_amount, payment_name,
				payment_method, payment_amount, transaction_id, order_currency_code,
				code_tracking, is_downloadable, max_downloads, expires_at, billing_email
				from ' . $wpdb->prefix . 'tcp_orders o left join ' .
				$wpdb->prefix . 'tcp_orders_details od on o.order_id = od.order_id where 1=1';
		if ( strlen( $status ) > 0 ) $sql .= $wpdb->prepare( ' and status = %s', $status );
		if ( ! empty( $customer_id ) && ( $customer_id > -1 ) ) $wpdb->prepare( ' and customer_id = %d', $customer_id );
		$sql .= ' order by created_at desc';
		return $wpdb->get_results( $sql );
	}

	static function getOrdersEx( $paged, $per_page = 20, $status = 'PENDING', $customer_id = -1 ) {
		global $wpdb;
		$sql = 'select order_id, shipping_firstname, shipping_lastname, created_at, customer_id,
				status, shipping_method, shipping_amount, discount_amount, payment_name,
				payment_method, payment_amount, transaction_id, order_currency_code,
				code_tracking, billing_email
				from ' . $wpdb->prefix . 'tcp_orders where 1=1';
		if ( strlen( $status ) > 0 ) $sql .= $wpdb->prepare( ' and status = %s', $status );
		if ( $customer_id > -1 ) $sql .= $wpdb->prepare( ' and customer_id = %d', $customer_id );
		$sql .= ' order by created_at desc' . $wpdb->prepare( ' limit %d, %d', ($paged-1) * $per_page, $per_page );
		$sql = apply_filters( 'get_orders_ex_sql', $sql, $paged, $per_page, $status, $customer_id );
		return $wpdb->get_results( $sql );
	}

	static function getOrderByTransactionId( $payment_method, $transaction_id ) {
		global $wpdb;
		$sql = 'select * from ' . $wpdb->prefix . 'tcp_orders where payment_method = %s and transaction_id = %s';
		return $wpdb->get_row( $wpdb->prepare( $sql, $payment_method, $transaction_id ) );
	}

	static function quickEdit( $order_id, $new_status, $new_code_tracking ) {
		global $wpdb;
		$wpdb->update( $wpdb->prefix . 'tcp_orders',
			array(
				'status'		=> $new_status,
				'code_tracking'	=> $new_code_tracking,
			),
			array(
				'order_id'		=> $order_id,
			), 
			array( '%s', '%s', '%s' ), array( '%d' ) );
		Orders::edit_downloadable_details( $order_id, $new_status );
	}

	static function edit( $order_id, $new_status, $new_code_tracking, $comment, $comment_internal ) {
		global $wpdb;
		$wpdb->update( $wpdb->prefix . 'tcp_orders',
			array(
				'status'			=> $new_status,
				'code_tracking'		=> $new_code_tracking,
				'comment'			=> $comment,
				'comment_internal'	=> $comment_internal,
			),
			array(
				'order_id'		=> $order_id,
			), 
			array( '%s', '%s', '%s', '%s', ), array( '%d' ) );
		Orders::edit_downloadable_details( $order_id, $new_status );
	}

	static function editStatus( $order_id, $new_status, $transaction_id = '', $comment_internal = '' ) {
		global $wpdb;
		$wpdb->update( $wpdb->prefix . 'tcp_orders',
			array(
				'status'			=> $new_status,
				'transaction_id'	=> $transaction_id,
				'comment_internal'	=> $comment_internal,
			),
			array(
				'order_id'		=> $order_id,
			), 
			array( '%s', '%s', '%s' ), array( '%d' ) );
		Orders::edit_downloadable_details( $order_id, $new_status );
	}

	static function getStatus( $order_id ) {
		global $wpdb;
		$sql = "select status from {$wpdb->prefix}tcp_orders where order_id = %d";
		return $wpdb->get_var( $wpdb->prepare( $sql, $order_id ) );
	}

	static function edit_downloadable_details( $order_id, $new_status ) {
		$completed = tcp_get_completed_order_status();
		if ( $new_status == $completed ) {
			$details = OrdersDetails::getDetails( $order_id );
			foreach( $details as $detail ) {
				$days_to_expire = get_post_meta( $detail->post_id, 'tcp_days_to_expire', true );
				if ( $days_to_expire > 0 ) {
					$today = date( 'Y-m-d' );
					$expires_at = date ( 'Y-m-d', strtotime( date( 'Y-m-d', strtotime( $today ) ) . " +$days_to_expire day" ) );
					OrdersDetails::edit_downloadable_data( $detail->order_detail_id, $expires_at );
				}
			}
		}
	}

	static function isDownloadable( $order_id ) {
		return OrdersDetails::isDownloadable( $order_id );
	}

	static function getProductsDownloadables( $customer_id ) {
		global $wpdb;
		$completed = tcp_get_completed_order_status();
		$today = date ( 'Y-m-d' );
		$max_date = date ( 'Y-m-d', mktime( 0, 0, 0, 1, 1, 2000 ) );
		$sql = $wpdb->prepare( 'select o.order_id as order_id, order_detail_id, post_id, expires_at, max_downloads from 
			' . $wpdb->prefix . 'tcp_orders o left join ' . $wpdb->prefix . 'tcp_orders_details d 
			on o.order_id = d.order_id
			where customer_id = %d and d.is_downloadable = \'Y\' and status=%s
			and ( ( d.expires_at > %s and ( d.max_downloads = -1 or d.max_downloads > 0 ) )
				or ( d.expires_at = %s and ( d.max_downloads > 0 or d.max_downloads = -1 ) ) )'
			, $customer_id, $completed, $today, $max_date );
		return $wpdb->get_results( $sql );
	}

	static function isProductDownloadable( $customer_id, $orders_details_id ) {
		global $wpdb;
		$completed = tcp_get_completed_order_status();
		$today = date ( 'Y-m-d' );
		$max_date = date ( 'Y-m-d', mktime( 0, 0, 0, 1, 1, 2000 ) );
		$sql = $wpdb->prepare( 'select count(*) from
			' . $wpdb->prefix . 'tcp_orders o left join ' . $wpdb->prefix . 'tcp_orders_details d
			on o.order_id = d.order_id
			where customer_id = %d and order_detail_id = %d and d.is_downloadable = \'Y \' and status=%s
			and ( ( d.expires_at > %s and ( d.max_downloads = -1 or d.max_downloads > 0 ) )
				or ( d.expires_at = %s and ( d.max_downloads > 0 or d.max_downloads = -1 ) ) )'
			, $customer_id, $orders_details_id, $completed, $today, $max_date );
		$count = $wpdb->get_var( $sql );
		return $count > 0;
	}

	static function takeAwayDownload( $order_detail_id ) {
		global $wpdb;

		$sql = 'update ' . $wpdb->prefix . 'tcp_orders_details set 
			max_downloads = max_downloads - 1 where order_detail_id = %d and max_downloads > 0';
		$wpdb->query( $wpdb->prepare( $sql, $order_detail_id ) );
	}

	static function removeSpaces( $postcode ) {
		return str_replace( ' ', '', $postcode );
	}

	/**
	 * Returns the lastest orders
	 */
	static function getLastOrders( $limit = 5, $status = '', $customer_id = -1 ) {
		global $wpdb;
		$sql = 'select order_id, billing_firstname,
				billing_lastname, created_at, customer_id, status, billing_email, billing_country
				from ' . $wpdb->prefix . 'tcp_orders where 1=1 ';
		if ( strlen( $status ) > 0 ) $sql .= $wpdb->prepare( ' and status = %s', $status );
		if ( $customer_id > -1 ) $sql .= $wpdb->prepare( ' and customer_id = %d', $customer_id );		
		$sql .= $wpdb->prepare(' order by created_at desc limit 0, %d', $limit );
		return $wpdb->get_results( $sql );
	}

	/**
	 * @author: Joy Reynolds and TheCartPress team
	 */
	static function getCounts( $status = '', $days_prev = 7, $customer_id = -1 ) {
		global $wpdb;

		$sql = 'SELECT DATE(created_at) AS thedate, SUM(payment_amount) AS sales, SUM(1) AS count FROM ' . $wpdb->prefix . 'tcp_orders';
		$sql .= ' WHERE DATE_SUB( NOW(), INTERVAL %d DAY)  <= created_at';
		if ( $status != '' ) $sql .= $wpdb->prepare( ' AND status = %s', $status );
		if ( $customer_id > -1 ) $sql .= $wpdb->prepare( ' AND customer_id = %d', $customer_id );
		$sql .= ' GROUP BY DATE(created_at)';
		$sql = $wpdb->prepare( $sql, $days_prev );
		$sql = apply_filters( 'get_counts_sql', $sql, $status, $days_prev, $customer_id );
		return $wpdb->get_results( $sql );
	}

	static function getAmountByDay( $date, $status = '' ) {
		global $wpdb;

		$tomorrow = date( 'Y-m-d', strtotime( $date . ' +1 day' ) );
		$sql = $wpdb->prepare( 'select order_id from ' . $wpdb->prefix . 'tcp_orders where created_at > %s and created_at < %s', $date, $tomorrow );
		if ( $status != '' ) $sql .= $wpdb->prepare( ' AND status = %s', $status );
		$orders = $wpdb->get_results( $sql );
		$amount = 0;
		foreach( $orders as $order ) {
			$amount += Orders::getTotal( $order->order_id );
		}
		return $amount;
	}

}
?>
