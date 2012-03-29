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
$products 				= RelEntities::select( $post_id );
global $wish_list;
remove_filter( 'tcp_the_add_to_cart_button', array( $wish_list, 'tcp_the_add_to_cart_button' ), 10, 2 ); ?>
<script type="text/javascript">
	function add_to_the_cart_<?php echo $post_id; ?>(id_to) {
		var count = jQuery("#tcp_count_" + id_to).val();
		if (count == 0) count = 1;
		jQuery("#tcp_frm_<?php echo $post_id; ?> .tcp_count").each(function(i) {
			jQuery(this).val(0);
		});
		jQuery("#tcp_count_" + id_to).val(count);
		jQuery("#tcp_add_selected_to_shopping_cart_<?php echo $post_id; ?>").click();
		//jQuery("#tcp_buy_button_form_<?php echo $post_id; ?>").submit();			
	}
</script>

<?php /**** Start editing to customise your buy buttons! */ ?>

<div class="tcp_buy_button_area <?php echo implode( ' ' , apply_filters( 'tcp_buy_button_get_product_classes', array(), $post_id ) ); ?>">
<form method="post" id="tcp_frm_<?php echo $post_id; ?>" action="<?php echo $action; ?>">

<table class="tcp_buy_button">
<tbody>
<?php foreach( $products as $product ) :
	$meta_value	= unserialize( $product->meta_value );
	$units		= isset( $meta_value['units'] ) ? (int)$meta_value['units'] : 0;
	$product_id	= tcp_get_current_id( $product->id_to, get_post_type( $product->id_to ) ); 
	if ( get_post_status( $product_id ) == 'publish' ) : ?>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('#tcp_add_product_<?php echo $product_id; ?>').click(function() {
					add_to_the_cart_<?php echo $post_id; ?>(<?php echo $product_id; ?>);
				});
			});
		</script>
		<tr>
		<td class="tcp_buy_button_thumbnail">

			<?php $image = tcp_get_the_thumbnail_with_permalink( $product_id, false, false ); ?>
			<?php echo apply_filters( 'tcp_get_image_in_grouped_buy_button', $image, $product_id ); ?>

		</td>
		<td class="tcp_buy_button_name">

		<?php if ( tcp_is_visible( $product_id ) ) : ?>

			<a href="<?php echo get_permalink( $product_id ); ?>"><?php echo tcp_get_the_title( $product_id, 0, 0, true, false ); ?></a>

		<?php else : ?>

			<?php echo tcp_get_the_title( $product_id, 0, 0, true, false ); ?>

		<?php endif; ?>

		<?php //tcp_the_excerpt( $product_id ); ?>
		<?php //tcp_the_content( $product_id ); ?>

		</td>
		<td class="tcp_buy_button_price">

			<?php if ( function_exists( 'tcp_the_buy_button_options' ) ) : ?>
			
				<?php echo tcp_the_buy_button_options( $product_id, $post_id ); ?>
				
			<?php endif; ?>

			<?php if ( ! ( function_exists( 'tcp_has_options' ) && tcp_has_options( $product_id ) ) ) : ?>

				<span class="tcp_unit_price" id="tcp_unit_price_<?php echo $product_id; ?>">
				<?php echo tcp_get_the_price_label( $product_id ); ?>
				</span>

			<?php endif; ?>

			<?php if ( function_exists( 'tcp_the_buy_button_dyamic_options' ) && tcp_has_dynamic_options( $product_id ) ) : ?>

				<?php tcp_the_buy_button_dyamic_options( $product_id ); ?>

			<?php endif; ?>

		</td>
		<td class="tcp_buy_button_count">

			<?php if ( ! $disable_shopping_cart ) tcp_the_add_to_cart_unit_field( $product_id, $units ); ?>
		
			<?php if ( ! tcp_hide_buy_button( $product_id ) ) : ?>

				<?php tcp_the_add_to_cart_button( $product_id ); ?>

				<?php tcp_the_add_to_cart_items_in_the_cart( $product_id ); ?>

			<?php endif; ?></td>

		</tr>

	<?php endif; ?>

<?php endforeach; ?>
</tbody>
</table>
<p>
<?php tcp_the_add_wishlist_button( $post_id ) ; ?>

<?php if ( ! tcp_hide_buy_button( $post_id ) ) : ?>

	<?php tcp_the_add_to_cart_button( $post_id ); ?>

	<?php tcp_the_add_to_cart_items_in_the_cart( $post_id ); ?>

<?php endif; ?>
</p>
</form>
</div>
