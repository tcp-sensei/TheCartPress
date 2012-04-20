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

require_once( TCP_DAOS_FOLDER . 'Addresses.class.php' );
require_once( TCP_DAOS_FOLDER . 'Orders.class.php' );

require_once( TCP_CLASSES_FOLDER . 'OrderPage.class.php' );

class TCPCheckoutManager {

	public static $TOP_BAR = 'TOP_BAR';
	public static $ACCORDION = 'ACCORDION';

	private $checkout_type;

	public static $default_steps = array(
		'TCPSigninBox',
		'TCPBillingBox',
		'TCPShippingBox',
		'TCPShippingMethodsBox',
		'TCPPaymentMethodsBox',
		'TCPCartBox',
		'TCPNoticeBox',
	);
	
	private $steps = array();

	function __construct( $checkout_type = 'ACCORDION' ) { //ACCORDION, TOP_BAR
		$this->checkout_type = $checkout_type;
		$this->steps = TCPCheckoutManager::get_steps();
		if ( ! session_id() ) session_start();
		if ( ! isset( $_SESSION['tcp_checkout'] ) ) $_SESSION['tcp_checkout'] = array();
	}
	
	function show() {
		$html = apply_filters( 'tcp_checkout_manager', '' );
		if ( strlen( $html ) > 0 ) return $html;

		$step = isset( $_REQUEST['step'] ) ? $_REQUEST['step'] : 0;
		$box = $this->get_box( $step );
		if ( isset( $_REQUEST['tcp_continue'] ) ) {
			if ( ! $box->after_action() ) {
				return $this->show_box( $box, $step );
			} else {
				return $this->show_next_box( $box, $step );
			}
		} elseif ( isset( $_REQUEST['tcp_back'] ) ) {
			return $this->show_previous_box( $box, $step );
		} else {
			$box = $this->get_box( $step );
			$next_step = $step + $box->before_action();
			if ( $step == $next_step ) {
				return $this->show_box( $box, $step );
			} else {
				return $this->show_next_box( $box, $step );
			}
		}
	}

	static function get_steps() {
		return get_option( 'tcp_checkout_steps', TCPCheckoutManager::$default_steps );
	}

	static function update_steps( $steps) {
		update_option( 'tcp_checkout_steps', $steps );
	}

	static function restore_default() {
		update_option( 'tcp_checkout_steps', TCPCheckoutManager::$default_steps );
	}

	static function add_step( $class_name ) {
		$steps = TCPCheckoutManager::get_steps();
		$steps[] = $class_name;
		TCPCheckoutManager::update_steps( $steps );
	}

	static function remove_step( $class_name ) {
		$steps = TCPCheckoutManager::get_steps();
		foreach( $steps as $i => $item ) {
			if ( $item == $class_name ) {
				unset( $steps[$i] );
				break;
			}
		}
		$new_steps = array();
		foreach( $steps as $item ) {
			$new_steps[] = $item;
		}
		TCPCheckoutManager::update_steps( $new_steps );
	}

	private function show_next_box( $box, $step ) {
		$step++;
		$next_box = $this->get_box( $step );
		if ( $next_box ) {
			$next_step = $step + $next_box->before_action();
			if ( $step != $next_step ) {
				$next_box = $this->get_box( $next_step );
				return $this->show_next_box( $next_box, $step );
			} else {
				return $this->show_box( $next_box, $next_step );
			}
		} else {
			return $this->show_box( $box, $step ); //to see the last step
		}
	}

	private function show_previous_box( $box, $step ) {
		if ( $step == 0 ) {
			return $this->show_next_box( $box, 0 );
		} else {
			$step--;
			$previous_box = $this->get_box( $step );
			$previous_step = $step - abs( $previous_box->before_action() );
			if ( $step != $previous_step ) {
				$previous_box = $this->get_box( $previous_step );
				return $this->show_previous_box( $previous_box, $step );
			} else {
				return $this->show_box( $previous_box, $previous_step );
			}
		}
	}

