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

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once( TCP_DAOS_FOLDER . 'RelEntitiesOptions.class.php' );

//Create an option from another
function tcp_create_option( $post_id, $option_id, $rel_type = 'OPTIONS' ) {
	$post = get_post( $option_id );
	$price = tcp_get_the_price( $option_id );
	$weight = tcp_get_the_weight( $option_id );
	$order = tcp_get_the_order( $option_id );
	unset( $post->ID );
	$new_option_id = wp_insert_post( $post );
	add_post_meta( $new_option_id, 'tcp_price',  $price );
	add_post_meta( $new_option_id, 'tcp_weight', $weight );
	add_post_meta( $new_option_id, 'tcp_order',  $order );
	do_action( 'tcp_create_option', $option_id, $new_option_id );

	RelEntities::insert( $post_id, $new_option_id, $rel_type, $order );
	$options = tcp_get_all_translations( $option_id, 'tcp_product_option' );
	if ( is_array( $options ) && count( $options ) > 0 )
		foreach( $options as $option )
			if ( ! $option->original ) {
				$post = get_post( $option->element_id );
				unset( $post->ID );
				$new_id = wp_insert_post( $post );
				tcp_add_translation( $new_option_id, $new_id, $option->language_code, 'tcp_product_option' );
			}
	return $new_option_id;
}
$post_id  = isset( $_REQUEST['post_id'] )  ? $_REQUEST['post_id'] : 0;
$rel_type = 'OPTIONS';
$cat_slug = isset( $_REQUEST['category_slug'] ) ? $_REQUEST['category_slug'] : '';

if ( isset( $_REQUEST['tcp_update_price'] ) ) {
	$post_id_froms = tcp_get_request_array( 'post_id_from' );
	$option_id_tos = tcp_get_request_array( 'option_id_to' );
	$prices = tcp_get_request_array( 'tcp_price' );
	$orders = tcp_get_request_array( 'tcp_order' );
	foreach( $post_id_froms as $i => $post_id_from ) {
		$option_id_to = $option_id_tos[$i];
		$price = tcp_input_number( $prices[$i] );
		$order = $orders[$i];
		RelEntities::update( $post_id_from, $option_id_to, $rel_type, $order );
		update_post_meta( $option_id_to, 'tcp_price', $price );
		update_post_meta( $option_id_to, 'tcp_order', $order );
	}
}

$index = tcp_is_request( 'tcp_create_relation' );
if ( $index !== false ) {
	$post_id_to	= isset( $_REQUEST['post_id_to' . $index] ) ? $_REQUEST['post_id_to' . $index] : 0;
	$order = isset( $_REQUEST['order' . $index] ) ? (int)$_REQUEST['order' . $index] : 0;
	if ( $post_id_to > 0 ) {
		RelEntities::insert( $post_id, $post_id_to, $rel_type, $order, 0 );?>
		<div id="message" class="updated"><p>
			<?php _e( 'Relation created', 'tcp' );?>
		</p></div><?php
	}
}

$index = tcp_is_request( 'tcp_delete_relation' );
if ( $index !== false ) {
//} elseif ( isset( $_REQUEST['tcp_delete_relation'] ) ) {
	$post_id_to = isset( $_REQUEST['post_id_to' . $index] ) ? $_REQUEST['post_id_to' . $index] : 0;
	$post_id_delete  = isset( $_REQUEST['post_id_delete' . $index] )  ? $_REQUEST['post_id_delete' . $index] : 0;
	if ( $post_id_delete > 0 && RelEntities::count( $post_id_to, $rel_type ) == 0 ) {
		RelEntities::delete( $post_id_delete, $post_id_to, $rel_type );
		wp_delete_post( $post_id_to, true );?>
		<div id="message" class="updated"><p>
			<?php _e( 'Relation deleted', 'tcp' );?>
		</p></div><?php
	}
}

$index = tcp_is_request( 'tcp_delete_children' );
if ( $index !== false ) {
//} elseif ( isset( $_REQUEST['tcp_delete_children'] ) ) {
	$post_id_delete = isset( $_REQUEST['post_id_delete' . $index] ) ? $_REQUEST['post_id_delete' . $index] : 0;
	if ( $post_id_delete > 0 && RelEntities::count( $post_id_delete, $rel_type ) > 0 ) {
		$children = RelEntities::select( $post_id_delete, $rel_type );
		foreach( $children as $child ) {
			wp_delete_post( $child->id_to, true );
			RelEntities::delete( $post_id_delete, $child->id_to, $rel_type );
		}?>
		<div id="message" class="updated"><p>
			<?php _e( 'Children deleted', 'tcp' );?>
		</p></div><?php
	}
}

