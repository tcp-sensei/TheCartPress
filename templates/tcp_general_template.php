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

function tcp_the_shopping_cart_url( $echo = true ) {
	$url = get_permalink( tcp_get_current_id( get_option( 'tcp_shopping_cart_page_id' ), 'page' ) );
	if ( $echo )
		echo $url;
	else
		return $url;
}

function tcp_get_the_shopping_cart_url() {
	return tcp_the_shopping_cart_url( false );
}

function tcp_the_checkout_url( $echo = true ) {
	$url = get_permalink( tcp_get_current_id( get_option( 'tcp_checkout_page_id' ), 'page' ) );
	if ( $echo )
		echo $url;
	else
		return $url;
}

function tcp_get_the_checkout_url() {
	return tcp_the_checkout_url( false );
}

function tcp_the_continue_url( $echo = true) {
	global $thecartpress;
	$url = isset( $thecartpress->settings['continue_url'] ) && strlen( $thecartpress->settings['continue_url'] ) > 0 ? $thecartpress->settings['continue_url'] : get_home_url();
	if ( $echo ) echo $url;
	else return $url;
}

function tcp_get_the_continue_url() {
	return tcp_the_continue_url( false );
}


/**
 * Display Taxonomy Tree.
 *
 * This function is primarily used by themes which want to hardcode the Taxonomy
 * Tree into the sidebar and also by the TaxonomyTree widget in TheCartPress.
 *
 * There is also an action that is called whenever the function is run called,
 * 'tcp_get_taxonomy_tree'.
 *
 * @since 1.0.7
 * @param array $args
 * @param boolean $echo Default to echo and not return the form.
 */
function tcp_get_taxonomy_tree( $args = false, $echo = true, $before = '', $after = '' ) {
	do_action( 'tcp_get_taxonomy_tree' );
	wp_parse_args( $args, array(
		'style'			=> 'list',
		'show_count'	=> true,
		'hide_empty'	=> true,
		'taxonomy'		=> 'tcp_product_category',
		'title_li'		=> '',
	) );
	ob_start();
	if ( isset( $args['dropdown'] ) && $args['dropdown'] ) { //TODO
		$args['show_option_none']	= sprintf ( __( 'Select %s', 'tcp' ), $args['taxonomy']);
		$args['name']				= $args['taxonomy'];
		$args['walker']				= new TCPWalker_CategoryDropdown(); ?>
		<?php echo wp_dropdown_categories( apply_filters( 'tcp_widget_taxonomy_tree_dropdown_args', $args ) ); ?>
<script type='text/javascript'>
// <![CDATA[
	var dropdown = document.getElementById("<?php echo $args['name']; ?>");
	function on_<?php echo $args['name']; ?>_change() {
		if ( dropdown.options[dropdown.selectedIndex].value != -1 )
			location.href = dropdown.options[dropdown.selectedIndex].value;
	}
	dropdown.onchange = on_<?php echo $args['name']; ?>_change;
// ]]>
</script>
	<?php } else { ?>
		<ul><?php echo wp_list_categories( apply_filters( 'tcp_widget_taxonomy_tree_args', $args ) ); ?></ul>
<script>
/*jQuery('ul.children').hide();
<?php $term = get_queried_object();
if ( isset( $term->term_id ) ) : ?>
	jQuery('li.cat-item-<?php echo $term->term_id; ?> ul').show();
	jQuery('li.cat-item-<?php echo $term->term_id; ?> ul').parents(function() {
		jQuery(this).show();
	});
<?php endif; ?>*/
</script>

	<?php }
	$tree = ob_get_clean();
	$tree = apply_filters( 'tcp_get_taxonomy_tree', $tree );
	if ( $echo )
		echo $before, $tree, $after;
	else
		return $before . $tree . $after;
}

class TCPWalker_CategoryDropdown extends Walker {
	var $tree_type = 'category';
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id');
	
