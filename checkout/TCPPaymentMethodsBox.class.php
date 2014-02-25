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
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'TCPPaymentMethodsBox' ) ) :

require_once( TCP_CHECKOUT_FOLDER . 'TCPCheckoutBox.class.php' );
require_once( TCP_DAOS_FOLDER . 'Addresses.class.php' );

class TCPPaymentMethodsBox extends TCPCheckoutBox {
	private $errors = array();
	private $applicable_plugins = array();
	private $payment_sorting = array();

	function get_title() {
		if ( isset( $_SESSION['tcp_checkout']['payment_methods'] ) ) {
			$plugin = explode( '#', $_SESSION['tcp_checkout']['payment_methods']['payment_method_id'] );
			if ( class_exists( $plugin[0] ) ) {
				$object = new $plugin[0]();
				return sprintf( __( 'Payment method: %s', 'tcp' ), $object->getCheckoutMethodLabel( $plugin[1], '', false ) );
			} else {
				unset( $_SESSION['tcp_checkout']['payment_methods'] );
				return __( 'Payment methods', 'tcp' );
			}
		} else {
			return __( 'Payment methods', 'tcp' );
		}
	}

	function get_class() {
		return 'payment_layer';
	}

	function get_name() {
		return 'payment-methods';
	}

	function is_hidden() {
		$settings = get_option( 'tcp_' . get_class( $this ), array() );
		$hide_box = isset( $settings['hide_box'] ) ? $settings['hide_box'] : false;
		if ( count( $this->applicable_plugins ) == 1 ) {
			$hidden_if_unique = isset( $settings['hidden_if_unique'] ) ? $settings['hidden_if_unique'] : false;	
			if ( $hidden_if_unique && $hide_box) return true;
		} elseif ( count( $this->applicable_plugins ) == 0 && $hide_box ) {
			return true;
		}
		return false;
	}

	function isRecoveryBox() {
		return true;
	}

	function before_action() {
		$shoppingCart = TheCartPress::getShoppingCart();
		$billing_country = '';
		$selected_billing_address = isset( $_SESSION['tcp_checkout']['billing']['selected_billing_address'] ) ? $_SESSION['tcp_checkout']['billing']['selected_billing_address'] : false;
		if ( $selected_billing_address == 'new' ) {
			$billing_country = $_SESSION['tcp_checkout']['billing']['billing_country_id'];
		} elseif ( isset( $_SESSION['tcp_checkout']['billing']['selected_billing_id'] )) { //if ( $selected_billing_address == 'Y' ) {
			$billing_country = Addresses::getCountryId( $_SESSION['tcp_checkout']['billing']['selected_billing_id'] );
		} else {
			$billing_country = '';
		}
		$this->applicable_plugins = tcp_get_applicable_payment_plugins( $billing_country, $shoppingCart );
		$settings = get_option( 'tcp_' . get_class( $this ), array() );
		$hidden_if_unique = isset( $settings['hidden_if_unique'] ) ? $settings['hidden_if_unique'] : false;
		if ( $hidden_if_unique && count( $this->applicable_plugins ) == 1 ) {
			$plugin_data = reset( $this->applicable_plugins );
			$tcp_plugin = $plugin_data['plugin'];
			$instance = $plugin_data['instance'];
			$plugin_name = get_class( $tcp_plugin );
			$_SESSION['tcp_checkout']['payment_methods'] = array(
				'payment_method_id' => $plugin_name . '#' . $instance,
			);
			return 1;
		} else {
			$this->payment_sorting = isset( $settings['sorting'] ) ? $settings['sorting'] : '';
			return 0;
		}
	}

	function after_action() {
		if ( ! isset( $_REQUEST['payment_method_id'] ) )
			$this->errors['payment_method_id'] = __( 'You must select a payment method', 'tcp' );
		if ( count( $this->errors ) > 0 ) {
			return false;
		} else {
			$payment_method = array(
				'payment_method_id' => isset( $_REQUEST['payment_method_id'] ) ? $_REQUEST['payment_method_id'] : 0,
			);
			$_SESSION['tcp_checkout']['payment_methods'] = $payment_method;
			return apply_filters( 'tcp_after_payment_methods_box', true );
		}
	}

