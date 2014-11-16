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

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'TCPShippingMethodsBox' ) ) :

require_once( TCP_CHECKOUT_FOLDER . 'TCPCheckoutBox.class.php' );

class TCPShippingMethodsBox extends TCPCheckoutBox {
	private $errors = array();
	private $applicable_sending_plugins = array();
	private $shipping_sorting = array();

	function get_title() {
		//return __( 'Sending methods', 'tcp' );
		if ( isset( $_SESSION['tcp_checkout']['shipping_methods'] ) ) {
			$plugin = explode( '#', $_SESSION['tcp_checkout']['shipping_methods']['shipping_method_id'] );
			$object = new $plugin[0]();
			return sprintf( __( 'Sending method: %s', 'tcp' ), $object->getCheckoutMethodLabel( $plugin[1], '', false ) );
		} else {
			return __( 'Sending methods', 'tcp' );
		}
	}

	function get_class() {
		return 'sending_layer';
	}

	function get_name() {
		return 'shipping-methods';
	}

	function is_hidden() {
		$settings = get_option( 'tcp_' . get_class( $this ), array() );
		$hide_box = isset( $settings['hide_box'] ) ? $settings['hide_box'] : false;
		if ( count( $this->applicable_sending_plugins ) == 1 ) {
			$hidden_if_unique = isset( $settings['hidden_if_unique'] ) ? $settings['hidden_if_unique'] : false;
			if ( $hidden_if_unique && $hide_box) return true;
		} elseif ( count( $this->applicable_sending_plugins ) == 0 && $hide_box ) {
			return true;
		}
		return false;
	}

	function before_action() {
		$shoppingCart = TheCartPress::getShoppingCart();		
		if ( $shoppingCart->isDownloadable() ) {
			unset( $_SESSION['tcp_checkout']['shipping_methods'] );
			return 1;
		} else {
			$selected_shipping_address = isset( $_SESSION['tcp_checkout']['shipping']['selected_shipping_address'] ) ? $_SESSION['tcp_checkout']['shipping']['selected_shipping_address'] : false;
			if ( $selected_shipping_address == 'new' ) {
				$shipping_country = $_SESSION['tcp_checkout']['shipping']['shipping_country_id'];
			} elseif ( $selected_shipping_address == 'BIL' ) {
				if ( $_SESSION['tcp_checkout']['billing']['selected_billing_address'] == 'new' ) {
					$shipping_country = $_SESSION['tcp_checkout']['billing']['billing_country_id'];
				} else { //if ( $_SESSION['tcp_checkout']['billing']['selected_billing_addres'] == 'Y' ) {
					$shipping_country = Addresses::getCountryId( $_SESSION['tcp_checkout']['billing']['selected_billing_id'] );
				}
			} else { //if ( $selected_billing_address == 'Y' ) {

				$shipping_country = Addresses::getCountryId( $_SESSION['tcp_checkout']['shipping']['selected_shipping_id'] );
			}
			if ( ! $shipping_country ) $shipping_country = '';
			$this->applicable_sending_plugins = tcp_get_applicable_shipping_plugins( $shipping_country, $shoppingCart );
			//Get box settings
			$settings = get_option( 'tcp_' . get_class( $this ), array() );
			//If hidden if unique (not display the box opened) and the numbers of applucable methos is one
			$hidden_if_unique = isset( $settings['hidden_if_unique'] ) ? $settings['hidden_if_unique'] : false;
			if ( $hidden_if_unique && count( $this->applicable_sending_plugins ) == 1 ) {
				$plugin_data = reset( $this->applicable_sending_plugins );
				$tcp_plugin = $plugin_data['plugin'];
				$instance = $plugin_data['instance'];
				$plugin_name = get_class( $tcp_plugin );
				$_SESSION['tcp_checkout']['shipping_methods'] = array(
					'shipping_method_id' => $plugin_name . '#' . $instance,
				);
				return 1;
			} else {
				$this->shipping_sorting = isset( $settings['sorting'] ) ? $settings['sorting'] : '';
				return 0;
			}
		}
	}

	function after_action() {
		if ( ! isset( $_REQUEST['shipping_method_id'] ) )
			$this->errors['shipping_method_id'] = __( 'You must select a shipping method', 'tcp' );
		if ( count( $this->errors ) > 0 ) {
			return false;
		} else {
			$shipping_method = array(
				'shipping_method_id' => isset( $_REQUEST['shipping_method_id'] ) ? $_REQUEST['shipping_method_id'] : 0,
			);
			$_SESSION['tcp_checkout']['shipping_methods'] = $shipping_method;
			return apply_filters( 'tcp_after_shipping_methods_box', true );
		}
	}