	function start_el( &$output, $category, $depth, $args ) {
		$pad = str_repeat( '&nbsp;', $depth * 3 );

		$cat_name = apply_filters( 'list_cats', $category->name, $category );
		//$output .= "\t<option class=\"level-$depth\" value=\"" . $category->term_id . "\"";

		$output .= "\t<option class=\"level-$depth\" value=\"" . get_term_link( $category->term_id, $category->taxonomy ) . "\"";

		if ( $category->term_id == $args['selected'] )
			$output .= ' selected="selected"';
		$output .= '>';
		$output .= $pad . $cat_name;
		if ( $args['show_count'] )
			$output .= '&nbsp;&nbsp;('. $category->count .')';
		if ( $args['show_last_update'] ) {
			$format = 'Y-m-d';
			$output .= '&nbsp;&nbsp;' . gmdate( $format, $category->last_update_timestamp );
		}
		$output .= "</option>\n";
	}
}
/**
 *
 * Display Shopping Cart Detail.
 *
 * This function is primarily used by themes which want to hardcode the Detailed
 * Shopping Cart into the sidebar and also by the ShoppingCart widget
 * in TheCartPress.
 *
 * There is also an action that is called whenever the function is run called,
 * 'tcp_get_shopping_cart_detail'.
 *
 * @since 1.1.6
 * @param array $args
 * @param boolean $echo Default to echo and not return the form.
 */
