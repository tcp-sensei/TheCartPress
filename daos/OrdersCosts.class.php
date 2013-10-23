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

class OrdersCosts {

	static function createTable() {
		global $wpdb;
		$sql = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . 'tcp_orders_costs` (
		  `order_cost_id`		bigint(20) unsigned NOT NULL auto_increment,
		  `order_id`			bigint(20) unsigned NOT NULL,
		  `description`			varchar(255)		NOT NULL,
		  `cost`				decimal(13,2)		NOT NULL default 0,
		  `tax`					float UNSIGNED		NOT NULL default 0,
		  `cost_order`			varchar(4) 			NOT NULL default \'\',
		  PRIMARY KEY  (`order_cost_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
		$wpdb->query( $sql );
	}

	static function get( $order_cost_id ) {
		global $wpdb;
		return $wpdb->get_row( $wpdb->prepare( 'select * from ' . $wpdb->prefix . 'tcp_orders_costs where order_cost_id = %d', $order_cost_id ) );
	}

	static function getCosts( $order_id ) {
		global $wpdb;
		return $wpdb->get_results( $wpdb->prepare( 'select * from ' . $wpdb->prefix . 'tcp_orders_costs where order_id = %d order by cost_order', $order_id ) );
	}

	/**
	 * Returns the total costs associated to an order
	 *
	 * @param int $order_id, id of the order
	 * @param double $total, inital total
	 */
	static function getTotalCost( $order_id, $total = 0 ) {
		global $wpdb;
		$res = $wpdb->get_results( $wpdb->prepare( 'select order_cost_id, cost, tax from ' . $wpdb->prefix . 'tcp_orders_costs where order_id = %d', $order_id ) );
		foreach( $res as $row ) {
			if ( $row->tax > 0 ) {
				$total += $row->cost + $row->cost * $row->tax / 100;
			} else {
				$total += $row->cost;
			}
		}
		return $total;
	}

	static function getTotalDetailed( $order_id ) {
		$detailed = array(
			'amount'	=> 0,
			'tax'		=> 0,
		);
		global $wpdb;
		$res = $wpdb->get_results( $wpdb->prepare( 'select order_cost_id, cost, tax from ' . $wpdb->prefix . 'tcp_orders_costs where order_id = %d', $order_id ) );
		foreach( $res as $row ) {
			$detailed['amount'] += $row->cost;
			if ( $row->tax > 0 ) {
				$detailed['tax'] += $detailed['amount'] * $row->tax / 100;
			}
		}
		return $detailed;
	}

	static function insert( $ordersCosts ) {
		global $wpdb;
		$wpdb->insert( $wpdb->prefix . 'tcp_orders_costs', array (
				'order_id'		=> $ordersCosts['order_id'],
				'description'	=> $ordersCosts['description'],
				'cost'			=> $ordersCosts['cost'],
				'tax'			=> $ordersCosts['tax'],
				'cost_order'	=> $ordersCosts['cost_order'],
			),
			array( '%d', '%s', '%f', '%f', '%s' )
		);
		return $wpdb->insert_id;
	}

	static function delete_by_order_id( $order_id ) {
		global $wpdb;
		$sql = 'delete from ' . $wpdb->prefix . 'tcp_orders_costs where ';
		$sql .= $wpdb->prepare( 'order_id = %d', $order_id);
		return $wpdb->query( $sql );
	}

	static function get_orders_costs_ids_by_order_id( $order_id ) {
		global $wpdb;
		$sql = 'select order_cost_id from ' . $wpdb->prefix . 'tcp_orders_costs where ';
		$sql .= $wpdb->prepare( 'order_id = %d', $order_id);
		return $wpdb->get_results( $sql );
	}
}
?>