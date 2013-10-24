<?php
/**
 * Common template-functions
 *
 * Defines common template-functions
 *
 * @package TheCartPress
 * @subpackage Templates
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

//Multilingual support: WPML or Qtranslate (or by any other plugin)
function tcp_get_admin_language_iso() {
	if ( strlen( WPLANG ) > 0 ) {
		$lang_country = explode ( '_', WPLANG );
		if ( is_array( $lang_country ) && count( $lang_country ) > 0 ) {
			return $lang_country[0];
		} else {
			return 'en';//by default
		}
	} else {
		return 'en'; //by default
	}
}

$multilingual_template_path = apply_filters( 'tcp_get_multilingual_template_path', '' );

if ( strlen( $multilingual_template_path ) > 0 ) {
	include_once( $multilingual_template_path );
} else {
	global $sitepress;
	if ( $sitepress ) {
		include_once( dirname( __FILE__ ) . '/tcp_wpml_template.php' );
	} else {
		include_once( dirname( __FILE__ ) . '/tcp_qt_template.php' );
	}
}
//End Multilingual support

//Returns the title of a product (with/without options)
function tcp_get_the_title( $post_id = 0, $option_1_id = 0, $option_2_id = 0, $html = true, $show_parent = true ) {
	if ( $post_id == 0 ) $post_id = get_the_ID();
	$post_id = tcp_get_current_id( $post_id );
	$title = '';
	if ( $html ) $title .= '<span class="tcp_nested_title">';
	$title .= get_the_title( $post_id );
	if ( $html ) $title .= '</span>';
	if ( $option_1_id > 0 ) {
		$option_1_id = tcp_get_current_id( $option_1_id, 'tcp_product_option' );
		if ( $html ) $title .= ' <span class="tcp_nested_option_1">';
		else $title .= ' - ';
		$title .= get_the_title( $option_1_id );
		if ( $html ) $title .= '</span>';
	}
	if ( $option_2_id > 0 ) {
		$option_2_id = tcp_get_current_id( $option_2_id, 'tcp_product_option' );
		if ( $html ) $title .= ' <span class="tcp_nested_option_1">';
		else $title .= ' - ';
		$title .= get_the_title( $option_2_id );
		if ( $html ) $title .= '</span>';
	}
	if ( $show_parent && ! tcp_is_visible( $post_id ) ) {
		$parent_id = tcp_get_the_parent( $post_id );
		//$parent_id = tcp_get_current_id( $parent_id );
		if ( $parent_id ) $title = get_the_title( $parent_id ) . ' - ' . $title;
	}
	return apply_filters ( 'tcp_get_the_title', $title, $post_id, $html, $show_parent );
}

function tcp_the_title( $echo = true ) {
	$title = tcp_get_the_title();
	if ( $echo ) echo $title;
	else return $title;
}

function tcp_get_the_currency() {
	return tcp_the_currency( false );
}

function tcp_the_currency( $echo = true ) {
	global $thecartpress;
	$currency = $thecartpress->get_setting( 'currency', 'EUR' );
	$currency = apply_filters( 'tcp_the_currency', $currency );
	if ( $echo ) echo $currency;
	else return $currency;
}

function tcp_the_currency_iso( $echo = true ) {
	global $thecartpress;
	$currency = $thecartpress->get_setting( 'currency', 'EUR' );
	$currency = apply_filters( 'tcp_the_currency_iso', $currency );
	if ( $echo ) echo $currency;
	else return $currency;
}

function tcp_get_the_currency_iso() {
	return tcp_the_currency_iso( false );
}

function tcp_the_currency_layout( $echo = true ) {
	global $thecartpress;
	$currency_layout = $thecartpress->get_setting( 'currency_layout', __( '%1$s%2$s (%3$s)', 'tcp' ) ); //'currency + price + (currency ISO)'
	$currency_layout = apply_filters( 'tcp_the_currency_layout', $currency_layout );
	if ( $echo )
		echo $currency_layout;
	else
		return $currency_layout;
}

function tcp_get_the_currency_layout() {
	return tcp_the_currency_layout( false );
}

function tcp_get_decimal_currency() {
	global $thecartpress;
	$decimal_currency = $thecartpress->get_setting( 'decimal_currency', '2' );
	$decimal_currency = apply_filters( 'tcp_get_decimal_currency', $decimal_currency );
	return $decimal_currency;
}

function tcp_get_number_format_example( $number = 19.99, $see_eg = true, $echo = false ) {
	$out = '';
	if ( $see_eg ) $out .= 'e.g. ';
	$out .= tcp_number_format( $number );
	if ( $echo ) echo $out;
	else return $out;
}

function tcp_number_format_example( $number = 19.99, $see_eg = true ) {
	tcp_get_number_format_example( $number, $see_eg, true );
}

function tcp_the_unit_weight( $echo = true ) {
	global $thecartpress;
	$unit_weight = $thecartpress->get_setting( 'unit_weight', 'gr' );
	$unit_weight = apply_filters( 'tcp_the_unit_weight', $unit_weight );
	if ( $echo ) echo $unit_weight;
	else return $unit_weight;
}

function tcp_get_the_unit_weight() {
	return tcp_the_unit_weight( false );
}

function tcp_get_default_currency() {
	global $thecartpress;
	return $thecartpress->get_setting( 'currency', '' );
}

/**
 * Returns the price for current product
 * @since 1.0.9
 */
function tcp_the_price( $before = '', $after = '', $echo = true ) {
	$price = tcp_number_format( tcp_get_the_price() );
	$price = $before . $price . $after;
	if ( $echo )
		echo $price;
	else
		return $price;
}

/**
 * Returns the price for given product
 * @param $apply_filters, true to execute filters (by default) false, it doesn't apply filters. Since 1.1.9
 * @since 1.0.9
 */
function tcp_get_the_price( $post_id = 0, $apply_filters = true ) {
	$price = (float)tcp_get_the_meta( 'tcp_price', $post_id );
	if ( $apply_filters ) $price = (float)apply_filters( 'tcp_get_the_price', $price, $post_id );
	return $price;
}