function tcp_get_shopping_cart_detail( $args = false, $echo = true ) {
	do_action( 'tcp_get_shopping_cart_before', $args, $echo );
	$see_thumbnail		= isset( $args['see_thumbnail'] ) ? $args['see_thumbnail'] : false;
	$thumbnail_size		= isset( $args['thumbnail_size'] ) ? $args['thumbnail_size'] : 'thumbnail';
	if ( is_numeric( $thumbnail_size ) ) $thumbnail_size = array( $thumbnail_size, $thumbnail_size );
	$see_modify_item	= isset( $args['see_modify_item'] ) ? $args['see_modify_item'] : true;
	$see_weight			= isset( $args['see_weight'] ) ? $args['see_weight'] : true;
	$see_delete_item	= isset( $args['see_delete_item'] ) ? $args['see_delete_item'] : true;
	$see_delete_all		= isset( $args['see_delete_all'] ) ? $args['see_delete_all'] : true;
	$see_shopping_cart	= isset( $args['see_shopping_cart'] ) ? $args['see_shopping_cart'] : true;
	$see_checkout		= isset( $args['see_checkout'] ) ? $args['see_checkout'] : true; ?>
	<ul class="tcp_shopping_cart">
	<?php
	$shoppingCart = TheCartPress::getShoppingCart();
	foreach( $shoppingCart->getItems() as $item ) : ?>
		<li class="tcp_widget_cart_detail_item_<?php echo $item->getPostId(); ?>"><form method="post">
			<input type="hidden" name="tcp_post_id" value="<?php echo $item->getPostId(); ?>" />
			<input type="hidden" name="tcp_option_1_id" value="<?php echo $item->getOption1Id(); ?>" />
			<input type="hidden" name="tcp_option_2_id" value="<?php echo $item->getOption2Id(); ?>" />
			<input type="hidden" name="tcp_unit_price" value="<?php echo $item->getUnitPrice(); ?>" />
			<input type="hidden" name="tcp_tax" value="<?php echo $item->getTax(); ?>" />
			<input type="hidden" name="tcp_unit_weight" value="<?php echo $item->getWeight(); ?>" />
			<ul class="tcp_shopping_cart_widget">
				<?php $title = tcp_get_the_title( $item->getPostId(), $item->getOption1Id(), $item->getOption2Id() );
				$url = tcp_get_permalink( $item->getPostId(), $item->getOption1Id(), $item->getOption2Id() ); ?>
				<li class="tcp_cart_widget_item"><span class="tcp_name"><a href="<?php echo $url; ?>"><?php echo $title; ?></a></span></li>
				<?php if ( $see_thumbnail ) : ?>
					<li class="tcp_cart_widget_thumbnail"><?php echo tcp_get_the_thumbnail( $item->getPostId(), $item->getOption1Id(), $item->getOption2Id(), $thumbnail_size ); ?></li>
				<?php endif; ?>
				<li><span class="tcp_unit_price"><?php _e( 'Price', 'tcp' ); ?>:&nbsp;<?php echo tcp_format_the_price( $item->getPriceToshow() ); ?></span></li>
				<?php if ( ! tcp_is_downloadable( $item->getPostId() ) ) : ?>
				<li><?php if ( $see_modify_item ) :?>
						<input type="number" min="0" name="tcp_count" value="<?php echo $item->getCount(); ?>" size="2" maxlength="4" class="tcp_count"/>
						<input type="submit" name="tcp_modify_item_shopping_cart" class="tcp_modify_item_shopping_cart" value="<?php _e( 'Modify', 'tcp' ); ?>"/>
					<?php else : ?>
						<span class="tcp_units"><?php _e( 'Units', 'tcp' ); ?>:&nbsp;<?php echo $item->getCount(); ?></span>
					<?php endif; ?>
					<?php do_action( 'tcp_shopping_cart_widget_units', $item, $args ); ?>
				</li>
				<?php endif; ?>
				<?php if ( $item->getDiscount() > 0 ) : ?>
				<li><span class="tcp_discount"><?php _e( 'Discount', 'tcp' ); ?>:&nbsp;<?php echo tcp_format_the_price( $item->getDiscount() ); ?></span></li>
				<?php endif; ?>
				<li><span class="tcp_subtotal"><?php _e( 'Total', 'tcp' ); ?>:&nbsp;<?php echo tcp_format_the_price( $item->getTotalToShow() ); ?></span></li>
			<?php if ( ! tcp_is_downloadable( $item->getPostId() ) ) : ?>
				<?php if ( $see_weight && $item->getWeight() > 0 ) :?>
					<li><span class="tcp_weight"><?php _e( 'Weight', 'tcp' ); ?>:</span>&nbsp;<?php echo tcp_number_format( $item->getWeight() ); ?>&nbsp;<?php tcp_the_unit_weight(); ?></li>
				<?php endif; ?>
			<?php endif; ?>
			<?php do_action( 'tcp_shopping_cart_widget_item', $item ); ?>
			<?php if ( $see_delete_item ) :?>
				<li><input type="submit" name="tcp_delete_item_shopping_cart" class="tcp_delete_item_shopping_cart" value="<?php _e( 'Delete item', 'tcp' ); ?>"/></li>
			<?php endif; ?>
			<?php do_action( 'tcp_get_shopping_cart_widget_item', $args, $item ); ?>
			</ul>
		</form></li>
	<?php endforeach; ?>
	<?php $discount = $shoppingCart->getCartDiscountsTotal(); //$shoppingCart->getAllDiscounts();
	if ( $discount > 0 ) : ?>
		<li><span class="tcp_discount"><?php _e( 'Discount', 'tcp' ); ?>:&nbsp;<?php echo tcp_format_the_price( $discount ); ?></span></li>
	<?php endif; ?>
		<li><span class="tcp_total"><?php _e( 'Total', 'tcp' ); ?>:&nbsp;<?php echo tcp_format_the_price( $shoppingCart->getTotalToShow() ); ?></span></li>
	<?php if ( $see_shopping_cart ) :?>
		<li class="tcp_cart_widget_footer_link tcp_shopping_cart_link"><a href="<?php tcp_the_shopping_cart_url(); ?>"><?php _e( 'shopping cart', 'tcp' ); ?></a></li>
	<?php endif; ?>
	<?php if ( $see_checkout ) :?>
		<li class="tcp_cart_widget_footer_link tcp_checkout_link"><a href="<?php tcp_the_checkout_url(); ?>"><?php _e( 'checkout', 'tcp' ); ?></a></li>
	<?php endif; ?>
	<?php if ( $see_delete_all ) :?>
		<li class="tcp_cart_widget_footer_link tcp_delete_all_link"><form method="post"><input type="submit" name="tcp_delete_shopping_cart" class="tcp_delete_shopping_cart" value="<?php _e( 'delete', 'tcp' ); ?>"/></form></li>
	<?php endif; ?>
	<?php do_action( 'tcp_get_shopping_cart_widget', $args ); ?>
	</ul><?php 
}