	private	function show_box( $box, $step = 0 ) {
		global $thecartpress;
		$user_registration = isset( $thecartpress->settings['user_registration'] ) ? $thecartpress->settings['user_registration'] : false;
		if ( $user_registration && $step > 0 && ! is_user_logged_in() ) return $this->show_box( $this->get_box( 0 ) );
		ob_start(); ?>
		<div class="checkout" id="checkout">
		<?php $this->show_header( $box, $step );
		if ( $step == count( $this->steps ) ) : //last step, no return
			echo $this->create_order(); //create the order, show payment form
		else :
			if ( $box->is_form_encapsulated() ) :?><form method="post"><?php endif; ?>
		<div class="<?php echo $box->get_class(); ?> active" id="<?php echo $box->get_class(); ?>">
			<h3><?php echo $step + 1; ?>. <?php echo $box->get_title(); ?></h3>
			<?php $see_continue_button = $box->show();
			$html = '';
			if ( ! $box->is_form_encapsulated() ) $html .= '<form method="post">';
			if ( $step > 0 ) $html .= '<input type="submit" name="tcp_back" id="tcp_back" value="' . __( 'Back', 'tcp' ) . '" class="tcp_checkout_button" />';
			if ( $see_continue_button && $step < count( $this->steps ) ) {
				$html .= '<input type="submit" name="tcp_continue" id="tcp_continue" value="' . __( 'Continue', 'tcp' ) . '" class="tcp_checkout_button" />';
			}
			$html .= '<input type="hidden" name="step" value="' . $step . '" />';
			if ( ! $box->is_form_encapsulated() ) $html .= '</form>';
			if ( strlen( $html ) > 0 ) :?>
			<span class="tcp_back_continue"><?php echo $html; ?></span>
			<?php endif;
			$this->show_footer( $box, $step ); ?>
		</div><!-- <?php echo $box->get_class(); ?> -->
		<?php if ( $box->is_form_encapsulated() ) :?></form><?php endif;
		endif; ?>
		</div><!-- checkout --><?php
		return ob_get_clean();
	}

	private function show_header( $box, $step = 0 ) {
		if ( $step == count( $this->steps ) ) { //last step, no return
		} elseif ( $this->checkout_type == TCPCheckoutManager::$TOP_BAR ) { ?>
			<ul class="tcp_checkout_bar">
			<?php foreach( $this->steps as $s => $value ) {
				if ( $s < $step ) {
					$b = $this->get_box( $s );
					$url = add_query_arg( 'step', $s, get_permalink() ); ?>
				<li class="tcp_ckeckout_step"><a href="<?php echo $url; ?>"><?php echo $b->get_title(); ?></a></li>
				<?php } else {
					$b = $this->get_box( $s ); ?>
				<li class="<?php if ( $s == $step ) : ?>tcp_ckeckout_active_step<?php else : ?>tcp_ckeckout_step<?php endif; ?>"><span><?php echo $b->get_title(); ?></span></li>
				<?php }
			}?>
			</ul>
		<?php } else { //TCPCheckoutManager::$ACCORDION
			foreach( $this->steps as $s => $value ) {
				if ( $s < $step ) {
					$b = $this->get_box( $s );
					$url = add_query_arg( 'step', $s, get_permalink() ); ?>
					<div class="<?php echo $b->get_class(); ?>" id="<?php echo $b->get_class(); ?>">
					<h3 class="tcp_ckeckout_step"><a href="<?php echo $url; ?>"><?php echo $s + 1; ?>. <?php echo $b->get_title(); ?></a></h3>
					</div>
				<?php }
			}
		}
	}

	private function show_footer( $box, $step = 0 ) {
		if ( $step == count( $this->steps ) -1 ) { //last step, no return
		} elseif ( $this->checkout_type == TCPCheckoutManager::$ACCORDION ) {
			foreach( $this->steps as $s => $value ) {
				if ( $s > $step ) {
				$b = $this->get_box( $s ); ?>
					<h3 class="tcp_ckeckout_step"><span><?php echo $s + 1; ?>. <?php echo $b->get_title(); ?></span></h3>
				<?php }
			}
		}
		
	}

	private function get_box( $step = 0 ) {
		if ( isset( $this->steps[$step] ) ) {
			$checkout_box = isset( $this->steps[$step] ) ? $this->steps[$step] : false;
			if ( $checkout_box ) {
				global $tcp_checkout_boxes;
				$initial_path = dirname( dirname( TCP_ADMIN_FOLDER ) ) . '/';
				require_once( $initial_path . $tcp_checkout_boxes[$checkout_box] );
				return new $checkout_box();
			} else {
				return false;
				//throw new InvalidArgumentException( 'The Checkout is not configured correctly' );
			}
		} else {
			return false;
			//throw new InvalidArgumentException( 'The step ' . $step .' doesn\'t exist in the checkout configuration' );
		}
	}
	