/**
 * Returns the real price for given product.
 * This functions differs for 'tcp_get_the_price' that it returns the calculated price, not only the price saved in price field
 * It's useful to get the price of a product + dynamic option in tha front-end
 * @since 1.2.5
 */
function tcp_get_the_product_price( $post_id = 0 ) {
	$price = tcp_get_the_price( $post_id );
	return (float)apply_filters( 'tcp_get_the_product_price', $price, $post_id );
	return $price;
}
/**
 * Adds the currency to the price
 * @since 1.0.9
 */
function tcp_format_the_price( $price, $currency = '') {
	if ( strlen( $currency ) == '' ) {
		$currency = tcp_get_the_currency();
		$currency_iso = tcp_get_the_currency_iso();
	} else {
		$currency_iso = $currency;
		$currency = apply_filters( 'tcp_the_currency', $currency_iso );
	}
	$layout = tcp_get_the_currency_layout();
	if ( strlen( $layout ) == 0 ) $layout = __( '%1$s%2$s (%3$s)', 'tcp' ); //'currency + price + (currency ISO)'

	$label = sprintf( $layout, $currency, tcp_number_format( $price, tcp_get_decimal_currency() ), $currency_iso );
	$label = apply_filters( 'tcp_format_the_price', $label );
	return $label;
}

/**
 * Returns the price to show in the catalog
 * @since 1.1.1
 */
function tcp_get_the_price_to_show( $post_id = 0, $price = false ) {
	if ( $post_id == 0 ) $post_id = get_the_ID();
	if ( $price === false ) $price = tcp_get_the_price( $post_id );
	if ( tcp_is_display_prices_with_taxes() ) {
		if ( tcp_is_price_include_tax() ) {
			$price_wo_tax = tcp_get_the_price_without_tax( $post_id, $price );
			$tax = tcp_get_the_tax( $post_id );
			return $price_wo_tax * ( 1 + $tax / 100 );
		} else { //add tax from price
			$tax = tcp_get_the_tax( $post_id );
			$amount = $price * $tax / 100;
			return $price + $amount;
		}
	} elseif ( ! tcp_is_price_include_tax() ) {
		return $price;
	} else { //remove tax from price
		$price_wo_tax = tcp_get_the_price_without_tax( $post_id, $price );
		return $price_wo_tax;
	}
}

/**
 * Display the price with currency
 * @since 1.0.9
 */
function tcp_the_price_label( $before = '', $after = '', $echo = true ) {
	$label = tcp_get_the_price_label();
	$label = $before . $label . $after;
	if ( $echo ) echo $label;
	else return $label;
}

/**
 * Returns the price with currency
 * @since 1.0.9
 */
function tcp_get_the_price_label( $post_id = 0, $price = false, $apply_filters = true ) {
	if ( $post_id == 0 ) $post_id = get_the_ID();
	$post_id = tcp_get_default_id( $post_id );
	$price = tcp_get_the_price_to_show( $post_id, $price );
	$label = tcp_format_the_price( $price );
	if ( $apply_filters ) $label = apply_filters( 'tcp_get_the_price_label', $label, $post_id, $price );
	return $label;
}

/**
 * Returns the (min, max) price for grouped products
 * @since 1.1.0
 */
function tcp_get_min_max_price( $post_id = 0 ) {
	if ( $post_id == 0 ) $post_id = get_the_ID();
	require_once( TCP_DAOS_FOLDER . 'RelEntities.class.php' );
	$products = RelEntities::select( $post_id, 'GROUPED' );
	if ( is_array( $products ) && count( $products ) > 0 ) {
		$min = 99999999999;
		$max = 0;
		foreach( $products as $product ) {
			if ( ! tcp_is_exclude_range( $product->id_to ) ) {
				$price = (float)tcp_get_the_price_to_show( $product->id_to );
				if ( $price > 0 ) {
					if ( $price < $min ) $min = $price;
					if ( $price > $max ) $max = $price;
				}
			}
		}
		if ( $min == 99999999999 ) $min = $max;
		return apply_filters( 'tcp_get_min_max_price', array( $min, $max ), $post_id );
	} else {
		return false;
	}
}

/**
 * Returns the min price of a grouped product
 * @since 1.1.0
 */
function tcp_get_min_price( $post_id = 0 ) {
	if ( $post_id == 0 ) $post_id = get_the_ID();
	$min_max = tcp_get_min_max_price( $post_id );
	if ( is_array( $min_max ) && count( $min_max ) == 2 ) {
		return $min_max[0];
	} else {
		return 0;
	}
}

/**
 * Returns the max price of a grouped product
 * @since 1.1.0
 */
function tcp_get_max_price( $post_id = 0 ) {
	if ( $post_id == 0 ) $post_id = get_the_ID();
	$min_max = tcp_get_min_max_price( $post_id );
	if ( is_array( $min_max ) && count( $min_max ) == 2 ) {
		return $min_max[1];
	} else {
		return 0;
	}
}

/**
 * Returns the price without taxes
 * @since 1.1.8
 */
function tcp_get_the_price_without_tax( $post_id = 0, $price = false ) {
	if ( $post_id == 0 ) $post_id = get_the_ID();
	if ( $price === false ) $price = tcp_get_the_price( $post_id );
	if ( tcp_is_price_include_tax() ) {
		$tax = tcp_get_the_default_tax( $post_id );
		$price_without_tax = $price / (1 + $tax / 100 );
		return $price_without_tax;
	} else {
		return $price;
	}
}

/**
 * Returns the tax to apply to a product
 * @since 1.0.9
 */
function tcp_get_the_tax( $post_id = 0 ) {
	$tax_id = tcp_get_the_tax_id( $post_id );
	if ( $tax_id == 0 ) return 0;
	$country_iso = tcp_get_tax_country();
	$region_iso = tcp_get_tax_region();
	require_once( TCP_DAOS_FOLDER . 'TaxRates.class.php' );
	$tax = TaxRates::find( $country_iso, $region_iso, 'all', $tax_id );
	$tax = apply_filters( 'tcp_get_the_tax', $tax, $post_id );
	if ( $tax ) return $tax->rate; //$tax->label
	else return 0;
}

/**
 * Returns the default tax to apply to a product
 * @since 1.1.8
 */
