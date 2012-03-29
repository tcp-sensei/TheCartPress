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

require_once( TCP_CLASSES_FOLDER . 'ICartSource.interface.php' );

class TCPCartSourceSession implements TCP_ICartSource {

	private $order;
	private $orders_details;
	private $orders_costs;

	private $is_editing_units;
	private $see_other_costs;
	private $see_address;
	private $see_full;
	private $see_thumbnail;
	private $see_tax_summary;

	function __construct( $is_editing_units = true, $see_other_costs = false, $see_address = false, $see_full = false, $see_tax_summary = false, $see_thumbnail = true ) {
		$this->is_editing_units	= $is_editing_units;
		$this->see_other_costs	= $see_other_costs;
		$this->see_address		= $see_address;
		$this->see_full			= $see_full;
		$this->see_tax_summary	= $see_tax_summary;
		$this->see_thumbnail	= $see_thumbnail;
	}

	public function get_order_id() {
		return false;
	}

	public function get_created_at() {
			return false;
	}

	public function get_payment_method() {
		return false;
	}

	public function get_shipping_method() {
		return false;
	}

	public function get_status() {
		return false;
	}

	public function see_other_costs() {
		return $this->see_other_costs;
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
		return $this->is_editing_units;
	}

	public function see_product_link() {
		return true;
	}
	
	public function see_comment() {
		return false;
	}

	public function get_shipping_firstname() {
		if ( isset( $_SESSION['tcp_checkout'] ) ) return $_SESSION['tcp_checkout']['shipping']['shipping_firstname'];
		else return false;
	}

	public function get_shipping_lastname() {
		if ( isset( $_SESSION['tcp_checkout'] ) ) return $_SESSION['tcp_checkout']['shipping']['shipping_lasname'];
		else return false;
	}

	public function get_shipping_company() {
		if ( isset( $_SESSION['tcp_checkout'] ) ) return $_SESSION['tcp_checkout']['shipping']['shipping_company'];
		else return false;
	}

	public function get_shipping_street() {
		if ( isset( $_SESSION['tcp_checkout'] ) ) return $_SESSION['tcp_checkout']['shipping']['shipping_street'];
		else return false;
	}

	public function get_shipping_postcode() {
		if ( isset( $_SESSION['tcp_checkout'] ) ) return $_SESSION['tcp_checkout']['shipping']['shipping_postcode'];
		else return false;
	}

	public function get_shipping_city() {
		if ( isset( $_SESSION['tcp_checkout'] ) ) return $_SESSION['tcp_checkout']['shipping']['shipping_city'];
		else return false;
	}

	public function get_shipping_region() {
		if ( isset( $_SESSION['tcp_checkout'] ) ) return $_SESSION['tcp_checkout']['shipping']['shipping_region'];
		else return false;
	}

	public function get_shipping_country() {
		if ( isset( $_SESSION['tcp_checkout'] ) ) return $_SESSION['tcp_checkout']['shipping']['shipping_country'];
		else return false;
	}

	public function get_shipping_telephone_1() {
		if ( isset( $_SESSION['tcp_checkout'] ) ) return $_SESSION['tcp_checkout']['shipping']['shipping_telephone_1'];
		else return false;
	}

	public function get_shipping_telephone_2() {
		if ( isset( $_SESSION['tcp_checkout'] ) ) return $_SESSION['tcp_checkout']['shipping']['shipping_telephone_2'];
		else return false;
	}

	public function get_shipping_fax() {
		if ( isset( $_SESSION['tcp_checkout'] ) ) return $_SESSION['tcp_checkout']['shipping']['shipping_fax'];
		else return false;
	}

	public function get_shipping_email() {
		if ( isset( $_SESSION['tcp_checkout'] ) ) return $_SESSION['tcp_checkout']['shipping']['shipping_email'];
		else return false;
	}

	public function get_billing_firstname() {
		if ( isset( $_SESSION['tcp_checkout'] ) ) return $_SESSION['tcp_checkout']['billing']['billing_firstname'];
		else return false;
	}

	public function get_billing_lastname() {
		if ( isset( $_SESSION['tcp_checkout'] ) ) return $_SESSION['tcp_checkout']['billing']['billing_lasname'];
		else return false;
	}

	public function get_billing_company() {
		if ( isset( $_SESSION['tcp_checkout'] ) ) return $_SESSION['tcp_checkout']['billing']['billing_company'];
		else return false;
	}

	public function get_billing_street() {
		if ( isset( $_SESSION['tcp_checkout'] ) ) return $_SESSION['tcp_checkout']['billing']['billing_street'];
		else return false;
	}