/**
 * Display Shopping Cart Summary.
 *
 * This function is primarily used by themes which want to hardcode the Resumen
 * Shopping Cart into the sidebar and also by the ShoppingCartSummary widget
 * in TheCartPress.
 *
 * There is also an action that is called whenever the function is run called,
 * 'tcp_get_shopping_cart_summary'.
 *
 * @since 1.0.7
 * @param array $args
 * @param boolean $echo Default to echo and not return the form.
 */
function tcp_get_shopping_cart_summary( $args = false, $echo = true ) {
	$summary = apply_filters( 'tcp_get_shopping_cart_before_summary', '', $args );
	if ( ! $args )
		$args = array(
			'see_product_count' => false,
			'see_weight'		=> true,
			'see_delete_all'	=> false,
			'see_shopping_cart'	=> true,
			'see_checkout'		=> true,
		);
	global $thecartpress;
	$unit_weight		= $thecartpress->get_setting( 'unit_weight', 'gr' );
	$shoppingCart		= TheCartPress::getShoppingCart();
	$summary .= '<ul class="tcp_shopping_cart_resume">';
	$discount = $shoppingCart->getAllDiscounts();
	if ( $discount > 0 )
		$summary .= '<li><span class="tcp_resumen_discount">' . __( 'Discount', 'tcp' ) . ':</span>&nbsp;' . tcp_format_the_price( $discount ) . '</li>';
	$summary .= '<li><span class="tcp_resumen_subtotal">' . __( 'Total', 'tcp' ) . ':</span>&nbsp;' . tcp_format_the_price( $shoppingCart->getTotalToShow( false ) ) . '</li>';

	if ( isset( $args['see_product_count'] ) ? $args['see_product_count'] : false )
		$summary .=	'<li><span class="tcp_resumen_count">' . __( 'N<sup>o</sup> products', 'tcp' ) . ':</span>&nbsp;' . $shoppingCart->getCount() . '</li>';

	$see_weight = isset( $args['see_weight'] ) ? $args['see_weight'] : false;
	if ( $see_weight && $shoppingCart->getWeight() > 0 ) 
		$summary .= '<li><span class="tcp_resumen_weight">' . __( 'Weigth', 'tcp' ) . ':</span>&nbsp;' . tcp_number_format( $shoppingCart->getWeight() ) . '&nbsp;' . $unit_weight . '</li>';
		
	if ( isset( $args['see_shopping_cart'] ) ? $args['see_shopping_cart'] : true )
		$summary .= '<li class="tcp_cart_widget_footer_link tcp_shopping_cart_link"><a href="' . tcp_get_the_shopping_cart_url() . '">' . __( 'Shopping cart', 'tcp' ) . '</a></li>';

	if ( isset( $args['see_checkout'] ) ? $args['see_checkout'] : true )
		$summary .= '<li class="tcp_cart_widget_footer_link tcp_checkout_link"><a href="' . tcp_get_the_checkout_url() . '">' . __( 'Checkout', 'tcp' ) . '</a></li>';

	if ( isset( $args['see_delete_all'] ) ? $args['see_delete_all'] : false ) 
		$summary .= '<li class="tcp_cart_widget_footer_link tcp_delete_all_link"><form method="post"><input type="submit" name="tcp_delete_shopping_cart" class="tcp_delete_shopping_cart" value="' . __( 'Delete', 'tcp' ) . '"/></form></li>';
	$summary = apply_filters( 'tcp_get_shopping_cart_summary', $summary, $args );
	$summary .= '</ul>';
	if ( $echo )
		echo $summary;
	else
		return $summary;
}