function tcp_get_the_default_tax( $post_id = 0 ) {
	$tax_id = tcp_get_the_tax_id( $post_id );
	if ( $tax_id == 0 ) return 0;
	$country_iso = tcp_get_default_tax_country();
	$region_iso = tcp_get_default_tax_region();
	require_once( TCP_DAOS_FOLDER . 'TaxRates.class.php' );
	$tax = TaxRates::find( $country_iso, $region_iso, 'all', $tax_id );
	$tax = apply_filters( 'tcp_get_the_default_tax', $tax, $post_id );
	if ( $tax ) return $tax->rate; //$tax->label
	else return 0;
}

/**
 * @since 1.0.9
 */
function tcp_the_tax( $before = '', $after = '', $echo = true ) {
	$tax = tcp_number_format( tcp_get_the_tax() );
	$tax = $before . $tax . $after;
	if ( $echo )
		echo $tax;
	else
		return $tax;
}

function tcp_get_the_tax_id( $post_id = 0 ) {
	$tax_id = tcp_get_the_meta( 'tcp_tax_id', $post_id );
	$tax_id = apply_filters( 'tcp_get_the_tax_id', $tax_id, $post_id );
	if ( ! $tax_id ) return 0;//-1;
	else return $tax_id;
}

/**
 * Returns the tax title
 * @since 1.0.9
 */
function tcp_get_the_tax_type( $post_id = 0 ) {
	$tax_id = tcp_get_the_tax_id( $post_id );
	if ( ! $tax_id ) {
		return '';
	} else {
		require_once( TCP_DAOS_FOLDER . 'Taxes.class.php' );
		$tax_type = Taxes::get( $tax_id );
		if ( $tax_type )
			return $tax_type->title;
		else
			return '';
	}
}

/**
 * Returns the default country
 * @since 1.2.9
 */
function tcp_get_default_country() {
	global $thecartpress;
	return $thecartpress->get_setting( 'country', false );
}

/**
 * Returns the default country to calculate tax
 * @since 1.0.9
 */
function tcp_get_default_tax_country() {
	global $thecartpress;
	return $thecartpress->get_setting( 'default_tax_country', '' );
}

/**
 * Returns the default region to calculate tax
 * @since 1.1.8
 */
function tcp_get_default_tax_region() {
	global $thecartpress;
	//return $thecartpress->get_setting( 'default_tax_region', '' );
	return '';
}
/**
 * Returns the country iso to calculate tax
 * @since 1.0.9
 */
function tcp_get_tax_country() {
	$tax_based_on = tcp_get_tax_based_on();
	if ( $tax_based_on == 'billing' && function_exists( 'tcp_get_billing_country' ) ) {
		$country = tcp_get_billing_country();
		return $country;
	} elseif ( $tax_based_on == 'shipping' && function_exists( 'tcp_get_shipping_country' ) ) {
		$country = tcp_get_shipping_country();
		return $country;
	} else {
		return tcp_get_default_tax_country();
	}
}

/**
 * Returns the region iso to calculate tax
 * @since 1.0.9
 */
function tcp_get_tax_region() {
	$tax_based_on = tcp_get_tax_based_on();
	$region_iso = '';
	if ( $tax_based_on == 'billing' && function_exists( 'tcp_get_billing_region' ) ) {
		$region_iso = tcp_get_billing_region();
	} elseif ( $tax_based_on == 'shipping' && function_exists( 'tcp_get_shipping_region' ) ) {
		$region_iso = tcp_get_shipping_region();
	}
	if ( $region_iso != '') {
		return $region_iso;
	} else {
		return 'all';
	}
}

/**
 * Returns the shipping cost to show in the checkout. Not in use.
 * @since 1.1.5
 */
function tcp_get_the_shipping_cost_to_show( $cost ) {
	if ( tcp_is_display_shipping_cost_with_taxes() ) {
		if ( tcp_is_shipping_cost_include_tax() ) {
			$cost_wo_tax = tcp_get_the_shipping_cost_without_tax( $cost );
			$tax = tcp_get_the_shipping_tax();
			return $cost_wo_tax * ( 1 + $tax / 100 );
		} else { //add tax to the cost
			$tax = tcp_get_the_shipping_tax();
			$amount = $cost * $tax / 100;
			return $cost + $amount;
		}
	} elseif ( ! tcp_is_shipping_cost_include_tax() ) {
		return $cost;
	} else { //remove tax from cost
		$cost_wo_tax = tcp_get_the_shipping_cost_without_tax( $cost );
		return $cost_wo_tax;
	}
}

/**
 * Returns the shipping cost without tax
 * @since 1.0.9
 */
function tcp_get_the_shipping_cost_without_tax( $cost ) {
	if ( tcp_is_shipping_cost_include_tax() ) {
		$tax = tcp_get_the_shipping_default_tax();
		if ( $tax == 0 ) return $cost;
		$cost_without_tax = $cost / ( 1 + $tax / 100 );
		return $cost_without_tax;
	} else {
		return $cost;
	}
}

/**
 * Returns the tax (float) to apply to the shipping/payment/other costs
 * @since 1.0.9
 */
function tcp_get_the_shipping_tax() {
	$tax_id = tcp_get_the_shipping_tax_id();
	if ( $tax_id == 0 ) return apply_filters( 'tcp_get_the_shipping_tax', 0 );
	$country_iso = tcp_get_tax_country();
	$region_iso = tcp_get_tax_region();
	require_once( TCP_DAOS_FOLDER . 'TaxRates.class.php' );
	$tax = TaxRates::find( $country_iso, $region_iso, 'all', $tax_id );
	$tax = apply_filters( 'tcp_get_the_shipping_tax', $tax );
	if ( $tax ) return $tax->rate;
	else return 0;
}

/**
 * Returns the default tax (float) to apply to the shipping/payment/other costs
 * @since 1.1.8
 */
function tcp_get_the_shipping_default_tax() {
	$tax_id = tcp_get_the_shipping_tax_id();
	if ( $tax_id == 0 ) return 0;
	$country_iso = tcp_get_default_tax_country();
	$region_iso = tcp_get_default_tax_region();
	require_once( TCP_DAOS_FOLDER . 'TaxRates.class.php' );
	$tax = TaxRates::find( $country_iso, $region_iso, 'all', $tax_id );
	$tax = apply_filters( 'tcp_get_the_shipping_default_tax', $tax );
	if ( $tax ) return $tax->rate;
	else return 0;
}
/**
 * Returns the tax id to apply to the shipping/payment/other costs
 * @since 1.0.9
 */