	public function get_billing_postcode() {
		if ( isset( $_SESSION['tcp_checkout'] ) ) return $_SESSION['tcp_checkout']['billing']['billing_postcode'];
		else return false;
	}

	public function get_billing_city() {
		if ( isset( $_SESSION['tcp_checkout'] ) ) return $_SESSION['tcp_checkout']['billing']['billing_city'];
		else return false;
	}

	public function get_billing_region() {
		if ( isset( $_SESSION['tcp_checkout'] ) ) return $_SESSION['tcp_checkout']['billing']['billing_region'];
		else return false;
	}

	public function get_billing_country() {
		if ( isset( $_SESSION['tcp_checkout'] ) ) return $_SESSION['tcp_checkout']['billing']['billing_country'];
		else return false;
	}

	public function get_billing_telephone_1() {
		if ( isset( $_SESSION['tcp_checkout'] ) ) return $_SESSION['tcp_checkout']['billing']['billing_telephone_1'];
		else return false;
	}

	public function get_billing_telephone_2() {
		if ( isset( $_SESSION['tcp_checkout'] ) ) return $_SESSION['tcp_checkout']['billing']['billing_telephone_2'];
		else return false;
	}

	public function get_billing_fax() {
		if ( isset( $_SESSION['tcp_checkout'] ) ) return $_SESSION['tcp_checkout']['billing']['billing_fax'];
		else return false;
	}

	public function get_billing_email() {
		if ( isset( $_SESSION['tcp_checkout'] ) ) return $_SESSION['tcp_checkout']['billing']['billing_email'];
		else return false;
	}

	public function has_order_details() {
		$shopping_cart = TheCartPress::getShoppingCart();
		return ! $shopping_cart->isEmpty();
	}

	public function get_orders_details() {
		$details = array();
		if ( ! $this->has_order_details() )	return $details;
		$shopping_cart = TheCartPress::getShoppingCart();
		foreach( $shopping_cart->getItems() as $detail )
			$details[] = new TCP_DetailSourceSession( $detail );
		return $details;
	}

	public function get_discount() {
		$shopping_cart = TheCartPress::getShoppingCart();
		return $shopping_cart->getCartDiscountsTotal();
	}

	public function get_comment() {
		return false;
	}

	public function has_orders_costs() {
		$shopping_cart = TheCartPress::getShoppingCart();
		$costs = $shopping_cart->getOtherCosts();
		return is_array( $costs ) && count( $costs ) > 0;
	}

	public function get_orders_costs() {
		$shopping_cart = TheCartPress::getShoppingCart();
		$session_costs = $shopping_cart->getOtherCosts();
		asort( $session_costs, SORT_STRING );
		$costs = array();
		foreach( $session_costs as $cost )
			$costs[] = new TCP_CostSourceSession( $cost );
		return $costs;
	}
}

class TCP_DetailSourceSession implements TCP_IDetailSource {
	private $item;

	function __construct( $item ) {
		$this->item = $item;
	}

	public function get_post_id() {
		if ( $this->item ) return $this->item->getPostId();
		else return false;
	}

	public function get_option_1_id() {
		if ( $this->item ) return $this->item->getOption1Id();
		else return false;
	}

	public function get_option_2_id() {
		if ( $this->item ) return $this->item->getOption2Id();
		else return false;
	}

	public function get_name() {
		if ( $this->item ) return stripslashes( $this->item->getTitle() );
		else return false;
	}

	/*public function get_option_1_name() {
		return false;
	}

	public function get_option_2_name() {
		return false;
	}*/

	public function get_qty_ordered() {
		if ( $this->item ) return $this->item->getCount();
		else return false;
	}

	public function get_tax() {
		if ( $this->item ) return $this->item->getTax();
		else return false;
	}

	public function get_price() {
		if ( $this->item ) return $this->item->getPriceToShow();
		else return false;
	}
	
	public function get_discount() {
		if ( $this->item ) return $this->item->getDiscount();
		else return false;
	}

	public function get_sku() {
		return false;
	}

	public function get_weight() {
		if ( $this->item ) return $this->item->getUnitWeight();
		else return false;
	}
}

class TCP_CostSourceSession {
	private $cost;

	public function __construct( $cost ) {
		$this->cost = $cost;
	}

	public function get_description() {
		if ( $this->cost) return $this->cost->getDesc();
		else return false;
	}

	public function get_cost() {
		if ( $this->cost) return $this->cost->getCost();
		else return false;
	}

	public function get_tax() {
		return 0;
	}
}
?>
