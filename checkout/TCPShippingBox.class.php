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

class TCPShippingBox extends TCPCheckoutBox {
	private $errors = array();

	function __construct() {
		parent::__construct();
		add_action( 'wp_footer', 'tcp_states_footer_scripts' );
	}

	function get_title() {
		return __( 'Shipping options', 'tcp' );
	}

	function get_class() {
		return 'shipping_layer';
	}

	function before_action() {
		$shoppingCart = TheCartPress::getShoppingCart();
		if ( $shoppingCart->isDownloadable() ) {
			unset( $_SESSION['tcp_checkout']['shipping'] );
			return 1;
		} else {
			return 0;
		}
	}

	function after_action() {
		$selected_shipping_address = isset( $_REQUEST['selected_shipping_address'] ) ? $_REQUEST['selected_shipping_address'] : 'N';
		if ( $selected_shipping_address == 'new' ) {
			if ( ! isset( $_REQUEST['shipping_firstname'] ) || strlen( $_REQUEST['shipping_firstname'] ) == 0 )
				$this->errors['shipping_firstname'] = __( 'The shipping First name field must be completed', 'tcp' );
			if ( ! isset( $_REQUEST['shipping_lastname'] ) || strlen( $_REQUEST['shipping_lastname'] ) == 0 )
				$this->errors['shipping_lastname'] = __( 'The shipping Last name field must be completed', 'tcp' );
			if ( ! isset( $_REQUEST['shipping_street'] ) || strlen( $_REQUEST['shipping_street'] ) == 0 )
				$this->errors['shipping_street'] = __( 'The shipping Street field must be completed', 'tcp' );
			if ( isset( $_REQUEST['shipping_city'] ) && strlen( $_REQUEST['shipping_city'] ) == 0 )
				$this->errors['shipping_city'] = __( 'The shipping City field must be completed', 'tcp' );
			if ( isset( $_REQUEST['shipping_region'] ) && strlen( $_REQUEST['shipping_region'] ) == 0 && $_REQUEST['shipping_region_id'] == '' )
				$this->errors['shipping_region'] = __( 'The shipping Region field must be completed', 'tcp' );
			global $thecartpress;
			$shipping_isos = $thecartpress->get_setting( 'shipping_isos', false );
			if ( $shipping_isos ) {
				if ( ! in_array( $_REQUEST['shipping_country_id'], $shipping_isos ) ) {
					$this->errors['shipping_country_id'] = __( 'The shipping Country is not allowed', 'tcp' );
				}
			}
			if ( ! isset( $_REQUEST['shipping_postcode'] ) || strlen( $_REQUEST['shipping_postcode'] ) == 0 )
				$this->errors['shipping_postcode'] = __( 'The shipping Postcode field must be completed', 'tcp' );
			if ( ! isset( $_REQUEST['shipping_email'] ) || strlen( $_REQUEST['shipping_email'] ) == 0 )
				$this->errors['shipping_email'] = __( 'The shipping eMail field must be completed', 'tcp' );
			elseif ( ! $this->check_email_address( $_REQUEST['shipping_email'] ) ) 
				$this->errors['shipping_email'] = __( 'The shipping eMail field must be a valid email', 'tcp' );
		} elseif ( $selected_shipping_address == 'Y' ) { // && is_user_logged_in() ) {
			global $thecartpress;
			$shipping_isos = $thecartpress->get_setting( 'shipping_isos', false );
			if ( $shipping_isos ) {
				$shipping_country_id = Addresses::getCountryId( $_REQUEST['selected_shipping_id'] );
				if ( ! in_array( $shipping_country_id, $shipping_isos ) ) {
					$this->errors['shipping_country_id'] = __( 'The shipping Country is not allowed', 'tcp' );
				}
			}
//		} elseif ( $selected_shipping_address == 'Y' ) {
//			$selected_shipping_address = 'new';
		}
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
					'shipping_tax_id_number'	=> isset( $_REQUEST['shipping_tax_id_number'] ) ? $_REQUEST['shipping_tax_id_number'] : '',
					'shipping_country'			=> isset( $_REQUEST['shipping_country'] ) ? $_REQUEST['shipping_country'] : '',
					'shipping_country_id'		=> isset( $_REQUEST['shipping_country_id'] ) ? $_REQUEST['shipping_country_id'] : 0,
					'shipping_region'			=> isset( $_REQUEST['shipping_region'] ) ? $_REQUEST['shipping_region'] : '',
					'shipping_region_id'		=> isset( $_REQUEST['shipping_region_id'] ) ? $_REQUEST['shipping_region_id'] : 0,
					'shipping_city'				=> isset( $_REQUEST['shipping_city'] ) ? $_REQUEST['shipping_city'] : '',
					'shipping_city_id'			=> isset( $_REQUEST['shipping_city_id'] ) ? $_REQUEST['shipping_city_id'] : 0,
					'shipping_street'			=> isset( $_REQUEST['shipping_street'] ) ? $_REQUEST['shipping_street'] : '',
					'shipping_postcode'			=> isset( $_REQUEST['shipping_postcode'] ) ? str_replace( ' ' , '', $_REQUEST['shipping_postcode'] ): '',
					'shipping_telephone_1'		=> isset( $_REQUEST['shipping_telephone_1'] ) ? $_REQUEST['shipping_telephone_1'] : '',
					'shipping_telephone_2'		=> isset( $_REQUEST['shipping_telephone_2'] ) ? $_REQUEST['shipping_telephone_2'] : '',
					'shipping_fax'				=> isset( $_REQUEST['shipping_fax'] ) ? $_REQUEST['shipping_fax'] : '',
					'shipping_email'			=> isset( $_REQUEST['shipping_email'] ) ? $_REQUEST['shipping_email'] : '',
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
		if ( $selected_shipping_address == 'Y' && ! is_user_logged_in() ) $selected_shipping_address = 'new';?>
		<div class="shipping_layer_info checkout_info clearfix" id="shipping_layer_info">
		<?php global $current_user;
		get_currentuserinfo();
		$addresses = Addresses::getCustomerAddresses( $current_user->ID );
		$default_address = false;
		if ( count( $addresses ) > 0 ) {
			if ( isset( $_REQUEST['selected_shipping_id'] ) ) {
				$default_address_id = $_REQUEST['selected_shipping_id'];
			} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['selected_shipping_id'] ) ) {
				$default_address_id = $_SESSION['tcp_checkout']['shipping']['selected_shipping_id'];
			} else {
				$default_address = Addresses::getCustomerDefaultShippingAddress( $current_user->ID );
				$default_address_id = $default_address ? $default_address->address_id : 0;
			}?>
			<div id="selected_shipping_area" class="checkout_info clearfix" <?php if ( $selected_shipping_address != 'Y' ) : ?>style="display:none"<?php endif;?>>
				<label for="selected_shipping_id"> <?php _e( 'Select shipping address:', 'tcp' );?></label>
				<br />
				<select id="selected_shipping_id" name="selected_shipping_id">
				<?php foreach( $addresses as $address ) :?>
					<option value="<?php echo $address->address_id;?>" <?php selected( $address->address_id, $default_address_id );?>><?php echo stripslashes( $address->street . ', ' . $address->city );?></option>
				<?php endforeach;?>
				</select>
				<?php if ( $selected_shipping_address == 'Y' ) $this->showErrorMsg( 'shipping_country_id' );?>
			</div> <!-- selected_shipping_area -->
			
