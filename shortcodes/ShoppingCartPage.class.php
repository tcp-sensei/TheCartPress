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

class TCPShoppingCartPage {

	function __construct() {
		add_shortcode( 'tcp_shopping_cart', array( $this, 'show' ) );
	}

	function show( $notice = '' ) {
		$shoppingCart = TheCartPress::getShoppingCart();
		require_once( dirname( dirname( __FILE__ ) ) . '/classes/CartTable.class.php' );
		require_once( dirname( dirname( __FILE__ ) ) . '/classes/CartSourceSession.class.php' );
		$cart_table = new TCPCartTable( ); 
		ob_start(); ?>
<div class="tcp_shopping_cart_page">
<?php  if ( $shoppingCart->isEmpty() ) : ?>
	<span class="tcp_shopping_cart_empty"><?php echo __( 'The cart is empty', 'tcp' );?></span>
	<?php tcp_do_template( 'tcp_shopping_cart_empty' ); ?>
	<?php do_action( 'tcp_shopping_cart_empty' ); ?>
<?php else : ?>
	<div class="entry-content" id="shopping_cart">
	<?php if ( is_array( $notice ) && count( $notice ) > 0 ) : ?>
	<p class="tcp_shopping_cart_notice">
	<?php foreach( $notice as $not ) : ?>
		<?php echo $not; ?><br/>
	<?php endforeach; ?>
	</p>
	<?php elseif ( strlen( $notice ) > 0 ) : ?>
		<p class="tcp_shopping_cart_notice"><?php echo $notice; ?></p>
	<?php endif;
	do_action( 'tcp_shopping_cart_before_cart' );
	$cart_table->show( new TCPCartSourceSession() );
	do_action( 'tcp_shopping_cart_after_cart' ); ?>
		<ul class="tcp_sc_links">
			<li class="tcp_sc_checkout"><a href="<?php tcp_the_checkout_url();?>"><?php _e( 'Checkout', 'tcp' );?></a></li>
			<li class="tcp_sc_continue"><a href="<?php tcp_the_continue_url();?>"><?php _e( 'Continue shopping', 'tcp' );?></a></li>
			<?php do_action( 'tcp_shopping_cart_after_links' );?>
		</ul>
	</div><!-- .entry-content -->
	<?php endif; ?>
</div><!-- .tcp_shopping_cart_page -->
<?php return ob_get_clean();
	}
}

new TCPShoppingCartPage();
?>