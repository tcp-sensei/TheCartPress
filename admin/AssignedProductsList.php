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

require_once( TCP_DAOS_FOLDER . 'RelEntities.class.php' );

$post_id		= isset( $_REQUEST['post_id'] )  ? $_REQUEST['post_id']  : 0;
$post_type		= get_post_type( $post_id );
$rel_type		= isset( $_REQUEST['rel_type'] ) ? $_REQUEST['rel_type'] : '';
$post_type_to	= isset( $_REQUEST['post_type_to'] ) ? $_REQUEST['post_type_to'] : $post_type;

$category_slug	= false;
foreach ( get_object_taxonomies( $post_type_to, 'objects' ) as $tax ) {
	if ( $tax->hierarchical ) {
		$category_slug	= $tax->name;//rewrite['slug'];
		$category_value	= isset( $_REQUEST[$category_slug] ) ? $_REQUEST[$category_slug] : false;
		break;
	}
}
//$category_slug	= 'tcp_product_category';
//$category_value	= isset( $_REQUEST['category_value'] ) ? $_REQUEST['category_value'] : false;
$product_type	= isset( $_REQUEST['product_type'] ) ? $_REQUEST['product_type'] : false;
if ( strlen( $product_type ) == 0 ) $product_type = false;
//if ( ! $product_type ) $product_type = get_post_type( $post_id );

global $thecartpress;
$show_back_end_label = $thecartpress->get_setting( 'show_back_end_label', false );
if ( isset( $_REQUEST['tcp_create_relation'] ) ) {
	$post_id_to = isset( $_REQUEST['post_id_to'] ) ? $_REQUEST['post_id_to'] : 0;
	$units = isset( $_REQUEST['units'] ) ? (int)$_REQUEST['units'] : 0;
	$list_order = isset( $_REQUEST['list_order'] ) ? (int)$_REQUEST['list_order'] : 0;
	if ( $post_id_to > 0 ) {
		$meta_value = array( 'units' => $units );
		$meta_value = apply_filters( 'tcp_create_assigned_relation', $meta_value, $post_id, $post_id_to );
		RelEntities::insert( $post_id, $post_id_to, $rel_type, $list_order, $meta_value ); ?>

		<div id="message" class="updated"><p>
			<?php _e( 'The relation has been created', 'tcp' ); ?>
		</p></div>

<?php }
} elseif ( isset( $_REQUEST['tcp_delete_relation'] ) ) {
	$post_id_to = isset( $_REQUEST['post_id_to'] ) ? $_REQUEST['post_id_to'] : 0;
	if ( $post_id > 0 ) {
		RelEntities::delete( $post_id, $post_id_to, $rel_type ); ?>

		<div id="message" class="updated"><p>
			<?php _e( 'The relation has been deleted.', 'tcp' ); ?>
		</p></div>

<?php }
} elseif ( isset( $_REQUEST['tcp_delete_all_relation'] ) ) {
	RelEntities::deleteAll( $post_id, $rel_type ); ?>

	<div id="message" class="updated"><p>
		<?php _e( 'All relations have been deleted', 'tcp' ); ?>
	</p></div>

<?php } elseif ( isset( $_REQUEST['tcp_modify_relation'] ) ) {
	$post_id_to = isset( $_REQUEST['post_id_to'] ) ? $_REQUEST['post_id_to'] : 0;
	$list_order = isset( $_REQUEST['list_order'] ) ? $_REQUEST['list_order'] : 0;
	$units = isset( $_REQUEST['units'] ) ? $_REQUEST['units'] : 0;
	$meta_value = array( 'units' => $units );
	$meta_value = apply_filters( 'tcp_modify_assigned_relation', $meta_value, $post_id, $post_id_to );
	RelEntities::update( $post_id, $post_id_to, $rel_type, $list_order, $meta_value );
?>
	<div id="message" class="updated"><p>
		<?php _e( 'The relation has been modified', 'tcp' ); ?>
	</p></div>
<?php }

if ( $post_id ) :
	$post = get_post( $post_id );
	if ( $post ) : ?>

