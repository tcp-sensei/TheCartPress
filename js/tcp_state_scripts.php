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
?>

var countries = {
	'CA': { //Canada
		'AB' : 'Alberta', 'BC' : 'British Columbia', 'MA' : 'Manitoba', 'NB' : 'New Brunswick', 'NL' : 'Newfoundland and Labrador', 'NT' : 'Northwest Territories', 'NS' : 'Nova Scotia', 'NU' : 'Nunavut', 'ON' : 'Ontario', 'PE' : 'Prince Edward Island', 'QU' : 'Québec', 'SA' : 'Saskatchewan', 'YU' : 'Yukon'
	},
	'US' : { //USA
		'AL' : 'Alabama', 'AK' : 'Alaska', 'AZ' : 'Arizona', 'AR' : 'Arkansas', 'CA' : 'California', 'CO' : 'Colorado', 'CT' : 'Connecticut', 'DE' : 'Delaware', 
		'DC' : 'District of Columbia', 'FL' : 'Florida', 'GA' : 'Georgia', 'HI' : 'Hawaii', 'ID' : 'Idaho', 'IL' : 'Illinois', 'IN' : 'Indiana',
		'IA' : 'Iowa', 'KS' : 'Kansas', 'KY' : 'Kentucky', 'LA' : 'Louisiana', 'ME' : 'Maine', 'MD' : 'Maryland', 'MA' : 'Massachusetts', 'MI' : 'Michigan',
		'MN' : 'Minnesota', 'MS' : 'Mississippi', 'MO' : 'Missouri', 'MT' : 'Montana', 'NE' : 'Nebraska', 'NV' : 'Nevada', 'NH' : 'New Hampshire',
		'NJ' : 'New Jersey', 'NM' : 'New Mexico', 'NY' : 'New York', 'NC' : 'North Carolina', 'ND' : 'North Dakota', 'OH' : 'Ohio', 'OK' : 'Oklahoma',
		'OR' : 'Oregon', 'PA' : 'Pennsylvania', 'RI' : 'Rhode Island', 'SC' : 'South Carolina', 'SD' : 'South Dakota', 'TN' : 'Tennessee', 'TX' : 'Texas',
		'UT' : 'Utah', 'VT' : 'Vermont', 'VA' : 'Virginia', 'WA' : 'Washington', 'WV' : 'West Virginia', 'WI' : 'Wisconsin', 'WY' : 'Wyoming'
	}
	<?php do_action( 'tcp_states_loading' );?>
};

//jQuery(document).ready(function() {
	if (jQuery('#country_id')) {
		jQuery('#country_id').change(function () {
			var country_id = jQuery('#country_id').val();

			var region_select = jQuery('#region_id'); //state
			if (region_select) {
				var first_option = jQuery('#region_id option:first');
				region_select.empty();
				region_select.append(first_option);
			}
			var states = countries[country_id];
			if (states) {
				if (region_select) {
					jQuery.each(states, function(key, title) {
						region_select.append(jQuery('<option></option>').attr('value', key).text(title));
					});
					region_select.show();
				}
				jQuery('#region').hide();//textbox
				region_select.val(jQuery('#region_selected_id').val());
			} else {
				jQuery('#region').show();//textbox
				region_select.hide();
			}
		});
		jQuery('#country_id').change();
		
		jQuery('#region_id').change(function() {
			jQuery('#region').val(jQuery("#region_id option:selected").text());
		});
	}
	
	if (jQuery('#billing_country_id')) {
		jQuery( '#new_billing_area' ).on( 'change', '#billing_country_id', function () {
			var country_id = jQuery('#billing_country_id').val();
			var region_select = jQuery('#billing_region_id'); //state
			if (region_select) {
				var first_option = jQuery('#billing_region_id option:first');
				region_select.empty();
				region_select.append(first_option);
			}
			var states = countries[country_id];
			if (states) {
				if (region_select) {
					jQuery.each(states, function(key, title) {
						region_select.append(jQuery('<option></option>').attr('value', key).text(title));
					});
					region_select.show();
				}
				jQuery('#billing_region').hide();//textbox
				region_select.val(jQuery('#billing_region_selected_id').val());
			} else {
				jQuery('#billing_region').show();//textbox
				region_select.hide();
			}
		} );
		jQuery('#billing_country_id').change();

		jQuery('#billing_region_id').change(function() {
			jQuery('#billing_region').val(jQuery("#billing_region_id option:selected").text());
		} );
	}

	if (jQuery('#shipping_country_id')) {
		jQuery('#shipping_country_id').change(function () {
			var country_id = jQuery('#shipping_country_id').val();

			var region_select = jQuery('#shipping_region_id'); //state
			if (region_select) {
				var first_option = jQuery('#shipping_region_id option:first');
				region_select.empty();
				region_select.append(first_option);
			}
			var states = countries[country_id];
			if (states) {
				if (region_select) {
					jQuery.each(states, function(key, title) {
						region_select.append(jQuery('<option></option>').attr('value', key).text(title));
					});
					region_select.show();
				}
				jQuery('#shipping_region').hide();//textbox
				region_select.val(jQuery('#shipping_region_selected_id').val());
			} else {
				jQuery('#shipping_region').show();//textbox
				region_select.hide();
			}
		});
		jQuery('#shipping_country_id').change();

		jQuery('#shipping_region_id').change(function() {
			jQuery('#shipping_region').val(jQuery("#shipping_region_id option:selected").text());
		});
	}
//});
