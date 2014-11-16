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

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'TCPMainSettings' ) ) {

class TCPMainSettings {

	private $updated = false;

	function __construct() {
		add_action( 'tcp_admin_menu', array( $this, 'tcp_admin_menu' ) );
		global $tcp_miranda;
		if ( $tcp_miranda ) $tcp_miranda->add_item( 'settings', 'default_settings', __( 'Main Settings', 'tcp' ), false, array( 'TCPMainSettings', __FILE__ ) );
	}

	function tcp_admin_menu() {
		if ( ! current_user_can( 'tcp_edit_settings' ) ) return;
		global $thecartpress;
		$base = $thecartpress->get_base_settings();
		$page = add_submenu_page( $base, __( 'Main Settings', 'tcp' ), __( 'Main Settings', 'tcp' ), 'tcp_edit_settings', $base, array( &$this, 'admin_page' ) );

		add_action( "load-$page", array( $this, 'admin_load' ) );
		add_action( "load-$page", array( $this, 'admin_action' ) );
	}

	function admin_load() {
		get_current_screen()->add_help_tab( array(
			'id'	  => 'overview',
			'title'   => __( 'Overview' ),
			'content' =>
				'<p>' . __( 'You can customize TheCartPress as a Framework disabling the eCommerce funtionalities.', 'tcp' ) . '</p>' .
				'<p>' . __( 'Set the different URLs for TheCartPress actions', 'tcp' ) . '</p>'
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
	<?php screen_icon( 'tcp-main' ); ?><h2><?php _e( 'Main Settings', 'tcp' ); ?></h2>
<?php if ( !empty( $this->updated ) ) : ?>
	<div id="message" class="updated">
	<p><?php _e( 'Settings updated', 'tcp' ); ?></p>
	</div>
<?php endif; ?>

<?php global $thecartpress;
$disable_ecommerce		= $thecartpress->get_setting( 'disable_ecommerce', false );
$disable_shopping_cart	= $thecartpress->get_setting( 'disable_shopping_cart', false );
$continue_url			= $thecartpress->get_setting( 'continue_url', '' );
$after_add_to_cart		= $thecartpress->get_setting( 'after_add_to_cart', '' );
$hide_downloadable_menu	= $thecartpress->get_setting( 'hide_downloadable_menu', false );
$downloadable_path		= $thecartpress->get_setting( 'downloadable_path', '' );
$hide_visibles			= $thecartpress->get_setting( 'hide_visibles', false ); ?>
<form method="post" action="">
<div class="postbox">
<div class="inside">
<table class="form-table">
<tbody>
<tr valign="top">
	<th scope="row">
		<label for="disable_ecommerce"><?php _e( 'Disable eCommerce', 'tcp' ); ?></label>
	</th>
	<td>
	<input type="checkbox" id="disable_ecommerce" name="disable_ecommerce" value="yes" <?php checked( true, $disable_ecommerce ); ?> />
	<span class="description"><?php _e( 'To use TheCartPress as a Framework, disabling all eCommerce functionalities.', 'tcp' ); ?></span>
	</td>
</tr>
<tr valign="top">
	<th scope="row">
		<label for="disable_shopping_cart"><?php _e( 'Disable Shopping Cart', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="checkbox" id="disable_shopping_cart" name="disable_shopping_cart" value="yes" <?php checked( true, $disable_shopping_cart ); ?> />
		<span class="description"><?php _e( 'To use TheCartPress as a catalog, disabling the Shopping Cart and the Checkout.', 'tcp' ); ?></span>
	</td>
</tr>
<tr valign="top">
	<th scope="row">
		<label for="continue_url"><?php _e( 'Continue Shopping in', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="text" id="continue_url" name="continue_url" value="<?php echo $continue_url; ?>" size="50" maxlength="255" />
		<p class="description"><?php _e( 'This value is used in the Continue shopping link into the Shopping Cart page.', 'tcp' ); ?></p>
		<p class="description"><?php _e( 'If the value is left to blank then the "home url" will be used.', 'tcp' ); ?></p>
	</td>
</tr>
<tr valign="top">
	<th scope="row">
		<label for="continue_url"><?php _e( 'After adding to cart', 'tcp' ); ?></label>
	</th>
	<td>
		<select id="after_add_to_cart" name="after_add_to_cart">
			<option value="" <?php selected( $after_add_to_cart, '' ); ?>><?php _e( 'Nothing', 'tcp' ); ?></option>
			<option value="ssc" <?php selected( $after_add_to_cart, 'ssc' ); ?>><?php _e( 'Show the Shopping Cart', 'tcp' ); ?></option>
			<option value="sco" <?php selected( $after_add_to_cart, 'sco' ); ?>><?php _e( 'Show Checkout', 'tcp' ); ?></option>
		</select>
	</td>
</tr>
<tr valign="top">
	<th scope="row">
		<label for="continue_url"><?php _e( 'Hide downloadable menu', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="checkbox" id="hide_downloadable_menu" name="hide_downloadable_menu" value="yes" <?php checked( $hide_downloadable_menu, true ); ?> />
	</td>
</tr>
<tr valign="top">
	<th scope="row">
		<label for="continue_url"><?php _e( 'Downloadable path', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="text" id="downloadable_path" name="downloadable_path" value="<?php echo $downloadable_path; ?>" size="50" maxlength="255" />
		<p class="description"><?php _e( 'To protect the downloadable files, from public download, this path must be non-public directory.', 'tcp' ); ?></p>
		<p class="description"><?php printf( __( 'For example, path for the current page in your server is: %s' , 'tcp' ), dirname( __FILE__ ) ); ?></p>
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="hide_visibles"><?php _e( 'Hide visibles', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="checkbox" id="hide_visibles" name="hide_visibles" value="yes" <?php checked( $hide_visibles, true ); ?> />
		<span class="description"><?php _e( 'Hide the invisible products in the back-end.', 'tcp' ); ?></span>
	</td>
</tr>
<?php do_action( 'tcp_main_settings_page', $thecartpress ); ?>
</tbody>
</table>
</div>
</div><!-- .postbox -->

<?php do_action( 'tcp_main_settings_after_page', $thecartpress ); ?>

<?php wp_nonce_field( 'tcp_main_settings' ); ?>
<?php submit_button( null, 'primary', 'save-main-settings' ); ?>
</form>
</div><?php
	}

	function admin_action() {
		if ( empty( $_POST ) ) return;
		check_admin_referer( 'tcp_main_settings' );
		$settings = get_option( 'tcp_settings' );
		$settings['disable_ecommerce'] = isset( $_POST['disable_ecommerce'] );// ? $_POST['disable_ecommerce'] == 'yes' : false;
		if ( $settings['disable_ecommerce'] ) $settings['disable_shopping_cart'] = true;
		else $settings['disable_shopping_cart']	= isset( $_POST['disable_shopping_cart'] );// ? $_POST['disable_shopping_cart'] == 'yes' : false;
		$settings['continue_url'] = isset( $_POST['continue_url'] ) ? wp_filter_nohtml_kses( $_POST['continue_url'] ) : '';
		$settings['after_add_to_cart'] = isset( $_POST['after_add_to_cart'] ) ? $_POST['after_add_to_cart'] : '';
		$settings['hide_downloadable_menu'] = isset( $_POST['hide_downloadable_menu'] );// ? $_POST['hide_downloadable_menu'] == 'yes' : false;
		$settings['downloadable_path'] = isset( $_POST['downloadable_path'] ) ? wp_filter_nohtml_kses( $_POST['downloadable_path'] ) : '';
		$settings['hide_visibles'] = isset( $_POST['hide_visibles'] );// ? $_POST['hide_visibles'] == 'yes' : false;
		$settings = apply_filters( 'tcp_main_settings_action', $settings );
		update_option( 'tcp_settings', $settings );
		$this->updated = true;
		global $thecartpress;
		$thecartpress->load_settings();
	}
}

new TCPMainSettings();
} // class_exists check