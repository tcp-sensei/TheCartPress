<?php
/**
 * General templates
 *
 * TheCartPress template functions
 *
 * @package TheCartPress
 * @subpackage Template-functions
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

function tcp_the_shopping_cart_url( $echo = true ) {
	$url = get_permalink( tcp_get_current_id( get_option( 'tcp_shopping_cart_page_id' ), 'page' ) );
	$url = apply_filters( 'tcp_the_shopping_cart_url', $url );
	if ( $echo ) echo $url;
	else return $url;
}

function tcp_get_the_shopping_cart_url() {
	return tcp_the_shopping_cart_url( false );
}

/**
 * @since 1.2.5.2
 */
function tcp_is_the_shopping_cart_page() {
	return is_page( tcp_get_current_id( get_option( 'tcp_shopping_cart_page_id' ), 'page' ) );
}

function tcp_the_checkout_url( $echo = true ) {
	$url = get_permalink( tcp_get_current_id( get_option( 'tcp_checkout_page_id' ), 'page' ) );
	$url = apply_filters( 'tcp_the_checkout_url', $url );
	if ( $echo ) echo $url;
	else return $url;
}

function tcp_get_the_checkout_url() {
	return tcp_the_checkout_url( false );
}

function tcp_get_the_checkout_ok_url( $order_id = false ) {
	$url = add_query_arg( 'tcp_checkout', 'ok', tcp_get_the_checkout_url() );
	if ( $order_id !== false ) $url = add_query_arg( 'order_id', $order_id, $url );
	$url = apply_filters( 'tcp_get_the_checkout_ok_url', $url );
	return $url;
}

function tcp_get_the_checkout_ko_url( $order_id = false ) {
	$url = add_query_arg( 'tcp_checkout', 'ko', tcp_get_the_checkout_url() );
	if ( $order_id !== false ) $url = add_query_arg( 'order_id', $order_id, $url );
	$url = apply_filters( 'tcp_get_the_checkout_ko_url', $url );
	return $url;
}

/**
 * @since 1.2.5.2
 */
function tcp_is_the_checkout_page() {
	return is_page( tcp_get_current_id( get_option( 'tcp_checkout_page_id' ), 'page' ) );
}

/**
 * @since 1.2.5.2
 */
function tcp_is_the_catalogue_page() {
	return is_page( tcp_get_current_id( get_option( 'tcp_catalogue_page_id' ), 'page' ) );
}

/**
 * @since 1.3.4.2
 */
