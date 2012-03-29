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

require_once( dirname( dirname( __FILE__ ) ).'/daos/RelEntities.class.php' );

$post_id	= isset( $_REQUEST['post_id'] ) ? $_REQUEST['post_id'] : 0;
if ( $post_id == 0 ) die( __( 'post_id param required!!!', 'tcp' ) );
$rel_type	= isset( $_REQUEST['rel_type'] ) ? $_REQUEST['rel_type'] : 'PROD-CAT_POST';

$post = get_post( $post_id );

if ( isset( $_REQUEST['tcp_save_related_categories'] ) ) {
	RelEntities::deleteAll( $post_id, $rel_type );
	$rels = isset( $_REQUEST['tcp_terms'] ) ? $_REQUEST['tcp_terms'] : array( '' );
	if ( is_array( $rels ) && $rels[0] == '' ) {
		?><div id="message" class="updated"><p>
		<?php _e( 'The relations has been deleted', 'tcp' ); ?>
		</p></div><?php
	} else {
		foreach( $rels as $term ) {
			RelEntities::insert( $post_id, $term, $rel_type );
		}
		?><div id="message" class="updated"><p>
		<?php _e( 'The relations has been created', 'tcp' ); ?>
		</p></div><?php
	}
} else {
	$rels = RelEntities::select( $post_id, $rel_type );
	$simple_rels = array();
	foreach( $rels as $rel)
		$simple_rels[] = $rel->id_to;
	$rels = $simple_rels;
}
?>
<div class="wrap">
<h2><?php printf( __( 'Related categories for %s', 'tcp' ), $post->post_title ); ?></h2>
<ul class="subsubsub">
	<li><a href="post.php?action=edit&post=<?php echo $post_id; ?>"><?php printf( __( 'return to %s', 'tcp' ), $post->post_title ); ?></a></li>
</ul><!-- subsubsub -->
	
<div class="clear"></div>

<form method="post">
<input type="hidden" name="post_id" value="<?php echo $post_id;?>" />
<input type="hidden" name="rel_type" value="<?php echo $rel_type;?>" />

<p><input type="submit" value="<?php _e( 'Save', 'tcp' );?>" class="button-primary" name="tcp_save_related_categories" /></p>
<table class="widefat fixed"><!-- Assigned -->
<thead>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Related categories', 'tcp' );?></th>
</tr>
</thead>
<tfoot>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Related categories', 'tcp' );?></th>
</tr>
</tfoot>
<tbody>
<tr>
	<td>
		<select id="tcp_terms" name="tcp_terms[]" style="height: auto;" multiple size="8" >
			<option value=""><?php _e( 'none', 'tcp' ); ?></option>
		<?php $terms = get_terms( $rel_type == 'PROD-CAT_POST' ? 'category' : 'tcp_product_category' );
		foreach( $terms as $term ) : ?>
			<option value="<?php echo $term->term_id; ?>" <?php tcp_selected_multiple( $rels, $term->term_id ); ?>><?php echo $term->name; ?></option>
		<?php endforeach; ?>
		</select>
	</td>
</tr>
</tbody>
</table>
<p><input type="submit" value="<?php _e( 'Save', 'tcp' );?>" class="button-primary" name="tcp_save_related_categories" /></p>
</form>
</div><!-- .wrap -->

