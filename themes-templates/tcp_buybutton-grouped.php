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
$activate_ajax			= $thecartpress->get_setting( 'activate_ajax', true );
$disable_shopping_cart	= $thecartpress->get_setting( 'disable_shopping_cart' );
$after_add_to_cart		= $thecartpress->get_setting( 'after_add_to_cart', '' );
if ( $after_add_to_cart == 'ssc' ) {
	$action = get_permalink( tcp_get_current_id( get_option( 'tcp_shopping_cart_page_id', '' ), 'page' ) );
} elseif ( $after_add_to_cart == 'sco' ) {
	$action = get_permalink( tcp_get_current_id( get_option( 'tcp_checkout_page_id', '' ), 'page' ) );
} else {
	$action = '';
}
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
	}
</script>

<?php /**** Start editing to customise your buy buttons! */ ?>

<div class="tcp_buy_button_area tcp_buy_button_grouped tcp_buy_button_<?php echo get_post_type(); ?> tcpf <?php echo implode( ' ' , apply_filters( 'tcp_buy_button_get_product_classes', array(), $post_id ) ); ?>">

<form method="post" id="tcp_frm_<?php echo $post_id; ?>" action="<?php echo $action; ?>">

<?php do_action( 'tcp_buy_button_top', $post_id ); ?>

<div class="tcp_buy_button tcp_buy_button_grouped">
<?php foreach( $products as $product ) :
	$meta_value	= unserialize( $product->meta_value );
	$units		= isset( $meta_value['units'] ) ? (int)$meta_value['units'] : 0;
	$product_id	= tcp_get_current_id( $product->id_to, get_post_type( $product->id_to ) ); 
	if ( get_post_status( $product_id ) == 'publish' ) : ?>

	<div class="tcp_buy_button_simple_item tcp_buy_button_simple_item_<?php echo $post_id; ?>_<?php echo $product_id; ?> tcpf">

		<div class="tcp_buy_button_name">
			<?php if ( tcp_is_visible( $product_id ) ) : ?>
				<a href="<?php echo get_permalink( $product_id ); ?>"><?php echo tcp_get_the_title( $product_id, 0, 0, true, false ); ?></a>
			<?php else : ?>
				<?php echo tcp_get_the_title( $product_id, 0, 0, true, false ); ?>
			<?php endif; ?>
		</div>
	
		<div class="tcp_buy_button_thumbnail">
			<?php $args = array(
				'size'	=> $thecartpress->get_setting( 'image_size_grouped_by_button', 'thumbnail' ),
				'align'	=> '',
				'link'	=> 'permalink',
			);
			if ( ! tcp_is_visible( $product_id ) ) $args['link'] = 'file';
			$image = tcp_get_the_thumbnail_with_permalink( $product_id, $args, false );
			echo apply_filters( 'tcp_get_image_in_grouped_buy_button', $image, $product_id ); ?>
		</div>

		<div class="tcp_buy_button_main tcpf">
			<?php if ( function_exists( 'tcp_the_buy_button_options' ) ) : ?>
				<?php echo tcp_the_buy_button_options( $product_id, $post_id ); ?>
			<?php endif; ?>
			<?php if ( ! ( function_exists( 'tcp_has_options' ) && tcp_has_options( $product_id ) ) ) : ?>
			<div class="tcp_unit_price" id="tcp_unit_price_<?php echo $product_id; ?>">
				<?php echo tcp_get_the_price_label( $product_id ); ?>
			</div>
			<?php endif; ?>
	
			<?php if ( function_exists( 'tcp_the_buy_button_dyamic_options' ) && tcp_has_dynamic_options( $product_id ) ) : ?>
			<div class="tcp-buy-dynamic-options">
				<?php tcp_the_buy_button_dyamic_options( $product_id ); ?>
			</div><!-- .tcp-buy-dynamic-options -->
			<?php endif; ?>

			<div class="tcp-add-to-cart">

			<?php if ( ! $disable_shopping_cart ) tcp_the_add_to_cart_unit_field( $product_id, $units ); ?>

				<?php if ( ! $disable_shopping_cart && !tcp_hide_buy_button( $product_id ) ) : ?>

					<?php tcp_the_add_to_cart_button( $product_id ); ?> 

					<?php if ( ! $activate_ajax ) : ?>

					<script type="text/javascript">
						jQuery('#tcp_add_to_shopping_cart_<?php echo $product_id; ?>').click(function() {
							add_to_the_cart_<?php echo $post_id; ?>(<?php echo $product_id; ?>);
						});
					</script>

					<?php endif; ?>

					<?php tcp_the_add_to_cart_items_in_the_cart( $product_id ); ?>
		
				<?php endif; ?>

			</div><!-- .tcp_buy_button_main -->

			<?php if ( function_exists( 'tcp_the_tier_price' ) && tcp_has_tier_price( $product_id ) ) : ?>

				<a href="#" class="tcp_view_tier_price" product_id="<?php echo $product_id; ?>" title="<?php _e( 'View/hide tier price', 'tcp' ); ?>"><?php _e( 'View/hide tier price', 'tcp' ); ?></a>

				<?php tcp_the_tier_price( $product_id ); ?>

			<?php endif; ?>

			<div class="tcp_buy_button_excerpt">
				<?php //tcp_the_excerpt( $product_id ); ?>
			</div> 
			
			<div class="tcp_buy_button_content">
				<?php //tcp_the_content( $product_id ); ?>
			</div>
			
			<?php do_action( 'tcp_buy_button_item', $product_id ); ?>

	</div> <!--end tcp_buy_button_main-->		  
		  
	<?php endif; ?>

</div> <!--end tcp_buy_button_simple_item-->

<?php endforeach; ?>

</div> <!--end tcp_buy_button-->

<div>
	<?php if ( ! $disable_shopping_cart && !tcp_hide_buy_button( $post_id ) ) : ?>

	<span class="tcp_add_selected_to_cart">

		<?php tcp_the_add_to_cart_button( $post_id ); ?>

	</span>

		<?php tcp_the_add_to_cart_items_in_the_cart( $post_id ); ?>

	<?php endif; ?>

	<div class="tcp-add-to-wishlist">
		<?php tcp_the_add_wishlist_button( $post_id ) ; ?>
	</div>

</div>

<?php do_action( 'tcp_buy_button_bottom', $post_id ); ?>

</form>
</div> <!--end tcp_buy_button_area-->

<?php $activate_hide_tier_price = true;
if ( $activate_hide_tier_price && function_exists( 'tcp_the_tier_price' ) ) : ?>
<script>
jQuery(document).ready(function() {
	jQuery('.tcp_tier_price').hide();
	jQuery('.tcp_view_tier_price').click(function() {
		var product_id = jQuery(this).attr('product_id');
		var cl = '.tcp_tier_price_' + product_id;
		if (jQuery(cl).is(":visible")) {
			jQuery(cl).hide();
		} else {
			jQuery('.tcp_tier_price').hide();
			jQuery(cl).show();
		}
		return false;
	});
});
</script>
<?php endif;