$index = tcp_is_request( 'tcp_copy_suboptions' );
if ( $index !== false ) {
//} elseif ( isset( $_REQUEST['tcp_copy_suboptions'] ) ) {
	$option_id_from = isset( $_REQUEST['option_id_from' . $index] ) ? $_REQUEST['option_id_from' . $index] : 0;
	$option_id_to = isset( $_REQUEST['option_id_to' . $index] ) ? $_REQUEST['option_id_to' . $index] : 0;
	$options = RelEntities::select( $option_id_from, $rel_type );
	foreach( $options as $option ) {
		$new_option_id = tcp_create_option( $option_id_to, $option->id_to );
	}?>
	<div id="message" class="updated"><p>
		<?php _e( 'Options copied', 'tcp' );?>
	</p></div><?php
}
if ( isset( $_REQUEST['tcp_copy_options_from_product'] ) ) {
	$post_id_from = isset( $_REQUEST['post_id_from' . $index] ) ? $_REQUEST['post_id_from' . $index] : 0;
	$options = RelEntities::select( $post_id_from, $rel_type );
	foreach( $options as $option ) {
		$new_option_id = tcp_create_option( $post_id, $option->id_to );
		$sub_options = RelEntities::select( $option->id_to, $rel_type );
		foreach( $sub_options as $sub_option ) {
			$new_sub_option_id = tcp_create_option( $new_option_id, $sub_option->id_to );
		}
	}?>
	<div id="message" class="updated"><p>
		<?php _e( 'Options created', 'tcp' );?>
	</p></div><?php
}
$index = tcp_is_request( 'tcp_update_price' );
if ( $index !== false ) {
	$post_id_from = isset( $_REQUEST['post_id_from' . $index] ) ? $_REQUEST['post_id_from' . $index] : 0;
	$option_id_to = isset( $_REQUEST['option_id_to' . $index] ) ? $_REQUEST['option_id_to' . $index] : 0;
	$price = isset( $_REQUEST['tcp_price' . $index] ) ? tcp_input_number( $_REQUEST['tcp_price' . $index] ) : 0;
	$order = isset( $_REQUEST['tcp_order' . $index] ) ? $_REQUEST['tcp_order' . $index] : '';
	RelEntities::update( $post_id_from, $option_id_to, $rel_type, $order );
	update_post_meta( $option_id_to, 'tcp_price', $price );
	update_post_meta( $option_id_to, 'tcp_order', $order );?>
	<div id="message" class="updated"><p>
		<?php _e( 'Price and Order updated', 'tcp' );?>
	</p></div><?php
}

$currency = tcp_get_the_currency( );
$post = get_post( $post_id );?>
<div class="wrap">
	<script>
	function show_delete_relation(id_to) {
		var id = "#div_delete_relation_" + id_to;
		jQuery(".delete_relation").hide();
		jQuery(id).show();
		return false;
	}
	function show_delete_children(id_to) {
		var id = "#div_delete_children_" + id_to;
		jQuery(".delete_children").hide();
		jQuery(id).show();
		return false;
	}	
	</script>
	<h2><?php echo __( 'Options for', 'tcp' ), '&nbsp;', $post->post_title;?></h2>
	<ul class="subsubsub">
		<li><a href="post.php?action=edit&post=<?php echo $post->ID;?>"><?php _e( 'return to the product', 'tcp' );?></a></li>
		<li>|</li>
		<li><a href="post-new.php?post_type=tcp_product_option&tcp_product_parent_id=<?php echo $post->ID;?>"><?php _e( 'create new option', 'tcp' );?></a></li>
	</ul>
	<div class="clear"></div>

