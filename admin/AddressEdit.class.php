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

require_once( TCP_DAOS_FOLDER . 'Addresses.class.php' );

class TCPAddressEdit {

	private $address = false;

	function show( $echo = true ) {
		if ( is_admin() ) add_action( 'admin_footer', 'tcp_states_footer_scripts' );
		else tcp_states_footer_scripts();
		$address_id = isset( $_REQUEST['address_id'] ) ? $_REQUEST['address_id'] : '0';
		global $current_user;
		get_currentuserinfo();
		$customer_id = $current_user->ID;
		if ( $address_id > 0 && $customer_id > 0 && ! Addresses::isOwner( $address_id, $current_user->ID ) )
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );

		require_once( TCP_DAOS_FOLDER . 'Countries.class.php' );

		//array( 'id' => array( 'name', ), 'id' => array( 'name', ), ... )
		$regions = array(); //apply_filters( 'tcp_address_editor_load_regions', false );
		$error_address = array();
		ob_start();
		if ( isset( $_REQUEST['tcp_save_address'] ) ) {
			if ( strlen( $_REQUEST['address_name'] ) == 0 )
				$error_address['address_name'][] = __( 'Address name field must be completed', 'tcp' );
			if ( strlen( $_REQUEST['firstname'] ) == 0 )
				$error_address['firstname'][] = __( 'Firstname field must be completed', 'tcp' );
			if ( strlen( $_REQUEST['lastname'] ) == 0 )
				$error_address['lastname'][] = __( 'Lastname field must be completed', 'tcp' );
			if ( strlen( $_REQUEST['street'] ) == 0 )
				$error_address['street'][] = __( 'Street field must be completed', 'tcp' );
			if ( strlen( $_REQUEST['city'] ) == 0 ) { // && strlen( $_REQUEST['city_id'] ) == 0 ) {
				$error_address['city'][] = __( 'City field must be completed', 'tcp' );
				$error_address['city_id'][] = __( 'City field must be completed', 'tcp' );
			}
			if ( strlen( $_REQUEST['region'] ) == 0 && strlen( $_REQUEST['region_id'] ) == 0  )
				$error_address['region'][] = __( 'Region field must be completed', 'tcp' );
			if ( ! isset( $_REQUEST['postcode'] ) || strlen( $_REQUEST['postcode'] ) == 0 )
				$error_address['postcode'][] = __( 'Postcode field must be completed', 'tcp' );
			if ( ! isset( $_REQUEST['email'] ) || strlen( $_REQUEST['email'] ) == 0 )
				$error_address['email'][] = __( 'eMail field must be completed', 'tcp' );
			$has_validation_error = count( $error_address ) > 0;
			if ( ! $has_validation_error ) {
				$_REQUEST['customer_id'] = $customer_id;
				if ( ! isset( $_REQUEST['city'] ) ) $_REQUEST['city'] = '';
				if ( ! isset( $_REQUEST['city_id'] ) ) $_REQUEST['city_id'] = '';
				if ( ! isset( $_REQUEST['region'] ) ) $_REQUEST['region'] = '';
				if ( ! isset( $_REQUEST['region_id'] ) ) $_REQUEST['region_id'] = '';
				if ( ! isset( $_REQUEST['default_billing'] ) ) $_REQUEST['default_billing'] = '';
				if ( ! isset( $_REQUEST['default_shipping'] ) ) $_REQUEST['default_shipping'] = '';
				Addresses::save( $_REQUEST ); ?>
				<div id="message" class="updated"><p>
					<?php _e( 'Address saved', 'tcp' ); ?>
				</p></div><?php
			} else { ?>
				<div id="message" class="error"><p>
					<?php _e( 'Validation errors. Address has not been saved', 'tcp' ); ?>
				</p></div><?php
			}
		} elseif ( $address_id > 0 ) {
			$this->address = Addresses::get( $address_id );
		}
		if ( is_admin() ) $admin_path = TCP_ADMIN_PATH . 'AddressesList.php';
		else $admin_path = get_permalink( get_option( 'tcp_addresses_list_page_id' ) ); ?>
		<div class="wrap tcp_frontend_address">
		<?php if ( is_admin() ) : ?><h2><?php _e( 'Address', 'tcp' ); ?></h2><?php endif; ?>
		<ul class="subsubsub">
			<li><a href="<?php echo $admin_path; ?>"><?php _e( 'Return to the list', 'tcp' ); ?></a></li>
		</ul>
		<div class="clear"></div>

