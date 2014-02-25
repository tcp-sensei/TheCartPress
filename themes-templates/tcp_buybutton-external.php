<?php
/**
 * Buy button Template for External/Affiliate products
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

$action = tcp_get_the_product_url( $post_id );
$title	= tcp_string( 'TheCartPress', 'external_button_text-' . $post_id, tcp_get_the_buy_button_text( $post_id ) );
if ( strlen( $title ) == 0 ) {
	$title = apply_filters( 'tcp_the_add_to_cart_button_title', __( 'Add to cart', 'tcp' ), $post_id );
}

/**** Start editing to customise your buy buttons! */ ?>
<div class="tcp_buy_button_area tcp_buy_button_external tcp_buy_button_<?php echo get_post_type(); ?> tcpf
	<?php echo implode( ' ' , apply_filters( 'tcp_buy_button_get_product_classes', array(), $post_id ) ); ?>">
	<form method="post" id="tcp_frm_<?php echo $post_id; ?>" action="<?php echo $action; ?>" class="form-inline" target="_blank">
		<div class="tcp_buy_button tcp_buy_button_external">
		<?php do_action( 'tcp_buy_button_top', $post_id ); ?>
		<?php $price = tcp_get_the_price( $post_id );
		if ( $price > 0 ) : ?>
		<div class="tcp_unit_price form-group" id="tcp_unit_price_<?php echo $post_id; ?>">
			<?php echo tcp_get_the_price_label( $post_id, $price ); ?>
		</div>
		<?php endif; ?>
		<?php if ( !tcp_hide_buy_button( $post_id ) ) : ?>
			<div class="tcp-add-to-cart">
				<div class="form-group">
					<button type="submit" name="tcp_add_to_shopping_cart" id="tcp_add_to_shopping_cart_<?php echo $post_id; ?>"
					class="<?php tcp_the_buy_button_color(); ?> <?php tcp_the_buy_button_size(); ?>">
						<?php echo stripcslashes( $title ); ?>
					</button>
				</div>
				<div class="tcp-add-to-wishlist form-group">
					<?php tcp_the_add_wishlist_button( $post_id ) ; ?>
				</div>	
			</div><!-- .tcp-add-to-cart -->
		<?php endif; ?>
		<?php do_action( 'tcp_buy_button_bottom', $post_id ); ?>
		</div><!-- .tcp_buy_button .tcp_buy_button_simple -->
	</form>
</div><!-- .tcp_buy_button_area .tcp_buy_button_external -->