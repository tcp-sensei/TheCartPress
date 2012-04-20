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

<div class="tcp_buy_button_area cf <?php echo implode( ' ' , apply_filters( 'tcp_buy_button_get_product_classes', array(), $post_id ) ); ?>">
<form method="post" id="tcp_frm_<?php echo $post_id; ?>" action="<?php echo $action; ?>">

<?php do_action( 'tcp_buy_button_top', $post_id ); ?>


<div class="tcp_buy_button  tcp_buy_button_simple">

	<?php if ( function_exists( 'tcp_the_buy_button_options' ) && tcp_has_options( $post_id ) ) : ?>  

		<div class="tcp-buy-options">

			<?php echo tcp_the_buy_button_options( $post_id ); ?>

		</div>		 

	<?php else : ?>

		<div class="tcp_unit_price" id="tcp_unit_price_<?php echo $post_id; ?>">

			<?php echo tcp_get_the_price_label( $post_id ); ?>

		</div>

	<?php endif; ?>	

	<?php if ( function_exists( 'tcp_has_dynamic_options' ) && tcp_has_dynamic_options( $post_id ) ) : ?>
	 
		<div class="tcp-buy-dynamic-options">

			<?php tcp_the_buy_button_dyamic_options( $post_id ); ?>

		</div>

	<?php endif; ?>

	<?php if ( ! tcp_hide_buy_button( $post_id ) && ! $disable_shopping_cart ) : ?>

		<div class="tcp-add-to-cart">
			<?php tcp_the_add_to_cart_unit_field( $post_id ); ?>
		   
			<?php tcp_the_add_to_cart_button( $post_id ); ?>

			<div class="tcp-add-to-wishlist">
			
				<?php tcp_the_add_wishlist_button( $post_id ) ; ?>

			</div>

			<?php tcp_the_add_to_cart_items_in_the_cart( $post_id ); ?>

		</div>
		   
	<?php endif; ?>

		   
	<?php if ( function_exists( 'tcp_the_tier_price' ) ) tcp_the_tier_price(); ?>
		   
</div>
<?php do_action( 'tcp_buy_button_bottom', $post_id ); ?>

</form>
</div>