		<form method="post">
			<input type="hidden" name="address_id" value="<?php echo $address_id; ?>" />
			<table class="form-table">
			<tr valign="top">
			<th scope="row"><label for="address_name"><?php _e( 'Address name', 'tcp' ); ?>:<span class="compulsory">(*)</span></label></th>
			<td>
				<input type="text" id="address_name" name="address_name" value="<?php $this->tcp_get_value( 'address_name' ); ?>" size="40" maxlength="50" />
				<?php $this->tcp_show_error_msg( $error_address, 'address_name' ); ?></td>
			</tr>
			<tr valign="top">
			<th scope="row"><label for="firstname"><?php _e( 'Firstname', 'tcp' ); ?>:<span class="compulsory">(*)</span></label></th>
			<td>
				<input type="text" id="firstname" name="firstname" value="<?php $this->tcp_get_value( 'firstname' ); ?>" size="40" maxlength="50" />
				<?php $this->tcp_show_error_msg( $error_address, 'firstname' ); ?></td>
			</tr>
			<tr valign="top">
			<th scope="row"><label for="lastname"><?php _e( 'Lastname', 'tcp' ); ?>:<span class="compulsory">(*)</span></label></th>
			<td>
				<input type="text" id="lastname" name="lastname" value="<?php $this->tcp_get_value( 'lastname' ); ?>" size="40" maxlength="100" />
				<?php $this->tcp_show_error_msg( $error_address, 'lastname' ); ?></td>
			</tr>
	
