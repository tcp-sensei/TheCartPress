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

class RelatedListWidget extends CustomListWidget {

	function RelatedListWidget() {
		parent::CustomListWidget( 'tcprelatedlist', __( 'Allow to create related lists', 'tcp' ), 'TCP Related List' );
	}

	function widget( $args, $instance ) {
		$loop_args = array();
		$loop_args['posts_per_page'] = $instance['limit'];
		if ( is_single() && ( $instance['rel_type'] == 'POST-POST' || $instance['rel_type'] == 'PROD-POST' || $instance['rel_type'] == 'PROD-PROD' || $instance['rel_type'] == 'POST-PROD' ) ) {
			if ( $instance['rel_type'] == 'POST-POST' || $instance['rel_type'] == 'POST-PROD' ) {
				$post_type_search = 'post';
			} else {
				$post_type_search = TCP_PRODUCT_POST_TYPE;
			}
			if ( $instance['rel_type'] == 'POST-POST' || $instance['rel_type'] == 'PROD-POST' ) {
				$post_type = 'post';
			} else {
				$post_type = TCP_PRODUCT_POST_TYPE;
			}
			global $post;
			$post_id = tcp_get_default_id( $post->ID, $post_type_search );
			if ( get_post_type( $post_id ) != $post_type_search ) return;
			require_once( TCP_DAOS_FOLDER . 'RelEntities.class.php' );
			$res = RelEntities::select( $post_id, $instance['rel_type'] );
			if ( count( $res ) == 0 ) return;
			$ids = array();
			foreach ( $res as $row )
				$ids[] = $row->id_to;
			$loop_args['post__in'] = $ids;
			$loop_args['post_type'] = $post_type;
		} elseif ( is_single() && ( $instance['rel_type'] == 'POST-CAT_PROD' || $instance['rel_type'] == 'PROD-CAT_POST' || $instance['rel_type'] == 'PROD-CAT_PROD' ) ) {
			if ( $instance['rel_type'] == 'POST-CAT_PROD' ) {
				$post_type_search = 'post';
				$post_type = TCP_PRODUCT_POST_TYPE;
			} elseif ( $instance['rel_type'] == 'PROD-CAT_PROD' ) {
				$post_type_search = TCP_PRODUCT_POST_TYPE;
				$post_type = TCP_PRODUCT_POST_TYPE;
			} else {
				$post_type_search = TCP_PRODUCT_POST_TYPE;
				$post_type = 'post';
			}
			global $post;
			$post_id = tcp_get_default_id( $post->ID, $post_type_search );
 			if ( get_post_type( $post_id ) != $post_type_search ) return;
			require_once( TCP_DAOS_FOLDER . 'RelEntities.class.php' );
			$res = RelEntities::select( $post_id, $instance['rel_type'] );
			if ( count( $res ) == 0 ) return;
			$ids = array();
			foreach( $res as $re )
				$ids[] = (int)$re->id_to;
			if ( $post_type == 'post' ) {
				$loop_args['category__in'] = $ids;
				$loop_args['post_type'] = 'post';
			} else {//TCP_PRODUCT_POST_TYPE
				$loop_args['tax_query'] = array(
					array(
						'taxonomy'	=> TCP_PRODUCT_CATEGORY,
						'terms'		=> $ids,
						'field'		=> 'id',
					),
				);
				$loop_args['post_type'] = TCP_PRODUCT_POST_TYPE;
			}
		} else {
		//TODO falta?
			if ( ! is_single() && ( $instance['rel_type'] == 'CAT_PROD-CAT_PROD' || $instance['rel_type'] == 'CAT_POST-CAT_PROD' ) ) {
				$instance['taxonomy'] = TCP_PRODUCT_CATEGORY;
			} elseif ( ! is_single() && ( $instance['rel_type'] == 'CAT_PROD-CAT_POST' || $instance['rel_type'] == 'CAT_POST-CAT_POST' ) ) {
				$instance['taxonomy'] = 'category';
			} else {
				return;
			}
			if ( $instance['rel_type'] == 'CAT_PROD-CAT_PROD' || $instance['rel_type'] == 'CAT_PROD-CAT_POST' ) {
				$taxonomy_search = TCP_PRODUCT_CATEGORY;
				$cat = get_the_terms( get_the_ID(), TCP_PRODUCT_CATEGORY );
			} else {//if ( $instance['rel_type'] == 'CAT_POST-CAT_PROD' || $instance['rel_type'] == 'CAT_POST-CAT_POST' )
				$taxonomy_search = 'category';
				$cat = get_the_category();
			}
			if ( empty( $cat ) ) return;
			$cat = array_slice( $cat, 0, 1 );
			$term_id = $cat[0]->term_id;
			if ( $term_id <= 0 ) return;
			$term_id = tcp_get_default_id( $term_id, $taxonomy_search );
			require_once( TCP_DAOS_FOLDER . 'RelEntities.class.php' );
			$res = RelEntities::select( $term_id, $instance['rel_type'] );
			if ( count( $res ) == 0 ) return;
			$ids = array();
			foreach ( $res as $row )
				$ids[] = $row->id_to;
			if ( count( $ids ) == 0) return;
			if ( $instance['taxonomy'] == 'category' ) {
				$loop_args['category__in'] = $ids;
				$loop_args['post_type'] = 'post';
			} else {
				$loop_args['tax_query'] = array(
					array(
						'taxonomy'	=> TCP_PRODUCT_CATEGORY,
						'terms'		=> $ids,
						'field'		=> 'id',
					),
				);
				$loop_args['post_type'] = TCP_PRODUCT_POST_TYPE;
			}
		}
		if ( $loop_args['post_type'] != 'post' )
			$loop_args['meta_query'] = array(
				array(
					'key'	=> 'tcp_is_visible',
					'value'	=> true,
				),
			);
		parent::widget( $args, $loop_args, $instance );
	}

