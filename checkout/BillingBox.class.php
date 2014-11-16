<?php
/**
 * Billing Box
 *
 * Billing data step for the Checkout
 *
 * @package TheCartPress
 * @subpackage Checkout
 * @since 1.3.2
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
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'TCPBillingBox' ) ) :

require_once( TCP_CHECKOUT_FOLDER	. 'TCPCheckoutBox.class.php' );
require_once( TCP_CLASSES_FOLDER	. 'CustomForms.class.php' );

class TCPBillingBox extends TCPCheckoutBox {
	protected $default_address = false;
	protected $errors = array();

	function __construct() {
		parent::__construct();
		add_action( 'states_footer', 'tcp_states_footer_scripts' );
	}

	function get_title() {
		return __( 'Billing Info', 'tcp' );
	}

	function get_class() {
		return 'billing_layer';
	}

	function get_name() {
		return 'billing';
	}

	function after_action() {
		global $thecartpress;

		//Getting Saved Settings
		$settings		 = get_option( 'tcp_' . get_class( $this ), array() );
		$use_as_shipping = isset( $settings['use_as_shipping'] ) ? $settings['use_as_shipping'] : false;

		//Only if a new address is typed...
		$selected_billing_address	= isset( $_REQUEST['selected_billing_address'] ) ? $_REQUEST['selected_billing_address'] : 'N';
		if ( $selected_billing_address == 'new' || $selected_billing_address == 'N' ) {
			//Getting defalt fields
			$fields = $this->getDefaultFields();
			
			//Applying active and required properties
			foreach( $fields as $id => $field ) {
				$active = isset( $settings['active-' . $id] ) ? $settings['active-' . $id] : true;
				if ( $active ) {
					if ( isset( $settings['callback_required-' . $id] ) ) {
						//TODO
					} else {
						$required = isset( $settings['required-' . $id] ) ? $settings['required-' . $id] : false;
						if ( $required && ( ! isset( $_REQUEST[$id] ) || strlen( $_REQUEST[$id] ) == 0 ) ) {
							$this->errors[$id] = $field['error'];
						} elseif ( $id == 'billing_country_id' ) {
							$billing_isos = isset( $thecartpress->settings['billing_isos'] ) ? $thecartpress->settings['billing_isos'] : false;
							if ( $billing_isos && ! in_array( $_REQUEST['billing_country_id'], $billing_isos ) ) {
								$this->errors['billing_country_id'] = __( 'The billing Country is not allowed', 'tcp' );
							}
						} elseif ( $id == 'billing_email' ) {
							if ( ! $this->check_email_address( $_REQUEST['billing_email'] ) ) {
								$this->errors['billing_email'] = __( 'The billing eMail field must be a valid email', 'tcp' );
							}
						} else {
							unset( $this->errors[$id] );
						}
					}
				}
			}
		} elseif ( $selected_billing_address == 'Y' ) {
			$billing_isos = isset( $thecartpress->settings['billing_isos'] ) ? $thecartpress->settings['billing_isos'] : false;
			if ( $billing_isos ) {
				$billing_country_id = Addresses::getCountryId( $_REQUEST['selected_billing_id'] );
				if ( ! in_array( $billing_country_id, $billing_isos ) ) {
					$this->errors['billing_country_id'] = __( 'The billing Country is not allowed', 'tcp' );
				}
			}
		}

		if ( $use_as_shipping ) {
			$_SESSION['tcp_checkout']['shipping'] = array(
				'selected_shipping_address' => "BIL",
			);
		}
		$this->errors = apply_filters( 'tcp_billing_box_after_action', $this->errors );
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
					'billing_firstname'			=> isset( $_REQUEST['billing_firstname'] ) ? $_REQUEST['billing_firstname'] : '',
					'billing_lastname'			=> isset( $_REQUEST['billing_lastname'] ) ? $_REQUEST['billing_lastname'] : '',
					'billing_company'			=> isset( $_REQUEST['billing_company'] ) ? $_REQUEST['billing_company'] : '',
					'billing_tax_id_number'		=> isset( $_REQUEST['billing_tax_id_number'] ) ? $_REQUEST['billing_tax_id_number'] : '',
					'billing_country'			=> isset( $_REQUEST['billing_country'] ) ? $_REQUEST['billing_country'] : '',
					'billing_country_id'		=> isset( $_REQUEST['billing_country_id'] ) ? $_REQUEST['billing_country_id'] : 0,
					'billing_region'			=> isset( $_REQUEST['billing_region'] ) ? $_REQUEST['billing_region'] : '',
					'billing_region_id'			=> isset( $_REQUEST['billing_region_id'] ) ? $_REQUEST['billing_region_id'] : 0,
					'billing_city'				=> isset( $_REQUEST['billing_city'] ) ? $_REQUEST['billing_city'] : '',
					'billing_city_id'			=> isset( $_REQUEST['billing_city_id'] ) ? $_REQUEST['billing_city_id'] : 0,
					'billing_street'			=> isset( $_REQUEST['billing_street'] ) ? $_REQUEST['billing_street'] : '',
					'billing_street_2'			=> isset( $_REQUEST['billing_street_2'] ) ? $_REQUEST['billing_street_2'] : '',
					'billing_postcode'			=> isset( $_REQUEST['billing_postcode'] ) ? str_replace( ' ' , '', $_REQUEST['billing_postcode'] ) : '',
					'billing_telephone_1'		=> isset( $_REQUEST['billing_telephone_1'] ) ? $_REQUEST['billing_telephone_1'] : '',
					'billing_telephone_2'		=> isset( $_REQUEST['billing_telephone_2'] ) ? $_REQUEST['billing_telephone_2'] : '',
					'billing_fax'				=> isset( $_REQUEST['billing_fax'] ) ? $_REQUEST['billing_fax'] : '',
					'billing_email'				=> isset( $_REQUEST['billing_email'] ) ? $_REQUEST['billing_email'] : '',
				);
			}
			$_SESSION['tcp_checkout']['billing'] = $billing;
			return apply_filters( 'tcp_after_billing_box', true );
		}
	}

	function show() {
		$use_as_shipping = isset( $settings['use_as_shipping'] ) ? $settings['use_as_shipping'] : false;
		if ( isset( $_REQUEST['selected_billing_address'] ) ) {
			$selected_billing_address = $_REQUEST['selected_billing_address'];
		} elseif ( isset( $_SESSION['tcp_checkout']['billing']['selected_billing_address'] ) ) {
			$selected_billing_address = $_SESSION['tcp_checkout']['billing']['selected_billing_address'];
		} else {
			$selected_billing_address = false;
		}?>
		<div class="checkout_info clearfix" id="billing_layer_info">
			<?php if ( $use_as_shipping ) : ?>
			<span class="tcp_use_as_shipping"><?php _e( 'This data will be used, also, as Shipping.', 'tcp' ); ?></span><br/>
			<?php endif; ?>
		<?php global $current_user;
		get_currentuserinfo();
		if ( $current_user->ID > 0 ) {
			$addresses = Addresses::getCustomerAddresses( $current_user->ID );
		} else {
			$addresses = array();
		}
		if ( is_array( $addresses ) && count( $addresses ) > 0 ) {
			if ( $selected_billing_address === false ) $selected_billing_address = 'Y';
			if ( isset( $_REQUEST['selected_billing_id'] ) ) {
				$default_address_id = $_REQUEST['selected_billing_id'];
			} elseif ( isset( $_SESSION['tcp_checkout']['billing']['selected_billing_id'] ) ) {
				$default_address_id = $_SESSION['tcp_checkout']['billing']['selected_billing_id'];
			} else {
				$this->default_address = Addresses::getCustomerDefaultBillingAddress( $current_user->ID );
				$default_address_id = $this->default_address ? $this->default_address->address_id : 0;
			} ?>
			<div id="selected_billing_area" <?php if ( $selected_billing_address == 'new' ) : ?>style="display:none"<?php endif;?>>
				<label for="selected_billing_id"> <?php _e( 'Select billing address:', 'tcp' ); ?></label>
				<br />
				<select id="selected_billing_id" name="selected_billing_id">
				<?php foreach( $addresses as $address ) : ?>
					<option value="<?php echo $address->address_id;?>" <?php selected( $address->address_id, $default_address_id ); ?>><?php echo stripslashes( $address->street . ' ' . $address->street_2 . ', ' . $address->city ); ?></option>
				<?php endforeach;?>
				</select>
				<?php if ( $selected_billing_address == 'Y' ) $this->showErrorMsg( 'billing_country_id' ); ?>
			</div> <!-- selected_billing_area -->
			<label for="selected_billing_address">
				<input type="radio" id="selected_billing_address" name="selected_billing_address" value="Y"<?php if ( ( $selected_billing_address == 'Y' && count( $addresses ) > 0 ) ) : ?> checked="true"<?php endif;?> onChange="jQuery('#selected_billing_area').show();jQuery('#new_billing_area').hide();" />
				<?php _e( 'Billing to the address selected', 'tcp' ); ?>
			</label>
			<br />
		<?php } ?>
			<label for="new_billing_address">
				<input type="radio" id="new_billing_address" name="selected_billing_address" value="new" <?php if ( $selected_billing_address == 'new' || count( $addresses ) == 0 ) : ?> checked="true"<?php endif;?> onChange="jQuery('#new_billing_area').show();jQuery('#selected_billing_area').hide();" />
				<?php _e( 'New billing address', 'tcp' ); ?>
			</label>
			<div id="new_billing_area" class="clearfix" <?php
				if ( $selected_billing_address == 'new' ) :
				?><?php elseif ( is_array( $addresses ) && count( $addresses ) > 0 ) :
					?>style="display:none"<?php
				endif;?>><?php
			//Getting defalt fields
			$fields = $this->getDefaultFields();
			if ( isset( $_REQUEST['billing_firstname'] ) ) {
				$firstname = $_REQUEST['billing_firstname'];
			} elseif ( isset( $_SESSION['tcp_checkout']['billing']['billing_firstname'] ) ) {
				$firstname = $_SESSION['tcp_checkout']['billing']['billing_firstname'];
			} elseif ( $this->default_address ) {
				$firstname = stripslashes( $this->default_address->firstname );
			} elseif ( $current_user && $current_user instanceof WP_User ) {
				$firstname = $current_user->first_name;
			} else {
				$firstname = '';
			}
			$fields['billing_firstname']['value'] = $firstname;

			if ( isset( $_REQUEST['billing_lastname'] ) ) {
				$lastname = $_REQUEST['billing_lastname'];
			} elseif ( isset( $_SESSION['tcp_checkout']['billing']['billing_lastname'] ) ) {
				$lastname = $_SESSION['tcp_checkout']['billing']['billing_lastname'];
			} elseif ( $this->default_address ) {
				$lastname = stripslashes( $this->default_address->lastname );
			} elseif ( $current_user && $current_user instanceof WP_User ) {
				$lastname = $current_user->last_name;
			} else {
				$lastname = '';
			}
			$fields['billing_lastname']['value'] = $lastname;

			if ( isset( $_REQUEST['billing_company'] ) ) {
				$company = $_REQUEST['billing_company'];
			} elseif ( isset( $_SESSION['tcp_checkout']['billing']['billing_company'] ) ) {
				$company = $_SESSION['tcp_checkout']['billing']['billing_company'];
			} else {
				$company = $this->default_address ? stripslashes( $this->default_address->company ) : '';
			}
			$fields['billing_company']['value'] = $company;
			
			if ( isset( $_REQUEST['billing_tax_id_number'] ) ) {
				$tax_id_number = $_REQUEST['billing_tax_id_number'];
			} elseif ( isset( $_SESSION['tcp_checkout']['billing']['billing_tax_id_number'] ) ) {
				$tax_id_number = $_SESSION['tcp_checkout']['billing']['billing_tax_id_number'];
			} else {
				$tax_id_number = $this->default_address ? $this->default_address->tax_id_number : '';
			}
			$fields['billing_tax_id_number']['value'] = $tax_id_number;
			
			if ( isset( $_REQUEST['billing_street'] ) ) {
				$street = $_REQUEST['billing_street'];
			} elseif ( isset( $_SESSION['tcp_checkout']['billing']['billing_street'] ) ) {
				$street = $_SESSION['tcp_checkout']['billing']['billing_street'];
			} else {
				$street = $this->default_address ? stripslashes( $this->default_address->street ) : '';
			}
			$fields['billing_street']['value'] = $street;

			if ( isset( $_REQUEST['billing_street_2'] ) ) {
				$street_2 = $_REQUEST['billing_street_2'];
			} elseif ( isset( $_SESSION['tcp_checkout']['billing']['billing_street_2'] ) ) {
				$street_2 = $_SESSION['tcp_checkout']['billing']['billing_street_2'];
			} else {
				$street_2 = $this->default_address ? stripslashes( $this->default_address->street_2 ) : '';
			}
			$fields['billing_street_2']['value'] = $street_2;

			if ( isset( $_REQUEST['billing_city_id'] ) ) {
				$city_id = $_REQUEST['billing_city_id'];
			} elseif ( isset( $_SESSION['tcp_checkout']['billing']['billing_city_id'] ) ) {
				$city_id = $_SESSION['tcp_checkout']['billing']['billing_city_id'];
			} else {
				$city_id = $this->default_address ? $this->default_address->city_id : '';
			} //not ready TODO
			if ( isset( $_REQUEST['billing_city'] ) ) {
				$city = $_REQUEST['billing_city'];
			} elseif ( isset( $_SESSION['tcp_checkout']['billing']['billing_city'] ) ) {
				$city = $_SESSION['tcp_checkout']['billing']['billing_city'];
			} else {
				$city = $this->default_address ? $this->default_address->city : '';
			}
			$fields['billing_city']['value'] = $city;

			if ( isset( $_REQUEST['billing_region_id'] ) ) {
				$region_id = $_REQUEST['billing_region_id'];
			} elseif ( isset( $_SESSION['tcp_checkout']['billing']['billing_region_id'] ) ) {
				$region_id = $_SESSION['tcp_checkout']['billing']['billing_region_id'];
			} else {
				$region_id = $this->default_address ? $this->default_address->region_id : '';
			}
			if ( isset( $_REQUEST['billing_region'] ) ) {
				$region = $_REQUEST['billing_region'];
			} elseif ( isset( $_SESSION['tcp_checkout']['billing']['billing_region'] ) ) {
				$region = $_SESSION['tcp_checkout']['billing']['billing_region'];
			} else {
				$region = $this->default_address ? stripslashes( $this->default_address->region ) : '';
			}
			$fields['billing_region_id']['value'] = $region_id;

			if ( isset( $_REQUEST['billing_postcode'] ) ) {
				$postcode = $_REQUEST['billing_postcode'];
			} elseif ( isset( $_SESSION['tcp_checkout']['billing']['billing_postcode'] ) ) {
				$postcode = $_SESSION['tcp_checkout']['billing']['billing_postcode'];
			} else {
				$postcode = $this->default_address ? str_replace( ' ' , '', $this->default_address->postcode ) : '';
			}
			$fields['billing_postcode']['value'] = $postcode;

			if ( isset( $_REQUEST['billing_country_id'] ) ) {
				$country_id = $_REQUEST['billing_country_id'];
			} elseif ( isset( $_SESSION['tcp_checkout']['billing']['billing_country_id'] ) ) {
				$country_id = $_SESSION['tcp_checkout']['billing']['billing_country_id'];
			} else {
				$country_id = $this->default_address ? $this->default_address->country_id : '';
			}
			$fields['billing_country_id']['value'] = $country_id;

			if ( isset( $_REQUEST['billing_telephone_1'] ) ) {
				$telephone_1 = $_REQUEST['billing_telephone_1'];
			} elseif ( isset( $_SESSION['tcp_checkout']['billing']['billing_telephone_1'] ) ) {
				$telephone_1 = $_SESSION['tcp_checkout']['billing']['billing_telephone_1'];
			} else {
				$telephone_1 = $this->default_address ? $this->default_address->telephone_1 : '';
			}
			$fields['billing_telephone_1']['value'] = $telephone_1;
			
			if ( isset( $_REQUEST['billing_telephone_2'] ) ) {
				$telephone_2 = $_REQUEST['billing_telephone_2'];
			} elseif ( isset( $_SESSION['tcp_checkout']['billing']['billing_telephone_2'] ) ) {
				$telephone_2 = $_SESSION['tcp_checkout']['billing']['billing_telephone_2'];
			} else {
				$telephone_2 = $this->default_address ? $this->default_address->telephone_2 : '';
			}
			$fields['billing_telephone_2']['value'] = $telephone_2;
			
			if ( isset( $_REQUEST['billing_fax'] ) ) {
				$fax = $_REQUEST['billing_fax'];
			} elseif ( isset( $_SESSION['tcp_checkout']['billing']['billing_fax'] ) ) {
				$fax = $_SESSION['tcp_checkout']['billing']['billing_fax'];
			} else {
				$fax = $this->default_address ? $this->default_address->fax : '';
			}
			$fields['billing_fax']['value'] = $fax;
			
			if ( isset( $_REQUEST['billing_email'] ) ) {
				$email = $_REQUEST['billing_email'];
			} elseif ( isset( $_SESSION['tcp_checkout']['billing']['billing_email'] ) ) {
				$email = $_SESSION['tcp_checkout']['billing']['billing_email'];
			} elseif ( $this->default_address ) {
				$email = $this->default_address->email;
			} elseif ( $current_user ) { //&& $current_user instanceof WP_User ) {
				$email = '';//$current_user->email;
			} else {
				$email = '';
			}
			$fields['billing_email']['value'] = $email;
			//Getting Saved Settings
			$settings = get_option( 'tcp_' . get_class( $this ), array() );
			//Get the Ordering field
			$sorting = isset( $settings['sorting'] ) ? $settings['sorting'] : array();
			//Applying active and required properties
			foreach( $fields as $id => $field ) {
				$active		= isset( $settings['active-' . $id] ) ? $settings['active-' . $id] : true;
				if ( $active ) {
					if ( isset( $settings['required-' . $id] ) ) {
						$required = $settings['required-' . $id];
					} else {
						$required = isset( $field['required'] ) ? $field['required'] : false;
					}
					$fields[$id]['required'] = $required;
				 } else {
				 	unset( $fields[$id] );
				 }
			} ?>
				<ul>
					<?php TCPCustomForms::showCheckout( $fields, $this, $sorting ); ?>
				</ul>
			</div> <!-- new_billing_area -->
			<?php tcp_do_template( 'tcp_checkout_billing_notice' ); ?>
			<?php do_action( 'tcp_checkout_billing' ); ?>
			<?php do_action( 'states_footer' ); ?>
		</div><!-- billing_layer_info -->
		<?php return true;
	}

	function getDefaultFields() {
		$fields = array(
			'billing_firstname'		=> array(
				'label'		=> __( 'First name', 'tcp' ),
				'required'	=> true,
				'error'		=> __( 'The billing First name field must be completed', 'tcp' ),
				'input'		=> 'text',
				'attrs'		=> array(
					'size'		=> 20,
					'maxlength'	=> 255,
					'class'		=> 'form-control',
				),
			),
			'billing_lastname'		=> array(
				'label'		=> __( 'Last name', 'tcp' ),
				'required'	=> true,
				'error'		=> __( 'The billing Last name field must be completed', 'tcp' ),
				'input'		=> 'text',
				'attrs'		=> array(
					'size'		=> 40,
					'maxlength'	=> 255,
					'class'		=> 'form-control',
				),
			),
			'billing_company'		=> array(
				'label'		=> __( 'Company', 'tcp' ),
				'required'	=> true,
				'error'		=>__( 'The billing Company name must be completed', 'tcp' ),
				'input'		=> 'text',
				'attrs'		=> array(
					'size'		=> 20,
					'maxlength'	=> 255,
					'class'		=> 'form-control',
				),
			),
			'billing_tax_id_number'	=> array(
				'label'		=> __( 'Tax ID number', 'tcp' ),
				'input'		=> 'text',
				'error'		=> __( 'The billing Tax Id field must be completed', 'tcp' ),
				'attrs'		=> array(
					'size'		=> 20,
					'maxlength'	=> 255,
					'class'		=> 'form-control',
				),
			),
			'billing_country_id'	=> array(
				'callback'	=> array( $this, 'showCountry' ),//function with two parameters(id, field)
				'error'		=> __( 'The billing Country is not allowed', 'tcp' ),
			),
			'billing_region_id'		=> array(
				'callback'	=> array( $this, 'showRegion' ),
				'error'		=> __( 'The billing State field must be completed', 'tcp' ),
				'callback_required'	=> array( $this, 'requiredRegion' ),
			),
			'billing_city'			=> array(
				'callback'	=> array( $this, 'showCity' ),
				'error'		=> __( 'The billing City field must be completed', 'tcp' ),
			),
			'billing_street'		=> array(
				'label'		=> __( 'Address 1', 'tcp' ),
				'required'	=> true,
				'input'		=> 'text',
				'error'		=> __( 'The billing Address field must be completed', 'tcp' ),
				'attrs'		=> array(
					'size'		=> 20,
					'maxlength'	=> 255,
					'class'		=> 'form-control',
				),
			),
			'billing_street_2'		=> array(
				'label'		=> __( 'Address 2', 'tcp' ),
				'required'	=> false,
				'input'		=> 'text',
				//'error'		=> __( 'The billing Street 2 field must be completed', 'tcp' ),
				'attrs'		=> array(
					'size'		=> 20,
					'maxlength'	=> 255,
					'class'		=> 'form-control',
				),
			),
			'billing_postcode'		=> array(
				'label'		=> __( 'Postal code', 'tcp' ),
				'required'	=> true,
				'error'		=> __( 'The billing Postcode field must be completed', 'tcp' ),
				'input'		=> 'text',
				'attrs'		=> array(
					'size'		=> 10,
					'maxlength'	=> 10,
					'class'		=> 'form-control',
				),
			),
			'billing_telephone_1'	=> array(
				'label'		=> __( 'Telephone 1', 'tcp' ),
				'input'		=> 'text',
				'error'		=> __( 'The billing Telephone field must be completed', 'tcp' ),
				'attrs'		=> array(
					'size'		=> 15,
					'maxlength'	=> 20,
					'class'		=> 'form-control',
				),
			),
			'billing_telephone_2'	=> array(
				'label'		=> __( 'Telephone 2', 'tcp' ),
				'input'		=> 'text',
				'error'		=> __( 'The billing Second Telephone field must be completed', 'tcp' ),
				'attrs'		=> array(
					'size'		=> 15,
					'maxlength'	=> 20,
					'class'		=> 'form-control',
				),
			),
			'billing_fax'			=> array(
				'label'		=> __( 'Fax', 'tcp' ),
				'input'		=> 'text',
				'error'		=> __( 'The billing Fax field must be completed', 'tcp' ),
				'attrs'		=> array(
					'size'		=> 15,
					'maxlength'	=> 20,
					'class'		=> 'form-control',
				),
			),
			'billing_email'			=> array(
				'label'		=> __( 'Email', 'tcp' ),
				'required'	=> true,
				'error'		=>  __( 'The billing eMail field must be completed and valid', 'tcp' ),
				'input'		=> 'email',
				'attrs'		=> array(
					'size'		=> 20,
					'maxlength'	=> 255,
					'class'		=> 'form-control',
				),
			),
		);
		return apply_filters( 'tcp_fields_billing_checkout', $fields ) ;
	}

	function showCountry( $id, $field ) {
		$active			= isset( $field['active'] ) ? $field['active'] : true;
		if ( ! $active ) return;
		$required		= isset( $field['required'] ) ? $field['required'] : false;
		$country_id		= $field['value']; ?>
		<label for="billing_country_id"><?php _e( 'Country', 'tcp' ); ?>:<?php if ( $required ) echo '<em>*</em>'; ?></label>
		<?php global $thecartpress;
		//Default country
		$country		= $thecartpress->get_setting( 'country', '' );
		//Allowed countries
		$billing_isos	= $thecartpress->get_setting( 'billing_isos', false );
		//Getting allowed countries info
		if ( $billing_isos ) {
			$countries	= TCPCountries::getSome( $billing_isos, tcp_get_current_language_iso() );
		} else {
			$countries	= TCPCountries::getAll( tcp_get_current_language_iso() );
		}
		//Get current selected country (if available)
		$country_bill	= $country_id;
		//If no country selected, set the default country
		if ( $country_bill == '' ) $country_bill = $country; ?>
		<select id="billing_country_id" name="billing_country_id" class="form-control">
		<?php foreach( $countries as $item ) { ?>
			<option value="<?php echo $item->iso;?>" <?php selected( $item->iso, $country_bill ); ?>><?php echo $item->name; ?></option>
		<?php } ?>
		</select>
	<?php }

	function showRegion( $id, $field ) {
		$active		= isset( $field['active'] ) ? $field['active'] : true;
		if ( ! $active ) return;
		$required	= isset( $field['required'] ) ? $field['required'] : false;
		$region_id	= $field['value'];
		if ( isset( $_REQUEST['billing_region'] ) ) {
			$region = $_REQUEST['billing_region'];
		} elseif ( isset( $_SESSION['tcp_checkout']['billing']['billing_region'] ) ) {
			$region = $_SESSION['tcp_checkout']['billing']['billing_region'];
		} else {
			$region = $this->default_address ? stripslashes( $this->default_address->region ) : '';
		} ?>
		<label for="billing_region_id"><?php _e( 'Region', 'tcp' ); ?>:<?php if ( $required ) echo '<em>*</em>'; ?></label>
		<?php $regions = apply_filters( 'tcp_load_regions_for_billing', false ); //array( 'id' => array( 'name'), 'id' => array( 'name'), ... )	?>
		<select id="billing_region_id" name="billing_region_id" <?php if ( is_array( $regions ) && count( $regions ) > 0 ) {} else { echo 'style="display:none;"'; }?> class="form-control">
			<option value=""><?php _e( 'No state selected', 'tcp' ); ?></option>
		<?php if ( is_array( $regions ) && count( $regions ) > 0 ) foreach( $regions as $id => $region_item ) { ?>
			<option value="<?php echo $id;?>" <?php selected( $id, $region_id ); ?>><?php echo $region_item['name']; ?></option>
		<?php } ?>
		</select>
		<input type="hidden" id="billing_region_selected_id" value="<?php echo $region_id; ?>"/>
		<?php $this->showErrorMsg( 'billing_region_id' ); ?>
		<input type="text" id="billing_region" name="billing_region" value="<?php echo $region; ?>" size="20" maxlength="255" <?php if ( is_array( $regions ) && count( $regions ) > 0 ) echo 'style="display:none;"';?>/>
		<?php $this->showErrorMsg( 'billing_region' ); ?>
	<?php }

	function showCity( $id, $field ) {
		$active		= isset( $field['active'] ) ? $field['active'] : true;
		if ( ! $active ) return;
		$required	= isset( $field['required'] ) ? $field['required'] : false;
		$city_id	= $field['value'];
		if ( isset( $_REQUEST['billing_city'] ) ) {
			$city	= $_REQUEST['billing_city'];
		} elseif ( isset( $_SESSION['tcp_checkout']['billing']['billing_city'] ) ) {
			$city	= $_SESSION['tcp_checkout']['billing']['billing_city'];
		} else {
			$city	= $this->default_address ? $this->default_address->city : '';
		} ?>
		<label for="billing_city_id"><?php _e( 'City', 'tcp' ); ?>:<?php if ( $required ) echo '<em>*</em>'; ?></label>
		<?php
		$cities		= array(); //array( 'id' => array( 'name'), 'id' => array( 'name'), ... )
		$cities		= apply_filters( 'tcp_load_cities_for_billing', $cities );
		if ( is_array( $cities ) && count( $cities ) > 0 ) { ?>
			<select id="billing_city_id" name="billing_city_id" class="form-control">
			<?php foreach( $cities as $id => $city ) { ?>
				<option value="<?php echo $id;?>" <?php selected( $id, $city_id ); ?>><?php echo $city['name'];?></option>
			<?php } ?>
			</select>
			<?php $this->showErrorMsg( 'billing_city_id' ); ?>
		<?php } else { ?>
			<input type="text" id="billing_city" name="billing_city" value="<?php echo $city;?>" size="20" maxlength="255" class="form-control"/>
			<?php $this->showErrorMsg( 'billing_city' ); ?>
		<?php }
	}

	function show_config_settings() {?>
		<style>
		#tcp_field_list {
			list-style-type: none;
			margin: 0;
			padding: 0;
			width: 60%;
		}
		#tcp_field_list li { 
			margin: 0 3px 3px 3px;
			padding: 0.4em;
			padding-left: 1.5em;
			font-size: 1.1em;
			border: 1px solid #BBBBBB;
			padding: 2px;
			background: url("../images/white-grad.png") repeat-x scroll left top #F2F2F2;
			text-shadow: 0 1px 0 #FFFFFF;
			cursor: move;
		}
		.tcp-field-properties {
			padding-left: 2em;
			font-size: .9em;
		}
		</style>
		<script>
		jQuery( function() {
			jQuery( '#tcp_field_list' ).sortable();
			jQuery( '#tcp_field_list' ).disableSelection();
			jQuery( '#tcp_save_<?php echo get_class( $this ); ?>' ).click( function( e ) {
				var vals = '';
				jQuery( 'li.tcp_field_item' ).each( function( index ) {
					vals += jQuery( this).attr( 'id' ) + ',';
				});
				vals = vals.slice( 0, -1 );
				jQuery( '#tcp_field_sorting' ).val( vals );
			} );
		} );
		</script>
		<p><?php _e( 'Drag and Drop fields to sort them', 'tcp' ); ?></p>
		<?php //Get the settings related with this box
		$settings = get_option( 'tcp_' . get_class( $this ), array() );
		//Get the Ordering field
		$field_sorting = isset( $settings['sorting'] ) ? $settings['sorting'] : array();
		?>
		<input type="hidden" name="tcp_field_sorting" id="tcp_field_sorting" value="" />
		<ul id="tcp_field_list">
		<?php //Gel all the fields. At the moment, only the default ones
		$tcp_fields = $this->getDefaultFields();
		if ( is_array( $field_sorting ) && count( $field_sorting ) > 0) {
			foreach( $field_sorting as $id ) {
				if ( isset( $tcp_fields[$id] ) ) {
					$tcp_field		= $tcp_fields[$id];
					$label			= isset( $tcp_field['label'] ) ? $tcp_field['label'] : $id;
					//Each field can be actived and required
					$tcp_active		= isset( $settings['active-' . $id] ) ? $settings['active-' . $id] : true;
					if ( isset( $settings['required-' . $id] ) ) {
						$tcp_required = $settings['required-' . $id];
					} else {
						$tcp_required = isset( $tcp_field['required'] ) ? $tcp_field['required'] : false;
					}
			?>
			<li class="tcp_field_item" id="<?php echo $id; ?>">
				<strong><?php echo $label; ?></strong>
				<div class="tcp-field-properties">
					<label><input type="checkbox" value="yes" name="tcp_active-<?php echo $id; ?>" <?php checked( $tcp_active ); ?>/> <?php _e( 'Active', 'tcp' ); ?></label>
					<br/>
					<label><input type="checkbox" value="yes" name="tcp_required-<?php echo $id; ?>" <?php checked( $tcp_required ); ?>/> <?php _e( 'Required', 'tcp' ); ?></label>
				</div>
			</li>
			<?php }
			}
		}
		foreach( $tcp_fields as $id => $tcp_field ) {
			if ( ! in_array( $id, $field_sorting ) ) {
				$label			= isset( $tcp_field['label'] ) ? $tcp_field['label'] : $id;
				$tcp_active		= isset( $settings['active-' . $id] ) ? $settings['active-' . $id] : true;
				$tcp_active		= isset( $settings['active-' . $id] ) ? $settings['active-' . $id] : true;
				if ( isset( $settings['required-' . $id] ) ) {
					$tcp_required = $settings['required-' . $id];
				} else {
					$tcp_required = isset( $tcp_field['required'] ) ? $tcp_field['required'] : false;
				}
			?>
			<li class="tcp_field_item" id="<?php echo $id; ?>">
				<strong><?php echo $label; ?></strong>
				<div class="tcp-field-properties">
					<label><input type="checkbox" value="yes" name="tcp_active-<?php echo $id; ?>" <?php checked( $tcp_active ); ?>/> <?php _e( 'Active', 'tcp' ); ?></label>
					<br/>
					<label><input type="checkbox" value="yes" name="tcp_required-<?php echo $id; ?>" <?php checked( $tcp_required ); ?>/> <?php _e( 'Required', 'tcp' ); ?></label>
				</div>
			</li>
			<?php }
		}
		?>
		</ul><!-- #tcp_field_list -->

		<?php $use_as_shipping	= isset( $settings['use_as_shipping'] ) ? $settings['use_as_shipping'] : false; ?>
		<table class="form-table">
		<tbody>
		<tr valign="top">
			<th scope="row">
				<label for="use_as_shipping"><?php _e( 'Use as shipping', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input type="checkbox" name="use_as_shipping" id="use_as_shipping" value="yes" <?php checked( $use_as_shipping ); ?>/>
				<span class="description"><?php _e( 'This option only must be activated if the Shipping Box is not used', 'tcp' ); ?></span>
			</td>
		</tr>
		</tbody>
		</table>
		<?php
		return true;
	}

	function save_config_settings() {
		$settings = array();
		$settings['sorting']			= explode( ',', $_REQUEST['tcp_field_sorting'] );
		$settings['use_as_shipping']	= isset( $_REQUEST['use_as_shipping'] );
		$tcp_fields = $this->getDefaultFields();
		if ( is_array( $tcp_fields ) && count( $tcp_fields ) > 0 ) {
			foreach( $tcp_fields as $id => $field ) {
				$settings['active-' . $id]		= isset( $_REQUEST['tcp_active-' . $id] );
				$settings['required-' . $id]	= isset( $_REQUEST['tcp_required-' . $id] );
			}
		}
		update_option( 'tcp_' . get_class( $this ), $settings );
		return true;
	}

	public function showErrorMsg( $field_name ) {
		if ( isset( $this->errors[$field_name] ) ) : ?>
			<br/><span class="error"><?php echo $this->errors[$field_name];?></span>
		<?php endif;
	}

	private function check_email_address( $email ) {
		$pattern = "/^[\w-]+(\.[\w-]+)*@([0-9a-z][0-9a-z-]*[0-9a-z]\.)+([a-z]{2,4})$/i";
		if ( ! preg_match( $pattern, $email ) ) return false;
		return true;
	}
}

new TCPBillingBox();
endif; // class_exists check