	private function create_order() {
		$selected_billing_address	= isset( $_SESSION['tcp_checkout']['billing']['selected_billing_address'] ) ? $_SESSION['tcp_checkout']['billing']['selected_billing_address'] : 'N';
		$selected_shipping_address	= isset( $_SESSION['tcp_checkout']['shipping']['selected_shipping_address'] ) ? $_SESSION['tcp_checkout']['shipping']['selected_shipping_address'] : 'N';
		do_action( 'tcp_checkout_create_order_start' );
		$order = array();
		$order['created_at']			= date( 'Y-m-d H:i:s' );
		$order['ip']					= tcp_get_remote_ip();
		$order['status']				= Orders::$ORDER_PENDING;
		$order['comment']				= isset( $_SESSION['tcp_checkout']['cart']['comment'] ) ? $_SESSION['tcp_checkout']['cart']['comment'] : '';
		$order['order_currency_code']	= tcp_get_the_currency_iso();
		if ( $selected_billing_address == 'Y' ) {
			$address = Addresses::get( $_SESSION['tcp_checkout']['billing']['selected_billing_id'] );
			$order['billing_firstname']		= $address->firstname;
			$order['billing_lastname']		= $address->lastname;
			$order['billing_company']		= $address->company;
			$order['billing_street']		= $address->street;
			$order['billing_city']			= $address->city;
			$order['billing_city_id']		= $address->city_id;
			$order['billing_region']		= $address->region;
			$order['billing_region_id']		= $address->region_id;
			$order['billing_postcode']		= $address->postcode;
			$order['billing_country']		= ''; //$address->country;
			$order['billing_country_id']	= $address->country_id;
			$order['billing_telephone_1']	= $address->telephone_1;
			$order['billing_telephone_2']	= $address->telephone_2;
			$order['billing_fax']			= $address->fax;
			$order['billing_email']			= $address->email;
			$create_billing_address = false;
		} else {
			$order['billing_firstname']		= $_SESSION['tcp_checkout']['billing']['billing_firstname'];
			$order['billing_lastname']		= $_SESSION['tcp_checkout']['billing']['billing_lastname'];
			$order['billing_company']		= $_SESSION['tcp_checkout']['billing']['billing_company'];
			$order['billing_street']		= $_SESSION['tcp_checkout']['billing']['billing_street'];
			$order['billing_city']			= $_SESSION['tcp_checkout']['billing']['billing_city'];
			$order['billing_city_id']		= isset( $_SESSION['tcp_checkout']['billing']['billing_city_id'] ) ? $_SESSION['tcp_checkout']['billing']['billing_city_id'] : '';
			$order['billing_region']		= isset( $_SESSION['tcp_checkout']['billing']['billing_region'] ) ? $_SESSION['tcp_checkout']['billing']['billing_region'] : '';
			$order['billing_region_id']		= isset( $_SESSION['tcp_checkout']['billing']['billing_region_id'] ) ? $_SESSION['tcp_checkout']['billing']['billing_region_id'] : '';
			$order['billing_postcode']		= $_SESSION['tcp_checkout']['billing']['billing_postcode'];
			$order['billing_country']		= isset( $_SESSION['tcp_checkout']['billing']['billing_country'] ) ? $_SESSION['tcp_checkout']['billing']['billing_country'] : '';
			$order['billing_country_id']	= $_SESSION['tcp_checkout']['billing']['billing_country_id'];
			$order['billing_telephone_1']	= $_SESSION['tcp_checkout']['billing']['billing_telephone_1'];
			$order['billing_telephone_2']	= $_SESSION['tcp_checkout']['billing']['billing_telephone_2'];
			$order['billing_fax']			= $_SESSION['tcp_checkout']['billing']['billing_fax'];
			$order['billing_email']			= $_SESSION['tcp_checkout']['billing']['billing_email'];
			$create_billing_address = true;
		}
		if ( $selected_shipping_address == 'Y' ) {
			$address = Addresses::get( $_SESSION['tcp_checkout']['shipping']['selected_shipping_id'] );
			$order['shipping_firstname']	= $address->firstname;
			$order['shipping_lastname']		= $address->lastname;
			$order['shipping_company']		= $address->company;
			$order['shipping_street']		= $address->street;
			$order['shipping_city']			= $address->city;
			$order['shipping_city_id']		= $address->city_id;
			$order['shipping_region']		= $address->region;
			$order['shipping_region_id']	= $address->region_id;
			$order['shipping_postcode']		= $address->postcode;
			$order['shipping_country']		= ''; //$address->country;
			$order['shipping_country_id']	= $address->country_id;
			$order['shipping_telephone_1']	= $address->telephone_1;
			$order['shipping_telephone_2']	= $address->telephone_2;
			$order['shipping_fax']			= $address->fax;
			$order['shipping_email']		= $address->email;
			$create_shipping_address = false;
		} elseif ( $selected_shipping_address == 'BIL' ) {
			$order['shipping_firstname']	= $order['billing_firstname'];
			$order['shipping_lastname']		= $order['billing_lastname'];
			$order['shipping_company']		= $order['billing_company'];
			$order['shipping_street']		= $order['billing_street'];
			$order['shipping_city']			= $order['billing_city'];
			$order['shipping_city_id']		= $order['billing_city_id'];
			$order['shipping_region']		= $order['billing_region'];
			$order['shipping_region_id']	= $order['billing_region_id'];
			$order['shipping_postcode']		= $order['billing_postcode'];
			$order['shipping_country']		= //$order['billing_country'];
			$order['shipping_country_id']	= $order['billing_country_id'];
			$order['shipping_telephone_1']	= $order['billing_telephone_1'];
			$order['shipping_telephone_2']	= $order['billing_telephone_2'];
			$order['shipping_fax']			= $order['billing_fax'];
			$order['shipping_email']		= $order['billing_email'];
			$create_shipping_address = false;
		} elseif ( isset( $_SESSION['tcp_checkout']['shipping'] ) ) {
			$order['shipping_firstname']	= $_SESSION['tcp_checkout']['shipping']['shipping_firstname'];
			$order['shipping_lastname']		= $_SESSION['tcp_checkout']['shipping']['shipping_lastname'];
			$order['shipping_company']		= $_SESSION['tcp_checkout']['shipping']['shipping_company'];
			$order['shipping_street']		= $_SESSION['tcp_checkout']['shipping']['shipping_street'];
			$order['shipping_city']			= $_SESSION['tcp_checkout']['shipping']['shipping_city'];
			$order['shipping_city_id']		= isset( $_SESSION['tcp_checkout']['shipping']['shipping_city_id'] ) ? $_SESSION['tcp_checkout']['shipping']['shipping_city_id'] : '';
			$order['shipping_region']		= isset( $_SESSION['tcp_checkout']['shipping']['shipping_region'] ) ? $_SESSION['tcp_checkout']['shipping']['shipping_region'] : '';
			$order['shipping_region_id']	= isset( $_SESSION['tcp_checkout']['shipping']['shipping_region_id'] ) ? $_SESSION['tcp_checkout']['shipping']['shipping_region_id'] : '';
			$order['shipping_postcode']		= $_SESSION['tcp_checkout']['shipping']['shipping_postcode'];
			$order['shipping_country']		= isset( $_SESSION['tcp_checkout']['shipping']['shipping_country'] ) ? $_SESSION['tcp_checkout']['shipping']['shipping_country'] : '';
			$order['shipping_country_id']	= $_SESSION['tcp_checkout']['shipping']['shipping_country_id'];
			$order['shipping_telephone_1']	= $_SESSION['tcp_checkout']['shipping']['shipping_telephone_1'];
			$order['shipping_telephone_2']	= $_SESSION['tcp_checkout']['shipping']['shipping_telephone_2'];
			$order['shipping_fax']			= $_SESSION['tcp_checkout']['shipping']['shipping_fax'];
			$order['shipping_email']		= $_SESSION['tcp_checkout']['shipping']['shipping_email'];
			$create_shipping_address = true;
		} else {
			$order['shipping_firstname']	= '';
			$order['shipping_lastname']		= '';
			$order['shipping_company']		= '';
			$order['shipping_street']		= '';
			$order['shipping_city']			= '';
			$order['shipping_city_id']		= '';
			$order['shipping_region']		= '';
			$order['shipping_region_id']	= '';
			$order['shipping_postcode']		= '';
			$order['shipping_country']		= '';
			$order['shipping_country_id']	= '';
			$order['shipping_telephone_1']	= '';
			$order['shipping_telephone_2']	= '';
			$order['shipping_fax']			= '';
			$order['shipping_email']		= '';
			$create_shipping_address = false;
		}
		if ( is_user_logged_in() ) {
			global $current_user;
			get_currentuserinfo();
			$order['customer_id'] = $current_user->ID;
		} else {
			$order['customer_id'] = 0;
		}
		$shoppingCart = TheCartPress::getShoppingCart();
		$shipping_country = $this->get_shipping_country();
		if ( isset( $_SESSION['tcp_checkout']['shipping_methods']['shipping_method_id'] ) ) { //sending
			$smi = $_SESSION['tcp_checkout']['shipping_methods']['shipping_method_id'];
			$smi = explode( '#', $smi );
			$class = $smi[0];
			$instance = $smi[1];
			$shipping_method = new $class();
			$shipping_amount = $shipping_method->getCost( $instance, $shipping_country, $shoppingCart );
			$shoppingCart->addOtherCost( ShoppingCart::$OTHER_COST_SHIPPING_ID, $shipping_amount, __( 'Shipping cost', 'tcp' ) );
			$order['shipping_amount'] = 0;
			//$order['shipping_method'] = $class;
			$order['shipping_method'] = $shipping_method->getCheckoutMethodLabel( $instance, $shipping_country, $shoppingCart );// . ' [' . $class . ']';
		} else {
			$order['shipping_amount'] = 0;
			$order['shipping_method'] = '';
		}
		if ( isset( $_SESSION['tcp_checkout']['payment_methods']['payment_method_id'] ) ) {
			$pmi = $_SESSION['tcp_checkout']['payment_methods']['payment_method_id'];
			$pmi = explode ('#', $pmi );
			$class = $pmi[0];
			$instance = $pmi[1];
			$payment_method = new $class();
			$payment_amount = $payment_method->getCost( $instance, $shipping_country, $shoppingCart );
			$order['payment_amount'] = 0;
			$shoppingCart->addOtherCost( ShoppingCart::$OTHER_COST_PAYMENT_ID, $payment_amount, __( 'Payment cost', 'tcp' ) );
			$order['payment_method'] = $class;
			//$order['payment_name']   = $payment_method->getName();
			$order['payment_name']   = $payment_method->getCheckoutMethodLabel( $instance, $shipping_country, $shoppingCart );// . ' [' . $payment_method->getName() . ']';
		} else {
			$order['payment_amount'] = 0;
			$order['payment_method'] = '';
			$order['payment_name']   = '';
		}
		do_action( 'tcp_checkout_calculate_other_costs' );
		if ( tcp_is_display_prices_with_taxes() ) $order['discount_amount'] = $shoppingCart->getAllDiscounts();
		else $order['discount_amount'] = $shoppingCart->getCartDiscountsTotal();
		$order['weight'] = $shoppingCart->getWeight();
		$order['comment_internal'] = '';
		$order['code_tracking'] = '';
		$order['transaction_id'] = '';
		//TODO more values???
		if ( isset( $order['billing_country'] ) && strlen( $order['billing_country'] ) == 0 ) {
			$country_bill = Countries::get( $order['billing_country_id'] );
			$order['billing_country'] = $country_bill->name;
		}
		if ( $order['shipping_country_id'] == $order['billing_country_id'] )
			$order['shipping_country'] = $order['billing_country'];
		elseif ( isset( $order['shipping_country'] ) && strlen( $order['shipping_country'] ) == 0 ) {
			$country_ship = Countries::get( $order['shipping_country_id'] );
			if ( $country_ship ) $order['shipping_country'] = $country_ship->name;
		}
		$order_id = Orders::insert( $order );
		do_action( 'tcp_checkout_create_order_insert', $order_id );
		foreach( $shoppingCart->getItems() as $item ) {
			$post = get_post( $item->getPostId() );
			$sku = tcp_get_the_sku( $item->getPostId(), $item->getOption1Id(), $item->getOption2Id() );
			$days_to_expire = (int)get_post_meta( $post->ID, 'tcp_days_to_expire', true );
			if ( $days_to_expire > 0 ) {
				$today = date( 'Y-m-d' );
				$expires_at = date ( 'Y-m-d', strtotime( date( 'Y-m-d', strtotime( $today ) ) . " +$days_to_expire day" ) );
			} elseif ( $days_to_expire == 0 ) {
				$expires_at = date( 'Y-m-d' );
			} else {
				$expires_at = date( 'Y-m-d', mktime( 0, 0, 0, 1, 1, 2000 ) );
			}
			$ordersDetails = array();
			$ordersDetails['order_id']			= $order_id;
			$ordersDetails['post_id']			= $item->getPostId();
			$ordersDetails['option_1_id']		= $item->getOption1Id();
			$ordersDetails['option_2_id']		= $item->getOption2Id();
			$ordersDetails['weight']			= $item->getWeight();
			$ordersDetails['is_downloadable']	= $item->isDownloadable() ? 'Y' : '';
			$ordersDetails['sku']				= $sku;
			$ordersDetails['name']				= tcp_get_the_title( $post->ID );
			$ordersDetails['option_1_name']		= $item->getOption1Id() > 0 ? get_the_title( $item->getOption1Id() ) : '';
			$ordersDetails['option_2_name']		= $item->getOption2Id() > 0 ? get_the_title( $item->getOption2Id() ) : '';
			if ( ! tcp_is_display_prices_with_taxes() ) $discount = $item->getDiscount() / $item->getUnits();
			else $discount = 0;
			$ordersDetails['price']				= tcp_get_the_price_without_tax( $item->getPostId(), $item->getUnitPrice() ) - $discount;
			$ordersDetails['original_price']	= $item->getUnitPrice();
			$ordersDetails['tax']				= tcp_get_the_tax( $item->getPostId() );
			$ordersDetails['qty_ordered']		= $item->getCount();
			$ordersDetails['max_downloads']		= get_post_meta( $post->ID, 'tcp_max_downloads', true );
			$ordersDetails['expires_at']		= $expires_at;
			$orders_details_id = OrdersDetails::insert( $ordersDetails );
			if ( $item->hasAttributes() ) tcp_update_order_detail_meta( $orders_details_id, 'tcp_attributes', $item->getAttributes() );
			do_action( 'tcp_checkout_create_order_insert_detail', $order_id, $orders_details_id, $item->getPostId(), $ordersDetails ); //, $item->getOption1Id(), $item->getOption2Id() );
		}
		foreach( $shoppingCart->getOtherCosts() as $id => $cost ) {
			if ( $id == ShoppingCart::$OTHER_COST_SHIPPING_ID && $shoppingCart->isFreeShipping() ) {
				continue;
			} else {
			//if ( $id != ShoppingCart::$OTHER_COST_SHIPPING_ID && $id != ShoppingCart::$OTHER_COST_PAYMENT_ID ) {
				$ordersCosts = array();
				$ordersCosts['order_id']	= $order_id;
				$ordersCosts['description']	= $cost->getDesc();
				$ordersCosts['cost']		= tcp_get_the_shipping_cost_without_tax( $cost->getCost() );
				$ordersCosts['tax']			= tcp_get_the_shipping_tax();
				$ordersCosts['cost_order']	= $cost->getOrder();
				$orders_cost_id = OrdersCosts::insert( $ordersCosts );
				do_action( 'tcp_checkout_create_order_insert_cost', $orders_cost_id );
			}
		}
		if ( $create_shipping_address ) $this->createNewShippingAddress( $order );
		if ( $create_billing_address ) $this->createNewBillingAddress( $order );
		//
		// shows Payment Area
		//
		ob_start(); ?>
		<div class="tcp_payment_area">
		<?php do_action( 'tcp_checkout_ok', $order_id ); ?>
		<p><?php _e( 'The next step helps you to pay using the payment method chosen by you.', 'tcp' ); ?></p>
		<?php if ( isset( $_SESSION['tcp_checkout']['payment_methods']['payment_method_id'] ) ) {
			$pmi = $_SESSION['tcp_checkout']['payment_methods']['payment_method_id'];
			$pmi = explode( '#', $pmi );
			$class = $pmi[0];
			$instance = $pmi[1];
			$payment_method = new $class();
			do_action( 'tcp_checkout_calculate_other_costs' ); ?>
			<div class="tcp_pay_form">
			<?php $payment_method->showPayForm( $instance, $shipping_country, $shoppingCart, $order_id ); ?>
			<div class="tcp_plugin_notice $plugin_name"><?php tcp_do_template( 'tcp_payment_plugins_' . $class ); ?></div>
			</div>
		<?php }
		$order_page = OrderPage::show( $order_id, true, false );
		$_SESSION['order_page'] = $order_page;
		echo $order_page; ?>
		<br />
		<a href="<?php echo plugins_url( 'thecartpress/admin/PrintOrder.php' ); ?>" target="_blank"><?php _e( 'Print', 'tcp' ); ?></a>
		</div><!-- tcp_payment_area--><?php
		$shoppingCart->setOrderId( $order_id );//since 1.1.0
		//$shoppingCart->deleteAll();//remove since 1.1.0
		return ob_get_clean();
	}