<form name="frm" method="post">
	<input id="post_id" name="post_id" value="<?php echo $post_id;?>" type="hidden" />
	<p class="search-box">
		<label for="category_id"><?php _e( 'Category', 'tcp' );?>:</label>
		<select id="category_slug" name="category_slug">
			<option value="0" selected="selected"><?php _e( 'no one selected', 'tcp' );?></option>
			<?php $terms = get_terms( 'tcp_product_category', array( 'hide_empty' => true ) );
			foreach( $terms as $term ): ?>
			<option value="<?php echo $term->slug;?>"<?php selected( $cat_slug, $term->slug ); ?>><?php echo esc_attr( $term->name );?></option>
			<?php endforeach; ?>
		</select>
		<input id="tcp_filter_by_category" name="tcp_filter_by_category" value="<?php _e( 'filter', 'tcp' );?>" type="submit" class="button-secondary" />

		<label for="product_id_from"><?php _e( 'product', 'tcp' );?>:</label>
		<select id="post_id_from" name="post_id_from">
			<?php if ( strlen( $cat_slug ) > 0 ) :
				$args = array(
					'post_type'				=> 'tcp_product',
					'tcp_product_category'	=>  $cat_slug,
					'posts_per_page'		=> -1,
				);
				$query = new WP_query( $args );
				if ( $query->have_posts() ) while ( $query->have_posts() ) : $query->the_post();
					if ( get_the_ID() != $post_id && RelEntities::count( get_the_ID(), 'OPTIONS' ) > 0 ) : ?>
			<option value="<?php the_ID(); ?>" <?php selected( get_the_ID(), isset( $_REQUEST['post_id_from'] ) ? $_REQUEST['post_id_from'] : 0 );?>><?php the_title(); ?></option>
					<?php endif;?>
				<?php endwhile; wp_reset_query();
			else: ?>
			<option value="" selected="selected"><?php _e( 'Please, filter first a category', 'tcp' );?></option>
			<?php endif;?>
		</select>
		<input id="tcp_copy_options_from_product" name="tcp_copy_options_from_product" value="<?php _e( 'copy', 'tcp' );?>" type="submit" class="button-secondary" />
	</p>
</form>

<form method="post">
<p><input type="submit" id="tcp_update_price" name="tcp_update_price" value="<?php _e( 'modify all', 'tcp' );?>" class="button-primary"/></p>
<table class="widefat fixed">
<thead>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Thumbnail', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Option', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Status', 'tcp' );?></th>
	<th scope="col" class="manage-column" style="width: 25%;"><?php _e( 'Price and order', 'tcp' );?></th>
	<th scope="col" class="manage-column" style="width: 25%;">&nbsp;</th>
</tr>
</thead>

<tfoot>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Thumbnail', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Option', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' );?></th>
	<th scope="col" class="manage-column"><?php _e( 'Status', 'tcp' );?></th>
	<th scope="col" class="manage-column" style="width: 25%;"><?php _e( 'Price and order', 'tcp' );?></th>
	<th scope="col" class="manage-column" style="width: 25%;">&nbsp;</th>
</tr>
</tfoot>

