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

global $thecartpress;
$disable_shopping_cart	= $thecartpress->get_setting( 'disable_shopping_cart' );
$after_add_to_cart		= $thecartpress->get_setting( 'after_add_to_cart', '' );
$action					= $after_add_to_cart == 'ssc' ? get_permalink( tcp_get_current_id( get_option( 'tcp_shopping_cart_page_id', 0 ), 'page' ) ) : '';
?>

<?php /**** Start editing to customise your buy buttons! */ ?>

<div class="tcp_buy_button_area <?php echo implode( ' ' , apply_filters( 'tcp_buy_button_get_product_classes', array(), $post_id ) ); ?>">
<form method="post" id="tcp_frm_<?php echo $post_id; ?>" action="<?php echo $action; ?>">

<table class="tcp_buy_button">
<thead>
<tr>
	<th scope="col"><?php _e( 'Price', 'tcp' ); ?></th>
	<th scope="col"><?php _e( 'Units', 'tcp' ); ?></th>
</tr>
</thead>
<tbody>
<tr>

<td class="tcp_buy_button_price">

	<?php if ( function_exists( 'tcp_the_buy_button_options' ) && tcp_has_options( $post_id ) ) : ?>  

		<?php echo tcp_the_buy_button_options( $post_id ); ?>

	<?php else : ?>

		<span class="tcp_unit_price" id="tcp_unit_price_<?php echo $post_id; ?>">

		<?php echo tcp_get_the_price_label( $post_id ); ?>

		</span>

	<?php endif; ?>

	<?php if ( function_exists( 'tcp_has_dynamic_options' ) && tcp_has_dynamic_options( $post_id ) ) : ?>

		<?php tcp_the_buy_button_dyamic_options( $post_id ); ?>

	<?php endif; ?>

</td>

<td class="tcp_buy_button_count">

	<?php if ( ! tcp_hide_buy_button( $post_id ) && ! $disable_shopping_cart ) : ?>

		<?php tcp_the_add_to_cart_unit_field( $post_id ); ?>

		<?php tcp_the_add_to_cart_button( $post_id ); ?>

		<?php tcp_the_add_to_cart_items_in_the_cart( $post_id ); ?>

	<?php endif; ?>

	<?php tcp_the_add_wishlist_button( $post_id ) ; ?>

</td>

</tr>
</tbody>
</table>
</form>
</div>
