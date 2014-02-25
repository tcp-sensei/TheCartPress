<?php
/**
 * Custom Style
 *
 * Allows to add a custom Css Style editor to add css code to the header of the site
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'TCPCustomStyles' ) ) :

class TCPCustomStyles {
	function __construct() {
		if ( is_admin() ) {
			add_action( 'tcp_admin_menu'	, array( $this, 'tcp_admin_menu' ), 40 );
		} else {
			add_filter( 'body_class'		, array( $this, 'body_classes' ) );
			add_action( 'wp_head'			, array( $this, 'wp_head' ) );
		}
		global $tcp_miranda;
		if ( $tcp_miranda ) $tcp_miranda->add_item( 'settings', 'default_settings', __( 'Custom Styles', 'tcp' ), false, array( 'TCPCustomStyles', __FILE__ ), plugins_url( 'thecartpress/images/miranda/customstyles_settings_48.png' ) );
	}

	function body_classes( $classes ) {
		if ( tcp_is_the_checkout_page() ) {
			$classes[] = 'tcp-store';
			$classes[] = 'tcp-checkout-page';
		} elseif ( tcp_is_the_shopping_cart_page() ) {
			$classes[] = 'tcp-store';
			$classes[] = 'tcp-shopping-cart-page';
		} elseif ( tcp_is_the_catalogue_page() ) {
			$classes[] = 'tcp-store';
			$classes[] = 'tcp-catalogue-page';
		} elseif ( is_tax() && tcp_is_saleable_taxonomy( tcp_get_current_taxonomy() ) ) {
			$classes[] = 'tcp-store';
		} elseif ( is_single() && tcp_is_saleable() ) {
			$classes[] = 'tcp-store';
		}
		return $classes;
	}

	function wp_head() {
		if ( ! get_option( 'tcp_custom_style_activate', false ) ) return;
		$custom_styles = stripslashes( get_option( 'tcp_custom_style', '' ) );
		if ( strlen( $custom_styles ) > 0 ) : ?>
<style type="text/css">
	<?php echo $custom_styles; ?>
</style>
		<?php endif;
	}

	function tcp_admin_menu() {
		if ( ! current_user_can( 'tcp_edit_settings' ) ) return;
		$base = thecartpress()->get_base_appearance();
		$page = add_submenu_page( $base, __( 'Custom Style', 'tcp' ), __( 'Custom Styles', 'tcp' ), 'tcp_edit_settings', 'custom_style_settings', array( &$this, 'admin_page' ) );
		add_action( "load-$page", array( $this, 'admin_load' ) );
		add_action( "load-$page", array( $this, 'admin_action' ) );
	}

	function admin_load() {
		get_current_screen()->add_help_tab( array(
		    'id'      => 'overview',
		    'title'   => __( 'Overview' ),
		    'content' =>
	            '<p>' . __( 'You can add Custom Styles.', 'tcp' ) . '</p>'
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
	<?php screen_icon( 'tcp-custom-styles' ); ?><h2><?php _e( 'Custom Styles', 'tcp' ); ?></h2>

<?php if ( ! empty( $this->updated ) ) : ?>
	<div id="message" class="updated">
	<p><?php _e( 'Settings updated', 'tcp' ); ?></p>
	</div>
<?php endif; ?>

<div class="clear"></div>

<form method="post">
	<?php $tcp_custom_style_activate = get_option( 'tcp_custom_style_activate', false ); ?>
	<label for="tcp_custom_style_activate"><input type="checkbox" name="tcp_custom_style_activate" id="tcp_custom_style_activate" value="yes" <?php checked( $tcp_custom_style_activate ); ?>/>&nbsp;<?php _e( 'Activate Styles', 'tcp' ); ?></label>
	<br/>
	<textarea name="tcp_custom_style" id="tcp_custom_style" cols="60" rows="30"><?php
	echo stripslashes( get_option( 'tcp_custom_style', '' ) );
	?></textarea>

	<?php //$templates = tcp_get_custom_templates(); ?>
	<?php do_action( 'tcp_custom_styles_editor' ); ?>
	<?php wp_nonce_field( 'tcp_custom_style_settings' ); ?>
	<?php submit_button( null, 'primary', 'save-custom_styles-settings' ); ?>
</form>
</div><!-- .wrap -->
<?php
	}

	function admin_action() {
		if ( empty( $_POST ) ) return;
		check_admin_referer( 'tcp_custom_style_settings' );
		update_option( 'tcp_custom_style_activate'	, isset( $_POST['tcp_custom_style_activate'] ) );
		update_option( 'tcp_custom_style'			, $_POST['tcp_custom_style'] );
		$this->updated = true;
	}
}

new TCPCustomStyles();
endif; // class_exists check