function tcp_get_the_shipping_tax_id() {
	global $thecartpress;
	$tax_id = $thecartpress->get_setting( 'tax_for_shipping', 0 );
	$tax_id = apply_filters( 'tcp_get_the_shipping_tax_id', $tax_id );
	return $tax_id; 
}

function tcp_is_shipping_cost_include_tax() {
	global $thecartpress;
	return $thecartpress->get_setting( 'shipping_cost_include_tax', false );
}

function tcp_is_display_shipping_cost_with_taxes() {
	global $thecartpress;
	return $thecartpress->get_setting( 'display_shipping_cost_with_taxes', false );
}

/**
 * Returns true if the prices include the taxes
 */
function tcp_is_price_include_tax() {
	global $thecartpress;
	return $thecartpress->get_setting( 'prices_include_tax', false );
}

/**
 * @since 1.1.8
 */
function tcp_price_include_tax_message() {
	if ( tcp_is_price_include_tax() ) _e( '(Inc. Tax)', 'tcp' );
	else _e( '(No inc. Tax)', 'tcp' );
}

function tcp_get_tax_based_on() {
	global $thecartpress;
	return $thecartpress->get_setting('tax_based_on', 'origin' );
}

/**
 * Returns true if the prices must be displayed with taxes
 */
function tcp_is_display_prices_with_taxes() {
	global $thecartpress;
	return $thecartpress->get_setting( 'display_prices_with_taxes', false );
}

function tcp_get_the_product_type( $post_id = 0 ) {
	$type = tcp_get_the_meta( 'tcp_type', $post_id );
	if ( strlen( $type ) == 0 ) $type = '';
	return apply_filters( 'tcp_get_the_product_type', $type );
}

/**
 * @since 1.2
 */
function tcp_get_the_initial_units( $post_id = 0 ) {
	$initial_units = (int)tcp_get_the_meta( 'tcp_initial_units', $post_id );
	$initial_units = (int)apply_filters( 'tcp_get_the_initial_units', $initial_units, $post_id );
	return $initial_units;
}

/**
 * @since 1.2
 */
function tcp_the_initial_units( $before = '', $after = '', $echo = true ) {
	$initial_units = tcp_get_the_initial_units();
	$initial_units = $before . $initial_units . $after;
	if ( $echo ) echo $initial_units;
	else return $initial_units;
}

function tcp_get_the_weight( $post_id = 0 ) {
	$weight = (float)tcp_get_the_meta( 'tcp_weight', $post_id );
	$weight = apply_filters( 'tcp_get_the_weight', $weight, $post_id );
	return $weight;
}

function tcp_the_weight( $before = '', $after = '', $echo = true ) {
	$weight = tcp_number_format( tcp_get_the_weight() );
	$weight = $before . $weight . $after;
	if ( $echo ) echo $weight;
	else return $weight;
}

/**
 * @sonce 1.2.9
 */
function tcp_get_the_weight_label( $post_id = 0 ) {
	return sprintf( '%s %s', tcp_get_the_weight( $post_id ), tcp_get_the_unit_weight() );
}

function tcp_get_the_order( $post_id = 0 ) {
	return (int)tcp_get_the_meta( 'tcp_order', $post_id );
}

function tcp_the_sku( $before = '', $after = '', $echo = true ) {
	//$sku = tcp_the_meta( 'tcp_sku', $before, $after, false );
	$sku = tcp_get_the_sku( get_the_ID() );
	if ( $echo ) echo $before . $sku . $after;
	else return $before . $sku . $after;
}

function tcp_get_the_sku( $post_id = 0, $option_1_id = 0, $option_2_id = 0 ) {
	if ( $option_2_id > 0) {
		$sku = tcp_get_the_meta( 'tcp_sku', $option_2_id );
		if ( strlen( $sku ) == 0 ) {
			return tcp_get_the_sku( $post_id, $option_1_id );
		}
	} elseif ( $option_1_id > 0) {
		$sku = tcp_get_the_meta( 'tcp_sku', $option_1_id );
		if ( strlen( $sku ) == 0 )
			return tcp_get_the_sku( $post_id );
	} else {
		$sku = tcp_get_the_meta( 'tcp_sku', $post_id );
	}
	$sku = apply_filters( 'tcp_get_the_sku', $sku, $post_id, $option_1_id, $option_2_id );
	return $sku;
}

/**
 * @since 1.2.5
 */
function tcp_get_product_by_sku( $sku, $object = false ) {
	global $wpdb;
	$post_id = $wpdb->get_var( $wpdb->prepare( "select post_id from {$wpdb->prefix}postmeta where meta_key='tcp_sku' and meta_value=%s limit 0, 1", $sku ) );
	if ( $post_id ) {
		$post_id = tcp_get_default_id( $post_id );
		if ( $object ) {
			return get_post( $post_id );
		} else {
			return $post_id;
		}
	} else {
		return false;
	}
	/*$args = array(
		'numberposts' => 1,
		'post_type' => tcp_get_saleable_post_types(),
		'status' => array( 'publish', 'draft' ),//TODO
		'meta_query' => array(
			array(
				'key' => 'tcp_sku',
				'value' => $sku,
				'compare' => '='
			),
		),
	);
	if ( ! $object ) $args['fields'] = 'ids';
	$posts = get_posts( $args );
	if ( is_array( $posts ) && count( $posts ) > 0 ) {
		$post_id = $posts[0];
		$post_id = tcp_get_default_id( $post_id );
	} else {
		$post_id = false;
	}
	return $post_id;*/
}

function tcp_is_downloadable( $post_id = 0 ) {
	return tcp_get_the_meta( 'tcp_is_downloadable', $post_id );
}

function tcp_is_exclude_range( $post_id = 0 ) {
	$default_id = tcp_get_default_id( $post_id, get_post_type( $post_id ) );
	return tcp_get_the_meta( 'tcp_exclude_range', $default_id );
}