	private function get_shipping_country() {
		$shipping_country = '';
		$selected_shipping_address = isset( $_SESSION['tcp_checkout']['shipping']['selected_shipping_address'] ) ? $_SESSION['tcp_checkout']['shipping']['selected_shipping_address'] : false;
		if ( $selected_shipping_address == 'new' ) {
			$shipping_country = $_SESSION['tcp_checkout']['shipping']['shipping_country_id'];
		} elseif ( $selected_shipping_address == 'BIL' ) {
			if ( isset( $_SESSION['tcp_checkout']['billing']['selected_billing_address'] ) && $_SESSION['tcp_checkout']['billing']['selected_billing_address'] == 'new' )
				$shipping_country = $_SESSION['tcp_checkout']['billing']['billing_country_id'];
			else {
				$address_id = $_SESSION['tcp_checkout']['billing']['selected_billing_id'];
				$address = Addresses::get( $address_id );
				$shipping_country = $address->country_id;
			}
		} elseif ( $selected_shipping_address == 'Y' ) {
			if ( isset( $_SESSION['tcp_checkout']['shipping']['selected_shipping_id'] ) ) {
				$address_id = $_SESSION['tcp_checkout']['shipping']['selected_shipping_id'];
				$address = Addresses::get( $address_id );
				$shipping_country = $address->country_id;
			}
		}
		return $shipping_country;
	}

