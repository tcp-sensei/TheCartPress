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

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'RelationsMetabox' ) ) :

require_once( TCP_DAOS_FOLDER . 'RelEntities.class.php' );

class RelationsMetabox {

	function __construct() {
		add_action( 'tcp_admin_init', array( $this, 'register_metabox' ) );
	}

	function register_metabox() {
		$saleable_post_types = tcp_get_product_post_types();
		if ( is_array( $saleable_post_types ) && count( $saleable_post_types ) ) {
			foreach( $saleable_post_types as $post_type ) {
				add_meta_box( 'tcp-product-assign', __( 'Related data', 'tcp' ), array( $this, 'show' ), $post_type, 'normal', 'high' );
			}
		}
	}

	function show() {
		global $post;
		if ( !tcp_is_saleable_post_type( $post->post_type ) ) return;
		$post_id = tcp_get_default_id( $post->ID, $post->post_type );
		$type	 = tcp_get_the_product_type( $post_id );
		if ( $type == 'GROUPED' ) {
			$this->show_grouped( $post_id );
		} else { //if ( $type == 'SIMPLE' ) {
			$this->show_options( $post_id );//TODO Dynamic options
		}
	}

	function show_grouped( $post_id ) {
		$count = RelEntities::count( $post_id );
		if ( $count > 0 ) $count = ' (' . $count . ')';
		else $count = ''; ?>
<ul class="subsubsub">
	<li><a href="<?php echo TCP_ADMIN_PATH;?>AssignedProductsList.php&post_id=<?php echo $post_id;?>&rel_type=GROUPED"><?php _e( 'Manage grouped products', 'tcp' );?><?php echo $count;?></a></li>
	<li>|</li>
	<li><a href="post-new.php?post_type=<?php echo get_post_type(); ?>&tcp_product_parent_id=<?php echo $post_id;?>&rel_type=GROUPED"><?php _e( 'Create new grouped product', 'tcp' );?></a></li>
	<?php do_action( 'tcp_relations_metabox_grouped_toolbar', $post_id ); ?>
</ul>
<div class="form-wrap">
<table class="widefat fixed"><!-- Assigned -->
<thead>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Thumbnail', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Price', 'tcp' ); ?></th>
	<th scope="col" class="manage-column tcp_meta_value">&nbsp;</th>
</tr>
</thead>
<tfoot>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Thumbnail', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Price', 'tcp' ); ?></th>
	<th scope="col" class="manage-column tcp_meta_value">&nbsp;</th>
</tr>
</tfoot>
<tbody>
<?php $type = tcp_get_the_product_type( $post_id );
$assigned_list = RelEntities::select( $post_id, $type );
if ( is_array( $assigned_list ) && count( $assigned_list ) > 0 ):
	foreach( $assigned_list as $assigned ) :
		$assigned_post = get_post( $assigned->id_to );
		$meta_value = unserialize( $assigned->meta_value );
		$units = isset( $meta_value['units'] )	? $meta_value['units'] : 0; ?>
		<tr>
		<td><a href="post.php?action=edit&post=<?php echo $assigned->id_to;?>" title="<?php _e( 'edit product', 'tcp' ); ?>"><?php echo get_the_post_thumbnail( $assigned_post->ID, array( '50', '50' ) ); ?></a></td>
		<td><a href="post.php?action=edit&post=<?php echo $assigned->id_to;?>" title="<?php _e( 'edit product', 'tcp' ); ?>"><?php
			$title = $assigned_post->post_title;
			echo apply_filters( 'tcp_assigned_product_list_title', $title, $assigned_post->ID ); ?></a></td>
		<td><?php $price = tcp_get_the_price_label( $assigned->id_to );
			echo apply_filters( 'tcp_assigned_product_list_price', $price, $post_id, $assigned->id_to ); ?></td>
		<td class="tcp_meta_value">
			<form method="post" name="frm_delete_relation_<?php echo $assigned->id_to;?>" id="frm_create_relation_<?php echo $assigned_post->id_to;?>">
				<a href="post.php?action=edit&post=<?php echo $assigned->id_to;?>"><?php _e( 'edit product', 'tcp' ); ?></a>
				&nbsp;|&nbsp;<?php echo _x( 'Order', 'to sort the list', 'tcp' ); ?>:&nbsp;<?php echo $assigned->list_order;?>&nbsp;<?php _e( 'Units', 'tcp' ); ?>:&nbsp;<?php echo $units; ?>
				<?php do_action( 'tcp_create_assigned_relation_fields', $post_id, $assigned->id_to, $meta_value ); ?>
				<?php do_action( 'tcp_assigned_products_product_toolbar', $post_id, $assigned->id_to ); ?>
			</form>
		</td>
		</tr>
	<?php endforeach; ?>
