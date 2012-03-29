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

require_once( TCP_WIDGETS_FOLDER . 'TCPParentWidget.class.php' );

class TaxonomyTreesPostTypeWidget extends TCPParentWidget {
	function TaxonomyTreesPostTypeWidget() {
		parent::__construct( 'taxonomytreesposttype', __( 'Use this widget to add trees of different taxonomies', 'tcp' ), 'TCP Navigation Tree' );
	}
	
	function widget( $args, $instance ) {
		if ( ! parent::widget( $args, $instance ) ) return;
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( $title ) echo $before_title, $title, $after_title;
		$args = array(
			//'show_option_all'		=> ,
			//'orderby'				=> 'name',
			//'order'				=> 'ASC',
			'show_last_update'		=> 0,
			'style'					=> 'list',
			'show_count'			=> isset( $instance['see_number_products'] ) ? $instance['see_number_products'] : false,
			'hide_empty'			=> isset( $instance['hide_empty_taxonomies'] ) ? $instance['hide_empty_taxonomies'] : false,
			'use_desc_for_title'	=> isset( $instance['use_desc_for_title'] ) ? $instance['use_desc_for_title'] : false,
			'child_of'				=> 0,
			//'feed'				=> ,
			//'feed_type'			=> ,
			//'feed_image'			=> ,
			//'exclude'				=> ,
			//'exclude_tree'		=> ,
			//'include'				=> ,
			'current_category'		=> 0,
			'hierarchical'			=> true,
			'title_li'				=> '', //$options['txt_title_li'],
			'number'				=> NULL,
			'echo'					=> 0,
			'depth'					=> 0,
			'taxonomy'				=> isset( $instance['taxonomy'] ) ? $instance['taxonomy'] : TCP_PRODUCT_CATEGORY,
			'dropdown'				=> isset( $instance['dropdown'] ) ? $instance['dropdown'] : false,
		);
		$excluded_taxonomies = isset( $instance['excluded_taxonomies'] ) ? $instance['excluded_taxonomies'] : false;
		if ( is_array( $excluded_taxonomies ) ) $args['exclude'] = implode( ",", $excluded_taxonomies );
		$included_taxonomies = isset( $instance['included_taxonomies'] ) ? $instance['included_taxonomies'] : false;
		if ( is_array( $included_taxonomies ) ) $args['include'] = implode( ",", $included_taxonomies );
		$order_included = isset( $instance['order_included'] ) ? $instance['order_included'] : false;
		if ( $order_included ) {
			$this->orderIncluded = explode( '#', $order_included );
			add_filter( 'get_terms', array( $this, 'orderTaxonomies' ) );
		}
		tcp_get_taxonomy_tree( $args, true );
		if ( strlen( $order_included ) > 0 )
			remove_filter( 'get_terms', array( $this, 'orderTaxonomies' ) );
		echo $after_widget;
	}

	//for order taxonomies list
	function orderTaxonomies( $terms ) { 
		usort( $terms, array( $this, 'compare' ) );
		return $terms;
	}

	function compare( $a, $b ) {
		if ( $a == $b ) return 0;
		foreach( $this->orderIncluded as $id ) {
			if ( $id == $a->term_id )
				return -1;
			elseif ( $id == $b->term_id )
				return 1;
		}
		return 0;
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = parent::update( $new_instance, $old_instance );
		$instance['post_type']				= $new_instance['post_type'];
		$instance['taxonomy']				= $new_instance['taxonomy'];
		$instance['see_number_products']	= $new_instance['see_number_products'];
		$instance['hide_empty_taxonomies']	= isset( $new_instance['hide_empty_taxonomies'] );
		$instance['use_desc_for_title']		= isset( $new_instance['use_desc_for_title'] );
		$instance['dropdown']				= isset( $new_instance['dropdown'] );
		$instance['included_taxonomies']	= isset( $new_instance['included_taxonomies'] ) ? $new_instance['included_taxonomies'] : false;
		$instance['order_included']			= $new_instance['order_included'];
		$instance['excluded_taxonomies']	= $new_instance['excluded_taxonomies'];
		return $instance;
	}