function tcp_get_taxonomies_cloud( $args = false, $echo = true, $before = '', $after = '' ) {
	do_action( 'tcp_get_taxonomies_cloud' );
	if ( ! $args )
		$args = array(
			'taxonomy'	=> 'tcp_product_tag',
			'echo'		=> false,
		);
	$cloud = wp_tag_cloud( $args );
	$cloud = apply_filters( 'tcp_get_taxonomies_cloud', $cloud );
	if ( $echo )
		echo $before, $cloud, $after;
	else
		return $before . $cloud . $after;
}

function tcp_get_tags_cloud( $args = false, $echo = true, $before = '', $after = '' ) {
	do_action( 'tcp_get_tags_cloud' );
	$cloud = tcp_get_taxonomies_cloud( $args, false, $before, $after );
	$cloud = apply_filters( 'tcp_get_tags_cloud', $cloud );
	if ( $echo )
		echo $cloud;
	else
		return $cloud;
}

function tcp_get_suppliers_cloud( $args = false, $echo = true, $before = '', $after = '' ) {
	do_action( 'tcp_get_suppliers_cloud' );
	if ( ! $args )
		$args = array(
			'taxonomy'	=> 'tcp_product_supplier',
			'echo'		=> false,
		);
	$cloud = tcp_get_taxonomies_cloud( $args, false, $before, $after );
	$cloud = apply_filters( 'tcp_get_suppliers_cloud', $cloud );
	if ( $echo )
		echo $cloud;
	else
		return $cloud;
}

function tcp_get_number_of_attachments( $post_id = 0 ) {
	if ( $post_id == 0 ) $post_id = get_the_ID();
	$args = array(
		'post_type'		=> 'attachment',
		'numberposts'	=> -1,
		'post_status'	=> null,
		'post_parent'	=> $post_id,
		'fields'		=> 'ids',
	);
	$attachments = get_posts( $args );
	if ( is_array( $attachments ) )
		return count( $attachments );
	else
		return 0;
}

function tcp_get_sorting_fields() {
	$sorting_fields = array(
		array(
			'value'	=> '',
			'title'	=> __( 'Unordered', 'tcp' ),
		),
		array(
			'value'	=> 'order',
			'title'	=> __( 'Suggested', 'tcp' ),
		),
		array(
			'value'	=> 'price',
			'title' => __( 'Price', 'tcp' ),
		),
		array(
			'value'	=> 'title',
			'title'	=> __( 'Title', 'tcp' ),
		),
		array(
			'value'	=> 'author',
			'title'	=> __( 'Author', 'tcp' ),
		),
		array(
			'value'	=> 'date',
			'title'	=> __( 'Date', 'tcp' ),
		),
		array(
			'value'	=> 'comment_count',
			'title'	=> __( 'Popular', 'tcp' ),
		)
	);
	return apply_filters( 'tcp_sorting_fields', $sorting_fields );
}

function tcp_the_sort_panel() {
	$filter = new TCPFilterNavigation();
	$order_type = $filter->get_order_type();
	$order_desc = $filter->get_order_desc();
	$settings = get_option( 'ttc_settings' );
	$disabled_order_types = isset( $settings['disabled_order_types'] ) ? $settings['disabled_order_types'] : array();
	$sorting_fields = tcp_get_sorting_fields(); ?>
<div class="tcp_order_panel">
	<form action="" method="post">
	<span class="tcp_order_type">
	<label for="tcp_order_type">
		<?php _e( 'Order by', 'tcp' ); ?>:&nbsp;
		<select id="tcp_order_type" name="tcp_order_type">
		<?php foreach( $sorting_fields as $sorting_field ) : 
			if ( ! in_array( $sorting_field['value'], $disabled_order_types ) ) : ?>
			<option value="<?php echo $sorting_field['value']; ?>" <?php selected( $order_type, $sorting_field['value'] ); ?>><?php echo $sorting_field['title']; ?></option>
			<?php endif;
		endforeach; ?>
		</select>
	</label>
	</span><!-- .tcp_order_type -->
	<span class="tcp_order_desc">
	<label>
		<input type="radio" name="tcp_order_desc" id="tcp_order_asc" value="asc" <?php checked( $order_desc, 'asc' );?>/>
		<?php _e( 'Asc.', 'tcp' ); ?>
	</label>
	<label>
		<input type="radio" name="tcp_order_desc" id="tcp_order_desc" value="desc" <?php checked( $order_desc, 'desc' );?>/>
		<?php _e( 'Desc.', 'tcp' ); ?>
	</label>
	<span class="tcp_order_submit"><input type="submit" name="tcp_order_by" value="<?php _e( 'Sort', 'tcp' );?>" /></span>
	</span><!-- .tcp_order_desc -->
	</form>
</div><!-- .tcp_order_panel --><?php
}

