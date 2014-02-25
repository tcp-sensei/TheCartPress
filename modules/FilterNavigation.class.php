<?php
/**
 * Filter Navigation
 *
 * Allows to filter the catalogue: at this moment only supports sorting
 *
 * @package TheCartPress
 * @subpackage Modules
 */

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
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'TCPFilterNavigation' ) ) :

class TCPFilterNavigation {
	private $min_price = 0;
	private $max_price = 999;
	private $filter_by_price_range = false;
	private $order_type;
	private $order_desc;
	private $filter_by_layered = false;
	private $layered;

	function __construct( $check_request = true ) {
		//if ( ! isset( $_SESSION['tcp_filter'] ) ) $_SESSION['tcp_filter'] = array();
		//$filter = $_SESSION['tcp_filter'];
		$filter = array();
		if ( $check_request && isset( $_REQUEST['tcp_min_price'] ) && isset( $_REQUEST['tcp_max_price'] ) ) {
			$filter['tcp_min_price'] = $_REQUEST['tcp_min_price'];
			$filter['tcp_max_price'] = $_REQUEST['tcp_max_price'];
		}
		if ( isset( $filter['tcp_min_price'] ) && isset( $filter['tcp_max_price'] ) ) {
			$this->min_price = $filter['tcp_min_price'];
			$this->max_price = $filter['tcp_max_price'];
			$this->filter_by_price_range = true;
		}

		if ( $check_request && isset( $_REQUEST['tcp_order_type'] ) && isset( $_REQUEST['tcp_order_desc'] ) ) {
			$filter['order_type'] = isset( $_REQUEST['tcp_order_type'] ) ? $_REQUEST['tcp_order_type'] : 'order';
			$filter['order_desc'] = isset( $_REQUEST['tcp_order_desc'] ) ? $_REQUEST['tcp_order_desc'] : 'asc';
		} elseif ( isset( $filter['order_type'] ) && isset( $filter['order_desc'] ) ) {
		} else {
			$settings = get_option( 'ttc_settings' );
			global $wp_query;
			$post_type = $wp_query->get( 'post_type' );
			if ( is_array( $post_type ) ) {
				$post_type = $post_type[0];
			}
			$suffix = $post_type;
			if ( isset( $settings['order_type-' . $suffix] ) ) {
				$filter['order_type'] = $settings['order_type-' . $suffix];
			} elseif ( isset( $settings['order_type'] ) ) {
				$filter['order_type'] = $settings['order_type'];
			} else {
				$filter['order_type'] = 'order';
			}
			if ( isset( $settings['order_desc-' . $suffix] ) ) {
				$filter['order_desc'] = $settings['order_desc-' . $suffix];
			} elseif ( isset( $settings['order_desc'] ) ) {
				$filter['order_desc'] = $settings['order_desc'];
			} else {
				$filter['order_desc'] = 'asc';
			}
		}
		$this->order_type = $filter['order_type'];
		$this->order_desc = $filter['order_desc'];

		if ( $check_request ) {
			$filters = $this->get_filters_request();
			foreach( $filters as $f ) {
				if ( isset( $f['type'] ) && 'taxonomy' == $f['type'] ) {
					$taxonomy = $f['taxonomy'];
					$filter['layered'][$taxonomy][] = array(
						'type' => $f['type'],
						'term' => $f['term']
					);
				} elseif ( isset( $f['custom_field'] ) ) { //custom_field
					$custom_field = $f['custom_field'];
					$filter['layered'][$custom_field][] = array(
						'type' => $f['type'],//TODO????
						'value' => $f['value']
					);
				} else {
					$filter = apply_filters( 'tcp_filter_navigation_get_filter', $filter, $f );
				}
			}
		}

		if ( isset( $filter['layered'] ) && is_array( $filter['layered'] ) && count( $filter['layered'] ) > 0 ) {
			$this->layered = $filter['layered'];
			$this->filter_by_layered = true;
		}
		//$_SESSION['tcp_filter'] = $filter;
	}

	function get_filters_request() {
		$filters = array();
		foreach( $_REQUEST as $key => $value )
			if ( $pos = strpos( $key, 'tcp_filter' ) === 0 ) {
				$taxonomy = substr( $key, $pos + 10 );
				$filters[] = array(
					'type' => 'taxonomy',
					'taxonomy' => $taxonomy,
					'term' => $value
				);
			} elseif ( $pos = strpos( $key, 'tcp_custom' ) === 0 ) {
				$custom_field = substr( $key, $pos + 10 );
				$filters[] = array(
					'type' => 'custom_field',
					'custom_field' => $custom_field,
					'value' => $value,
				);
			}
		$filters = apply_filters( 'tcp_filter_navigation_get_filters_request', $filters );
		return $filters;
	}

	function is_filter_by_price_range() {
		return $this->filter_by_price_range;
	}

	function get_min_price() {
		return $this->min_price;
	}

	function get_max_price() {
		return $this->max_price;
	}

	function get_order_type() {
		return $this->order_type;
	}

	function get_order_desc() {
		return $this->order_desc;
	}

	function is_filter_by_layered() {
		return $this->filter_by_layered;
	}

	function get_layered() {
		return $this->layered;
	}

	function is_filter_by_taxonomy( $taxonomy ) {
		//if ( isset( $this->layered[$taxonomy] ) ) return $this->layered[$taxonomy]['type'] != 'taxonomy';
		return isset( $this->layered[$taxonomy] );
	}

	function is_filter_by_term( $taxonomy, $term ) {
		if ( isset( $this->layered[$taxonomy] ) ) {
			//if ( $this->layered[$taxonomy]['type'] != 'taxonomy' ) return false;
			foreach( $this->layered[$taxonomy] as $t ) {
				if ( $t['term'] == $term ) return true;
			}
		}
		return false;
	}

	function is_filter_by_dinamic_options( $taxonomy, $term ) {
		if ( isset( $this->layered['dynamic_options'] ) )
			foreach( $this->layered['dynamic_options'] as $layered )
				if ( $layered['taxonomy'] == $taxonomy && $layered['term'] == $term ) return true;
		return false;
	}

	function is_filter_by_custom_field( $custom_field ) {
		//if ( isset( $this->layered[$custom_field] ) ) return $this->layered[$custom_field]['type'] != 'custom_field';
		return isset( $this->layered[$custom_field] );
	}

	function is_filter_by_value( $custom_field, $value ) {
		if ( isset( $this->layered[$custom_field] ) ) {
			//if ( $this->layered[$custom_field]['type'] != 'custom_field' ) return false;
			foreach( $this->layered[$custom_field] as $v ) {
				if ( $v['value'] == $value ) return true;
			}
		}
		return false;
	}
}
endif; // class_exists check