function tcp_is_visible( $post_id = 0 ) {
	return tcp_get_the_meta( 'tcp_is_visible', $post_id );
}

function tcp_hide_buy_button( $post_id = 0 ) {
	return tcp_get_the_meta( 'tcp_hide_buy_button', $post_id );
}

function tcp_get_the_file( $post_id = 0 ) {
	return tcp_get_the_meta( 'tcp_download_file', $post_id );
}

function tcp_set_the_file( $post_id, $upload_file ) {
	$default_id = tcp_get_default_id( $post_id, get_post_type( $post_id ) );
	if ( $default_id != $post_id ) $post_id = $default_id;
	update_post_meta( $post_id, 'tcp_download_file', $upload_file );
}

function tcp_get_the_parent( $post_id, $rel_type = 'GROUPED' ) {
	require_once( dirname( dirname( __FILE__ ) ) . '/daos/RelEntities.class.php' );
	return RelEntities::getParent( $post_id, $rel_type );
}

function tcp_get_the_parents( $post_id, $rel_type = 'GROUPED' ) {
	require_once( dirname( dirname( __FILE__ ) ) . '/daos/RelEntities.class.php' );
	return RelEntities::getParents( $post_id, $rel_type );
}

function tcp_get_the_thumbnail_src( $post_id = 0, $image_size = 'full' ) {
	$thumbnail_id = get_post_thumbnail_id( $post_id );
	$image_attributes = wp_get_attachment_image_src( $thumbnail_id, $image_size );
	return $image_attributes[0];
}

function tcp_get_the_thumbnail_image( $post_id = 0, $args = false ) {
	if ( has_post_thumbnail( $post_id ) ) {
		$image_size = isset( $args['size'] ) ? $args['size'] : 'thumbnail';
		 if ( is_array( $image_size ) ) $image_size = $image_size[0];
		$image_align = isset( $args['align'] ) ? $args['align'] : '';
		$thumbnail_id = get_post_thumbnail_id( $post_id );
		$attr = array( 'class' => $image_align . ' size-' . $image_size . ' wp-image-' . $thumbnail_id . ' tcp_single_img_featured tcp_image_' . $post_id );
		if ( is_numeric( $image_size ) ) $image_size = array( $image_size, $image_size );
		if ( function_exists( 'get_the_post_thumbnail' ) ) $image = get_the_post_thumbnail( $post_id, $image_size, $attr );
		return $image;
	}
}

function tcp_get_the_thumbnail_with_permalink( $post_id = 0, $args = false, $echo = true ) {
	$image = tcp_get_the_thumbnail_image( $post_id, $args );
	if ( strlen( $image ) > 0 ) {
		$image_link = isset( $args['link'] ) && strlen( $args['link'] ) > 0 ? $args['link'] : 'permalink';
		$thumbnail_id = get_post_thumbnail_id( $post_id );
		if ( strlen( $image_link ) > 0 ) {
			if ( $image_link == 'file' ) {
				$image_attributes = wp_get_attachment_image_src( $thumbnail_id, 'full' ); //$image_size );
				$href = $image_attributes[0];
			} else {//None, or Post URL
				//$href = tcp_get_permalink( $post_id );
				$href = tcp_get_permalink( $thumbnail_id );
				//$href = get_permalink( $thumbnail_id );
			}
			$html = '<a href="' . $href . '"';
			if ( isset( $args['class'] ) ) $html .= ' class="' . $args['class'] . '"';
			$image = $html . '>' . $image . '</a>';
		}
	}
	$image = apply_filters( 'tcp_get_the_thumbnail_with_permalink', $image, $post_id, $args );
	if ( $echo ) echo $image;
	else return $image;
}

function tcp_get_permalink( $post_id = 0, $option_1_id = 0, $option_2_id = 0 ) {
	$post_id = tcp_get_current_id( $post_id, get_post_type( $post_id ) );
	if ( ! tcp_is_visible( $post_id ) ) {
		$parent_id = tcp_get_the_parent( $post_id );
		if ( $parent_id > 0 ) $post_id = $parent_id;
	}
	$url = get_permalink( $post_id );
	return apply_filters( 'tcp_get_permalink', $url, $post_id );
}

function tcp_get_the_thumbnail( $post_id = 0, $option_1_id = 0, $option_2_id = 0, $size = 'thumbnail' ) {
	$image = '';
	$args = array( 'size' => $size );
	if ( $option_2_id > 0 ) {
		$image = tcp_get_the_thumbnail_image( $option_2_id, $args );
		if ( strlen( $image ) == 0 ) {
			$option_2_id = tcp_get_default_id( $option_2_id, get_post_type( $option_2_id ) );
			//$image = get_the_post_thumbnail( $option_2_id, $size );
			$image = tcp_get_the_thumbnail_image( $option_2_id, $args );
		}
	}
	if ( strlen( $image ) == 0 && $option_1_id > 0 ) {
		$image = tcp_get_the_thumbnail_image( $option_1_id, $args );
		if ( strlen( $image ) == 0 ) {
			$option_1_id = tcp_get_default_id( $option_1_id, get_post_type( $option_1_id ) );
			$image = tcp_get_the_thumbnail_image( $option_1_id, $args );
		}
	}
	if ( strlen( $image ) == 0 && $post_id > 0 ) {
		$image = tcp_get_the_thumbnail_image( $post_id, $args );
		if ( has_post_thumbnail( $post_id ) ) {
			$post_id = tcp_get_default_id( $post_id, get_post_type( $post_id ) );
			$image = tcp_get_the_thumbnail_image( $post_id, $args );
		}
	}
	return apply_filters( 'tcp_get_the_thumbnail', $image, $post_id, $size );
}

/**
 * Displays the content of the given post
 * @since 1.1.8
 */
function tcp_the_content( $post_id = 0 ) {
	tcp_get_the_content( $post_id, true );
}

/**
 * Returns the content of the given post
 * @since 1.1.8
 */
function tcp_get_the_content( $post_id = 0, $echo = false ) {
	global $thecartpress;
	//remove_filter( 'the_content', array( $thecartpress, 'the_content' ) );
	$post = get_post( $post_id );
	$content = $post->post_content;
	$content = apply_filters( 'the_content', $content );
	//add_filter( 'the_content', array( $thecartpress, 'the_content' ) );
	$content = str_replace(']]>', ']]>', $content);
	if ( $echo ) echo $content;
	else return $content;
}

