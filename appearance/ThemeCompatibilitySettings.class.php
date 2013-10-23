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

if ( ! class_exists( 'TCPThemeCompatibilitySettings' ) ) {

class TCPThemeCompatibilitySettings {

	private $updated = false;

	function __construct() {
		add_action( 'tcp_admin_menu', array( $this, 'tcp_admin_menu' ) );
		global $tcp_miranda;
		if ( $tcp_miranda ) $tcp_miranda->add_item( 'settings', 'default_settings', __( 'Theme Compatibility', 'tcp' ), false, array( 'TCPThemeCompatibilitySettings', __FILE__ ), plugins_url( 'images/miranda/theme_settings_48.png', TCP_FOLDER ) );
	}

	function tcp_admin_menu() {
		if ( ! current_user_can( 'tcp_edit_settings' ) ) return;
		global $thecartpress;
		$base = $thecartpress->get_base_appearance();
		add_menu_page( '', __( 'Look&Feel', 'tcp' ), 'tcp_edit_settings', $base, '', plugins_url( 'thecartpress/images/tcp.png', TCP_FOLDER ), 42 );
		$page = add_submenu_page( $base, __( 'Theme Compatibility Settings', 'tcp' ), __( 'Theme Compatibility', 'tcp' ), 'tcp_edit_settings', $base, array( &$this, 'admin_page' ) );
		add_action( "load-$page", array( &$this, 'admin_load' ) );
		add_action( "load-$page", array( &$this, 'admin_action' ) );
	}

	function admin_load() {
		get_current_screen()->add_help_tab( array(
			'id'	  => 'overview',
			'title'   => __( 'Overview' ),
			'content' =>
				'<p>' . __( 'Customize Theme Compatibility. Thanks to this feature, TheCartPress supports all themes.', 'tcp' ) . '</p>'
		) );

		get_current_screen()->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'tcp' ) . '</strong></p>' .
			'<p>' . __( '<a href="http://thecartpress.com" target="_blank">Documentation on TheCartPress</a>', 'tcp' ) . '</p>' .
			'<p>' . __( '<a href="http://community.thecartpress.com/" target="_blank">Support Forums</a>', 'tcp' ) . '</p>' .
			'<p>' . __( '<a href="http://extend.thecartpress.com/" target="_blank">Extend site</a>', 'tcp' ) . '</p>'
		);
	}

	function admin_page() { ?>
<div class="wrap">
	<?php screen_icon( 'tcp-theme' ); ?><h2><?php _e( 'Theme Compatibility Settings', 'tcp' ); ?></h2>

<?php if ( !empty( $this->updated ) ) : ?>
	<div id="message" class="updated">
	<p><?php _e( 'Settings updated', 'tcp' ); ?></p>
	</div>
<?php endif; ?>

<?php global $thecartpress;
if ( isset( $_POST['current_post_type'] ) && strlen( trim( $_POST['current_post_type'] ) ) > 0 ) {
	$current_post_type = $_POST['current_post_type'];
	$suffix = '-' . $current_post_type;
} else {
	$suffix = '';
	$current_post_type = '';
}

$load_default_buy_button_style	= $thecartpress->get_setting( 'load_default_buy_button_style', true );
$load_default_shopping_cart_checkout_style	= $thecartpress->get_setting( 'load_default_shopping_cart_checkout_style', true );
$load_default_loop_style = $thecartpress->get_setting( 'load_default_loop_style', true );

$products_per_page			= $thecartpress->get_setting( 'products_per_page' . $suffix, '10' );//TODO

$image_size_grouped_by_button = $thecartpress->get_setting( 'image_size_grouped_by_button' . $suffix, 'thumbnail' );
?>

<form method="post" action="">

<h3><?php _e( 'TheCartPress Styles', 'tcp' ); ?></h3>

<p class="description"><?php _e( 'Allows to load default styles provided by TheCartPress. To create your own styles, deactivate these settings. You could, also, customise by copying these CSS files to your theme.', 'tcp' ); ?></p>

<div class="postbox">

<table class="form-table">
<tbody>

<tr valign="top">
	<th scope="row">
	<label for="load_default_buy_button_style"><?php _e( 'Load default Buy Button styles', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="checkbox" id="load_default_buy_button_style" name="load_default_buy_button_style" value="yes" <?php checked( true, $load_default_buy_button_style ); ?> />
	</td>
</tr>

<tr valign="top">
	<th scope="row">
	<label for="load_default_shopping_cart_checkout_style"><?php _e( 'Load default Shopping Cart & Checkout styles', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="checkbox" id="load_default_shopping_cart_checkout_style" name="load_default_shopping_cart_checkout_style" value="yes" <?php checked( true, $load_default_shopping_cart_checkout_style ); ?> />
	</td>
</tr>

<tr valign="top">
	<th scope="row">
	<label for="load_default_loop_style"><?php _e( 'Load default Catalogue styles', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="checkbox" id="load_default_loop_style" name="load_default_loop_style" value="yes" <?php checked( true, $load_default_loop_style ); ?> />
	</td>
</tr>

<tr valign="top">
	<th scope="row">
	<label for="products_per_page"><?php _e( 'Product pages show at most', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="text" id="products_per_page" name="products_per_page" value="<?php echo $products_per_page; ?>" class="small-text tcp_count" maxlength="4"/>
		<?php _e( 'products', 'tcp'); ?>
	</td>
</tr>
</tbody>
</table>

</div>

<?php do_action( 'tcp_theme_compatibility_settings_page_top', $suffix, $thecartpress ); ?>

<h3 class="hndle"><?php _e( 'How to display Product Details', 'tcp' ); ?></h3>

<div class="postbox">

<table class="form-table">
<tbody>

<tr valign="top">
	<th scope="row">
		<label for="current_post_type"><?php _e( 'Post type', 'tcp' ); ?></label>
	</th>
	<td>
		<?php $post_types = get_post_types( '', 'object' ); ?>

		<select id="current_post_type" name="current_post_type">
			<option value="" <?php selected( true, $current_post_type ); ?>><?php _e( 'Default', 'tcp'); ?></option>
			<?php foreach( $post_types as $i => $post_type ) : 
				$existe = $thecartpress->get_setting( 'image_size_grouped_by_button-' . $i, 'false' ) !== 'false'; ?>
			<option value="<?php echo $i; ?>" <?php selected( $i, $current_post_type ); ?>
			<?php if ( $existe ) : ?> style="font-weight: bold;"<?php endif; ?>
			>
			<?php echo $post_type->labels->singular_name; ?><?php if ( $thecartpress->get_setting( 'image_size_grouped_by_button-' . $i, false ) !== false ) : ?> (*)<?php endif; ?>
			<?php if ( $existe ) : ?> *<?php endif; ?>
			</option>
			<?php endforeach; ?>
		</select>

		<input type="submit" name="load_post_type_settings" value="<?php _e( 'Load post type settings', 'tcp' ); ?>" class="button-secondary"/>
		<input type="submit" name="delete_post_type_settings" value="<?php _e( 'Delete post type settings', 'tcp' ); ?>" class="button-secondary"/>
		<p class="description"><?php _e( 'Allows to create different configuration for each Post Type.', 'tcp' ); ?></p>
		<span class="description"><?php _e( 'Options in bold have a specific configuration.', 'tcp' ); ?>
		<?php _e( 'Remember to save the changes before to load new post type settings.', 'tcp' ); ?>
		</span>

	</td>
</tr>

<tr valign="top">
	<th scope="row">
	<label for="image_size_grouped_by_button"><?php _e( 'Image size grouped buy button', 'tcp' ); ?></label>
	</th>
	<td>
		<select id="image_size_grouped_by_button" name="image_size_grouped_by_button">
			<option value="none" <?php selected( 'none', $image_size_grouped_by_button ); ?>><?php _e( 'No image', 'tcp' ); ?></option>
			<option value="thumbnail" <?php selected( 'thumbnail', $image_size_grouped_by_button ); ?>><?php _e( 'Thumbnail', 'tcp' ); ?></option>
			<option value="64" <?php selected( '64', $image_size_grouped_by_button ); ?>><?php _e( '64x64', 'tcp' ); ?></option>
			<option value="48" <?php selected( '48', $image_size_grouped_by_button ); ?>><?php _e( '48x48', 'tcp' ); ?></option>
			<option value="32" <?php selected( '32', $image_size_grouped_by_button ); ?>><?php _e( '32x32', 'tcp' ); ?></option>
		</select>
		<p class="description"><?php _e( 'allows to select the size of the image to show in grouped products.', 'tcp' ); ?></p>
	</td>
</tr>

</tbody>
</table>

</div><!-- .postbox -->

<?php do_action( 'tcp_theme_compatibility_settings_page', $suffix, $thecartpress ); ?>

<?php wp_nonce_field( 'tcp_theme_compatibility_settings' ); ?>
<?php submit_button( null, 'primary', 'save-theme_compatibility-settings' ); ?>
</form>
</div><!-- .wrap -->
<?php
	}

	function admin_action() {
		if ( empty( $_POST ) ) return;
		check_admin_referer( 'tcp_theme_compatibility_settings' );
		if ( isset( $_POST['load_post_type_settings'] ) ) return;
		if ( isset( $_POST['current_post_type'] )  && strlen( $_POST['current_post_type'] ) > 0 ) $suffix = '-' . $_POST['current_post_type'];
		else $suffix = '';
		if ( isset( $_POST['delete_post_type_settings'] ) ) {
			if ( strlen( $suffix ) == 0 ) return;
			$settings = get_option( 'tcp_settings' );
			unset( $settings['products_per_page' . $suffix] );

			unset( $settings['image_size_grouped_by_button' . $suffix] );


			$settings = apply_filters( 'tcp_theme_compatibility_unset_settings_action', $settings, $suffix );
			update_option( 'tcp_settings', $settings );
			$this->updated = true;
			global $thecartpress;
			$thecartpress->load_settings();
			return;
		}
		$settings = get_option( 'tcp_settings' );

		$settings['load_default_buy_button_style']			= isset( $_POST['load_default_buy_button_style'] ) ? $_POST['load_default_buy_button_style'] == 'yes' : false;
		$settings['load_default_shopping_cart_checkout_style'] = isset( $_POST['load_default_shopping_cart_checkout_style'] ) ? $_POST['load_default_shopping_cart_checkout_style'] == 'yes' : false;
		$settings['load_default_loop_style']				= isset( $_POST['load_default_loop_style'] ) ? $_POST['load_default_loop_style'] == 'yes' : false;

		$settings['products_per_page' . $suffix]			= isset( $_POST[ 'products_per_page' ] ) ? $_POST[ 'products_per_page' ] : false;

		$settings['image_size_grouped_by_button' . $suffix]	= isset( $_POST['image_size_grouped_by_button'] ) ? $_POST['image_size_grouped_by_button'] : 'thumbnail';
		$settings['see_image_in_content' . $suffix]			= isset( $_POST['see_image_in_content'] ) ? $_POST['see_image_in_content'] == 'yes' : false;
		$settings['image_size_content' . $suffix]			= isset( $_POST['image_size_content'] ) ? $_POST['image_size_content'] : 'thumbnail';
		$settings['image_align_content' . $suffix]			= isset( $_POST['image_align_content'] ) ? $_POST['image_align_content'] : 'north';
		$settings['image_link_content' . $suffix]			= isset( $_POST['image_link_content'] ) ? $_POST['image_link_content'] : '';

		$settings = apply_filters( 'tcp_theme_compatibility_settings_action', $settings, $suffix );
		
		update_option( 'tcp_settings', $settings );
		$this->updated = true;
		global $thecartpress;
		$thecartpress->load_settings();
	}
}

new TCPThemeCompatibilitySettings();
} // class_exists check