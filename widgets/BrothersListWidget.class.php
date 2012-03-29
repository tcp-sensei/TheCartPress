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
		parent::CustomListWidget( 'tcpbrotherslist', __( 'Allow to create brothers lists', 'tcp' ), 'TCP Brothers List' );
	}

	function widget( $args, $instance ) {
		if ( ! is_single() ) return;
		global $post;
		if ( $post ) {
			$post_type = get_post_type_object( $post->post_type );
			$taxonomies = get_object_taxonomies( $post->post_type );
			if ( count( $taxonomies ) == 0 ) return;
			$terms = get_the_terms( $post->ID, $taxonomies[0] );
			$title = '';
			$ids = array();
			if ( is_array( $terms ) && count( $terms ) ) {
				foreach( $terms as $term ) {
					$ids[] = tcp_get_default_id( $term->term_id, $term->taxonomy );
					if ( $title == '' ) $title = $term->name;
					else $title .= ' - ' . $term->name;
				}
			}
			$instance['title'] .= ': ' . $title;
			$loop_args = array(
				'post_type'			=> $post->post_type,
				'posts_per_page'	=> $instance['limit'],
				'post__not_in'			=> array( $post->ID, ),
				'tax_query'			=> array(
					array(
						'taxonomy'	=> $taxonomies[0],
						'terms'		=> $ids,
						'field'		=> 'id',
					),
				),
			);
			parent::widget( $args, $loop_args, $instance );
		}
	}

	function form( $instance ) {
		parent::form( $instance, __( 'Brothers list', 'tcp' ) );
		parent::show_post_type_form( $instance );
	}
}
?>
