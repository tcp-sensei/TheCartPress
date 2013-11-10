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

class OrdersDetails {

	static function createTable() {
		global $wpdb;
		$sql = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . 'tcp_orders_details` (
			`order_detail_id`	bigint(20) unsigned NOT NULL auto_increment,
			`order_id`			bigint(20) unsigned NOT NULL,
			`post_id`			bigint(20) unsigned NOT NULL,
			`option_1_id`		bigint(20) unsigned NOT NULL,
			`option_2_id`		bigint(20) unsigned NOT NULL,
			`weight`			double				NOT NULL default 0,
			`is_downloadable`	char(1)				NOT NULL COMMENT \'Y->yes\',
			`sku`				varchar(50)			NOT NULL,
			`name`				varchar(255)		NOT NULL,
			`option_1_name`		varchar(255)		NOT NULL,
			`option_2_name`		varchar(255)		NOT NULL,
			`price`				decimal(13,2)		NOT NULL default 0,
			`original_price`	decimal(13,2)		NOT NULL default 0,
			`tax`				double				NOT NULL default 0,
			`qty_ordered`		int(11) unsigned	NOT NULL default 0,
			`max_downloads`		int(4)				NOT NULL default 10,
			`expires_at`		date				NOT NULL,
			PRIMARY KEY  (`order_detail_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
		$wpdb->query( $sql );
	}

	static function get( $order_details_id ) {
		global $wpdb;
		return $wpdb->get_row( $wpdb->prepare( 'select * from ' . $wpdb->prefix . 'tcp_orders_details where order_detail_id = %d', $order_details_id ) );
	}

	static function getDetails( $order_id ) {
		global $wpdb;
		$sql = $wpdb->prepare( 'select * from ' . $wpdb->prefix . 'tcp_orders_details where order_id = %d', $order_id );
		$sql = apply_filters( 'get_details_sql', $sql, $order_id );
		return $wpdb->get_results( $sql );
	}

	static function getTotal( $order_id, $total = 0 ) {
		$decimals = tcp_get_decimal_currency();
		global $wpdb;
		$res =  $wpdb->get_results( $wpdb->prepare( 'select order_detail_id, price, tax, qty_ordered from ' . $wpdb->prefix . 'tcp_orders_details where order_id = %d', $order_id ) );
		foreach( $res as $row ) {
			if ( $row->tax == 0 ) {
				$total += $row->price * $row->qty_ordered;
			} else {
				//$t = $row->price * $row->tax / 100;
				////$t = ( $row->price + round( $t, $decimals ) ) * $row->qty_ordered;
				//$t = ( $row->price + $t ) * $row->qty_ordered;
				//$total += $t;
				$tax = round( $row->price * $row->tax / 100, $decimals );
				$total += ( $row->price + $tax ) * $row->qty_ordered;
			}
		}
		return round( $total, $decimals );
	}

	/**
	 * Returns the total weight of an order
	 *
	 * @param $order_id
	 * @param $weight, initial weight
	 * @since 1.3.2
	 */
	static function getTotalWeight( $order_id, $weight = 0 ) {
		global $wpdb;
		$res =  $wpdb->get_results( $wpdb->prepare( 'select weight, qty_ordered from ' . $wpdb->prefix . 'tcp_orders_details where order_id = %d', $order_id ) );
		foreach( $res as $row ) {
			$weight += $row->weight * $row->qty_ordered;
		}
		return $weight;
	}

	static function getTotalDetailed( $order_id ) {
		$detailed = array(
			'amount'	=> 0,
			'tax'		=>0,
		);
		$decimals = tcp_get_decimal_currency();
		global $wpdb;
		$res =  $wpdb->get_results( $wpdb->prepare( 'select order_detail_id, price, tax, qty_ordered from ' . $wpdb->prefix . 'tcp_orders_details where order_id = %d', $order_id ) );
		foreach( $res as $row ) {
			$detailed['amount'] += $row->price * $row->qty_ordered;
			if ( $row->tax > 0 ) {
				$detailed['tax'] += $detailed['amount'] * $row->tax / 100;
			}
		}
		return $detailed;
	}

	static function insert( $ordersDetails ) {
		global $wpdb;
		$wpdb->insert( $wpdb->prefix . 'tcp_orders_details', array (
				'order_id'			=> $ordersDetails['order_id'],
				'post_id'			=> $ordersDetails['post_id'],
				'option_1_id'		=> $ordersDetails['option_1_id'],
				'option_2_id'		=> $ordersDetails['option_2_id'],
				'weight'			=> $ordersDetails['weight'],
				'is_downloadable'	=> $ordersDetails['is_downloadable'],
				'sku'				=> $ordersDetails['sku'],
				'name'				=> $ordersDetails['name'],
				'option_1_name'		=> $ordersDetails['option_1_name'],
				'option_2_name'		=> $ordersDetails['option_2_name'],
				'price'				=> $ordersDetails['price'],
				'original_price'	=> $ordersDetails['original_price'],
				'tax'				=> $ordersDetails['tax'],
				'qty_ordered'		=> $ordersDetails['qty_ordered'],
				'max_downloads'		=> $ordersDetails['max_downloads'],
				'expires_at'		=> $ordersDetails['expires_at'],
			),
			array( '%d', '%d', '%d', '%d', '%f', '%s', '%s', '%s', '%s', '%s', '%f', '%f', '%f', '%d', '%d', '%s' )
		);
		return $wpdb->insert_id;
	}

	static function edit_downloadable_data( $order_detail_id, $expires_at ) {
		global $wpdb;
		$wpdb->update( $wpdb->prefix . 'tcp_orders_details', array (
				'expires_at'		=> $expires_at,
			),
			array (
				'order_detail_id'	=> $order_detail_id,
			),
			array( '%s' ),
			array( '%d' )
		);
	}

	static function isDownloadable( $order_id ) {
		global $wpdb;
		$count = $wpdb->get_var( $wpdb->prepare( 'select count(*) from ' . $wpdb->prefix . 'tcp_orders_details
			where order_id = %d and is_downloadable = \'Y \'', $order_id ) );
		return $count == 0;
	}

	static function getCrossSelling( $post_id ) {
		global $wpdb;
		$sql =  'select odd.post_id as id, sum(odd.qty_ordered) as count from (' . $wpdb->prefix . 'tcp_orders_details od';
		$sql .= ' inner join ' . $wpdb->prefix . 'tcp_orders o';
		$sql .= ' on od.order_id = o.order_id ) inner join ' . $wpdb->prefix . 'tcp_orders_details odd';
		$sql .= ' on o.order_id = odd.order_id';
		$sql .= $wpdb->prepare( ' where od.post_id = %d', $post_id );
		$sql .= $wpdb->prepare( ' and o.status = %s', tcp_get_completed_order_status() );
		$sql .= ' group by odd.post_id order by count';
		return $wpdb->get_results( $sql );
	}

	static function delete_by_order_id( $order_id ) {
		global $wpdb;
		$sql = 'delete from ' . $wpdb->prefix . 'tcp_orders_details where ';
		$sql .= $wpdb->prepare( 'order_id = %d', $order_id);
		return $wpdb->query( $sql );
	}

	static function get_orders_details_ids_by_order_id( $order_id ) {
		global $wpdb;
		$sql = 'select order_detail_id from ' . $wpdb->prefix . 'tcp_orders_details where ';
		$sql .= $wpdb->prepare( 'order_id = %d', $order_id);
		return $wpdb->get_results( $sql );
	}

	static function get_product_total_sales( $post_id ) {
		global $wpdb;
		$sql = 'select sum( od.qty_ordered ) from ' . $wpdb->prefix . 'tcp_orders_details od left join ' . $wpdb->prefix . 'tcp_orders o on od.order_id = o.order_id';
		$sql .= $wpdb->prepare( ' and o.status = %s', Orders::$ORDER_COMPLETED);
		$sql .= $wpdb->prepare( ' where od.post_id = %d', $post_id);
		return $wpdb->get_var( $sql );	
	}
}
?>