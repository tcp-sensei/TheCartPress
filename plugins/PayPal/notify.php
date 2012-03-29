<?php
/**
* PHP-PayPal-IPN Example
*
* This shows a basic example of how to use the IpnListener() PHP class to
* implement a PayPal Instant Payment Notification (IPN) listener script.
*
* For a more in depth tutorial, see my blog post:
* http://www.micahcarrick.com/paypal-ipn-with-php.html
*
* This code is available at github:
* https://github.com/Quixotix/PHP-PayPal-IPN
*
* @package PHP-PayPal-IPN
* @author Micah Carrick
* @copyright (c) 2011 - Micah Carrick
* @license http://opensource.org/licenses/gpl-3.0.html
*
* Modifiyed by Joy Reynolds and TheCartPress team
*/

//ini_set('log_errors', true);
//ini_set('error_log', dirname(__FILE__).'/ipn_errors.log');

$custom		= isset( $_POST['custom'] ) ? $_POST['custom'] : '0-1-CANCELLED-TCPPayPal-0';//Order_id-test_mode-new_status-class-instance
$custom		= explode( '-', $custom );
$order_id	= $custom[0];
$test_mode	= $custom[1] == '1';
$new_status	= $custom[2];
$classname	= $custom[3];
$instance	= $custom[4];
$transaction_id = isset( $_POST['txn_id'] ) ? $_POST['txn_id'] : '';

$wordpress_path = dirname( dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) ) . '/';
include_once( $wordpress_path . 'wp-config.php' );			//loads WordPress
$thecartpress_path = dirname( dirname( dirname( __FILE__ ) ) ) . '/';
require_once( $thecartpress_path . 'daos/Orders.class.php');
require_once( $thecartpress_path . 'checkout/ActiveCheckout.class.php');

$cancelled_status = tcp_get_cancelled_order_status();
$completed_status = tcp_get_completed_order_status();

if ( isset( $_REQUEST['tcp_checkout'] ) && $_REQUEST['tcp_checkout'] == 'ko' ) {
	Orders::editStatus( $order_id, $cancelled_status, $transaction_id, __( 'Customer cancel at PayPal', 'tcp' ) );
	$redirect = add_query_arg( 'tcp_checkout', 'ko', get_permalink( tcp_get_current_id( get_option( 'tcp_checkout_page_id' ), 'page' ) ) );
?>
<html><head><title>Canceling Payment</title>
<script language="javascript">
//<!--
window.location="<?php echo $redirect;?>";
//-->
</script>
</head>
<body><noscript><meta http-equiv="refresh" content="1;url=<?php echo $redirect;?>"></noscript>
<p>Canceling your order. Please wait...</p>
</body></html>
<?php
} else {
	$data = tcp_get_payment_plugin_data( $classname, $instance );

	include( 'ipnlistener.class.php' );
	$listener = new IpnListener();
	$listener->use_sandbox = $test_mode == 1;
	//To post over standard HTTP connection, use:
	//$listener->use_ssl = false;
	//To post using the fsockopen() function rather than cURL, use:
	$listener->use_curl = false;

	try {
		$listener->requirePostMethod();
		$verified = $listener->processIpn();
	} catch (Exception $e) {
		$verified = false;
		Orders::editStatus( $order_id, Orders::$ORDER_SUSPENDED, $transaction_id, 'No validation. Error in connection.' );
		exit(0);
	}
	if ( $verified ) {
//  	Once you have a verified IPN you need to do a few more checks on the POST
//		fields--typically against data you stored in your database during when the
//		end user made a purchase (such as in the "success" page on a web payments
//		standard button). The fields PayPal recommends checking are:
//		1. Check the $_POST['payment_status'] is "Completed"
//		2. Check that $_POST['txn_id'] has not been previously processed
//		3. Check that $_POST['receiver_email'] is your Primary PayPal email
//		4. Check that $_POST['payment_amount'] and $_POST['payment_currency']
//		are correct
//		Since implementations on this varies, I will leave these checks out of this
//		example and just send an email using the getTextReport() method to get all
//		of the details about the IPN.
		if ( $_POST['receiver_email'] == $data['business'] ) {
			$order_row = Orders::getOrderByTransactionId( $classname, $transaction_id );
			$additional = "\npayment_status=" . $_POST['payment_status'];
			switch ( $_POST['payment_status'] ) {
				case 'Completed':
				case 'Canceled_Reversal':
				case 'Processed': //should check price, but with profile options, we can't know it, could check currency
					$comment = "\nmc_gross=" . $_POST['mc_gross'] . ' ' . $_POST['mc_currency'];
					$comment .= "\nmc_shipping=" . $_POST['mc_shipping'] . ', tax=' . $_POST['tax'];
					if ( $_POST['receipt_id'] ) $additional .= "\nPayPal Receipt ID: " . $_POST['receipt_id'];
					if ( $_POST['memo'] ) $additional .= "\nCustomer comment: " . $_POST['memo'];
					//if ( Orders::isDownloadable( $order_id ) )
					//	Orders::editStatus( $order_id, $completed_status, $transaction_id, $comment . $additional );
					//else
						Orders::editStatus( $order_id, $new_status, $transaction_id, $comment . $additional );
					break;
				case 'Refunded':
				case 'Reversed':
					$additional = $_POST['payment_status']. ': ' . $_POST['reason_code'];
					Orders::editStatus( $order_id, Orders::$ORDER_SUSPENDED, $transaction_id, $comment . $additional );
					break;
				case 'Expired':
				case 'Failed':
					Orders::editStatus( $order_id, Orders::$ORDER_PROCESSING, $transaction_id, $_POST['payment_status'] );
					require_once( dirname( dirname( dirname( __FILE__ ) ) ) . '/checkout/ActiveCheckout.class.php' );
					break;
				case 'Pending':
					$additional .= "\npending_reason=" . $_POST['pending_reason'];
					Orders::editStatus( $order_id, Orders::$ORDER_PENDING, $transaction_id, $comment . $additional );
					break;
				case 'Expired':
				case 'Failed':
				case 'Denied':
				case 'Voided':
					Orders::editStatus( $order_id, $cancelled_status, $transaction_id, $comment . $additional );
					break;
				default :
					break;
			}
			ActiveCheckout::sendMails( $order_id, $additional );
			//mail( debug_email, 'Verified IPN', $listener->getTextReport() );
		} else {
			$additional = $_POST['payment_status']. ': receiver_email is wrong (' . $_POST['receiver_email'] . ')';
			Orders::editStatus( $order_id, Orders::$ORDER_SUSPENDED, $transaction_id, $additional );
		}
	} else {
		//An Invalid IPN *may* be caused by a fraudulent transaction attempt. It's
		//a good idea to have a developer or sys admin manually investigate any
		//invalid IPN.
		//save for further investigation?
		//mail( debug_email, 'Invalid IPN', $listener->getTextReport() );
	}
}
?>
