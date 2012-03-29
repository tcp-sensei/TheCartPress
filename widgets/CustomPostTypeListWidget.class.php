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
 
require_once( TCP_WIDGETS_FOLDER . 'CustomListWidget.class.php' );
 
class CustomPostTypeListWidget extends CustomListWidget {

	function CustomPostTypeListWidget() {
		parent::__construct( 'customposttypelist', __( 'Allow to create Custom Post Type Lists', 'tcp' ), 'TCP Custom Post Type List' );
	}

	function widget( $args, $instance ) {
		extract( $args );
		global $wp_query;
		$loop_args = array(
			'post_type'			=> isset( $instance['post_type'] ) ? $instance['post_type'] : TCP_PRODUCT_POST_TYPE,
			'posts_per_page'	=> isset( $instance['limit'] ) ? $instance['limit'] : -1,
		);
		$see_pagination = isset( $instance['see_pagination'] ) ? $instance['see_pagination'] : false;
		if ( $see_pagination ) $loop_args['paged'] = isset( $wp_query->query_vars['paged'] ) ? $wp_query->query_vars['paged'] : 1;
		if ( isset( $instance['use_taxonomy'] ) && $instance['use_taxonomy'] ) {
			$taxonomy = ( $instance['taxonomy'] == 'category' ) ? 'category_name' : $instance['taxonomy'];
			if ( strlen( $taxonomy ) > 0 ) {
				$loop_args[$taxonomy] = $instance['term'];
			}
		} else {
			if ( isset( $instance['included'] ) && count( $instance['included'] ) > 0 && strlen( $instance['included'][0] ) > 0 ) {
				$loop_args['post__in'] = $instance['included'];
			}
		}
		$loop_args = apply_filters( 'tcp_custom_post_type_list_widget', $loop_args, $instance );
		parent::widget( $args, $loop_args, $instance );
	}

	function update( $new_instance, $old_instance ) {
		$instance = parent::update( $new_instance, $old_instance );
		$instance['post_type']			= $new_instance['post_type'];
		$instance['use_taxonomy']		= $new_instance['use_taxonomy'] == 'yes';
		$instance['taxonomy']			= $new_instance['taxonomy'];
		$instance['term']				= $new_instance['term'];
		$instance['related_type']		= $new_instance['related_type'];
		$instance['included']			= $new_instance['included'];
		$instance['order_type']			= $new_instance['order_type'];
		$instance['order_desc']			= $new_instance['order_desc'];
		$instance['see_posted_on']		= $new_instance['see_posted_on'] == 'yes';
		$instance['see_taxonomies']		= $new_instance['see_taxonomies'] == 'yes';
		$instance['see_meta_utilities']	= $new_instance['see_meta_utilities'] == 'yes';
		return apply_filters( 'tcp_custom_post_type_list_widget_update', $instance, $new_instance );
	}