<?php else: ?>
	<tr>
	<td colspan="4"><?php _e( 'No items to show', 'tcp' ); ?></td>
	</tr>
<?php endif; ?>
</tbody>
</table>
</div><?php
	}

	function show_options( $post_id ) { ?>
	<ul class="subsubsub"><?php do_action( 'tcp_relations_metabox_options_toolbar', $post_id ); ?></ul>
<div class="form-wrap">
<table class="widefat fixed">
<thead>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Thumbnail', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Option', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Status', 'tcp' ); ?></th>
	<th scope="col" class="manage-column" style="width: 25%;"><?php _e( 'Price and order', 'tcp' ); ?></th>
	<th scope="col" class="manage-column" style="width: 25%;">&nbsp;</th>
</tr>
</thead>
<tfoot>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Thumbnail', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Option', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Status', 'tcp' ); ?></th>
	<th scope="col" class="manage-column" style="width: 25%;"><?php _e( 'Price and order', 'tcp' ); ?></th>
	<th scope="col" class="manage-column" style="width: 25%;">&nbsp;</th>
</tr>
</tfoot>
<tbody>
<?php $options = RelEntities::select( $post_id, 'OPTIONS' );
if ( is_array( $options ) && count( $options ) > 0 ) :
	foreach( $options as $i => $option ) : $post = get_post( $option->id_to ); 
		if ( $post ) : ?>
	<tr>
		<td><a href="post.php?action=edit&post_type=tcp_product_option&post=<?php echo $post->ID;?>" title="<?php _e( 'edit option', 'tcp' ); ?>"><?php echo tcp_get_the_thumbnail( $post_id, $post->ID, 0, array( '50', '50' ) ); ?></a></td>
		<td><a href="post.php?action=edit&post_type=tcp_product_option&post=<?php echo $post->ID;?>" title="<?php _e( 'edit option', 'tcp' ); ?>"><?php echo $post->post_title;?></a></td>
		<td><?php echo $post->post_content;?></td>
		<td><?php echo $post->post_status;?></td>
		<td><?php echo tcp_format_the_price( tcp_get_the_price( $post->ID ) ); ?>&nbsp;<?php echo _x( 'Order', 'to sort lists', 'tcp' ); ?>:&nbsp;<?php echo tcp_get_the_order( $post->ID ); ?></td>
		<td><a href="post.php?action=edit&post_type=tcp_product_option&post=<?php echo $post->ID;?>"><?php _e( 'edit option', 'tcp' ); ?></a></td>
	</tr>
		<?php $options_2 = RelEntities::select( $option->id_to, 'OPTIONS' );
		foreach( $options_2 as $j => $option_2 ) : $post_2 = get_post( $option_2->id_to ); ?>
	<tr>
		<td style="padding-left: 2em;"><a href="post.php?action=edit&post_type=tcp_product_option&post=<?php echo $post_2->ID;?>" title="<?php _e( 'edit option', 'tcp' ); ?>"><?php echo tcp_get_the_thumbnail( $post_id, $option->id_to, $post_2->ID, array( '50', '50' ) ); ?></a></td>
		<td><span style="padding-left: 2em;">&nbsp;</span><a href="post.php?action=edit&post_type=tcp_product_option&post=<?php echo $post_2->ID;?>" title="<?php _e( 'edit option', 'tcp' ); ?>"><?php echo $post_2->post_title;?></a></td>
		<td><?php echo $post_2->post_content;?></td>
		<td><?php echo $post_2->post_status;?></td>
		<td><?php echo tcp_format_the_price( tcp_get_the_price( $post_2->ID ) ); ?>&nbsp;<?php echo _x( 'Order', 'to sort lists', 'tcp' ); ?>:&nbsp;<?php echo tcp_get_the_order( $post_2->ID ); ?></td>
		<td><a href="post.php?action=edit&post_type=tcp_product_option&post=<?php echo $post_2->ID;?>"><?php _e( 'edit option', 'tcp' ); ?></a></td>
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
</div><?php
	}
}

new RelationsMetabox();
endif; // class_exists check