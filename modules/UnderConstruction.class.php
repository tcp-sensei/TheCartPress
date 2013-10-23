<?php
/**
 * Under Construction
 *
 * Allows tp close the site displaying a Page, for Underconstrucction sites, Exclusive sales, etc.
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

if ( ! class_exists( 'TCPUnderConstruction' ) ) {

class TCPUnderConstruction {
	function __construct() {
		add_action( 'tcp_admin_menu'	, array( $this, 'tcp_admin_menu' ), 90 );
		add_action( 'template_redirect'	, array( $this, 'template_redirect' ) );
	}

	function tcp_admin_menu() {
		if ( ! current_user_can( 'tcp_edit_settings' ) ) return;
		$base = thecartpress()->get_base_tools();
		$page = add_submenu_page( $base, __( 'Under Construction', 'tcp' ), __( 'Under Construction', 'tcp' ), 'tcp_edit_settings', 'under_construction_settings', array( &$this, 'admin_page' ) );
		add_action( "load-$page", array( $this, 'admin_load' ) );
		add_action( "load-$page", array( $this, 'admin_action' ) );
	}

	function admin_load() {
		get_current_screen()->add_help_tab( array(
			'id'	  => 'overview',
			'title'   => __( 'Overview' ),
			'content' =>
				'<p>' . __( 'You can set you site Under Contruction.', 'tcp' ) . '</p>'
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
	<?php screen_icon( 'tcp-underconstruction' ); ?><h2><?php _e( 'Under Construction', 'tcp' ); ?></h2>

<?php if ( !empty( $this->updated ) ) : ?>
	<div id="message" class="updated">
		<p><?php _e( 'Settings updated', 'tcp' ); ?></p>
	</div>
<?php endif; ?>

<?php global $thecartpress;
$activated			= $thecartpress->get_setting( 'under_construction_activated', false );
$redirect_to		= $thecartpress->get_setting( 'under_construction_redirect_to', 'login' );
$url				= $thecartpress->get_setting( 'under_construction_url', '' );
$page_id_selected	= $thecartpress->get_setting( 'under_construction_page_id', '' );
$time				= $thecartpress->get_setting( 'under_construction_time', '' );
?>
<form method="post" action="">
<div class="postbox">
	<table class="form-table">
	<tbody>
	<tr valign="top">
		<th scope="row">
			<label for="activated"><?php _e( 'Activated', 'tcp' ); ?></label>
		</th>
		<td>
			<input type="checkbox" id="activated" name="activated" value="yes" <?php checked( $activated ); ?> />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="redirect_to"><?php _e( 'Redirect To', 'tcp' ); ?></label>
		</th>
		<td>
			<input type="radio" id="redirect_to" name="redirect_to" value="login" <?php checked( 'login', $redirect_to ); ?> />&nbsp;<label for="redirect_to"><?php _e( 'Login', 'tcp' ); ?></label>
			<input type="radio" id="redirect_to_url" name="redirect_to" value="url" <?php checked( 'url', $redirect_to ); ?> />&nbsp;<label for="redirect_to_url"><?php _e( 'Url', 'tcp' ); ?></label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="page_id"><?php _e( 'Page', 'tcp' ); ?></label>
		</th>
		<td>
			<?php $pages = get_posts( array(
			 	'post_type'		=> 'page',
			 	'numberposts'	=> -1,
				'post_status'	=> 'publish',
				'fields'		=> 'ids',
			 ) ); ?>
			<select name="page_id" id="page_id">
				<option value="0" <?php selected( 0, $page_id_selected ); ?>><?php _e( 'None', 'tcp' ); ?></option>
			<?php foreach( $pages as $page_id ) { ?>
				<option value="<?php echo $page_id; ?>" <?php selected( $page_id, $page_id_selected ); ?>><?php echo get_the_title( $page_id ); ?></option>
			<?php } ?>
			</select>
			<p><?php _e( 'Choose a page to display if the Under-Construcction is activated', 'tcp' ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="url"><?php _e( 'URL', 'tcp' ); ?></label>
		</th>
		<td>
			 <?php echo site_url(); ?>/<input type="text" id="url" name="url" value="<?php echo $url; ?>" maxlength="255" size="20" />
			 <p><?php _e( 'Or type an url. The URL will be use if Page is set to "none".', 'tcp' ); ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="tcp_time"><?php _e( 'Date/time to Launch', 'tcp' ); ?></label>
		</th>
		<td>
			<label><?php _e( 'Time', 'tcp' ); ?>: <input type="text" id="tcp_time" name="tcp_time" value="<?php echo date( 'Y-m-d H:i', (int)$time ); ?>" size="20" /></label> <?php _e( 'Format YYYY-MM-DD hh:mm', 'tcp' ); ?>
		</td>
	</tr>
	</tbody>
	</table>
</div><!-- .postbox -->
	<?php wp_nonce_field( 'tcp_under_construction_settings' ); ?>
	<?php submit_button( null, 'primary', 'save-under_construction-settings' ); ?>
</form>
</div>
<?php
	}

	function admin_action() {
		if ( empty( $_POST ) ) return;
		check_admin_referer( 'tcp_under_construction_settings' );
		$settings = get_option( 'tcp_settings' );
		$settings['under_construction_activated']		= isset( $_POST['activated'] );
		$settings['under_construction_redirect_to']		= isset( $_POST['redirect_to'] ) ? $_POST['redirect_to'] : 'login';
		$settings['under_construction_url']				= isset( $_POST['url'] ) ? $_POST['url'] : '';
		$settings['under_construction_page_id']			= isset( $_POST['page_id'] ) ? $_POST['page_id'] : '';
		//Clock
		$settings['under_construction_time']			= isset( $_POST['tcp_time'] ) ? strtotime( $_POST['tcp_time'] ) : '';

		update_option( 'tcp_settings', $settings );
		$this->updated = true;
		global $thecartpress;
		$thecartpress->load_settings();
	}

	function template_redirect() {
		global $userdata;

		if ( ! empty( $userdata->ID ) ) return;
		global $thecartpress;
		$activated = $thecartpress->get_setting( 'under_construction_activated', false );
		if ( $activated ) {
			$current_url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];		
			$redirect_to = $thecartpress->get_setting( 'under_construction_redirect_to', 'login' );
			if ( $redirect_to == 'login' ) {
				$url = wp_login_url();
			} else {
				$page_id = $thecartpress->get_setting( 'under_construction_page_id', 0 );
				if ( $page_id == 0 ) {
					$url = site_url() . '/' . $thecartpress->get_setting( 'under_construction_url', '' );	
				} else {
					$url = get_permalink( $page_id );
					global $post;
					if ( $post->ID != $page_id ) {
						$children = get_posts( array( 
							'post_parent'	=> $page_id,
							'post_type'		=> $post->post_type, 
							'numberposts'	=> -1,
							'post_status'	=> 'publish',
							'fields'		=> 'ids',
						) );
						if ( in_array( $post->ID, $children ) ) {
							$url = get_permalink( $post->ID );
						}
					}
				}
			}
			if ( $current_url != $url && $current_url != $url . '/' ) {
				tcp_redirect_302( $url );
			}
		}
	}
}

new TCPUnderConstruction();
} // class_exists check