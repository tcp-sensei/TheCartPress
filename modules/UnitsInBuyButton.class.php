<?php
/**
 * Units in Buy button
 *
 * Allows to define units label for products
 *
 * @package TheCartPress
 * @subpackage Modules
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.	If not, see <http://www.gnu.org/licenses/>.
 */

class TCPUnitsInBuyButton {

	static function initModule() {
		if ( is_admin() ) add_action( 'admin_init'	, array( __CLASS__, 'admin_init' ) );
		add_filter( 'tcp_get_the_price_label'		, array( __CLASS__, 'tcp_the_price_label' ), 50, 3 );
	}

	static function admin_init() {
		//Product metabox
		add_action( 'tcp_product_metabox_custom_fields'			, array( __CLASS__, 'tcp_product_metabox_custom_fields' ) );
		add_action( 'tcp_product_metabox_save_custom_fields'	, array( __CLASS__, 'tcp_product_metabox_save_custom_fields' ) );
		add_action( 'tcp_product_metabox_delete_custom_fields'	, array( __CLASS__, 'tcp_product_metabox_delete_custom_fields' ) );
		//Localize settings
		add_action( 'tcp_localize_settings_page'				, array( __CLASS__, 'tcp_localize_settings_page' ) );
		add_filter( 'tcp_localize_settings_action'				, array( __CLASS__, 'tcp_localize_settings_action' ) );
		//CSV Loader
		add_filter( 'tcp_csvl_option_columns'					, array( __CLASS__, 'tcp_csvl_option_columns' ), 10, 2 );
		add_action( 'tcp_csv_loader_row'						, array( __CLASS__, 'tcp_csv_loader_row' ), 10, 2 );
	}

	static function tcp_the_price_label( $label, $post_id, $price ) {
		$current_unit = tcp_get_product_unit_by_product( $post_id );
		if ( $current_unit == 'by-default' ) {
			global $thecartpress;
			$current_unit = $thecartpress->get_setting( 'tcp_product_current_unit', '' );
		}
		$units = tcp_get_product_units_list();
		if ( isset( $units[$current_unit] ) ) $current_unit = $units[$current_unit];
		else $current_unit = '';
		if ( strlen( $current_unit ) > 0 ) return sprintf( __( '%s per %s', 'tcp' ), $label, $current_unit );
		else return $label;
	}

	static function tcp_product_metabox_custom_fields( $post_id ) { ?>
		<tr valign="top">
		<th scope="row"><label for="tcp_product_current_unit"><?php _e( 'Product unit', 'tcp' ); ?>:</label></th>
		<td>
		<?php $current_unit = tcp_get_product_unit_by_product( $post_id );
		if ( $current_unit == '' ) $current_unit = 'by-default';
		$units = tcp_get_product_units_list( true ); ?>
		<select id="tcp_product_current_unit" name="tcp_product_current_unit">
			<?php foreach( $units as $id => $unit ) { ?>
			<option value="<?php echo $id; ?>" <?php selected( $current_unit, $id ); ?>><?php echo $unit == '' ? __( 'Empty', 'tcp' ) : $unit; ?></option>
			<?php  } ?>
		</select>
		<?php //$current_unit = $thecartpress->get_setting( 'tcp_product_current_unit', '' ); ?>
		</td>
	</tr>
	<?php }

	static function tcp_product_metabox_save_custom_fields( $post_id ) {
		$tcp_product_current_unit = isset( $_POST['tcp_product_current_unit'] ) ? $_POST['tcp_product_current_unit'] : '';
		update_post_meta( $post_id, 'tcp_product_unit', $tcp_product_current_unit );
	}

	static function tcp_product_metabox_delete_custom_fields( $post_id ) {
		delete_post_meta( 'tcp_product_unit', $post_id );
	}

	static function tcp_localize_settings_page() {
		global $thecartpress;
		if ( ! isset( $thecartpress ) ) return; ?>

<h3><?php _e( 'Product Units', 'tcp'); ?></h3>

<div class="postbox">

<table class="form-table">
<tbody>
<tr valign="top">
	<th scope="row">
	<label for="tcp_product_units"><?php _e( 'Units', 'tcp' ); ?></label>
	</th>
	<td>
		<?php $current_unit = $thecartpress->get_setting( 'tcp_product_current_unit', '' );
		$units = tcp_get_product_units_list(); ?>
		<select id="tcp_product_current_unit" name="tcp_product_current_unit">
			<?php foreach( $units as $id => $unit ) { ?>
			<option value="<?php echo $id; ?>" <?php selected( $current_unit, $id ); ?>><?php echo $unit == '' ? __( 'Empty', 'tcp' ) : $unit; ?></option>
			<?php  } ?>
		</select>
	</td>
</tr>
</tbody>
</table>

</div><!-- .postbox -->
	<?php }

	static function tcp_localize_settings_action( $settings ) {
		$settings['tcp_product_current_unit'] = isset( $_POST['tcp_product_current_unit'] ) ? $_POST['tcp_product_current_unit'] : '';
		return $settings;
	}
	
	static function tcp_csvl_option_columns( $options, $col ) {
		$options[] = array( 'tcp_product_unit', strtoupper( $col ) == 'PRODUCT-UNIT', __( 'Product unit', 'tcp' ) );
		return $options;
	}

	function tcp_csv_loader_row( $post_id, $cols ) {
		foreach( $cols as $i => $col ) {
			$col_names = isset( $_REQUEST['col_' . $i] ) ? $_REQUEST['col_' . $i] : array();
			if ( is_array( $col_names ) && count( $col_names ) > 0 ) {
				foreach( $col_names as $col_name ) {
					if ( 'tcp_product_unit' == $col_name ) {
						update_post_meta( $post_id, 'tcp_product_unit', trim( $col ) );
					}
				}
			}
		}
	}
}

TCPUnitsInBuyButton::initModule();

function tcp_get_product_units_list( $by_default = false ) {
	$units = array(
		'empty' => '',
		'unit' => __( 'unit', 'tcp' ),
		'piece' => __( 'piece', 'tcp' ),
		'roll' => __( 'roll', 'tcp' ),
		'meter' => __( 'meter', 'tcp' ),
		'kg' => __( 'kg', 'tcp' ),
		'gr' => __( 'gr', 'tcp' ),
		'pack' => __( 'pack', 'tcp' ),
	);
	if ( $by_default ) $units['by-default'] = __( 'By default', 'tcp' );
	return apply_filters( 'tcp_product_units_list', $units );
}

function tcp_get_product_unit_by_product( $post_id ) {
	$unit = get_post_meta( $post_id, 'tcp_product_unit', true );
	return $unit;
}
?>