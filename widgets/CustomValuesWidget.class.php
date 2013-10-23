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

require_once( TCP_WIDGETS_FOLDER . 'TCPParentWidget.class.php' );

class CustomValuesWidget extends TCPParentWidget {

	function CustomValuesWidget() {
		parent::__construct( 'customvalues', __( 'Allows to create Custom Values Lists', 'tcp' ), 'TCP Custom Values' );
	}

	function widget( $args, $instance ) {
		if ( ! parent::widget( $args, $instance ) ) return;
		extract( $args );
		ob_start();
		tcp_display_custom_values( 0, $instance );
		$html = ob_get_clean();
		if ( strlen( $html ) > 0 ) {
			echo $before_widget;
			$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : false );
			if ( $title ) echo $before_title, $title, $after_title;
			echo $html;
			echo $after_widget;
		}
	}

	function update( $new_instance, $old_instance ) {
		$instance = parent::update( $new_instance, $old_instance );
		$instance['post_type'] = $new_instance['post_type'];
		$instance['see_label'] = $new_instance['see_label'] == 'yes';
		$instance['hide_empty_fields'] = $new_instance['hide_empty_fields'] == 'yes';
		$instance['see_links'] = $new_instance['see_links'] == 'yes';
		$instance['selected_custom_fields'] =  str_replace( ',,', ',', $new_instance['selected_custom_fields'] );
		return apply_filters( 'tcp_custom_values_widget_update', $instance, $new_instance );
	}

	function form( $instance ) {
		parent::form( $instance, __( 'Custom Values', 'tcp' ) );

		$defaults = array(
			'post_type' => 'tcp_product',
			'see_label' =>  true,
			'hide_empty_fields' => true,
			'see_links' => false,
			'selected_custom_fields' => '',
			'post_type' => TCP_PRODUCT_POST_TYPE,
		);
		$instance = wp_parse_args( (array)$instance, $defaults );

		$tcp_custom_fields = $this->get_field_id( 'tcp_custom_fields' );
		$tcp_add_custom_field = $this->get_field_id( 'tcp_add_custom_field' );

		$tcp_taxomonies = $this->get_field_id( 'tcp_taxonomy' );
		$tcp_add_tax = $this->get_field_id( 'tcp_add_tax' );

		$tcp_other_values = $this->get_field_id( 'tcp_other_values' );
		$tcp_add_other_value = $this->get_field_id( 'tcp_add_other_value' );

		$tcp_custom_field_list = $this->get_field_id( 'tcp_custom_field_list' );
		$tcp_selected_custom_fields = $this->get_field_id( 'tcp_selected_custom_fields' ); ?>
<p>
	<label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( 'Post type', 'tcp' )?>:</label>
	<select name="<?php echo $this->get_field_name( 'post_type' ); ?>" id="<?php echo $this->get_field_id( 'post_type' ); ?>" class="widefat">
	<?php foreach( get_post_types( array( 'show_in_nav_menus' => true ) ) as $post_type ) : 
		if ( $post_type != 'tcp_product_option' ) : 
			$obj_type = get_post_type_object( $post_type ); ?>
		<option value="<?php echo $post_type;?>"<?php selected( $instance['post_type'], $post_type ); ?>><?php echo $obj_type->labels->singular_name; ?></option>
		<?php endif;?>
	<?php endforeach; ?>
	</select>
	<span class="description"><?php _e( 'Press save to load the next list', 'tcp' );?></span>
</p>
<p>
	<label for="<?php echo $tcp_custom_fields; ?>"><?php _e( 'Custom Fields', 'tcp' ); ?>:</label>
	<select id="<?php echo $tcp_custom_fields; ?>">
	<?php $custom_fields_def = tcp_get_custom_fields_def();
	foreach( $custom_fields_def as $i => $def ) { ?>
		<option value="custom_field-<?php echo $def['id']; ?>"><?php echo esc_attr( $def['label'] ); ?></option>
	<?php } ?>
	</select>
	<a href="#" id="<?php echo $tcp_add_custom_field; ?>"><?php _e( 'Add', 'tcp' ); ?></a>
</p>
<p>
	<label for="<?php echo $tcp_taxomonies; ?>"><?php _e( 'Taxonomies', 'tcp' )?>:</label>
	<select id="<?php echo $tcp_taxomonies; ?>" >
	<?php foreach( get_object_taxonomies( $instance['post_type'] ) as $taxonomy ) : $tax = get_taxonomy( $taxonomy ); ?>
		<option value="tax-<?php echo esc_attr( $taxonomy ); ?>"><?php echo esc_attr( $tax->labels->name ); ?></option>
	<?php endforeach; ?>
	</select>
	<a href="#" id="<?php echo $tcp_add_tax; ?>"><?php _e( 'Add', 'tcp' ); ?></a>
</p>
<p>
	<label for="<?php echo $tcp_other_values; ?>"><?php _e( 'Other values', 'tcp' )?>:</label>
	<select id="<?php echo $tcp_other_values; ?>">
	<?php $other_values = apply_filters( 'tcp_custom_values_get_other_values', array() );
	foreach( $other_values as $id => $other_value ) : ?>
		<option value="o_v-<?php echo $id; ?>"><?php echo esc_attr( $other_value['label'] ); ?></option>
	<?php endforeach; ?>
	</select>
	<a href="#" id="<?php echo $tcp_add_other_value; ?>"><?php _e( 'Add', 'tcp' ); ?></a>
</p>

<ul id="<?php echo $tcp_custom_field_list; ?>">
<?php $field_ids = explode( ',', $instance['selected_custom_fields'] );
foreach( $field_ids as $field_id ) {
	if ( substr( $field_id, 0, 12 ) == 'custom_field' ) {
		$field_def = tcp_get_custom_field_def( substr( $field_id, 13 ) );
		if ( $field_def != false ) { ?>
	<li id="custom_field-<?php echo $field_def['id']; ?>"><?php echo $field_def['label']; ?> <a href="#" class="tcp_custom_value_remove" onclick="return tcp_remove_field( 'custom_field-<?php echo $field_def['id']; ?>', 'input#<?php echo $tcp_selected_custom_fields; ?>' );"> <?php _e( 'remove', 'tcp' ); ?></a></li>
		<?php }
	} elseif ( substr( $field_id, 0, 3 ) == 'tax' ) {
		$tax_id = substr( $field_id, 4 );
		$tax = get_taxonomy( $tax_id );
		if ( $tax != false ) { ?>
	<li id="tax-<?php echo $tax_id; ?>"><?php echo $tax->labels->name; ?> <a href="#" class="tcp_custom_value_remove" onclick="return tcp_remove_field( 'tax-<?php echo $tax_id; ?>', 'input#<?php echo $tcp_selected_custom_fields; ?>' );"> <?php _e( 'remove', 'tcp' ); ?></a></li>
		<?php }
	} elseif ( substr( $field_id, 0, 3 ) == 'o_v' ) {
		$ov_id = substr( $field_id, 4 );
		$ov = isset( $other_values[$ov_id] ) ? $other_values[$ov_id] : false;
		if ( $ov != false ) { ?>
	<li id="o_v-<?php echo $ov_id; ?>"><?php echo $ov['label']; ?> <a href="#" class="tcp_other_value_remove" onclick="return tcp_remove_field( 'o_v-<?php echo $ov_id; ?>', 'input#<?php echo $tcp_selected_custom_fields; ?>' );"> <?php _e( 'remove', 'tcp' ); ?></a></li>
		<?php }
	}
} ?>
</ul>

<p class="description"><?php _e( 'Add fields to the list, and drag and drop them to change order', 'tcp' ); ?></a>

<input type="hidden" name="<?php echo $this->get_field_name( 'selected_custom_fields' ); ?>" id="<?php echo $tcp_selected_custom_fields; ?>" value="<?php echo $instance['selected_custom_fields']; ?>"/>
<script>
jQuery( 'a#<?php echo $tcp_add_custom_field; ?>' ).click( function( event ) {
	var id = jQuery( 'select#<?php echo $tcp_custom_fields; ?>' ).val();
	var li = jQuery( '<li>' ).attr( 'id', id ).html( jQuery( 'select#<?php echo $tcp_custom_fields; ?> :selected' ).text() );
	var a = jQuery( '<a>' ).attr( 'href', '#' ).addClass( 'tcp_custom_value_remove' ).attr( 'onclick', 'return tcp_remove_field( \'' + id + '\', \'input#<?php echo $tcp_selected_custom_fields; ?>\' );' ).html( '<?php _e( 'remove', 'tcp' ); ?>' );
	li.append( a );
	jQuery( 'ul#<?php echo $tcp_custom_field_list; ?>' ).append( li );
	tcp_load_custom_fields( 'ul#<?php echo $tcp_custom_field_list; ?>', 'input#<?php echo $tcp_selected_custom_fields; ?>' );
	event.stopPropagation();
	return false;
} );

jQuery( 'a#<?php echo $tcp_add_tax; ?>' ).click( function( event ) {
	var id = jQuery( 'select#<?php echo $tcp_taxomonies; ?>' ).val();
	var li = jQuery( '<li>' ).attr( 'id', id ).html( jQuery( 'select#<?php echo $tcp_taxomonies; ?> :selected' ).text() );
	var a = jQuery( '<a>' ).attr( 'href', '#' ).addClass( 'tcp_custom_value_remove' ).attr( 'onclick', 'return tcp_remove_field( \'' + id + '\', \'input#<?php echo $tcp_selected_custom_fields; ?>\' );' ).html( '<?php _e( 'remove', 'tcp' ); ?>' );
	li.append( a );
	jQuery( 'ul#<?php echo $tcp_custom_field_list; ?>' ).append( li );
	tcp_load_custom_fields( 'ul#<?php echo $tcp_custom_field_list; ?>', 'input#<?php echo $tcp_selected_custom_fields; ?>' );
	event.stopPropagation();
	return false;
} );

jQuery( 'a#<?php echo $tcp_add_other_value; ?>' ).click( function( event ) {
	var id = jQuery( 'select#<?php echo $tcp_other_values; ?>' ).val();
	var li = jQuery( '<li>' ).attr( 'id', id ).html( jQuery( 'select#<?php echo $tcp_other_values; ?> :selected' ).text() );
	var a = jQuery( '<a>' ).attr( 'href', '#' ).addClass( 'tcp_other_value_remove' ).attr( 'onclick', 'return tcp_remove_field( \'' + id + '\', \'input#<?php echo $tcp_selected_custom_fields; ?>\' );' ).html( '<?php _e( 'remove', 'tcp' ); ?>' );
	li.append( a );
	jQuery( 'ul#<?php echo $tcp_custom_field_list; ?>' ).append( li );
	tcp_load_custom_fields( 'ul#<?php echo $tcp_custom_field_list; ?>', 'input#<?php echo $tcp_selected_custom_fields; ?>' );
	event.stopPropagation();
	return false;
} );

function tcp_load_custom_fields( ul, input ) {
	var jinput = jQuery( input );
	jinput.val( '' );
	jQuery( ul ).find( 'li' ).each( function() {
		jinput.val( jinput.val() + jQuery( this ).attr( 'id' ) + ',' );
	} );
}

function tcp_remove_field( id, input ) {
	var jinput = jQuery( input );
	jinput.val( jinput.val().replace( id + ',', '' ) );
	jQuery( 'li[id=' + id + ']' ).remove();
	return false;
}

jQuery( 'ul#<?php echo $tcp_custom_field_list; ?>' ).sortable( {
	stop: function(event, ui) { tcp_load_custom_fields( 'ul#<?php echo $tcp_custom_field_list; ?>', 'input#<?php echo $tcp_selected_custom_fields; ?>' ); },
} );
</script>

<p>
	<input type="checkbox" id="<?php echo $this->get_field_id( 'see_label' ); ?>" name="<?php echo $this->get_field_name( 'see_label' ); ?>" value="yes" <?php checked( $instance['see_label'] ); ?> />
	<label for="<?php echo $this->get_field_id( 'see_label' ); ?>"><?php _e( 'See label', 'tcp' ); ?></label>
</p>
<p>
	<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'hide_empty_fields' ); ?>" name="<?php echo $this->get_field_name( 'hide_empty_fields' ); ?>" value="yes" <?php checked( $instance['hide_empty_fields'] ); ?> />
	<label for="<?php echo $this->get_field_id( 'hide_empty_fields' ); ?>"><?php _e( 'Hide empty fields', 'tcp' ); ?></label>
</p>
<!--<p>
	<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_links' ); ?>" name="<?php echo $this->get_field_name( 'see_links' ); ?>" value="yes" <?php checked( $instance['see_links'] ); ?> />
	<label for="<?php echo $this->get_field_id( 'see_links' ); ?>"><?php _e( 'See links', 'tcp' ); ?></label>
</p>-->
<?php do_action( 'tcp_custom_values_widget_form', $instance );
	}
}

add_filter( 'tcp_custom_values_get_other_values', 'custom_values_widget_add_default_values' );

function custom_values_widget_add_default_values( $other_values ) {
	$other_values['wp_modifed_date'] = array(
		'label' => __( 'Modified date', 'tcp' ),
		'callback' => 'get_the_modified_date',
	);
	$other_values['wp_modifed_time'] = array(
		'label' => __( 'Modified time', 'tcp' ),
		'callback' => 'get_the_modified_time',
	);
	return $other_values;
}
?>