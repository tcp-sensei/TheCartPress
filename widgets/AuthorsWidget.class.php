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

class TCPAuthorsWidget extends TCPParentWidget {

	function TCPAuthorsWidget() {
		parent::__construct( 'tcpauthorslist', __( 'Allow to create Authors Lists', 'tcp' ), 'TCP Authors List' );
	}

	function widget( $args, $instance ) {
		if ( ! parent::widget( $args, $instance ) ) return;
		extract( $args );
		echo $before_widget;
		$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : false );
		if ( $title ) echo $before_title, $title, $after_title; ?>
		<ul>
		<?php tcp_list_authors( $instance ); ?>
		</ul>
		<?php echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = parent::update( $new_instance, $old_instance );
		$instance['number']			= (int)$new_instance['number'];
		$instance['see_avatar']		= $new_instance['see_avatar'] == 'yes';
		//$instance['avatar_size']	= $new_instance['avatar_size'];
		$instance['optioncount']	= $new_instance['optioncount'] == 'yes';
		$instance['exclude_admin']	= $new_instance['exclude_admin'] == 'yes';
		$instance['show_fullname']	= $new_instance['show_fullname'] == 'yes';
		$instance['see_bio']		= $new_instance['see_bio'] == 'yes';
		$instance['hide_empty']		= $new_instance['hide_empty'] == 'yes';
		$instance['order_by']		= $new_instance['order_by'];
		$instance['order']			= $new_instance['order'];
		return $instance;
	}

	function form( $instance, $title = '' ) {
		$instance = wp_parse_args( $instance, array(
			'number'		=> null,
			'see_avatar'	=> true,
			'avatar_size'	=> '32x32',
			'optioncount'	=> true,
			'exclude_admin'	=> true,
			'show_fullname'	=> true,
			'see_bio'		=> true,
			'hide_empty'	=> false,
			'order_by'		=> 'name',
			'order'			=> 'ASC',
		) );
		parent::form( $instance, $title ); ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Limit', 'tcp' ); ?>:</label>
			<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $instance['number']; ?>" size="3" />
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_avatar' ); ?>" name="<?php echo $this->get_field_name( 'see_avatar' ); ?>" value="yes" <?php checked( $instance['see_avatar'] ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_avatar' ); ?>"><?php _e( 'See avatar', 'tcp' ); ?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'optioncount' ); ?>" name="<?php echo $this->get_field_name( 'optioncount' ); ?>" value="yes" <?php checked( $instance['optioncount'] ); ?> />
			<label for="<?php echo $this->get_field_id( 'optioncount' ); ?>"><?php _e( 'See count', 'tcp' ); ?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'exclude_admin' ); ?>" name="<?php echo $this->get_field_name( 'exclude_admin' ); ?>" value="yes" <?php checked( $instance['exclude_admin'] ); ?> />
			<label for="<?php echo $this->get_field_id( 'exclude_admin' ); ?>"><?php _e( 'Exclude admin', 'tcp' ); ?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'show_fullname' ); ?>" name="<?php echo $this->get_field_name( 'show_fullname' ); ?>" value="yes" <?php checked( $instance['show_fullname'] ); ?> />
			<label for="<?php echo $this->get_field_id( 'show_fullname' ); ?>"><?php _e( 'Show fullname', 'tcp' ); ?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'see_bio' ); ?>" name="<?php echo $this->get_field_name( 'see_bio' ); ?>" value="yes" <?php checked( $instance['see_bio'] ); ?> />
			<label for="<?php echo $this->get_field_id( 'see_bio' ); ?>"><?php _e( 'See bio', 'tcp' ); ?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'hide_empty' ); ?>" name="<?php echo $this->get_field_name( 'hide_empty' ); ?>" value="yes" <?php checked( $instance['hide_empty'] ); ?> />
			<label for="<?php echo $this->get_field_id( 'hide_empty' ); ?>"><?php _e( 'Hide empty', 'tcp' ); ?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'order_by' ); ?>"><?php _e( 'Order by', 'tcp' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'order_by' ); ?>" name="<?php echo $this->get_field_name( 'order_by' ); ?>">
				<option value="name" <?php selected( 'name', $instance['order_by'] );?>><?php _e( 'By name', 'tcp' ); ?></option>
				<option value="email" <?php selected( 'email', $instance['order_by'] );?>><?php _e( 'By email', 'tcp' ); ?></option>
				<option value="url" <?php selected( 'url', $instance['order_by'] );?>><?php _e( 'By url', 'tcp' ); ?></option>
				<option value="registered" <?php selected( 'registered', $instance['order_by'] );?>><?php _e( 'By registered date', 'tcp' ); ?></option>
				<option value="id" <?php selected( 'id', $instance['order_by'] );?>><?php _e( 'By author\'s Id', 'tcp' ); ?></option>
				<option value="user_login" <?php selected( 'user_login', $instance['order_by'] );?>><?php _e( 'By user login', 'tcp' ); ?></option>
				<option value="post_count" <?php selected( 'post_count', $instance['order_by'] );?>><?php _e( 'By post count', 'tcp' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php _e( 'order', 'tcp' ); ?></label>
			<input type="radio" class="radio" id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>" value="ASC" <?php checked( 'ASC', $instance['order'] ); ?> /> <?php _e( 'Asc.', 'tcp' ); ?>
			<input type="radio" class="radio" id="<?php echo $this->get_field_id( 'order' ); ?>_desc" name="<?php echo $this->get_field_name( 'order' ); ?>" value="DESC" <?php checked( 'DESC', $instance['order'] ); ?> /> <?php _e( 'Desc.', 'tcp' ); ?>
		</p><?php
	}
}
?>