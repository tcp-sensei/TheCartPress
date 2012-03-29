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

/**
 * All the checkout plugins must implement this class
 */
require_once( WP_PLUGIN_DIR . '/thecartpress/templates/tcp_template_template.php' );

class TCP_Plugin {

	function isForOtherPayments() {
		return false;
	}

	/**
	 * Returns the title of the plugin.
	 * It's used to display the name of the plugin in the admin pages
	 * Must be implemented
	 */
	function getTitle() {
	}

	/**
	 * Returns the name of the plugin.
	 * It's used by the orders
	 */
	function getName() {
		return $this->getTitle();
	}

	/**
	 * Returns the description of the plugin
	 * Must be implemented
	 */
	function getDescription() {
	}

	/**
	 * Shows the data that the plugin need to be edited
	 * Must be implemented
	 */
	function showEditFields( $data, $instance = 0 ) {
	}

	/**
	 * This functions is run when the edut plugin page is saved
	 * Must be implemented
	 */
	function saveEditFields( $data, $instance = 0 ) {
		return $data;
	}

	/**
	 * Returns if the plugin is applicable
	 * Must be implemented
	 */
	function isApplicable( $shippingCountry, $shoppingCart, $data ) {
		return true;
	}

	/**
	 * Returns if the plugin allows instances
	 * @sincve 1.1.8
	 */
	function isInstanceable() {
		return true;
	}

	/**
	 * Returns the text label to show in the checkout.
	 * Must be implemented
	 */
	function getCheckoutMethodLabel( $instance, $shippingCountry, $shoppingCart ) {
	}

	/**
	 * Returns the cost of the service
	 * Must be implemented
	 */
	function getCost( $instance, $shippingCountry, $shoppingCart ) {
		return 0;
	}

	/**
	 * Shows the button or the notice after the orders have been saved
	 *
	 * Must be implemented only for payment methods
	 */
	function showPayForm( $instance, $shippingCountry, $shoppingCart, $order_id ) {
	}

	function __construct() {
	}
}

$tcp_shipping_plugins = array();
$tcp_payment_plugins = array();

/**
 * Registers a shipping plugin
 */
function tcp_register_shipping_plugin( $class_name ) {
	global $tcp_shipping_plugins;
	$obj = new $class_name();
	$tcp_shipping_plugins['shi_' . $class_name] = $obj;
	tcp_add_template_class( 'tcp_shipping_plugins_' . $class_name, sprintf( __( 'This notice will be displayed in the checkout process and added in the email to the customer with the info related to %s', 'tcp' ), $obj->getName() ) );
}

/**
 * Registers a payment plugin
 */
function tcp_register_payment_plugin( $class_name ) {
	global $tcp_payment_plugins;
	$obj = new $class_name();
	$tcp_payment_plugins['pay_' . $class_name] = $obj;
	tcp_add_template_class( 'tcp_payment_plugins_' . $class_name, sprintf( __( 'This notice will be displayed in the checkout process and added in the email to the customer with the info related to %s', 'tcp' ), $obj->getName() ) );
}

/**
 * Returns the plugin object from a given plugin_id
 */
function tcp_get_plugin( $plugin_id ) {
	global $tcp_shipping_plugins;
	global $tcp_payment_plugins;

	if ( isset( $tcp_shipping_plugins[$plugin_id] ) )
		return $tcp_shipping_plugins[$plugin_id];
	elseif ( isset( $tcp_payment_plugins[$plugin_id] ) )
		return $tcp_payment_plugins[$plugin_id];
	else return null;
}

function tcp_get_plugin_type( $plugin_id ) {
	global $tcp_shipping_plugins;
	global $tcp_payment_plugins;

	if ( isset( $tcp_shipping_plugins[$plugin_id] ) )
		return 'shipping';
	elseif ( isset( $tcp_payment_plugins[$plugin_id] ) )
		return 'payment';
	else return '';
}

function tcp_get_applicable_shipping_plugins( $shipping_country, $shoppingCart ) {
	if ( $shoppingCart->isDownloadable() )
		return array();
	else
		$shipping_plugins = tcp_get_applicable_plugins( $shipping_country, $shoppingCart );
		//FreeTrans
		//var_dump( $shipping_plugins );
		/*foreach( $shipping_plugins as $shipping_plugin )
			if ( get_class( $shipping_plugin['plugin'] ) == 'FreeTrans' ) {
				$shipping_plugins = array ( $shipping_plugin );
				break;
			}*/
		return $shipping_plugins;
}

