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
 * along with This program. If not, see <http://www.gnu.org/licenses/>.
 */

require_once( TCP_DAOS_FOLDER . 'Orders.class.php' );
require_once( TCP_DAOS_FOLDER . 'OrdersDetails.class.php' );
require_once( TCP_DAOS_FOLDER . 'OrdersCosts.class.php' );

require_once( TCP_CLASSES_FOLDER . 'ICartSource.interface.php' );

class TCP_CartSourceDB implements TCP_ICartSource {

	private $order; //order from Orders table

	private $orders_details;//details from OrdersDetails table
	private $orders_costs;//costs from OrdersCosts table

	private $see_address;
	private $see_full;
	private $see_thumbnail;
	private $see_tax_summary;
	private $see_comment;

	function __construct( $order_id, $see_address = true, $see_full = true, $see_tax_summary = true, $see_comment = true, $see_thumbnail = false ) {
		$this->order			= Orders::get( $order_id );
		$this->orders_details	= OrdersDetails::getDetails( $order_id );
		$this->orders_costs		= OrdersCosts::getCosts( $order_id );
		$this->see_address		= $see_address;
		$this->see_full			= $see_full;
		$this->see_tax_summary	= $see_tax_summary;
		$this->see_comment		= $see_comment;
		$this->see_thumbnail	= $see_thumbnail;
	}

	public function get_order_id() {
		if ( $this->order )	return $this->order->order_id;
		else false;
	}

	public function get_created_at() {
		if ( $this->order )	return $this->order->created_at;
		else false;
	}

	public function get_payment_method() {
		if ( $this->order )	return $this->order->payment_name;
		else false;
	}

	public function get_shipping_method() {
		if ( $this->order )	return $this->order->shipping_method;
		else false;
	}

	public function get_status() {
		if ( $this->order )	return $this->order->status;
		else false;
	}
	
	public function see_other_costs() {
		return true;
	}

	public function see_address() {
		return $this->see_address;
	}

	public function see_full() {
		return $this->see_full;
	}
	
	public function see_thumbnail() {
		return $this->see_thumbnail;
	}

	public function see_tax_summary() {
		return $this->see_tax_summary;
	}

	public function is_editing_units() {
		return false;
	}

	public function see_product_link() {
		return false;
	}

	public function see_comment() {
		return $this->see_comment;
	}

	public function get_shipping_firstname() {
		if ( $this->order ) return stripslashes( $this->order->shipping_firstname );
		else return false;
	}

	public function get_shipping_lastname() {
		if ( $this->order ) return stripslashes( $this->order->shipping_lastname );
		else return false;
	}

	public function get_shipping_company() {
		if ( $this->order ) return stripslashes( $this->order->shipping_company );
		else return false;
	}

	public function get_shipping_street() {
		if ( $this->order ) return stripslashes( $this->order->shipping_street );
		else return false;
	}

	public function get_shipping_postcode() {
		if ( $this->order ) return stripslashes( $this->order->shipping_postcode );
		else return false;
	}

	public function get_shipping_city() {
		if ( $this->order ) return stripslashes( $this->order->shipping_city );
		else return false;
	}

	public function get_shipping_region() {
		if ( $this->order ) return stripslashes( $this->order->shipping_region );
		else return false;
	}

	public function get_shipping_country() {
		if ( $this->order ) return stripslashes( $this->order->shipping_country );
		else return false;
	}

	public function get_shipping_telephone_1() {
		if ( $this->order ) return stripslashes( $this->order->shipping_telephone_1 );
		else return false;
	}

	public function get_shipping_telephone_2() {
		if ( $this->order ) return stripslashes( $this->order->shipping_telephone_2 );
		else return false;
	}

	public function get_shipping_fax() {
		if ( $this->order ) return stripslashes( $this->order->shipping_fax );
		else return false;
	}

	public function get_shipping_email() {
		if ( $this->order ) return $this->order->shipping_email;
		else return false;
	}

	public function get_billing_firstname() {
		if ( $this->order ) return stripslashes( $this->order->billing_firstname );
		else return false;
	}

	public function get_billing_lastname() {
		if ( $this->order ) return stripslashes( $this->order->billing_lastname );
		else return false;
	}

	public function get_billing_company() {
		if ( $this->order ) return stripslashes( $this->order->billing_company );
		else return false;
	}

	public function get_billing_street() {
		if ( $this->order ) return stripslashes( $this->order->billing_street );
		else return false;
	}

