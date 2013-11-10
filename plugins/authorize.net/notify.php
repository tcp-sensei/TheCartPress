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

$wordpress_path = dirname( dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) ) . '/';
include_once( $wordpress_path . 'wp-config.php' );
include_once( $wordpress_path . 'wp-includes/wp-db.php' );

$thecartpress_path = dirname( dirname( dirname( __FILE__ ) ) )  . '/';
require_once( $thecartpress_path . 'daos/Orders.class.php' );
require_once( $thecartpress_path . 'checkout/ActiveCheckout.class.php' );

$plugin_path = dirname( dirname( dirname( __FILE__ ) ) )  . '/classes/TCP_Plugin.class.php';
require_once( $plugin_path );
$instance = $_REQUEST['instance'];
$data = tcp_get_payment_plugin_data( 'TCPAuthorizeNet', $instance, $order_id );

$api_login_id	= $data['api_login_id'];
$md5_hash		= $data['md5_hash'];
//$x_login		= $_REQUEST['x_login'];
$x_md5_hash		= strtolower( $_REQUEST['x_MD5_Hash'] );
$x_amount		= $_REQUEST['x_amount'];
$x_md5_hash		= strtolower( $_REQUEST['x_MD5_Hash'] );
$x_trans_id		= isset( $_REQUEST['x_trans_id'] ) ? $_REQUEST['x_trans_id'] : 'no id';
$order_id		= $_REQUEST['order_id'];
$fingerprint	= strtolower( md5( $md5_hash . $api_login_id . $x_trans_id . $x_amount ) );

$cancelled_status = tcp_get_cancelled_order_status();
$error = '';
if ( $fingerprint == $x_md5_hash ) {
	$new_status = $_REQUEST['new_status'];
	$response_code = isset( $_REQUEST['x_response_code'] ) ? $_REQUEST['x_response_code'] : 0;//1 ->OK, 2->declined, else->error
	if ( $response_code == 1 ) {
		Orders::editStatus( $order_id, $new_status, $x_trans_id );
		ActiveCheckout::sendMails( $order_id );
	} else {
		$response_reason_text = isset( $_REQUEST['x_response_reason_text'] ) ? $_REQUEST['x_response_reason_text'] : 'no reason';
		$response_reason_code = isset( $_REQUEST['x_response_reason_code'] ) ? $_REQUEST['x_response_reason_code'] : 0;
		$error = __( 'Error from authorize.net: ', 'tcp' ) . $response_reason_text . '(' . $response_reason_code . ')';
		Orders::editStatus( $order_id, $cancelled_status, $x_trans_id, $error );
		ActiveCheckout::sendMails( $order_id, $error );
	}
	$redirect = tcp_get_the_checkout_ok_url( $order_id );
} else {
	$error = __( 'Error notifiying Authorize.net payment', 'tcp' );
	$error .= ' fp=' . $fingerprint . ', md5=' . $x_md5_hash;
	Orders::editStatus( $order_id, $cancelled_status, $x_trans_id, $error );
	$redirect = tcp_get_the_checkout_ko_url( $order_id );
} ?>
<html>
<head>
<title>Processing Payment</title>
<script language="javascript">
//<!--
window.location="<?php echo $redirect;?>";
//-->
</script>
</head>
<body>
<noscript><meta http-equiv="refresh" content="1;url=<?php echo $redirect;?>"></noscript>
<p>Processing your payment. Please wait...</p>
</body>
</html>