	function show_config_settings() { ?>
		<style>
		#tcp_shipping_list {
			list-style-type: none;
			margin: 0;
			padding: 0;
			width: 60%;
		}
		#tcp_shipping_list li { 
			margin: 0 3px 3px 3px;
			padding: 0.4em;
			padding-left: 1.5em;
			font-size: 1.1em;
			height: 18px;
			border: 1px solid #BBBBBB;
			padding: 2px;
			background: url("../images/white-grad.png") repeat-x scroll left top #F2F2F2;
			text-shadow: 0 1px 0 #FFFFFF;
			-moz-box-sizing: content-box;
			border-radius: 5px 0px 0px 0px;
			cursor: move;
		}
		</style>
		<script>
		jQuery(document).ready(function() {
			jQuery('#tcp_shipping_list').sortable();
			jQuery('#tcp_shipping_list').disableSelection();
			
			jQuery('#tcp_save_TCPShippingMethodsBox').click(function(e) {
				var vals = '';
				jQuery('li.tcp_shipping_item').each(function(index) {
					vals += jQuery(this).attr('id') + ',';
				});
				vals = vals.slice(0, -1);
				jQuery('#tcp_shipping_sorting').val(vals);
			});
		});
		</script>
		<p><?php _e( 'Drag the Shippings plugins to sort them', 'tcp' ); ?></p>
		<?php $settings = get_option( 'tcp_' . get_class( $this ), array() );
		$shipping_sorting = isset( $settings['sorting'] ) ? $settings['sorting'] : false; ?>
		<input type="hidden" name="tcp_shipping_sorting" id="tcp_shipping_sorting" value="" />
		<ul id="tcp_shipping_list">
		<?php //TODO If is saved before activate a new shipping method. The new one is not displayed
		global $tcp_shipping_plugins;
		if ( is_array( $shipping_sorting ) && count( $shipping_sorting ) > 1 ) {
			foreach( $shipping_sorting as $id ) {
				if ( isset( $tcp_shipping_plugins[$id] ) ) {
					$tcp_shipping_plugin = $tcp_shipping_plugins[$id]; ?>
				<li class="tcp_shipping_item" id="<?php echo $id; ?>"><?php echo $tcp_shipping_plugin->getName(); ?></li>
			<?php }
			}
		} else {
			foreach( $tcp_shipping_plugins as $id => $tcp_shipping_plugin ) { ?>
				<li class="tcp_shipping_item" id="<?php echo $id; ?>"><?php echo $tcp_shipping_plugin->getName(); ?></li>
		<?php }
		} ?>
		</ul>
		<?php $tcp_hidden_if_unique	= isset( $settings['hidden_if_unique'] ) ? $settings['hidden_if_unique'] : false;
		$tcp_hide_box = isset( $settings['hide_box'] ) ? $settings['hide_box'] : false; ?>
		<ul>
			<li><label><input type="checkbox" value="yes" name="tcp_hidden_if_unique" <?php checked( $tcp_hidden_if_unique ); ?>/> <?php _e( 'Hide box, displaying only header, if only one method is applicable', 'tcp' ); ?></label></li>
			<li><label><input type="checkbox" value="yes" name="tcp_hide_box" <?php checked( $tcp_hide_box ); ?>/> <?php _e( 'Hide box if only one method is applicable', 'tcp' ); ?></label></li>
		</ul>
		<?php do_action( 'tcp_shipping_methods_box_show_config_settings', $settings ); ?>
		<?php return true;
	}

	function save_config_settings() {
		$settings = array(
			'sorting' => isset( $_REQUEST['tcp_shipping_sorting'] ) ? explode( ',', $_REQUEST['tcp_shipping_sorting'] ) : '',
			'hidden_if_unique' => isset( $_REQUEST['tcp_hidden_if_unique'] ),
			'hide_box' => isset( $_REQUEST['tcp_hide_box'] ),
		);
		$settings = apply_filters( 'tcp_shipping_methods_box_save_config_settings', $settings );
		update_option( 'tcp_' . get_class( $this ), $settings );
		return true;
	}

	function show() {
		$shoppingCart = TheCartPress::getShoppingCart();
		$selected_shipping_address = isset( $_SESSION['tcp_checkout']['shipping']['selected_shipping_address'] ) ? $_SESSION['tcp_checkout']['shipping']['selected_shipping_address'] : false;
		if ( $selected_shipping_address == 'new' ) {
			$shipping_country = $_SESSION['tcp_checkout']['shipping']['shipping_country_id'];
		} elseif ( $selected_shipping_address == 'BIL' ) {
			if ( $_SESSION['tcp_checkout']['billing']['selected_billing_address'] == 'new' ) {
				$shipping_country = $_SESSION['tcp_checkout']['billing']['billing_country_id'];
			} else { //if ( $_SESSION['tcp_checkout']['billing']['selected_billing_addres'] == 'Y' ) {
				$shipping_country = Addresses::getCountryId( $_SESSION['tcp_checkout']['billing']['selected_billing_id'] );
			}
		} else { //if ( $selected_billing_address == 'Y' ) {
			$shipping_country = Addresses::getCountryId( $_SESSION['tcp_checkout']['shipping']['selected_shipping_id'] );
		}
		if ( ! $shipping_country ) $shipping_country = '';
		if ( is_array( $this->shipping_sorting ) && count( $this->shipping_sorting ) > 0 ) {
			usort( $this->applicable_sending_plugins, array( $this, 'sort_plugins' ) );
		} ?>
		<div class="checkout_info clearfix" id="sending_layer_info"><?php
		if ( is_array( $this->applicable_sending_plugins ) && count( $this->applicable_sending_plugins ) > 0 ) : ?>
			<ul><?php
			$shipping_method_id = isset( $_SESSION['tcp_checkout']['shipping_methods']['shipping_method_id'] ) ? $_SESSION['tcp_checkout']['shipping_methods']['shipping_method_id'] : false;
			$exist_id = false;
			foreach( $this->applicable_sending_plugins as $plugin_data ) {
				$tcp_plugin = $plugin_data['plugin'];
				$instance = $plugin_data['instance'];
				$plugin_name = get_class( $tcp_plugin );
				$id = $plugin_name . '#' . $instance;
				if ( $shipping_method_id == $id ) {
					$exist_id = true;
					break;
				}
			}
			if ( ! $exist_id ) $shipping_method_id = false;
			foreach( $this->applicable_sending_plugins as $plugin_data ) :
				$tcp_plugin = $plugin_data['plugin'];
				$instance = $plugin_data['instance'];
				$plugin_name = get_class( $tcp_plugin );
				$plugin_value = $plugin_name . '#' . $instance;
				if ( ! $shipping_method_id ) $shipping_method_id = $plugin_value; ?>
				<li>
					<label for="<?php echo $plugin_name;?>_<?php echo $instance;?>">
					<input type="radio" id="<?php echo $plugin_name;?>_<?php echo $instance;?>" name="shipping_method_id" value="<?php echo $plugin_value;?>" <?php checked( $plugin_value, $shipping_method_id );?> />
					<span class="tcp_shipping_title_<?php echo $plugin_name;?>"><?php echo $tcp_plugin->getCheckoutMethodLabel( $instance, $shipping_country, $shoppingCart );?></span>
					</label>
					<div class="tcp_plugin_notice tcp_plugin_notice_<?php echo $plugin_name; ?>"><?php tcp_do_template( 'tcp_shipping_plugins_' . $plugin_name ); ?></div>
				</li>
			<?php endforeach;?>
			</ul>
			<?php if ( isset( $this->errors['shipping_method_id'] ) ) : ?><br/><span class="error"><?php echo $this->errors['shipping_method_id'];?></span><?php endif;?>
		<?php else : ?>
			<?php _e( 'There is not applicable methods', 'tcp' ); ?>
		<?php endif;
		do_action( 'tcp_checkout_sending' );?>
		</div><!-- sending_layer_info --><?php
		return true;
	}

	function sort_plugins( $a, $b ) {
		$k_a = $a['id'];
		$k_b = $b['id'];
		$pos_a = array_search( $k_a, $this->shipping_sorting );
		$pos_b = array_search( $k_b, $this->shipping_sorting );
		if ( $pos_a === false ) return 1;
		elseif ( $pos_b === false ) return -1;
		elseif ( $pos_a < $pos_b ) return -1;
		elseif ( $pos_a == $pos_b ) return 0;
		else return 1;
	}
}
endif; // class_exists check