			<input type="radio" id="selected_shipping_address" name="selected_shipping_address" value="Y"<?php if ( $selected_shipping_address == 'Y' && count( $addresses ) > 0 ) : ?> checked="true"<?php endif;?> onChange="jQuery('#selected_shipping_area').show();jQuery('#new_shipping_area').hide();" />
			<label for="selected_shipping_address"><?php _e( 'shipping to the address selected', 'tcp' )?></label>
			<br /><?php
		} ?>
			<span id="p_use_billing_address">
			<input type="radio" id="use_billing_address" name="selected_shipping_address" value="BIL" <?php if ( $selected_shipping_address == 'BIL' ) : ?> checked="true"<?php endif;?> onChange="jQuery('#selected_shipping_area').hide();jQuery('#new_shipping_area').hide();" />
			<label for="use_billing_address"><?php _e( 'Use billing address', 'tcp' );?></label>
			<?php if ( $selected_shipping_address == 'BIL' ) $this->showErrorMsg( 'shipping_country_id' );?>
			<br/>
			</span>

			<input type="radio" id="new_shipping_address" name="selected_shipping_address" value="new" <?php if ( $selected_shipping_address == 'new' || count( $addresses ) == 0 ) : ?> checked="true"<?php endif;?> onChange="jQuery('#new_shipping_area').show();jQuery('#selected_shipping_area').hide();" />
			<label for="new_shipping_address"><?php _e( 'New shipping address', 'tcp' );?></label>
			<div id="new_shipping_area" <?php if ( $selected_shipping_address != 'new' ) : ?>style="display:none"<?php endif;?>><?php
			if ( isset( $_REQUEST['shipping_firstname'] ) ) {
				$firstname = $_REQUEST['shipping_firstname'];
			} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['shipping_firstname'] ) ) {
				$firstname = $_SESSION['tcp_checkout']['shipping']['shipping_firstname'];
			} elseif ( $default_address ) {
				$firstname = stripslashes( $default_address->firstname );
			} elseif ( $current_user && $current_user instanceof WP_User ) {
				$firstname = $current_user->first_name;
			} else {
				$firstname = '';
			}
			if ( isset( $_REQUEST['shipping_lastname'] ) ) {
				$lastname = $_REQUEST['shipping_lastname'];
			} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['shipping_lastname'] ) ) {
				$lastname = $_SESSION['tcp_checkout']['shipping']['shipping_lastname'];
			} elseif ( $default_address ) {
				$lastname = stripslashes( $default_address->lastname );
			} elseif ( $current_user && $current_user instanceof WP_User ) {
				$lastname = $current_user->last_name;
			} else {
				$lastname = '';
			}
			if ( isset( $_REQUEST['shipping_company'] ) ) {
				$company = $_REQUEST['shipping_company'];
			} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['shipping_lastname'] ) ) {
				$company = $_SESSION['tcp_checkout']['shipping']['shipping_company'];
			} else {
				$company = $default_address ? stripslashes( $default_address->company ) : '';
			}
			if ( isset( $_REQUEST['shipping_tax_id_number'] ) ) {
				$tax_id_number = $_REQUEST['shipping_tax_id_number'];
			} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['shipping_lastname'] ) ) {
				$tax_id_number = $_SESSION['tcp_checkout']['shipping']['shipping_tax_id_number'];
			} else {
				$tax_id_number = $default_address ? $default_address->tax_id_number : '';
			}
			if ( isset( $_REQUEST['shipping_street'] ) ) {
				$street = $_REQUEST['shipping_street'];
			} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['shipping_street'] ) ) {
				$street = $_SESSION['tcp_checkout']['shipping']['shipping_street'];
			} else {
				$street = $default_address ? stripslashes( $default_address->street ) : '';
			}
			if ( isset( $_REQUEST['shipping_city_id'] ) ) {
				$city_id = $_REQUEST['shipping_city_id'];
			} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['shipping_city_id'] ) ) {
				$city_id = $_SESSION['tcp_checkout']['shipping']['shipping_city_id'];
			} else {
				$city_id = $default_address ? $default_address->city_id : '';
			}
			if ( isset( $_REQUEST['shipping_city'] ) ) {
				$city = $_REQUEST['shipping_city'];
			} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['shipping_city'] ) ) {
				$city = $_SESSION['tcp_checkout']['shipping']['shipping_city'];
			} else {
				$city = $default_address ? stripslashes( $default_address->city ) : '';
			}
			if ( isset( $_REQUEST['shipping_region_id'] ) ) {
				$region_id = $_REQUEST['shipping_region_id'];
			} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['shipping_region_id'] ) ) {
				$region_id = $_SESSION['tcp_checkout']['shipping']['shipping_region_id'];
			} else {
				$region_id = $default_address ? $default_address->region_id : '';
			}
			if ( isset( $_REQUEST['shipping_region'] ) ) {
				$region = $_REQUEST['shipping_region'];
			} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['shipping_region'] ) ) {
				$region = $_SESSION['tcp_checkout']['shipping']['shipping_region'];
			} else {
				$region = $default_address ? stripslashes( $default_address->region ) : '';
			}
			if ( isset( $_REQUEST['shipping_postcode'] ) ) {
				$postcode = $_REQUEST['shipping_postcode'];
			} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['shipping_postcode'] ) ) {
				$postcode = $_SESSION['tcp_checkout']['shipping']['shipping_postcode'];
			} else {
				$postcode = $default_address ? str_replace( ' ' , '', $default_address->postcode ) : '';
			}
			if ( isset( $_REQUEST['shipping_country_id'] ) ) {
				$country_id = $_REQUEST['shipping_country_id'];
			} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['shipping_country_id'] ) ) {
				$country_id = $_SESSION['tcp_checkout']['shipping']['shipping_country_id'];
			} else {
				$country_id = $default_address ? $default_address->country_id : '';
			}
			if ( isset( $_REQUEST['shipping_telephone_1'] ) ) {
				$telephone_1 = $_REQUEST['shipping_telephone_1'];
			} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['shipping_telephone_1'] ) ) {
				$telephone_1 = $_SESSION['tcp_checkout']['shipping']['shipping_telephone_1'];
			} else {
				$telephone_1 = $default_address ? $default_address->telephone_1 : '';
			}
			if ( isset( $_REQUEST['shipping_telephone_2'] ) ) {
				$telephone_2 = $_REQUEST['shipping_telephone_2'];
			} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['shipping_telephone_2'] ) ) {
				$telephone_2 = $_SESSION['tcp_checkout']['shipping']['shipping_telephone_2'];
			} else {
				$telephone_2 = $default_address ? $default_address->telephone_2 : '';
			}
			if ( isset( $_REQUEST['shipping_fax'] ) ) {
				$fax = $_REQUEST['shipping_fax'];
			} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['shipping_fax'] ) ) {
				$fax = $_SESSION['tcp_checkout']['shipping']['shipping_fax'];
			} else {
				$fax = $default_address ? $default_address->fax : '';
			}
			if ( isset( $_REQUEST['shipping_email'] ) ) {
				$email = $_REQUEST['shipping_email'];
			} elseif ( isset( $_SESSION['tcp_checkout']['shipping']['shipping_email'] ) ) {
				$email = $_SESSION['tcp_checkout']['shipping']['shipping_email'];
			} elseif ( $default_address ) {
				$email = $default_address->email;
			} elseif ( $current_user ) { //&& $current_user instanceof WP_User ) {
				$email = '';//$current_user->email;
			} else {
				$email = '';
			}?>
				<ul>
					<li><label for="shipping_firstname"><?php _e( 'Firstname', 'tcp' );?>:<em>*</em></label>
					<input type="text" id="shipping_firstname" name="shipping_firstname" value="<?php echo $firstname;?>" size="20" maxlength="50" />
					<?php $this->showErrorMsg( 'shipping_firstname' );?></li>

					<li><label for="shipping_lastname"><?php _e( 'Lastname', 'tcp' );?>:<em>*</em></label>
					<input type="text" id="shipping_lastname" name="shipping_lastname" value="<?php echo $lastname;?>" size="40" maxlength="100" />
					<?php $this->showErrorMsg( 'shipping_lastname' );?></li>

					<li><label for="shipping_company"><?php _e( 'Company', 'tcp' );?>:</label>
					<input type="text" id="shipping_company" name="shipping_company" value="<?php echo $company;?>" size="20" maxlength="50" />
					<?php $this->showErrorMsg( 'shipping_company' );?></li>
					<li><label for="shipping_tax_id_number"><?php _e( 'Tax ID number', 'tcp' );?>:</label>
					<input type="text" id="shipping_tax_id_number" name="shipping_tax_id_number" value="<?php echo $tax_id_number;?>" size="20" maxlength="30" />
					<?php $this->showErrorMsg( 'shipping_tax_id_number' );?></li>
					<li><label for="shipping_country_id"><?php _e( 'Country', 'tcp' );?>:<em>*</em></label>
					<?php global $thecartpress;
					$country = isset( $thecartpress->settings['country'] ) ? $thecartpress->settings['country'] : '';
					$shipping_isos = isset( $thecartpress->settings['shipping_isos'] ) ? $thecartpress->settings['shipping_isos'] : false;
					if ( $shipping_isos ) {
						$countries = Countries::getSome( $shipping_isos, tcp_get_current_language_iso() );
					} else {
						$countries = Countries::getAll( tcp_get_current_language_iso() );
					}
					$country_bill = $country_id;
					if ( $country_bill == '' ) $country_bill = $country;
					?><select id="shipping_country_id" name="shipping_country_id"><?php //p_use_shipping_address
					foreach( $countries as $item ) :?>
						<option value="<?php echo $item->iso;?>" <?php selected( $item->iso, $country_bill )?>><?php echo $item->name;?></option>
					<?php endforeach;?>
					</select>
					</li>

					<li><label for="shipping_region_id"><?php _e( 'Region', 'tcp' );?>:<em>*</em></label>
					<?php $regions = apply_filters( 'tcp_load_regions_for_shipping', false ); //array( 'id' => array( 'name'), 'id' => array( 'name'), ... )?>
					<select id="shipping_region_id" name="shipping_region_id" <?php if ( is_array( $regions ) && count( $regions ) > 0 ) {} else { echo 'style="display:none;"'; }?>>
						<option value=""><?php _e( 'No state selected', 'tcp' );?></option>
					<?php if ( is_array( $regions ) && count( $regions ) > 0 ) foreach( $regions as $id => $region ) : ?>
						<option value="<?php echo $id;?>" <?php selected( $id, $region_id );?>><?php echo $region['name'];?></option>
					<?php endforeach;?>
					</select>
					<input type="hidden" id="shipping_region_selected_id" value="<?php echo $region_id;?>"/>
					<?php $this->showErrorMsg( 'shipping_region_id' );?>
					<input type="text" id="shipping_region" name="shipping_region" value="<?php echo $region;?>" size="20" maxlength="50" <?php if ( is_array( $regions ) && count( $regions ) > 0 ) echo 'style="display:none;"';?>/>
					<?php $this->showErrorMsg( 'shipping_region' );?>
					</li>

					<li id="li_shipping_city_id"><label for="shipping_city_id"><?php _e( 'City', 'tcp' );?>:<em>*</em></label>
					<?php $cities = array(); //array( 'id' => array( 'name'), 'id' => array( 'name'), ... )
					$cities = apply_filters( 'tcp_load_cities_for_shipping', $cities );
					if ( is_array( $cities ) && count( $cities ) > 0 ) : ?>
						<select id="shipping_city_id" name="shipping_city_id">
						<?php foreach( $cities as $id => $city ) : ?>
							<option value="<?php echo $id;?>" <?php selected( $id, $city_id );?>><?php echo $city['name'];?></option>
						<?php endforeach;?>
						</select>
						<?php $this->showErrorMsg( 'shipping_city_id' );?>
					<?php else : ?>
						<input type="text" id="shipping_city" name="shipping_city" value="<?php echo $city;?>" size="20" maxlength="50" />
						<?php $this->showErrorMsg( 'shipping_city' );?>
					<?php endif;?>
					</li>

					<li><label for="shipping_street"><?php _e( 'Address', 'tcp' );?>:<em>*</em></label>
					<input type="text" id="shipping_street" name="shipping_street" value="<?php echo $street;?>" size="20" maxlength="50" />
					<?php $this->showErrorMsg( 'shipping_street' );?></li>

					<li><label for="shipping_postcode"><?php _e( 'Postal code', 'tcp' );?>:<em>*</em></label>
					<input type="text" id="shipping_postcode" name="shipping_postcode" value="<?php echo $postcode;?>" size="7" maxlength="7" />
					<?php $this->showErrorMsg( 'shipping_postcode' );?></li>

					<li><label for="shipping_telephone_1"><?php _e( 'Telephone 1', 'tcp' );?>:</label>
					<input type="text" id="shipping_telephone_1" name="shipping_telephone_1" value="<?php echo $telephone_1;?>" size="15" maxlength="20" />
					<?php $this->showErrorMsg( 'shipping_telephone_1' );?></li>

					<li><label for="shipping_telephone_2"><?php _e( 'Telephone 2', 'tcp' );?>:</label>
					<input type="text" id="shipping_telephone_2" name="shipping_telephone_2" value="<?php echo $telephone_2;?>" size="15" maxlength="20" />
					<?php $this->showErrorMsg( 'shipping_telephone_2' );?></li>

					<li><label for="shipping_fax"><?php _e( 'Fax', 'tcp' );?>:</label>
					<input type="text" id="shipping_fax" name="shipping_fax" value="<?php echo $fax;?>" size="15" maxlength="20" />
					<?php $this->showErrorMsg( 'shipping_fax' );?></li>

					<li><label for="shipping_email"><?php _e( 'eMail', 'tcp' );?>:<em>*</em></label>
					<input type="email" id="shipping_email" name="shipping_email" value="<?php echo $email;?>" size="15" maxlength="255" />
					<?php $this->showErrorMsg( 'shipping_email' );?></li>
				</ul>
			</div> <!-- new_shipping_area -->
			<?php do_action( 'tcp_checkout_shipping' );?>
		</div><!-- shipping_layer_info --><?php
		return true;
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
