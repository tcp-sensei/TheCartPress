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

/**
 * Displays a buy button
 * @since 1.1.8
 */
function tcp_the_add_to_cart_button( $post_id, $echo = true ) {
	ob_start();
	if ( tcp_get_the_product_type( $post_id ) == 'SIMPLE' ) : ?>
		<input type="hidden" name="tcp_post_id[]" id="tcp_post_id_<?php echo $post_id; ?>" value="<?php echo $post_id; ?>" />
		<input type="submit" name="tcp_add_to_shopping_cart" class="tcp_add_to_shopping_cart" id="tcp_add_product_<?php echo $post_id; ?>" value="<?php _e( 'Add to cart', 'tcp' ); ?>"/>
	<?php endif;
	$out = ob_get_clean();
	$out = apply_filters( 'tcp_the_add_to_cart_button', $out, $post_id );
	if ( $echo ) echo $out;
	else return $out;
}

/**
 * Displays the unit field add to cart
 * @since 1.1.8
 */
function tcp_the_add_to_cart_unit_field( $post_id, $units = 1, $echo = true ) {
	ob_start();
	if ( tcp_get_the_product_type( $post_id ) == 'SIMPLE' ) : ?>
		<input type="number" min="0" step="1" name="tcp_count[]" id="tcp_count_<?php echo $post_id; ?>" value="<?php echo $units; ?>" class="tcp_count" size="3" />
	<?php endif;
	$out = ob_get_clean();
	$out = apply_filters( 'tcp_the_add_to_cart_unit_field', $out, $post_id );
	if ( $echo ) echo $out;
	else return $out;
}

/**
 * Displays the text x items in the cart
 * @param unknown_type $post_id
 * @since 1.1.8
 */
function tcp_the_add_to_cart_items_in_the_cart( $post_id, $echo = true ) {
	ob_start();
	$shoppingCart = TheCartPress::getShoppingCart();
	$item = $shoppingCart->getItem( tcp_get_default_id( $post_id, get_post_type( $post_id ) ) );
	if ( $item ) : ?>
		<span class="tcp_added_product_title"><?php printf ( __( '%s unit(s) <a href="%s">in your cart</a>', 'tcp' ), $item->getCount(), tcp_get_the_shopping_cart_url() ); ?></span>
	<?php endif;
	$out = ob_get_clean();
	$out = apply_filters( 'tcp_the_add_to_cart_items_in_the_cart', $out, $post_id );
	if ( $echo ) echo $out;
	else return $out;
}
?>