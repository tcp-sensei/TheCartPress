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

/**
 * Shows products of the same category of the displayed current product
 */
class BrothersListWidget extends CustomListWidget {

	function BrothersListWidget() {
		parent::CustomListWidget( 'tcpbrotherslist', __( 'Allow to create brother product lists', 'tcp' ), 'TCP Brothers List' );
	}

	function widget( $args, $instance ) {
		if ( ! is_single() ) return;
		global $post;
		if ( $post ) {
			$taxonomies = get_object_taxonomies( $post->post_type );
			if ( count( $taxonomies ) == 0 ) return;
			$brother_taxonomies = isset( $instance['brother_taxonomies'] ) ? $instance['brother_taxonomies'] : array();
			$tcp_brother_multiple = isset( $instance['brother_multiple'] ) ? $instance['brother_multiple'] : 'OR';
			$loop_args = array(
				'post_type'		=> $post->post_type,
				'posts_per_page'=> $instance['limit'],
				'post__not_in'	=> array( $post->ID, ),
				'tax_query'		=> array(
					'relation'	=> $tcp_brother_multiple,
				),
			);
			$titles = array();
			foreach( $taxonomies as $taxonomy ) {
				if ( in_array( $taxonomy, $brother_taxonomies ) ) {
					$terms = get_the_terms( $post->ID, $taxonomy );
					$title = '';
					$ids = array();
					if ( is_array( $terms ) && count( $terms ) ) {
						foreach( $terms as $term ) {
							$ids[] = $term->term_id; //tcp_get_default_id( $term->term_id, $term->taxonomy );
							if ( $title == '' ) {
								$title = $term->name;
							} else {
								$title .= ' - ' . $term->name;
							}
						}
					}
					$titles[] = $title;
					$loop_args['tax_query'][] = array(
						'taxonomy'	=> $taxonomy,
						'terms'		=> $ids,
						'field'		=> 'id',
					);
				}
			}
			$instance['title'] .= ': ' . implode( ', ', $titles );
			$instance['loop_args'] = $loop_args;
			$instance['post_type'] = tcp_get_saleable_post_types();
			parent::widget( $args, $instance );
		}
	}

	function update( $new_instance, $old_instance ) {
		$instance['brother_taxonomies'] = $_REQUEST['tcp_brother_taxonomies'];
		$instance['brother_multiple'] = isset( $_REQUEST['tcp_brother_multiple'] ) ? $_REQUEST['tcp_brother_multiple'] : 'OR';
		$instance['order_type'] = $new_instance['order_type'];
		$instance['order_desc'] = $new_instance['order_desc'];
		return parent::update( $new_instance, $instance );
	}

	function form( $instance ) {
		if ( ! isset( $instance['title'] ) ) $instance['title'] = __( 'Brothers list', 'tcp');
		parent::form( $instance );
		$post_types = get_post_types( array(), 'objects');
		$tcp_brother_taxonomies = isset( $instance['brother_taxonomies'] ) ? $instance['brother_taxonomies'] : array();
		$tcp_brother_multiple = isset( $instance['brother_multiple'] ) ? $instance['brother_multiple'] : 'OR';
		$order_type = isset( $instance['order_type'] ) ? $instance['order_type'] : 'date';
		$order_desc = isset( $instance['order_desc'] ) ? $instance['order_desc'] : 'asc'; ?>
		<p>
			<label for="tcp_brother_multiple_or"><?php _e( 'Multiple Taxonomy Handling', 'tcp' ); ?></label>
			<ul>
				<li><label><input type="radio" id="tcp_brother_multiple_or" name="tcp_brother_multiple" value="OR" <?php checked( 'OR', $tcp_brother_multiple ); ?>/> <?php _e( 'OR', 'tcp' ); ?></label></li>
				<li><label><input type="radio" id="tcp_brother_multiple_and" name="tcp_brother_multiple" value="AND" <?php checked( 'AND', $tcp_brother_multiple ); ?>/> <?php _e( 'AND', 'tcp' ); ?></label></li>
			</ul>
		</p>
		<p>
			<ul>
		<?php foreach( $post_types as $post_type ) :
			$taxonomies = get_object_taxonomies( $post_type->name );
			if ( is_array( $taxonomies ) && count( $taxonomies ) > 0 ) : ?>
				<li><?php echo $post_type->labels->name; ?>:<ul>
				<?php foreach( $taxonomies as $taxonomy ) : ?>
					<li><label><input type="checkbox" name="tcp_brother_taxonomies[]" value="<?php echo $taxonomy; ?>" <?php checked( in_array( $taxonomy, $tcp_brother_taxonomies ) ); ?>> <?php echo $taxonomy; ?></label></li>
				<?php endforeach; ?>
				</ul>
				</li>
			<?php endif;
		endforeach; ?>
			</ul>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'order_type' ); ?>"><?php _e( 'Order by', 'tcp' ); ?></label>:
			<?php $sorting_fields = tcp_get_sorting_fields();
			//$sorting_fields[] = array( 'value' => 'rand', 'title' => __( 'Random', 'tcp' ) ); ?>
			<select id="<?php echo $this->get_field_id( 'order_type' ); ?>" name="<?php echo $this->get_field_name( 'order_type' ); ?>">
				<?php foreach( $sorting_fields as $sorting_field ) : ?>
				<option value="<?php echo $sorting_field['value']; ?>" <?php selected( $order_type, $sorting_field['value'] ); ?>><?php echo $sorting_field['title']; ?></option>
				<?php endforeach; ?>
			</select>

			<input type="radio" name="<?php echo $this->get_field_name( 'order_desc' ); ?>" id="<?php echo $this->get_field_id( 'order_desc' ); ?>" value="asc" <?php checked( $order_desc, 'asc' ); ?>/>
			<label for="<?php echo $this->get_field_id( 'order_desc' ); ?>"><?php _e( 'Asc.', 'tcp' ); ?></label>
			<input type="radio" name="<?php echo $this->get_field_name( 'order_desc' ); ?>" id="<?php echo $this->get_field_id( 'order_desc' ); ?>" value="desc" <?php checked( $order_desc, 'desc' ); ?>/>
			<label for="<?php echo $this->get_field_id( 'order_desc' ); ?>"><?php _e( 'Desc.', 'tcp' ); ?></label>
		</p>
	<?php parent::show_post_type_form( $instance );
	}
}
?>