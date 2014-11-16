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

if ( ! class_exists( 'TCPEMarketingSettings' ) ) {

class TCPEMarketingSettings {

	private $updated = false;

	function __construct() {
		add_action( 'tcp_admin_menu', array( $this, 'tcp_admin_menu' ) );
	}

	function tcp_admin_menu() {
		if ( ! current_user_can( 'tcp_edit_settings' ) ) return;
		$base = thecartpress()->get_base_settings();
		$page = add_submenu_page( $base, __( 'eMarketing Tools Settings', 'tcp' ), __( 'eMarketing Tools', 'tcp' ), 'tcp_edit_settings', 'emarketing_tools_settings', array( &$this, 'admin_page' ) );
		add_action( "load-$page", array( $this, 'admin_load' ) );
		add_action( "load-$page", array( $this, 'admin_action' ) );
	}

	function admin_load() {
		get_current_screen()->add_help_tab( array(
			'id'	  => 'overview',
			'title'   => __( 'Overview' ),
			'content' =>
				'<p>' . __( 'You can customize eMarketing Tools (BETA).', 'tcp' ) . '</p>'
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
	<?php screen_icon(); ?><h2><?php _e( 'eMarketing Tools', 'tcp' ); ?></h2>

<?php if ( !empty( $this->updated ) ) : ?>
	<div id="message" class="updated">
	<p><?php _e( 'Settings updated', 'tcp' ); ?></p>
	</div>
<?php endif; ?>

<?php global $thecartpress;
$search_engine_activated = $thecartpress->get_setting( 'search_engine_activated', false ); ?>

<form method="post" action="">
<table class="form-table">
<tbody>
<tr valign="top">
	<th scope="row">
	<label for="search_engine_activated"><?php _e( 'Search engine activated', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="checkbox" id="search_engine_activated" name="search_engine_activated" value="yes" <?php checked( $search_engine_activated, true ); ?> />
	</td>
</tr>

</tbody>
</table>

<?php wp_nonce_field( 'tcp_emarketing_tools_settings' ); ?>
<?php submit_button( null, 'primary', 'save-emarketing_tools-settings' ); ?>
</form>
</div>
<?php
	}

	function admin_action() {
		if ( empty( $_POST ) ) return;
		check_admin_referer( 'tcp_emarketing_tools_settings' );
		$settings = get_option( 'tcp_settings' );
		$settings['search_engine_activated'] = isset( $_POST['search_engine_activated'] ) ? $_POST['search_engine_activated'] : false;
		update_option( 'tcp_settings', $settings );
		$this->updated = true;
		global $thecartpress;
		$thecartpress->load_settings();
	}
}

new TCPEMarketingSettings();
} // class_exists check