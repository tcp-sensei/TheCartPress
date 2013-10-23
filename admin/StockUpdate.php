<?php
/**
 * This file is part of TheCartPress.
 * 
 * TheCartPress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TheCartPress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TheCartPress.  If not, see <http://www.gnu.org/licenses/>.
 */

$post_type		= isset( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : 'tcp_product';
$taxonomy		= isset( $_REQUEST['taxonomy'] ) ? $_REQUEST['taxonomy'] : 'tcp_product_category';
$cat_slug		= isset( $_REQUEST['category_slug'] ) ? $_REQUEST['category_slug'] : '';
$added_stock	= isset( $_REQUEST['added_stock'] ) ? (int)$_REQUEST['added_stock'] : 0;
$pagination		= isset( $_REQUEST['pagination'] ) ? (int)$_REQUEST['pagination'] : 1;

if ( isset( $_REQUEST['tcp_update_stock'] ) ) {
	$args = array(
		'post_type'			=> $post_type,
		$taxonomy			=> $cat_slug ,
		'posts_per_page'	=> 900,//TODO Pagination?
	);
	$args = apply_filters( 'tcp_update_stock_query_args', $args );
	$query = new WP_query( $args );
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$post = $query->next_post();
			$new_stock = isset( $_REQUEST['tcp_new_stock_' . $post->ID] ) ? $_REQUEST['tcp_new_stock_' . $post->ID] : '';
			if ( $new_stock == '' || $new_stock < -1 )
				update_post_meta( $post->ID, 'tcp_stock', -1 );
			else
				update_post_meta( $post->ID, 'tcp_stock', (int)$new_stock );
			do_action( 'tcp_update_stock', $post );
		}?>
		<div id="message" class="updated"><p>
			<?php _e( 'Stock updated.', 'tcp' );?>
		</p></div><?php
	}
	wp_reset_query();
}
?>
<div class="wrap">
	<?php screen_icon( 'tcp-stock-update' ); ?><h2><?php _e( 'Stock Update', 'tcp' );?></h2>
<div class="clear"></div>