	function form( $instance ) {
		parent::form( $instance, __( 'Custom Post type', 'tcp' ) );
		$defaults = array(
			'post_type'			=> TCP_PRODUCT_POST_TYPE,
			'taxonomy'			=> true,
			'term'				=> TCP_PRODUCT_CATEGORY,
			'included'			=> array(),
		);
		$instance = wp_parse_args( ( array ) $instance, $defaults );
		$order_type			= isset( $instance['order_type'] ) ? $instance['order_type'] : 'date';
		$order_desc			= isset( $instance['order_desc'] ) ? $instance['order_desc'] : 'asc';
		$use_taxonomy		= isset( $instance['use_taxonomy'] ) ? $instance['use_taxonomy'] : true;
		$related_type		= isset( $instance['related_type'] ) ? $instance['related_type'] : '';
		if ( $use_taxonomy ) {
			$use_taxonomy_style	= '';
			$included_style		= 'display: none;';
		} else {
			$use_taxonomy_style	= 'display: none;';
			$included_style		= '';
		}
		if ( $related_type != '') {
			$p_included_style	= 'display: none;';
		} else {
			$p_included_style	= '';
		}?>
		<script>
		function tcp_show_taxonomy(checked) {
			if (checked) {
				jQuery('.tcp_taxonomy_controls').show();
				jQuery('.tcp_post_included').hide();
			} else {
				jQuery('.tcp_taxonomy_controls').hide();
				jQuery('.tcp_post_included').show();
			}
		}
		</script>
		<p>
			<label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( 'Post type', 'tcp' )?>:</label>
			<select name="<?php echo $this->get_field_name( 'post_type' ); ?>" id="<?php echo $this->get_field_id( 'post_type' ); ?>" class="widefat">
			<?php foreach( get_post_types( array( 'show_in_nav_menus' => true ), object ) as $post_type ) : ?>
				<option value="<?php echo $post_type->name; ?>"<?php selected( $instance['post_type'], $post_type->name ); ?>><?php echo $post_type->labels->name; ?></option>
			<?php endforeach; ?>
			</select>
			<span class="description"><?php _e( 'Press save to load the next list', 'tcp' ); ?></span>
		</p><p style="margin-bottom:0;">
			<input type="checkbox" class="checkbox" onclick="tcp_show_taxonomy(this.checked);" id="<?php echo $this->get_field_id( 'use_taxonomy' ); ?>" name="<?php echo $this->get_field_name( 'use_taxonomy' ); ?>" value="yes" <?php checked( $use_taxonomy ); ?> />
			<label for="<?php echo $this->get_field_id( 'use_taxonomy' ); ?>"><?php _e( 'Use Taxonomy', 'tcp' ); ?></label>
		</p>
		<div class="tcp_taxonomy_controls" style="<?php echo $use_taxonomy_style; ?>">
			<p style="margin-top:0;">
				<label for="<?php echo $this->get_field_id( 'taxonomy' ); ?>"><?php _e( 'Taxonomy', 'tcp' )?>:</label>
				<select name="<?php echo $this->get_field_name( 'taxonomy' ); ?>" id="<?php echo $this->get_field_id( 'taxonomy' ); ?>" class="widefat">
					<option value="" <?php selected( $instance['taxonomy'], '' ); ?>><?php _e( 'all', 'tcp' ); ?></option>
				<?php foreach( get_object_taxonomies( $instance['post_type'] ) as $taxonomy ) : $tax = get_taxonomy( $taxonomy ); ?>
					<option value="<?php echo esc_attr( $taxonomy ); ?>"<?php selected( $instance['taxonomy'], $taxonomy ); ?>><?php echo esc_attr( $tax->labels->name ); ?></option>
				<?php endforeach; ?>
				</select>
				<span class="description"><?php _e( 'Press save to load the next list', 'tcp' ); ?></span>
			</p><p>
				<label for="<?php echo $this->get_field_id( 'term' ); ?>"><?php _e( 'Term', 'tcp' )?>:</label>
				<select name="<?php echo $this->get_field_name( 'term' ); ?>" id="<?php echo $this->get_field_id( 'term' ); ?>" class="widefat">
				<?php if ( $instance['taxonomy'] ) : 
					$term_slug = isset( $instance['term'] ) ? $instance['term'] : '';
					$terms = get_terms( $instance['taxonomy'], array( 'hide_empty' => false ) );
					if ( is_array( $terms ) && count( $terms ) )
						foreach( $terms as $term ) : 
							if ( $term->term_id == tcp_get_default_id( $term->term_id, $instance['taxonomy'] ) ) :?>
								<option value="<?php echo $term->slug; ?>"<?php selected( $term_slug, $term->slug ); ?>><?php echo esc_attr( $term->name ); ?></option>
							<?php endif;
						endforeach;
				endif; ?>
				</select>
			</p>
		</div> <!-- tcp_taxonomy_controls -->
		<div class="tcp_post_included" style="<?php echo $included_style; ?>">
			<div id="p_included" style="<?php echo $p_included_style; ?>"><p style="margin-top:0;">
				<label for="<?php echo $this->get_field_id( 'included' ); ?>"><?php _e( 'Included', 'tcp' )?>:</label>
				<select name="<?php echo $this->get_field_name( 'included' ); ?>[]" id="<?php echo $this->get_field_id( 'included' ); ?>" class="widefat" multiple size="8" style="height: auto">
					<option value="" <?php selected( $instance['included'], '' ); ?>><?php _e( 'all', 'tcp' ); ?></option>
				<?php
				$args = array(
					'post_type'			=> $instance['post_type'],
					'posts_per_page'	=> -1,
					'fields'			=> 'ids',
				);
				if ( $instance['post_type'] == TCP_PRODUCT_POST_TYPE ) {
					$args['meta_key'] = 'tcp_is_visible';
					$args['meta_value'] = true;
				}
				$included = isset( $instance['included'] ) ? $instance['included'] : array();
				if ( ! is_array( $included ) ) $included = array();
				$ids = get_posts( $args );
				if ( is_array( $ids ) && count( $ids ) ) :
					foreach( $ids as $id ) : $post = get_post( $id ); ?>
					<option value="<?php echo $id; ?>"<?php tcp_selected_multiple( $included, $post->ID ); ?>><?php echo $post->post_title; ?></option>
					<?php endforeach;
				endif; ?>
				</select>
				</p>
			</div><!-- p_included -->
		</div><!-- tcp_post_included -->
		
		<p>
			<label for="<?php echo $this->get_field_id( 'order_type' ); ?>"><?php _e( 'Order by', 'tcp' ); ?></label>:
			<?php $sorting_fields = tcp_get_sorting_fields();
			//$sorting_fields[] = array( 'value' => 'rand', 'title' => __( 'Random', 'tcp' ) ); ?>
			<select id="<?php echo $this->get_field_id( 'order_type' ); ?>" name="<?php echo $this->get_field_name( 'order_type' ); ?>">
			<?php foreach( $sorting_fields as $sorting_field ) : ?>
			<option value="<?php echo $sorting_field['value']; ?>" <?php selected( $order_type, $sorting_field['value'] ); ?>><?php echo $sorting_field['title']; ?></option>
			<?php endforeach; ?>
			</select>
			<input type="radio" name="<?php echo $this->get_field_name( 'order_desc' ); ?>" id="<?php echo $this->get_field_id( 'order_desc' ); ?>" value="asc" <?php checked( $instance['order_desc'], 'asc' ); ?>/>
			<label for="<?php echo $this->get_field_id( 'order_desc' ); ?>"><?php _e( 'Asc.', 'tcp' ); ?></label>
			<input type="radio" name="<?php echo $this->get_field_name( 'order_desc' ); ?>" id="<?php echo $this->get_field_id( 'order_desc' ); ?>" value="desc" <?php checked( $instance['order_desc'], 'desc' ); ?>/>
			<label for="<?php echo $this->get_field_id( 'order_desc' ); ?>"><?php _e( 'Desc.', 'tcp' ); ?></label>
		</p>
		<?php parent::show_post_type_form( $instance );
	}
}
?>