<tbody>
<?php $options = RelEntities::select( $post_id, $rel_type );
if ( is_array( $options ) && count( $options ) > 0 ) :
	foreach( $options as $i => $option ) : $post = get_post( $option->id_to ); 
		if ( $post ) : ?>
	<tr>
		<td><a href="post.php?action=edit&post_type=tcp_product_option&post=<?php echo $post->ID;?>" title="<?php _e( 'edit option', 'tcp' ); ?>"><?php echo tcp_get_the_thumbnail( $post_id, $post->ID, 0, array( '50', '50' ) ); ?></a></td>
		<td><a href="post.php?action=edit&post_type=tcp_product_option&post=<?php echo $post->ID;?>" title="<?php _e( 'edit option', 'tcp' ); ?>"><?php echo $post->post_title;?></a></td>
		<td><?php echo $post->post_content;?></td>
		<td><?php echo $post->post_status;?></td>
		<td>
			<input type="hidden" id="post_id" name="post_id" value="<?php echo $post_id;?>" />
			<input type="hidden" id="post_id_from" name="post_id_from_<?php echo $i;?>" value="<?php echo $post_id;?>" />
			<input type="hidden" id="option_id_to" name="option_id_to_<?php echo $i;?>" value="<?php echo $option->id_to;?>" />
			<input type="text" id="tcp_price" name="tcp_price_<?php echo $i;?>" value="<?php echo tcp_number_format( tcp_get_the_price( $post->ID ) );?>" size="6" maxlength="13" class="tcp_count"/>&nbsp;<?php echo $currency;?>
			&nbsp;<label><?php echo __( 'Order', 'tcp' );?>:&nbsp;<input type="text" id="tcp_order" name="tcp_order_<?php echo $i;?>" value="<?php echo tcp_get_the_order( $post->ID );?>" size="4" maxlength="8" class="tcp_count"/></label>
		</td>
		<td>
			<a href="post.php?action=edit&post_type=tcp_product_option&post=<?php echo $post->ID;?>"><?php _e( 'edit option', 'tcp' );?></a>
			&nbsp;|&nbsp;
		<?php if ( RelEntities::count( $option->id_to, $rel_type ) == 0 ) :?>
			<a href="#" onclick="return show_delete_relation(<?php echo $post->ID;?>);" class="delete"><?php _e( 'delete option', 'tcp' );?></a>
		<?php else:?>
			<a href="#" onclick="return show_delete_children(<?php echo $post->ID;?>);" class="delete"><?php _e( 'delete children', 'tcp' );?></a>			
		<?php endif;?>
			&nbsp;|&nbsp;
			<a href="post-new.php?post_type=tcp_product_option&tcp_product_parent_id=<?php echo $post_id;?>&tcp_product_option_parent_id=<?php echo $post->ID;?>"><?php _e( 'create child option', 'tcp' );?></a>
			<br/>
			<div class="wrap copy_relation">
				<input id="post_id" name="post_id" value="<?php echo $post_id;?>" type="hidden" />
				<input id="rel_type" name="rel_type" value="<?php echo $rel_type;?>" type="hidden" />
				<input id="option_id_to" name="option_id_to_<?php echo $i;?>" value="<?php echo $option->id_to?>" type="hidden" />
				<input id="tcp_copy_suboptions" name="tcp_copy_suboptions_<?php echo $i;?>" value="<?php _e( 'copy from' , 'tcp' );?>" type="submit" class="button-secondary"/>
				<select id="option_id_from" name="option_id_from_<?php echo $i;?>">
			<?php foreach( $options as $option_to_copy ) : 
				if ( $option->id_to != $option_to_copy->id_to ) : $post = get_post( $option_to_copy->id_to ); ?>
					<option value="<?php echo $option_to_copy->id_to;?>"><?php echo $post->post_title;?></option>
				<?php endif;?>
			<?php endforeach;?>
				</select>
			</div>
			<?php if ( RelEntities::count( $option->id_to, $rel_type ) == 0 ) :?>
				<div class="wrap delete_relation" id="div_delete_relation_<?php echo $option->id_to;?>" style="display: none; border: 1px dotted orange; padding: 2px">
					<input id="post_id" name="post_id" value="<?php echo $post_id;?>" type="hidden" />
					<input id="post_id_delete" name="post_id_delete_<?php echo $i;?>" value="<?php echo $option->id_from;?>" type="hidden" />
					<input id="post_id_to" name="post_id_to_<?php echo $i;?>" value="<?php echo $option->id_to;?>" type="hidden" />
					<input id="rel_type" name="rel_type" value="<?php echo $rel_type;?>" type="hidden" />
					<p><?php _e( 'Do you really want to delete this option?', 'tcp' );?></p>
					<input type="submit" id="tcp_delete_relation" name="tcp_delete_relation_<?php echo $i;?>" class="button-secondary" value="<?php _e( 'Yes' , 'tcp' );?>"/> |
					<a href="#" onclick="jQuery('#div_delete_relation_<?php echo $option->id_to;?>').hide();return false;"><?php _e( 'No, I don\'t' , 'tcp' );?></a>
				</div>
			<?php else: ?>
				<div class="wrap delete_children" id="div_delete_children_<?php echo $option->id_to;?>" style="display: none; border: 1px dotted orange; padding: 2px">
					<input id="post_id" name="post_id" value="<?php echo $post_id;?>" type="hidden" />
					<input id="post_id_delete" name="post_id_delete_<?php echo $i;?>" value="<?php echo $option->id_to;?>" type="hidden" />
					<input id="rel_type" name="rel_type" value="<?php echo $rel_type;?>" type="hidden" />
					<p><?php _e( 'Do you really want to delete those children?', 'tcp' );?></p>
					<input type="submit" id="tcp_delete_children" name="tcp_delete_children_<?php echo $i;?>" class="button-secondary" value="<?php _e( 'Yes' , 'tcp' );?>"/> |
					<a href="#" onclick="jQuery('#div_delete_children_<?php echo $option->id_to;?>').hide();return false;"><?php _e( 'No, I don\'t' , 'tcp' );?></a>
				</div>
			<?php endif;?>
		</td>
	</tr>
		<?php $options_2 = RelEntities::select( $option->id_to, $rel_type );
		foreach( $options_2 as $j => $option_2 ) : $post_2 = get_post( $option_2->id_to ); ?>
	<tr>
		<td style="padding-left: 2em;"><a href="post.php?action=edit&post_type=tcp_product_option&post=<?php echo $post_2->ID;?>" title="<?php _e( 'edit option', 'tcp' ); ?>"><?php echo tcp_get_the_thumbnail( $post_id, $option->id_to, $post_2->ID, array( '50', '50' ) ); ?></a></td>
		<td><span style="padding-left: 2em;">&nbsp;</span><a href="post.php?action=edit&post_type=tcp_product_option&post=<?php echo $post_2->ID;?>" title="<?php _e( 'edit option', 'tcp' ); ?>"><?php echo $post_2->post_title;?></a></td>
		<td><?php echo $post_2->post_content;?></td>
		<td><?php echo $post_2->post_status;?></td>
		<td>
			<input type="hidden" id="post_id_from" name="post_id_from_<?php echo $i, '_', $j;?>" value="<?php echo $option_2->id_from;?>" />
			<input type="hidden" id="option_id_to" name="option_id_to_<?php echo $i, '_', $j;?>" value="<?php echo $post_2->ID?>" />
			<input type="text" min="0" id="tcp_price" name="tcp_price_<?php echo $i, '_', $j;?>" value="<?php echo tcp_number_format( tcp_get_the_price( $post_2->ID ) );?>" size="6" maxlength="13" class="tcp_count"/>&nbsp;<?php echo $currency;?>
			&nbsp;<label><?php echo __( 'Order', 'tcp' );?>:&nbsp;<input type="text" id="tcp_order" name="tcp_order_<?php echo $i, '_', $j;?>" value="<?php echo tcp_get_the_order( $post_2->ID );?>" size="4" maxlength="8" class="tcp_count"/></label>
		</td>
		<td>
			<a href="post.php?action=edit&post_type=tcp_product_option&post=<?php echo $post_2->ID;?>"><?php _e( 'edit option', 'tcp' );?></a>
			<?php if ( RelEntities::count( $option_2->id_to, $rel_type ) == 0 ) :?>
				&nbsp;|&nbsp;
				<a href="#" onclick="return show_delete_relation(<?php echo $option_2->id_to;?>);" class="delete"><?php _e( 'delete option', 'tcp' );?></a>
				<div class="wrap delete_relation" id="div_delete_relation_<?php echo $option_2->id_to;?>" style="display: none; border: 1px dotted orange; padding: 2px">
					<input id="post_id" name="post_id" value="<?php echo $post_id;?>" type="hidden" />
					<input id="post_id_delete" name="post_id_delete_<?php echo $i, '_', $j;?>" value="<?php echo $option_2->id_from;?>" type="hidden" />
					<input id="post_id_to" name="post_id_to_<?php echo $i, '_', $j;?>" value="<?php echo $option_2->id_to;?>" type="hidden" />
					<input id="rel_type" name="rel_type_<?php echo $i, '_', $j;?>" value="<?php echo $rel_type;?>" type="hidden" />
					<p><?php _e( 'Do you really want to delete the option?', 'tcp' );?></p>
					<input type="submit" id="tcp_delete_relation" name="tcp_delete_relation_<?php echo $i, '_', $j;?>" class="button-secondary" value="<?php _e( 'Yes' , 'tcp' );?>"/> |
					<a href="#" onclick="jQuery('#div_delete_relation_<?php echo $option_2->id_to;?>').hide();return false;"><?php _e( 'No, I don\'t' , 'tcp' );?></a>
				</div>
			<?php endif;?>
		</td>
	</tr>
		<?php endforeach;?>
		<?php endif;?>
	<?php endforeach;?>
<?php else:?>
	<tr>
		<td colspan="6"><?php _e( 'The options list is empty', 'tcp' )?></td>
	</tr>
<?php endif;?>
</tbody>
</table>
<p><input type="submit" id="tcp_update_price" name="tcp_update_price" value="<?php _e( 'modify all', 'tcp' );?>" class="button-primary"/></p>
</form>
</div>
