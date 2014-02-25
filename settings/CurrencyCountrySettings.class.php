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
if ( ! defined( 'ABSPATH' ) ) exit;

require_once( TCP_DAOS_FOLDER . 'Currencies.class.php' );

if ( ! class_exists( 'TCPCurrencyCountrySettings' ) ) :

class TCPCurrencyCountrySettings {

	private $updated = false;

	function __construct() {
		add_action( 'tcp_admin_menu', array( $this, 'tcp_admin_menu' ) );
		global $tcp_miranda;
		if ( $tcp_miranda ) $tcp_miranda->add_item( 'settings', 'default_settings', __( 'Localize', 'tcp' ), false, array( 'TCPCurrencyCountrySettings', __FILE__ ), plugins_url( 'thecartpress/images/miranda/localize_settings_48.png' ) );
	}

	function tcp_admin_menu() {
		if ( ! current_user_can( 'tcp_edit_settings' ) ) return;
		global $thecartpress;
		$base = $thecartpress->get_base_settings();
		$page = add_submenu_page( $base, __( 'Currency & Country Settings', 'tcp' ), __( 'Localize', 'tcp' ), 'tcp_edit_settings', 'currency_settings', array( &$this, 'admin_page' ) );
		add_action( "load-$page", array( $this, 'admin_load' ) );
		add_action( "load-$page", array( $this, 'admin_action' ) );
	}