	private function createNewBillingAddress( $order ) {
		if ( $order['customer_id'] > 0 ) {
			$address = array();
			$address['customer_id'] = $order['customer_id'];
			$address['default_shipping'] = 'N';
			$address['default_billing'] = 'Y';
			$address['name'] = __( 'billing address', 'tcp' );//title
			$address['firstname'] = $order['billing_firstname'];
			$address['lastname'] = $order['billing_lastname'];
			$address['company'] = $order['billing_company'];
			$address['street'] = $order['billing_street'];
			$address['city'] = $order['billing_city'];
			$address['city_id'] = $order['billing_city_id'];
			$address['region_id'] = $order['billing_region_id'];
			$address['region'] = $order['billing_region'];
			$address['postcode'] = $order['billing_postcode'];
			$address['country'] = $order['billing_country'];
			$address['country_id'] = $order['billing_country_id'];
			$address['telephone_1'] = $order['billing_telephone_1'];
			$address['telephone_2'] = $order['billing_telephone_2'];
			$address['fax'] = $order['billing_fax'];
			$address['email'] = $order['billing_email'];
			Addresses::save($address);
		}
	}

	function createNewShippingAddress( $order ) {
		if ( $order['customer_id'] > 0 ) {
			$address = array();
			$address['customer_id'] = $order['customer_id'];
			$address['default_shipping'] = 'Y';
			$address['default_billing'] = 'N';
			$address['name'] = __( 'shipping address', 'tcp' );
			$address['firstname'] = $order['shipping_firstname'];
			$address['lastname'] = $order['shipping_lastname'];
			$address['company'] = $order['shipping_company'];
			$address['street'] = $order['shipping_street'];
			$address['city'] = $order['shipping_city'];
			$address['city_id'] = $order['shipping_city_id'];
			$address['region_id'] = $order['shipping_region_id'];
			$address['region'] = $order['shipping_region'];
			$address['postcode'] = $order['shipping_postcode'];
			$address['country'] = $order['shipping_country'];
			$address['country_id'] = $order['shipping_country_id'];
			$address['telephone_1'] = $order['shipping_telephone_1'];
			$address['telephone_2'] = $order['shipping_telephone_2'];
			$address['fax'] = $order['shipping_fax'];
			$address['email'] = $order['shipping_email'];
			Addresses::save( $address );
		}
	}
}
?>
