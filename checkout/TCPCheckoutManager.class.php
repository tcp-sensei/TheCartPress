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

if ( ! class_exists( 'TCPCheckoutManager' ) ) :

require_once( TCP_DAOS_FOLDER . 'Addresses.class.php' );
require_once( TCP_DAOS_FOLDER . 'Orders.class.php' );

require_once( TCP_CLASSES_FOLDER . 'OrderPage.class.php' );

class TCPCheckoutManager {

	public static $TOP_BAR	 = 'TOP_BAR';
	public static $ACCORDION = 'ACCORDION';

	protected $checkout_type;

	public static $default_steps = array(
		'TCPSigninBox',
		'TCPBillingBox',
		'TCPShippingBox',
		'TCPShippingMethodsBox',
		'TCPPaymentMethodsBox', //Payment step
		'TCPCartBox',
		//'TCPNoticeBox',
	);

	protected $steps = array();
	protected $steps_objects = array();

	function __construct( $checkout_type = 'ACCORDION' ) { //ACCORDION, TOP_BAR
		//global $thecartpress;
		//$https_checkout = $thecartpress->get_setting( 'https_checkout', false );
		$this->checkout_type = $checkout_type;
		$this->steps = TCPCheckoutManager::get_steps();
		if ( ! session_id() ) session_start();
		if ( ! isset( $_SESSION['tcp_checkout'] ) ) $_SESSION['tcp_checkout'] = array();
	}

	function getPaymentStep() {
		foreach( $this->steps as $s => $step ) {
			if ( $step == 'TCPPaymentMethodsBox' ) {
				return $s;
			}
		}
		return 0;
	}

	function show() {
		$html = apply_filters( 'tcp_checkout_manager', '' );
		if ( strlen( $html ) > 0 ) return $html;
		$step = isset( $_REQUEST['tcp_step'] ) ? $_REQUEST['tcp_step'] : 0;
		$box = $this->get_box( $step );
		if ( !$box ) {
			$step = 0;
			$box = $this->get_box( $step );
		}
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
		$default_steps = TCPCheckoutManager::$default_steps;
		$steps = get_option( 'tcp_checkout_steps', $default_steps );
		return apply_filters( 'tcp_checkout_get_defaults', $steps );
	}

	static function update_steps( $steps) {
		update_option( 'tcp_checkout_steps', $steps );
	}