	public function get_billing_postcode() {
		if ( $this->order ) return stripslashes( $this->order->billing_postcode );
		else return false;
	}

	public function get_billing_city() {
		if ( $this->order ) return stripslashes( $this->order->billing_city );
		else return false;
	}

	public function get_billing_region() {
		if ( $this->order ) return stripslashes( $this->order->billing_region );
		else return false;
	}

	public function get_billing_country() {
		if ( $this->order ) return stripslashes( $this->order->billing_country );
		else return false;
	}

	public function get_billing_telephone_1() {
		if ( $this->order ) return stripslashes( $this->order->billing_telephone_1 );
		else return false;
	}

	public function get_billing_telephone_2() {
		if ( $this->order ) return stripslashes( $this->order->billing_telephone_2 );
		else return false;
	}

	public function get_billing_fax() {
		if ( $this->order ) return stripslashes( $this->order->billing_fax );
		else return false;
	}

	public function get_billing_email() {
		if ( $this->order ) return $this->order->billing_email;
		else return false;
	}

	public function has_order_details() {
		return is_array( $this->orders_details) && count( $this->orders_details ) > 0;
	}

	public function get_orders_details() {
		$details = array();
		if ( ! $this->has_order_details() )	return $details;
		foreach( $this->orders_details as $detail )
			$details[] = new TCP_DetailSourceDB( $detail );
		return $details;
	}

	public function get_discount() {
		if ( $this->order ) return $this->order->discount_amount;
		else return false;
	}

	public function get_comment() {
		if ( $this->order ) return $this->order->comment;
		else return false;
	}

	public function has_orders_costs() {
		return is_array( $this->orders_costs) && count( $this->orders_costs ) > 0;
	}

	public function get_orders_costs() {
		$costs = array();
		if ( ! $this->has_orders_costs() ) return $costs;
		foreach( $this->orders_costs as $cost )
			$costs[] = new TCP_CostSourceDB( $cost );
		return $costs;
	}
}

class TCP_DetailSourceDB implements TCP_IDetailSource {
	private $detail;

	function __construct( $detail ) {
		$this->detail = $detail;
	}

	public function get_post_id() {
		if ( $this->detail ) return $this->detail->post_id;
		else return false;
	}

	public function get_option_1_id() {
		if ( $this->detail ) return $this->detail->option_1_id;
		else return false;
	}

	public function get_option_2_id() {
		if ( $this->detail ) return $this->detail->option_2_id;
		else return false;
	}

	public function get_name() {
		if ( $this->detail ) {
			$name = $this->detail->name;
			if ( strlen( $this->detail->option_1_name ) > 0 ) $name .= '<br />' . $this->detail->option_1_name;
			if ( strlen( $this->detail->option_2_name ) > 0 ) $name .= '-' . $this->detail->option_2_name;
			$name = stripslashes( $name );
			$attributes = tcp_get_order_detail_meta( $this->detail->order_detail_id, 'tcp_attributes' );
			if ( $attributes ) foreach( $attributes as $id => $value )
				$name .= '<br/>' . $id . ' = ' . $value;
			return $name;
		} else {
			return false;
		}
	}

	/*public function get_option_1_name() {
		if ( $this->detail ) return stripslashes( $this->detail->option_1_name );
		else return false;
	}

	public function get_option_2_name() {
		if ( $this->detail ) return stripslashes( $this->detail->option_2_name );
		else return false;
	}*/

	public function get_qty_ordered() {
		if ( $this->detail ) return $this->detail->qty_ordered;
		else return false;
	}

	public function get_tax() {
		if ( $this->detail ) return $this->detail->tax;
		else return false;
	}

	public function get_price() {
		if ( $this->detail ) return $this->detail->price;
		else return false;
	}

	public function get_discount() {
		return 0;
	}

	public function get_sku() {
		if ( $this->detail ) return stripslashes( $this->detail->sku );
		else return false;
	}

	public function get_weight() {
		if ( $this->detail ) return $this->detail->weight;
		else return false;
	}
}

class TCP_CostSourceDB {
	private $cost;

	public function __construct( $cost ) {
		$this->cost = $cost;
	}

	public function get_description() {
		if ( $this->cost) return $this->cost->description;
		else return false;
	}

	public function get_cost() {
		if ( $this->cost) return $this->cost->cost;
		else return false;
	}

	public function get_tax() {
		if ( $this->cost) return $this->cost->tax;
		else return false;
	}
}

?>