	function admin_load() {
		get_current_screen()->add_help_tab( array(
			'id'      => 'overview',
			'title'   => __( 'Overview' ),
			'content' =>
				'<p>' . __( 'You can customize TheCartPress behaviour about international sales customisation currencies.', 'tcp' ) . '</p>' .
				'<p>' . __( 'Set Unit weight to use across your site.'. 'tcp' ) . '</p>' .
				'<p>' . __( 'You can customize Countries to use across your installation.', 'tcp' ) . '</p>'
		) );
		get_current_screen()->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'tcp' ) . '</strong></p>' .
			'<p>' . __( '<a href="http://thecartpress.com" target="_blank">Documentation on TheCartPress</a>', 'tcp' ) . '</p>' .
			'<p>' . __( '<a href="http://community.thecartpress.com/" target="_blank">Support Forums</a>', 'tcp' ) . '</p>' .
			'<p>' . __( '<a href="http://extend.thecartpress.com/" target="_blank">Extend site</a>', 'tcp' ) . '</p>'
		);
		//wp_enqueue_script('custom-background');
		//wp_enqueue_style('farbtastic');
	}

	function admin_page() { ?>
<div class="wrap">

<?php screen_icon( 'tcp-localize' ); ?><h2><?php _e( 'Localize', 'tcp' ); ?></h2>

<?php if ( !empty( $this->updated ) ) : ?>
	<div id="message" class="updated">
	<p><?php _e( 'Settings updated', 'tcp' ); ?></p>
	</div>
<?php endif; ?>

<?php global $thecartpress;
$currency				= $thecartpress->get_setting( 'currency', 'EUR' );
$currency_layout		= $thecartpress->get_setting( 'currency_layout', '%1$s%2$s (%3$s)' );
$decimal_currency		= (int)$thecartpress->get_setting( 'decimal_currency', 2 );
$decimal_point			= $thecartpress->get_setting( 'decimal_point', '.' );
$thousands_separator	= $thecartpress->get_setting( 'thousands_separator', ',' );
$use_weight				= $thecartpress->get_setting( 'use_weight', true );
$unit_weight			= $thecartpress->get_setting( 'unit_weight', 'gr' );
$date_format			= $thecartpress->get_setting( 'date_format', 'y-m-d' );
$country				= $thecartpress->get_setting( 'country', '' );
$billing_isos			= $thecartpress->get_setting( 'billing_isos', array() );
$shipping_isos			= $thecartpress->get_setting( 'shipping_isos', array() ); ?>

<form method="post" action="">

<h3><?php _e( 'Currency Settings', 'tcp' ); ?></h3>

<div id="excerpt_content" class="postbox">
<div class="inside">

<table class="form-table">
<tbody>
<tr valign="top">
	<th scope="row">
		<label for="currency"><?php _e( 'Currency', 'tcp' ); ?></label>
	</th>
	<td>
		<select id="currency" name="currency">
		<?php $currencies = Currencies::getAll();
		foreach( $currencies as $currency_row ) : ?>
			<option value="<?php echo $currency_row->iso; ?>" <?php selected( $currency_row->iso, $currency ); ?>><?php echo $currency_row->currency; ?></option>
		<?php endforeach; ?>
		</select>
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="tcp_custom_layouts"><?php _e( 'Currency layouts', 'tcp' ); ?></label>
	</th>
	<td>
		<p><label for="tcp_custom_layouts"><?php _e( 'Default layouts', 'tcp' ); ?>:</label>
		<select id="tcp_custom_layouts" onchange="jQuery('#currency_layout').val(jQuery('#tcp_custom_layouts').val());">
			<option value="%1$s%2$s %3$s" <?php selected( '%1$s%2$s %3$s', $currency_layout); ?>><?php _e( 'Currency sign left, Currency ISO right: $100 USD', 'tcp' ); ?></option>
			<option value="%1$s%2$s" <?php selected( '%1$s%2$s', $currency_layout); ?>><?php _e( 'Currency sign left: $100', 'tcp' ); ?></option>
			<option value="%2$s %1$s" <?php selected( '%2$s %1$s', $currency_layout); ?>><?php _e( 'Currency sign right: 100 &euro;', 'tcp' ); ?></option>
		</select>
		</p>
		<label for="currency_layouts"><?php _e( 'Custom layout', 'tcp' ); ?>:</label>
		<input type="text" id="currency_layout" name="currency_layout" value="<?php echo stripslashes( $currency_layout ); ?>" size="20" maxlength="25" />
		<p class="description"><?php _e( '%1$s -> Currency; %2$s -> Amount; %3$s -> ISO Code. By default, use %1$s%2$s (%3$s) -> $100 (USD).', 'tcp' ); ?></p>
		<p class="description"><?php _e( 'For Example: For Euro use %2$s %1$s -> 100&euro;.', 'tcp' ); ?></p>
		<p class="description"><?php _e( 'If this value is left to blank, then TheCartPress will take this layout from the languages configuration files (mo files), looking for the literal "%1$s%2$s (%3$s)."', 'tcp' ); ?></p>
	</td>
</tr>
<tr valign="top">
	<th scope="row">
		<label for="decimal_currency"><?php _e( 'Currency decimals', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="text" id="decimal_currency" name="decimal_currency" value="<?php echo $decimal_currency; ?>" size="1" maxlength="1" class="tcp_count"/>
	</td>
</tr>
<tr valign="top">
	<th scope="row">
		<label for="decimal_point"><?php _e( 'Decimal point separator', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="text" id="decimal_point" name="decimal_point" value="<?php echo stripslashes( $decimal_point ); ?>" size="1" maxlength="1" />
	</td>
</tr>
<tr valign="top">
	<th scope="row">
		<label for="thousands_separator"><?php _e( 'Thousands separator', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="text" id="thousands_separator" name="thousands_separator" value="<?php echo stripslashes( $thousands_separator ); ?>" size="1" maxlength="1" />
	</td>
</tr>
<tr valign="top">
	<th scope="row">
		<label for="use_weight"><?php _e( 'Use weight', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="checkbox" name="use_weight" id="use_weight" <?php checked( $use_weight ); ?> value="yes" />
		<p class="description"><?php _e( 'Allows to show or hide weight values in the store', 'tcp' ); ?></p>
	</td>
</tr>
<tr valign="top">
	<th scope="row">
		<label for="unit_weight"><?php _e( 'Unit weight', 'tcp' ); ?></label>
	</th>
	<td>
		<select id="unit_weight" name="unit_weight">
			<option value="kg." <?php selected( 'kg.', $unit_weight ); ?>><?php _e( 'Kilogram (kg)', 'tcp' ); ?></option>
			<option value="gr." <?php selected( 'gr.', $unit_weight ); ?>><?php _e( 'Gram (gr)', 'tcp' ); ?></option>
			<option value="T." <?php selected( 'T.', $unit_weight ); ?>><?php _e( 'Ton (t)', 'tcp' ); ?></option>
			<option value="mg." <?php selected( 'mg.', $unit_weight ); ?>><?php _e( 'Milligram (mg)', 'tcp' ); ?></option>
			<option value="ct." <?php selected( 'ct.', $unit_weight ); ?>><?php _e( 'Karat (ct)', 'tcp' ); ?></option>
			<option value="oz." <?php selected( 'oz.', $unit_weight ); ?>><?php _e( 'Ounce (oz)', 'tcp' ); ?></option>
			<option value="lb." <?php selected( 'lb.', $unit_weight ); ?>><?php _e( 'Pound (lb)', 'tcp' ); ?></option>
			<option value="oz t." <?php selected( 'oz t.', $unit_weight ); ?>><?php _e( 'Troy ounce (oz t)', 'tcp' ); ?></option>
			<option value="dwt." <?php selected( 'dwt.', $unit_weight ); ?>><?php _e( 'Pennyweight (dwt)', 'tcp' ); ?></option>
			<option value="gr. (en)" <?php selected( 'gr. (en)', $unit_weight ); ?>><?php _e( 'Grain (gr)', 'tcp' ); ?></option>
			<option value="cwt." <?php selected( 'cwt.', $unit_weight ); ?>><?php _e( 'Hundredweight (cwt)', 'tcp' ); ?></option>
			<option value="st." <?php selected( 'st.', $unit_weight ); ?>><?php _e( 'Ston (st)', 'tcp' ); ?></option>
			<option value="T. (long)" <?php selected( 'T. (long)', $unit_weight ); ?>><?php _e( 'Long ton (T long)', 'tcp' ); ?></option>
			<option value="T. (short)" <?php selected( 'T. (short)', $unit_weight ); ?>><?php _e( 'Short ton (T shors)', 'tcp' ); ?></option>
			<option value="hw. (long)" <?php selected( 'hw. (long)', $unit_weight ); ?>><?php _e( 'Long Hundredweights (hw long)', 'tcp' ); ?></option>
			<option value="hw. (short)" <?php selected( 'hw. (short)', $unit_weight ); ?>><?php _e( 'Short Hundredweights (hw short)', 'tcp' ); ?></option>
			<option value="koku" <?php selected( 'koku', $unit_weight ); ?>><?php _e( 'koku', 'tcp' ); ?></option>
			<option value="kann" <?php selected( 'kann', $unit_weight ); ?>><?php _e( 'kann', 'tcp' ); ?></option>
			<option value="kinn" <?php selected( 'kinn', $unit_weight ); ?>><?php _e( 'kinn', 'tcp' ); ?></option>
			<option value="monnme" <?php selected( 'monnme', $unit_weight ); ?>><?php _e( 'monnme', 'tcp' ); ?></option>
			<option value="tael" <?php selected( 'tael', $unit_weight ); ?>><?php _e( 'tael', 'tcp' ); ?></option>
			<option value="ku ping" <?php selected( 'ku ping', $unit_weight ); ?>><?php _e( 'ku ping', 'tcp' ); ?></option>
		</select>
	</td>
</tr>

</tbody>
</table>
</div>
</div>

<h3><?php _e( 'Date Settings', 'tcp'); ?></h3>

<div id="excerpt_content" class="postbox">
<div class="inside">
<table class="form-table">
<tbody>
<tr valign="top">
	<th scope="row">
		<label for="date_format"><?php _e( 'Date format', 'tcp' ); ?></label>
	</th>
	<td>
		<label><?php _e( 'Default format', 'tcp' ); ?>: <select id="date_format_predesign">
		<?php $date_format_predesigns = array(
			'Y-m-d' => 'Y-m-d',
			'd-m-Y' => 'd-m-Y',
			'd-M-Y' => 'd-M-Y',
			'M d, Y' => 'M d, Y',
		);
		foreach( $date_format_predesigns as $key => $date_format_predesign ) : ?>
			<option value="<?php echo $key; ?>" <?php selected( $key, $date_format ); ?>><?php echo $date_format_predesign; ?></option>
		<?php endforeach; ?>
		</select></label>
		<br/>
		<label><?php _e( 'Custom format', 'tcp' ); ?>: <input type="text" id="date_format" name="date_format" class="input-medium" value="<?php echo $date_format; ?>"/></label>
		<script>
		jQuery( '#date_format_predesign' ).on( 'change', function( e ) {
			var format = jQuery( this ).val();
			jQuery( '#date_format' ).val( format );
			return false;
		} );
		</script>
		<p class="description"><?php _e( 'Format: Y - Year (4 digits), y -> year (2 digits), M - Month (2 digits), d (days)', 'tcp' ); ?></p>
	</td>
</tr>
</tbody>
</table>
</div>
</div>

<h3><?php _e( 'Countries Settings', 'tcp'); ?></h3>

<div id="excerpt_content" class="postbox">
<div class="inside">
<table class="form-table">
<tbody>
<tr valign="top">
	<th scope="row">
		<label for="country"><?php _e( 'Base country', 'tcp' ); ?></label>
	</th>
	<td>
		<select id="country" name="country">
		<?php $countries = TCPCountries::getAll();
		foreach( $countries as $item ) : ?>
			<option value="<?php echo $item->iso; ?>" <?php selected( $item->iso, $country ); ?>><?php echo $item->name; ?></option>
		<?php endforeach; ?>
		</select>
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="all_billing_isos"><?php _e( 'Allowed billing countries', 'tcp' ); ?></label>
	</th>
	<td>
		<p><?php _e( 'All countries', 'tcp' ); ?>:&nbsp;<input type="checkbox" name="all_billing_isos" id="all_billing_isos" <?php checked( count( $billing_isos ), 0 ); ?> value="yes"
		onclick="if (this.checked) { jQuery('.sel_billing_isos').hide(); tcp_select_none('billing_isos'); } else { jQuery('.sel_billing_isos').show(); }"/></p>
		<div class="sel_billing_isos" <?php if ( count( $billing_isos ) == 0 ) echo 'style="display:none;"'; ?> >
			<select id="billing_isos" name="billing_isos[]" style="height:auto" size="8" multiple>
			<?php $countries = TCPCountries::getAll();
			foreach( $countries as $item ) :?>
				<option value="<?php echo $item->iso; ?>" <?php tcp_selected_multiple( $billing_isos, $item->iso ); ?>><?php echo $item->name; ?></option>
			<?php endforeach; ?>
			</select>
			<p>
			<input type="button" value="<?php _e( 'EU', 'tcp'); ?>" title="<?php _e( 'To select countries from the European Union', 'tcp' ); ?>" onclick="tcp_select_eu('billing_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'NAFTA', 'tcp'); ?>" title="<?php _e( 'To select countries from the NAFTA', 'tcp' ); ?>" onclick="tcp_select_nafta('billing_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'CARICOM', 'tcp'); ?>" title="<?php _e( 'To select countries from CARICOM', 'tcp' ); ?>" onclick="tcp_select_caricom('billing_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'MERCASUR', 'tcp'); ?>" title="<?php _e( 'To select countries from MERCASUR', 'tcp' ); ?>" onclick="tcp_select_mercasur('billing_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'CAN', 'tcp'); ?>" title="<?php _e( 'To select countries from Andean Comunity', 'tcp' ); ?>" onclick="tcp_select_can('billing_isos');" class="button-secondary"/>				
			<input type="button" value="<?php _e( 'AU', 'tcp'); ?>" title="<?php _e( 'To select countries from African Union', 'tcp' ); ?>" onclick="tcp_select_au('billing_isos');" class="button-secondary"/>				
			<input type="button" value="<?php _e( 'APEC', 'tcp'); ?>" title="<?php _e( 'To select countries from Asia-Pacific Economic Cooperation', 'tcp' ); ?>" onclick="tcp_select_apec('billing_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'ASEAN', 'tcp'); ?>" title="<?php _e( 'To select countries from Association of Southeast Asian Nations', 'tcp' ); ?>" onclick="tcp_select_asean('billing_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'Toggle', 'tcp'); ?>" title="<?php _e( 'Toggle the selected ones', 'tcp' ); ?>" onclick="tcp_select_toggle('billing_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'None', 'tcp'); ?>" title="<?php _e( 'Deselect all', 'tcp' ); ?>" onclick="tcp_select_none('billing_isos');" class="button-secondary"/>
			</p>
			<script>
			jQuery('#billing_isos').tcp_convert_multiselect();
			</script>
		</div>
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="all_shipping_isos"><?php _e( 'Allowed Shipping countries', 'tcp' ); ?></label>
	</th>
	<td>
		<p><?php _e( 'All countries', 'tcp' ); ?>:&nbsp;<input type="checkbox" name="all_shipping_isos" id="all_shipping_isos" <?php checked( count( $shipping_isos ), 0 ); ?> value="yes"
		onclick="if (this.checked) { jQuery('.sel_shipping_isos').hide(); tcp_select_none('shipping_isos'); } else { jQuery('.sel_shipping_isos').show(); }"/>
		</p>
		<div class="sel_shipping_isos" <?php if ( ! $shipping_isos ) echo 'style="display:none;"'; ?>>
			<select id="shipping_isos" name="shipping_isos[]" style="height:auto" size="8" multiple>
			<?php $countries = TCPCountries::getAll();
			foreach( $countries as $item ) :?>
				<option value="<?php echo $item->iso; ?>" <?php tcp_selected_multiple( $shipping_isos, $item->iso ); ?>><?php echo $item->name; ?></option>
			<?php endforeach; ?>
			</select>
			<p>
			<input type="button" value="<?php _e( 'EU', 'tcp'); ?>" title="<?php _e( 'To select countries from the European Union', 'tcp' ); ?>" onclick="tcp_select_eu('shipping_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'NAFTA', 'tcp'); ?>" title="<?php _e( 'To select countries from the NAFTA', 'tcp' ); ?>" onclick="tcp_select_nafta('shipping_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'CARICOM', 'tcp'); ?>" title="<?php _e( 'To select countries from CARICOM', 'tcp' ); ?>" onclick="tcp_select_caricom('shipping_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'MERCASUR', 'tcp'); ?>" title="<?php _e( 'To select countries from MERCASUR', 'tcp' ); ?>" onclick="tcp_select_mercasur('shipping_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'CAN', 'tcp'); ?>" title="<?php _e( 'To select countries from Andean Comunity', 'tcp' ); ?>" onclick="tcp_select_can('shipping_isos');" class="button-secondary"/>				
			<input type="button" value="<?php _e( 'AU', 'tcp'); ?>" title="<?php _e( 'To select countries from African Union', 'tcp' ); ?>" onclick="tcp_select_au('shipping_isos');" class="button-secondary"/>				
			<input type="button" value="<?php _e( 'APEC', 'tcp'); ?>" title="<?php _e( 'To select countries from Asia-Pacific Economic Cooperation', 'tcp' ); ?>" onclick="tcp_select_apec('shipping_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'ASEAN', 'tcp'); ?>" title="<?php _e( 'To select countries from Association of Southeast Asian Nations', 'tcp' ); ?>" onclick="tcp_select_asean('shipping_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'Toggle', 'tcp'); ?>" title="<?php _e( 'Toggle the selected ones', 'tcp' ); ?>" onclick="tcp_select_toggle('shipping_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'None', 'tcp'); ?>" title="<?php _e( 'Deselect all', 'tcp' ); ?>" onclick="tcp_select_none('shipping_isos');" class="button-secondary"/>
			</p>
			<script>
			jQuery('#shipping_isos').tcp_convert_multiselect();
			</script>
		</div>
	</td>
</tr>

</tbody>
</table>
</div>
</div>

<?php do_action( 'tcp_localize_settings_page' ); ?>

<?php wp_nonce_field( 'tcp_currency_settings' ); ?>
<?php submit_button( null, 'primary', 'save-currency-settings' ); ?>

</form>

</div>
<?php
	}

	function admin_action() {
		if ( empty( $_POST ) ) return;
		check_admin_referer( 'tcp_currency_settings' );
		$settings = get_option( 'tcp_settings' );
		$settings['currency']				= isset( $_POST['currency'] ) ? $_POST['currency'] : 'EUR';		
		$settings['currency_layout']		= isset( $_POST['currency_layout'] ) ? $_POST['currency_layout'] : '%1$s%2$s (%3$s)';
		$settings['decimal_currency']		= (int)isset( $_POST['decimal_currency'] ) ? $_POST['decimal_currency'] : 2;
		$settings['decimal_point']			= isset( $_POST['decimal_point'] ) ? $_POST['decimal_point'] : '.';
		$settings['thousands_separator']	= isset( $_POST['thousands_separator'] ) ? $_POST['thousands_separator'] : ',';
		$settings['use_weight']				= isset( $_POST['use_weight'] );// ? $_POST['use_weight'] == 'yes' : false;
		$settings['unit_weight']			= isset( $_POST['unit_weight'] ) ? $_POST['unit_weight'] : 'gr';
		$settings['date_format']			= isset( $_POST['date_format'] ) ? $_POST['date_format'] : 'y-m-d';
		if ( isset( $_POST['all_shipping_isos'] ) && $_POST['all_shipping_isos'] == 'yes' )  {
			$settings['shipping_isos']		= array();
		} else {
			$settings['shipping_isos']		= isset( $_POST['shipping_isos'] ) ? $_POST['shipping_isos'] : array();
		}
		if ( isset( $_POST['all_billing_isos'] ) && $_POST['all_billing_isos'] == 'yes' ) {
			$settings['billing_isos']		= array();
		} else {
			$settings['billing_isos']		= isset( $_POST['billing_isos'] ) ? $_POST['billing_isos'] : array();
		}
		$settings['country']				= isset( $_POST['country'] ) ? $_POST['country'] : '';
		$settings = apply_filters( 'tcp_localize_settings_action', $settings );
		update_option( 'tcp_settings', $settings );
		$this->updated = true;
		global $thecartpress;
		$thecartpress->load_settings();
	}
}

new TCPCurrencyCountrySettings();
endif; // class_exists check