			<tr valign="top">
			<th scope="row"><label for="custom_id"><?php _e( 'Custom id', 'tcp' ); ?>:</label></th>
			<td>
				<input type="text" id="custom_id" name="custom_id" value="<?php $this->tcp_get_value( 'custom_id' ); ?>" size="20" maxlength="20" />
				<span class="description"><?php _e( 'This id is useful to connect the customers with third software', 'tcp' ); ?></span></td>
			</tr>
			<tr valign="top">
			<th scope="row"><label for="company"><?php _e( 'Company', 'tcp' ); ?>:</label></th>
			<td>
				<input type="text" id="company" name="company" value="<?php $this->tcp_get_value( 'company' ); ?>" size="20" maxlength="50" />
				<?php $this->tcp_show_error_msg( $error_address, 'company' ); ?></td>
			</tr>
			<tr valign="top">
			<th scope="row"><label for="tax_id_number"><?php _e( 'Tax id number', 'tcp' ); ?>:</label></th>
			<td>
				<input type="text" id="tax_id_number" name="tax_id_number" value="<?php $this->tcp_get_value( 'tax_id_number' ); ?>" size="20" maxlength="30" />
				<?php $this->tcp_show_error_msg( $error_address, 'tax_id_number' ); ?></td>
			</tr>
			<tr valign="top">
			<th scope="row"><label for="company_id"><?php _e( 'Company id', 'tcp' ); ?>:</label></th>
			<td>
				<input type="text" id="company_id" name="company_id" value="<?php $this->tcp_get_value( 'company_id' ); ?>" size="20" maxlength="30" />
				<span class="description"><?php _e( 'This id is useful to connect the companies with third software', 'tcp' ); ?></span></td>
			</tr>
			<tr valign="top">
			<th scope="row"><label for="country_id"><?php _e( 'Country', 'tcp' ); ?>:</label></th>
			<td>
				<select id="country_id" name="country_id">
				<?php $billing_isos = isset( $thecartpress->settings['billing_isos'] ) ? $thecartpress->settings['billing_isos'] : array();
				$shipping_isos = isset( $thecartpress->settings['shipping_isos'] ) ? $thecartpress->settings['shipping_isos'] : array();
				$billing_isos = array_merge( $billing_isos, $shipping_isos );
				if ( count( $billing_isos ) > 0 )
					$countries = Countries::getSome( $billing_isos, tcp_get_admin_language_iso() );
				else
					$countries = Countries::getAll( tcp_get_admin_language_iso() );
				$country_id = $this->tcp_get_value( 'country_id', false );
				if ( $country_id == '' ) {
					global $thecartpress;
					$country_id = $thecartpress->settings['country'];
				}
				foreach( $countries as $item ) :?>
					<option value="<?php echo $item->iso; ?>" <?php selected( $item->iso, $country_id )?>><?php echo $item->name; ?></option>
				<?php endforeach; ?>
				</select>
				<?php $this->tcp_show_error_msg( $error_address, 'country_id' ); ?></td>
			</tr>
			<tr valign="top">
			<tr valign="top">
			<th scope="row"><label for="region"><?php _e( 'Region', 'tcp' ); ?>:<span class="compulsory">(*)</span></label></th>
			<td>
			<select id="region_id" name="region_id" <?php if ( is_array( $regions ) && count( $regions ) > 0 ) {} else { echo 'style="display:none;"'; }?>>
				<option value=""><?php _e( 'No state selected', 'tcp' ); ?></option>
			<?php foreach( $regions as $id => $region ) : ?>
				<option value="<?php echo $id; ?>" <?php selected( $id, $this->tcp_get_value( 'region_id', false ) ); ?>><?php echo $region['name']; ?></option>
			<?php endforeach; ?>
			</select>
			<input type="hidden" id="region_selected_id" value="<?php $this->tcp_get_value( 'region_id' ); ?>"/>
			<?php //$this->tcp_show_error_msg( $error_address, 'region_id' ); ?>
			<input type="text" id="region" name="region" value="<?php $this->tcp_get_value( 'region' ); ?>" size="20" maxlength="50" <?php if ( is_array( $regions ) && count( $regions ) > 0 ) echo 'style="display:none;"'; ?>/>
			<?php $this->tcp_show_error_msg( $error_address, 'region' ); ?>
			</td>
			</tr>
			<tr valign="top">
			<th scope="row"><label for="city"><?php _e( 'City', 'tcp' ); ?>:<span class="compulsory">(*)</span></label></th>
			<td>
			<?php $cities = array(); //array( 'id' => array( 'name'), 'id' => array( 'name'), ... )
			$cities = apply_filters( 'tcp_address_editor_load_cities', $cities );
			if ( is_array( $cities ) && count( $cities ) > 0 ) : ?>
				<select id="city_id" name="city_id">
				<?php foreach( $cities as $id => $city ) : ?>
					<option value="<?php echo $id; ?>" <?php selected( $id, $this->tcp_get_value( 'city_id', false ) ); ?>><?php echo $city['name']; ?></option>
				<?php endforeach; ?>
				</select>
				<?php $this->tcp_show_error_msg( $error_address, 'city_id' ); ?>
			<?php else : ?>
				<input type="text" id="city" name="city" value="<?php $this->tcp_get_value( 'city' ); ?>" size="20" maxlength="50" />
				<?php $this->tcp_show_error_msg( $error_address, 'city' ); ?>
			<?php endif; ?>
			</td>
			</tr>
			<tr valign="top">
			<th scope="row"><label for="postcode"><?php _e( 'Postal code', 'tcp' ); ?>:<span class="compulsory">(*)</span></label></th>
			<td>
				<input type="text" id="postcode" name="postcode" value="<?php $this->tcp_get_value( 'postcode' ); ?>" size="7" maxlength="7" />
				<?php $this->tcp_show_error_msg( $error_address, 'postcode' ); ?></td>
			</tr>
			<tr valign="top">
			<th scope="row"><label for="street"><?php _e( 'Address', 'tcp' ); ?>:<span class="compulsory">(*)</span></label></th>
			<td>
				<input type="text" id="street" name="street" value="<?php $this->tcp_get_value( 'street' ); ?>" size="20" maxlength="50" />
				<?php $this->tcp_show_error_msg( $error_address, 'street' ); ?></td>
			</tr>
			<tr>
			<th scope="row"><label for="telephone_1"><?php _e( 'Telephone 1', 'tcp' ); ?>:</label></th>
			<td>
				<input type="text" id="telephone_1" name="telephone_1" value="<?php $this->tcp_get_value( 'telephone_1' ); ?>" size="15" maxlength="20" />
				<?php $this->tcp_show_error_msg( $error_address, 'telephone_1' ); ?></td>
			</tr>
			<tr valign="top">
			<th scope="row"><label for="telephone_2"><?php _e( 'Telephone 2', 'tcp' ); ?>:</label></th>
			<td>
				<input type="text" id="telephone_2" name="telephone_2" value="<?php $this->tcp_get_value( 'telephone_2' ); ?>" size="15" maxlength="20" />
				<?php $this->tcp_show_error_msg( $error_address, 'telephone_2' ); ?></td>
			</tr>
			<tr valign="top">
			<th scope="row"><label for="fax"><?php _e( 'Fax', 'tcp' ); ?>:</label></th>
			<td>
				<input type="text" id="fax" name="fax" value="<?php $this->tcp_get_value( 'fax' ); ?>" size="15" maxlength="20" />
				<?php $this->tcp_show_error_msg( $error_address, 'fax' ); ?></td>
			</tr>
			<tr valign="top">
			<th scope="row"><label for="email"><?php _e( 'eMail', 'tcp' ); ?>:<span class="compulsory">(*)</span></label></th>
			<td>
				<input type="text" id="email" name="email" value="<?php $this->tcp_get_value( 'email' ); ?>" size="35" maxlength="50" />
				<?php $this->tcp_show_error_msg( $error_address, 'email' ); ?></td>
			</tr>
			<tr valign="top">
			<th scope="row"><label for="default_billing"><?php _e( 'Default billing', 'tcp' ); ?>:</label></th>
			<td>
				<input type="checkbox" id="default_billing" name="default_billing" value="Y" <?php checked( 'Y', $this->tcp_get_value( 'default_billing', false ) ); ?> />
			</tr>
			<tr valign="top">
			<th scope="row"><label for="default_shipping"><?php _e( 'Default shipping', 'tcp' ); ?>:</label></th>
			<td>
				<input type="checkbox" id="default_shipping" name="default_shipping" value="Y" <?php checked( 'Y', $this->tcp_get_value( 'default_shipping', false ) ); ?> />
			</tr>
			</table>

			<p class="submit">
				<input type="submit" id="tcp_save_address" name="tcp_save_address" class="button-primary" value="<?php _e('Save') ?>" />
			</p>
		</form>
		</div><?php
		$out = ob_get_clean();
		if ( $echo ) echo $out;
		else return $out;
	}

	function tcp_show_error_msg( $error_array, $id ) {
		if ( isset( $error_array[$id][0] ) ) echo '<span class="tcp_error">', $error_array[$id][0], '</span>';
	}

	function tcp_get_value( $id, $echo = true ) {
		if ( isset( $_REQUEST[$id] ) ) {
			$res = $_REQUEST[$id];
		} else {
			if ( $id == 'address_name' ) $id = 'name';
			if ( isset( $this->address->$id ) ) $res = $this->address->$id;
			else $res = '';
		}
		if ( ! is_numeric( $res ) ) $res = stripslashes( $res );
		if ( $echo ) echo $res;
		else return $res;
	}
}
?>