/**
 * Echoes the excerpt of the given post
 * @since 1.1.8
 */
function tcp_the_excerpt( $post_id = 0, $length = 0 ) {
	echo tcp_get_the_excerpt( $post_id, $length );
}

/**
 * Returns the excerpt of the given post
 * @since 1.2.8
 * @see http://wp-snippets.com/dynamic-custom-length-excpert/
 */
function tcp_get_the_excerpt( $post_id = 0, $length = 0 ) { // Max excerpt length. Length is set in characters
	if ( $post_id == 0 ) $post_id = get_the_ID();
	$post = get_post( $post_id );
	$text = $post->post_excerpt;
	$text = apply_filters( 'the_excerpt', $text );
	$see_points = false;
	if ( strlen( $text ) == 0 ) {
		$text = tcp_get_the_content( $post_id );
		$see_points = true;
	}
	$text = strip_shortcodes( $text ); // optional, recommended
	$text = strip_tags( $text ); // use ' $text = strip_tags($text,'&lt;p&gt;&lt;a&gt;'); ' if you want to keep some tags
//	if ( $length > 0 ) $text = substr( $text, 0, $length );
	if ( $length > 0 ) {
		$initial_length = strlen( $text );
		$text = explode( ' ', $text );
		array_splice( $text, $length );
		$text = implode( ' ', $text );
		$see_points = $initial_length > strlen( $text );
	}
	if ( $see_points && strlen( $text ) ) $text .= sprintf( ' <a href="%s">[...]</a>', tcp_get_permalink( $post_id ) );
	return $text;
}

// Returns the portion of haystack which goes until the last occurrence of needle
function tcp_reverse_strrchr( $haystack, $needle, $trail ) {
	return strrpos( $haystack, $needle ) ? substr( $haystack, 0, strrpos( $haystack, $needle ) + $trail ) : false;
}

function tcp_the_meta( $meta_key, $before = '', $after = '' ) {
	$meta_value = tcp_get_the_meta( $meta_key );
	if ( strlen( $meta_value ) == 0 ) return '';
	$meta_value = $before . $meta_value . $after;
	echo $meta_value;
}

function tcp_get_the_meta( $meta_key, &$post_id = 0 ) {
	if ( $post_id == 0 ) $post_id = get_the_ID();
	$meta_value = get_post_meta( $post_id, $meta_key, true );
	if ( ! $meta_value ) {
		$default_id = tcp_get_default_id( $post_id, get_post_type( $post_id ) );
		if ( $default_id != $post_id ) $meta_value = get_post_meta( $default_id, $meta_key, true );
	}
	$meta_value = apply_filters( 'tcp_get_the_meta', $meta_value, $meta_key, $post_id );
	return $meta_value;
}

//
//Saleable post types
//
/**
 * Returns all saleable post types
 * But is importat to remask that you would prefer to uses "tcp_get_product_post_types"
 * because it returns all saleable products (removing options, for example)
 *
 * @see tcp_get_product_post_types
 * @since 1.1.0
 */
function tcp_get_saleable_post_types( $one_more = false ) {
	$saleable_post_types = apply_filters( 'tcp_get_saleable_post_types', array() );
	if ( $one_more !== false ) $saleable_post_types[] = $one_more;
	return $saleable_post_types;
}

/**
 * Returns true if a post_type is saleable
 * @since 1.1.6
 */
function tcp_is_saleable_post_type( $post_type ) {
	$saleable_post_types = tcp_get_saleable_post_types();
	return in_array( $post_type, $saleable_post_types );
}

/**
 * Returns true if a post, defined by post_id, is saleable
 * @since 1.1.6
 */
function tcp_is_saleable( $post_id = 0 ) {
	if ( $post_id == 0 ) $post_id = get_the_ID();
	return tcp_is_saleable_post_type( get_post_type( $post_id ) );
}

/**
 * Registers a post type as saleable
 * @since 1.1.6
 */
function tcp_register_saleable_post_type( $saleable_post_type ) {
	global $thecartpress;
	$thecartpress->register_saleable_post_type( $saleable_post_type );
}

/**
 * Returns true if a taxonomy has saleable post types
 * @since 1.1.6
 */
function tcp_is_saleable_taxonomy( $taxonomy ) {
	$tax = get_taxonomy( $taxonomy );
	if ( isset( $tax->object_type[0] ) ) return tcp_is_saleable_post_type( $tax->object_type[0] );
	else return false;
}

/**
 * Dynamic Option is a saleable post type, but it's not a product, it's an option
 * @since 1.2.6.1
 */
function tcp_get_product_post_types( $one_more = false ) {
	$product_post_types = tcp_get_saleable_post_types( $one_more );
	$product_post_types = apply_filters( 'tcp_get_product_post_types', $product_post_types );
	return $product_post_types;
}

//
//Order status template functions
//
/**
 * The order is very important, the first one is greather than the second one...
 * @since 1.2.5.2
 */
function tcp_get_order_status() {
	require_once( TCP_DAOS_FOLDER . 'Orders.class.php' );
	$status_list = array(
		Orders::$ORDER_COMPLETED => array(
			'name'	=> Orders::$ORDER_COMPLETED,
			'label'	=>__( 'Completed', 'tcp' ),
			'show_in_dashboard'		=> true,
			'valid_for_deleting'	=> false,
			'is_completed'			=> true,
		),
		Orders::$ORDER_PROCESSING => array(
			'name'	=> Orders::$ORDER_PROCESSING,
			'label'	=>__( 'Processing', 'tcp' ),
			'show_in_dashboard'		=> true,
			'valid_for_deleting'	=> false,
		),
		Orders::$ORDER_PENDING => array(
			'name'	=> Orders::$ORDER_PENDING,
			'label'	=>__( 'Pending', 'tcp' ),
			'show_in_dashboard'		=> true,
			'valid_for_deleting'	=> false,
		),
		Orders::$ORDER_CANCELLED => array(
			'name'	=> Orders::$ORDER_CANCELLED,
			'label'	=>__( 'Cancelled', 'tcp' ),
			'show_in_dashboard'		=> true,
			'valid_for_deleting'	=> true,
			'is_cancelled'			=> true,
		),
		Orders::$ORDER_SUSPENDED => array(
			'name'	=> Orders::$ORDER_SUSPENDED,
			'label'	=>__( 'Suspended', 'tcp' ),
			'show_in_dashboard'		=> true,
			'valid_for_deleting'	=> true,
		),
	);
	return apply_filters( 'tcp_get_order_status', $status_list );
}