	static function restore_default() {
		$default_steps = TCPCheckoutManager::$default_steps;
		//Cart Step: Notice area
		$settings = get_option( 'tcp_TCPCartBox', array() );
		if ( ! isset( $settings['see_notice'] ) ) {
			$settings['see_notice'] = true;
			update_option( 'tcp_TCPCartBox', $settings );
		}
		update_option( 'tcp_checkout_steps', apply_filters( 'tcp_checkout_restore_defaults', $default_steps ) );
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

	static function get_step_by_permalink( $permalink ) {
		$steps = TCPCheckoutManager::get_steps();
		if ( defined( 'TCP_CHECKOUT_PURCHASE' ) && TCP_CHECKOUT_PURCHASE == $permalink ) return count( $steps );
		global $tcp_checkout_boxes;
		$i = 0;
		foreach( $steps as $step ) {
			$box = $tcp_checkout_boxes[$step];
			if ( $box['name'] == $permalink ) return $i;
			else $i++;
		}
		return 0;
	}

	protected function show_next_box( $box, $step ) {
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

	protected function show_previous_box( $box, $step ) {
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

	protected function show_box( $box, $step = 0 ) {
		global $thecartpress;
		$user_registration	= $thecartpress->get_setting( 'user_registration', false );
		$buy_button_color	= tcp_get_buy_button_color();
		if ( $user_registration && $step > 0 && ! is_user_logged_in() ) return $this->show_box( $this->get_box( 0 ) );
		ob_start(); ?>
<div class="checkout tcpf" id="checkout">
	<?php $last_step_number = $this->show_header( $box, $step );
	if ( $step == count( $this->steps ) ) { //last step, no return
		if ( in_the_loop() ) {
			$order_id = $this->create_order(); //create the order, show payment form
		} else {
			$shoppingCart = TheCartPress::getShoppingCart();
			$order_id = $shoppingCart->getOrderId();
		}
		echo $this->show_payment_area( $order_id );
	} else {
		$next_box = $this->get_box( $step + 1 );
		$action = tcp_get_the_checkout_url();
		if ( defined( 'TCP_CHECKOUT_PURCHASE' ) ) {
			if ( $next_box ) $action .= $next_box->get_name();
			else $action = $action .= TCP_CHECKOUT_PURCHASE;
		}
		if ( ! $box->is_hidden() ) {
			if ( $box->is_form_encapsulated() ) { ?>
			<form method="post" action="<?php echo $action; ?>">
			<?php } ?>
				<div class="<?php echo $box->get_class(); ?> active" id="<?php echo $box->get_class(); ?>">
					<h3>
						<?php echo apply_filters( 'tcp_ckeckout_current_title', $last_step_number++ . '. ' . $box->get_title(), $step ); ?>
					</h3>
					<?php $see_continue_button = $box->show();
					ob_start(); //Create continue and back buttons
					if ( ! $box->is_form_encapsulated() ) : ?><form method="post" action="<?php echo $action; ?>"><?php endif;
					if ( $step > 0 ) : 
						if ( defined( 'TCP_CHECKOUT' ) ) : ?>
						<a href="<?php $previous_box = $this->get_box( $step - 1 ); echo get_site_url() . '/' . TCP_CHECKOUT .'/' . $previous_box->get_name(); ?>" name="tcp_back" id="tcp_back" class="tcp-btn tcp_checkout_button"><?php _e( 'Back', 'tcp' ); ?></a>
						<?php else : ?>
						<button type="submit" name="tcp_back" id="tcp_back" class="tcp_checkout_button tcp-btn tcp-btn-default"><?php echo apply_filters( 'tcp_checkout_back_button_title', __( 'Back', 'tcp' ), $step, $this->steps ); ?></button>
						<?php endif;
					endif;
					if ( $see_continue_button ) :
						if ( $step < count( $this->steps ) - 1 ) : ?>
							<button type="submit" name="tcp_continue" id="tcp_continue" class="tcp_checkout_button tcp-btn <?php echo $buy_button_color; ?>"><?php echo apply_filters( 'tcp_checkout_continue_button_title', __( 'Continue', 'tcp' ), $step, $this->steps ); ?></button>
						<?php elseif ( $step == count( $this->steps ) - 1 ) : ?>
							<button type="submit" name="tcp_continue" id="tcp_continue" class="tcp_checkout_button tcp-btn <?php echo $buy_button_color; ?>"><?php echo apply_filters( 'tcp_checkout_purchase_button_title', __( 'Purchase', 'tcp' ), $step, $this->steps ); ?></button>
							<input type="hidden" name="tcp_step" value="<?php echo count( $this->steps ) - 1; ?>" />
						<?php endif;
					endif; ?>
					<input type="hidden" name="tcp_step" value="<?php echo $step; ?>" />
					<?php $html = apply_filters( 'tcp_show_box_back_continue', ob_get_clean(), $step, $this->steps );
					if ( ! $box->is_form_encapsulated() ) $html .= '</form>';
					if ( strlen( $html ) > 0 ) : ?>
						<span class="tcp_back_continue"><?php echo $html; ?></span>
					<?php endif; ?>
					<?php $this->show_footer( $box, $step, $last_step_number ); ?>
				</div><!-- <?php echo $box->get_class(); ?> -->
			<?php if ( $box->is_form_encapsulated() ) { ?>
			</form>
			<?php }
		}
	}
	do_action( 'tcp_show_box', $step ); ?>
</div><!-- checkout --><?php
		return apply_filters( 'tcp_show_box_filter', ob_get_clean(), $step );
	}

	protected function show_header( $box, $step = 0 ) {
		$step_number = 1;
		if ( $step == count( $this->steps ) ) { //last step, no return
		} elseif ( $this->checkout_type == TCPCheckoutManager::$TOP_BAR ) { ?>
			<ul class="tcp_checkout_bar">
			<?php foreach( $this->steps as $s => $value ) { ?>
				<?php if ( $s < $step ) {
					$b = $this->get_box( $s );
					if ( defined( 'TCP_CHECKOUT' ) ) $url = get_site_url() . '/' . TCP_CHECKOUT .'/' . $b->get_name();
					else $url = add_query_arg( 'tcp_step', $s, get_permalink() ); ?>
				<li class="tcp_ckeckout_step">
					<a href="<?php echo $url; ?>"><?php echo $b->get_title(); ?></a>
				</li>
				<?php } else { $b = $this->get_box( $s ); ?>
				<li class="<?php if ( $s == $step ) { ?>tcp_ckeckout_active_step<?php } else { ?>tcp_ckeckout_step<?php } ?>">
					<span><?php echo $b->get_title(); ?></span>
					<?php do_action( 'tcp_ckeckout_header', $step ); ?>
				</li>
				<?php }
			} ?>
			</ul>
		<?php } else {//TCPCheckoutManager::$ACCORDION
			foreach( $this->steps as $s => $value ) { ?>
				<?php if ( $s < $step ) {
					$b = $this->get_box( $s );
					if ( ! $b->is_hidden() ) {
						if ( defined( 'TCP_CHECKOUT' ) ) $url = get_site_url() . '/' . TCP_CHECKOUT .'/' . $b->get_name();
						else $url = add_query_arg( 'tcp_step', $s, get_permalink() ); ?>
						<div class="<?php echo $b->get_class(); ?>" id="<?php echo $b->get_class(); ?>">
							<h3 class="tcp_ckeckout_step"><a href="<?php echo $url; ?>" tcp_step="<?php echo $s; ?>"><?php echo $step_number++; ?>. <?php echo $b->get_title(); ?></a></h3>
						</div>
					<?php }
				}
			}
		}
		return $step_number;
	}

	protected function show_footer( $box, $step = 0, $step_number = 0 ) {
		if ( $step == count( $this->steps ) -1 ) { //last step, no return
		} elseif ( $this->checkout_type == TCPCheckoutManager::$ACCORDION ) {
			foreach( $this->steps as $s => $value ) {
				if ( $s > $step ) {
					$b = $this->get_box( $s );
					if ( $b && ! $b->is_hidden() ) : ?>
					<h3 class="tcp_ckeckout_step"><span><?php echo $step_number++; ?>. <?php echo $b->get_title(); ?></span></h3>
					<?php endif;
				}
			}
		}
	}

	protected function get_box( $step = 0 ) {
		if ( isset( $this->steps_objects[$step] ) ) return $this->steps_objects[$step];
		if ( isset( $this->steps[$step] ) ) {
			$checkout_box = isset( $this->steps[$step] ) ? $this->steps[$step] : false;
			global $tcp_checkout_boxes;
			if ( $checkout_box && isset( $tcp_checkout_boxes[$checkout_box] ) ) {
				$initial_path = dirname( dirname( TCP_ADMIN_FOLDER ) ) . '/';
				require_once( $initial_path . $tcp_checkout_boxes[$checkout_box]['path'] );

				//instantiate the box object
				$this->steps_objects[$step] = new $checkout_box();
				return $this->steps_objects[$step];
			} else {
				return false;
				//throw new InvalidArgumentException( 'The Checkout is not configured correctly' );
			}
		} else {
			return false;
			//throw new InvalidArgumentException( 'The step ' . $step .' doesn\'t exist in the checkout configuration' );
		}
	}
	
	protected function create_order() {
		$selected_billing_address = isset( $_SESSION['tcp_checkout']['billing']['selected_billing_address'] ) ? $_SESSION['tcp_checkout']['billing']['selected_billing_address'] : 'N';
		$selected_shipping_address = isset( $_SESSION['tcp_checkout']['shipping']['selected_shipping_address'] ) ? $_SESSION['tcp_checkout']['shipping']['selected_shipping_address'] : 'N';
		do_action( 'tcp_checkout_create_order_start' );
		$order = array();
		$order['created_at'] = current_time( 'mysql' ); //date( 'Y-m-d H:i:s' );
		$order['ip'] = tcp_get_remote_ip();
		$order['status'] = Orders::$ORDER_PENDING;
		$order['comment'] = isset( $_SESSION['tcp_checkout']['cart']['comment'] ) ? $_SESSION['tcp_checkout']['cart']['comment'] : '';
		$order['order_currency_code'] = tcp_get_the_currency_iso();
		/* 
		 *  Set order billing address related fields to blanks so we avoid any errors later on 
		 *  if we use payment gateway to fill these fields and not the local wordpress form
		 */
		$order['billing_firstname'] = '';
		$order['billing_lastname'] = '';
		$order['billing_company'] = '';
		$order['billing_tax_id_number'] = '';
		$order['billing_street'] = '';
		$order['billing_street_2'] = '';
		$order['billing_city'] = '';
		$order['billing_city_id'] = '';
		$order['billing_region'] = '';
		$order['billing_region_id'] = '';
		$order['billing_postcode'] = '';
		$order['billing_country'] = ''; //$address->country;
		$order['billing_country_id'] = '';
		$order['billing_telephone_1'] = '';
		$order['billing_telephone_2'] = '';
		$order['billing_fax'] = '';
		$order['billing_email'] = '';
		$create_billing_address = false;
		$create_shipping_address = false;

		if ( $selected_billing_address == 'Y' ) {
			$address = Addresses::get( $_SESSION['tcp_checkout']['billing']['selected_billing_id'] );
			$order['billing_firstname']		= $address->firstname;
			$order['billing_lastname']		= $address->lastname;
			$order['billing_company']		= $address->company;
			$order['billing_tax_id_number']	= $address->tax_id_number;
			$order['billing_street']		= $address->street;
			$order['billing_street_2']		= $address->street_2;
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
			$order['billing_tax_id_number']	= $_SESSION['tcp_checkout']['billing']['billing_tax_id_number'];
			$order['billing_street']		= $_SESSION['tcp_checkout']['billing']['billing_street'];
			$order['billing_street_2']		= $_SESSION['tcp_checkout']['billing']['billing_street_2'];
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
			$order['shipping_street_2']		= $address->street_2;
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
			$order['shipping_street_2']		= $order['billing_street_2'];
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
			$order['shipping_street_2']		= $_SESSION['tcp_checkout']['shipping']['shipping_street_2'];
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
			$order['shipping_street_2']		= '';
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
		$shoppingCart = apply_filters( 'tcp_checkout_create_order_get_shopping_cart', TheCartPress::getShoppingCart() );
		$shipping_country = $this->get_shipping_country();
		if ( isset( $_SESSION['tcp_checkout']['shipping_methods']['shipping_method_id'] ) ) { //sending
			$smi				= $_SESSION['tcp_checkout']['shipping_methods']['shipping_method_id'];
			$smi				= explode( '#', $smi );
			$class				= $smi[0];
			$instance			= $smi[1];
			$shipping_method 	= new $class();
			$shipping_amount 	= $shipping_method->getCost( $instance, $shipping_country, $shoppingCart );
			$shoppingCart->addOtherCost( ShoppingCart::$OTHER_COST_SHIPPING_ID, $shipping_amount, __( 'Shipping cost', 'tcp' ) );
			$order['shipping_amount']	= 0;
			$order['shipping_method']	= strip_tags( $shipping_method->getCheckoutMethodLabel( $instance, $shipping_country, $shoppingCart ) );// . ' [' . $class . ']';
			$order['shipping_notice']	= $shipping_method->getNotice( $instance, $shipping_country, $shoppingCart );
			$order['shipping_class']	= $class;
			$order['shipping_instance']	= $instance;
		} else {
			$order['shipping_amount']	= 0;
			$order['shipping_method']	= '';//isset( $_SESSION['tcp_checkout']['shipping_methods']['shipping_label'] ) ? $_SESSION['tcp_checkout']['shipping_methods']['shipping_label'] : '';
			$order['shipping_notice']	= '';
			$order['shipping_class']	= '';
			$order['shipping_instance']	= 0;
		}
		if ( isset( $_SESSION['tcp_checkout']['payment_methods']['payment_method_id'] ) ) {
			$pmi = $_SESSION['tcp_checkout']['payment_methods']['payment_method_id'];
			$pmi = explode ('#', $pmi );
			$class = $pmi[0];
			$instance = $pmi[1];
			$payment_method	= new $class();
			$payment_amount	= $payment_method->getCost( $instance, $shipping_country, $shoppingCart );
			$order['payment_amount'] = 0;
			$shoppingCart->addOtherCost( ShoppingCart::$OTHER_COST_PAYMENT_ID, $payment_amount, __( 'Payment cost', 'tcp' ) );
			$order['payment_method'] = $class;
			$order['payment_notice'] = $payment_method->getNotice( $instance, $shipping_country, $shoppingCart );
			//$order['payment_name'] = $payment_method->getName();
			$order['payment_name']	 = $payment_method->getCheckoutMethodLabel( $instance, $shipping_country, $shoppingCart );// . ' [' . $payment_method->getName() . ']';
		} else {
			$order['payment_amount'] = 0;
			$order['payment_method'] = '';
			$order['payment_notice'] = '';
			$order['payment_name']	 = '';
		}
		do_action( 'tcp_checkout_calculate_other_costs', $order );
		if ( tcp_is_display_prices_with_taxes() ) $order['discount_amount'] = $shoppingCart->getAllDiscounts();
		else $order['discount_amount'] = $shoppingCart->getCartDiscountsTotal();
		$order['weight'] = $shoppingCart->getWeight();
		$order['comment_internal']	= '';
		$order['code_tracking']		= '';
		$order['transaction_id']	= '';
		//TODO more values???
		if ( isset( $order['billing_country'] ) && strlen( $order['billing_country'] ) == 0 ) {
			$country_bill = TCPCountries::get( $order['billing_country_id'] );
			if ( $country_bill ) $order['billing_country'] = $country_bill->name;
			else $order['billing_country'] = '';
		}
		if ( $order['shipping_country_id'] == $order['billing_country_id'] ) {
			$order['shipping_country'] = $order['billing_country'];
		} elseif ( isset( $order['shipping_country'] ) && strlen( $order['shipping_country'] ) == 0 ) {
			$country_ship = TCPCountries::get( $order['shipping_country_id'] );
			if ( $country_ship ) $order['shipping_country'] = $country_ship->name;
			else $order['shipping_country'] = '';
		}
		$order_id = Orders::insert( $order );
		$shoppingCart->setOrderId( $order_id );//since 1.1.0
		do_action( 'tcp_checkout_create_order_insert', $order_id, $order );
		foreach( $shoppingCart->getItems() as $item ) {
			$post = get_post( $item->getPostId() );
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
			$ordersDetails['is_downloadable']	= $item->isDownloadable() ? 'Y' : 'N';
			$ordersDetails['sku']				= $item->getSKU(); //tcp_get_the_sku( $item->getPostId(), $item->getOption1Id(), $item->getOption2Id() );
			$ordersDetails['name']				= tcp_get_the_title( $post->ID );
			$ordersDetails['option_1_name']		= $item->getOption1Id() > 0 ? get_the_title( $item->getOption1Id() ) : '';
			$ordersDetails['option_2_name']		= $item->getOption2Id() > 0 ? get_the_title( $item->getOption2Id() ) : '';
			if ( ! tcp_is_display_prices_with_taxes() ) $discount = $item->getDiscount() / $item->getUnits();
			else $discount = 0;
			$ordersDetails['price']				= tcp_get_the_price_without_tax( $item->getPostId(), $item->getUnitPrice() ) - $discount;
			$ordersDetails['original_price']	= $item->getUnitPrice();
			$ordersDetails['tax']				= $item->getTax(); //tcp_get_the_tax( $item->getPostId() );
			$ordersDetails['qty_ordered']		= $item->getCount();
			$ordersDetails['max_downloads']		= get_post_meta( $post->ID, 'tcp_max_downloads', true );
			$ordersDetails['expires_at']		= $expires_at;
			$ordersDetails['discount']			= $item->getDiscount();
			$orders_details_id = OrdersDetails::insert( $ordersDetails );
			if ( $item->has_attributes() ) tcp_update_order_detail_meta( $orders_details_id, 'tcp_attributes', $item->get_attributes() );
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
		return $order_id;
	}
	
	function show_payment_area( $order_id ) {
		$shipping_country = tcp_get_shipping_country();
		$shoppingCart = TheCartPress::getShoppingCart();
		ob_start(); ?>
		<div class="tcp_payment_area">

		<?php do_action( 'tcp_checkout_ok', $order_id ); ?>

		<?php if ( isset( $_SESSION['tcp_checkout']['payment_methods']['payment_method_id'] ) ) :
			$pmi = $_SESSION['tcp_checkout']['payment_methods']['payment_method_id'];
			$pmi = explode( '#', $pmi );
			$class = $pmi[0];
			$instance = $pmi[1];
			$payment_method = new $class();
			if ( $payment_method->sendPurchaseMail() ) {
				global $thecartpress;
				$send_email				= $thecartpress->get_setting( 'send_email', true ); //to merchant
				$send_email_customer	= $thecartpress->get_setting( 'send_email_customer', $send_email );
				ActiveCheckout::sendOrderMails( $order_id, '', $send_email_customer, $send_email );
			}
			do_action( 'tcp_checkout_calculate_other_costs' ); ?>
			<div class="tcp_plugin_notice">
				<?php $msg = tcp_do_template( 'tcp_payment_plugins_' . $class, false );
				if ( strlen( $msg ) > 0 ) {
					echo $msg;
				} else {
					echo apply_filters( 'tcp_checkout_ok_message', __( 'Please continue checking out using your chosen payment method.', 'tcp' ), $order_id );
				} ?>
			</div>
			<div class="tcp_pay_form">
				<?php $payment_method->showPayForm( $instance, $shipping_country, $shoppingCart, $order_id ); ?>
			</div>
		<?php endif; ?>
		<?php OrderPage::show( $order_id, array( 'see_sku' => true ) ); ?>
			<br />
			<a href="<?php echo add_query_arg( 'action', 'tcp_print_order', add_query_arg( 'order_id', $order_id, admin_url( 'admin-ajax.php' ) ) ); ?>" target="_blank"><?php _e( 'Print', 'tcp' ); ?></a>
		</div><!-- tcp_payment_area--><?php
		//$shoppingCart->deleteAll();//removed since 1.1.0
		return ob_get_clean();
	}

	protected function get_shipping_country() {
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

	protected function createNewBillingAddress( $order ) {
		if ( $order['customer_id'] > 0 ) {
			$address = array();
			$address['customer_id']	= $order['customer_id'];
			$address['default_shipping']	= 'N';
			$address['default_billing']		= 'Y';
			$address['name']		= __( 'billing address', 'tcp' );//title
			$address['firstname']	= $order['billing_firstname'];
			$address['lastname']	= $order['billing_lastname'];
			$address['company']		= $order['billing_company'];
			$address['tax_id_number']	= $order['billing_tax_id_number'];
			$address['street']		= $order['billing_street'];
			$address['street_2']	= $order['billing_street_2'];
			$address['city']		= $order['billing_city'];
			$address['city_id']		= $order['billing_city_id'];
			$address['region_id']	= $order['billing_region_id'];
			$address['region']		= $order['billing_region'];
			$address['postcode']	= $order['billing_postcode'];
			$address['country']		= $order['billing_country'];
			$address['country_id']	= $order['billing_country_id'];
			$address['telephone_1']	= $order['billing_telephone_1'];
			$address['telephone_2']	= $order['billing_telephone_2'];
			$address['fax']			= $order['billing_fax'];
			$address['email']		= $order['billing_email'];
			Addresses::save($address);
		}
	}

	function createNewShippingAddress( $order ) {
		if ( $order['customer_id'] > 0 ) {
			$address = array();
			$address['customer_id']			= $order['customer_id'];
			$address['default_shipping']	= 'Y';
			$address['default_billing']		= 'N';
			$address['name']				= __( 'shipping address', 'tcp' );
			$address['firstname']			= $order['shipping_firstname'];
			$address['lastname']			= $order['shipping_lastname'];
			$address['company']				= $order['shipping_company'];
			$address['street']				= $order['shipping_street'];
			$address['street_2']			= $order['shipping_street_2'];
			$address['city']				= $order['shipping_city'];
			$address['city_id']				= $order['shipping_city_id'];
			$address['region_id']			= $order['shipping_region_id'];
			$address['region']				= $order['shipping_region'];
			$address['postcode']			= $order['shipping_postcode'];
			$address['country']				= $order['shipping_country'];
			$address['country_id']			= $order['shipping_country_id'];
			$address['telephone_1']			= $order['shipping_telephone_1'];
			$address['telephone_2']			= $order['shipping_telephone_2'];
			$address['fax']					= $order['shipping_fax'];
			$address['email']				= $order['shipping_email'];
			Addresses::save( $address );
		}
	}
}
endif; // class_exists check