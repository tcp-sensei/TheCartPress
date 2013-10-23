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
	<?php screen_icon( 'tcp-post-type-list' ); ?><h2><?php _e( 'Post types', 'tcp' ); ?></h2>
<ul class="subsubsub">
	<li><a href="<?php echo TCP_ADMIN_PATH; ?>PostTypeEdit.php"><?php _e( 'Add new post type', 'tcp' ); ?></a></li>
</ul>
<div class="clear"></div>
<?php if ( isset( $_REQUEST['tcp_delete_post_type'] ) && isset( $_REQUEST['post_type'] )  && tcp_exist_custom_post_type( $_REQUEST['post_type'] ) ) :
	tcp_delete_custom_post_type( $_REQUEST['post_type'] );
	update_option( 'tcp_rewrite_rules', true ); ?>
	<div id="message" class="updated"><p><?php _e( 'Post type deleted', 'tcp' );?></p></div>
<?php endif; ?>
<script>
jQuery(document).ready(function() {
	jQuery('.tcp_show_delete_area').click(function() {
		var id = jQuery(this).attr('id');
		jQuery('.tcp_delete_post_type_area').hide();
		jQuery('#tcp_delete_area_' + id).show();
		return false;
	});
	jQuery('.tcp_no_delete').click(function() {
		jQuery('.tcp_delete_post_type_area').hide();
		return false;
	});
	jQuery('.tcp_delete_post_type').click(function() {
		jQuery(this).parent('form').submit();
		return false;
	});
});
</script>

<table class="widefat fixed" cellspacing="0">
<thead>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Id', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Activate', 'tcp' ); ?></th>
	<th scope="col" class="manage-column" style="width: 20%;">&nbsp;</th>
</tr>
</thead>
<tfoot>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Id', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Description', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Activate', 'tcp' ); ?></th>
	<th scope="col" class="manage-column" style="width: 20%;">&nbsp;</th>
</tr>
</tfoot>
<tbody>
<?php $post_type_defs = tcp_get_custom_post_types();
if ( is_array( $post_type_defs ) && count( $post_type_defs ) > 0 ) :
	foreach( $post_type_defs as $post_type => $post_type_def ) :?>
<tr>
	<td><?php echo $post_type_def['name']; ?></td>
	<td><?php echo $post_type; ?></td>
	<td><?php echo $post_type_def['desc']; ?></td>
	<td><?php $post_type_def['activate'] ? _e( 'Activated', 'tcp' ) : _e( 'No Activated', 'tcp' ); ?></td>
	<td><a href="<?php echo TCP_ADMIN_PATH; ?>PostTypeEdit.php&post_type=<?php echo $post_type; ?>"><?php _e( 'edit', 'tcp' ); ?></a>
	 | <a href="" class="tcp_show_delete_area" id="<?php echo $post_type;?>"><?php _e( 'delete', 'tcp' ); ?></a></div>
		<div id="tcp_delete_area_<?php echo $post_type; ?>" class="tcp_delete_post_type_area" style="display:none; border: 1px dotted orange; padding: 2px">
			<form method="post">
			<input type="hidden" name="post_type" value="<?php echo $post_type; ?>" />
			<input type="hidden" name="tcp_delete_post_type" value="y" />
			<p><?php _e( 'Do you really want to delete this post type?', 'tcp' ); ?></p>
			<a href="" class="tcp_delete_post_type"><?php _e( 'Yes' , 'tcp' ); ?></a> |
			<a href="" class="tcp_no_delete"><?php _e( 'No, I don\'t' , 'tcp' ); ?></a>
			</form>
		</div>
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
