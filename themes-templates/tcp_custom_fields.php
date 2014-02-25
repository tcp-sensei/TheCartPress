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

//$instance:	 array( 'post_type', 'see_label', 'hide_empty_fields', 'see_links', 'selected_custom_fields' )
//$post_id:		 Current post id (or product id)
//$field_ids:	 Custom fields to display
//$other_values: Other "custom fields" that are not custom fields, as 'price', 'sku', 'stock', etc...

ob_start();
foreach( $field_ids as $id ) {
	if ( $id == '' ) continue;
	
	if ( substr( $id, 0, 12 ) == 'custom_field' ) {
		$field_id = substr( $id, 13 );
		$value = get_post_meta( $post_id, $field_id, true );
		if ( $value == '' && $instance['hide_empty_fields'] ) continue;
		if ( $value == '' ) $value == '&nbsp;';
		if ( $instance['see_label'] ) {
			$field_def = tcp_get_custom_field_def( $field_id, $instance['post_type'] );
			$label = tcp_string( 'TheCartPress', 'custom_field_' . $field_id . '-label', $field_def['label'] );
		}
	} elseif ( substr( $id, 0, 3 ) == 'tax' ) {
		$tax_id = substr( $id, 4 );
		$value = '';
		$term_list = wp_get_post_terms( $post_id, $tax_id, array( 'fields' => 'names' ) );
		if ( is_array( $term_list ) && count( $term_list ) > 0 ) {
			foreach( $term_list as $term )
				$value .= $term . ', ';
			$value = substr( $value, 0, -2 );
		}
		if ( $value == '' && $instance['hide_empty_fields'] ) continue;
		if ( $instance['see_label'] ) {
			$tax = get_taxonomy( $tax_id );
			$label = $tax->labels->name;
			$label = tcp_string( 'TheCartPress', 'custom_tax_' . $tax->labels->post_type . '_' . $tax_id . '-name', $label );
			//$label = tcp_string( 'TheCartPress', 'custom_tax_' . $tax_id . '-name', $label );
		}
	} elseif ( substr( $id, 0, 3 ) == 'o_v' ) {
		$ov_id = substr( $id, 4 );
		if ( ! isset( $other_values[$ov_id] ) ) continue;
		if ( function_exists( $other_values[$ov_id]['callback'] ) ) $value = $other_values[$ov_id]['callback']();
		else $value = '';
		if ( $value == '' && $instance['hide_empty_fields'] ) continue;
		if ( $instance['see_label'] ) {
			$label = isset( $other_values[$ov_id]['label'] ) ? $other_values[$ov_id]['label'] : false;
		}
	} ?>
<?php if ( $instance['see_label'] ) : ?><dt class="tcp-<?php echo $id; ?>"><?php echo $label; ?></dt><?php endif; ?>
	<dd><?php echo $value; ?></dd>
<?php }
$html = ob_get_clean();
if ( strlen( $html ) > 0 ) { ?>
<dl>
	<?php echo $html; ?>
</dl>
<?php }