<form method="post">
	<table class="form-table" >
	<tbody>
	<tr valign="top">
	<th scope="row"><label for="post_type"><?php _e( 'Post type', 'tcp' )?>:</label></th>
	<td>
		<select name="post_type" id="post_type">
		<?php foreach( tcp_get_saleable_post_types() as $pt ) :
			$obj_type = get_post_type_object( $pt ); ?>
			<option value="<?php echo $pt;?>"<?php selected( $post_type, $pt ); ?>><?php echo $obj_type->labels->singular_name; ?></option>
		<?php endforeach;?>
		</select>
		<input type="submit" name="tcp_load_taxonomies" value="<?php _e( 'Load taxonomies', 'tcp' );?>" class="button-secondary"/>
	</td>
	</tr>	
	<tr valign="top">
	<th scope="row"><label for="taxonomy"><?php _e( 'Taxonomy', 'tcp' )?>:</label></th>
	<td>
		<select name="taxonomy" id="taxonomy">
		<?php foreach( get_object_taxonomies( $post_type ) as $taxmy ) : $tax = get_taxonomy( $taxmy ); ?>
			<option value="<?php echo esc_attr( $taxmy );?>"<?php selected( $taxmy, $taxonomy ); ?>><?php echo $tax->labels->name;?></option>
		<?php endforeach;?>
		</select>
		<input type="submit" name="tcp_load_terms" value="<?php _e( 'Load categories', 'tcp' );?>" class="button-secondary"/>
	</td>
	</tr>
	<tr valign="top">
	<th scope="row"><label for="category_slug"><?php _e( 'Category', 'tcp' );?>:</label></th>
	<td>
		<select id="category_slug" name="category_slug">
			<option value="0"><?php _e( 'no one selected', 'tcp' );?></option>
		<?php $terms = get_terms( $taxonomy, array( 'hide_empty' => true ) );
		foreach( $terms as $term ): ?>
			<option value="<?php echo $term->slug;?>"<?php selected( $cat_slug, $term->slug ); ?>><?php echo esc_attr( $term->name );?></option>
		<?php endforeach; ?>
		</select>
	</td>
	</tr>
	<tr valign="top">
	<th scope="row"><label for="added_stock"><?php _e( 'Units to add', 'tcp' );?>:</label></th>
	<td>
		<input type="text" name="added_stock" id="added_stock" value="<?php echo $added_stock;?>" sixe="4" maxlength="8" class="tcp_count"/>
	</td>
	</tr>
	<?php do_action( 'tcp_update_stock_search_controls' );?>
	</tbody>
	</table>
	<p class="submit">
		<input type="submit" id="tcp_search" name="tcp_search" class="button-secondary" value="<?php _e('Search') ?>" />
	</p>
	<?php if ( isset( $_REQUEST['tcp_search'] ) && strlen( $cat_slug ) > 0 ) :
		$args = array(
			'post_type'			=> $post_type,
			$taxonomy			=>  $cat_slug ,
			'posts_per_page'	=> -1,
		);
		$query = new WP_query( $args );
		if ( $query->have_posts() ) :?>
		<div>
			<h3><?php _e( 'Updated products', 'tcp' );?></h3>

			<span class="description"><?php _e( 'The eCommerce use the last level stock into options structure.', 'tcp' );?></span>
			<table class="widefat fixed" cellspacing="0"><!-- No assigned -->
			<thead>
			<tr>
				<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' );?></th>
				<th scope="col" class="manage-column"><?php _e( 'Actual stock', 'tcp' );?></th>
				<th scope="col" class="manage-column"><?php _e( 'New stock', 'tcp' );?></th>
				<th scope="col" class="manage-column">&nbsp;</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' );?></th>
				<th scope="col" class="manage-column"><?php _e( 'Actual stock', 'tcp' );?></th>
				<th scope="col" class="manage-column"><?php _e( 'New stock', 'tcp' );?></th>
				<th scope="col" class="manage-column">&nbsp;</th>
			</tr>
			</tfoot>
			<tbody>
			<?php
			$fix = 0;
			while ( $query->have_posts() ) :
				$post = $query->next_post();
				$stock = tcp_get_the_stock( $post->ID );
				if ( $added_stock == -1 ||  $stock == -1 )
					$new_stock = -1;
				elseif ( $added_stock == 0 ) {
					$new_stock = $stock;
				} else {
					if ( $stock > -1 ) {
						$new_stock = $stock + $added_stock;
					} else {
						$new_stock = $added_stock;
					}
				}?>
			<tr>
				<td><a href="post.php?action=edit&post=<?php echo $post->ID;?>"><?php echo $post->post_title;?></a></td>
				<td><?php echo $stock;?> <?php _e( 'units', 'tcp' );?></td>
				<td><input type="text" value="<?php echo $new_stock;?>" id="tcp_new_stock_<?php echo $post->ID;?>" name="tcp_new_stock_<?php echo $post->ID;?>" size="13" maxlength="13" class="tcp_count"/> <?php _e( 'units', 'tcp' );?>
				<input type="button" value="<?php _e( 'no stock', 'tcp' );?>" onclick="jQuery('#tcp_new_stock_<?php echo $post->ID;?>').val(-1);" class="button-secondary" /></td>
				<td>&nbsp;</td>
			</tr>
			<?php do_action( 'tcp_update_stock_controls', $post );
			endwhile;?>
			</tbody>
			</table>
		</div>
		<?php endif;
		wp_reset_query();?>
	<p class="submit">
		<input type="submit" id="tcp_update_stock" name="tcp_update_stock" class="button-primary" value="<?php _e('Update') ?>" />
	</p>
	<?php endif;?>
</form>
