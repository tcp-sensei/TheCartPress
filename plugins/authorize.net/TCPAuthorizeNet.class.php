<?php
/**
 * This file is part of TheCartPress.
 * 
 * TheCartPress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TheCartPress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TheCartPress.  If not, see <http://www.gnu.org/licenses/>.
 */

class TCPAuthorizeNet extends TCP_Plugin {

	function getTitle() {
		return '<img src="http://www.authorize.net/resources/images/authorizenet_logo.gif" height="32px" />';
	}

	function getDescription() {
		return 'authorize.net payment method.<br>Author: <a href="http://thecartpress.com">TheCartPress team</a>';
	}

	function getName() {
		return 'Authorize.net';
	}

	function getCheckoutMethodLabel( $instance, $shippingCountry, $shoppingCart ) {
		$data = tcp_get_payment_plugin_data( 'TCPAuthorizeNet', $instance );
		$title = isset( $data['title'] ) ? $data['title'] : $this->getTitle();
		return $title;
	}

	function showEditFields( $data ) {?>
		<tr valign="top">
		<th scope="row">
			<label for="test_mode"><?php _e( 'Test mode', 'tcp' );?>:</label>
		</th><td>
			<input type="checkbox" id="test_mode" name="test_mode" value="yes" <?php checked( true , isset( $data['test_mode'] ) ? $data['test_mode'] : false );?> />
		</td></tr>
		<tr valign="top">
		<th scope="row">
			<label for="api_login_id"><?php _e( 'API Login id', 'tcp' );?>:</label>
		</th><td>
			<input type="text" id="api_login_id" name="api_login_id" size="20" maxlength="20" value="<?php echo isset( $data['api_login_id'] ) ? $data['api_login_id'] : '';?>" />
		</td></tr>
		<tr valign="top">
		<th scope="row">
			<label for="transaction_key"><?php _e( 'Transaction key', 'tcp' );?>:</label>
		</th><td>
			<input type="text" id="transaction_key" name="transaction_key" size="20" maxlength="20" value="<?php echo isset( $data['transaction_key'] ) ? $data['transaction_key'] : '';?>" />
		</td></tr>
		<tr valign="top">
		<th scope="row">
			<label for="md5_hash"><?php _e( 'MD5 Hash', 'tcp' );?>:</label>
		</th><td>
			<input type="text" id="md5_hash" name="md5_hash" value="<?php echo isset( $data['md5_hash'] ) ? $data['md5_hash'] : '';?>" />
		</td></tr>
		
		<tr valign="top">
		<th scope="row">
			<label><?php _e( 'Response URL', 'tcp' ); ?>:</label>
		</th><td>
			<?php echo plugins_url( 'thecartpress/plugins/authorize.net/notify.php' ); ?>
		</td></tr>
		
		<tr valign="top">
		<th scope="row">
			<label for=""><?php _e( 'Sandbox account', 'tcp' );?>:</label>
		</th><td>
			<a href="https://sandbox.authorize.net/" target="_blank">https://sandbox.authorize.net/</a>
		</td></tr><?php
	}

	function saveEditFields( $data ) {
		$data['api_login_id']		= isset( $_REQUEST['api_login_id'] ) ? $_REQUEST['api_login_id'] : '';
		$data['transaction_key']	= isset( $_REQUEST['transaction_key'] ) ? $_REQUEST['transaction_key'] : '';
		$data['test_mode']			= isset( $_REQUEST['test_mode'] ) ? $_REQUEST['test_mode'] == 'yes' : false;
		$data['md5_hash']			= isset( $_REQUEST['md5_hash'] ) ? $_REQUEST['md5_hash'] : '';
		return $data;
	}