function tcp_attribute_list( $taxonomies = false ) {
	global $post;
	if ( $taxonomies === false ) $taxonomies = get_object_taxonomies( $post->post_type );
	if ( is_array( $taxonomies ) && count( $taxonomies ) > 0 ) : ?>
		<table class="tcp_attribute_list">
		<tbody>
		<?php $par = true;
		foreach( $taxonomies as $tax ) :
			$taxonomy = get_taxonomy( $tax );
			$terms = wp_get_post_terms( $post->ID, $tax );
			if ( count( $terms ) > 0 ) : ?>
			<tr <?php if ( $par ) echo 'class="tcp_odd"'; $par = !$par; ?>>
				<th scope="row"><?php echo $taxonomy->labels->name; ?></th>
				<td><?php foreach( $terms as $term ) echo $term->name . '&nbsp;'; ?></td>
			</tr>
			<?php endif; ?>
		<?php endforeach; ?>
		</tbody>
		</table>
	<?php endif;
}

/**
 * 'echo'			=> true,
 * 'redirect'		=> get_permalink(),
 * 'form_id'		=> 'loginform',
 * 'label_username'	=> __( 'Username', 'tcp' ),
 * 'label_password'	=> __( 'Password', 'tcp' ),
 * 'label_remember'	=> __( 'Remember Me', 'tcp' ),
 * 'label_log_in'	=> __( 'Log In', 'tcp' ),
 * 'id_username'	=> 'user_login',
 * 'id_password'	=> 'user_pass',
 * 'id_remember'	=> 'rememberme',
 * 'id_submit'		=> 'wp-submit',
 * 'remember'		=> true,
 * 'value_username'	=> '',
 * 'value_remember'	=> false
 */
function tcp_login_form( $args = array() ) {
	$defaults = array(
		'echo'				=> true,
		'redirect'			=> site_url( $_SERVER['REQUEST_URI'] ), // Default redirect is back to the current page
		'form_id'			=> 'loginform',
		'label_username'	=> __( 'Username' ),
		'label_password'	=> __( 'Password' ),
		'label_remember'	=> __( 'Remember Me' ),
		'label_log_in'		=> __( 'Log In' ),
		'id_username'		=> 'user_login',
		'id_password'		=> 'user_pass',
		'id_remember'		=> 'rememberme',
		'id_submit'			=> 'wp-submit',
		'remember'			=> true,
		'value_username'	=> '',
		'value_remember'	=> false, // Set this to true to default the "Remember me" checkbox to checked
	);
	$args = wp_parse_args( $args, apply_filters( 'login_form_defaults', $defaults ) );
	ob_start(); ?>
	<form id="<?php echo $args['form_id']; ?>" method="post" action="<?php echo plugins_url( 'checkout/login.php' , dirname( __FILE__ ) ); ?>" name="<?php echo $args['form_id']; ?>">
		<?php echo apply_filters( 'login_form_top', '', $args ); ?>
		<p class="login-username">
		<label for="<?php echo esc_attr( $args['id_username'] ); ?>"><?php echo esc_html( $args['label_username'] ); ?></label>: <input id="<?php echo $args['id_username']; ?>" class="input" type="text" size="20" value="" name="tcp_log" />
		</p>
		<p class="login-password">
		<label for="<?php echo esc_attr( $args['id_password'] ); ?>"><?php echo esc_html( $args['label_password'] ); ?></label>: <input id="<?php echo $args['id_password']; ?>" class="input" type="password" size="20" value="" name="tcp_pwd" />
		</p>
		<?php apply_filters( 'login_form_middle', '', $args ); ?>
		<p class="login-remember">
		<label><input id="<?php echo esc_attr( $args['id_remember'] ); ?>" type="checkbox" value="forever" name="tcp_rememberme" <?php echo $args['value_remember'] ? ' checked="checked"' : ''; ?>/> <?php echo esc_html( $args['label_remember'] ); ?></label>
		</p>
		<p class="login-submit">
		<input id="<?php echo esc_attr( $args['id_submit'] ); ?>" class="button-primary tcp_checkout_button" type="submit" value="<?php echo esc_html( $args['label_log_in'] ); ?>" name="tcp_submit" />
		<input type="hidden" value="<?php echo esc_attr( $args['redirect'] ); ?>" name="tcp_redirect_to" />
		</p>
		<?php echo apply_filters( 'login_form_bottom', '', $args ); ?>
		<?php do_action( 'login_form' ); ?>
	</form>
<?php if ( isset( $_REQUEST['tcp_register_error'] ) ) : ?>
	<p class="error">
	<strong><?php _e( 'ERROR', 'tcp' ); ?></strong>: <?php echo $_REQUEST['tcp_register_error']; ?>
	</p>
<?php //<a title="<?php _e( 'Password Lost and Found', 'tcp' ); ? >" href="<?php site_url( 'wp-login.php?action=lostpassword' ); ? >"><?php _e( 'Lost your password', 'tcp' ); ? ></a>? ?>
<?php endif;
	$out = ob_get_clean();
	if ( $args['echo'] ) echo $out;
	else return $out;
}

