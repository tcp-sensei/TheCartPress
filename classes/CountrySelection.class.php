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

class TCPCountrySelection {

	static function init() {
		add_action( 'wp_head', array( __CLASS__, 'wp_head' ), 5 );
	}
	
	static function show() { ?>
		<form method="post" id="tcp_country_selection">
		<div class="tcp_select_country">
			<label for="selected_country_id"><?php _e( 'Country', 'tcp' ); ?></label>
			<?php global $thecartpress;
			$country = isset( $_REQUEST['tcp_selected_country_id'] ) ? $_REQUEST['tcp_selected_country_id'] : false;
			if ( ! $country ) $country = tcp_get_billing_country();
			//if ( ! $country ) $country = tcp_get_tax_country();
			$billing_isos = $thecartpress->get_setting( 'billing_isos', false );
			if ( $billing_isos ) $countries = TCPCountries::getSome( $billing_isos,  $country );
			else $countries = TCPCountries::getAll( $country ); ?>
			<select id="selected_country_id" name="tcp_selected_country_id">
			<?php foreach( $countries as $item ) : ?>
				<option value="<?php echo $item->iso; ?>" <?php selected( $item->iso, $country ); ?>><?php echo $item->name; ?></option>
			<?php endforeach; ?>
			</select>
			<script>
				jQuery( '#selected_country_id' ).change( function() {
					jQuery( '#tcp_country_selection' ).submit();
				} );
			</script>
		</div>
		<!--<div class="tcp_select_region">
			<label for="selected_region_id"><?php //_e( 'Region', 'tcp' ); ?></label>
			<?php //$regions = apply_filters( 'tcp_load_regions_for_billing', false ); //array( 'id' => array( 'name'), 'id' => array( 'name'), ... )
			//$region_id = isset( $_REQUEST['tcp_selected_region_id'] ) ? $_REQUEST['tcp_selected_region_id'] : false;
			//if ( ! $region_id ) $region_id = tcp_get_default_tax_region(); ?>
			<select id="selected_region_id" name="tcp_selected_region_id" <?php //if ( is_array( $regions ) && count( $regions ) > 0 ) {} else { echo 'style="display:none;"'; }?>>
				<option value=""><?php //_e( 'No state selected', 'tcp' );?></option>
			<?php //if ( is_array( $regions ) && count( $regions ) > 0 ) foreach( $regions as $id => $region ) : ?>
				<option value="<?php //echo $id;?>" <?php //selected( $id, $region_id ); ?>><?php //echo $region['name']; ?></option>
			<?php //endforeach; ?>
			</select>
			<input type="hidden" id="billing_region_selected_id" value="<?php //echo $region_id;?>"/>
		</div>-->
		<!--<div class="tcp_select_country_submit"><input type="submit" value="<?php //_e( 'Set country', 'tcp' ); ?>" /></div>-->
		</form><?php
	}

	static function wp_head() {
		if ( isset( $_REQUEST['tcp_selected_country_id'] ) ) {
			tcp_set_billing_country( $_REQUEST['tcp_selected_country_id'] );
			tcp_set_shipping_as_billing();
		}
		//if ( isset( $_REQUEST['tcp_selected_region_id'] ) ) tcp_set_billing_region( $_REQUEST['tcp_selected_region_id'] );
	}
}

TCPCountrySelection::init();
?>
