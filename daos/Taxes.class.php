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

class Taxes {

	static function createTable() {
		global $wpdb;
		$sql = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . 'tcp_taxes` (
		  `tax_id`		bigint(20)		unsigned NOT NULL auto_increment,
		  `title`		varchar(100)	NOT NULL,
  		  `desc`		varchar(255)	NOT NULL,
		  PRIMARY KEY (`tax_id`)
		) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;';
		$wpdb->query( $sql );
	}

	/**
	 * Returns the tax data by id
	 */
	static function getAll() {
		global $wpdb;
		$sql = 'select * from ' . $wpdb->prefix . 'tcp_taxes';
		return $wpdb->get_results($sql);
	}

	/**
	 * Returns the tax data by id
	 */
	static function get( $tax_id ) {
		global $wpdb;
		return $wpdb->get_row( $wpdb->prepare( 'select * from '. $wpdb->prefix . 'tcp_taxes where tax_id = %d', $tax_id ) );
	}

	/**
	 * Returns the tax data by id
	 */
/*	static function getTax( $tax_id ) {
		global $wpdb;
		return $wpdb->get_var( $wpbb->prepare( 'select tax from '. $wpdb->prefix . 'tcp_taxes where tax_id = %d', $tax_id ) );
	}*/

	static function save( $tax ) {
		global $wpdb;
		if ( ! isset( $tax['tax_id'] ) )
			$tax['tax_id'] = 0;
		else
			$tax['tax_id'] = (int)$tax['tax_id'];
		if ( $tax['tax_id'] > 0 )
			return Taxes::update( $tax );
		else
			return Taxes::insert( $tax );
	}

	static function insert( $tax ) {
		global $wpdb;
		$wpdb->insert( $wpdb->prefix . 'tcp_taxes',
			array(
				'title'	=> $tax['title'],
				'desc'	=> $tax['desc'],
			),
			array( '%s', '%s' ) );
		return $wpdb->insert_id;
	}

	static function update( $tax ) {
		global $wpdb;
		$wpdb->update( $wpdb->prefix . 'tcp_taxes',
			array(
				'title'	=> $tax['title'],
				'desc'	=> $tax['desc'],
			),
			array(
				'tax_id'	=> $tax['tax_id'],
			),
			array( '%s', '%s' ),
			array( '%d' )
		);
	}

	static function delete( $tax_id ) {
		global $wpdb;
		$wpdb->query( $wpdb->prepare( 'delete from '. $wpdb->prefix . 'tcp_taxes where tax_id = %d' , $tax_id ) );
	}
}
?>