/**
 * Displays/returns the total of the cart
 * @since 1.1.6
 */
function tcp_the_total( $echo = true ) {
    //global $shoppingCart;
    $shoppingCart = TheCartPress::getShoppingCart();
    //if ( ! $shoppingCart->isEmpty() )
        $out = tcp_format_the_price( $shoppingCart->getTotalToShow( false ) );
    if ( $echo ) echo $out;
    else return $out;
}

/**
 * Since 1.1.7
 */
function tcp_get_the_pagination( $echo = true) {
	ob_start();
	if ( function_exists( 'wp_pagenavi' ) ) {
		wp_pagenavi();
	} else {
		global $wp_query;
		$big = 999999999; // need an unlikely integer
		echo paginate_links( array(
			'base'		=> str_replace( $big, '%#%', get_pagenum_link( $big ) ),
			//'format'	=> '?paged=%#%',
			'current'	=> max( 1, get_query_var('paged') ),
			'total'		=> $wp_query->max_num_pages
		) );
	}
	$out = ob_get_clean();
	if ( $echo ) echo $out;
	else return $out;
}

/**
 * Displays a breadcrumb.
 * code based of http://dimox.net/wordpress-breadcrumbs-without-a-plugin/
 *
 * @param $delimiter
 * @param $before tag before the current crumb
 * @param $after tag after the current crumb
 * @since 1.1.8
 */
