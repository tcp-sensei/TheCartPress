<?php
/**
 * Shipping cost
 *
 * Allows to calculates shipping costs using destination and weight
 *
 * @package TheCartPress
 * @subpackage Plugins
 */

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

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ShippingCost' ) ) :

require_once( dirname( dirname( __FILE__ ) ) . '/daos/Countries.class.php' );

class ShippingCost extends TCP_Plugin {

	function getTitle() {
		return 'ShippingCost';
	}

	function getDescription() {
		return __( 'Calculate the shipping cost using a table of weights ranges and zones.<br>Author: <a href="http://thecartpress.com" target="_blank">TheCartPress team</a>', 'tcp' );
	}

	function getIcon() {
		return plugins_url( 'images/shippingcost.png', __FILE__ );
	}

	function getCheckoutMethodLabel( $instance, $shippingCountry = '', $shoppingCart = false ) {
		$data = tcp_get_shipping_plugin_data( get_class( $this ), $instance );
		//$title = isset( $data['title'] ) ? $data['title'] : $this->getTitle();
		//$title = tcp_string( 'TheCartPress', 'shi_ShippingCost-title', $title );
		if ( isset( $data['title'] ) ) {
			//$title = tcp_string( 'TheCartPress', 'shi_FlatRateShipping-title', $title );
			$title = tcp_string( 'TheCartPress', apply_filters( 'tcp_plugin_data_get_option_translatable_key', 'shi_ShippingCost-title-' . $instance ), $data['title'] );
		} else {
			$title = $this->getTitle();
		}
		$cost = tcp_get_the_shipping_cost_to_show( $this->getCost( $instance, $shippingCountry, $shoppingCart ) );
		return sprintf( __( '%s. Cost: %s', 'tcp' ), $title, tcp_format_the_price( $cost ) );
	}

