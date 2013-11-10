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

class RelEntities {

	static function createTable() {
		global $wpdb;
		$sql = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . 'tcp_rel_entities` (
			`id_from`		bigint(20) unsigned NOT NULL,
			`id_to`			bigint(20) unsigned NOT NULL,
			`rel_type`		varchar(20)			NOT NULL,
			`list_order`	int(4) unsigned		NOT NULL default 0,
			`meta_value`	longtext			NOT NULL,
			PRIMARY KEY (`id_to`,`id_from`,`rel_type`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
		$wpdb->query( $sql );
	}

	static function insert( $id_from, $id_to, $rel_type = 'GROUPED', $list_order = 0, $meta_value = '' ) {
		global $wpdb;
		$wpdb->insert( $wpdb->prefix . 'tcp_rel_entities', array(
			'id_from'		=> $id_from,
			'id_to'			=> $id_to,
			'rel_type'		=> $rel_type,
			'list_order'	=> $list_order,
			'meta_value'	=> serialize( $meta_value ) ),
			array( '%d', '%d', '%s', '%d', '%s' )
		);
	}

	static function update( $id_from, $id_to, $rel_type = 'GROUPED', $list_order = 0, $meta_value = '' ) {
		global $wpdb;
		$wpdb->update( $wpdb->prefix . 'tcp_rel_entities',
			array(
				'list_order'	=> $list_order,
				'meta_value'	=> serialize( $meta_value ),
			),
			array(
				'id_from'		=> $id_from,
				'id_to'			=> $id_to,
				'rel_type'		=> $rel_type,
			),
			array( '%d', '%s' ),
			array( '%d', '%d', '%s', )
		);
	}

	static function delete( $id_from, $id_to, $rel_type = 'GROUPED' ) {
		global $wpdb;
		$wpdb->query( $wpdb->prepare( 'delete from ' . $wpdb->prefix . 'tcp_rel_entities
				where id_from = %d and id_to = %d and rel_type = %s', $id_from, $id_to, $rel_type ) );
	}

	static function deleteAll( $id_from, $rel_type = 'GROUPED' ) {
		global $wpdb;
		$wpdb->query( $wpdb->prepare( 'delete from ' . $wpdb->prefix . 'tcp_rel_entities
				where id_from = %d and rel_type = %s', $id_from, $rel_type ) );
	}

	static function deleteAllRelations( $id_from ) {
		global $wpdb;
		$wpdb->query( $wpdb->prepare( 'delete from ' . $wpdb->prefix . 'tcp_rel_entities
				where id_from = %d', $id_from ) );
	}

	static function deleteAllTo( $id_to, $rel_type = 'GROUPED' ) {
		global $wpdb;
		$wpdb->query( $wpdb->prepare( 'delete from ' . $wpdb->prefix . 'tcp_rel_entities
				where id_to = %d and rel_type = %s', $id_to, $rel_type ) );
	}

	/**
	 * Returns number of children from an id_from
	 */
	static function count( $id_from, $rel_type = 'GROUPED' ) {
		global $wpdb;
		return $wpdb->get_var( $wpdb->prepare( 'select count(*) from ' . $wpdb->prefix . 'tcp_rel_entities 
				where id_from = %d and rel_type = %s', $id_from, $rel_type ) );
	}

	/**
	 * Returns children from an id_from
	 */
	static function select( $id_from, $rel_type = 'GROUPED' ) {
		global $wpdb;
		return $wpdb->get_results( $wpdb->prepare( 'select * from ' . $wpdb->prefix . 'tcp_rel_entities 
				where id_from = %d and rel_type = %s order by list_order ', $id_from, $rel_type ) );
	}

	/*static function getOptionsTree( $id_from ) {
		global $wpdb;
		$options = array();
		$options_1 = $wpdb->get_results( $wpdb->prepare( 'select * from ' . $wpdb->prefix . 'tcp_rel_entities 
			where id_from = %d and rel_type = %s order by list_order', $id_from, 'OPTIONS' ) );
		if ( is_array( $options_1 ) && count( $options_1 ) )
			foreach( $options_1 as $option_1 ) {
				$options_2 = $wpdb->get_results( $wpdb->prepare( 'select * from ' . $wpdb->prefix . 'tcp_rel_entities 
					where id_from = %d and rel_type = %s order by list_order', $option_1->id_to, 'OPTIONS' ) );
				if ( is_array( $options_2 ) && count( $options_2 ) ) {
					$options[$option_1->id_to] = array();
					foreach( $options_2 as $option_2 ) {
						$options[$option_1->id_to][$option_2->id_to] = $option_2->id_to;
					}
				} else {
					$options[$option_1->id_to] = $option_1->id_to;
				}
			}
		return $options;
	}*/

	static function exists( $id_from, $id_to, $rel_type = 'GROUPED' ) {
		global $wpdb;
		$count = $wpdb->get_var( $wpdb->prepare( 'select count(*) from ' . $wpdb->prefix . 'tcp_rel_entities 
				where id_from = %d and id_to = %d and rel_type = %s', $id_from, $id_to, $rel_type ) );
		return $count > 0;
	}

	/**
	 * Returns the parent
	 */
	static function getParent( $id_to, $rel_type = 'GROUPED' ) {
		global $wpdb;
		return $wpdb->get_var( $wpdb->prepare( 'select id_from from ' . $wpdb->prefix . 'tcp_rel_entities 
				where id_to = %d and rel_type = %s', $id_to, $rel_type ) );
	}

	/**
	 * Returns the parents
	 */
	static function getParents( $id_to, $rel_type = 'GROUPED' ) {
		global $wpdb;
		return $wpdb->get_results( $wpdb->prepare( 'select id_from from ' . $wpdb->prefix . 'tcp_rel_entities 
				where id_to = %d and rel_type = %s', $id_to, $rel_type ) );
	}
	/**
	 * Returns a row
	 */
	static function get( $id_from, $id_to, $rel_type = 'GROUPED' ) {
		global $wpdb;
		return $wpdb->get_row( $wpdb->prepare( 'select * from ' . $wpdb->prefix . 'tcp_rel_entities 
				where id_from = %d and id_to = %d and rel_type = %s', $id_from, $id_to, $rel_type ) );
	}
}
?>