	function showPayForm( $instance, $shippingCountry, $shoppingCart, $order_id ) {
		$data = tcp_get_payment_plugin_data( get_class( $this ), $instance );
		$api_login_id		= $data['api_login_id'];
		$transaction_key	= $data['transaction_key'];
		$new_status			= $data['new_status'];
		$test_mode			= isset( $data['test_mode'] ) ? $data['test_mode'] : true;
		//$md5_hash			= isset( $data['md5_hash'] ) ? $data['md5_hash'] : true;
		require_once( TCP_DAOS_FOLDER . 'Orders.class.php' );
		$paymentAmount		= Orders::getTotal( $order_id );
		$amount				= number_format( $paymentAmount, 2, '.', '' );
		$notify_url			= plugins_url( 'thecartpress/plugins/authorize.net/notify.php' );//?orderid=' . $order_id . '&status=' . $new_status );
		$order				= Orders::get( $order_id );
		require_once dirname( __FILE__ ) . '/anet_php_sdk/AuthorizeNet.php'; // Include the SDK you downloaded in Step 2
		$fp_timestamp	= time();
		$fp_sequence	= $order_id . time(); // Enter an invoice or other unique number.
		$fingerprint	= AuthorizeNetSIM_Form::getFingerprint( $api_login_id, $transaction_key, $amount, $fp_sequence, $fp_timestamp );

		$fields = array(
			'x_address'				=> $order->billing_street,
			'x_amount'				=> $amount,
//			'x_background_url'		=> '',
//			'x_card_num'			=> '',
			'x_city'				=> $order->billing_city,
//			'x_color_background'	=> '',
//			'x_color_link'			=> '',
//			'x_color_text'			=> '',
			'x_company'				=> $order->billing_company,
			'x_country'				=> $order->billing_country_id,
//			'x_cust_id'				=> '',
			'x_customer_ip'			=> $_SERVER['REMOTE_ADDR'],
//			'x_description'			=> '',
			'x_delim_data'			=> false,
//			'x_duplicate_window'	=> '',
//			'x_duty'				=> '',
			'x_email'				=> isset( $thecartpress->settings['emails'] ) ? $thecartpress->settings['emails'] : '',
			'x_email_customer'		=> $order->billing_email,
			'x_fax'					=> $order->billing_fax,
			'x_first_name'			=> $order->billing_firstname,
//			'x_footer_email_receipt'		=> '',
//			'x_footer_html_payment_form'	=> '',
//			'x_footer_html_receipt'			=> '',
			'x_fp_hash'				=> $fingerprint,
			'x_fp_sequence'			=> $fp_sequence,
			'x_fp_timestamp'		=> $fp_timestamp,
//			'x_freight'				=> '',
//			'x_header_email_receipt'		=> '',
//			'x_header_html_payment_form'	=> '',
//			'x_header_html_receipt'	=> '',
			'x_invoice_num'			=> $order_id,
			'x_last_name'			=> $order->billing_lastname,
//			'x_line_item'			=> '',
			'x_login'				=> $api_login_id,
//			'x_logo_url'			=> '',
			'x_method'				=> 'cc',
			'x_phone'				=> $order->billing_telephone_1,
//			'x_po_num'				=> '',
			'x_receipt_link_method'	=> 'LINK',
			'x_receipt_link_text'	=> __( 'Returns to the eCommerce', 'tcp'),
			'x_receipt_link_url'	=> home_url(),
//			'x_recurring_billing'	=> '',
			'x_relay_response'		=> 'true',
			'x_relay_url'			=> $notify_url,
//			'x_rename'				=> '',
			'x_ship_to_address'		=> $order->shipping_street,
			'x_ship_to_company'		=> $order->shipping_company,
			'x_ship_to_country'		=> $order->shipping_country_id,
			'x_ship_to_city'		=> $order->shipping_city,
			'x_ship_to_first_name'	=> $order->shipping_firstname,
			'x_ship_to_last_name'	=> $order->shipping_lastname,
			'x_ship_to_state'		=> $order->shipping_region_id,
			'x_ship_to_zip'			=> $order->shipping_postcode,
			'x_show_form'			=> 'payment_form',
			'x_state'				=> $order->billing_region_id,
//			'x_tax'					=> '',
//			'x_tax_exempt'			=> '',
			'x_test_request'		=> $test_mode,
//			'x_trans_id'			=> '',
//			'x_type'				=> '',
//			'x_version'				=> '',
			'x_zip'					=> $order->billing_postcode,
		);
		$form = new AuthorizeNetSIM_Form( $fields );
		if ( $test_mode ) : ?>
			<form method="post" action="https://test.authorize.net/gateway/transact.dll">
		<?php else : ?>
			<form method="post" action="https://secure.authorize.net/gateway/transact.dll">
		<?php endif;
		echo $form->getHiddenFieldString();?>
		<input type="hidden" name="order_id" value="<?php echo $order_id;?>"/>
		<input type="hidden" name="new_status" value="<?php echo $new_status;?>"/>
		<input type="hidden" name="instance" value="<?php echo $instance;?>"/>
		<input type="submit" value="secure payment" />
		</form><?php
	}
}
?>