	function showEditFields( $data, $instance = 0 ) {
		add_action( 'admin_footer', 'tcp_states_footer_scripts' ); // To load states/regions

		$stored_data = isset( $data['costs'] );
		$ranges		 = isset( $data['ranges'] ) ? $data['ranges'] : array( 10, 20 );
		$zones		 = isset( $data['zones'] ) ? $data['zones'] : array(
			'0' => array( 'ES', 'FR', 'PT' ),
			'1' => array( 'CA', 'MX', 'US' ),
			'2' => array( 'CN', 'KR', 'JP' ),
		);
		$costs = isset( $data['costs'] ) ? $data['costs'] : array(
			10 => array(
				'0' => 1.5,
				'1' => 2.5,
				'2' => 3,
			),
			20 => array(
				'0' => 4.5,
				'1' => 5.5,
				'2' => 6
			),
		);
//echo '<br><br>zones: ';var_dump( $zones );
//echo '<br><br>costs: ';var_dump( $costs );
//echo '<br><br>ranges: ';var_dump( $ranges );
		if ( isset( $_REQUEST['tcp_copy_from_instance'] ) ) {
			$plugin_data	= get_option( 'tcp_plugins_data_shi_' . get_class( $this ) );
			$data			= reset( $plugin_data );
			$ranges			= $data['ranges'];
			$zones			= $data['zones'];
			$costs			= $data['costs']; ?>
			<div id="message" class="updated"><p>
				<?php _e( 'Remember to <strong>save</strong> before deleting other rows or columns', 'tcp' ); ?>
			</p></div>
			<?php $stored_data = false;
		} elseif ( isset( $_REQUEST['tcp_insert_range'] ) && isset( $_REQUEST['tcp_insert_range_value'] ) ) {
			$new_range = tcp_input_number( $_REQUEST['tcp_insert_range_value'] );
			foreach( $zones as $z => $zone ) {
				$new_cost[] = 0;
			}
			$new_ranges = array();
			$new_costs = array();
			$insert_new_range = false;
			foreach( $ranges as $r => $range ) {
				if ( $range < $new_range ) {
					$new_ranges[] = $range;
					$new_costs[] = $costs[$r];
				} elseif ( ! $insert_new_range ) {
					$new_ranges[] = $new_range;
					$new_ranges[] = $range;
					$new_costs[] = $new_cost;
					$new_costs[] = $costs[$r];
					$insert_new_range = true;
				} else {
					$new_ranges[] = $range;
					$new_costs[] = $costs[$r];
				}
			}
			if ( ! $insert_new_range ) {
				$new_ranges[] = $new_range;
				$new_costs[] = $new_cost;
			}
			$ranges = $new_ranges;
			$costs = $new_costs; ?>
			<div id="message" class="updated"><p>
				<?php _e( 'Remember to <strong>save</strong> before deleting other rows or columns', 'tcp' ); ?>
			</p></div>
			<?php $stored_data = false;
		} elseif ( isset( $_REQUEST['tcp_add_zone'] ) ) {
			$zones[] = array();
			foreach( $ranges as $range )
				$costs[$range][] = 0;
			$stored_data = false; ?>
			<div id="message" class="updated">
				<p><?php _e( 'Remember to <strong>save</strong> to add the new zone', 'tcp' ); ?></p>
			</div><?php
		} else {
			foreach( $_REQUEST as $index => $value ) {
				if ( $this->startsWith( $index, 'tcp_delete_range-' ) ) {
					$names = explode( '-', $index );
					$range_to_delete = $names[1];
					$new_ranges = array();
					$new_costs = array();
					foreach( $ranges as $r => $range ) {
						if ( $r != $range_to_delete ) $new_ranges[] = $range;
						if ( $r != $range_to_delete ) $new_costs[] = $costs[$r];
					}
					$ranges = $new_ranges;
					$costs = $new_costs; ?>
					<div id="message" class="updated">
						<p><?php _e( 'Remember to <strong>save</strong> before deleting other rows or columns', 'tcp' ); ?></p>
					</div>
					<?php $stored_data = false;
					break;
				} elseif ( $this->startsWith( $index, 'tcp_delete_zone-' ) ) {
					$names = explode( '-', $index );
					$zone = $names[1];
					unset( $zones[$zone] ); ?>
					<div id="message" class="updated">
						<p><?php _e( 'Remember to <strong>save</strong> before deleting other rows or columns', 'tcp' ); ?></p>
					</div>
					<?php $stored_data = false;
					break;
				} elseif ( $this->startsWith( $index, 'tcp_delete_def_zone-' ) ) {
					$names = explode( '-', $index );
					$zone_id = $names[1];
					unset( $zones[$zone_id] ); ?>
					<div id="message" class="updated">
						<p><?php _e( 'Remember to <strong>save</strong> before deleting other zones', 'tcp' ); ?></p>
					</div>
					<?php $stored_data = false;
				}
			}
		}
		if ( $stored_data ) : ?>
		<p>
			<input type="submit" name="tcp_copy_from_instance" value="<?php _e( 'copy from first instance', 'tcp' ); ?>" class="button-secondary"/>
			<input name="tcp_plugin_save" value="<?php _e( 'save', 'tcp' ); ?>" type="submit" class="button-primary" />
			<input name="tcp_plugin_delete" value="<?php _e( 'delete', 'tcp' ); ?>" type="submit" class="button-secondary" />
		</p>
		<?php endif; ?>
		<table class="widefat fixed">
		<thead>
		<tr>
			<th class="manage-column">
				<?php _e( 'Weight ranges', 'tcp' ); ?>
			</th>
			<?php foreach( $zones as $z => $isos ) : ?>
			<th scope="col" class="manage-column">
				<?php printf( __( 'Zone %d', 'tcp' ), $z ); ?>
				<?php if ( $stored_data ) : ?>
				<input type="submit" name="tcp_delete_zone-<?php echo $z; ?>" value="<?php _e( 'delete', 'tcp' ); ?>" class="button-secondary"/>
				<?php endif; ?>
				<input type="hidden" name="zones[]" value="<?php echo $z; ?>"/>
			</th>
			<?php endforeach; ?>
			<th>&nbsp;</th>
		</tr>
		</thead>
		<tfoot>
		<tr>
			<th class="manage-column"><?php _e( 'Weight ranges', 'tcp' ); ?></th>
			<?php foreach( $zones as $z => $isos ) : ?>
			<th scope="col" class="manage-column"><?php printf( __( 'Zone %d', 'tcp' ), $z ); ?></th>
			<?php endforeach; ?>
			<th>&nbsp;</th>
		</tr>
		</tfoot>
		<tbody>
		<?php foreach( $ranges as $r => $range ) : ?>
		<tr>
			<th scope="row">
				<?php printf( __( 'Range %d, less or equal than', 'tcp' ), $r ); ?>: 
				<input type="text" name="ranges[]" value="<?php echo tcp_number_format( $range ); ?>" size="5" maxlength="10"/>&nbsp;<?php tcp_the_unit_weight(); ?>
			</th>
			<?php foreach( $zones as $z => $zone ) : ?>
			<td>
				<input type="text" name="cost-<?php echo $r; ?>[]" value="<?php echo isset( $costs[$r][$z] ) ? tcp_format_number( $costs[$r][$z] ) : ''; ?>" size="6" maxlength="13"/>&nbsp;<?php tcp_the_currency(); ?>
			</td>
			<?php endforeach; ?>
			<td>
			<?php if ( $stored_data ) : ?>
				<input type="submit" name="tcp_delete_range-<?php echo $r; ?>" value="<?php _e( 'delete range', 'tcp' ); ?>" class="button-secondary" />
			<?php endif; ?>&nbsp;
			</td>
		</tr>
		<?php endforeach; ?>
		<?php if ( $stored_data ) : ?>
		<tr>
			<td colspan="<?php echo count( $zones ) + 2; ?>">
				<input type="submit" name="tcp_insert_range" value="<?php _e( 'insert new range', 'tcp' ); ?>" class="button-secondary" />
				<input type="text" name="tcp_insert_range_value" size="5" maxlength="10" />
				<span><?php _e( 'Remember to save all values before inserting a new range', 'tcp' ); ?></span>
			</td>
		</tr>
		<?php endif; ?>
		</tbody>
		</table>

		<p class="submit">
			<input name="tcp_plugin_save" value="<?php _e( 'save', 'tcp' ); ?>" type="submit" class="button-primary" />
			<input name="tcp_plugin_delete" value="<?php _e( 'delete', 'tcp' ); ?>" type="submit" class="button-secondary" />
		</p>

		<table  class="widefat fixed">
		<thead>
			<tr>
			<?php foreach( $zones as $z => $isos ) : ?>
				<th class="manage-column" colspan="1"><?php printf( __( 'Zone %s', 'tcp' ), $z ); ?></th>
			<?php endforeach; ?>
			</tr>
		</thead>
		<tfoot>
			<tr>
			<?php foreach( $zones as $z => $isos ) : ?>
				<th class="manage-column" colspan="1"><?php printf( __( 'Zone %s', 'tcp' ), $z ); ?></th>
			<?php endforeach; ?>
			</tr>
		</tfoot>
		<tbody>
		<tr>
		<?php foreach( $zones as $z => $isos ) : ?>
			<td>
				<select id="zones_isos_<?php echo $z; ?>" name="zones_isos_<?php echo $z; ?>[]" class="tcp_zones" style="height:auto" size="8" multiple>
				<?php if ( count( $data['countries'] ) != 1 ) {
					global $thecartpress;
					$shipping_isos = isset( $thecartpress->settings['shipping_isos'] ) ? $thecartpress->settings['shipping_isos'] : false;
					if ( $shipping_isos ) {
						$countries = TCPCountries::getSome( $shipping_isos );
					} else {
						$countries = TCPCountries::getAll();
					}
					foreach( $countries as $country ) :?>
					<option value="<?php echo $country->iso; ?>" <?php tcp_selected_multiple( $isos, $country->iso ); ?>><?php echo $country->name; ?></option>
					<?php endforeach;
				}?>
				</select>
			<br/>
			<?php //if ( count( $data['countries'] ) != 1 ) :?>
				<select class="tcp_select_countries" zone_isos="<?php echo $z; ?>">
					<option value="none"><?php _e( 'None', 'tcp'); ?></option>
					<option value="eu" title="<?php _e( 'To select countries from the European Union', 'tcp' ); ?>"><?php _e( 'EU', 'tcp'); ?></option>
					<option value="nafta"><?php _e( 'NAFTA', 'tcp'); ?></option>
					<option value="caricom"><?php _e( 'CARICOM', 'tcp'); ?></option>
					<option value="mercasur"><?php _e( 'MERCASUR', 'tcp'); ?></option>
					<option value="can"><?php _e( 'CAN', 'tcp'); ?></option>
					<option value="au"><?php _e( 'AU', 'tcp'); ?></option>
					<option value="apec"><?php _e( 'APEC', 'tcp'); ?></option>
					<option value="asean"><?php _e( 'ASEAN', 'tcp'); ?></option>
					<option value="toggle"><?php _e( 'Toggle', 'tcp'); ?></option>
					<option value="all"><?php _e( 'All', 'tcp'); ?></option>
				</select>
			<?php //endif; ?>
				<?php if ( $stored_data && count( $zones ) > 1) : ?>
				<input type="submit" name="tcp_delete_def_zone-<?php echo $z; ?>" id="tcp_delete_def_zone" value="<?php _e( 'delete zone', 'tcp'); ?>" title="<?php _e( 'To delete a defined zone', 'tcp' ); ?>" class="button-<?php if ( count( $data['countries'] ) == 1 ):?>primary<?php else:; ?>secondary<?php endif; ?>"/>
				<?php endif; ?>
			</td>
		<?php endforeach; ?>
		</tr>
		<?php if ( $stored_data ) : ?>
		<tr>
			<td colspan="<?php echo count( $zones ); ?>">
				<input type="submit" id="tcp_add_zone" name="tcp_add_zone" value="<?php _e( 'Add new zone', 'tcp' ); ?>" class="button-secondary" />
				<span><?php _e( 'Remember to save all values before inserting a new zone', 'tcp' ); ?></span>
			</td>
		</tr>
		<?php endif; ?>
		</tbody>
		</table>
<script>
<?php if ( isset( $data['countries'] ) && count( $data['countries'] ) == 1 ) : $zones_states = $data['zones']; ?>
jQuery(document).ready(function() {
	<?php echo 'var sel_states = new Array();', "\n";
	foreach( $zones_states as $i => $states ) {
		echo 'sel_states[', $i, '] = new Array();' , "\n";
		foreach( $states as $j => $state ) {
			echo 'sel_states[', $i, '][', $j, '] = \'', $state, '\';', "\n";
		}
	}?>
	var selects = jQuery( '.tcp_zones' );
	if (selects) {
		//var i = 0;
		jQuery.each(selects, function( i, region_select ) {
			region_select = jQuery('#' + region_select.id);
			var states = countries['<?php echo $data['countries'][0]; ?>'];
			if ( states ) {
				if ( region_select) {
					jQuery.each(states, function(key, title) {
						region_select.append(jQuery('<option></option>').attr('value', key).text(title));
					} );
					if (sel_states) {
						region_select.val(sel_states[i++]);
					}
				}
			}
		} );
	}
} );
<?php endif; ?>

jQuery( '.tcp_select_countries' ).on( 'change', function() {
	var org = jQuery( this ).val();
	var zones_isos = 'zones_isos_' + jQuery( this ).attr( 'zone_isos' );
	if ( org == 'eu' ) {
		tcp_select_eu( zones_isos );
	} else if ( org == 'nafta' ) {
		tcp_select_nafta( zones_isos );
	} else if ( org == 'caricom' ) {
		tcp_select_caricom( zones_isos );
	} else if ( org == 'mercasur' ) {
		tcp_select_mercasur( zones_isos );
	} else if ( org == 'can' ) {
		tcp_select_can( zones_isos );
	} else if ( org == 'au' ) {
		tcp_select_au( zones_isos );
	} else if ( org == 'apec' ) {
		tcp_select_apec( zones_isos );
	} else if ( org == 'asean' ) {
		tcp_select_asean( zones_isos );
	} else if ( org == 'toggle' ) {
		tcp_select_toggle( zones_isos );
	} else if ( org == 'none' ) {
		tcp_select_none( zones_isos );
	} else if ( org == 'all' ) {
		tcp_select_all( zones_isos );
	}
} );

jQuery(document).ready( function() {
	<?php foreach( $zones as $z => $isos ) : ?>
	//jQuery( '#zones_isos_<?php echo $z; ?>' ).tcp_convert_multiselect();
	<?php endforeach; ?>
} );
</script>
	<?php
	}

