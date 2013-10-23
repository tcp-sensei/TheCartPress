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
 * along with This program.  If not, see <http://www.gnu.org/licenses/>.
 */

class TCPArchivesWidget extends WP_Widget {

	private $post_type	= 'posts';
	private $type		= 'monthly';

	function TCPArchivesWidget() {
		$widget = array(
			'classname'		=> 'tcparchives',
			'description'	=> __( 'Use this widget to add a monthly/year/daily/... lists of different post types', 'tcp' ),
		);
		$control = array(
			'width'		=> 400,
			'id_base'	=> 'tcparchives-widget',
		);
		$this->WP_Widget( 'tcparchives-widget', 'TCP Archives', $widget, $control );
	}

	function widget( $args, $instance ) {
		extract($args);
		$type		= isset( $instance['type'] ) ? $instance['type'] : 'monthly';
		$post_type	= isset( $instance['post_type'] ) ? $instance['post_type'] : TCP_PRODUCT_POST_TYPE;
		$count		= $instance['count'] ? '1' : '0';
		$dropdown	= $instance['dropdown'] ? '1' : '0';
		$title		= apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Archives' ) : $instance['title'], $instance, $this->id_base );

		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;
		add_filter( 'getarchives_join', array( &$this, 'getarchives_join' ), 99, 2 );
		add_filter( 'getarchives_where', array( &$this, 'getarchives_where' ), 99, 2 );
		if ( $post_type != 'post' ) {
			$this->post_type = $post_type;
			$this->type = $type;
			add_filter( 'home_url', array( &$this, 'home_url' ), 99, 4 );
		}
		if ( $dropdown ) : ?>

		<select name="archive-dropdown" onchange='document.location.href=this.options[this.selectedIndex].value;'>
			<option value="">
			<?php if ( $type == 'monthly' ) {
				echo esc_attr(__('Select Month' ) );
			} elseif ( $type == 'yearly' ) {
				echo esc_attr(__('Select Year' ) );
			} elseif ( $type == 'weekly' ) {
				echo esc_attr(__('Select Week' ) );
			} elseif ( $type == 'daily' ) {
				echo esc_attr(__('Select Day' ) );
			} else {
				echo esc_attr(__('Select One', 'tcp' ) );
			} ?>
			</option>
			<?php
			$args = array(
				'type'				=> $type,
				'post_type'			=> $post_type,
				'format'			=> 'option',
				'show_post_count'	=> $count
			);
			wp_get_archives( apply_filters( 'widget_archives_dropdown_args', $args ) );?>
		</select>

		<?php else : ?>

		<ul>
		<?php
		$args = array(
			'type'				=> $type,
			'post_type'			=> $post_type,
			'show_post_count'	=> $count,
		);
		wp_get_archives( apply_filters( 'widget_archives_args', $args ) );?>
		</ul>

		<?php endif;
		echo $after_widget;
		if ( $post_type != 'post' ) {
			remove_filter( 'home_url', array( $this, 'home_url' ), 99 );
		}
		remove_filter( 'getarchives_where', array( $this, 'getarchives_where' ), 99 );
		remove_filter( 'getarchives_join', array( $this, 'getarchives_join' ), 99 );
	}

	function home_url( $url, $path, $orig_scheme, $blog_id ) {
		if ( $this->type == 'yearly' || $this->type == 'monthly' || $this->type == 'daily' ) {
			$post_type_object = get_post_type_object( $this->post_type );
			$new_url = substr( $url, 0, -strlen( $path ) ) . '/' . $post_type_object->rewrite['slug'] . $path;
		} elseif ( $this->type == 'weekly' ) {
			$post_type_object = get_post_type_object( $this->post_type );
			$new_url = $url . '/' . $post_type_object->rewrite['slug'];
		} else {
			$new_url = $url;
		}
		//$new_url = str_replace( 'archives/date/', '', $new_url );
		return $new_url;
	}

	function getarchives_where( $where , $r ) {
		if ( isset( $r['post_type'] ) ) {
			global $wpdb;
			$post_type = $wpdb->escape( $r['post_type'] );
			$where = str_replace( "post_type = 'post'" , "post_type = '$post_type'" , $where );
		}
		return $where;
	}

	function getarchives_join( $join , $r ) {
		if ( isset( $r['post_type'] ) ) {
			global $wpdb;
			$post_type = $wpdb->escape( $r['post_type'] );
			$join = str_replace( "t.element_type='post_post'" , "t.element_type='post_$post_type' " , $join );//WPML Support
		}
		return $join;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, array( 'title' => '', 'count' => 0, 'dropdown' => '') );
		$instance['title']		= strip_tags( $new_instance['title'] );
		$instance['type']		= $new_instance['type'];
		$instance['post_type']	= $new_instance['post_type'];
		$instance['count']		= $new_instance['count'] ? 1 : 0;
		$instance['dropdown']	= $new_instance['dropdown'] ? 1 : 0;
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => 'TCP Archives', 'count' => 0, 'dropdown' => '', 'type' => 'monthly' ) );
		$title		= strip_tags($instance['title']);
		$type		= isset( $instance['type'] ) ? $instance['type'] : 'monthly';
		$post_type	= isset( $instance['post_type'] ) ? $instance['post_type'] : TCP_PRODUCT_POST_TYPE;
		$count		= $instance['count'] ? 'checked="checked"' : '';
		$dropdown	= $instance['dropdown'] ? 'checked="checked"' : '';?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?></label>:<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		<p>
			<label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( 'Post type', 'tcp' )?>:</label>
			<select name="<?php echo $this->get_field_name( 'post_type' ); ?>" id="<?php echo $this->get_field_id( 'post_type' ); ?>" class="widefat">
			<?php foreach( get_post_types( array( 'show_in_nav_menus' => true ), object ) as $post_type_def ) : ?>
				<option value="<?php echo $post_type_def->name;?>" <?php selected( $post_type, $post_type_def->name ); ?>><?php echo $post_type_def->labels->name;?></option>
			<?php endforeach;?>
			</select>
		</p><p>		
		<label for="<?php echo $this->get_field_id('type'); ?>"><?php _e( 'Type', 'tcp' ); ?></label>:
			<select id="<?php echo $this->get_field_id('type'); ?>" name="<?php echo $this->get_field_name('type'); ?>">
				<option value="yearly" <?php selected( $type, 'yearly' );?>><?php _e( 'yearly', 'tcp' );?></option>
				<option value="monthly" <?php selected( $type, 'monthly' );?>><?php _e( 'monthly', 'tcp' );?></option>
				<option value="daily" <?php selected( $type, 'daily' );?>><?php _e( 'daily', 'tcp' );?></option>
				<option value="weekly" <?php selected( $type, 'weekly' );?>><?php _e( 'weekly', 'tcp' );?></option>
				<option value="postbypost" <?php selected( $type, 'postbypost' );?>><?php _e( 'postbypost', 'tcp' );?></option>
				<option value="alpha" <?php selected( $type, 'alpha' );?>><?php _e( 'alpha', 'tcp' );?></option>
			</select>
		</p>
			<input class="checkbox" type="checkbox" <?php echo $dropdown; ?> id="<?php echo $this->get_field_id('dropdown'); ?>" name="<?php echo $this->get_field_name('dropdown'); ?>" /> <label for="<?php echo $this->get_field_id('dropdown'); ?>"><?php _e('Display as dropdown'); ?></label>
		<br/>
			<input class="checkbox" type="checkbox" <?php echo $count; ?> id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" /> <label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Show post counts'); ?></label>
		<?php
	}
}
?>