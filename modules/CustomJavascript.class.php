<?php
/**
 * Custom Javascript
 *
 * Allows to add a custom Javascript editor to add javascript code to the header of the site
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

if ( ! class_exists( 'TCPCustomJavascript' ) ) {

class TCPCustomJavascript {

	function __construct() {
		add_action( 'tcp_admin_menu'	, array( $this, 'tcp_admin_menu' ), 50 );
		add_action( 'wp_head'			, array( $this, 'wp_head' ) );
		global $tcp_miranda;
		if ( $tcp_miranda ) $tcp_miranda->add_item( 'settings', 'default_settings', __( 'Custom Javascript', 'tcp' ), false, array( 'TCPCustomJavascript', __FILE__ ), plugins_url( 'thecartpress/images/miranda/customjavascript_settings_48.png' ) );
	}

	function wp_head() {
		if ( ! get_option( 'tcp_custom_javascript_activate', false ) ) return;
		$custom_javascript = stripslashes( get_option( 'tcp_custom_javascript', '' ) );
		if ( strlen( $custom_javascript ) > 0 ) : ?>
<script>
	<?php echo $custom_javascript; ?>
</script>
		<?php endif;
	}

	function tcp_admin_menu() {
		if ( ! current_user_can( 'tcp_edit_settings' ) ) return;
		$base = thecartpress()->get_base_appearance();
		$page = add_submenu_page( $base, __( 'Custom JavaScript', 'tcp' ), __( 'Custom JavaScript', 'tcp' ), 'tcp_edit_settings', 'custom_javascript_settings', array( &$this, 'admin_page' ) );
		add_action( "load-$page", array( &$this, 'admin_load' ) );
		add_action( "load-$page", array( &$this, 'admin_action' ) );
	}

	function admin_load() {
		get_current_screen()->add_help_tab( array(
			'id'	  => 'overview',
			'title'   => __( 'Overview' ),
			'content' =>
				'<p>' . __( 'You can add Custom javascript.', 'tcp' ) . '</p>'
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
	<?php screen_icon( 'tcp-custom-javascript' ); ?><h2><?php _e( 'Custom Javascript', 'tcp' ); ?></h2>

<?php if ( ! empty( $this->updated ) ) : ?>
	<div id="message" class="updated">
		<p><?php _e( 'Settings updated', 'tcp' ); ?></p>
	</div>
<?php endif; ?>

<div class="clear"></div>

<form method="post">
	<?php $tcp_custom_javascript_activate = get_option( 'tcp_custom_javascript_activate', false ); ?>
	<label for="tcp_custom_javascript_activate"><input type="checkbox" name="tcp_custom_javascript_activate" id="tcp_custom_javascript_activate" value="yes" <?php checked( $tcp_custom_javascript_activate ); ?>/>&nbsp;<?php _e( 'Activate next Javascript', 'tcp' ); ?></label>
	<br/>
	<textarea name="tcp_custom_javascript" id="tcp_custom_javascript" cols="60" rows="30">
	<?php echo stripslashes( get_option( 'tcp_custom_javascript', '' ) ); ?>
	</textarea>

	<?php $templates = tcp_get_custom_templates(); ?>

	<?php wp_nonce_field( 'tcp_custom_javascript_settings' ); ?>

	<?php submit_button( null, 'primary', 'save-custom_javascript-settings' ); ?>
</form>
</div><!-- .wrap -->
<?php
	}

	function admin_action() {
		if ( empty( $_POST ) ) return;
		check_admin_referer( 'tcp_custom_javascript_settings' );
		update_option( 'tcp_custom_javascript_activate', isset( $_POST['tcp_custom_javascript_activate'] ) );
		update_option( 'tcp_custom_javascript', trim( $_POST['tcp_custom_javascript'] ) );
		$this->updated = true;
	}
}

new TCPCustomJavascript();
} // class_exists check