	function update( $new_instance, $old_instance ) {
		$instance['rel_type'] = strip_tags( $new_instance['rel_type'] );
		return parent::update( $new_instance, $instance );
	}

	function form( $instance ) {
		parent::form( $instance, __( 'Related List', 'tcp' ) );
		$defaults = array(
			'rel_type'		=> 'CAT_PROD-CAT_PROD',
		);
		$instance = wp_parse_args( ( array ) $instance, $defaults ); ?>
		<div id="particular">
		<p>
			<label for="<?php echo $this->get_field_id( 'rel_type' ); ?>"><?php _e( 'Relation type', 'tcp' )?>:</label>
			<select name="<?php echo $this->get_field_name( 'rel_type' ); ?>" id="<?php echo $this->get_field_id( 'rel_type' ); ?>" class="widefat">
				<option value="CAT_POST-CAT_POST" <?php selected( $instance['rel_type'], 'CAT_POST-CAT_POST' ); ?>><?php _e( 'Cat. Posts &raquo; Cat. Posts', 'tcp' );?></option>
				<option value="CAT_PROD-CAT_POST" <?php selected( $instance['rel_type'], 'CAT_PROD-CAT_POST' ); ?>><?php _e( 'Cat. Products &raquo; Cat. Posts', 'tcp' );?></option>
				<option value="CAT_POST-CAT_PROD" <?php selected( $instance['rel_type'], 'CAT_POST-CAT_PROD' ); ?>><?php _e( 'Cat. Posts &raquo; Cat. Products', 'tcp' );?></option>
				<option value="CAT_PROD-CAT_PROD" <?php selected( $instance['rel_type'], 'CAT_PROD-CAT_PROD' ); ?>><?php _e( 'Cat. Products &raquo; Cat. Products', 'tcp' );?></option>
				<option value="POST-POST" <?php selected( $instance['rel_type'], 'POST-POST' ); ?>><?php _e( 'Post &raquo; Posts', 'tcp' );?></option>
				<option value="PROD-POST" <?php selected( $instance['rel_type'], 'PROD-POST' ); ?>><?php _e( 'Product &raquo; Posts', 'tcp' );?></option>
				<option value="PROD-PROD" <?php selected( $instance['rel_type'], 'PROD-PROD' ); ?>><?php _e( 'Product &raquo; Products', 'tcp' );?></option>
				<option value="POST-PROD" <?php selected( $instance['rel_type'], 'POST-PROD' ); ?>><?php _e( 'Post &raquo; Products', 'tcp' );?></option>
				<option value="POST-CAT_PROD" <?php selected( $instance['rel_type'], 'POST-CAT_PROD' ); ?>><?php _e( 'Post &raquo; Cat. Products', 'tcp' );?></option>
				<option value="PROD-CAT_POST" <?php selected( $instance['rel_type'], 'PROD-CAT_POST' ); ?>><?php _e( 'Product &raquo; Cat. Posts', 'tcp' );?></option>
				<option value="PROD-CAT_PROD" <?php selected( $instance['rel_type'], 'PROD-CAT_PROD' ); ?>><?php _e( 'Product &raquo; Cat. Products', 'tcp' );?></option>
			</select>
		</p>
		<?php parent::show_post_type_form( $instance ); ?>
		</div>
	<?php
	}
}
?>
