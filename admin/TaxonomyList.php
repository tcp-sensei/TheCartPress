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

?>
<div class="wrap">
<h2><?php _e( 'Taxonomies', 'tcp' ); ?></h2>
<ul class="subsubsub">
	<li><a href="<?php echo TCP_ADMIN_PATH; ?>TaxonomyEdit.php"><?php _e( 'Add new taxonomy', 'tcp' ); ?></a></li>
</ul>
<div class="clear"></div>

<?php if ( isset( $_REQUEST['tcp_delete_taxonomy'] ) && isset( $_REQUEST['taxonomy'] )  && tcp_exist_custom_taxonomy( $_REQUEST['taxonomy'] ) ) :
	tcp_delete_custom_taxonomy( $_REQUEST['taxonomy'] );
	update_option( 'tcp_rewrite_rules', true ); ?>
	<div id="message" class="updated"><p><?php _e( 'Taxonomy deleted', 'tcp' );?></p></div>
<?php endif; ?>
<?php do_action( 'tcp_taxonomy_list_actions' ); ?>
<script>
jQuery(document).ready(function() {
	jQuery('.tcp_show_delete_area').click(function() {
		var id = jQuery(this).attr('id');
		jQuery('.tcp_delete_taxonomy_area').hide();
		jQuery('#tcp_delete_area_' + id).show(200);
		return false;
	});
	jQuery('.tcp_no_delete').click(function() {
		jQuery('.tcp_delete_taxonomy_area').hide(100);
		return false;
	});
	jQuery('.tcp_delete_taxonomy').click(function() {
		jQuery(this).parent('form').submit();
		return false;
	});
});
</script>

<table class="widefat fixed" cellspacing="0">
<thead>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Post type', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Id', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Activate', 'tcp' ); ?></th>
	<th scope="col" class="manage-column" style="width: 20%;">&nbsp;</th>
</tr>
</thead>
<tfoot>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Post type', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Id', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Activate', 'tcp' ); ?></th>
	<th scope="col" class="manage-column" style="width: 20%;">&nbsp;</th></tr>
</tfoot>
<tbody>
<?php $taxonomy_defs = tcp_get_custom_taxonomies();

if ( is_array( $taxonomy_defs ) && count( $taxonomy_defs ) > 0 ) :
	foreach( $taxonomy_defs as $taxonomy => $taxonomy_def ) : ?>
<tr>
	<td><?php $post_types = $taxonomy_def['post_type'];
		if ( ! is_array( $post_types ) ) $post_types = array( $post_types );
		$post_type_names = array();
		foreach( $post_types as $post_type ) {
			$object = get_post_type_object( $post_type );
			if ( $object ) {
				$post_type_names[] = $object->labels->name;
			} else {
				$post_type_names[] = _e( 'No post type', 'tcp' );
			}
		}
		echo implode( ', ', $post_type_names ); ?>
	</td>
	<td><?php echo $taxonomy_def['name']; ?></td>
	<td><?php echo $taxonomy; ?></td>
	<td><?php echo $taxonomy_def['desc']; ?>&nbsp;</td>
	<td><?php $taxonomy_def['activate'] ? _e( 'Activated', 'tcp' ) : _e( 'No Activated', 'tcp' ); ?></td>
	<td><a href="<?php echo TCP_ADMIN_PATH; ?>TaxonomyEdit.php&taxonomy=<?php echo $taxonomy; ?>"><?php _e( 'Edit', 'tcp' ); ?></a>
	| <a href="#" class="tcp_show_delete_area" id="<?php echo $taxonomy; ?>"><?php _e( 'delete', 'tcp' ); ?></a></div>
		<div id="tcp_delete_area_<?php echo $taxonomy; ?>" class="tcp_delete_taxonomy_area" style="display:none; border: 1px dotted orange; padding: 2px">
			<form method="post">
			<input type="hidden" name="taxonomy" value="<?php echo $taxonomy; ?>" />
			<input type="hidden" name="tcp_delete_taxonomy" value="y" />
			<p><?php _e( 'Do you really want to delete this taxonomy?', 'tcp' ); ?></p>
			<a href="#" class="tcp_delete_taxonomy"><?php _e( 'Yes' , 'tcp' ); ?></a> |
			<a href="#" class="tcp_no_delete"><?php _e( 'No, I don\'t' , 'tcp' ); ?></a>
			</form>
		</div>
		<?php do_action( 'tcp_taxonomy_list_action_list', $taxonomy, $taxonomy_def ); ?>
	</td>
</tr>
	<?php endforeach; ?>
<?php else : ?>
<tr>
	<td colspan="5"><?php _e( 'The list is empty', 'tcp' ); ?></td>
</tr>
<?php endif; ?>
</tbody>
</table>
</div>
