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

class CommentsCustomPostTypeWidget extends WP_Widget {

	function CommentsCustomPostTypeWidget( ) {
		$widget_settings = array(
			'classname'		=> 'commentscustomposttype',
			'description'	=> __( 'Allow to create most recent lists of comments for custom post types', 'tcp' ),
		);
		$control_settings = array(
			'width'		=> 300,
			'id_base'	=> 'commentscustomposttype-widget'
		);
		$this->WP_Widget( 'commentscustomposttype-widget', 'TCP Comments for Custom Post Type', $widget_settings, $control_settings );
		//add_action( 'wp_unregister_sidebar_widget', array( $this, 'wpUnregisterSidebarWidget' ) );
	}

//	function wpUnregisterSidebarWidget( $id ) {
//	}

	function widget( $args, $instance ) {
		global $comments, $comment;

 		extract( $args );
 		//$title = apply_filters('widget_title', empty($instance['title']) ? __('Recent Comments') : $instance['title']);
		$title = apply_filters( 'widget_title', $instance['title'] );
		if ( ! $number = absint( $instance['number'] ) )
 			$number = 5;

		$post_type = isset( $instance['post_type'] ) ? $instance['post_type'] : TCP_PRODUCT_POST_TYPE;
		$comments = get_comments( array( 'number' => $number, 'status' => 'approve' ) );
		echo $before_widget;
		if ( $title ) echo $before_title, $title, $after_title;

		echo '<ul id="tcp_recent_comments">';
		if ( $comments )
			foreach ( (array) $comments as $comment)
				// translators: comments widget: 1: comment author, 2: post link
				if ( strlen( $post_type ) == 0 || get_post_type( $comment->comment_post_ID ) == $post_type )
					echo '<li class="tcp_recent_comments">',  sprintf( __( '%1$s on %2$s', 'tcp' ), get_comment_author_link(), '<a href="' . esc_url( get_comment_link($comment->comment_ID) ) . '">' . get_the_title($comment->comment_post_ID) . '</a>'), '</li>';
		echo '</ul>';
		echo $after_widget;
	}

	function theComments( $comments ) {
		if ( strlen( $this->post_type ) == 0 ) {
			return $comments;
		} else {
			$new_comments = array();
			foreach( $comments as $comment )
				if ( get_post_type( $comment->comment_post_ID ) == $this->post_type )
					$new_comments[] = $comment;
			return $new_comments;
		}
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title']		= strip_tags($new_instance['title']);
		$instance['number']		= (int)$new_instance['number'];
		$instance['post_type']	= $new_instance['post_type'];
		return $instance;
	}

	function form( $instance ) {
		$title		= isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : __( 'Comments for Custom Post Type', 'tcp' );
		$number		= isset( $instance['number'] ) ? (int)$instance['number'] : 5;
		$type		= isset( $instance['post_type'] ) ? $instance['post_type'] : TCP_PRODUCT_POST_TYPE;?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'tcp' ); ?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p><p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e('Number of comments to show:', 'tcp' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" maxlength="4" />
		</p><p>
			<label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( 'Post type', 'tcp' )?>:</label>
			<select name="<?php echo $this->get_field_name( 'post_type' ); ?>" id="<?php echo $this->get_field_id( 'post_type' ); ?>" class="widefat">
				<option value=""<?php selected( '', $type ); ?>><?php _e( 'All', 'tcp' );?></option>
			<?php foreach( get_post_types( array( 'show_in_nav_menus' => true ) ) as $post_type ) : 
				$obj_type = get_post_type_object( $post_type ); ?>
				<option value="<?php echo $post_type;?>"<?php selected( $type, $post_type ); ?>><?php echo $obj_type->labels->singular_name; ?></option>
			<?php endforeach; ?>
			</select>
		</p><?php
	}
}