<div class="wrap">
	<script>
	function show_delete_relation(id_to) {
		var id = "#div_delete_relation_" + id_to;
		jQuery(".delete_relation").hide();
		jQuery(id).show();
		return false;
	}
	</script>

	<h2><?php printf( __( 'Assigned products/post for %s', 'tcp' ), '<i>' . $post->post_title . '</i>' ); ?></h2>

	<ul class="subsubsub">
		<li><a href="post.php?action=edit&post=<?php echo $post_id; ?>"><?php printf( __( 'return to %s', 'tcp' ), $post->post_title ); ?></a></li>
		<li>&nbsp;|&nbsp;</li>
		<?php $url = add_query_arg( 'tcp_delete_all_relation', 'y' ); ?>
		<li>
			<a href="#" onclick="return jQuery('#delete_all_relations').show();return false;" class="delete"><?php _e( 'delete all', 'tcp' ); ?></a>
			<div id="delete_all_relations" style="display: none; border: 1px dotted orange; padding: 2px">
				<form method="POST">
					<input id="post_id" name="post_id" value="<?php echo $post_id; ?>" type="hidden" />
					<input id="post_type_to" name="post_type_to" value="<?php echo $post_type_to; ?>" type="hidden" />
					<input id="rel_type" name="rel_type" value="<?php echo $rel_type; ?>" type="hidden" />
					<input id="product_type" name="product_type" value="<?php echo $product_type ? $product_type : ''; ?>" type="hidden" />
					<input id="category_value" name="category_value" value="<?php echo $category_value; ?>" type="hidden" />
					<input id="tcp_delete_all_relation" name="tcp_delete_all_relation" value="y" type="hidden" />
					<p><?php _e( 'Do you really want to delete all relations?', 'tcp' ); ?></p>
					<input type="submit" id="tcp_delete_all_relation" name="tcp_delete_all_relation" value="<?php _e( 'Yes', 'tcp' ); ?>"  class="button-secondary" /> | 
					<a href="#" onclick="jQuery('#delete_all_relations').hide();return false;"><?php _e( 'No, I don\'t' , 'tcp' ); ?></a>
				</form>
			</div>
		</li>
		<li>&nbsp;|&nbsp;</li>
		<li><a href="post-new.php?post_type=<?php echo $post_type_to; ?>&tcp_product_parent_id=<?php echo $post_id; ?>&tcp_rel_type=<?php echo $rel_type; ?>"><?php _e( 'create new assigned product', 'tcp' ); ?></a></li>
	</ul><!-- subsubsub -->
	
	<div class="clear"></div>

	<table class="widefat fixed"><!-- Assigned -->
	<thead>
	<tr>
		<th scope="col" class="manage-column"><?php _e( 'Thumbnail', 'tcp' ); ?></th>
		<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' ); ?></th>
		<?php if ( tcp_is_saleable_post_type( $post_type ) ) : ?><th scope="col" class="manage-column"><?php _e( 'Price', 'tcp' ); ?></th><?php endif; ?>
		<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' ); ?></th>
		<th scope="col" class="manage-column tcp_meta_value">&nbsp;</th>
	</tr>
	</thead>
	<tfoot>
	<tr>
		<th scope="col" class="manage-column"><?php _e( 'Thumbnail', 'tcp' ); ?></th>
		<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' ); ?></th>
		<?php if ( tcp_is_saleable_post_type( $post_type ) ) : ?><th scope="col" class="manage-column"><?php _e( 'Price', 'tcp' ); ?></th><?php endif; ?>
		<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' ); ?></th>
		<th scope="col" class="manage-column tcp_meta_value">&nbsp;</th>
	</tr>
	</tfoot>

	<tbody>
	<?php $assigned_list = RelEntities::select( $post_id, $rel_type );
	if ( is_array( $assigned_list ) && count( $assigned_list ) > 0 ):
		foreach( $assigned_list as $assigned ) :
			$assigned_post = get_post( $assigned->id_to );
			$meta_value = unserialize( $assigned->meta_value );
			$units = isset( $meta_value['units'] )	? $meta_value['units'] : 0; ?>
			<tr>

			<td>
				<a href="post.php?action=edit&post=<?php echo $assigned->id_to; ?>" title="<?php _e( 'edit product', 'tcp' ); ?>"><?php echo get_the_post_thumbnail( $assigned_post->ID, array( '50', '50' ) ); ?></a></td>

			<td>
				<a href="post.php?action=edit&post=<?php echo $assigned->id_to; ?>" title="<?php _e( 'edit product', 'tcp' ); ?>"><?php
				$title = $assigned_post->post_title;
				echo apply_filters( 'tcp_assigned_product_list_title', $title, $assigned_post->ID ); ?></a>
			</td>

			<?php if ( tcp_is_saleable_post_type( $post_type ) ) : ?>
			
			<td>
				<?php $price = tcp_get_the_price_label( $assigned->id_to );
				echo apply_filters( 'tcp_assigned_product_list_price', $price, $post_id, $assigned->id_to ); ?>
			</td>

			<?php endif; ?>

			<td>
				<?php if ( $show_back_end_label ) echo get_post_meta( $assigned->id_to, 'tcp_back_end_label', true );
				else echo $assigned_post->post_excerpt; ?>
			</td>

			<td class="tcp_meta_value">

				<form method="post" name="frm_delete_relation_<?php echo $assigned->id_to; ?>" id="frm_create_relation_<?php echo $assigned_post->id_to; ?>">

					<a href="post.php?action=edit&post=<?php echo $assigned->id_to; ?>"><?php _e( 'edit product', 'tcp' ); ?></a>
					&nbsp;|&nbsp;

					<label for="list_order"><?php echo _x( 'Position', 'to sort the list', 'tcp' ); ?>:&nbsp;</label><input type="text" min="0" name="list_order" id="list_order" size="2" maxlength="4" value="<?php echo $assigned->list_order; ?>" class="tcp_count"/>

					<label for="units"><?php _e( 'Default Units', 'tcp' ); ?>:&nbsp;</label><input type="text" min="0" name="units" id="units" size="2" maxlength="4" value="<?php echo $units; ?>" class="tcp_count"/>

					<?php do_action( 'tcp_create_assigned_relation_fields', $post_id, $assigned->id_to, $meta_value ); ?>

					<input type="submit" name="tcp_modify_relation" id="tcp_modify_relation" value="<?php _e( 'modify', 'tcp' ); ?>" class="button-secondary"/>
					&nbsp;|&nbsp;

					<a href="#" onclick="return show_delete_relation(<?php echo $assigned->id_to; ?>);" class="delete"><?php _e( 'delete', 'tcp' ); ?></a>

					<div class="wrap delete_relation" id="div_delete_relation_<?php echo $assigned->id_to; ?>" style="display: none; border: 1px dotted orange; padding: 2px">
						<input id="post_id" name="post_id" value="<?php echo $assigned->id_from; ?>" type="hidden" />
						<input id="post_type_to" name="post_type_to" value="<?php echo $post_type_to; ?>" type="hidden" />
						<input id="post_id_to" name="post_id_to" value="<?php echo $assigned->id_to; ?>" type="hidden" />
						<input id="rel_type" name="rel_type" value="<?php echo $rel_type; ?>" type="hidden" />
						<input id="product_type" name="product_type" value="<?php echo $product_type; ?>" type="hidden" />
						<input id="category_value" name="category_value" value="<?php echo $category_value; ?>" type="hidden" />
						<p><?php _e( 'Do you really want to delete the relation?', 'tcp' ); ?></p>
						<input id="tcp_delete_relation" name="tcp_delete_relation" type="submit" class="button-secondary" value="<?php _e( 'Yes' , 'tcp' ); ?>" /> |
						<a href="#" onclick="jQuery('#div_delete_relation_<?php echo $assigned->id_to; ?>').hide();return false;"><?php _e( 'No, I don\'t' , 'tcp' ); ?></a>
					</div>

					<?php do_action( 'tcp_assigned_products_product_toolbar', $post_id, $assigned->id_to ); ?>

				</form>

			</td>

			</tr>

		<?php endforeach; ?>
	<?php else: ?>

		<tr>

		<td colspan="<?php if ( tcp_is_saleable_post_type( $post_type ) ) :?>5<?php else:?>4<?php endif; ?>"><?php _e( 'No items to show', 'tcp' ); ?></td>

		</tr>

	<?php endif; ?>
	</tbody>
	</table>

