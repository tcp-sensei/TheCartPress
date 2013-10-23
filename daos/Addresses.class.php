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
 * along with This program.  If not, see <http://www.gnu.org/licenses/>.
 */

class Addresses {

	static function createTable() {
		global $wpdb;
		$sql = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . 'tcp_addresses` (
			`address_id`		bigint(20) unsigned NOT NULL auto_increment,
			`customer_id`		bigint(20) unsigned NOT NULL,
			`custom_id`			bigint(20) unsigned NOT NULL,
			`default_shipping`	char(1)			NOT NULL COMMENT \'Y->yes\',
			`default_billing`	char(1)			NOT NULL COMMENT \'Y->yes\',
			`name`				varchar(250)	NOT NULL,
			`firstname`			varchar(50)		NOT NULL,
			`lastname`			varchar(100)	NOT NULL,
			`company`			varchar(50)		NOT NULL,
			`tax_id_number`		varchar(30)		NOT NULL,
			`company_id`		varchar(30)		NOT NULL,
			`street`			varchar(255)	NOT NULL,
			`street_2`			varchar(255)	NOT NULL,
			`city`				varchar(100)	NOT NULL,
			`city_id`			char(4)			NOT NULL,
			`region`			varchar(100)	NOT NULL,
			`region_id`			char(2)			NOT NULL,
			`postcode`			char(10)		NOT NULL,
			`country_id`		char(2)			NOT NULL,
			`telephone_1`		varchar(50)		NOT NULL,
			`telephone_2`		varchar(50)		NOT NULL,
			`fax`				varchar(50)		NOT NULL,
			`email`				varchar(50)		NOT NULL,
			PRIMARY KEY  (`address_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT=\'Addresses\';';
		$wpdb->query( $sql );
	}

	static function getCustomerAddresses( $customer_id = false ) {
		global $wpdb;
		$sql = 'select * from ' . $wpdb->prefix . 'tcp_addresses';
		if ( $customer_id !== false ) $sql .= $wpdb->prepare( ' where customer_id = %d', $customer_id );
		return $wpdb->get_results( $sql );
	}

	static function isOwner( $address_id, $customer_id ) {
		global $wpdb;
		$count = $wpdb->get_var( $wpdb->prepare( 'select count(*) from ' . $wpdb->prefix . 'tcp_addresses
			where address_id = %d and customer_id = %d', $address_id, $customer_id ) );
		return $count == 1;
	}

	/**
	 * Returns the address data by id
	 */
	static function get( $address_id ) {
		global $wpdb;
		return $wpdb->get_row( $wpdb->prepare( 'select * from ' . $wpdb->prefix . 'tcp_addresses
			where address_id = %d', $address_id ) );
	}

	/**
	 * Returns the country_id data by id
	 */
	static function getCountryId( $address_id ) {
		global $wpdb;
		return $wpdb->get_var( $wpdb->prepare( 'select country_id from ' . $wpdb->prefix . 'tcp_addresses
			where address_id = %d', $address_id ) );
	}

	static function setDefaultShipping( $customer_id, $address_id ) {
		global $wpdb;
		$wpdb->query( $wpdb->prepare( 'update ' .  $wpdb->prefix . 'tcp_addresses
				set default_shipping = \'\'	where customer_id = %d', $customer_id ) );
		$wpdb->query( $wpdb->prepare( 'update '.  $wpdb->prefix.'tcp_addresses
			set default_shipping = \'Y\' where address_id = %d', $address_id ) );
	}

	static function setDefaultBilling( $customer_id, $address_id ) {
		global $wpdb;
		$wpdb->query( $wpdb->prepare( 'update ' .  $wpdb->prefix . 'tcp_addresses
			set default_billing = \'\' where customer_id = %d', $customer_id ) );
		$wpdb->query( $wpdb->prepare( 'update ' .  $wpdb->prefix.'tcp_addresses
			set default_billing = \'Y\' where address_id = %d', $address_id ) );
	}

	static function getCustomerDefaultShippingAddressId( $customer_id ) {
		global $wpdb;
		return $wpdb->get_var( $wpdb->prepare( 'select address_id from ' . $wpdb->prefix . 'tcp_addresses
			where customer_id = %d and default_shipping = \'Y\'', $customer_id ) );
	}

	static function getCustomerDefaultShippingAddress( $customer_id ) {
		global $wpdb;
		return $wpdb->get_row( $wpdb->prepare( 'select * from ' . $wpdb->prefix . 'tcp_addresses
			where customer_id = %d and default_shipping = \'Y\'', $customer_id ) );
	}

	static function getCustomerDefaultShippingAddresses( $customer_id = false ) {
		global $wpdb;
		$sql = 'select * from ' . $wpdb->prefix . 'tcp_addresses where default_shipping = \'Y\'';
		if ( $customer_id !== false ) $sql .= $wpdb->prepare( ' and customer_id = %d', $customer_id );
		return $wpdb->get_results( $sql );
	}

	static function getCustomerDefaultBillingAddressId( $customer_id ) {
		global $wpdb;
		return $wpdb->get_var( $wpdb->prepare( 'select address_id from ' . $wpdb->prefix . 'tcp_addresses
			where customer_id = %d and default_billing = \'Y\'', $customer_id ) );
	}

	static function getCustomerDefaultBillingAddress( $customer_id ) {
		global $wpdb;
		return $wpdb->get_row( $wpdb->prepare( 'select * from ' . $wpdb->prefix . 'tcp_addresses
			where customer_id = %d and default_billing = \'Y\'', $customer_id ) );
	}

	static function getCustomerDefaultBillingAddresses( $customer_id = false ) {
		global $wpdb;
		$sql = 'select * from ' . $wpdb->prefix . 'tcp_addresses where default_billing = \'Y\'';
		if ( $customer_id !== false ) $sql .= $wpdb->prepare( ' and customer_id = %d', $customer_id );
		return $wpdb->get_results(  $sql );
	}

	static function save( $address ) {
		if ( ! isset( $address['address_id'] ) ) 
			$address['address_id'] = 0;
		else
			$address['address_id'] = (int)$address['address_id'];
		if ( $address['address_id'] > 0 )
			return Addresses::update( $address );
		else
			return Addresses::insert( $address );
	}

	static function insert( $address ) {
		global $wpdb;
		$wpdb->insert( $wpdb->prefix . 'tcp_addresses', array(
				'customer_id'		=> $address['customer_id'],
				'custom_id'			=> isset( $address['custom_id'] ) ? $address['custom_id'] : 0,
				'default_shipping'	=> $address['default_shipping'],
				'default_billing'	=> $address['default_billing'],
				'name'				=> isset( $address['address_name'] ) ? $address['address_name'] : '',
				'firstname'			=> $address['firstname'],
				'lastname'			=> $address['lastname'],
				'company'			=> $address['company'],
				'tax_id_number'		=> isset( $address['tax_id_number'] ) ? $address['tax_id_number'] : '',
				'company_id'		=> isset( $address['company_id'] ) ? $address['company_id'] : 0,
				'street'			=> $address['street'],
				'street_2'			=> isset( $address['street_2'] ) ? $address['street_2'] : '',
				'city'				=> $address['city'],
				'city_id'			=> $address['city_id'],
				'region'			=> $address['region'],
				'region_id'			=> $address['region_id'],
				'postcode'			=> $address['postcode'],
				'country_id'		=> $address['country_id'],
				'telephone_1'		=> $address['telephone_1'],
				'telephone_2'		=> $address['telephone_2'],
				'fax'				=> $address['fax'],
				'email'				=> $address['email'],
			),
			array ( '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s','%s', '%s',  '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
		);
		return $wpdb->insert_id;
	}

	static public function update( $address ) {
		global $wpdb;
		$wpdb->update( $wpdb->prefix . 'tcp_addresses', array(
				'customer_id'		=> $address['customer_id'],
				'custom_id'			=> isset( $address['custom_id'] ) ? $address['custom_id'] : 0,
				'default_shipping'	=> $address['default_shipping'],
				'default_billing'	=> $address['default_billing'],
				'name'				=> $address['address_name'],
				'firstname'			=> $address['firstname'],
				'lastname'			=> $address['lastname'],
				'company'			=> $address['company'],
				'tax_id_number'		=> $address['tax_id_number'],
				'company_id'		=> isset( $address['company_id'] ) ? $address['company_id'] : 0,
				'street'			=> $address['street'],
				'street_2'			=> isset( $address['street_2'] ) ? $address['street_2'] : '',
				'city'				=> $address['city'],
				'city_id'			=> $address['city_id'],
				'region'			=> $address['region'],
				'region_id'			=> $address['region_id'],
				'postcode'			=> $address['postcode'],
				'country_id'		=> $address['country_id'],
				'telephone_1'		=> $address['telephone_1'],
				'telephone_2'		=> $address['telephone_2'],
				'fax'				=> $address['fax'],
				'email'				=> $address['email'],
			),
			array( 'address_id' =>  $address['address_id']),
			array ( '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s','%s', '%s', '%s', '%s', '%s' ),
			array ( '%d' )
		);
		return $wpdb->insert_id;
	}

	static public function delete( $address_id ) {
		global $wpdb;
		$wpdb->query( $wpdb->prepare( 'delete from ' . $wpdb->prefix . 'tcp_addresses where address_id = %d', $address_id ) );
	}
}