	function saveEditFields( $data, $instance = 0 ) {
		$zones = isset( $_REQUEST['zones'] ) ? $_REQUEST['zones'] : array();
		$ranges = isset( $_REQUEST['ranges'] ) ? $_REQUEST['ranges'] : array();
		$ranges = array();
		if ( isset( $_REQUEST['ranges'] ) ) foreach( $_REQUEST['ranges'] as $r => $range ) {
			$ranges[$r] = tcp_input_number( $range );
		}

		$costs = array();
		foreach( $zones as $z => $zone ) {
			foreach( $ranges as $r => $range ) {
				$costs[$r][] = isset( $_REQUEST['cost-' . $r][$z] ) ? tcp_input_number( $_REQUEST['cost-' . $r][$z] ) : 0;
			}
		}
		$new_zones = array();
		$z = 0;
		foreach( $zones as $zone ) {
			if ( isset( $_REQUEST['zones_isos_' . $zone] ) ) {
				$new_zones[$z++] = $_REQUEST['zones_isos_' . $zone];
			} else {
				$new_zones[$z++] = array();
			}
		}
		$data['zones']	= $new_zones;
		$data['ranges']	= $ranges;
		$data['costs']	= $costs;
		return $data;
	}

	function getCost( $instance, $shippingCountry, $shoppingCart = false ) {
		if ( $shoppingCart === false ) {
			$shoppingCart = TheCartPress::getShoppingCart();
		}
		$total_weight = $shoppingCart->getWeightForShipping();
		$data	= tcp_get_shipping_plugin_data( get_class( $this ), $instance );
		$zones	= $data['zones'];
		$ranges	= $data['ranges'];
		$costs	= $data['costs'];
		if ( ! is_array( $ranges ) || count( $ranges ) == 0 ) {
			return false;
		}
		foreach( $ranges as $r => $range ) {
			if ( $range >= $total_weight ) {
				$selected_range = $r;
				break;
			}
		}
		if ( ! isset( $selected_range ) ) {
			end( $ranges );
			$selected_range = key( $ranges );
		}
		$selected_zone = 0;
		if ( count( $data['countries'] ) == 1 ) {
			$region_id = $this->get_shipping_region_id();
			foreach( $zones as $z => $zone ) {
				if ( in_array( $region_id, $zone ) ) {
					$selected_zone = $z;
					break;
				}
			}
		} else {
			foreach( $zones as $z => $zone ) {
				if ( in_array( $shippingCountry, $zone ) ) {
					$selected_zone = $z;
					break;
				}
			}
		}
		if ( ! isset( $selected_zone ) ) {
			end( $zones );
			$selected_zone = key( $zones );
		}
		return $costs[$selected_range][$selected_zone];//TODO to add a based cost
	}