	function form( $instance ) {
		parent::form( $instance, __( 'Navigation trees', 'tcp') );
		$defaults = array(
			'post_type'				=> TCP_PRODUCT_POST_TYPE,
			'taxonomy'				=> TCP_PRODUCT_CATEGORY,
			'see_number_products'	=> true,
			'hide_empty_taxonomies'	=> false,
			'order_included'		=> '',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		$see_number_products	= isset( $instance['see_number_products'] ) ? (bool)$instance['see_number_products'] : false;
		$hide_empty_taxonomies	= isset( $instance['hide_empty_taxonomies'] ) ? (bool)$instance['hide_empty_taxonomies'] : false;
		$use_desc_for_title		= isset( $instance['use_desc_for_title'] ) ? (bool)$instance['use_desc_for_title'] : false;
		$dropdown				= isset( $instance['dropdown'] ) ? (bool)$instance['dropdown'] : false;
		$included_taxonomies	= isset( $instance['included_taxonomies'] ) ? $instance['included_taxonomies'] : array();
		$excluded_taxonomies	= isset( $instance['excluded_taxonomies'] ) ? $instance['excluded_taxonomies'] : array(); ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( 'Post type', 'tcp' )?>:</label>
			<select name="<?php echo $this->get_field_name( 'post_type' ); ?>" id="<?php echo $this->get_field_id( 'post_type' ); ?>" class="widefat">
			<?php foreach( get_post_types( array( 'show_in_nav_menus' => true ) ) as $post_type ) : 
				$obj_type = get_post_type_object( $post_type ); ?>
				<option value="<?php echo $post_type;?>"<?php selected( $instance['post_type'], $post_type ); ?>><?php echo $obj_type->labels->singular_name; ?></option>
			<?php endforeach;?>
			</select>
			<span class="description"><?php _e( 'Press save to load the list of taxonomies.', 'tcp' );?></span>
		</p><p>
			<label for="<?php echo $this->get_field_id( 'taxonomy' ); ?>"><?php _e( 'Taxonomy', 'tcp' )?>:</label>
			<select name="<?php echo $this->get_field_name( 'taxonomy' ); ?>" id="<?php echo $this->get_field_id( 'taxonomy' ); ?>" class="widefat">
			<?php $taxonomies = get_object_taxonomies( $instance['post_type'] );
			if ( is_array( $taxonomies ) && count( $taxonomies ) > 0 ) foreach( $taxonomies as $taxonomy ) : $tax = get_taxonomy( $taxonomy ); ?>
				<option value="<?php echo esc_attr( $taxonomy );?>"<?php selected( $instance['taxonomy'], $taxonomy ); ?>><?php echo $tax->labels->name;?></option>
			<?php endforeach;?>
			</select>
			<span class="description"><?php _e( 'Press save to load the next lists.', 'tcp' );?></span>
		</p><p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_number_products' ); ?>" name="<?php echo $this->get_field_name( 'see_number_products' ); ?>" <?php checked( $see_number_products, true ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_number_products' ); ?>"><?php _e( 'See children number', 'tcp' ); ?></label>
		<br />
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'hide_empty_taxonomies' ); ?>" name="<?php echo $this->get_field_name( 'hide_empty_taxonomies' ); ?>" <?php checked( $hide_empty_taxonomies, true ); ?> />
			<label for="<?php echo $this->get_field_id( 'hide_empty_taxonomies' ); ?>"><?php _e( 'Hide empty terms', 'tcp' ); ?></label>
		<br />
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'use_desc_for_title' ); ?>" name="<?php echo $this->get_field_name( 'use_desc_for_title' ); ?>" <?php checked( $use_desc_for_title, true ); ?> />
			<label for="<?php echo $this->get_field_id( 'use_desc_for_title' ); ?>"><?php _e( 'Use description for title', 'tcp' ); ?></label>
		<br />
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'dropdown' ); ?>" name="<?php echo $this->get_field_name( 'dropdown' ); ?>" <?php checked( $dropdown, true ); ?> />
			<label for="<?php echo $this->get_field_id( 'dropdown' ); ?>"><?php _e( 'Display as dropdown', 'tcp' ); ?></label>
		</p><p>
			<label for="<?php echo $this->get_field_id( 'included_taxonomies' ); ?>"><?php _e( 'Included and sorted', 'tcp' )?>:</label>
			<select name="<?php echo $this->get_field_name( 'included_taxonomies' ); ?>[]" id="<?php echo $this->get_field_id( 'included_taxonomies' ); ?>" class="widefat" multiple size="8" style="height: auto">
				<option value="0"<?php tcp_selected_multiple( $included_taxonomies, 0 ); ?>><?php _e( 'All', 'tcp' );?></option>
			<?php $args = array (
				'taxonomy'		=> $instance['taxonomy'],
				'hide_empty'	=> false,
			);
			$categories = get_categories( $args );
			$this->orderIncluded = explode( '#', $instance['order_included'] );
			usort( $categories, array( $this, 'compare' ) );
			if ( is_array( $categories ) && count( $categories ) > 0 ) foreach( $categories as $cat ) : ?>
				<option value="<?php echo esc_attr( $cat->term_id );?>"<?php tcp_selected_multiple( $included_taxonomies, $cat->term_id ); ?>><?php echo $cat->cat_name;?></option>
			<?php endforeach;?>
			</select>
			<input type="button" onclick="tcp_select_up('<?php echo $this->get_field_id( 'included_taxonomies' ); ?>', '<?php echo $this->get_field_id( 'order_included' ); ?>');" id="tcp_up" value="<?php _e( 'up', 'tcp' );?>" class="button-secondary"/>
		    <input type="button" onclick="tcp_select_down('<?php echo $this->get_field_id( 'included_taxonomies' ); ?>', '<?php echo $this->get_field_id( 'order_included' ); ?>');" id="tcp_down" value="<?php _e( 'down', 'tcp' );?>" class="button-secondary"/>
		    <span class="description"><?php _e( 'Use those actions to sort the list.', 'tcp' );?></span>
		    <input type="hidden" id="<?php echo $this->get_field_id( 'order_included' ); ?>" name="<?php echo $this->get_field_name( 'order_included' ); ?>" value="<?php echo $instance['order_included'];?>"/>
		</p><p>
			<label for="<?php echo $this->get_field_id( 'excluded_taxonomies' ); ?>"><?php _e( 'Excluded', 'tcp' )?>:</label>
			<select name="<?php echo $this->get_field_name( 'excluded_taxonomies' ); ?>[]" id="<?php echo $this->get_field_id( 'excluded_taxonomies' ); ?>" class="widefat" multiple size="6" style="height: auto">
				<option value="0"<?php tcp_selected_multiple( $excluded_taxonomies, 0 ); ?>><?php _e( 'No one', 'tcp' );?></option>
			<?php $args = array (
				'taxonomy'		=> $instance['taxonomy'],
				'hide_empty'	=> false,
			);
			foreach( get_categories( $args ) as $cat ) : ?>
				<option value="<?php echo esc_attr( $cat->term_id);?>"<?php tcp_selected_multiple( $excluded_taxonomies, $cat->term_id );?>><?php echo $cat->cat_name;?></option>
			<?php endforeach;?>
			</select>
		</p>
		<?php
	}
}
?>
