<?php
/**
 * ShoppingCart shortcodes
 *
 * Defines two shortcodes for the Shopiing Cart, to show the shopping cart and a button to go to the shopping cart
 *
 * @package TheCartPress
 * @subpackage Shortcodes
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

if ( ! class_exists( 'TCPShoppingCartPage' ) ) {

class TCPShoppingCartPage {

	static function show( $notice = '' ) {
		$shoppingCart = TheCartPress::getShoppingCart();
		require_once( TCP_CLASSES_FOLDER . 'CartTable.class.php' );
		require_once( TCP_CLASSES_FOLDER . 'CartSourceSession.class.php' );
		ob_start(); ?>
<div class="tcp_shopping_cart_page "><!-- .tcpf -->
	<?php if ( $shoppingCart->isEmpty() ) { ?>
	<span class="tcp_shopping_cart_empty"><?php echo __( 'The cart is empty', 'tcp' );?></span>
	<?php tcp_do_template( 'tcp_shopping_cart_empty' ); ?>
	<?php do_action( 'tcp_shopping_cart_empty' ); ?>
	<?php } else { ?>
	<div id="shopping_cart">
		<?php if ( is_array( $notice ) && count( $notice ) > 0 ) { ?>
		<p class="tcp_shopping_cart_notice">
			<?php foreach( $notice as $not ) echo $not, '<br/>'; ?>
		</p>
		<?php } elseif ( strlen( $notice ) > 0 ) { ?>
		<p class="tcp_shopping_cart_notice"><?php echo $notice; ?></p>
		<?php }
	do_action( 'tcp_shopping_cart_before_cart' );
	$cart_table = new TCPCartTable();
	$cart_table->show( new TCPCartSourceSession() );
	global $thecartpress;
	$buy_button_color = $thecartpress->get_setting( 'buy_button_color' );
	$buy_button_size = $thecartpress->get_setting( 'buy_button_size' );
	do_action( 'tcp_shopping_cart_after_cart' );
	//links at the bottom of the Shopping Cart
	$links = array(
		'tcp_checkout' => array(
			'li_class'	=> 'tcp_sc_checkout',
			'a_class'	=> $buy_button_color . ' ' . $buy_button_size,
			'url'		=> tcp_get_the_checkout_url(),
			'label'		=> __( 'Checkout', 'tcp' )
		),
		'tcp_continue'	=> array(
			'li_class'	=> 'tcp_sc_continue',
			'a_class'	=> $buy_button_size,
			'url'		=> tcp_get_the_continue_url(),
			'label'		=> __( 'Continue Shopping', 'tcp' )
		),
	);
	$links = apply_filters( 'tcp_shopping_cart_bottom_links', $links ); ?>
	<div class="tcpf">
		<ul class="tcp_sc_links">
		<?php foreach( $links as $link ) { ?>
			<li class="<?php echo $link['li_class']; ?>">
				<a href="<?php echo $link['url']; ?>" class="<?php echo $link['a_class']; ?>"><?php echo $link['label']; ?></a>
			</li>
		<?php } ?>
		</ul>
	</div><!-- .tcpf -->
	</div><!-- #shopping_cart -->
	<?php } ?>
	<?php do_action( 'tcp_shopping_cart_footer' ); ?>
</div><!-- .tcp_shopping_cart_page.tcpf -->
<?php do_action( 'tcp_shopping_cart_after' );
		return ob_get_clean();
	}

	static function show_button() {
		global $thecartpress;
		$buy_button_color	= $thecartpress->get_setting( 'buy_button_color' );
		$buy_button_size	= $thecartpress->get_setting( 'buy_button_size' );
		ob_start(); ?>
<div class="tcp-shopping-cart-direct-link">
	<a href="<?php tcp_the_shopping_cart_url(); ?>" class="tcp-btn <?php echo $buy_button_color, ' ', $buy_button_size; ?>"><?php echo apply_filters( 'tcp_shopping_cart_button_title', __( 'See Your Shopping Cart', 'tcp' ) ); ?></a>
</div>
		<?php return ob_get_clean();
	}
}

add_shortcode( 'tcp_shopping_cart'			, 'TCPShoppingCartPage::show' );
add_shortcode( 'tcp_shopping_cart_button'	, 'TCPShoppingCartPage::show_button' );
} // class_exists check