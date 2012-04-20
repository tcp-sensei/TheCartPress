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

if ( isset( $_REQUEST['tcp_save_templates'] ) ) {
	$post_types = $_REQUEST['tcp_custom_post_type_template_id'];
	$templates = $_REQUEST['tcp_custom_post_type_template'];
	foreach( $post_types as $id => $post_type )
		tcp_set_custom_template_by_post_type( $post_type, $templates[$id] );
//		echo 'Save Post type=', $post_type, ' template=', $templates[$id], '<br>';
echo '<br>';
	$taxonomies = $_REQUEST['tcp_custom_taxonomy_template_id'];
	$templates = $_REQUEST['tcp_custom_taxonomy_template'];
	foreach( $taxonomies as $id => $taxonomy )
		tcp_set_custom_template_by_taxonomy( $taxonomy, $templates[$id] );
//		echo 'Save taxonomy=', $taxonomy, ' template=', $templates[$id], '<br>';
echo '<br>';
	$terms = $_REQUEST['tcp_custom_term_template_id'];
	$templates = $_REQUEST['tcp_custom_term_template'];
	foreach( $terms as $id => $term_id )
		tcp_set_custom_template_by_term( $term_id, $templates[$id] );
//		echo 'Save term_id=', $term_id, ' template=', $templates[$id], '<br>';
?><div id="message" class="updated"><p>
	<?php _e( 'Custom templates updated', 'tcp' ); ?>
</p></div><?php
}
?>

<div class="wrap">

<h2><?php _e( 'List of Custom templates', 'tcp' ); ?></h2>
<div class="clear"></div>

<form method="post">
<p class="submit"><input type="submit" name="tcp_save_templates" value="<?php _e( 'Update templates', 'tcp' ); ?>" class="button-primary"/></p>
<table class="widefat fixed" cellspacing="0">
<thead>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Name id', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Template', 'tcp' ); ?></th>
</tr>
</thead>

<tfoot>
<tr>
	<th scope="col" class="manage-column"><?php _e( 'Name', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Name id', 'tcp' ); ?></th>
	<th scope="col" class="manage-column"><?php _e( 'Template', 'tcp' ); ?></th>
</tr>
</tfoot>
<tbody>

<?php $templates = tcp_get_custom_templates();
$post_types = get_post_types( array( 'show_in_nav_menus' => true ), object );
foreach( $post_types as $post_type ) : ?>
	<tr class="tcp_<?php echo $post_type->name; ?> alternate">
		<td><?php echo $post_type->labels->name; ?></td>
		<td><?php echo $post_type->name; ?></td>
		<?php $custom_template = tcp_get_custom_template_by_post_type( $post_type->name ); ?>
		<td style="<?php echo $custom_template == '' ? 'padding-left: 2em;' : ''; ?>">
			<input type="hidden" name="tcp_custom_post_type_template_id[]" value="<?php echo $post_type->name; ?>"/>
			<select name="tcp_custom_post_type_template[]" id="tcp_custom_post_type_template">
				<option value="" <?php selected( ! $custom_template ); ?>><?php _e( 'Default Template', 'tcp' ); ?></option>
				<?php foreach( $templates as $template => $file_name ) : ?>
				<option value="<?php echo $template; ?>" <?php selected( $custom_template, $template ); ?>><?php echo $file_name; ?></option>
				<?php endforeach;
				if ( $custom_template && ! array_key_exists( $custom_template, $templates ) ) : ?>
				<option value="<?php echo $custom_template; ?>" selected="true"><?php printf( __( '"%s" is missing', 'tcp' ), basename( $custom_template ) ); ?></option>
				<?php endif; ?>
			</select>
		</td>
	</tr>
		<?php $taxonomies = get_object_taxonomies( $post_type->name, object );
		foreach( $taxonomies as $taxonomy_object ) : ?>
	<tr>
		<td style="padding-left: 2em;"><?php echo $taxonomy_object->labels->name; ?></td>
		<td></td>
		<?php $custom_template = tcp_get_custom_template_by_taxonomy( $taxonomy_object->name ); ?>
		<td style="<?php echo $custom_template == '' ? 'padding-left: 2em;' : ''; ?>">
			<input type="hidden" name="tcp_custom_taxonomy_template_id[]" value="<?php echo $taxonomy_object->name; ?>"/>
			<select name="tcp_custom_taxonomy_template[]" id="tcp_custom_taxonomy_template">
				<option value="" <?php selected( ! $custom_template ); ?>><?php _e( 'Default Template', 'tcp' ); ?></option>
				<?php foreach( $templates as $template => $file_name ) : ?>
				<option value="<?php echo $template; ?>" <?php selected( $custom_template, $template ); ?>><?php echo $file_name; ?></option>
				<?php endforeach; 
				if ( $custom_template && ! array_key_exists( $custom_template, $templates ) ) : ?>
				<option value="<?php echo $custom_template; ?>" selected="true"><?php printf( __( '"%s" is missing', 'tcp' ), basename( $custom_template ) ); ?></option>
				<?php endif; ?>
			</select>
		</td>
	</tr><?php
			$taxonomy = get_taxonomy( $taxonomy_object->name );
			$args = array (
				'taxonomy'		=> $taxonomy,
				'hide_empty'	=> false,
			);
			$terms = get_terms( $taxonomy_object->name, 'orderby=name&hide_empty=false');
			foreach( $terms as $term ) : ?>
			<td style="padding-left: 4em;"><?php echo $term->name; ?></td>
			<td><?php echo $term->slug; ?></td>
			<?php $custom_template = tcp_get_custom_template_by_term( $term->term_id ); ?>
			<td style="<?php echo $custom_template == '' ? 'padding-left: 2em;' : ''; ?>">
				<input type="hidden" name="tcp_custom_term_template_id[]" value="<?php echo $term->term_id; ?>"/>
				<select name="tcp_custom_term_template[]" id="tcp_custom_term_template_<?php echo $term->term_id; ?>">
					<option value="" <?php selected( ! $custom_template ); ?>><?php _e( 'Default Template', 'tcp' ); ?></option>
					<?php foreach( $templates as $template => $file_name ) : ?>
					<option value="<?php echo $template; ?>" <?php selected( $custom_template, $template ); ?>><?php echo $file_name; ?></option>
					<?php endforeach; 
					if ( $custom_template && ! array_key_exists( $custom_template, $templates ) ) : ?>
				<option value="<?php echo $custom_template; ?>" selected="true"><?php printf( __( '"%s" is missing', 'tcp' ), basename( $custom_template ) ); ?></option>
				<?php endif; ?>
				</select>
			</td>
		</tr><?php
			endforeach;
		endforeach;
endforeach; ?>
</tbody>
</table>
<p class="submit"><input type="submit" name="tcp_save_templates" value="<?php _e( 'Update templates', 'tcp' ); ?>" class="button-primary"/></p>
</form>