function tcp_breadcrumbs( $delimiter = '&raquo;', $before = '<span class="current">', $after = '</span>' ) {
	$home = __( 'Home', 'tcp' ); // text for the 'Home' link
	if ( !is_home() && !is_front_page() || is_paged() ) {
		echo '<div id="crumbs">';

		global $post;
		$homeLink = get_bloginfo('url');
		echo '<a href="', $homeLink, '">', $home, '</a> ', $delimiter, ' ';
		if ( is_category() ) {
			global $wp_query;
			$cat_obj = $wp_query->get_queried_object();
			$thisCat = $cat_obj->term_id;
			$thisCat = get_category( $thisCat );
			$parentCat = get_category( $thisCat->parent );
			if ( $thisCat->parent != 0 )
				echo( get_category_parents( $parentCat, true, ' ' . $delimiter . ' ' ) );
			echo $before, 'Archive by category "', single_cat_title( '', false ), '"', $after;
		} elseif ( is_day() ) {
			echo '<a href="', get_year_link( get_the_time( 'Y' ) ), '">', get_the_time( 'Y' ), '</a> ', $delimiter, ' ';
			echo '<a href="', get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ), '">' . get_the_time('F'), '</a> ', $delimiter, ' ';
			echo $before, get_the_time( 'd' ), $after;
		} elseif ( is_month() ) {
			echo '<a href="', get_year_link(get_the_time( 'Y' ) ), '">', get_the_time( 'Y' ), '</a> ', $delimiter, ' ';
			echo $before, get_the_time( 'F' ), $after;
		} elseif ( is_year() ) {
			echo $before, get_the_time( 'Y' ), $after;
		} elseif ( is_single() && ! is_attachment() ) {
			if ( get_post_type() != 'post' ) {
				$post_type = get_post_type_object(get_post_type());
				$slug = $post_type->rewrite;
				echo '<a href="', $homeLink, '/', $slug['slug'], '/">', $post_type->labels->singular_name, '</a> ', $delimiter, ' ';
				echo $before, get_the_title(), $after;
			} else {
				$cat = get_the_category();
				$cat = $cat[0];
				echo get_category_parents( $cat, true, ' ' . $delimiter . ' ' );
				echo $before, get_the_title(), $after;
			}
		} elseif ( ! is_single() && ! is_page() && get_post_type() != 'post' && ! is_404() ) {
			$post_type = get_post_type_object( get_post_type() );
			echo $before, $post_type->labels->singular_name, $after;
		} elseif ( is_attachment() ) {
			$parent = get_post( $post->post_parent );
			$cat = get_the_category( $parent->ID);
			$cat = $cat[0];
			echo get_category_parents( $cat, true, ' ' . $delimiter . ' ' );
			echo '<a href="', get_permalink($parent), '">', $parent->post_title , '</a> ', $delimiter, ' ';
			echo $before, get_the_title(), $after;
		} elseif ( is_page() && !$post->post_parent ) {
			echo $before, get_the_title(), $after;
		} elseif ( is_page() && $post->post_parent ) {
			$parent_id  = $post->post_parent;
			$breadcrumbs = array();
			while ( $parent_id ) {
				$page = get_page( $parent_id );
				$breadcrumbs[] = '<a href="' . get_permalink( $page->ID ) . '">' . get_the_title( $page->ID ) . '</a>';
				$parent_id  = $page->post_parent;
			}
			$breadcrumbs = array_reverse( $breadcrumbs );
			foreach ( $breadcrumbs as $crumb )
				echo $crumb, ' ', $delimiter, ' ';
			echo $before, get_the_title(), $after;
		} elseif ( is_search() ) {
			echo $before;
			printf( __( 'Search results for "%s"', 'tcp' ), get_search_query() );
			echo $after;
		} elseif ( is_tag() ) {
			echo $before;
			printf( __( 'Posts tagged "%s"', 'tcp' ), single_tag_title( '', false ) );
			echo $after;
		} elseif ( is_author() ) {
			global $author;
			$userdata = get_userdata($author);
			echo $before;
			printf( __( 'Articles posted by %s', 'tcp' ), $userdata->display_name );
			echo $after;
		} elseif ( is_404() ) {
			echo $before;
			_e( 'Error 404', 'tcp' );
			echo $after;
		}
		if ( get_query_var( 'paged' ) ) {
			if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
			echo __( 'Page' ),  ' ', get_query_var( 'paged' );
			if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
		}
		echo '</div>';
	}
}

//
//for themes
//
function tcp_posted_on() {
	printf( __( '<span class="tcp_posted_on"><span class="sep">Posted on </span><a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s" pubdate>%4$s</time></a></span>', 'tcp' ),
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() )
	);
}

function tcp_posted_by() {
	printf( __( '<span class="tcp_by_author"><span class="sep">by </span> <span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>', 'tcp' ),
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		sprintf( esc_attr__( 'View all posts by %s', 'tcp' ), get_the_author() ),
		esc_html( get_the_author() )
	);
}
//
//End for themes
//
?>
