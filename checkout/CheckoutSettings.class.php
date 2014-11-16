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

if ( ! class_exists( 'TCPCheckoutSettings' ) ) {

class TCPCheckoutSettings {

	private $updated = false;

	function __construct() {
		add_action( 'tcp_admin_menu', array( $this, 'tcp_admin_menu' ) );
		global $tcp_miranda;
		if ( $tcp_miranda ) $tcp_miranda->add_item( 'settings', 'default_settings', __( 'Checkout', 'tcp' ), false, array( 'TCPCheckoutSettings', __FILE__ ), plugins_url( 'thecartpress/images/miranda/checkout_settings_48.png' ) );
	}

	function tcp_admin_menu() {
		if ( ! current_user_can( 'tcp_edit_settings' ) ) return;
		$base = thecartpress()->get_base_settings();
		$page = add_submenu_page( $base, __( 'Checkout Settings', 'tcp' ), __( 'Checkout', 'tcp' ), 'tcp_edit_settings', 'checkout_settings', array( &$this, 'admin_page' ) );
		add_action( "load-$page", array( $this, 'admin_load' ) );
		add_action( "load-$page", array( $this, 'admin_action' ) );
	}

	function admin_load() {
		get_current_screen()->add_help_tab( array(
			'id'      => 'overview',
			'title'   => __( 'Overview' ),
			'content' => '<p>' . __( 'You can customize the checkout process.', 'tcp' ) . '</p>'
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
	<?php screen_icon( 'tcp-checkout' ); ?><h2><?php _e( 'Checkout Settings', 'tcp' ); ?></h2>

<?php if ( !empty( $this->updated ) ) : ?>
	<div id="message" class="updated">
	<p><?php _e( 'Settings updated', 'tcp' ); ?></p>
	</div>
<?php endif; ?>

<?php global $thecartpress;
$https_checkout		= $thecartpress->get_setting( 'https_checkout', false );
$user_registration	= $thecartpress->get_setting( 'user_registration', false );
$emails				= $thecartpress->get_setting( 'emails' );
$from_email			= $thecartpress->get_setting( 'from_email' );
$send_email			= $thecartpress->get_setting( 'send_email' );
$send_email_customer= $thecartpress->get_setting( 'send_email_customer', $send_email );
$legal_notice		= $thecartpress->get_setting( 'legal_notice' );
tcp_register_string( 'TheCartPress', 'legal notice', $legal_notice );
$checkout_successfully_message	= $thecartpress->get_setting( 'checkout_successfully_message', __( 'The order has been completed successfully', 'tcp' ) ); ?>
<form method="post" action="">
<table class="form-table">
<tbody>
<!--<tr valign="top">
	<th scope="row">
		<label for="https_checkout"><?php _e( 'HTTPS process', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="checkbox" id="https_checkout" name="https_checkout" value="yes" <?php checked( $https_checkout ); ?> />
		<p class="description"><?php _e( 'Allows to make the checkout process under https.', 'tcp' ); ?></p>
	</td>
</tr>-->
<tr valign="top">
	<th scope="row">
		<label for="user_registration"><?php _e( 'User registration required', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="checkbox" id="user_registration" name="user_registration" value="yes" <?php checked( $user_registration ); ?> />
		<p class="description"><?php _e( 'Indicates if the clients should be or not registered to buy.', 'tcp' ); ?></p>
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="emails"><?php _e( '@mails to send orders', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="text" id="emails" name="emails" value="<?php echo $emails; ?>" size="40" maxlength="2550" />
		<span class="description"><?php _e( 'Comma (,) separated mails', 'tcp' ); ?></span>
		<p class="description"><?php _e( 'These emails will receive orders notifications.', 'tcp' ); ?></p>
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="from_email"><?php _e( 'From email', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="text" id="from_email" name="from_email" value="<?php echo $from_email; ?>" size="40" maxlength="255" />
		<p class="description"><?php _e( 'Host email. If not set, The emails will be sent to the customer from no-response@thecartpress.com', 'tcp' ); ?></p>		
	</td>
</tr>
<tr valign="top">
	<th scope="row">
		<label for="send_email"><?php _e( 'Send Purchase eMail to merchant', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="checkbox" id="send_email" name="send_email" value="yes" <?php checked( $send_email ); ?> />
		<p class="description"><?php _e( 'Allows to send an email to Merchant when customers click on Purchase button.', 'tcp' ); ?></p>
	</td>
</tr>
<tr valign="top">
	<th scope="row">
		<label for="send_email"><?php _e( 'Send Purchase eMail to customers', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="checkbox" id="send_email_customer" name="send_email_customer" value="yes" <?php checked( $send_email_customer ); ?> />
		<p class="description"><?php _e( 'Allows to send an email to Customer when customers click on Purchase button.', 'tcp' ); ?></p>
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="legal_notice"><?php _e( 'Checkout notice', 'tcp' ); ?></label>
	</th>
	<td>
		<textarea id="legal_notice" name="legal_notice" cols="40" rows="5" maxlength="1020"><?php echo $legal_notice; ?></textarea>
		<p class="description"><?php _e( 'If the checkout notice is blank, the Checkout page will try to use the Notice class called "tcp_checkout_notice"', 'tcp' ); ?></p>
		<p class="description"><?php _e( 'The Notice class allows to create a multilingual notice', 'tcp' ); ?></p>
		<p class="description"><?php _e( 'If the checkout notice is blank and no Notice class is assigned, then the Checkout page will not show the "Accept conditions" check.', 'tcp' ); ?></p>
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for=""><?php _e( 'Checkout successfully message', 'tcp' ); ?></label>
	</th>
	<td>
		<textarea id="checkout_successfully_message" name="checkout_successfully_message" cols="40" rows="5" maxlength="1020"><?php echo $checkout_successfully_message; ?></textarea>
		<p class="description"><?php _e( 'This text will show at the end of the checkout process.', 'tcp' ); ?></p>
		<p class="description"><?php _e( 'If this messages is blank, the Checkout page will try to use the Notice class called "tcp_checkout_end"', 'tcp' ); ?></p>	
	</td>
</tr>
<?php do_action( 'tcp_checkout_settings' ); ?>
</tbody>
</table>
<?php wp_nonce_field( 'tcp_checkout_settings' ); ?>
<?php submit_button( null, 'primary', 'save-checkout-settings' ); ?>
</form>
</div>
<?php
	}

	function admin_action() {
		if ( empty( $_POST ) ) return;
		check_admin_referer( 'tcp_checkout_settings' );
		$settings = get_option( 'tcp_settings' );
		$settings['https_checkout'] = isset( $_POST['https_checkout'] );
		$settings['user_registration'] = isset( $_POST['user_registration'] );// ? $_POST['user_registration'] == 'yes' : false;
		$settings['emails']			= $_POST['emails'];
		$settings['from_email']		= $_POST['from_email'];
		$settings['send_email']		= isset( $_POST['send_email'] );
		$settings['send_email_customer'] = isset( $_POST['send_email_customer'] );
		$settings['legal_notice']	= $_POST['legal_notice'];
		$settings['checkout_successfully_message']	= $_POST['checkout_successfully_message'];
		$settings = apply_filters( 'tcp_checkout_settings_action', $settings );
		update_option( 'tcp_settings', $settings );
		$this->updated = true;
		global $thecartpress;
		$thecartpress->load_settings();
	}
}

new TCPCheckoutSettings();
} // class_exists check