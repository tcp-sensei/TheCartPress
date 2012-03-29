<?php
/**
 * This file is part of TheCartPress.
 * 
 * TheCartPress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TheCartPress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TheCartPress.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once( dirname( __FILE__ ) . '/TCPCheckoutBox.class.php' );
require_once( dirname( dirname( __FILE__ ) ) . '/daos/Countries.class.php' );

class TCPBillingExBox extends TCPCheckoutBox {
	private $errors = array();

	function __construct() {
		parent::__construct();
		add_action( 'wp_footer', 'tcp_states_footer_scripts' );
	}

	function get_title() {
		return __( 'Billing options', 'tcp' );
	}

	function get_class() {
		return 'billing_layer';
	}

	function after_action() {
		$selected_billing_address = isset( $_REQUEST['selected_billing_address'] ) ? $_REQUEST['selected_billing_address'] : 'N';
		$settings = get_option( 'tcp_' . get_class( $this ), array() );
		$use_as_shipping = isset( $settings['use_as_shipping'] ) ? $settings['use_as_shipping'] : false;
		if ( $selected_billing_address == 'new' ) {
			$see_company		= isset( $settings['see_company'] ) ? $settings['see_company'] : true;
			$req_company		= isset( $settings['req_company'] ) ? $settings['req_company'] : false;
			$see_tax_id_number	= isset( $settings['see_tax_id_number'] ) ? $settings['see_tax_id_number'] : true;
			$req_tax_id_number	= isset( $settings['req_tax_id_number'] ) ? $settings['req_tax_id_number'] : true;
			$see_region			= isset( $settings['see_region'] ) ? $settings['see_region'] : true;
			$req_region			= isset( $settings['req_region'] ) ? $settings['req_region'] : true;
			$see_telephone_1	= isset( $settings['see_telephone_1'] ) ? $settings['see_telephone_1'] : true;
			$req_telephone_1	= isset( $settings['req_telephone_1'] ) ? $settings['req_telephone_1'] : false;
			$see_telephone_2	= isset( $settings['see_telephone_2'] ) ? $settings['see_telephone_2'] : true;
			$req_telephone_2	= isset( $settings['req_telephone_2'] ) ? $settings['req_telephone_2'] : false;
			$see_fax			= isset( $settings['see_fax'] ) ? $settings['see_fax'] : true;
			$req_fax			= isset( $settings['req_fax'] ) ? $settings['req_fax'] : false;
			if ( ! isset( $_REQUEST['billing_firstname'] ) || strlen( $_REQUEST['billing_firstname'] ) == 0 ) {
				$this->errors['billing_firstname'] = __( 'The billing First name field must be completed', 'tcp' );
			}
			if ( ! isset( $_REQUEST['billing_lastname'] ) || strlen( $_REQUEST['billing_lastname'] ) == 0 ) {
				$this->errors['billing_lastname'] = __( 'The billing Last name field must be completed', 'tcp' );
			}
			if ( $see_company && $req_company ) {
				if ( ! isset( $_REQUEST['billing_company'] ) || strlen( $_REQUEST['billing_company'] ) == 0 )
					$this->errors['billing_company'] = __( 'The billing company field must be completed', 'tcp' );
			}
			if ( $see_tax_id_number && $req_tax_id_number ) {
				if ( ! isset( $_REQUEST['billing_tax_id_number'] ) || strlen( $_REQUEST['billing_tax_id_number'] ) == 0 )
					$this->errors['billing_tax_id_number'] = __( 'The billing tax id number field must be completed', 'tcp' );
			}
			if ( ! isset( $_REQUEST['billing_street'] ) || strlen( $_REQUEST['billing_street'] ) == 0 ) {
				$this->errors['billing_street'] = __( 'The billing Street field must be completed', 'tcp' );
			}
			if ( isset( $_REQUEST['billing_city'] ) && strlen( $_REQUEST['billing_city'] ) == 0 ) {
				$this->errors['billing_city'] = __( 'The billing City field must be completed', 'tcp' );
			}
			if ( $see_region && $req_region ) {
				if ( isset( $_REQUEST['billing_region'] ) && strlen( $_REQUEST['billing_region'] ) == 0 && $_REQUEST['billing_region_id'] == '' )
					$this->errors['billing_region'] = __( 'The billing Region field must be completed', 'tcp' );
			}
			global $thecartpress;
			$billing_isos = isset( $thecartpress->settings['billing_isos'] ) ? $thecartpress->settings['billing_isos'] : false;
			if ( $billing_isos ) {
				if ( ! in_array( $_REQUEST['billing_country_id'], $billing_isos ) ) {
					$this->errors['billing_country_id'] = __( 'The billing Country is not allowed', 'tcp' );
				}
			}
			if ( ! isset( $_REQUEST['billing_postcode'] ) || strlen( $_REQUEST['billing_postcode'] ) == 0 ) {
				$this->errors['billing_postcode'] = __( 'The billing Postcode field must be completed', 'tcp' );
			}
			if ( $see_telephone_1 && $req_telephone_1 ) {
				if ( ! isset( $_REQUEST['billing_telephone_1'] ) || strlen( $_REQUEST['billing_telephone_1'] ) == 0 )
					$this->errors['billing_telephone_1'] = __( 'The billing telephone 1 field must be completed', 'tcp' );
			}
			if ( $see_telephone_2 && $req_telephone_2 ) {
				if ( ! isset( $_REQUEST['billing_telephone_2'] ) || strlen( $_REQUEST['billing_telephone_2'] ) == 0 )
					$this->errors['billing_telephone_2'] = __( 'The billing telephone 2 field must be completed', 'tcp' );
			}
			if ( $see_fax && $req_fax ) {
				if ( ! isset( $_REQUEST['billing_fax'] ) || strlen( $_REQUEST['billing_fax'] ) == 0 )
					$this->errors['billing_fax'] = __( 'The billing fax field must be completed', 'tcp' );
			}
			if ( ! isset( $_REQUEST['billing_email'] ) || strlen( $_REQUEST['billing_email'] ) == 0 ) {
				$this->errors['billing_email'] = __( 'The billing eMail field must be completed', 'tcp' );
			} elseif ( ! $this->check_email_address( $_REQUEST['billing_email'] ) ) {
				$this->errors['billing_email'] = __( 'The billing eMail field must be a valid email', 'tcp' );
			}
		} elseif ( $selected_billing_address == 'Y' ) {
			global $thecartpress;
			$billing_isos = isset( $thecartpress->settings['billing_isos'] ) ? $thecartpress->settings['billing_isos'] : false;
			if ( $billing_isos ) {
				$billing_country_id = Addresses::getCountryId( $_REQUEST['selected_billing_id'] );
				if ( ! in_array( $billing_country_id, $billing_isos ) ) {
					$this->errors['billing_country_id'] = __( 'The billing Country is not allowed', 'tcp' );
				}
			}
		}
		if ( count( $this->errors ) > 0 ) {
			return false;
		} else {
			if ( $selected_billing_address == 'Y' ) {
				$billing = array(
					'selected_billing_address'	=> 'Y',
					'selected_billing_id'		=> isset( $_REQUEST['selected_billing_id'] ) ? $_REQUEST['selected_billing_id'] : 0,
				);
			} else {
				$billing = array(
					'selected_billing_address'	=> 'new',
					'billing_firstname'			=> $_REQUEST['billing_firstname'],
					'billing_lastname'			=> $_REQUEST['billing_lastname'],
					'billing_company'			=> isset( $_REQUEST['billing_company'] ) ? $_REQUEST['billing_company'] : '',
					'billing_tax_id_number'		=> isset( $_REQUEST['billing_tax_id_number'] ) ? $_REQUEST['billing_tax_id_number'] : '',
					'billing_country'			=> isset( $_REQUEST['billing_country'] ) ? $_REQUEST['billing_country'] : '',
					'billing_country_id'		=> isset( $_REQUEST['billing_country_id'] ) ? $_REQUEST['billing_country_id'] : 0,
					'billing_region'			=> isset( $_REQUEST['billing_region'] ) ? $_REQUEST['billing_region'] : '',
					'billing_region_id'			=> isset( $_REQUEST['billing_region_id'] ) ? $_REQUEST['billing_region_id'] : 0,
					'billing_city'				=> isset( $_REQUEST['billing_city'] ) ? $_REQUEST['billing_city'] : '',
					'billing_city_id'			=> isset( $_REQUEST['billing_city_id'] ) ? $_REQUEST['billing_city_id'] : 0,
					'billing_street'			=> $_REQUEST['billing_street'],
					'billing_postcode'			=> str_replace( ' ' , '', $_REQUEST['billing_postcode'] ),
					'billing_telephone_1'		=> isset( $_REQUEST['billing_telephone_1'] ) ? $_REQUEST['billing_telephone_1'] : '',
					'billing_telephone_2'		=> isset( $_REQUEST['billing_telephone_2'] ) ? $_REQUEST['billing_telephone_2'] : '',
					'billing_fax'				=> isset( $_REQUEST['billing_fax'] ) ? $_REQUEST['billing_fax'] : '',
					'billing_email'				=> isset( $_REQUEST['billing_email'] ) ? $_REQUEST['billing_email'] : '',
				);
			}
			$_SESSION['tcp_checkout']['billing'] = $billing;
		}
		if ( $use_as_shipping ) {
			$_SESSION['tcp_checkout']['shipping'] = array(
				'selected_shipping_address' => "BIL",
			);
		}
		return apply_filters( 'tcp_after_billing_ex_box', true );
	}

	function show_config_settings() {
		$settings			= get_option( 'tcp_' . get_class( $this ), array() );
		$see_company		= isset( $settings['see_company'] ) ? $settings['see_company'] : true;
		$req_company		= isset( $settings['req_company'] ) ? $settings['req_company'] : false;
		$see_tax_id_number	= isset( $settings['see_tax_id_number'] ) ? $settings['see_tax_id_number'] : true;
		$req_tax_id_number	= isset( $settings['see_tax_id_number'] ) ? $settings['see_tax_id_number'] : true;
		$see_region			= isset( $settings['see_region'] ) ? $settings['see_region'] : true;
		$req_region			= isset( $settings['req_region'] ) ? $settings['req_region'] : true;
		$see_telephone_1	= isset( $settings['see_telephone_1'] ) ? $settings['see_telephone_1'] : true;
		$req_telephone_1	= isset( $settings['req_telephone_1'] ) ? $settings['req_telephone_1'] : false;
		$see_telephone_2	= isset( $settings['see_telephone_2'] ) ? $settings['see_telephone_2'] : true;
		$req_telephone_2	= isset( $settings['req_telephone_2'] ) ? $settings['req_telephone_2'] : false;
		$see_fax			= isset( $settings['see_fax'] ) ? $settings['see_fax'] : true;
		$req_fax			= isset( $settings['req_fax'] ) ? $settings['req_fax'] : false;
		$use_as_shipping	= isset( $settings['use_as_shipping'] ) ? $settings['use_as_shipping'] : false;
		?><tr valign="top">
			<th scope="row"><label for="see_company"><?php _e( 'Display billing company', 'tcp' );?>:</label></th>
			<td><input type="checkbox" name="see_company" id="see_company" value="yes" <?php checked( $see_company );?>/></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="req_company"><?php _e( 'Required billing company', 'tcp' );?>:</label></th>
			<td><input type="checkbox" name="req_company" id="req_company" value="yes" <?php checked( $req_company );?>/></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="see_tax_id_number"><?php _e( 'Display billing tax id number', 'tcp' );?>:</label></th>
			<td><input type="checkbox" name="see_tax_id_number" id="see_tax_id_number" value="yes" <?php checked( $see_tax_id_number );?>/></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="req_tax_id_number"><?php _e( 'Required billing tax id number', 'tcp' );?>:</label></th>
			<td><input type="checkbox" name="req_tax_id_number" id="req_tax_id_number" value="yes" <?php checked( $req_tax_id_number );?>/></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="see_region"><?php _e( 'Display billing region', 'tcp' );?>:</label></th>
			<td><input type="checkbox" name="see_region" id="see_region" value="yes" <?php checked( $see_region );?>/></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="req_region"><?php _e( 'Required billing region', 'tcp' );?>:</label></th>
			<td><input type="checkbox" name="req_region" id="req_region" value="yes" <?php checked( $req_region );?>/></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="see_telephone_1"><?php _e( 'Display billing telephone 1', 'tcp' );?>:</label></th>
			<td><input type="checkbox" name="see_telephone_1" id="see_telephone_1" value="yes" <?php checked( $see_telephone_1 );?>/></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="req_telephone_1"><?php _e( 'Required billing telephone 1', 'tcp' );?>:</label></th>
			<td><input type="checkbox" name="req_telephone_1" id="req_telephone_1" value="yes" <?php checked( $req_telephone_1 );?>/></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="see_telephone_2"><?php _e( 'Display billing telephone 2', 'tcp' );?>:</label></th>
			<td><input type="checkbox" name="see_telephone_2" id="see_telephone_2" value="yes" <?php checked( $see_telephone_2 );?>/></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="req_telephone_2"><?php _e( 'Required billing telephone 2', 'tcp' );?>:</label></th>
			<td><input type="checkbox" name="req_telephone_2" id="req_telephone_2" value="yes" <?php checked( $req_telephone_2 );?>/></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="see_fax"><?php _e( 'Display billing fax', 'tcp' );?>:</label></th>
			<td><input type="checkbox" name="see_fax" id="see_fax" value="yes" <?php checked( $see_fax );?>/></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="req_fax"><?php _e( 'Required billing fax', 'tcp' );?>:</label></th>
			<td><input type="checkbox" name="req_fax" id="req_fax" value="yes" <?php checked( $req_fax );?>/></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="use_as_shipping"><?php _e( 'Use as shipping', 'tcp' );?>:</label></th>
			<td><input type="checkbox" name="use_as_shipping" id="use_as_shipping" value="yes" <?php checked( $use_as_shipping );?>/>
			<span class="description"><?php _e( 'This option only must be activated if the shipping box is not used', 'tcp' );?></span></td>
		</tr><?php
		return true;
	}

	function save_config_settings() {
		$settings = array(
			'see_company'		=> isset( $_REQUEST['see_company'] ) ? $_REQUEST['see_company'] == 'yes' : false,
			'req_company'		=> isset( $_REQUEST['req_company'] ) ? $_REQUEST['req_company'] == 'yes' : false,
			'see_tax_id_number'	=> isset( $_REQUEST['see_tax_id_number'] ) ? $_REQUEST['see_tax_id_number'] == 'yes' : false,
			'req_tax_id_number'	=> isset( $_REQUEST['req_tax_id_number'] ) ? $_REQUEST['req_tax_id_number'] == 'yes' : false,
			'see_region'		=> isset( $_REQUEST['see_region'] ) ? $_REQUEST['see_region'] == 'yes' : false,
			'req_region'		=> isset( $_REQUEST['req_region'] ) ? $_REQUEST['req_region'] == 'yes' : false,
			'see_telephone_1'	=> isset( $_REQUEST['see_telephone_1'] ) ? $_REQUEST['see_telephone_1'] == 'yes' : false,
			'req_telephone_1'	=> isset( $_REQUEST['req_telephone_1'] ) ? $_REQUEST['req_telephone_1'] == 'yes' : false,
			'see_telephone_2'	=> isset( $_REQUEST['see_telephone_2'] ) ? $_REQUEST['see_telephone_2'] == 'yes' : false,
			'req_telephone_2'	=> isset( $_REQUEST['req_telephone_2'] ) ? $_REQUEST['req_telephone_2'] == 'yes' : false,
			'see_fax'			=> isset( $_REQUEST['see_fax'] ) ? $_REQUEST['see_fax'] == 'yes' : false,
			'req_fax'			=> isset( $_REQUEST['req_fax'] ) ? $_REQUEST['req_fax'] == 'yes' : false,
			'use_as_shipping'	=> isset( $_REQUEST['use_as_shipping'] ) ? $_REQUEST['use_as_shipping'] == 'yes' : false,
		);
		if ( ! $settings['see_company'] ) $settings['req_company'] = false;
		if ( ! $settings['see_tax_id_number'] ) $settings['req_tax_id_number'] = false;
		if ( ! $settings['see_region'] ) $settings['req_region'] = false;
		if ( ! $settings['see_telephone_1'] ) $settings['req_telephone_1'] = false;
		if ( ! $settings['see_telephone_2'] ) $settings['req_telephone_2'] = false;
		if ( ! $settings['see_fax'] ) $settings['req_fax'] = false;
		update_option( 'tcp_' . get_class( $this ), $settings );
		return true;
	}

	function show() {
		$settings			= get_option( 'tcp_' . get_class( $this ), array() );
		$see_company		= isset( $settings['see_company'] ) ? $settings['see_company'] : true;
		$req_company		= isset( $settings['req_company'] ) ? $settings['req_company'] : false;
		$see_tax_id_number	= isset( $settings['see_tax_id_number'] ) ? $settings['see_tax_id_number'] : true;
		$req_tax_id_number	= isset( $settings['req_tax_id_number'] ) ? $settings['req_tax_id_number'] : false;
		$see_region			= isset( $settings['see_region'] ) ? $settings['see_region'] : true;
		$req_region			= isset( $settings['req_region'] ) ? $settings['req_region'] : true;
		$see_telephone_1	= isset( $settings['see_telephone_1'] ) ? $settings['see_telephone_1'] : true;
		$req_telephone_1	= isset( $settings['req_telephone_1'] ) ? $settings['req_telephone_1'] : false;
		$see_telephone_2	= isset( $settings['see_telephone_2'] ) ? $settings['see_telephone_2'] : true;
		$req_telephone_2	= isset( $settings['req_telephone_2'] ) ? $settings['req_telephone_2'] : false;
		$see_fax			= isset( $settings['see_fax'] ) ? $settings['see_fax'] : true;
		$req_fax			= isset( $settings['req_fax'] ) ? $settings['req_fax'] : false;
		$use_as_shipping	= isset( $settings['use_as_shipping'] ) ? $settings['use_as_shipping'] : false;
		if ( isset( $_REQUEST['selected_billing_address'] ) ) {
			$selected_billing_address = $_REQUEST['selected_billing_address'];
		} elseif ( isset( $_SESSION['tcp_checkout']['billing']['selected_billing_address'] ) ) {
			$selected_billing_address = $_SESSION['tcp_checkout']['billing']['selected_billing_address'];
		} else {
			$selected_billing_address = 'Y';
		}?>
		<div class="checkout_info clearfix" id="billing_layer_info">
		<?php if ( $use_as_shipping ) :?>
			<span class="tcp_use_as_shipping"><?php _e( 'This data will be used also as Shipping.', 'tcp' );?></span><br/>
		<?php endif;?>
		<?php global $current_user;
		get_currentuserinfo();
		$addresses = Addresses::getCustomerAddresses( $current_user->ID );
		$default_address = false;
		if ( count( $addresses ) > 0 ) :
			if ( isset( $_REQUEST['selected_billing_id'] ) ) {
				$default_address_id = $_REQUEST['selected_billing_id'];
			} elseif ( isset( $_SESSION['tcp_checkout']['billing']['selected_billing_id'] ) ) {
				$default_address_id = $_SESSION['tcp_checkout']['billing']['selected_billing_id'];
			} else {
				$default_address = Addresses::getCustomerDefaultBillingAddress( $current_user->ID );
				$default_address_id = $default_address ? $default_address->address_id : 0;
			}?>
				<div id="selected_billing_area" <?php if ( $selected_billing_address == 'new' ) : ?>style="display:none"<?php endif;?>>
					<label for="selected_billing_id"> <?php _e( 'Select billing address:', 'tcp' );?></label>
					<br />
					<select id="selected_billing_id" name="selected_billing_id">
					<?php foreach( $addresses as $address ) :?>
						<option value="<?php echo $address->address_id;?>" <?php selected( $address->address_id, $default_address_id );?>><?php echo stripslashes( $address->street . ', ' . $address->city );?></option>
					<?php endforeach;?>
					</select>
					<?php if ( $selected_billing_address == 'Y' ) $this->showErrorMsg( 'billing_country_id' );?>
				</div> <!-- selected_billing_area -->
				<input type="radio" id="selected_billing_address" name="selected_billing_address" value="Y"<?php if ( ( $selected_billing_address == 'Y' && count( $addresses ) > 0 ) ) : ?> checked="true"<?php endif;?> onChange="jQuery('#selected_billing_area').show();jQuery('#new_billing_area').hide();" />
				<label for="selected_billing_address"><?php _e( 'Billing to the address selected', 'tcp' )?></label>
				<br />
			<?php endif;?>
			<input type="radio" id="new_billing_address" name="selected_billing_address" value="new" <?php if ( $selected_billing_address == 'new' || count( $addresses ) == 0 ) : ?> checked="true"<?php endif;?> onChange="jQuery('#new_billing_area').show();jQuery('#selected_billing_area').hide();" />
			<label for="new_billing_address"><?php _e( 'New billing address', 'tcp' );?></label>
			<div id="new_billing_area" class="clearfix" <?php
				if ( $selected_billing_address == 'new' ) :
				?><?php elseif ( count( $addresses ) > 0 ) :
					?>style="display:none"<?php
				endif;?>><?php
			if ( isset( $_REQUEST['billing_firstname'] ) ) {
				$firstname = $_REQUEST['billing_firstname'];
			} elseif ( isset( $_SESSION['tcp_checkout']['billing']['billing_firstname'] ) ) {
				$firstname = $_SESSION['tcp_checkout']['billing']['billing_firstname'];
			} elseif ( $default_address ) {
				$firstname = stripslashes( $default_address->firstname );
			} elseif ( $current_user && $current_user instanceof WP_User ) {
				$firstname = $current_user->first_name;
			} else {
				$firstname = '';
			}
			if ( isset( $_REQUEST['billing_lastname'] ) ) {
				$lastname = $_REQUEST['billing_lastname'];
			} elseif ( isset( $_SESSION['tcp_checkout']['billing']['billing_lastname'] ) ) {
				$lastname = $_SESSION['tcp_checkout']['billing']['billing_lastname'];
			} elseif ( $default_address ) {
				$lastname = stripslashes($default_address->lastname );
			} elseif ( $current_user && $current_user instanceof WP_User ) {
				$lastname = $current_user->last_name;
			} else {
				$lastname = '';
			}
			if ( isset( $_REQUEST['billing_company'] ) ) {
				$company = $_REQUEST['billing_company'];
			} elseif ( isset( $_SESSION['tcp_checkout']['billing']['billing_company'] ) ) {
				$company = $_SESSION['tcp_checkout']['billing']['billing_company'];
			} else {
				$company = $default_address ? stripslashes( $default_address->company ) : '';
			}
			if ( isset( $_REQUEST['billing_tax_id_number'] ) ) {
				$tax_id_number = $_REQUEST['billing_tax_id_number'];
			} elseif ( isset( $_SESSION['tcp_checkout']['billing']['billing_tax_id_number'] ) ) {
				$tax_id_number = $_SESSION['tcp_checkout']['billing']['billing_tax_id_number'];
			} else {
				$tax_id_number = $default_address ? $default_address->tax_id_number : '';
			}
			if ( isset( $_REQUEST['billing_street'] ) ) {
				$street = $_REQUEST['billing_street'];
			} elseif ( isset( $_SESSION['tcp_checkout']['billing']['billing_street'] ) ) {
				$street = $_SESSION['tcp_checkout']['billing']['billing_street'];
			} else {
				$street = $default_address ? stripslashes( $default_address->street ) : '';
			}
			if ( isset( $_REQUEST['billing_city_id'] ) ) {
				$city_id = $_REQUEST['billing_city_id'];
			} elseif ( isset( $_SESSION['tcp_checkout']['billing']['billing_city_id'] ) ) {
				$city_id = $_SESSION['tcp_checkout']['billing']['billing_city_id'];
			} else {
				$city_id = $default_address ? $default_address->city_id : '';
			}
			if ( isset( $_REQUEST['billing_city'] ) ) {
				$city = $_REQUEST['billing_city'];
			} elseif ( isset( $_SESSION['tcp_checkout']['billing']['billing_city'] ) ) {
				$city = $_SESSION['tcp_checkout']['billing']['billing_city'];
			} else {
				$city = $default_address ? stripslashes( $default_address->city ) : '';
			}
			if ( isset( $_REQUEST['billing_region_id'] ) ) {
				$region_id = $_REQUEST['billing_region_id'];
			} elseif ( isset( $_SESSION['tcp_checkout']['billing']['billing_region_id'] ) ) {
				$region_id = $_SESSION['tcp_checkout']['billing']['billing_region_id'];
			} else {
				$region_id = $default_address ? $default_address->region_id : '';
			}
			if ( isset( $_REQUEST['billing_region'] ) ) {
				$region = $_REQUEST['billing_region'];
			} elseif ( isset( $_SESSION['tcp_checkout']['billing']['billing_region'] ) ) {
				$region = $_SESSION['tcp_checkout']['billing']['billing_region'];
			} else {
				$region = $default_address ? stripslashes( $default_address->region ) : '';
			}
			if ( isset( $_REQUEST['billing_postcode'] ) ) {
				$postcode = $_REQUEST['billing_postcode'];
			} elseif ( isset( $_SESSION['tcp_checkout']['billing']['billing_postcode'] ) ) {
				$postcode = $_SESSION['tcp_checkout']['billing']['billing_postcode'];
			} else {
				$postcode = $default_address ? str_replace( ' ' , '', $default_address->postcode ) : '';
			}
			if ( isset( $_REQUEST['billing_country_id'] ) ) {
				$country_id = $_REQUEST['billing_country_id'];
			} elseif ( isset( $_SESSION['tcp_checkout']['billing']['billing_country_id'] ) ) {
				$country_id = $_SESSION['tcp_checkout']['billing']['billing_country_id'];
			} else {
				$country_id = $default_address ? $default_address->country_id : '';
			}
			if ( isset( $_REQUEST['billing_telephone_1'] ) ) {
				$telephone_1 = $_REQUEST['billing_telephone_1'];
			} elseif ( isset( $_SESSION['tcp_checkout']['billing']['billing_telephone_1'] ) ) {
				$telephone_1 = $_SESSION['tcp_checkout']['billing']['billing_telephone_1'];
			} else {
				$telephone_1 = $default_address ? $default_address->telephone_1 : '';
			}
			if ( isset( $_REQUEST['billing_telephone_2'] ) ) {
				$telephone_2 = $_REQUEST['billing_telephone_2'];
			} elseif ( isset( $_SESSION['tcp_checkout']['billing']['billing_telephone_2'] ) ) {
				$telephone_2 = $_SESSION['tcp_checkout']['billing']['billing_telephone_2'];
			} else {
				$telephone_2 = $default_address ? $default_address->telephone_2 : '';
			}
			if ( isset( $_REQUEST['billing_fax'] ) ) {
				$fax = $_REQUEST['billing_fax'];
			} elseif ( isset( $_SESSION['tcp_checkout']['billing']['billing_fax'] ) ) {
				$fax = $_SESSION['tcp_checkout']['billing']['billing_fax'];
			} else {
				$fax = $default_address ? $default_address->fax : '';
			}
			if ( isset( $_REQUEST['billing_email'] ) ) {
				$email = $_REQUEST['billing_email'];
			} elseif ( isset( $_SESSION['tcp_checkout']['billing']['billing_email'] ) ) {
				$email = $_SESSION['tcp_checkout']['billing']['billing_email'];
			} elseif ( $default_address ) {
				$email = $default_address->email;
			} elseif ( $current_user ) { //&& $current_user instanceof WP_User ) {
				$email = '';//$current_user->email;
			} else {
				$email = '';
			}?>
				<ul>
					<li><label for="billing_firstname"><?php _e( 'Firstname', 'tcp' );?>:<em>*</em></label>
					<input type="text" id="billing_firstname" name="billing_firstname" value="<?php echo $firstname;?>" size="20" maxlength="50" />
					<?php $this->showErrorMsg( 'billing_firstname' );?></li>

					<li><label for="billing_lastname"><?php _e( 'Lastname', 'tcp' );?>:<em>*</em></label>
					<input type="text" id="billing_lastname" name="billing_lastname" value="<?php echo $lastname;?>" size="40" maxlength="100" />
					<?php $this->showErrorMsg( 'billing_lastname' );?></li>

					<?php if ( $see_company ) :?>
						<li><label for="billing_company"><?php _e( 'Company', 'tcp' );?>:<?php if ( $req_company ) :?><em>*</em><?php endif;?></label>
						<input type="text" id="billing_company" name="billing_company" value="<?php echo $company;?>" size="20" maxlength="50" />
						<?php $this->showErrorMsg( 'billing_company' );?></li>
					<?php endif;?>
					<?php if ( $see_tax_id_number ) :?>
						<li><label for="billing_tax_id_number"><?php _e( 'Tax id number', 'tcp' );?>:</label>
						<input type="text" id="billing_tax_id_number" name="billing_tax_id_number" value="<?php echo $tax_id_number;?>" size="20" maxlength="50" />
						<?php $this->showErrorMsg( 'billing_tax_id_number' );?></li>
					<?php endif;?>
					<li><label for="billing_country_id"><?php _e( 'Country', 'tcp' );?>:<em>*</em></label>
					<?php global $thecartpress;
					$country = isset( $thecartpress->settings['country'] ) ? $thecartpress->settings['country'] : '';
					$billing_isos = isset( $thecartpress->settings['billing_isos'] ) ? $thecartpress->settings['billing_isos'] : false;
					if ( $billing_isos ) {
						$countries = Countries::getSome( $billing_isos, tcp_get_current_language_iso() );
					} else {
						$countries = Countries::getAll( tcp_get_current_language_iso() );
					}
					$country_bill = $country_id;
					if ( $country_bill == '' ) $country_bill = $country;
					?><select id="billing_country_id" name="billing_country_id"><?php //p_use_billing_address
					foreach( $countries as $item ) :?>
						<option value="<?php echo $item->iso;?>" <?php selected( $item->iso, $country_bill )?>><?php echo $item->name;?></option>
					<?php endforeach;?>
					</select>
					</li>
					<?php if ( $see_region ) :?>
						<li><label for="billing_region_id"><?php _e( 'Region', 'tcp' );?>:<?php if ( $req_region ) :?><em>*</em><?php endif;?></label>
						<?php $regions = apply_filters( 'tcp_load_regions_for_billing', false ); //array( 'id' => array( 'name'), 'id' => array( 'name'), ... )?>
						<select id="billing_region_id" name="billing_region_id" <?php if ( is_array( $regions ) && count( $regions ) > 0 ) {} else { echo 'style="display:none;"'; }?>>
							<option value=""><?php _e( 'No state selected', 'tcp' );?></option>
						<?php if ( is_array( $regions ) && count( $regions ) > 0 ) foreach( $regions as $id => $region ) : ?>
							<option value="<?php echo $id;?>" <?php selected( $id, $region_id );?>><?php echo $region['name'];?></option>
						<?php endforeach;?>
						</select>
						<input type="hidden" id="billing_region_selected_id" value="<?php echo $region_id;?>"/>
						<?php $this->showErrorMsg( 'billing_region_id' );?>
						<input type="text" id="billing_region" name="billing_region" value="<?php echo $region;?>" size="20" maxlength="50" <?php if ( is_array( $regions ) && count( $regions ) > 0 ) echo 'style="display:none;"';?>/>
						<?php $this->showErrorMsg( 'billing_region' );?>
						</li>
					<?php endif;?>
					<li id="li_billing_city_id"><label for="billing_city_id"><?php _e( 'City', 'tcp' );?>:<em>*</em></label>
					<?php $cities = array(); //array( 'id' => array( 'name'), 'id' => array( 'name'), ... )
					$cities = apply_filters( 'tcp_load_cities_for_billing', $cities );
					if ( is_array( $cities ) && count( $cities ) > 0 ) : ?>
						<select id="billing_city_id" name="billing_city_id">
						<?php foreach( $cities as $id => $city ) : ?>
							<option value="<?php echo $id;?>" <?php selected( $id, $city_id );?>><?php echo $city['name'];?></option>
						<?php endforeach;?>
						</select>
						<?php $this->showErrorMsg( 'billing_city_id' );?>
					<?php else : ?>
						<input type="text" id="billing_city" name="billing_city" value="<?php echo $city;?>" size="20" maxlength="50" />
						<?php $this->showErrorMsg( 'billing_city' );?>
					<?php endif;?>
					</li>

					<li><label for="billing_street"><?php _e( 'Address', 'tcp' );?>:<em>*</em></label>
					<input type="text" id="billing_street" name="billing_street" value="<?php echo $street;?>" size="20" maxlength="50" />
					<?php $this->showErrorMsg( 'billing_street' );?></li>

					<li><label for="billing_postcode"><?php _e( 'Postal code', 'tcp' );?>:<em>*</em></label>
					<input type="text" id="billing_postcode" name="billing_postcode" value="<?php echo $postcode;?>" size="5" maxlength="10" />
					<?php $this->showErrorMsg( 'billing_postcode' );?></li>

					<?php if ( $see_telephone_1 ) :?>
						<li><label for="billing_telephone_1"><?php _e( 'Telephone 1', 'tcp' );?>:<?php if ( $req_telephone_1 ) :?><em>*</em><?php endif;?></label>
						<input type="text" id="billing_telephone_1" name="billing_telephone_1" value="<?php echo $telephone_1;?>" size="15" maxlength="20" />
						<?php $this->showErrorMsg( 'billing_telephone_1' );?></li>
					<?php endif;?>

					<?php if ( $see_telephone_2 ) :?>
						<li><label for="billing_telephone_2"><?php _e( 'Telephone 2', 'tcp' );?>:<?php if ( $req_telephone_2 ) :?><em>*</em><?php endif;?></label>
						<input type="text" id="billing_telephone_2" name="billing_telephone_2" value="<?php echo $telephone_2;?>" size="15" maxlength="20" />
						<?php $this->showErrorMsg( 'billing_telephone_2' );?></li>
					<?php endif;?>

					<?php if ( $see_fax ) :?>
						<li><label for="billing_fax"><?php _e( 'Fax', 'tcp' );?>:<?php if ( $req_fax ) :?><em>*</em><?php endif;?></label>
						<input type="text" id="billing_fax" name="billing_fax" value="<?php echo $fax;?>" size="15" maxlength="20" />
						<?php $this->showErrorMsg( 'billing_fax' );?></li>
					<?php endif;?>

					<li><label for="billing_email"><?php _e( 'eMail', 'tcp' );?>:<em>*</em></label>
					<input type="email" id="billing_email" name="billing_email" value="<?php echo $email;?>" size="15" maxlength="255" />
					<?php $this->showErrorMsg( 'billing_email' );?></li>
				</ul>
			</div> <!-- new_billing_area -->
			<?php do_action( 'tcp_checkout_billing' );?>
		</div><!-- billing_layer_info -->
		<?php return true;
	}
	
	private function showErrorMsg( $field_name ) {
		if ( isset( $this->errors[$field_name] ) ) : ?>
			<br/><span class="error"><?php echo $this->errors[$field_name];?></span>
		<?php endif;
	}

	//http://www.linuxjournal.com/article/9585
	private function check_email_address( $email ) {
		// First, we check that there's one @ symbol, 
		// and that the lengths are right.
		if ( ! ereg("^[^@]{1,64}@[^@]{1,255}$", $email ) ) {
			// Email invalid because wrong number of characters 
			// in one section or wrong number of @ symbols.
			return false;
		}
		// Split it into sections to make life easier
		$email_array = explode( "@", $email );
		if ( count( $email_array ) < 2 )
			return false;
		$local_array = explode( ".", $email_array[0] );
		for ( $i = 0; $i < sizeof( $local_array ); $i++ ) {
			if ( ! ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&
			↪'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$",
			$local_array[$i] ) ) {
				return false;
			}
		}
		// Check if domain is IP. If not, 
		// it should be valid domain name
		if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) {
			$domain_array = explode(".", $email_array[1]);
			if (sizeof($domain_array) < 2) {
				return false; // Not enough parts to domain
			}
			for ($i = 0; $i < sizeof($domain_array); $i++) {
				if ( ! ereg( "^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|
				↪([A-Za-z0-9]+))$",
				$domain_array[$i] ) ) {
					return false;
				}
			}
		}
		return true;
	}
}
?>