function tcp_is_order_status_valid_for_deleting( $status ) {
	$status_list = tcp_get_order_status();
	if ( isset( $status_list[$status] ) && isset( $status_list[$status]['valid_for_deleting'] ) && $status_list[$status]['valid_for_deleting'] )
		return true;
	return false;
}

function tcp_get_cancelled_order_status() {
	$status_list = tcp_get_order_status();
	foreach( $status_list as $status )
		if ( isset( $status['is_cancelled'] ) && $status['is_cancelled'] )
			return $status['name'];
	return 'CANCELLED';
}

function tcp_get_completed_order_status() {
	$status_list = tcp_get_order_status();
	foreach( $status_list as $status )
		if ( isset( $status['is_completed'] ) && $status['is_completed'] )
			return $status['name'];
	return 'COMPLETED';
}

function tcp_get_pending_order_status() {
	$status_list = tcp_get_order_status();
	foreach( $status_list as $status )
		if ( isset( $status['is_pending'] ) && $status['is_pending'] )
			return $status['name'];
	return 'PENDING';
}

function tcp_get_processing_order_status() {
	$status_list = tcp_get_order_status();
	foreach( $status_list as $status )
		if ( isset( $status['is_processing'] ) && $status['is_processing'] )
			return $status['name'];
	return 'PROCESSING';
}

/**
 * Returns true if stauts 1 is greather than status 2
 * @since 1.2.5.2
 */
function tcp_is_greather_status( $status_1, $status_2 ) {
	$status = tcp_get_order_status();
	foreach( $status as $id => $status )
		if ( $id == $status_1 ) return true;
		elseif ( $id == $status_2 ) return false;
	return false;
}

/**
 * Returns status label
 * @since 1.2.6
 */
function tcp_get_status_label( $status ) {
	$status_list = tcp_get_order_status();
	foreach( $status_list as $item )
		if ( $item['name'] == $status )
			return $item['label'];
}
//
// End Order status functions templates
//

//
//Product types
//
function tcp_get_product_types( $no_one = false, $no_one_desc = '' ) {
	$types = array();
	if ( $no_one ) $types[''] = array( 'label' => $no_one_desc != '' ? $no_one_desc : __( 'No one', 'tcp' ) );
	$types['SIMPLE'] = array(
		'label'	=> __( 'Simple', 'tcp' ),
	);
	return apply_filters( 'tcp_get_product_types', $types );
}
//
//End product types
//

//
// Roles
//
function tcp_get_default_roles() {
	$default_roles = array( 'super-admin', 'administrator', 'editor', 'author', 'contributor', 'subscriber' );
	return apply_filters( 'tcp_get_default_roles', $default_roles );
}

//
// Utils and Tools
//

/**
 * Returns the current term
 * @since 1.2.5
 */
function tcp_get_current_taxonomy() {
	return get_query_var( 'taxonomy' );
}

/**
 * Returns the current term
 * @since 1.2.5
 */
function tcp_get_current_term() {
	return get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
}

/**
 * Selected in a multiple select control
 */
function tcp_selected_multiple( $values, $value, $echo = true ) {
	if ( ! is_array( $values ) ) $values = array( $values );
	if ( in_array( $value, $values ) )
		if ( $echo )
			echo ' selected="true"';
		else
			return ' selected="true"';
}

/**
 * Checked in a multiple select control
 */
function tcp_checked_multiple( $values, $value, $echo = true ) {
	if ( is_array( $values ) && count( $values ) > 0 && in_array( $value, $values ) ) {
		if ( $echo ) echo ' checked="true"';
		else return ' checked="true"';
	}
}

/**
 * Formats a float number to a string number to show in the screen
 * 
 * @param $number
 * @param $decimals
 * @since 1.0.7
 * @deprecated
 */
function tcp_number_format( $number, $decimals = 2 ) {
	return tcp_format_number( $number, $decimals );
}

/**
 * Formats a float number to a string number to show in the screen
 * 
 * @param $number
 * @param $decimals
 * @since 1.2.9
 */
function tcp_format_number( $number, $decimals = 2 ) {
	global $thecartpress;
	if ( ! $thecartpress ) return;
	return number_format( (float)$number, $decimals, stripslashes( $thecartpress->get_setting( 'decimal_point', '.' ) ), stripslashes( $thecartpress->get_setting( 'thousands_separator', ',' ) ) );
}

/**
 * Converts a typed number into a float number
 * @since 1.0.7
 */
function tcp_input_number( $input ) {
	global $thecartpress;
	if ( ! $thecartpress ) return;
	$aux = str_replace( stripslashes( $thecartpress->get_setting( 'thousands_separator', ',' ) ), '', $input );
	$aux = str_replace( stripslashes( $thecartpress->get_setting( 'decimal_point', '.' ) ), '.', $aux );
	return (float)$aux;
}

/**
 * Returns a date into the current format
 * @param $date, date in format y-m-d (default format)
 * @since 1.2.9
 */
function tcp_format_date( $date, $format = false ) {
	if ( $format === false ) {
		global $thecartpress;
		if ( ! $thecartpress ) return;
		$format = $thecartpress->get_setting( 'date_format', 'y-m-d' );
	}
	$date = DateTime::createFromFormat( 'Y-m-d', $date );
	return $date->format( $format );
}

/**
 * Converts a typed date (format YYYY-MM-DD) into a PHP date
 * @since 1.1.8
 */
function tcp_input_date( $input, $separator = '-' ) {
	if ( $input ) {
		list( $y, $m, $d ) = explode( $separator, $input);
		return mktime( 0, 0, 0, $m, $d, $y );
	}
	return false;
}