	function show_config_settings() {?>
		<style>
		#tcp_payment_list {
			list-style-type: none;
			margin: 0;
			padding: 0;
			width: 60%;
		}
		#tcp_payment_list li { 
			margin: 0 3px 3px 3px;
			padding: 0.4em;
			padding-left: 1.5em;
			font-size: 1.1em;
			height: 18px;
			border: 1px solid #BBBBBB;
			padding: 2px;
			background: url("../images/white-grad.png") repeat-x scroll left top #F2F2F2;
			text-shadow: 0 1px 0 #FFFFFF;
			cursor: move;
		}
		</style>
		<script>
		jQuery( function() {
			jQuery( '#tcp_payment_list' ).sortable();
			jQuery( '#tcp_payment_list' ).disableSelection();
			jQuery( '#tcp_save_TCPPaymentMethodsBox' ).click( function( e ) {
				var vals = '';
				jQuery( 'li.tcp_payment_item' ).each( function( index ) {
					vals += jQuery( this).attr( 'id' ) + ',';
				});
				vals = vals.slice( 0, -1 );
				jQuery( '#tcp_payment_sorting' ).val( vals );
			});
		});
		</script>
		<p><?php _e( 'Drag the payments plugins to sort them', 'tcp' ); ?></p>
		<?php $settings = get_option( 'tcp_' . get_class( $this ), array() );
		$payment_sorting = isset( $settings['sorting'] ) ? $settings['sorting'] : array(); ?>
		<input type="hidden" name="tcp_payment_sorting" id="tcp_payment_sorting" value="" />
		<ul id="tcp_payment_list">
		<?php global $tcp_payment_plugins;
		if ( is_array( $payment_sorting ) && count( $payment_sorting ) > 0 )
			foreach( $payment_sorting as $id ) 
				if ( isset( $tcp_payment_plugins[$id] ) ) :
				$tcp_payment_plugin = $tcp_payment_plugins[$id]; ?>
				<li class="tcp_payment_item" id="<?php echo $id; ?>"><?php echo $tcp_payment_plugin->getName(); ?></li>
			<?php endif;
		foreach( $tcp_payment_plugins as $id => $tcp_payment_plugin )
			if ( ! in_array( $id, $payment_sorting ) ) : ?>
				<li class="tcp_payment_item" id="<?php echo $id; ?>"><?php echo $tcp_payment_plugin->getName(); ?></li>
			<?php endif; ?>
		</ul>
		<?php $tcp_hidden_if_unique = isset( $settings['hidden_if_unique'] ) ? $settings['hidden_if_unique'] : false;
		$tcp_hide_box = isset( $settings['hide_box'] ) ? $settings['hide_box'] : false; ?>
		<ul>
			<li><label><input type="checkbox" value="yes" name="tcp_hidden_if_unique" <?php checked( $tcp_hidden_if_unique ); ?>/> <?php _e( 'Hide box, displaying only header, if only one method is applicable', 'tcp' ); ?></label></li>
			<li><label><input type="checkbox" value="yes" name="tcp_hide_box" <?php checked( $tcp_hide_box ); ?>/> <?php _e( 'Hide box if only one method is applicable', 'tcp' ); ?></label></li>
		</ul>
		<?php return true;
	}

	function save_config_settings() {
		$settings = array(
			'sorting'			=> isset( $_REQUEST['tcp_payment_sorting'] ) ? explode( ',', $_REQUEST['tcp_payment_sorting'] ) : '',
			'hidden_if_unique'	=> isset( $_REQUEST['tcp_hidden_if_unique'] ),
			'hide_box'			=> isset( $_REQUEST['tcp_hide_box'] ),
		);
		update_option( 'tcp_' . get_class( $this ), $settings );
		return true;
	}

	function show() {
		$shoppingCart = TheCartPress::getShoppingCart();
		$billing_country = '';
		$selected_billing_address = isset( $_SESSION['tcp_checkout']['billing']['selected_billing_address'] ) ? $_SESSION['tcp_checkout']['billing']['selected_billing_address'] : false;
		if ( $selected_billing_address == 'new' ) {
			$billing_country = $_SESSION['tcp_checkout']['billing']['billing_country_id'];
		} elseif ( isset( $_SESSION['tcp_checkout']['billing']['selected_billing_id'] )) { //if ( $selected_billing_address == 'Y' ) {
			$billing_country = Addresses::getCountryId( $_SESSION['tcp_checkout']['billing']['selected_billing_id'] );
		} else {
			$billing_country = '';
		}
		$shipping_country = '';
		$selected_shipping_address = isset( $_SESSION['tcp_checkout']['shipping']['selected_shipping_address'] ) ? $_SESSION['tcp_checkout']['shipping']['selected_shipping_address'] : false;
		if ( $selected_shipping_address == 'new' ) {
			$shipping_country = $_SESSION['tcp_checkout']['shipping']['shipping_country_id'];
		} elseif ( $selected_shipping_address == 'BIL' ) {
			$shipping_country = $billing_country;
		} elseif ( $selected_shipping_address == 'Y' && isset( $_SESSION['tcp_checkout']['shipping']['selected_shipping_id'] ) ) {
			$shipping_country = Addresses::getCountryId( $_SESSION['tcp_checkout']['shipping']['selected_shipping_id'] );
		} else {
			$shipping_country = '';
		}
		/*$this->applicable_plugins = tcp_get_applicable_payment_plugins( $billing_country, $shoppingCart );
		$settings = get_option( 'tcp_' . get_class( $this ), array() );
		$this->payment_sorting = isset( $settings['sorting'] ) ? $settings['sorting'] : '';*/
		if ( is_array( $this->payment_sorting ) && count( $this->payment_sorting ) > 0 ) {
			usort( $this->applicable_plugins, array( $this, 'sort_plugins' ) );
		} ?>
		<div class="checkout_info clearfix" id="payment_layer_info">
		<?php if ( is_array( $this->applicable_plugins ) && count( $this->applicable_plugins ) > 0 ) : ?>
			<ul><?php
			$first_plugin_value = false;
			$payment_method_id = isset( $_SESSION['tcp_checkout']['payment_methods']['payment_method_id'] ) ? $_SESSION['tcp_checkout']['payment_methods']['payment_method_id'] : false;
			foreach( $this->applicable_plugins as $plugin_data ) :
				$tcp_plugin = $plugin_data['plugin'];
				$instance = $plugin_data['instance'];
				$plugin_name = get_class( $tcp_plugin );
				$plugin_value = $plugin_name . '#' . $instance;
				if ( ! $payment_method_id ) $payment_method_id = $plugin_value;?>
				<li>
					<label for="<?php echo $plugin_name;?>_<?php echo $instance;?>" class="tcp_payment_<?php echo $plugin_name;?>">
					<input type="radio" id="<?php echo $plugin_name;?>_<?php echo $instance;?>"	name="payment_method_id" value="<?php echo $plugin_value;?>" <?php checked( $plugin_value, $payment_method_id );?> />
					<span class="tcp_payment_title_<?php echo $plugin_name;?>"><?php echo $tcp_plugin->getCheckoutMethodLabel( $instance, $shipping_country, $shoppingCart );?></span>
					</label>
					<div class="tcp_plugin_notice tcp_plugin_notice_<?php echo $plugin_name; ?>"><?php tcp_do_template_excerpt( 'tcp_payment_plugins_' . $plugin_name ); ?></div>
				</li>
			<?php endforeach;?>
			</ul>
			<?php if ( isset( $this->errors['payment_method_id'] ) ) : ?><br/><span class="error"><?php echo $this->errors['payment_method_id'];?></span><?php endif;?>
		<?php else: ?>
			<?php _e( 'There is not applicable methods', 'tcp' ); ?>
		<?php endif;?>
		<?php do_action( 'tcp_checkout_payments' );?>
		</div><!-- payment_layer_info --><?php
		return true;
	}

	function sort_plugins( $a, $b ) {
		$k_a = $a['id'];
		$k_b = $b['id'];
		$pos_a = array_search( $k_a, $this->payment_sorting );
		$pos_b = array_search( $k_b, $this->payment_sorting );
		if ( $pos_a === false ) return 1;
		elseif ( $pos_b === false ) return -1;
		elseif ( $pos_a < $pos_b ) return -1;
		elseif ( $pos_a == $pos_b ) return 0;
		else return 1;
	}
}
endif; // class_exists check