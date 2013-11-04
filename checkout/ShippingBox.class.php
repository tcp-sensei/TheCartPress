<?php
/**
 * Shipping Box
 *
 * Shipping data step for the Checkout
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
if ( !defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'TCPShippingBox' ) ) {

require_once( TCP_CHECKOUT_FOLDER . 'TCPCheckoutBox.class.php' );
require_once( TCP_CLASSES_FOLDER . 'CustomForms.class.php' );

class TCPShippingBox extends TCPCheckoutBox {
	protected $default_address = false;
	protected $errors = array();

	function __construct() {
		parent::__construct();
		add_action( 'states_footer', 'tcp_states_footer_scripts' );
	}

	function get_title() {
		return __( 'Shipping Info', 'tcp' );
	}

	function get_class() {
		return 'shipping_layer';
	}

	function get_name() {
		return 'shipping';
	}

	function after_action() {
		global $thecartpress;
		//Only if a new address is typed...
		$selected_shipping_address	= isset( $_REQUEST['selected_shipping_address'] ) ? $_REQUEST['selected_shipping_address'] : 'N';
		if ( $selected_shipping_address == 'new' || $selected_shipping_address == 'N' ) {
			//Getting defalt fields
			$fields = $this->getDefaultFields();
			//Getting Saved Settings
			$settings = get_option( 'tcp_' . get_class( $this ), array() );
			//Applying active and required properties
			foreach( $fields as $id => $field ) {
				$active = isset( $settings['active-' . $id] ) ? $settings['active-' . $id] : true;
				if ( $active ) {
					$required = isset( $settings['required-' . $id] ) ? $settings['required-' . $id] : false;
					if ( $required && ( ! isset( $_REQUEST[$id] ) || strlen( $_REQUEST[$id] ) == 0 ) ) {
						$this->errors[$id] = $field['error'];
					} elseif ( $id == 'shipping_country_id' ) {
						$shipping_isos = isset( $thecartpress->settings['shipping_isos'] ) ? $thecartpress->settings['shipping_isos'] : false;
						if ( $shipping_isos && ! in_array( $_REQUEST['shipping_country_id'], $shipping_isos ) ) {
							$this->errors['shipping_country_id'] = __( 'The shipping Country is not allowed', 'tcp' );
						}
					} elseif ( $id == 'shipping_email' ) {
						if ( ! $this->check_email_address( $_REQUEST['shipping_email'] ) ) {
							$this->errors['shipping_email'] = __( 'The shipping eMail field must be a valid email', 'tcp' );
						}
					} else {
						unset( $this->errors[$id] );
					}
				}
			}
		} elseif ( $selected_shipping_address == 'Y' ) {
			global $thecartpress;
			$shipping_isos = isset( $thecartpress->settings['shipping_isos'] ) ? $thecartpress->settings['shipping_isos'] : false;
			if ( $shipping_isos ) {
				$shipping_country_id = Addresses::getCountryId( $_REQUEST['selected_shipping_id'] );
				if ( ! in_array( $shipping_country_id, $shipping_isos ) ) {
					$this->errors['shipping_country_id'] = __( 'The shipping Country is not allowed', 'tcp' );
				}
			}
		}
		$this->errors = apply_filters( 'tcp_shipping_box_after_action', $this->errors );
		if ( count( $this->errors ) > 0 ) {
			return false;
		} else {
			if ( $selected_shipping_address == 'Y' ) {
				$shipping = array(
					'selected_shipping_address'	=> 'Y',
					'selected_shipping_id'		=> isset( $_REQUEST['selected_shipping_id'] ) ? $_REQUEST['selected_shipping_id'] : 0,
				);
			} elseif ( $selected_shipping_address == 'BIL' ) {
				$shipping = array(
					'selected_shipping_address'	=> 'BIL',
				);
			} else {
				$shipping = array(
					'selected_shipping_address'	=> 'new',
					'shipping_firstname'		=> isset( $_REQUEST['shipping_firstname'] ) ? $_REQUEST['shipping_firstname'] : '',
					'shipping_lastname'			=> isset( $_REQUEST['shipping_lastname'] ) ? $_REQUEST['shipping_lastname'] : '',
					'shipping_company'			=> isset( $_REQUEST['shipping_company'] ) ? $_REQUEST['shipping_company'] : '',
					'shipping_country'			=> isset( $_REQUEST['shipping_country'] ) ? $_REQUEST['shipping_country'] : '',
					'shipping_country_id'		=> isset( $_REQUEST['shipping_country_id'] ) ? $_REQUEST['shipping_country_id'] : 0,
					'shipping_region'			=> isset( $_REQUEST['shipping_region'] ) ? $_REQUEST['shipping_region'] : '',
					'shipping_region_id'		=> isset( $_REQUEST['shipping_region_id'] ) ? $_REQUEST['shipping_region_id'] : 0,
					'shipping_city'				=> isset( $_REQUEST['shipping_city'] ) ? $_REQUEST['shipping_city'] : '',
					'shipping_city_id'			=> isset( $_REQUEST['shipping_city_id'] ) ? $_REQUEST['shipping_city_id'] : 0,
					'shipping_street'			=> isset( $_REQUEST['shipping_street'] ) ? $_REQUEST['shipping_street'] : '',
					'shipping_street_2'			=> isset( $_REQUEST['shipping_street_2'] ) ? $_REQUEST['shipping_street_2'] : '',
					'shipping_postcode'			=> isset( $_REQUEST['shipping_postcode'] ) ? str_replace( ' ' , '', $_REQUEST['shipping_postcode'] ) : '',
					'shipping_telephone_1'		=> isset( $_REQUEST['shipping_telephone_1'] ) ? $_REQUEST['shipping_telephone_1'] : '',
					'shipping_telephone_2'		=> isset( $_REQUEST['shipping_telephone_2'] ) ? $_REQUEST['shipping_telephone_2'] : '',
					'shipping_fax'				=> isset( $_REQUEST['shipping_fax'] ) ? $_REQUEST['shipping_fax'] : '',
					'shipping_email'				=> isset( $_REQUEST['shipping_email'] ) ? $_REQUEST['shipping_email'] : '',
				);
			}
			$_SESSION['tcp_checkout']['shipping'] = $shipping;
			return apply_filters( 'tcp_after_shipping_box', true );
		}
	}

	function show() {
		if ( isset( $_REQUEST['selected_shipping_address'] ) ) {
			$selected_shipping_address = $_REQUEST['selected_shipping_address'];
		} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['selected_shipping_address'] ) ) {
			$selected_shipping_address = $_SESSION['tcp_checkout']['shipping']['selected_shipping_address'];
		} else {
			$selected_shipping_address = 'BIL';
		}
		if ( $selected_shipping_address == 'Y' && ! is_user_logged_in() ) $selected_shipping_address = 'new';
		?>
		<div class="checkout_info clearfix" id="shipping_layer_info">
		<?php global $current_user;
		get_currentuserinfo();
		if ( $current_user->ID > 0 ) $addresses = Addresses::getCustomerAddresses( $current_user->ID );
		else $addresses = false;
		if ( is_array( $addresses ) && count( $addresses ) > 0 ) :
			if ( $selected_shipping_address === false ) $selected_shipping_address = 'Y';
			if ( isset( $_REQUEST['selected_shipping_id'] ) ) {
				$default_address_id = $_REQUEST['selected_shipping_id'];
			} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['selected_shipping_id'] ) ) {
				$default_address_id = $_SESSION['tcp_checkout']['shipping']['selected_shipping_id'];
			} else {
				$this->default_address = Addresses::getCustomerDefaultShippingAddress( $current_user->ID );
				$default_address_id = $this->default_address ? $this->default_address->address_id : 0;
			} ?>
			<div id="selected_shipping_area" <?php if ( $selected_shipping_address == 'new' ) : ?>style="display:none"<?php endif;?>>
				<label for="selected_shipping_id"> <?php _e( 'Select shipping address:', 'tcp' ); ?></label>
				<br />
				<select id="selected_shipping_id" name="selected_shipping_id">
				<?php foreach( $addresses as $address ) : ?>
					<option value="<?php echo $address->address_id;?>" <?php selected( $address->address_id, $default_address_id ); ?>><?php echo stripslashes( $address->street . ', ' . $address->city ); ?></option>
				<?php endforeach;?>
				</select>
				<?php if ( $selected_shipping_address == 'Y' ) $this->showErrorMsg( 'shipping_country_id' ); ?>
			</div> <!-- selected_shipping_area -->
			<label for="selected_shipping_address">
				<input type="radio" id="selected_shipping_address" name="selected_shipping_address" value="Y"<?php if ( ( $selected_shipping_address == 'Y' && count( $addresses ) > 0 ) ) : ?> checked="true"<?php endif;?> onChange="jQuery('#selected_shipping_area').show();jQuery('#new_shipping_area').hide();" />
				<?php _e( 'Shipping to the address selected', 'tcp' ); ?>
			</label>
			<br />
		<?php endif;?>
			<span id="p_use_billing_address">
				<label for="use_billing_address">
					<input type="radio" id="use_billing_address" name="selected_shipping_address" value="BIL" <?php checked( $selected_shipping_address, 'BIL' ); ?> onChange="jQuery('#selected_shipping_area').hide();jQuery('#new_shipping_area').hide();" />
					<?php _e( 'Use billing address', 'tcp' );?>
				</label>
				<?php if ( $selected_shipping_address == 'BIL' ) $this->showErrorMsg( 'shipping_country_id' );?>
				<br/>
			</span>

			<label for="new_shipping_address">
				<input type="radio" id="new_shipping_address" name="selected_shipping_address" value="new" <?php if ( $selected_shipping_address == 'new' || count( $addresses ) == 0 ) : ?> checked="true"<?php endif;?> onChange="jQuery('#new_shipping_area').show();jQuery('#selected_shipping_area').hide();" />
				<?php _e( 'New shipping address', 'tcp' ); ?>
			</label>
			<div id="new_shipping_area" class="clearfix" <?php if ( $selected_shipping_address != 'new' ) : ?>style="display:none"<?php endif;?><?php
				if ( $selected_shipping_address == 'new' ) :
				?><?php elseif ( is_array( $addresses ) && count( $addresses ) > 0 ) :
					?>style="display:none"<?php
				endif;?>><?php
			//Getting defalt fields
			$fields = $this->getDefaultFields();
			if ( isset( $_REQUEST['shipping_firstname'] ) ) {
				$firstname = $_REQUEST['shipping_firstname'];
			} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['shipping_firstname'] ) ) {
				$firstname = $_SESSION['tcp_checkout']['shipping']['shipping_firstname'];
			} elseif ( $this->default_address ) {
				$firstname = stripslashes( $this->default_address->firstname );
			} elseif ( $current_user && $current_user instanceof WP_User ) {
				$firstname = $current_user->first_name;
			} else {
				$firstname = '';
			}
			$fields['shipping_firstname']['value'] = $firstname;

			if ( isset( $_REQUEST['shipping_lastname'] ) ) {
				$lastname = $_REQUEST['shipping_lastname'];
			} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['shipping_lastname'] ) ) {
				$lastname = $_SESSION['tcp_checkout']['shipping']['shipping_lastname'];
			} elseif ( $this->default_address ) {
				$lastname = stripslashes( $this->default_address->lastname );
			} elseif ( $current_user && $current_user instanceof WP_User ) {
				$lastname = $current_user->last_name;
			} else {
				$lastname = '';
			}
			$fields['shipping_lastname']['value'] = $lastname;

			if ( isset( $_REQUEST['shipping_company'] ) ) {
				$company = $_REQUEST['shipping_company'];
			} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['shipping_company'] ) ) {
				$company = $_SESSION['tcp_checkout']['shipping']['shipping_company'];
			} else {
				$company = $this->default_address ? stripslashes( $this->default_address->company ) : '';
			}
			$fields['shipping_company']['value'] = $company;
			
			if ( isset( $_REQUEST['shipping_street'] ) ) {
				$street = $_REQUEST['shipping_street'];
			} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['shipping_street'] ) ) {
				$street = $_SESSION['tcp_checkout']['shipping']['shipping_street'];
			} else {
				$street = $this->default_address ? stripslashes( $this->default_address->street ) : '';
			}
			$fields['shipping_street']['value'] = $street;

			if ( isset( $_REQUEST['shipping_street_2'] ) ) {
				$street_2 = $_REQUEST['shipping_street_2'];
			} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['shipping_street_2'] ) ) {
				$street_2 = $_SESSION['tcp_checkout']['shipping']['shipping_street_2'];
			} else {
				$street_2 = $this->default_address ? stripslashes( $this->default_address->street_2 ) : '';
			}
			$fields['shipping_street_2']['value'] = $street_2;

			if ( isset( $_REQUEST['shipping_city_id'] ) ) {
				$city_id = $_REQUEST['shipping_city_id'];
			} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['shipping_city_id'] ) ) {
				$city_id = $_SESSION['tcp_checkout']['shipping']['shipping_city_id'];
			} else {
				$city_id = $this->default_address ? $this->default_address->city_id : '';
			} //not ready TODO
			if ( isset( $_REQUEST['shipping_city'] ) ) {
				$city = $_REQUEST['shipping_city'];
			} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['shipping_city'] ) ) {
				$city = $_SESSION['tcp_checkout']['shipping']['shipping_city'];
			} else {
				$city = $this->default_address ? $this->default_address->city : '';
			}
			$fields['shipping_city']['value'] = $city;

			if ( isset( $_REQUEST['shipping_region_id'] ) ) {
				$region_id = $_REQUEST['shipping_region_id'];
			} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['shipping_region_id'] ) ) {
				$region_id = $_SESSION['tcp_checkout']['shipping']['shipping_region_id'];
			} else {
				$region_id = $this->default_address ? $this->default_address->region_id : '';
			}
			if ( isset( $_REQUEST['shipping_region'] ) ) {
				$region = $_REQUEST['shipping_region'];
			} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['shipping_region'] ) ) {
				$region = $_SESSION['tcp_checkout']['shipping']['shipping_region'];
			} else {
				$region = $this->default_address ? stripslashes( $this->default_address->region ) : '';
			}
			$fields['shipping_region_id']['value'] = $region_id;

			if ( isset( $_REQUEST['shipping_postcode'] ) ) {
				$postcode = $_REQUEST['shipping_postcode'];
			} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['shipping_postcode'] ) ) {
				$postcode = $_SESSION['tcp_checkout']['shipping']['shipping_postcode'];
			} else {
				$postcode = $this->default_address ? str_replace( ' ' , '', $this->default_address->postcode ) : '';
			}
			$fields['shipping_postcode']['value'] = $postcode;

			if ( isset( $_REQUEST['shipping_country_id'] ) ) {
				$country_id = $_REQUEST['shipping_country_id'];
			} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['shipping_country_id'] ) ) {
				$country_id = $_SESSION['tcp_checkout']['shipping']['shipping_country_id'];
			} else {
				$country_id = $this->default_address ? $this->default_address->country_id : '';
			}
			$fields['shipping_country_id']['value'] = $country_id;

			if ( isset( $_REQUEST['shipping_telephone_1'] ) ) {
				$telephone_1 = $_REQUEST['shipping_telephone_1'];
			} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['shipping_telephone_1'] ) ) {
				$telephone_1 = $_SESSION['tcp_checkout']['shipping']['shipping_telephone_1'];
			} else {
				$telephone_1 = $this->default_address ? $this->default_address->telephone_1 : '';
			}
			$fields['shipping_telephone_1']['value'] = $telephone_1;
			
			if ( isset( $_REQUEST['shipping_telephone_2'] ) ) {
				$telephone_2 = $_REQUEST['shipping_telephone_2'];
			} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['shipping_telephone_2'] ) ) {
				$telephone_2 = $_SESSION['tcp_checkout']['shipping']['shipping_telephone_2'];
			} else {
				$telephone_2 = $this->default_address ? $this->default_address->telephone_2 : '';
			}
			$fields['shipping_telephone_2']['value'] = $telephone_2;
			
			if ( isset( $_REQUEST['shipping_fax'] ) ) {
				$fax = $_REQUEST['shipping_fax'];
			} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['shipping_fax'] ) ) {
				$fax = $_SESSION['tcp_checkout']['shipping']['shipping_fax'];
			} else {
				$fax = $this->default_address ? $this->default_address->fax : '';
			}
			$fields['shipping_fax']['value'] = $fax;
			
			if ( isset( $_REQUEST['shipping_email'] ) ) {
				$email = $_REQUEST['shipping_email'];
			} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['shipping_email'] ) ) {
				$email = $_SESSION['tcp_checkout']['shipping']['shipping_email'];
			} elseif ( $this->default_address ) {
				$email = $this->default_address->email;
			} elseif ( $current_user ) { //&& $current_user instanceof WP_User ) {
				$email = '';//$current_user->email;
			} else {
				$email = '';
			}
			$fields['shipping_email']['value'] = $email;
			
			//Getting Saved Settings
			$settings = get_option( 'tcp_' . get_class( $this ), array() );
			//Get the Ordering field
			$sorting = isset( $settings['sorting'] ) ? $settings['sorting'] : array();
			//Applying active and required properties
			foreach( $fields as $id => $field ) {
				$active		= isset( $settings['active-' . $id] ) ? $settings['active-' . $id] : true;
				$required	= isset( $settings['required-' . $id] ) ? $settings['required-' . $id] : false;
				if ( $active ) {
					$fields[$id]['required'] = $required;
				 } else {
				 	unset( $fields[$id] );
				 }
			}
			?>
				<ul>
					<?php TCPCustomForms::showCheckout( $fields, $this, $sorting ); ?>
				</ul>
			</div> <!-- new_shipping_area -->
			<?php tcp_do_template( 'tcp_checkout_shipping_notice' ); ?>
			<?php do_action( 'tcp_checkout_shipping' ); ?>
			<?php do_action( 'states_footer' ); ?>
		</div><!-- shipping_layer_info -->
		<?php return true;
	}

	function getDefaultFields() {
		$fields = array(
			'shipping_firstname'		=> array(
				'label'		=> __( 'First name', 'tcp' ),
				'required'	=> true,
				'error'		=> __( 'The shipping First name field must be completed', 'tcp' ),
				'input'		=> 'text',
				'attrs'		=> array(
					'size'		=> 20,
					'maxlength'	=> 255,
				),
			),
			'shipping_lastname'		=> array(
				'label'		=> __( 'Last name', 'tcp' ),
				'required'	=> true,
				'error'		=> __( 'The shipping Last name field must be completed', 'tcp' ),
				'input'		=> 'text',
				'attrs'		=> array(
					'size'		=> 40,
					'maxlength'	=> 255,
				),
			),
			'shipping_company'		=> array(
				'label'		=> __( 'Company', 'tcp' ),
				'error'		=>__( 'The shipping Company name must be completed', 'tcp' ),
				'input'		=> 'text',
				'attrs'		=> array(
					'size'		=> 20,
					'maxlength'	=> 255,
				),
			),
			'shipping_country_id'	=> array(
				'callback'	=> array( $this, 'showCountry' ),//function with two parameters(id, field)
				'error'		=> __( 'The shipping Country is not allowed', 'tcp' ),
			),
			'shipping_region_id'		=> array(
				'callback'	=> array( $this, 'showRegion' ),
				'error'		=> __( 'The shipping State field must be completed', 'tcp' ),
			),
			'shipping_city'			=> array(
				'callback'	=> array( $this, 'showCity' ),
				'error'		=> __( 'The shipping City field must be completed', 'tcp' ),
			),
			'shipping_street'		=> array(
				'label'		=> __( 'Address', 'tcp' ),
				'required'	=> true,
				'input'		=> 'text',
				'error'		=> __( 'The shipping Street field must be completed', 'tcp' ),
				'attrs'		=> array(
					'size'		=> 20,
					'maxlength'	=> 255,
				),
			),
			'shipping_street_2'		=> array(
				'label'		=> __( 'Address 2', 'tcp' ),
				'required'	=> true,
				'input'		=> 'text',
				'error'		=> __( 'The shipping Street 2 field must be completed', 'tcp' ),
				'attrs'		=> array(
					'size'		=> 20,
					'maxlength'	=> 255,
				),
			),
			'shipping_postcode'		=> array(
				'label'		=> __( 'Postal code', 'tcp' ),
				'required'	=> true,
				'error'		=> __( 'The shipping Postcode field must be completed', 'tcp' ),
				'input'		=> 'text',
				'attrs'		=> array(
					'size'		=> 10,
					'maxlength'	=> 10,
				),
			),
			'shipping_telephone_1'	=> array(
				'label'		=> __( 'Telephone 1', 'tcp' ),
				'input'		=> 'text',
				'error'		=> __( 'The shipping Telephone field must be completed', 'tcp' ),
				'attrs'		=> array(
					'size'		=> 10,
					'maxlength'	=> 10,
				),
			),
			'shipping_telephone_2'	=> array(
				'label'		=> __( 'Telephone 2', 'tcp' ),
				'input'		=> 'text',
				'error'		=> __( 'The shipping Second Telephone field must be completed', 'tcp' ),
				'attrs'		=> array(
					'size'		=> 10,
					'maxlength'	=> 10,
				),
			),
			'shipping_fax'			=> array(
				'label'		=> __( 'Fax', 'tcp' ),
				'input'		=> 'text',
				'error'		=> __( 'The shipping Fax field must be completed', 'tcp' ),
				'attrs'		=> array(
					'size'		=> 15,
					'maxlength'	=> 20,
				),
			),
			'shipping_email'			=> array(
				'label'		=> __( 'Email', 'tcp' ),
				'required'	=> true,
				'error'		=>  __( 'The shipping eMail field must be completed and valid', 'tcp' ),
				'input'		=> 'email',
				'attrs'		=> array(
					'size'		=> 20,
					'maxlength'	=> 255,
				),
			),
		);
		return apply_filters( 'tcp_fields_shipping_checkout', $fields ) ;
	}

	function showCountry( $id, $field ) {
		$active			= isset( $field['active'] ) ? $field['active'] : true;
		if ( ! $active ) return;
		$required		= isset( $field['required'] ) ? $field['required'] : false;
		$country_id		= $field['value']; ?>
		<label for="shipping_country_id"><?php _e( 'Country', 'tcp' ); ?>:<?php if ( $required ) echo '<em>*</em>'; ?></label>
		<?php global $thecartpress;
		//Default country
		$country		= $thecartpress->get_setting( 'country', '' );
		//Allowed countries
		$shipping_isos	= $thecartpress->get_setting( 'shipping_isos', false );
		//Getting allowed countries info
		if ( $shipping_isos ) {
			$countries	= Countries::getSome( $shipping_isos, tcp_get_current_language_iso() );
		} else {
			$countries	= Countries::getAll( tcp_get_current_language_iso() );
		}
		//Get current selected country (if available)
		$country_bill	= $country_id;
		//If no country selected, set the default country
		if ( $country_bill == '' ) $country_bill = $country; ?>
		<select id="shipping_country_id" name="shipping_country_id">
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
		if ( isset( $_REQUEST['shipping_region'] ) ) {
			$region = $_REQUEST['shipping_region'];
		} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['shipping_region'] ) ) {
			$region = $_SESSION['tcp_checkout']['shipping']['shipping_region'];
		} else {
			$region = $this->default_address ? stripslashes( $this->default_address->region ) : '';
		} ?>
		<label for="shipping_region_id"><?php _e( 'Region', 'tcp' ); ?>:<?php if ( $required ) echo '<em>*</em>'; ?></label>
		<?php $regions = apply_filters( 'tcp_load_regions_for_shipping', false ); //array( 'id' => array( 'name'), 'id' => array( 'name'), ... )	?>
		<select id="shipping_region_id" name="shipping_region_id" <?php if ( is_array( $regions ) && count( $regions ) > 0 ) {} else { echo 'style="display:none;"'; }?>>
			<option value=""><?php _e( 'No state selected', 'tcp' ); ?></option>
		<?php if ( is_array( $regions ) && count( $regions ) > 0 ) foreach( $regions as $id => $region_item ) { ?>
			<option value="<?php echo $id;?>" <?php selected( $id, $region_id ); ?>><?php echo $region_item['name']; ?></option>
		<?php } ?>
		</select>
		<input type="hidden" id="shipping_region_selected_id" value="<?php echo $region_id; ?>"/>
		<?php $this->showErrorMsg( 'shipping_region_id' ); ?>
		<input type="text" id="shipping_region" name="shipping_region" value="<?php echo $region; ?>" size="20" maxlength="255" <?php if ( is_array( $regions ) && count( $regions ) > 0 ) echo 'style="display:none;"';?>/>
		<?php $this->showErrorMsg( 'shipping_region' ); ?>
	<?php }

	function showCity( $id, $field ) {
		$active		= isset( $field['active'] ) ? $field['active'] : true;
		if ( ! $active ) return;
		$required	= isset( $field['required'] ) ? $field['required'] : false;
		$city_id	= $field['value'];
		if ( isset( $_REQUEST['shipping_city'] ) ) {
			$city	= $_REQUEST['shipping_city'];
		} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['shipping_city'] ) ) {
			$city	= $_SESSION['tcp_checkout']['shipping']['shipping_city'];
		} else {
			$city	= $this->default_address ? $this->default_address->city : '';
		} ?>
		<label for="shipping_city_id"><?php _e( 'City', 'tcp' ); ?>:<?php if ( $required ) echo '<em>*</em>'; ?></label>
		<?php
		$cities		= array(); //array( 'id' => array( 'name'), 'id' => array( 'name'), ... )
		$cities		= apply_filters( 'tcp_load_cities_for_shipping', $cities );
		if ( is_array( $cities ) && count( $cities ) > 0 ) { ?>
			<select id="shipping_city_id" name="shipping_city_id">
			<?php foreach( $cities as $id => $city ) { ?>
				<option value="<?php echo $id;?>" <?php selected( $id, $city_id ); ?>><?php echo $city['name'];?></option>
			<?php } ?>
			</select>
			<?php $this->showErrorMsg( 'shipping_city_id' ); ?>
		<?php } else { ?>
			<input type="text" id="shipping_city" name="shipping_city" value="<?php echo $city;?>" size="20" maxlength="255" />
			<?php $this->showErrorMsg( 'shipping_city' ); ?>
		<?php }
	}

	function show_config_settings() {?>
		<style>
		#tcp_shipping_field_list {
			list-style-type: none;
			margin: 0;
			padding: 0;
			width: 60%;
		}
		#tcp_shipping_field_list li { 
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
			jQuery( '#tcp_shipping_field_list' ).sortable();
			jQuery( '#tcp_shipping_field_list' ).disableSelection();
			jQuery( '#tcp_save_<?php echo get_class( $this ); ?>' ).click( function( e ) {
				var vals = '';
				jQuery( 'li.tcp_shipping_field_item' ).each( function( index ) {
					vals += jQuery( this).attr( 'id' ) + ',';
				});
				vals = vals.slice( 0, -1 );
				jQuery( '#tcp_shipping_field_sorting' ).val( vals );
			} );
		} );
		</script>
		<p><?php _e( 'Drag and Drop fields to sort them', 'tcp' ); ?></p>
		<?php //Get the settings related with this box
		$settings		= get_option( 'tcp_' . get_class( $this ), array() );
		//Get the Ordering field
		$field_sorting	= isset( $settings['sorting'] ) ? $settings['sorting'] : array();
		?>
		<input type="hidden" name="tcp_shipping_field_sorting" id="tcp_shipping_field_sorting" value="" />
		<ul id="tcp_shipping_field_list">
		<?php //Gel all the fields. At the moment, only the default ones
		$tcp_fields = $this->getDefaultFields();
		if ( is_array( $field_sorting ) && count( $field_sorting ) > 0 ) {
			foreach( $field_sorting as $id ) {
				if ( isset( $tcp_fields[$id] ) ) {
					$tcp_field	= $tcp_fields[$id];
					$label		= isset( $tcp_field['label'] ) ? $tcp_field['label'] : $id;
					//Each field can be actived and required
					$tcp_active	= isset( $settings['active-' . $id] ) ? $settings['active-' . $id] : true;
					if ( isset( $settings['required-' . $id] ) ) {
						$tcp_required = $settings['required-' . $id];
					} else {
						$tcp_required = isset( $tcp_field['required'] ) ? $tcp_field['required'] : false;
					}
					?>
			<li class="tcp_shipping_field_item" id="<?php echo $id; ?>">
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
				$label		= isset( $tcp_field['label'] ) ? $tcp_field['label'] : $id;
				$tcp_active	= isset( $settings['active-' . $id] ) ? $settings['active-' . $id] : true;
				if ( isset( $settings['required-' . $id] ) ) {
					$tcp_required = $settings['required-' . $id];
				} else {
					$tcp_required = isset( $tcp_field['required'] ) ? $tcp_field['required'] : false;
				}
				?>
			<li class="tcp_shipping_field_item" id="<?php echo $id; ?>">
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
		<?php
		return true;
	}

	function save_config_settings() {
		$settings = array();
		$settings['sorting']			= explode( ',', $_REQUEST['tcp_shipping_field_sorting'] );
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

new TCPShippingBox();
} // class_exists check