/**
 * Returns the remote ip
 * @since 1.0.9
 */
function tcp_get_remote_ip() {
	if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '' ) { // for proxy
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		//$proxy = $_SERVER['REMOTE_ADDR'];
		//$host = @gethostbyaddr( $_SERVER['HTTP_X_FORWARDED_FOR'] );
	} else { // for normal user
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '';
		//$host = @gethostbyaddr( $_SERVER['REMOTE_ADDR'] );
	}
	return $ip;
}

function tcp_get_current_url() {
	$path = '';
	$path .= strstr( strtolower( $_SERVER['SERVER_PROTOCOL'] ), '/', true);
	$path .= ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ? 's' : '' ) . '://';
	$path .= $_SERVER['SERVER_NAME'];
	if ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) {
		if ( $_SERVER['SERVER_PORT'] != '443' ) {
			$path .= ':' . $_SERVER['SERVER_PORT'];
		}
	} else {
		if ( $_SERVER['SERVER_PORT'] != '80' ) {
			$path .= ':' . $_SERVER['SERVER_PORT'];
		}
	}
	$path .= $_SERVER['REQUEST_URI'];
	return $path;
}

/**
 * Returns a subfix from a request action
 * For example: if $_REQUEST['action_1'] exists,
 *  tcp_is_request('action'] -> '_1'
 *  tcp_is_request('other_action'] -> false
 */
function tcp_is_request( $name ) {
	foreach( $_REQUEST as $req => $value ) {
		$pos = strpos( $req, $name );
		if ( $pos !== false && $pos == 0 ) {
		//if ( strpos( $req, $action ) !== false ) {
			$index = substr( $req, strlen( $name ) );
			return strlen( $index ) > 0 ?  $index : false;
		}
	}
	return false;
}

/**
 * Returns values from request with the same prefix
 */
function tcp_get_request_array( $name ) {
	$values = array();
	foreach( $_REQUEST as $req => $value ) {
		$pos = strpos( $req, $name );
		if ( $pos !== false && $pos == 0 ) {
			$values[] = $value;
		}
	}
	return $values;
}

/**
 * Creates a select in html format
 * @param $options = array( 'value' => 'title', ...);
 */
function tcp_html_select( $name, $options, $value, $echo = true, $class = '', $id = false ) {
	if ( $id === false ) $id = $name;
	$out = '<select id="' . $id . '" name="' . $name . '"';
	if ( strlen( $class ) > 0 ) $out .= 'class="' . $class . '"';
	$out .= '>' . "\n";
	foreach( $options as $option_value => $option_text )
		$out .= '<option value="' . $option_value . '" ' . selected( $value, $option_value, false ) . '>' . $option_text . '</option>' . "\n";
	$out .= '</select>' . "\n";
	if ( $echo ) echo $out;
	else return $out;
}

/**
 * @since 1.2.0
 */
function tcp_the_cross_selling( $post_id = 0, $echo = true ) {
	if ( $post_id == 0 ) $post_id = get_the_ID();
	require_once( TCP_DAOS_FOLDER . 'OrdersDetails.class.php' );
	return OrdersDetails::getCrossSelling( $post_id );
}

/**
 * @since 1.2.5.2
 */
function tcp_redirect_302( $url ) {
	//Redirect Page
	if ( function_exists( 'status_header' ) ) status_header( 302 );
	header( 'HTTP/1.1 302 Temporary Redirect' );
	header( 'Location:' . $url );
	exit();
}

/**
 * Loads a template from an specific location. If not, it attempts to load it from the theme
 *
 * @since 1.2.6
 * @uses get_template_part
 */
function tcp_get_template_part( $path, $slug, $name = '' ) {
	$template = $path . '/' . $slug;
	$template .= ( $name != '' ) ? '-' . $name : '';
	$template .= '.php';
	if ( file_exists( $template ) ) require( $template );
	else get_template_part( $slug, $name );
}

/*
 * Loads the default TheCartPress loop
 * First, it attempts to load the file from the theme (child or parent theme),
 * if not it loads the loop availabe in the core
 *
 * @since 1.3.1
 * @uses locate_template, tcp_get_template_part, plugins_url, apply_filters (called using 'tcp_the_loop')
 *
function tcp_the_loop() {
	if ( ! locate_template( 'loop-tcp-grid.php', true ) ) {
		$url = plugins_url( dirname( __FILE__ ), 'themes-templates/loop-tcp-grid.php';
		tcp_get_template_part( apply_filters( 'tcp_the_loop', $url ) );
	}
}*/

/**
 * @since 1.2.7
 */
function tcp_debug_trace( $object = false, $args = false ) {
	$traces = debug_backtrace(); ?>
	<ul>
	<?php foreach( $traces as $id => $trace ) : ?>
		<li><?php printf( '%s -> %s: line %s (%s)', isset( $trace['class'] ) ? $trace['class'] : '', $trace['function'], isset( $trace['line'] ) ? $trace['line'] : '', isset( $trace['file'] ) ? $trace['file'] : '' ); ?></li>
	<?php endforeach; ?>
	</ul>
<?php }

function tcp_update_premium_plugin( $plugin_file ) {
	require_once ( TCP_CLASSES_FOLDER . 'AutoUpdate.class.php' );
	if ( class_exists( 'TCPAutoUpdate' ) ) new TCPAutoUpdate( $plugin_file );
}

/**
 * @since 1.2.9
 */
function tcp_session_start( $time = 3600, $session_name = 'tcp' ) {
	/*if ( session_id() == $session_name ) return;
	session_set_cookie_params( $time );
	session_name( $session_name );*/
	if ( ! session_id() ) session_start();
/*	if ( isset( $_COOKIE[$session_name] ) ) {
		setcookie( $session_name, $_COOKIE[$session_name], time() + $time, null, null, true );
		setcookie( $session_name, $_COOKIE[$session_name], time() + $time, null, null, false );
	}*/
}

function tcp_return_jsonp( $params ) {

//	exit( $_REQUEST['callback'] . '(' . json_encode( $params ) . ')' );
	//if ( is_array( $params ) ) $params = array( $params );
//var_dump( json_encode( $params ) );
	exit( json_encode( $params ) );
}
?>