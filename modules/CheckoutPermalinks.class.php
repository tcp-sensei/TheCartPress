<?php
/**
 * Checkout Permalinks
 *
 * Adds a permalink to each step of the checkout. This feature is not compatible with ajax.
 * By default it's disabled, and must be activated modifying manage_modules.php
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
if ( !defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'TCPCheckoutPermalinks' ) ) {

if ( get_option( 'permalink_structure' ) == '' ) return;

define( 'TCP_CHECKOUT', 'checkout' );
define( 'TCP_CHECKOUT_PURCHASE', 'purchase' );
define( 'TCP_CHECKOUT_PURCHASE_OK', 'purchase-ok' );
define( 'TCP_CHECKOUT_PURCHASE_KO', 'purchase-ko' );

class TCPCheckoutPermalinks {

	function __construct() {
		add_action( 'init'							, array( $this, 'init' ), 9 );
		add_filter( 'rewrite_rules_array'			, array( $this, 'rewrite_rules_array' ) );
		add_filter( 'tcp_get_the_checkout_ok_url'	, array( $this, 'tcp_get_the_checkout_ok_url' ) );
		add_filter( 'tcp_get_the_checkout_ko_url'	, array( $this, 'tcp_get_the_checkout_ko_url' ) );
		register_activation_hook( __FILE__			, array( $this, 'activate_plugin' ) );
		register_deactivation_hook( __FILE__		, array( $this, 'deactivate_plugin' ) );
	}

	function init() {
		add_filter( 'tcp_the_checkout_url'	, array( $this, 'tcp_the_checkout_url' ) );
		add_filter( 'query_vars'			, array( $this, 'query_vars' ) );
		add_action( 'template_redirect'		, array( $this, 'template_redirect' ) );
	}

	function tcp_the_checkout_url( $url ) {
		return get_site_url() . '/' . TCP_CHECKOUT . '/';
	}

	function tcp_get_the_checkout_ok_url( $url ) {
		$url = $this->tcp_the_checkout_url( $url );
		return $url . TCP_CHECKOUT_PURCHASE_OK;
	}

	function tcp_get_the_checkout_ko_url( $url ) {
		$url = $this->tcp_the_checkout_url( $url );
		return $url . TCP_CHECKOUT_PURCHASE_KO;
	}

	function rewrite_rules_array( $wp_rules ) {
		$base = TCP_CHECKOUT;
		$api_rules = array(
			"$base\$"		=> 'index.php?' . TCP_CHECKOUT . '=init',
			"$base/(.+)\$"	=> 'index.php?' . TCP_CHECKOUT . '=$matches[1]'
		);
		return array_merge( $api_rules, $wp_rules );
	}

	function query_vars( $vars ) {
		$vars[] = TCP_CHECKOUT;
		return $vars;
	}

	function template_redirect() {
		global $wp_query;
		if ( isset( $wp_query->query_vars[TCP_CHECKOUT] ) ) {
			$permalink = $wp_query->query_vars[TCP_CHECKOUT];
			if ( TCP_CHECKOUT_PURCHASE_OK == $permalink ) {
				$_REQUEST['tcp_checkout'] = 'ok';
			} elseif ( TCP_CHECKOUT_PURCHASE_KO == $permalink ) {
				$_REQUEST['tcp_checkout'] = 'ko';
			} else {
				$step = TCPCheckoutManager::get_step_by_permalink( $permalink );
				if ( $step !== false ) {
					if ( isset( $_REQUEST['tcp_continue'] ) ) $step = $step > 1 ? $step - 1 : 0;
					$_REQUEST['tcp_step'] = $step;
				}
			}
			global $wp_query;
			$wp_query = new WP_Query( 'page_id=' . tcp_get_current_id( get_option( 'tcp_checkout_page_id' ), 'page' ) );
			$template_name = get_page_template();
			include( $template_name );
			exit();
		}
	}

	function activate_plugin() {
		global $wp_rewrite;
		add_filter( 'rewrite_rules_array', array( &$this, 'rewrite_rules_array' ) );
		$wp_rewrite->flush_rules();
	}

	function deactivate_plugin() {
		global $wp_rewrite;
		remove_filter( 'rewrite_rules_array', 'rewrite_rules_array' );
		$wp_rewrite->flush_rules();
	}
}

new TCPCheckoutPermalinks();
} // class_exists check