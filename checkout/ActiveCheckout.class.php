<?php
/**
 * Active checkout
 *
 * Shortcode to display the checkout.
 * It has the emails methods to send orders or any other comunication with customers.
 *
 * @package TheCartPress
 * @subpackage Checkout
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
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ActiveCheckout' ) ) :

require_once( TCP_CLASSES_FOLDER . 'OrderPage.class.php' );

class ActiveCheckout {//shortcode

	function show() {
		global $thecartpress;
		$shoppingCart = TheCartPress::getShoppingCart();
		
		// Get $order_id
		if ( isset( $_REQUEST['order_id'] ) ) {
			$order_id = $_REQUEST['order_id'];
			$shoppingCart->setOrderId( $order_id );
		} else {
			$order_id = $shoppingCart->getOrderId();
		}

		if ( isset( $_REQUEST['tcp_checkout'] ) && $_REQUEST['tcp_checkout'] == 'ok' ) {
			$order_status = Orders::getStatus( $order_id );//We have to check if the order wasn't cancelled
			$cancelled = tcp_get_cancelled_order_status();
			if ( $order_status == $cancelled ) $_REQUEST['tcp_checkout'] = 'ko';
		}

		// Put the check on the cart first. This is because if people try and load the checkout OK page i.e. the URL
		// mysite/shopping_cart_slug/checkout/?tcp_checkout=ok then it would have a silly empty set of fields.
 		if ( $shoppingCart->isEmpty() ) {
			ob_start(); ?>
			<span class="tcp_shopping_cart_empty"><?php _e( 'The cart is empty', 'tcp' ); ?></span>
			<?php tcp_do_template( 'tcp_shopping_cart_empty' ); ?>
			<?php do_action( 'tcp_shopping_cart_empty' ); ?>
			<?php return ob_get_clean();

		} elseif ( isset( $_REQUEST['tcp_checkout'] ) && $_REQUEST['tcp_checkout'] == 'ok' ) {

			// This next function adjusts the stock counts IF the setup flag $stock_management AND $stock_adjust are both true.
			// If stock management is not used or if the stock_adjust is false then stock decrement would be done on checkout
			do_action( 'tcp_completed_ok_stockadjust', $order_id );
			$html = tcp_do_template( 'tcp_checkout_end', false );
			ob_start();
			if ( strlen( $html ) > 0 ) : echo $html;
			else : ?>
				<div class="tcp_payment_area">
					<div class="tcp_order_successfully">
					<?php $checkout_successfully_message = $thecartpress->get_setting( 'checkout_successfully_message', '' );
					if ( strlen( $checkout_successfully_message ) > 0 ) : ?>
						<p><?php echo str_replace ( "\n" , '<p></p>', $checkout_successfully_message ); ?></p>
					<?php else : ?>
						<span class="tcp_checkout_ok"><?php _e( 'The order has been completed successfully.', 'tcp' ); ?></span>
					<?php endif; ?>
					</div><!-- .tcp_payment_area -->
				</div><!-- .tcp_order_successfully -->
			<?php endif; ?>
			<?php do_action( 'tcp_checkout_ok_footer', $shoppingCart ); ?>
			<div>
			<?php OrderPage::show( $order_id, array() ); ?>
			</div>
			<a href="<?php echo add_query_arg( 'action', 'tcp_print_order', add_query_arg( 'order_id', $order_id, admin_url( 'admin-ajax.php' ) ) ); ?>" target="_blank"><?php _e( 'Print', 'tcp' ); ?></a>
			<?php if ( apply_filters( 'tcp_checkout_remove_shopping_cart', true ) ) TheCartPress::removeShoppingCart(); ?>
			<?php do_action( 'tcp_checkout_end', $order_id, true );
			return ob_get_clean();

		} elseif ( isset( $_REQUEST['tcp_checkout'] ) && $_REQUEST['tcp_checkout'] == 'ko' ) {
			$checkout_unsuccessfully_message = tcp_do_template( 'tcp_checkout_end_ko', false );
			ob_start(); ?>
			<div class="tcp_payment_area">
				<div class="tcp_order_unsuccessfully">
				<?php if ( strlen( $checkout_unsuccessfully_message ) > 0 )  {
					//echo str_replace ( "\n" , '<p></p>', $checkout_unsuccessfully_message );
					echo $checkout_unsuccessfully_message;
				} else { ?>
					<span class="tcp_checkout_ko"><?php _e( 'Transaction Error. The order has been canceled', 'tcp' ); ?></span>
				<?php } ?>
				</div><!-- .tcp_order_unsuccessfully -->
				<?php printf( __( 'Retry the <a href="%s">checkout process</a>', 'tcp' ), tcp_get_the_checkout_payment_url() ); ?>
			</div><!-- .tcp_payment_area -->
			<?php $html = ob_get_clean();
			do_action( 'tcp_checkout_end', $order_id, false );
			return $html;

		} else {
			$param = array(
				'validate'	=> true,
				'msg'		=> '',
			);
			$param = apply_filters( 'tcp_checkout_validate_before_enter', $param );
			if ( ! $param['validate'] ) {
				require_once( TCP_SHORTCODES_FOLDER .'ShoppingCartPage.class.php' );
				return TCPShoppingCartPage::show( $param['msg'] );
			} else {
				require_once( TCP_CHECKOUT_FOLDER .'TCPCheckoutManager.class.php' );
				$checkoutManager = new TCPCheckoutManager();
				return $checkoutManager->show();
			}
		}
	}

	/**
	 * To support previous version
	 */
	static function sendMails( $order_id, $additional_msg = '', $only_for_customers = false ) {
		ActiveCheckout::sendOrderMails( $order_id, $additional_msg, true, ! $only_for_customers );
	}

	/**
	 * @since 1.2.3
	 */
	static function sendOrderMails( $order_id, $additional_msg = '', $for_customer = true, $for_merchant = true ) {
		require_once( TCP_CLASSES_FOLDER .'OrderPage.class.php' );
		global $thecartpress;
		$order = Orders::get( $order_id );
		if ( $order ) {
			$from = $thecartpress->get_setting( 'from_email', 'no-response@thecartpress.com' );
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			//$headers .= 'To: ' . $to_customer . "\r\n";
			$headers .= 'From: ' . get_bloginfo( 'name' ) . ' <' . $from . ">\r\n";
			//$headers .= 'Cc: ' . $cc . "\r\n";
			//$headers .= 'Bcc: ' . $bcc . "\r\n";
			$subject  = sprintf( __( 'Order from %s, Order ID: %s', 'tcp' ), htmlentities( get_bloginfo( 'name' ) ), $order_id );
			$subject  = apply_filters( 'tcp_send_order_email_subject', $subject, $order_id );
			$old_value = $thecartpress->getShoppingCart()->getOrderId();
			$_REQUEST['order_id'] = $order_id;
			$thecartpress->getShoppingCart()->setOrderId( $order_id );
			$message  = TCPPrintOrder::printOrder( $order_id );
			$thecartpress->getShoppingCart()->setOrderId( $old_value );
			$message .= tcp_do_template( 'tcp_checkout_email', false );
			$message .= $additional_msg . "\n";
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			//$headers .= 'To: ' . $to . "\r\n";
			$name	  = substr( $from, 0, strpos( $from, '@' ) );
			$headers .= 'From: ' . $name . ' <' . $from . ">\r\n";
			if ( $for_customer ) {
				$customer_email = array();
				if ( strlen( $order->shipping_email ) > 0 ) $customer_email[] = $order->shipping_email;
				if ( strlen( $order->billing_email ) > 0 && $order->shipping_email != $order->billing_email ) $customer_email[] = $order->billing_email;
				$to_customer = implode( ',', $customer_email );
				$message_to_customer = apply_filters( 'tcp_send_order_mail_to_customer_message', $message, $order_id );
//echo $message_to_customer;
				$attachments = apply_filters( 'tcp_order_email_customer_attachments', null );
//var_dump($attachments);exit;
				wp_mail( $to_customer, $subject, $message_to_customer , $headers, $attachments );
				do_action( 'tcp_send_order_mail_to_customer', $to_customer, $subject, $message_to_customer, $headers, $order_id );
			}
			if ( $for_merchant ) {
				$to = $thecartpress->get_setting( 'emails', '' );
				if ( strlen( $to ) ) {
					$message_to_merchant = apply_filters( 'tcp_send_order_mail_to_merchant_message', $message, $order_id );
//echo $message_to_merchant;
					$attachments = apply_filters( 'tcp_order_email_merchant_attachments', null );
//var_dump($attachments);exit;
					wp_mail( $to, $subject, $message_to_merchant, $headers, $attachments );
					do_action( 'tcp_send_order_mail_to_merchant', $to, $subject, $message_to_merchant, $headers, $order_id );
				}
			}
		}
	}
}

add_shortcode( 'tcp_checkout', array( new ActiveCheckout(), 'show' ) );
endif; // class_exists check