	private function startsWith( $Haystack, $Needle ) {
		return strpos( $Haystack, $Needle ) === 0;
	}
	
	private function get_shipping_region_id() {
		$shipping_region = '';
		$selected_shipping_address = isset( $_SESSION['tcp_checkout']['shipping']['selected_shipping_address'] ) ? $_SESSION['tcp_checkout']['shipping']['selected_shipping_address'] : false;
		if ( $selected_shipping_address == 'new' ) {
			$shipping_region = $_SESSION['tcp_checkout']['shipping']['shipping_region_id'];
		} elseif ( $selected_shipping_address == 'BIL' ) {
			if ( isset( $_SESSION['tcp_checkout']['billing']['selected_billing_address'] )
					&& $_SESSION['tcp_checkout']['billing']['selected_billing_address'] == 'new' ) {
				$shipping_region = $_SESSION['tcp_checkout']['billing']['billing_region_id'];
			} else {
				$address_id = $_SESSION['tcp_checkout']['billing']['selected_billing_id'];
				$address = Addresses::get( $address_id );
				if ( $address ) {
					$shipping_region = $address->region_id;
				}
			}
		} elseif ( $selected_shipping_address == 'Y' ) {
			if ( isset( $_SESSION['tcp_checkout']['shipping']['selected_shipping_id'] ) ) {
				$address_id = $_SESSION['tcp_checkout']['shipping']['selected_shipping_id'];
				$address = Addresses::get( $address_id );
				if ( $address ) {
					$shipping_region = $address->region_id;
				}
			}
		}
		return $shipping_region;
	}
}
endif; // class_exists check