</div> <!-- .wrap -->

<div class="wrap">

	<form name="frm" id="frm" method="post">
		<input id="post_id" name="post_id" value="<?php echo $post_id; ?>" type="hidden" />
		<input id="post_type_to" name="post_type_to" value="<?php echo $post_type_to; ?>" type="hidden" />
		<input id="rel_type" name="rel_type" value="<?php echo $rel_type; ?>" type="hidden" />

		<p class="search-box" style="padding-top: 1em;">

			<?php foreach ( get_object_taxonomies( $post_type_to, 'objects' ) as $tax ) :
				if ( $tax->hierarchical ) : ?>
					<label for=""><?php echo $tax->labels->name; ?>:

					<select id="<?php echo $category_slug; ?>" name="<?php echo $category_slug; ?>">
						<option value=""<?php selected( $category_value, '' ); ?> <?php selected( $category_value, '' ); ?>><?php _e( 'All', 'tcp' ); ?></option>
					<?php $terms = get_terms( $category_slug, array( 'hide_empty' => true ) );
					foreach( $terms as $term ) : ?>
						<option value="<?php echo $term->slug; ?>"<?php selected( $category_value, $term->slug ); ?>><?php echo esc_attr( $term->name ); ?></option>
					<?php endforeach; ?>
					</select>

					</label>
				<?php break;
				endif;
			endforeach ?>

			<!--<label><?php _e( 'Category', 'tcp' ); ?>:

			<select id="category_value" name="_category_value">
				<option value="0"><?php _e( 'no one selected', 'tcp' ); ?></option>
			<?php if ( tcp_is_saleable_post_type( $post_type_to ) )
				$terms = get_terms( $category_slug, array( 'hide_empty' => true ) );
			else
				$terms = get_terms( 'category', array( 'hide_empty' => true ) );
			foreach( $terms as $term ): ?>

				<option value="<?php echo $term->slug; ?>"<?php selected( $category_value, $term->slug ); ?>><?php echo esc_attr( $term->name ); ?></option>

			<?php endforeach; ?>

			</select>

			</label>-->

			<?php if ( tcp_is_saleable_post_type( $post_type_to ) ) : ?>
				<label for="product_type"><?php _e( 'Products type', 'tcp' ); ?>:
					<?php $types = tcp_get_product_types();
					$product_types = array( '' => __( 'All', 'tcp' ) );
					foreach( $types as $id => $type ) $product_types[$id] = $type['label'];
					tcp_html_select( 'product_type', $product_types, $product_type ); ?>
				</label>
			<?php endif; ?>

			<input id="tcp_filter_product_type" name="tcp_filter_product_type" value="<?php _e( 'filter', 'tcp' ); ?>" type="submit" class="button"/>
		</p><!-- search-box -->
	</form>

	<table class="widefat fixed"><!-- No assigned -->
	<thead>
	<tr>
		<th scope="col" class="manage-column"><?php _e( 'Thumbnail', 'tcp' ); ?></th>
		<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' ); ?></th>
		<th scope="col" class="manage-column"><?php _e( 'Price', 'tcp' ); ?></th>
		<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' ); ?></th>
		<th scope="col" class="manage-column tcp_meta_value">&nbsp;</th>
	</tr>
	</thead>
	<tfoot>
	<tr>
		<th scope="col" class="manage-column"><?php _e( 'Thumbnail', 'tcp' ); ?></th>
		<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' ); ?></th>
		<th scope="col" class="manage-column"><?php _e( 'Price', 'tcp' ); ?></th>
		<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' ); ?></th>
		<th scope="col" class="manage-column tcp_meta_value">&nbsp;</th>
	</tr>
	</tfoot>
	<tbody>
	<?php
	//if ( ( tcp_is_saleable_post_type( $post_type_to ) && $category_value && $product_type ) || ( $post_type_to == 'post' && $category_value ) ) :
	//if ( tcp_is_saleable_post_type( $post_type_to ) || ( $post_type_to == 'post' && $category_value ) ) :
	if ( isset( $_REQUEST['tcp_filter_product_type'] ) ) :
		$ids = array();
		$ids[] = $post_id;
		foreach( $assigned_list as $assigned )
				$ids[] = $assigned->id_to;
		$args = array (
			'post_type'			=> $post_type_to,
			'post__not_in'		=> $ids,
			'posts_per_page'	=> -1,
			'fields'			=> 'ids',
		);
		if ( tcp_is_saleable_post_type( $post_type_to ) ) {
			if ( $product_type ) {
				$args['meta_key'] = 'tcp_type';
				$args['meta_value'] = $product_type;
			}
			if ( $category_slug ) $args[$category_slug] = $category_value;
		} else {
			$args['cat_in'] = array( $category_value );
		}

		$posts = get_posts( $args );
		if ( is_array( $posts ) && count( $posts ) > 0 ) :
			foreach( $posts as $id ) : $post = get_post( $id ); ?>
				<tr>

				<td><a href="post.php?action=edit&post=<?php echo $post->ID; ?>" title="<?php _e( 'edit product', 'tcp' ); ?>"><?php echo get_the_post_thumbnail( $post->ID, array( '50', '50' ) ); ?></a></td>

				<td><a href="post.php?action=edit&post=<?php echo $post->ID; ?>" title="<?php _e( 'edit product', 'tcp' ); ?>"><?php echo $post->post_title; ?></a></td>

				<td><?php echo tcp_get_the_price_label( $post->ID ); ?></td>

				<td><?php if ( $show_back_end_label ) echo get_post_meta( $post->ID, 'tcp_back_end_label', true );
				else echo $post->post_excerpt; ?>
				</td>

				<td class="tcp_meta_value">

				<div class="wrap">

					<form method="post" name="frm_create_relation_<?php echo $post->ID; ?>" id="frm_create_relation_<?php echo $post->ID; ?>">

						<a href="post.php?action=edit&post=<?php echo $post->ID; ?>"><?php _e( 'edit product', 'tcp' ); ?></a>

						<input id="tcp_create_relation" name="tcp_create_relation" value="y" type="hidden" />
						<input id="post_id" name="post_id" value="<?php echo $post_id; ?>" type="hidden" />
						<input id="post_id_to" name="post_id_to" value="<?php echo $post->ID; ?>" type="hidden" />
						<input id="post_type_to" name="post_type_to" value="<?php echo $post_type_to; ?>" type="hidden" />
						<input id="rel_type" name="rel_type" value="<?php echo $rel_type; ?>" type="hidden" />
						<input id="product_type" name="product_type" value="<?php echo $product_type; ?>" type="hidden" />
						<input id="category_value" name="category_value" value="<?php echo $category_value; ?>" type="hidden" />

						| <label for="list_order"><?php _e( 'Position', 'tcp' ); ?>:&nbsp;</label><input type="text" name="list_order" id="list_order" size="2" maxlength="4" value="0" class="tcp_count"/>

						<label for="units"><?php _e( 'Default Units', 'tcp' ); ?>:&nbsp;</label><input id="units" name="units" value="1" size="2" maxlength="3" type="text" class="tcp_count"/>

						<?php do_action( 'tcp_create_assigned_relation_fields', $post_id, get_the_ID() ); ?>

						<a href="javascript:document.frm_create_relation_<?php echo $post->ID; ?>.submit();"><?php _e( 'assign' , 'tcp' ); ?></a>

					</form>

				</div>

				</td>
				</tr>
			<?php endforeach;
		else : ?>
			<tr>
			<td colspan="5"><?php _e( 'No items to show', 'tcp' ); ?></td>
			</tr>
		<?php endif;
	else : ?>
		<tr>
		<td colspan="5"><?php _e( 'No items to show', 'tcp' ); ?></td>
		</tr>
	<?php endif; ?>
	</tbody>
	</table>

</div><!-- wrap -->
	<?php endif; ?>
<?php endif; ?>