function tcp_get_applicable_payment_plugins( $shipping_country, $shoppingCart ) {
	return tcp_get_applicable_plugins( $shipping_country, $shoppingCart, 'payment' );
}

function tcp_get_applicable_plugins( $shipping_country, $shoppingCart, $type = 'shipping' ) {
	if ( $type == 'shipping' ) {
		global $tcp_shipping_plugins;
		$tcp_plugins = $tcp_shipping_plugins;
	} else {
		global $tcp_payment_plugins;
		$tcp_plugins = $tcp_payment_plugins;
	}
	$isDownloadable = $shoppingCart->isDownloadable();
	$applicable_plugins = array();
	$applicable_for_country = false;
	foreach( $tcp_plugins as $plugin_id => $plugin ) {
		$plugin_data = get_option( 'tcp_plugins_data_' . $plugin_id );
		if ( is_array( $plugin_data ) && count( $plugin_data ) > 0 ) {
			$applicable_instance_id = -1;
			$applicable_for_country = false;
			foreach( $plugin_data as $instance_id => $instance ) {
				if ( $instance['active'] ) {
					$not_for_downloadable = isset( $instance['not_for_downloadable'] ) ? $instance['not_for_downloadable'] : false;
					if ( ! $isDownloadable || ( $isDownloadable && ! $not_for_downloadable ) ) {
						$all_countries = isset( $instance['all_countries'] ) ? $instance['all_countries'] == 'yes' : false;
						if ( $all_countries ) {
							$applicable_instance_id = $instance_id;
							$data = $plugin_data[$applicable_instance_id];
							if ( $plugin->isApplicable( $shipping_country, $shoppingCart, $data ) ) {
								$applicable_plugins[] = array(
									'id'		=> $plugin_id,
									'plugin'	=> $plugin,
									'instance'	=> $applicable_instance_id,
								);
							}
						} else {
							$countries = isset( $instance['countries'] ) ? $instance['countries'] : array();
							if ( in_array( $shipping_country, $countries ) ) {
								$applicable_instance_id = $instance_id;
								$applicable_for_country = true;
								$data = $plugin_data[$applicable_instance_id];
								if ( $plugin->isApplicable( $shipping_country, $shoppingCart, $data ) ) {
									$applicable_plugins[] = array(
										'id'		=> $plugin_id,
										'plugin'	=> $plugin,
										'instance'	=> $applicable_instance_id,
									);
								}
							}
						}
					}
				}
			}
			/*if ( $applicable_instance_id > -1 ) {
				$data = $plugin_data[$applicable_instance_id];
				if ( $plugin->isApplicable( $shipping_country, $shoppingCart, $data ) )
					$applicable_plugins[] = array(
						'id'		=> $plugin_id,
						'plugin'	=> $plugin,
						'instance'	=> $applicable_instance_id,
					);
			}*/
		}
	}
	if ( isset( $applicable_for_country ) && $applicable_for_country )
		foreach( $applicable_plugins as $id => $plugin_instance ) {
			$data = tcp_get_shipping_plugin_data( get_class( $plugin_instance['plugin'] ), $plugin_instance['instance'] );
			$all_countrie =	isset( $data['all_countries'] ) ? $data['all_countries'] == 'yes' : false;
			if ( $all_countrie ) unset( $applicable_plugins[$id] );
		}
	return $applicable_plugins;
}

function tcp_get_shipping_plugin_data( $plugin_name, $instance = 0 ) {
	return tcp_get_plugin_data( 'shi_' . $plugin_name, $instance );
}

function tcp_get_payment_plugin_data( $plugin_name, $instance = 0 ) {
	return tcp_get_plugin_data( 'pay_' . $plugin_name, $instance );
}

function tcp_get_plugin_data( $plugin_id, $instance = -1 ) {
	$plugin_data = get_option( 'tcp_plugins_data_' . $plugin_id );
	if ( $instance == -1 )
		return $plugin_data;
	else
		return $plugin_data[$instance];
}
?>
