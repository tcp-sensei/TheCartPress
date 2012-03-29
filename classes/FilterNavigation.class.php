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
			$filter['order_type'] = isset( $settings['order_type'] ) ? $settings['order_type'] : 'order';
			$filter['order_desc'] = isset( $settings['order_desc'] ) ? $settings['order_desc'] : 'asc';
		}
		$this->order_type = $filter['order_type'];
		$this->order_desc = $filter['order_desc'];

		/*if ( $check_request && isset( $_REQUEST['tcp_layered_terms'] ) && is_array( $_REQUEST['tcp_layered_terms'] ) && count( $_REQUEST['tcp_layered_terms'] ) > 0 ) {
			$taxonomy = $_REQUEST['tcp_layered_taxonomy'];
			unset( $filter['layered'][$taxonomy] );
			foreach( $_REQUEST['tcp_layered_terms'] as $term ) {
				$filter['layered'][$taxonomy][] = $term;
			}
		} elseif ( $check_request && isset( $_REQUEST['tcp_layered_submit'] ) ) {
			$taxonomy = $_REQUEST['tcp_layered_taxonomy'];
			unset( $filter['layered'][$taxonomy] );
		}*/
		if ( $check_request ) {
			$filters = $this->get_filters_request();
			foreach( $filters as $f ) {
				$taxonomy = $f['taxonomy'];
				$filter['layered'][$taxonomy][] = $f['term'];
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
					'taxonomy'	=> $taxonomy,
					'term'		=> $value
				);
			}
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
		return isset( $this->layered[$taxonomy] );
	}

	function is_filter_by_term( $taxonomy, $term ) {
		if ( isset( $this->layered[$taxonomy] ) )
			foreach( $this->layered[$taxonomy] as $t ) {
				if ( $t == $term ) return true;
			}
		return false;
	}
}
?>
