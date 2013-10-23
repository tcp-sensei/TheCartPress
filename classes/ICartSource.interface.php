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

interface TCP_ICartSource {
	public function see_address();
	public function see_sku();
	public function see_weight();
	public function see_tax();
	public function see_tax_summary();
	public function is_editing_units();
	public function see_other_costs();
	public function see_product_link();
	public function see_comment();
	public function see_thumbnail();

	public function __set( $name, $value );

	public function get_order_id();
	public function get_created_at();
	public function get_payment_method();
	public function get_payment_name();
	public function get_payment_notice();
	public function get_shipping_method();
	public function get_shipping_notice();
	public function get_status();
	
	public function get_shipping_firstname();//stripslashes
	public function get_shipping_lastname(); 
	public function get_shipping_company();
	public function get_shipping_street();
	public function get_shipping_postcode();
	public function get_shipping_city();
	public function get_shipping_region();
	public function get_shipping_region_id();
	public function get_shipping_country();
	public function get_shipping_country_id();
	public function get_shipping_telephone_1();
	public function get_shipping_telephone_2();
	public function get_shipping_fax();
	public function get_shipping_email();

	public function get_billing_firstname();//stripslashes
	public function get_billing_lastname(); 
	public function get_billing_company();
	public function get_billing_tax_id_number();
	public function get_billing_street();
	public function get_billing_postcode();
	public function get_billing_city();
	public function get_billing_region();
	public function get_billing_region_id();
	public function get_billing_country();
	public function get_billing_country_id();
	public function get_billing_telephone_1();
	public function get_billing_telephone_2();
	public function get_billing_fax();
	public function get_billing_email();

	public function has_order_details();
	public function get_orders_details();//Returns an array of TCP_IDetailSource
	
	public function get_discount();
	//public function get_discounts();//TODO
	public function get_comment();
}

interface TCP_IDetailSource {
	public function get_post_id();
	public function get_option_1_id();
	public function get_option_2_id();
	public function get_name();//stripslashes
	public function get_qty_ordered();
	public function get_tax();
	public function get_price();
	public function get_discount();
	public function get_sku();
	public function get_weight();
	public function has_attributes();
	public function get_attributes();
	public function get_attribute( $id );
}

interface TCP_ICostsSource {
	public function get_description();
	public function get_cost();
	public function get_tax();
}
?>