function tcp_get_the_catalogue_page_id() {
	return tcp_get_current_id( get_option( 'tcp_catalogue_page_id' ), 'page' );
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

function tcp_the_my_account_url( $echo = true ) {
	$url = get_permalink( tcp_get_current_id( get_option( 'tcp_my_account_page_id' ), 'page' ) );
	$url = apply_filters( 'tcp_the_my_account_url', $url );
	if ( $echo ) echo $url;
	else return $url;
}

function tcp_get_the_my_account_url() {
	return tcp_the_my_account_url( false );
}


function tcp_term_link_filter( $termlink, $term, $taxonomy ) {
	return $termlink;
}

/**
 * Display Taxonomy Tree.
 *
 * This function is primarily used by themes which want to hardcode the Taxonomy
 * Tree into the sidebar and also by the NavigationTree widget in TheCartPress.
 *
 * There is also an action that is called whenever the function is run called,
 * 'tcp_get_taxonomy_tree'.
 *
 * Change Log
 * -------------------------------------------
 * 1.2.8, you could filter by author.
 *
 * @since 1.0.7
 * @param array $args
 * @param boolean $echo Default to echo and not return the form.
 */
function tcp_get_taxonomy_tree( $args = false, $echo = true, $before = '', $after = '' ) {
	add_filter( 'term_link', 'tcp_term_link_filter', 10, 3 );
	$args = wp_parse_args( $args, array(
		'style'			=> 'list',
		'show_count'	=> true,
		'hide_empty'	=> true,
		'taxonomy'		=> 'tcp_product_category',
		'title_li'		=> '',
		'collapsible'	=> false,
		'dropdown'		=> false,
		'by_author'		=> false,
	) );
	do_action( 'tcp_taxonomy_tree', $args );
	ob_start();
	if ( $args['dropdown'] ) :
		$taxonomy = get_taxonomy( $args['taxonomy'] );
		$args['show_option_none'] = sprintf ( __( 'Select %s', 'tcp' ), $taxonomy->labels->name );
		global $wp_query;
		if ( isset( $wp_query->query_vars['taxonomy'] ) )
			$args['selected']	= get_query_var( $wp_query->query_vars['taxonomy'] );
		$args['name']		= $args['taxonomy'];
		$args['walker']		= new TCPWalker_CategoryDropdown(); ?>
		<?php echo wp_dropdown_categories( apply_filters( 'tcp_widget_taxonomy_tree_dropdown_args', $args ) ); ?>
<script type='text/javascript'>
// <![CDATA[
	var dropdown = document.getElementById("<?php echo $args['name']; ?>");
	function on_<?php echo $args['name']; ?>_change() {
		if ( dropdown.options[dropdown.selectedIndex].value != -1 ) {
			location.href = dropdown.options[dropdown.selectedIndex].value;
		}
	}
	dropdown.onchange = on_<?php echo $args['name']; ?>_change;
// ]]>
</script>
	<?php else :
		if ( $args['by_author'] ) {
			$terms = tcp_get_terms_by_author();
			$args['include'] = $terms;
		}
	?>
<ul class="tcp_navigation_tree">
	<?php echo wp_list_categories( apply_filters( 'tcp_widget_taxonomy_tree_args', $args ) ); ?>
</ul>
	<?php endif;
	remove_filter( 'term_link', 'tcp_term_link_filter', 10, 3 );
	$tree = ob_get_clean();
	do_action( 'tcp_taxonomy_tree_before', $args );
	$tree = apply_filters( 'tcp_get_taxonomy_tree', $tree );
	if ( $args['collapsible'] ) add_action ( 'wp_footer', 'tcp_get_taxonomy_tree_add_collapsible_behaviour' );
	if ( $echo ) echo $before, $tree, $after;
	else return $before . $tree . $after;
}

function tcp_get_terms_by_author( $author_id = 0 ) {
	if ( $author_id == 0 ) $author_id = get_current_user_id();
	$posts = get_posts( array(
		'author' => $author_id,
		'numberposts' => -1,
		'fields' => 'ids',
	) );
	$terms = array();
	foreach ( $posts as $post_id ) {
		$terms = get_the_terms( $post_id, 'tcp_product_category' );
		if ( $terms ) foreach( $terms as $term )
			$terms[$term->term_id] = $term->term_id;
		}
	return $terms;
}

class TCPWalker_CategoryDropdown extends Walker {
	var $tree_type = 'category';
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id');
	
	//function start_el( &$output, $category, $depth, $args ) {
	function start_el( &$output, $category, $depth = 0, $args = array(), $current_object_id = 0 ) {
		$pad = str_repeat( '&nbsp;', $depth * 3 );
		$cat_name = apply_filters( 'list_cats', $category->name, $category );
		$link = get_term_link( (int)$category->term_id, $category->taxonomy );
		if ( ! is_wp_error( $link ) ) {
			$output .= "\t<option class=\"level-$depth\" value=\"" . $link . "\"";
			if ( $category->slug == $args['selected'] ) $output .= ' selected="selected"';
			$output .= '>';
			$output .= $pad . $cat_name;
			if ( $args['show_count'] ) $output .= '&nbsp;&nbsp;('. $category->count .')';
			if ( $args['show_last_update'] ) {
				$format = 'Y-m-d';
				$output .= '&nbsp;&nbsp;' . gmdate( $format, $category->last_update_timestamp );
			}
			$output .= "</option>\n";
		}
	}
}

function tcp_get_taxonomy_tree_add_collapsible_behaviour() {
	if ( is_tax() ) {
		$term = tcp_get_current_term();
		$term_id = $term->term_id;
	} else {
		$term_id = isset( $_COOKIE['thecartpress_last_taxonomy'] ) ? $_COOKIE['thecartpress_last_taxonomy'] : 0;
	} ?>
<script type="text/javascript">
jQuery( 'li.cat-item > ul' ).each( function( i ) {
	var parent_li = jQuery( this ).parent('li');
	parent_li.addClass( 'tcp_collapsible' );
	var sub_ul = jQuery( this ).remove();
	//parent_li.wrapInner('<a/>').find('a').click(function() {
	parent_li.find( 'a:first' ).click( function( event) {
		sub_ul.toggle();
		return false;
	} );
	parent_li.append( sub_ul );
} );

jQuery( 'ul.tcp_navigation_tree ul' ).hide();
var current = jQuery( 'li.current-cat' );
if ( current.length ) {
	current.parents().show();
	current.show();
	<?php if ( is_single() ) : ?>
	current.parents().addClass( 'tcp-current-term' );
	current.addClass( 'tcp-current-term' );
	<?php endif; ?>
} else {
	jQuery( 'li.cat-item-<?php echo $term_id; ?>' ).parents().show();
	jQuery( 'li.cat-item-<?php echo $term_id; ?>' ).show();
	<?php if ( is_single() ) : ?>
	jQuery( 'li.cat-item-<?php echo $term_id; ?>' ).parents().addClass( 'tcp-current-term' );
	jQuery( 'li.cat-item-<?php echo $term_id; ?>' ).addClass( 'tcp-current-term' );
	<?php endif; ?>
}
</script>
<?php }

/**
 * Displays Shopping Cart Detail.
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
	ob_start();
	do_action( 'tcp_get_shopping_cart_before', $args );
	$see_thumbnail		= isset( $args['see_thumbnail'] ) ? $args['see_thumbnail'] : false;
	$thumbnail_size		= isset( $args['thumbnail_size'] ) ? $args['thumbnail_size'] : 'thumbnail';
	if ( is_numeric( $thumbnail_size ) ) $thumbnail_size = array( $thumbnail_size, $thumbnail_size );
	$see_modify_item	= isset( $args['see_modify_item'] ) ? $args['see_modify_item'] : true;
	$see_weight			= isset( $args['see_weight'] ) ? $args['see_weight'] : true;
	$see_delete_item	= isset( $args['see_delete_item'] ) ? $args['see_delete_item'] : true;
	$see_delete_all		= isset( $args['see_delete_all'] ) ? $args['see_delete_all'] : true;
	$see_shopping_cart	= isset( $args['see_shopping_cart'] ) ? $args['see_shopping_cart'] : true;
	$see_checkout		= isset( $args['see_checkout'] ) ? $args['see_checkout'] : true;
	$widget_id			= isset( $args['widget_id'] ) ? 'tcp_' . str_replace( '-', '_', $args['widget_id'] ) : 'tcp_shopping_cart_detail'; ?>
<div id="<?php echo $widget_id; ?>" class="tcpf">
	<ul class="tcp_shopping_cart">
	<?php $shoppingCart = TheCartPress::getShoppingCart();
	$items = $shoppingCart->getItems();
	foreach( $items as $item ) if ( !empty($item ) ) : ?>
		<li class="tcp_widget_cart_detail_item_<?php echo $item->getPostId(); ?>">
		<form method="post">
			<input type="hidden" name="tcp_post_id" value="<?php echo $item->getPostId(); ?>" />
			<input type="hidden" name="tcp_option_1_id" value="<?php echo $item->getOption1Id(); ?>" />
			<input type="hidden" name="tcp_option_2_id" value="<?php echo $item->getOption2Id(); ?>" />
			<?php do_action( 'tcp_get_shopping_cart_detail_hidden_fields', $item ); ?>
			<ul class="tcp_shopping_cart_widget">
				<?php $title = tcp_get_the_title( $item->getPostId(), $item->getOption1Id(), $item->getOption2Id() );
				$title = apply_filters( 'tcp_get_shopping_cart_detail_title', $title, $item );
				$url = tcp_get_permalink( $item->getPostId(), $item->getOption1Id(), $item->getOption2Id() ); ?>
				<li class="tcp_cart_widget_item">
					<span class="tcp_name"><a href="<?php echo $url; ?>"><?php echo $title; ?></a></span>
				</li>
				<?php if ( $see_thumbnail ) : ?>
				<li class="tcp_cart_widget_thumbnail">
					<?php echo tcp_get_the_thumbnail( $item->getPostId(), $item->getOption1Id(), $item->getOption2Id(), $thumbnail_size ); ?>
				</li>
				<?php endif; ?>
				<!--<li>
					<span class="tcp_unit_price"><?php _e( 'Price', 'tcp' ); ?>:&nbsp;<?php echo tcp_format_the_price( $item->getPriceToshow() ); ?></span>
				</li>-->
				<li>
					<?php //if ( ! tcp_is_downloadable( $item->getPostId() ) ) : ?>
					<?php if ( ! $item->isDownloadable() ) : ?>
				
					<?php if ( $see_modify_item ) : ?>
					<input type="number" min="0" name="tcp_count" value="<?php echo $item->getCount(); ?>" size="2" maxlength="4" class="tcp_count input-sm"/>
					<button type="submit" name="tcp_modify_item_shopping_cart" class="tcp_modify_item_shopping_cart tcp-btn tcp-btn-link tcp-btn-sm" title="<?php _e( 'Modify', 'tcp' ); ?>"><span class="glyphicon glyphicon-refresh"></span> <span class="sr-only"><?php _e( 'Modify', 'tcp' ); ?></span></button>
					<?php if ( $see_delete_item ) : ?>
					<button type="submit" name="tcp_delete_item_shopping_cart" class="tcp_delete_item_shopping_cart tcp-btn tcp-btn-link tcp-btn-sm" title="<?php _e( 'Delete item', 'tcp' ); ?>"><span class="glyphicon glyphicon-trash"></span> <span class="sr-only"><?php _e( 'Delete item', 'tcp' ); ?></span></button>
					<?php endif; ?>
				<?php else : ?>
					<span class="tcp_units"><?php _e( 'Units', 'tcp' ); ?>:&nbsp;<?php echo $item->getCount(); ?></span>
				<?php endif; ?>
					<?php do_action( 'tcp_shopping_cart_widget_units', $item, $args ); ?>
				</li>
				<?php endif; ?>
				<?php if ( $item->getDiscount() > 0 ) : ?>
				<li>
					<span class="tcp_discount"><?php _e( 'Discount', 'tcp' ); ?>:&nbsp;<?php echo tcp_format_the_price( $item->getDiscount() ); ?></span>
				</li>
				<?php endif; ?>
				<li>
					<span class="tcp_subtotal"><?php //_e( 'Total', 'tcp' ); ?><?php echo tcp_format_the_price( $item->getTotalToShow() ); ?></span>
				</li>
				<?php if ( ! tcp_is_downloadable( $item->getPostId() ) ) : ?>
					<?php if ( $see_weight && $item->getWeight() > 0 ) : ?>
				<li>
					<span class="tcp_weight"><?php _e( 'Weight', 'tcp' ); ?>:</span>&nbsp;<?php echo tcp_number_format( $item->getWeight() ); ?>&nbsp;<?php tcp_the_unit_weight(); ?>
				</li>
					<?php endif; ?>
				<?php endif; ?>
				<?php do_action( 'tcp_shopping_cart_widget_item', $item ); ?>
				<?php do_action( 'tcp_get_shopping_cart_widget_item', $args, $item ); ?>
			</ul>
		</form>
		</li>
	<?php endif; ?>
	<?php $discount = $shoppingCart->getCartDiscountsTotal(); //$shoppingCart->getAllDiscounts();
	if ( $discount > 0 ) : ?>
		<li>
			<span class="tcp_discount"><?php _e( 'Discount', 'tcp' ); ?>:&nbsp;<?php echo tcp_format_the_price( $discount ); ?></span>
		</li>
	<?php endif; ?>
		<li>
			<span class="tcp_total"><?php _e( 'Total', 'tcp' ); ?>:&nbsp;<?php echo tcp_format_the_price( $shoppingCart->getTotalToShow() ); ?></span>
		</li>
	<?php if ( $see_shopping_cart ) :?>
		<li class="tcp_cart_widget_footer_link tcp_shopping_cart_link">
			<a href="<?php tcp_the_shopping_cart_url(); ?>"><span class="glyphicon glyphicon-shopping-cart"></span> <?php _e( 'shopping cart', 'tcp' ); ?></a>
		</li>
	<?php endif; ?>
	<?php if ( $see_checkout ) : ?>
		<li class="tcp_cart_widget_footer_link tcp_checkout_link">
			<a href="<?php tcp_the_checkout_url(); ?>"><span class="glyphicon glyphicon-credit-card"></span> <?php _e( 'checkout', 'tcp' ); ?></a>
		</li>
	<?php endif; ?>
	<?php if ( $see_delete_all && count( $items ) > 0 ) : ?>
		<li class="tcp_cart_widget_footer_link tcp_delete_all_link">
			<form method="post">
				<button type="submit" name="tcp_delete_shopping_cart" class="tcp_delete_shopping_cart tcp-btn tcp-btn-default tcp-btn-sm" title="<?php _e( 'delete', 'tcp' ); ?>">
					<span class="glyphicon glyphicon-trash"></span>&nbsp;
					<span class=""><?php _e( 'Delete All', 'tcp' ); ?></span>
				</button>
			</form>
		</li>
	<?php endif; ?>
	<?php do_action( 'tcp_get_shopping_cart_widget', $args ); ?>
	</ul>
</div>
	<?php $out = ob_get_clean();
	if ( $echo ) echo $out;
	return $out;
}

/**
 * Displays Shopping Cart Summary.
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
	$args = wp_parse_args( $args, array(
		'see_total'			=> true,
		'see_discount'		=> true,
		'see_product_count' => false,
		'see_weight'		=> true,
		'see_delete_all'	=> false,
		'see_shopping_cart'	=> true,
		'see_checkout'		=> true,
		'hide_if_empty'		=> false,
	) );
	$shoppingCart = TheCartPress::getShoppingCart();
	if ( $args['hide_if_empty'] && $shoppingCart->isEmpty() ) return;
	ob_start();
	do_action( 'tcp_get_shopping_cart_before_summary', $args );
	$widget_id = isset( $args['widget_id'] ) ? str_replace( '-', '_', $args['widget_id'] ) : 'shopping_cart_summary'; ?>
<div id="tcp_<?php echo $widget_id; ?>" class="tcpf">
	<ul class="tcp_shopping_cart_resume">

	<?php if ( $args['see_product_count'] ) :
	$count = $shoppingCart->getCount(); ?>
	<li class="tcp_resumen_count_li"><span class="tcp_resumen_count"><?php _e( 'N<sup>o</sup> products', 'tcp' ); ?>:</span><span class="tcp_resumen_count_value">&nbsp;<?php echo $count; ?></span></li>
	<?php endif; ?>

	<?php if ( $args['see_discount'] ) : 
		$discount = $shoppingCart->getAllDiscounts();
		if ( $discount > 0 ) : ?>
		<li class="tcp_resumen_discount_li"><span class="tcp_resumen_discount"><?php _e( 'Discount', 'tcp' ); ?>:</span> <span class="tcp_resumen_discount_value"><?php echo tcp_format_the_price( $discount ); ?></span></li>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ( $args['see_total'] ) : 
		$subtotal = tcp_format_the_price( $shoppingCart->getTotalToShow( false ) ); ?>
		<li class="tcp_resumen_subtotal_li"><span class="tcp_resumen_subtotal"><?php _e( 'Total', 'tcp' ); ?>:</span><span class="tcp_resumen_subtotal_value"><?php echo $subtotal; ?></span></li>
	<?php endif; ?>

	<?php if ( $args['see_weight'] ) : 
		$weight = $shoppingCart->getWeight();
		if ( $weight > 0 ) :
			global $thecartpress;
			$unit_weight = $thecartpress->get_setting( 'unit_weight', 'gr' ); ?>
		<li class="tcp_resumen_weight_li"><span class="tcp_resumen_weight"><?php _e( 'Weigth', 'tcp' ); ?>:</span> <span class="tcp_resumen_weight_value"><?php echo $weight; ?></span>&nbsp;<?php echo $unit_weight; ?></li>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ( $args['see_shopping_cart'] ) : ?>
		<li class="tcp_cart_widget_footer_link tcp_shopping_cart_link"><a href="<?php echo tcp_get_the_shopping_cart_url(); ?>"><span class="glyphicon glyphicon-shopping-cart"></span> <?php _e( 'Shopping cart', 'tcp' ); ?></a></li>
	<?php endif; ?>

	<?php if ( $args['see_checkout'] ) : ?>
		<li class="tcp_cart_widget_footer_link tcp_checkout_link"><a href="<?php echo tcp_get_the_checkout_url(); ?>"><span class="glyphicon glyphicon-credit-card"></span> <?php _e( 'Checkout', 'tcp' ); ?></a></li>
	<?php endif; ?>

	<?php if ( $args['see_delete_all'] ) : ?>
		<li class="tcp_cart_widget_footer_link tcp_delete_all_link">
			<form method="post">
				<button type="submit" name="tcp_delete_shopping_cart" class="tcp_delete_shopping_cart tcp-btn tcp-btn-default tcp-btn-sm" title="<?php _e( 'Delete', 'tcp' ); ?>">
					<span class="glyphicon glyphicon-trash"></span> <span class=""><?php _e( 'Delete All', 'tcp' ); ?></span></button>
			</form>
		</li>
	<?php endif; ?>
	</ul>
</div>
<?php $out = ob_get_clean();
	$out = apply_filters( 'tcp_get_shopping_cart_summary', $out, $args );
	if ( $echo ) echo $out;
	else return $out;
}

function tcp_get_taxonomies_cloud( $args = false, $echo = true, $before = '', $after = '' ) {
	do_action( 'tcp_get_taxonomies_cloud' );
	if ( !$args ) {
		$args = array(
			'taxonomy'	=> 'tcp_product_tag',
			'echo'		=> false,
		);
	}
	$cloud = wp_tag_cloud( $args );
	$cloud = apply_filters( 'tcp_get_taxonomies_cloud', $cloud );
	if ( $echo ) echo $before, $cloud, $after;
	else return $before . $cloud . $after;
}

function tcp_get_tags_cloud( $args = false, $echo = true, $before = '', $after = '' ) {
	do_action( 'tcp_get_tags_cloud' );
	$cloud = tcp_get_taxonomies_cloud( $args, false, $before, $after );
	$cloud = apply_filters( 'tcp_get_tags_cloud', $cloud );
	if ( $echo ) echo $cloud;
	else return $cloud;
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
		),
		array(
			'value'	=> 'rand',
			'title'	=> __( 'Random', 'tcp' ),
		),
	);
	return apply_filters( 'tcp_sorting_fields', $sorting_fields );
}

function tcp_the_sort_panel( $post_type = false ) {
	if ( $post_type === false ) $post_type = get_post_type( get_the_ID() );
	$settings	= get_option( 'ttc_settings' );
	$suffix		= '-' . $post_type;
	if ( !isset( $settings['see_title' . $suffix] ) ) {
		$suffix = '';
	}

	if ( isset( $_REQUEST['tcp_order_type'] ) ) {
		$order_type = $_REQUEST['tcp_order_type'];
		$order_desc = $_REQUEST['tcp_order_desc'];
	} else {
		$order_type = $settings['order_type' . $suffix];
		$order_desc = $settings['order_desc' . $suffix];
	}
	$disabled_order_types	= isset( $settings['disabled_order_types' . $suffix] ) ? $settings['disabled_order_types' . $suffix] : array();
	$sorting_fields			= tcp_get_sorting_fields();
	$buy_button_color		= thecartpress()->get_setting( 'buy_button_color' ); ?>
<div class="tcp_order_panel tcpf">
	<form action="" method="post" class="form-inline" role="form">
	<span class="tcp_order_type form-group">
		<label for="tcp_order_type"><?php _e( 'Order by', 'tcp' ); ?>:</label>&nbsp;
		<select id="tcp_order_type" name="tcp_order_type" class="form-control">
			<?php foreach( $sorting_fields as $sorting_field ) : 
			if ( ! in_array( $sorting_field['value'], $disabled_order_types ) ) : ?>
			<option value="<?php echo $sorting_field['value']; ?>" <?php selected( $order_type, $sorting_field['value'] ); ?>><?php echo $sorting_field['title']; ?></option>
			<?php endif;
			endforeach; ?>
		</select>
	</span><!-- .tcp_order_type -->
	<span class="tcp_order_desc">
		<div class="form-group">
			<div class="radio">
				<label>
					<input type="radio" name="tcp_order_desc" id="tcp_order_asc" value="asc" <?php checked( $order_desc, 'asc' );?>/>
					<?php _e( 'Asc.', 'tcp' ); ?>
				</label>
			</div>
		</div>
		<div class="form-group">
			<div class="radio">
				<label>
					<input type="radio" name="tcp_order_desc" id="tcp_order_desc" value="desc" <?php checked( $order_desc, 'desc' );?>/>
					<?php _e( 'Desc.', 'tcp' ); ?>
				</label>
			</div>
		</div>
		<span class="tcp_order_submit">
			<button type="submit" name="tcp_order_by" class="tcp-btn <?php echo $buy_button_color; ?>"><?php _e( 'Sort', 'tcp' );?></button>
		</span>
	</span><!-- .tcp_order_desc -->
	</form>
</div><!-- .tcp_order_panel --><?php
}

function tcp_attribute_list( $taxonomies = false ) {
	global $post;
	if ( $taxonomies === false ) $taxonomies = get_object_taxonomies( $post->post_type );
	if ( is_array( $taxonomies ) && count( $taxonomies ) > 0 ) : ?>
		<dl class="tcp_attribute_list">
		<?php $par = true;
		foreach( $taxonomies as $tax ) :
			$taxonomy = get_taxonomy( $tax );
			$terms = wp_get_post_terms( $post->ID, $tax );
			if ( count( $terms ) > 0 ) : ?>
				<dt><?php echo $taxonomy->labels->name; ?></dt>
				<dd><?php $first = true; foreach( $terms as $term ) : if ( $first ) $first = false; else echo ' | ';
					?><a href="<?php echo get_term_link( $term->slug, $tax ); ?>"><?php echo $term->name;?></a><?php
					endforeach; ?>
				</dd>
			<?php endif; ?>
		<?php endforeach; ?>
		</dl>
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
 * 'value_remember'	=> false,
 * 'see_register'	=> true,
 */
function tcp_login_form( $args = array() ) {
	$defaults = array(
		'echo'				=> true,
//		'redirect'			=> site_url( $_SERVER['REQUEST_URI'] ), // Default redirect is back to the current page
//		'redirect'			=> 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'],
		'redirect'			=> $_SERVER['REQUEST_URI'], //'role:redirect,role:redirect'
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
		'see_register'		=> true,
		'ajax'				=> false,
	);
	$args = wp_parse_args( $args, apply_filters( 'login_form_defaults', $defaults ) );
	ob_start();
	if ( ! is_user_logged_in() ) :
	$url = plugins_url( 'checkout/login.php' , dirname( __FILE__ ) );
	//$url = tcp_admin_url( 'admin-ajax.php' ); ?>
<div id="tcp_login">
	<form id="<?php echo $args['form_id']; ?>" method="post" action="<?php echo $url; ?>" name="<?php echo $args['form_id']; ?>">
		<?php echo apply_filters( 'login_form_top', '', $args ); ?>
		<input type="hidden" name="action" value="tcp_register_and_login<?php if ( $args['ajax'] ) : ?>_ajax<?php endif; ?>" />
		<div class="tcp_login_username_label">
			<label for="<?php echo esc_attr( $args['id_username'] ); ?>"><?php echo esc_html( $args['label_username'] ); ?></label>
		</div>
		<div class="tcp_login_username">
			<input id="<?php echo $args['id_username']; ?>" class="input" type="text" size="20" value="" name="tcp_log" />
		</div>
		<div class="tcp_login_password_label">
			<label for="<?php echo esc_attr( $args['id_password'] ); ?>"><?php echo esc_html( $args['label_password'] ); ?></label>
		</div>
		<div class="tcp_login_password">
			<input id="<?php echo $args['id_password']; ?>" class="input" type="password" size="20" value="" name="tcp_pwd" />
		</div>
		<?php apply_filters( 'login_form_middle', '', $args ); ?>
		<div class="tcp_login_submit">
			<button id="<?php echo esc_attr( $args['id_submit'] ); ?>" class="tcp_checkout_button tcp-btn <?php echo thecartpress()->get_setting( 'buy_button_color' ); ?>" type="submit" value="" name="tcp_submit"><?php echo esc_html( $args['label_log_in'] ); ?></button>
			<?php $redirect = $args['redirect'];
			if ( strlen( $redirect ) == 0 ) $redirect = isset( $_REQUEST['redirect'] ) ? $_REQUEST['redirect'] : ''; ?>
			<input type="hidden" value="<?php echo esc_attr(  remove_query_arg( 'tcp_register_error', $redirect ) ); ?>" name="tcp_redirect_to" />
		</div>
		<div class="tcp_login_remember">
			<input id="<?php echo esc_attr( $args['id_remember'] ); ?>" type="checkbox" value="forever" name="tcp_rememberme" <?php echo $args['value_remember'] ? ' checked="checked"' : ''; ?>/>
		</div>
		<div class="tcp_login_remember_label">
			<label for="<?php echo esc_attr( $args['id_remember'] ); ?>"><?php echo esc_html( $args['label_remember'] ); ?></label>
		</div>
		<div class="tcp_lost_password">
			<a id="tcp_lost_password" href="<?php echo site_url( 'wp-login.php?action=lostpassword', 'login' ); ?>" title="<?php _e( 'Password Lost and Found' ) ?>"><?php _e( 'Lost your password?' ); ?></a>
				<?php if ( $args['see_register'] && get_option('users_can_register') ) : ?>
				<br />
				<?php if ( function_exists( 'bp_get_signup_page' ) ) { //Buddypress
					$register_link = bp_get_signup_page();
				} elseif ( file_exists( ABSPATH . '/wp-signup.php' ) ) { //MU + WP3
					$register_link = site_url( 'wp-signup.php', 'login');
				} else {
					$register_link = site_url( 'wp-login.php?action=register', 'login' );
				} ?>
				<a href="<?php echo $register_link ?>" id="tcp_link_register"><?php _e( 'Register' ); ?></a>
				<?php endif; ?>
		<?php echo apply_filters( 'login_form_bottom', '', $args ); ?>
		<?php do_action( 'login_form' ); ?>
		</div>
		<?php if ( isset( $_REQUEST['tcp_register_error'] ) ) : ?>
		<p class="error">
		<strong><?php _e( 'Error', 'tcp' ); ?></strong>: <?php echo $_REQUEST['tcp_register_error']; ?>
		</p>
		<?php endif; ?>
	</form>
</div><!-- .tcp_login -->
<?php else : ?>
<div class="tcp_profile">
	<?php tcp_author_profile(); ?>
</div><!-- .tcp_my_profile -->
<?php endif;
	$out = ob_get_clean();
	if ( $args['echo'] ) echo $out;
	else return $out;
}

function tcp_register_form( $args = array() ) {
	$defaults = array(
		'echo'			=> true,
//		'redirect'		=> site_url( $_SERVER['REQUEST_URI'] ), // Default redirect is back to the current page
//		'redirect'		=> 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'],
		'redirect'		=> get_permalink(),
		'role'			=> array( 'customer' ),
		'locked'		=> false,
		'login'			=> true,
		'form_id'		=> 'loginform',
		'label_username'=> __( 'Username', 'tcp' ),
		'label_password'=> __( 'Password', 'tcp' ),
		'label_repeat_password'=> __( 'Repeat password', 'tcp' ), //'&nbsp;',
		'label_remember'=> __( 'Remember Me', 'tcp' ),
		'label_log_in'	=> __( 'Log In', 'tcp' ),
		'id_username'	=> 'user_login',
		'id_password'	=> 'user_pass',
		'id_remember'	=> 'rememberme',
		'id_submit'		=> 'wp-submit',
		'remember'		=> true,
		'value_username'=> '',
		'value_remember'=> false, // Set this to true to default the "Remember me" checkbox to checked
		'see_register'	=> true,
		'ajax'			=> false,
	);
	$args = wp_parse_args( $args, apply_filters( 'register_form_defaults', $defaults ) );
	ob_start();
	if ( ! is_user_logged_in() ) :
		//$url = plugins_url( 'thecartpress/checkout/register_and_login.php' );
		$url = admin_url( 'admin-ajax.php' ); ?>
		<?php if ( isset( $_REQUEST['tcp_register_error'] ) ) : ?>
			<h3 class="error tcp_error"><?php _e( 'Error has occurred while registering', 'tcp' ); ?></h3>
			<p class="error tcp_error"><?php echo $_REQUEST['tcp_register_error']; ?></p>
		<?php elseif ( isset( $_REQUEST['tcp_new_user_name'] ) ) : ?>
			<p class="tcp_register_ok"><?php _e( 'Registration data has been saved.', 'tcp' ); ?></p>
		<?php endif; ?>
	<form class="tcp_register" action="<?php echo $url ?>" method="post">
		<input type="hidden" name="action" value="tcp_register_and_login<?php if ( $args['ajax'] ) : ?>_ajax<?php endif; ?>" />
		<?php ob_start(); ?>
		<div class="tcp_register_username_label">
			<label for="tcp_new_user_name"><?php echo $args['label_username']; ?></label>
		</div>
		<div class="tcp_register_username">
			<input type="text" name="tcp_new_user_name" value="<?php echo isset( $_REQUEST['tcp_new_user_name'] ) ? $_REQUEST['tcp_new_user_name'] : ''; ?>" size="20" />
		</div>
		<div class="tcp_register_password_label">
			<label for="tcp_new_user_pass"><?php echo $args['label_password']; ?></label>
		</div>
		<div class="tcp_register_password">
			<input type="password" name="tcp_new_user_pass" size="20" class="input"/>
		</div>
		<div class="tcp_register_repeat_password_label">
			<label for="tcp_repeat_user_pass"><?php echo $args['label_repeat_password']; ?></label>
		</div>
		<div class="tcp_register_repeat_password">
			<input type="password" name="tcp_repeat_user_pass" size="20" class="input"/>
			<!--<span class="description"><?php _e( 'Type your new password again.' ); ?></span>-->
		</div>
		<div class="tcp_register_user_email_label">
			<label for="tcp_user_email"><?php _e( 'E-mail', 'tcp' ); ?></label>
		</div>
		<div class="tcp_register_user_email">
			<input type="text" name="tcp_new_user_email" value="<?php echo isset( $_REQUEST['tcp_new_user_email'] ) ? $_REQUEST['tcp_new_user_email'] : ''; ?>" size="25" maxlength="100"/>
		</div>
		<?php echo apply_filters( 'tcp_register_form', ob_get_clean(), $args ); ?>
		<?php do_action( 'register_form' ); ?>
		<input type="hidden" name="tcp_redirect_to" value="<?php echo $args['redirect']; ?>" />
		<input type="hidden" name="tcp_role" value="<?php echo implode( ',', $args['role'] ); ?>" />
		<?php if ( $args['locked'] ) : ?><input type="hidden" name="tcp_locked" value="yes" /><?php endif; ?>
		<?php if ( $args['login'] ) : ?><input type="hidden" name="tcp_login" value="yes" /><?php endif; ?>
		<p>
			<button type="submit" name="tcp_register_action" id="tcp_register_action" class="tcp_checkout_button tcp-btn <?php echo thecartpress()->get_setting( 'buy_button_color' ); ?>"><?php _e( 'Register', 'tcp' ); ?></button>
		</p>
		<p id="tcp_error_register" class="tcp_error" style="display:none;"><?php _e( 'Error', 'tcp' ); ?>: </p>
	</form>
	<?php else : ?>
		<p><?php _e( 'The user is already logged', 'tcp' ); ?></p>
	<?php endif;
	$out = ob_get_clean();
	if ( $args['echo'] ) echo $out;
	else return $out;
}

/**
 * Displays/returns the current author's profile
 * @since 1.2.8
 */
function tcp_author_profile( $current_user = false) {
	//$current_user = get_query_var( 'author_name' ) ? get_user_by( 'slug', get_query_var( 'author_name' ) ) : get_userdata( get_query_var( 'author' ) );
	if ( $current_user == false ) {
		global $post;
		if ( !empty( $post ) ) {
			$current_user = new WP_User( $post->post_author );
		} else {
			$current_user = get_query_var( 'author_name' ) ? get_user_by( 'slug', get_query_var( 'author_name' ) ) : get_userdata( get_query_var( 'author' ) );
			if ( $current_user === false ) {
				$current_user = get_the_author();
				$current_user = get_user_by( 'login', $current_user );
			} else {
				global $current_user;
				global $user_level;
			}
		}
	}
	if ( !isset( $user_level ) ) $user_level = $current_user->user_level;

	$located = locate_template( 'tcp_author_profile.php' );
	if ( strlen( $located ) == 0 ) $located = TCP_THEMES_TEMPLATES_FOLDER . 'tcp_author_profile.php';
	include( $located );
}

/**
 * Displays/returns the total of the cart
 * @since 1.1.6
 */
function tcp_the_total( $echo = true ) {
	$shoppingCart = TheCartPress::getShoppingCart();
	$out = tcp_format_the_price( $shoppingCart->getTotalToShow( false ) );
	if ( $echo ) echo $out;
	else return $out;
}

/**
 * Displays or returns a pagination bar
 *
 * @param $echo, if true (default) the pagination bar is displayed.
 * @since 1.1.7
 */
function tcp_get_the_pagination( $echo = true ) {
	ob_start();
	if ( function_exists( 'wp_pagenavi' ) ) {
		wp_pagenavi();
	} else {
		global $wp_query;
		$big = 999999999; // need an unlikely integer
		$args = apply_filters( 'tcp_get_the_pagination_args',  array(
			'base'		=> str_replace( $big, '%#%', get_pagenum_link( $big ) ),
			//'format'	=> '?paged=%#%',
			'current'	=> max( 1, get_query_var('paged') ),
			'total'		=> $wp_query->max_num_pages,
			'type'		=> 'list'
		) );
		$out = paginate_links( $args );
		$out = str_replace( '<ul class=\'page-numbers\'>', '<ul class="page-numbers pagination">', $out );
		echo '<div class="tcpf">', $out, '</div>';
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

function tcp_list_authors( $args = '' ) {
	global $wpdb;

	$defaults = array(
		'orderby'		=> 'name',
		'order'			=> 'ASC',
		'number'		=> '',
		'optioncount'	=> false,
		'exclude_admin'	=> true,
		'show_fullname'	=> false,
		'hide_empty'	=> true,
		'feed'			=> '',
		'feed_image'	=> '',
		'feed_type'		=> '',
		'echo'			=> true,
		'style'			=> 'list',
		'html'			=> true,
		'post_type'		=> TCP_PRODUCT_POST_TYPE,
	);
	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );
	$return = '';
	$query_args = wp_array_slice_assoc( $args, array( 'orderby', 'order', 'number' ) );
	$query_args['fields'] = 'ids';
	$authors = get_users( $query_args );

	$author_count = array();
	foreach ( (array) $wpdb->get_results("SELECT DISTINCT post_author, COUNT(ID) AS count FROM $wpdb->posts WHERE post_type = '$post_type' AND " . get_private_posts_cap_sql( $post_type ) . " GROUP BY post_author") as $row )
		$author_count[$row->post_author] = $row->count;
	foreach ( $authors as $author_id ) {
		$author = get_userdata( $author_id );
		if ( $exclude_admin && 'admin' == $author->display_name ) continue;
		$posts = isset( $author_count[$author->ID] ) ? $author_count[$author->ID] : 0;
		if ( !$posts && $hide_empty ) continue;
		$link = '';
		if ( $show_fullname && $author->first_name && $author->last_name ) $name = "$author->first_name $author->last_name";
		else $name = $author->display_name;

		if ( !$html ) {
			$return .= $name . ', ';
			continue; // No need to go further to process HTML.
		}
		if ( 'list' == $style ) $return .= '<li>';
		$link = '<a href="' . tcp_get_author_posts_url( $author->ID, $author->user_nicename, $post_type ) . '" title="' . esc_attr( sprintf( __( "Posts by %s" ), $author->display_name ) ) . '">' . $name . '</a>';
		//$link = '<a href="' . get_author_posts_url( $author->ID, $author->user_nicename ) . '" title="' . esc_attr( sprintf( __( "Posts by %s" ), $author->display_name ) ) . '">' . $name . '</a>';
		
		if ( ! empty( $feed_image ) || !empty( $feed ) ) {
			$link .= ' ';
			if ( empty( $feed_image ) ) {
				$link .= '(';
			}
			$link .= '<a href="' . get_author_feed_link( $author->ID ) . '"';
			$alt = $title = '';
			if ( !empty( $feed ) ) {
				$title = ' title="' . esc_attr( $feed ) . '"';
				$alt = ' alt="' . esc_attr( $feed ) . '"';
				$name = $feed;
				$link .= $title;
			}
			$link .= '>';
			if ( !empty( $feed_image ) ) $link .= '<img src="' . esc_url( $feed_image ) . '" style="border: none;"' . $alt . $title . ' />';
			else $link .= $name;
			$link .= '</a>';
			if ( empty( $feed_image ) ) $link .= ')';
		}
		if ( $optioncount ) $link .= ' ('. $posts . ')';
		$return .= $link;
		$return .= ( 'list' == $style ) ? '</li>' : ', ';
	}
	$return = rtrim($return, ', ');
	if ( ! $echo ) return $return;
	echo $return;
}

function tcp_get_author_posts_url( $author_id, $author_nicename, $post_type = TCP_PRODUCT_POST_TYPE ) {
	$link = get_author_posts_url( $author_id, $author_nicename );
	$link = add_query_arg( 'post_type', $post_type, $link );//notice
	return $link;
}

/**
 * Displays the wishlist button
 *
 * @since 1.1.8
 */
function tcp_the_add_wishlist_button( $post_id ) {
	global $wish_list;
	if ( isset( $wish_list ) ) echo $wish_list->tcp_the_add_to_cart_button( '', $post_id );
}

/**
 * Display Custom Values
 *
 * @since 1.2.8
 */
function tcp_display_custom_values( $post_id = 0, $instance ) {
	if ( $post_id == 0 ) $post_id = get_the_ID();
	$defaults = array(
		'post_type'				 => TCP_PRODUCT_POST_TYPE,
		'see_label'				 => true,
		'hide_empty_fields'		 => true,
		'see_links'				 => false,
		'selected_custom_fields' => '',
	);
	$instance = wp_parse_args( (array)$instance, $defaults );
	$field_ids = explode( ',', $instance['selected_custom_fields'] );
	if ( is_array( $field_ids ) && count( $field_ids ) > 0 )  {
		$other_values = apply_filters( 'tcp_custom_values_get_other_values', array() );
		$template = locate_template( 'tcp_custom_fields.php' );
		if ( strlen( $template ) == 0 ) {
			$template = TCP_THEMES_TEMPLATES_FOLDER . 'tcp_custom_fields.php';
		}
		include ( $template );
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

function tcp_the_feedback_image( $class, $html = false ) { ?>
<span class="<?php echo $class; ?>" style="display: none;">
	<img src="<?php echo admin_url( 'images/loading.gif' ); ?>" />
	<?php if ( $html !== false ) echo $html; ?>
</span>
<?php }
