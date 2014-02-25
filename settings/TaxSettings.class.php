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
if ( !defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'TCPTaxSettings' ) ) {

class TCPTaxSettings {

	private $updated = false;

	function __construct() {
		add_action( 'tcp_admin_menu', array( $this, 'tcp_admin_menu' ) );
		global $tcp_miranda;
		if ( $tcp_miranda ) $tcp_miranda->add_item( 'settings', 'default_settings', __( 'Tax', 'tcp' ), false, array( 'TCPTaxSettings', __FILE__ ), plugins_url( 'thecartpress/images/miranda/tax_settings_48.png' ) );
	}

	function tcp_admin_menu() {
		if ( ! current_user_can( 'tcp_edit_settings' ) ) return;
		$base = thecartpress()->get_base_settings();
		$page = add_submenu_page( $base, __( 'Tax Settings', 'tcp' ), __( 'Tax', 'tcp' ), 'tcp_edit_settings', 'tax_settings', array( &$this, 'admin_page' ) );
		add_action( "load-$page", array( $this, 'admin_load' ) );
		add_action( "load-$page", array( $this, 'admin_action' ) );
	}

	function admin_load() {
		get_current_screen()->add_help_tab( array(
			'id'	  => 'overview',
			'title'   => __( 'Overview' ),
			'content' =>
				'<p>' . __( 'You can customize how to apply the taxes.', 'tcp' ) . '</p>'
		) );

		get_current_screen()->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'tcp' ) . '</strong></p>' .
			'<p>' . __( '<a href="http://thecartpress.com" target="_blank">Documentation on TheCartPress</a>', 'tcp' ) . '</p>' .
			'<p>' . __( '<a href="http://community.thecartpress.com/" target="_blank">Support Forums</a>', 'tcp' ) . '</p>'
		);
		//wp_enqueue_script('custom-background');
		//wp_enqueue_style('farbtastic');
	}

	function admin_page() { ?>
<div class="wrap">
	<?php screen_icon( 'tcp-tax' ); ?><h2><?php _e( 'Tax Settings', 'tcp' ); ?></h2>

<?php if ( !empty( $this->updated ) ) : ?>
	<div id="message" class="updated">
	<p><?php _e( 'Settings updated', 'tcp' ); ?></p>
	</div>
<?php endif; ?>

<?php global $thecartpress;
$default_tax_country		= $thecartpress->get_setting( 'default_tax_country', '' );
$tax_based_on				= $thecartpress->get_setting( 'tax_based_on' );
$prices_include_tax			= $thecartpress->get_setting( 'prices_include_tax', false );
$shipping_cost_include_tax	= $thecartpress->get_setting( 'shipping_cost_include_tax', false );
$display_prices_with_taxes	= $thecartpress->get_setting( 'display_prices_with_taxes', false );
$tax_for_shipping			= $thecartpress->get_setting( 'tax_for_shipping', '' );
$display_shipping_cost_with_taxes	= $thecartpress->get_setting( 'display_shipping_cost_with_taxes', false ); ?>

<form method="post" action="">
<div class="postbox">
<div class="inside">
<table class="form-table">
<tbody>
<tr valign="top">
	<th scope="row">
		<label for="default_tax_country"><?php _e( 'Default tax country', 'tcp' ); ?></label>
	</th>
	<td>
		<?php if ( $default_tax_country == '' ) $default_tax_country = $thecartpress->get_setting( 'country', '' );
		$billing_isos	= $thecartpress->get_setting( 'billing_isos', array() );
		$shipping_isos	= $thecartpress->get_setting( 'shipping_isos', array() );
		$isos			= array_merge( $billing_isos, $shipping_isos ); ?>
		<select id="default_tax_country" name="default_tax_country">
		<?php require_once( TCP_DAOS_FOLDER . 'Countries.class.php' );
		if ( is_array( $isos ) && count( $isos ) > 0 ) {
			$countries = TCPCountries::getSome( $isos );
		} else {
			$countries = TCPCountries::getAll();
		}
		foreach( $countries as $item ) : ?>
			<option value="<?php echo $item->iso; ?>" <?php selected( $item->iso, $default_tax_country ); ?>><?php echo $item->name; ?></option>
		<?php endforeach; ?>
		</select>
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="tax_based_on"><?php _e( 'Tax based on', 'tcp' ); ?></label>
	</th>
	<td>
		<select id="tax_based_on" name="tax_based_on">
			<option value="origin" <?php selected( 'origin', $tax_based_on ); ?>><?php _e( 'Default tax country', 'tcp' ); ?></option>
			<option value="billing" <?php selected( 'billing', $tax_based_on ); ?>><?php _e( 'Billing address', 'tcp' ); ?></option>
			<option value="shipping" <?php selected( 'shipping', $tax_based_on ); ?>><?php _e( 'Shipping address', 'tcp' ); ?></option>
		</select>
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="prices_include_tax"><?php _e( 'Prices include tax', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="checkbox" id="prices_include_tax" name="prices_include_tax" value="yes" <?php checked( $prices_include_tax, true ); ?> />
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="display_prices_with_taxes"><?php _e( 'Display prices with taxes', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="checkbox" id="display_prices_with_taxes" name="display_prices_with_taxes" value="yes" <?php checked( $display_prices_with_taxes, true ); ?> />
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="shipping_cost_include_tax"><?php _e( 'Shipping cost include tax', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="checkbox" id="shipping_cost_include_tax" name="shipping_cost_include_tax" value="yes" <?php checked( $shipping_cost_include_tax, true ); ?> />
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="tax_for_shipping"><?php _e( 'Select tax for shipping/payment/other costs', 'tcp' ); ?></label>
	</th>
	<td>
		<select id="tax_for_shipping" name="tax_for_shipping">
			<option value="0"><?php _e( 'No tax', 'tcp' ); ?></option>
		<?php require_once( TCP_DAOS_FOLDER . 'Taxes.class.php' );
		$taxes = Taxes::getAll();
		foreach ( $taxes as $tax ) : ?>
			<option value="<?php echo $tax->tax_id; ?>" <?php selected( $tax->tax_id, $tax_for_shipping ); ?>><?php echo $tax->title; ?></option>
		<?php endforeach; ?>
		</select>
	</td>
</tr>

<tr valign="top">
	<th scope="row">
	<label for="display_shipping_cost_with_taxes"><?php _e( 'Display shipping prices with taxes', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="checkbox" id="display_shipping_cost_with_taxes" name="display_shipping_cost_with_taxes" value="yes" <?php checked( $display_shipping_cost_with_taxes, true ); ?> />
	</td>
</tr>
</tbody>
</table>
</div>
</div>
<?php wp_nonce_field( 'tcp_tax_settings' ); ?>
<?php submit_button( null, 'primary', 'save-tax-settings' ); ?>
</form>

</div>
<?php
	}

	function admin_action() {
		if ( empty( $_POST ) ) return;
		check_admin_referer( 'tcp_tax_settings' );
		$settings = get_option( 'tcp_settings' );
		$settings['default_tax_country']		= isset( $_POST['default_tax_country'] ) ? $_POST['default_tax_country'] : false;		
		$settings['prices_include_tax']			= isset( $_POST['prices_include_tax'] ) ? $_POST['prices_include_tax'] == 'yes' : false;
		$settings['tax_based_on']				= isset( $_POST['tax_based_on'] ) ? $_POST['tax_based_on'] : '';
		$settings['shipping_cost_include_tax']	= isset( $_POST['shipping_cost_include_tax'] ) ? $_POST['shipping_cost_include_tax'] == 'yes' : false;
		$settings['display_prices_with_taxes']	= isset( $_POST['display_prices_with_taxes'] ) ? $_POST['display_prices_with_taxes'] == 'yes' : false;
		$settings['tax_for_shipping']			= $_POST['tax_for_shipping'];
		$settings['display_shipping_cost_with_taxes']	= isset( $_POST['display_shipping_cost_with_taxes'] ) ? $_POST['display_shipping_cost_with_taxes'] == 'yes' : false;
		update_option( 'tcp_settings', $settings );
		$this->updated = true;
		global $thecartpress;
		$thecartpress->load_settings();
	}
}

new TCPTaxSettings();
} // class_exists check