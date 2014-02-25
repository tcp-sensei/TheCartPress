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
 * Outputs the buy-button area
 *
 * @since 1.1.8
 * @uses tcp_get_the_buy_button
 */
function tcp_the_buy_button( $post_id = 0 ) {
	echo tcp_get_the_buy_button( $post_id );
}
	/**
	 * Returns the buy-button area
	 *
	 * @since 1.1.8
	 * @uses apply_filters (tcp_the_buy_button)
	 */
	function tcp_get_the_buy_button( $post_id = 0 ) {
		if ( $post_id == 0 ) $post_id = get_the_ID();
		return apply_filters( 'tcp_the_buy_button', TCPBuyButton::show( $post_id, false ), $post_id );
	}

/**
 * Outputs the button, for the buy-button area
 *
 * @since 1.1.8
 */
function tcp_the_add_to_cart_button( $post_id, $title = '', $echo = true ) {
	ob_start(); ?>
	<input type="hidden" name="tcp_post_id[]" id="tcp_post_id_<?php echo $post_id; ?>" value="<?php echo $post_id; ?>" />
	<?php if ( strlen( $title ) == 0 ) $title = apply_filters( 'tcp_the_add_to_cart_button_title', __( 'Add to cart', 'tcp' ), $post_id ); ?>
	<button type="submit" name="tcp_add_to_shopping_cart" id="tcp_add_to_shopping_cart_<?php echo $post_id; ?>" class="tcp_add_to_shopping_cart tcp_add_to_shopping_cart_<?php echo tcp_get_the_product_type( $post_id ); ?> <?php tcp_the_buy_button_color(); ?> <?php tcp_the_buy_button_size(); ?>" target="<?php echo $post_id; ?>"><?php echo $title; ?></button>
	<?php $out = apply_filters( 'tcp_the_add_to_cart_button', ob_get_clean(), $post_id );
	if ( $echo ) echo $out;
	else return $out;
}

/**
 * Displays the unit field add to cart
 *
 * @since 1.1.8
 */
function tcp_the_add_to_cart_unit_field( $post_id, $units = 1, $hidden = false, $echo = true ) {
	ob_start(); 
	if ( $units == 0 ) $units = 1;
	$type = $hidden === true ? 'hidden' : 'number'; ?>
	<input type="<?php echo $type; ?>" min="0" step="1" name="tcp_count[]" id="tcp_count_<?php echo $post_id; ?>" value="<?php echo $units; ?>" class="tcp_count form-control" />
	<?php $out = apply_filters( 'tcp_the_add_to_cart_unit_field', ob_get_clean(), $post_id );
	if ( $echo ) echo $out;
	else return $out;
}

/**
 * Displays the text x items in the cart
 *
 * @param unknown_type $post_id
 * @since 1.1.8
 */
function tcp_the_add_to_cart_items_in_the_cart( $post_id, $echo = true ) {
	$shoppingCart = TheCartPress::getShoppingCart();
	$item = $shoppingCart->getItem( tcp_get_default_id( $post_id, get_post_type( $post_id ) ) );
	ob_start(); ?>
<div class="tcp_added_product_title tcp_added_product_title_<?php echo $post_id; ?> alert alert-info"
<?php if ( !$item ) : ?>style="display:none;"<?php endif; ?>>
	<?php if ( $item ) printf ( __( '<span class="tcp_units">%s</span> <a href="%s" class="alert-link">in your cart</a>', 'tcp' ), $item->getCount(), tcp_get_the_shopping_cart_url() ); ?>
</div>
	<?php
	$out = apply_filters( 'tcp_the_add_to_cart_items_in_the_cart', ob_get_clean(), $post_id );
	if ( $echo ) echo $out;
	else return $out;
}

/**
 * Outputs the selected color for buttons
 *
 * @since 1.3.4.1
 *
 * @params string $suffix, '-' + post type 
 * @params string $default, values by default if empty ('tcp-btn tcp-btn-info')
 * @uses tcp_get_buy_button_color
 */
function tcp_the_buy_button_color( $suffix = '', $default = 'tcp-btn tcp-btn-info' ) {
	echo tcp_get_buy_button_color( $suffix, $default );
}
	/**
	 * Returns the selected color for buttons
	 *
	 * @since 1.3.4
	 *
	 * @params string $suffix, '-' + post type 
	 * @params string $default, values by default if empty ('tcp-btn tcp-btn-info')
	 * @uses $thecartpress->get_setting
	 */
	function tcp_get_buy_button_color( $suffix = '', $default = 'tcp-btn tcp-btn-info' ) {
		global $thecartpress;
		$buy_button_color = $thecartpress->get_setting( 'buy_button_color' . $suffix, $default );
		return apply_filters( 'tcp_get_buy_button_color', $buy_button_color, $suffix, $default );
	}

/**
 * Outputs the selected size for buttons
 *
 * @since 1.3.4.1
 *
 * @params string $suffix, '-' + post type , to get specific values for post types
 * @params string $default, values by default if empty ('')
 * @uses $thecartpress->get_setting
 */
function tcp_the_buy_button_size( $suffix = '', $default = '' ) {
	echo tcp_get_buy_button_size( $suffix, $default );
}
	/**
	 * Returns the selected size for buttons
	 *
	 * @since 1.3.4
	 *
	 * @params string $suffix, '-' + post type , to get specific values for post types
	 * @params string $default, values by default if empty ('')
	 * @uses tcp_get_buy_button_size
	 */
	function tcp_get_buy_button_size( $suffix = '', $default = '' ) {
		global $thecartpress;
		$buy_button_size = $thecartpress->get_setting( 'buy_button_size' . $suffix, $default );
		return apply_filters( 'tcp_get_buy_button_size', $buy_button_size, $suffix, $default );
	}