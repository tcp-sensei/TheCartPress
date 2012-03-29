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

class TaxRates {

	static function createTable() {
		global $wpdb;
		$sql = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . 'tcp_tax_rates` (
		  `tax_rate_id`		bigint(20)		unsigned NOT NULL auto_increment,
		  `country_iso`		char(3)			NOT NULL,
  		  `region_id`		char(4)			NOT NULL,
  		  `region`			varchar(100)	NOT NULL,
  		  `post_code`		varchar(255)	NOT NULL,
  		  `tax_id`			bigint(20)		NOT NULL,
  		  `rate`			float			NOT NULL,
  		  `label`			varchar(100)	NOT NULL,
		  PRIMARY KEY (`tax_rate_id`)
		) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;';
		$wpdb->query( $sql );
	}

	static function initData() {
		global $wpdb;
		$count = $wpdb->get_var( 'select count(*) from ' . $wpdb->prefix . 'tcp_tax_rates' );
		if ( $count == 0 ) {
			$sql = 'INSERT INTO `' . $wpdb->prefix . 'tcp_tax_rates` VALUES  (0, \'all\', \'all\', \'\', \'all\', 0, 0, \'' . __( 'No tax', 'tcp' ) . '\')';
			$wpdb->query( $sql );
		}
	}

	/**
	 * Returns the tax data by id
	 */
	static function getAll() {
		global $wpdb;
		$sql = 'select * from ' . $wpdb->prefix . 'tcp_tax_rates order by country_iso, region_id, post_code, tax_id';
		return $wpdb->get_results($sql);
	}

	/**
	 * Returns the tax data by id
	 */
	static function get( $tax_rate_id ) {
		global $wpdb;
		return $wpdb->get_row( $wpdb->prepare( 'select * from '. $wpdb->prefix . 'tcp_tax_rates where tax_rate_id = %d', $tax_rate_id ) );
	}

	static function find( $country_iso, $region_id, $post_code, $tax_id ) {
		global $wpdb;
		$sql = 'select rate, label from '. $wpdb->prefix . 'tcp_tax_rates where country_iso = %s and region_id=%s and post_code=%s and tax_id = %d';
		$row = $wpdb->get_row( $wpdb->prepare( $sql, $country_iso, $region_id, $post_code, $tax_id ) );
//TODO Only for debug pourpose
//echo $country_iso, ', ', $region_id, ', ', $post_code, ', ', $tax_id, '<br>';
//echo '1-';var_dump( $row ); echo '<br>';
		if ( $row ) return $row;
		$row = $wpdb->get_row( $wpdb->prepare( $sql, $country_iso, $region_id, $post_code, -1 ) );
//echo '2-';var_dump( $row ); echo '<br>';
		if ( $row ) return $row;
		$row = $wpdb->get_row( $wpdb->prepare( $sql, $country_iso, $region_id, 'all', $tax_id ) );
//echo '3-';var_dump( $row ); echo '<br>';
		if ( $row ) return $row;
		$row = $wpdb->get_row( $wpdb->prepare( $sql, $country_iso, $region_id, 'all', -1 ) );
//echo '4-';var_dump( $row ); echo '<br>';
		if ( $row ) return $row;
		$row = $wpdb->get_row( $wpdb->prepare( $sql, $country_iso, 'all', 'all', $tax_id ) );
//echo '5-';var_dump( $row ); echo '<br>';
		if ( $row ) return $row;
		$row = $wpdb->get_row( $wpdb->prepare( $sql, $country_iso, 'all', 'all', -1 ) );
//echo '6-';var_dump( $row ); echo '<br>';
		if ( $row ) return $row;
		$row = $wpdb->get_row( $wpdb->prepare( $sql, 'all', 'all', 'all', $tax_id ) );
//echo '7-';var_dump( $row ); echo '<br>';
		if ( $row ) return $row;
		$row = $wpdb->get_row( $wpdb->prepare( $sql, 'all', 'all', 'all', -1 ) );
//echo '8-';var_dump( $row ); echo '<br>', $wpdb->prepare( $sql, 'all', 'all', 'all', -1 ), '<br>';
		if ( $row ) return $row;
		return false;
	}

	static function save( $tax ) {
		global $wpdb;
		if ( ! isset( $tax['tax_rate_id'] ) )
			$tax['tax_rate_id'] = 0;
		else
			$tax['tax_rate_id'] = (int)$tax['tax_rate_id'];
		if ( $tax['tax_rate_id'] > 0 )
			return Taxes::update( $tax );
		else
			return Taxes::insert( $tax );
	}

	static function insert( $tax_rate ) {
		global $wpdb;
		$wpdb->insert( $wpdb->prefix . 'tcp_tax_rates',
			array(
				'country_iso'	=> $tax_rate['country_iso'],
				'region_id'		=> $tax_rate['region_id'],
				'region'		=> $tax_rate['region'],
				'post_code'		=> $tax_rate['post_code'],
				'tax_id'		=> $tax_rate['tax_id'],
				'rate'			=> $tax_rate['rate'],
				'label'			=> $tax_rate['label'],
			),
			array( '%s', '%s', '%s', '%s', '%d', '%f', '%s' ) );
		return $wpdb->insert_id;
	}

	static function update( $tax ) {
		global $wpdb;
		$wpdb->update( $wpdb->prefix . 'tcp_tax_rates',
			array(
				'country_iso'	=> $tax_rate['country_iso'],
				'region_id'		=> $tax_rate['region_id'],
				'region'		=> $tax_rate['region'],
				'post_code'		=> $tax_rate['post_code'],
				'tax_id'		=> $tax_rate['tax_id'],
				'rate'			=> $tax_rate['rate'],
				'label'			=> $tax_rate['label'],
			),
			array(
				'tax_rate_id'	=> $tax['tax_rate_id'],
			),
			array( '%s', '%s', '%s', '%s', '%d', '%f', '%s' ),
			array( '%d' )
		);
	}

	static function delete( $tax_rate_id ) {
		global $wpdb;
		$wpdb->query( $wpdb->prepare( 'delete from '. $wpdb->prefix . 'tcp_tax_rates where tax_rate_id = %d' , $tax_rate_id ) );
	}
}
?>
