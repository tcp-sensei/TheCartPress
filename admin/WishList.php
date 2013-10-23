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

class TCPWishListTable extends WP_List_Table {

	function __construct() {
		parent::__construct( array(
			'plural' => 'Wish List',
		) );
	}

	function ajax_user_can() {
		return false;
	}

	function prepare_items() {
		if ( ! is_user_logged_in() ) return;
		$items = array();
		if ( current_user_can( 'tcp_edit_orders' ) ) {
			global $wpdb;
			$per_page = apply_filters( 'tcp_orders_per_page', 15 );
			$paged = $this->get_pagenum();
			$sql = 'SELECT ID, user_nicename from ' . $wpdb->users . ' ORDER BY user_nicename';
			$sql .= $wpdb->prepare( ' limit %d, %d', ($paged-1) * $per_page, $per_page );
			$user_ids = $wpdb->get_results( $sql );
			foreach( $user_ids as $user) {
				$user_data = get_userdata( $user->ID );
				$wish_list = (array)get_user_meta( $user->ID, 'tcp_wish_list', true );
				if ( count( $wish_list ) > 0 )
					$items[$user->ID] = array(
						'user_id'	=> $user->ID,
						'name'		=> $user_data->display_name,
						'email'		=> $user_data->user_email,
						'wish'		=> $wish_list
					);
			}
			$total_items = count( $items);
			$total_pages = $total_items / $per_page;
			if ( $total_pages > (int)$total_pages ) {
				$total_pages = (int)$total_pages;
				$total_pages++;
			}
			$this->set_pagination_args( array(
				'total_items'	=> $total_items,
				'per_page'		=> $per_page,
				'total_pages'	=> $total_pages,
			) );
		} else {
			$current_user = wp_get_current_user();
			$current_user_data = get_userdata( $current_user->ID );
			$wish_list = (array)get_user_meta( $current_user->ID, 'tcp_wish_list', true );
			$items[$current_user->ID] = array(
				'user_id'	=> $current_user->ID,
				'name'		=> $current_user_data->display_name,
				'email'		=> $user_data->user_email,
				'wish'		=> $wish_list
			);
			$this->set_pagination_args( array(
				'total_items'	=> 1,
				'per_page'		=> 1,
				'total_pages'	=> 1,
			) );
		}
		$this->items = $items;
	}

	function get_table_classes() {
		return array( 'widefat', 'fixed', 'pages', 'tcp_wish_list'  );
	}

	function get_column_info() {
		$columns = array();
		//$orders_columns['cb'] = '<input type="checkbox" />';
		//$columns['user_id'] = _x( 'User ID', 'column name', 'tcp' );
		$columns['name'] = _x( 'Name', 'users\'s column name', 'tcp' );
		$columns['wish'] = _x( 'Wish List', 'column name', 'tcp' );
		$columns = apply_filters( 'tcp_manage_wish_list_columns', $columns );
		return array( $columns, array(), array() );
	}

	function column_cb( $item ) {
		?><input type="checkbox" name="user[]" value="<?php echo $item->user_id; ?>" /><?php
	}
	
	function column_user_id( $item ) {
		echo $item['user_id'];
	}

	function column_name( $item ) {
		?><img src="http://www.gravatar.com/avatar/<?php echo $item['email'];?>?d=identicon" width="50px" height="50px" /><?php
		echo $item['name'], ' [', $item['email'], ']';
	}

	function column_wish( $item ) {
		$wish = $item['wish'];
		$ids = array_keys( $wish );
		if ( count( $ids ) == 0 ) return;
		foreach( $ids as $id ) if ( $id > 0 ) {
			$post = get_post( $id );
			if ( $post ) echo $post->post_title, '<br>';
		}
	}
}
$wishListTable = new TCPWishListTable();
$wishListTable->prepare_items();?>
<form id="posts-filter" method="get" action="">
<input type="hidden" name="page" value="<?php echo isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : 0; ?>" />
<div class="wrap">
<?php screen_icon( 'tcp-wish-list' ); ?><h2><?php _e( 'Wish List', 'tcp' );?></h2>
<div class="clear"></div>
<?php $wishListTable->display(); ?>
</div><!-- .wrap -->
</form>