<?php
/**
 * Default Buy button Template
 *
 * It's used for Simple products and for any other type of products taht haven't a template
 *
 * @package TheCartPress
 * @subpackage Themes-Templates
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

global $thecartpress;
$disable_shopping_cart	= $thecartpress->get_setting( 'disable_shopping_cart' );
$after_add_to_cart		= $thecartpress->get_setting( 'after_add_to_cart', '' );
if ( $after_add_to_cart == 'ssc' ) {
	$action				= get_permalink( tcp_get_current_id( get_option( 'tcp_shopping_cart_page_id', '' ), 'page' ) );
} elseif ( $after_add_to_cart == 'sco' ) {
	$action				= get_permalink( tcp_get_current_id( get_option( 'tcp_checkout_page_id', '' ), 'page' ) );
} else {
	$action				= '';
}

/**** Start editing to customise your buy buttons! */ ?>
<div class="tcp_buy_button_area tcp_buy_button_simple tcp_buy_button_<?php echo get_post_type(); ?> tcpf <?php echo implode( ' ' , apply_filters( 'tcp_buy_button_get_product_classes', array(), $post_id ) ); ?>">
	<form method="post" id="tcp_frm_<?php echo $post_id; ?>" action="<?php echo $action; ?>" class="form-inline">
		<div class="tcp_buy_button tcp_buy_button_simple">
		<?php do_action( 'tcp_buy_button_top', $post_id ); ?>
		
		<?php if ( function_exists( 'tcp_the_buy_button_options' ) && tcp_has_options( $post_id ) ) : ?>  
			<div class="tcp-buy-options form-group">
				<?php echo tcp_the_buy_button_options( $post_id ); ?>
			</div>
		<?php else : ?>
			<div class="tcp_unit_price form-group" id="tcp_unit_price_<?php echo $post_id; ?>">
				<?php echo tcp_get_the_price_label( $post_id ); ?>
			</div>
		<?php endif; ?>
		<?php if ( function_exists( 'tcp_has_dynamic_options' ) && tcp_has_dynamic_options( $post_id ) ) : ?>
			<div class="tcp-buy-dynamic-options">
				<div class="form-group">
					<?php tcp_the_buy_button_dynamic_options( $post_id ); ?>
				</div><!-- .form-group -->
			</div><!-- .tcp-buy-dynamic-options -->
		<?php endif; ?>
		<?php if ( !tcp_hide_buy_button( $post_id ) && ! $disable_shopping_cart ) : ?>
			<div class="tcp-add-to-cart">
				<div class="form-group">
					<?php tcp_the_add_to_cart_unit_field( $post_id, tcp_get_the_initial_units( $post_id ) ); ?>
				</div>
				<div class="form-group">
					<?php tcp_the_add_to_cart_button( $post_id ); ?>
				</div>
				<div class="tcp-add-to-wishlist form-group">
					<?php tcp_the_add_wishlist_button( $post_id ) ; ?>
				</div>	
			</div><!-- .tcp-add-to-cart -->
			<?php tcp_the_add_to_cart_items_in_the_cart( $post_id ); ?>
		<?php endif; ?>
		<?php if ( function_exists( 'tcp_the_tier_price' ) ) tcp_the_tier_price( $post_id ); ?>
		<?php do_action( 'tcp_buy_button_bottom', $post_id ); ?>
		</div><!-- .tcp_buy_button .tcp_buy_button_simple -->
	</form>
</div><!-- .tcp_buy_button_area .tcp_buy_button_simple -->