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

class OrdersCostsMeta {

	static function createTable() {
		global $wpdb;
		$table_name = 'tcp_orders_costsmeta';
		$sql = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . $table_name . '` (
			`meta_id` bigint(20) UNSIGNED NOT NULL auto_increment,
			`tcp_orders_costs_id` bigint(20) UNSIGNED NOT NULL,
			`meta_key` varchar(255),
			`meta_value` longtext,
			PRIMARY KEY (`meta_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
		$wpdb->query( $sql );
	}

	static function delete_by_order_id( $order_id ) {
		global $wpdb;
		$sql = 'delete from ' . $wpdb->tcp_orders_costsmeta . ' where ';
		$tcp_orders_costs_ids = OrdersCosts::get_orders_costs_ids_by_order_id( $order_id );
		foreach( $tcp_orders_costs_ids as $tcp_orders_costs_id ) {
			$wpdb->query( $sql . $wpdb->prepare( 'tcp_orders_costs_id = %d', $tcp_orders_costs_id ) );
		}
	}
}
?>
