<?php
/**
 * Featured Video
 *
 * Allows to add a custom Javascript editor to add javascript code to the header of the site
 *
 * @package TheCartPress
 * @subpackage Modules
 */

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

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'TCPFeaturedVideo' ) ) {

class TCPFeaturedVideo {

	static function wp() {
		if ( ! is_admin() && ! tcp_is_the_shopping_cart_page() && ! tcp_is_the_checkout_page() ) {
			add_filter( 'post_thumbnail_html'	, array( __CLASS__, 'post_thumbnail_html' ), 10, 5 );
			add_filter( 'get_post_metadata'		, array( __CLASS__, 'get_post_metadata' ), 10, 4 );
		}
	}

	static function admin_init() {
		TCPFeaturedVideo::register_metabox();
		add_action( 'save_post'		, array( __CLASS__, 'save' ), 10, 2 );
		add_action( 'delete_post'	, array( __CLASS__, 'delete' ) );
	}

	static function post_thumbnail_html( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
		if ( tcp_has_featured_video( $post_id ) ) return apply_filters( 'the_content', tcp_get_the_featured_video( $post_id ) );
		return $html;
	}

	static function get_post_metadata( $check, $object_id, $meta_key, $single ) {
		if ( '_thumbnail_id' == $meta_key ) {
			if ( tcp_has_featured_video( $object_id ) ) return true;
		}
		return $check;
	}

	static function register_metabox() {
		foreach( get_post_types() as $post_type )
			add_meta_box( 'tcp-featured-video', __( 'Featured Video', 'tcp' ), array( __CLASS__, 'show_featured_metabox' ), $post_type, 'side', 'default' );
	}

	static function show_featured_metabox() {
		global $post;
		if ( ! current_user_can( 'edit_post', $post->ID ) ) return;
		if ( ! isset( $post->featured_video ) ) {
			$featured_video = '';
		} else {
			$featured_video = $post->featured_video;
		} ?>
<p class="hide-if-no-js">
	<label><?php _e( 'URL to video','tcp' ); ?>:
	<input type="text" name="tcp_featured_video" id="tcp_featured_video" value="<?php echo tcp_get_the_featured_video( $post->ID ); ?>" class=""/>
</p>
<?php
	}

	function save( $post_id, $post ) {
		if ( isset( $_POST['tcp_featured_video'] ) ) {
			if ( ! current_user_can( 'edit_post', $post_id ) ) return array( $post_id, $post );
			tcp_save_featured_video( $post_id, $_POST['tcp_featured_video'] );
		}
		return array( $post_id, $post );
	}

	function delete( $post_id ) {
		$post = get_post( $post_id );
		tcp_delete_featured_video( $post_id );
		return $post_id;
	}
}

add_action( 'wp', 'TCPFeaturedVideo::wp' );
add_action( 'admin_init', 'TCPFeaturedVideo::admin_init' );

/**
 * Outputs the featured video URL
 *
 * @since 1.3.2
 * @uses tcp_get_the_featured_video
 */
function tcp_the_featured_video( $post_id = false ) {
	echo tcp_get_the_featured_video( $post_id );
}

/**
 * Returns the featured video URL
 *
 * @since 1.3.2
 * @uses get_post_meta, get_the_ID, tcp_get_default_id
 */
function tcp_get_the_featured_video( $post_id = false ) {
	if ( $post_id == false ) $post_id = get_the_ID();
	$featured_video = get_post_meta( $post_id, '_tcp_featured_video', true );
	if ( $featured_video == '' ) {
		$post_id = $post_id = tcp_get_default_id( $post_id );
		$featured_video = get_post_meta( $post_id, '_tcp_featured_video', true );
	}
	return $featured_video;
}

/**
 * Returns true if the post has a featured video
 *
 * @since 1.3.2
 * @uses tcp_get_the_featured_video
 */
function tcp_has_featured_video( $post_id = false ) {
	$featured_video = tcp_get_the_featured_video( $post_id );
	return $featured_video != '';
}

/**
 * Saves the featured video URL to a post
 *
 * @since 1.3.2
 * @uses update_post_meta
 */
function tcp_save_featured_video( $post_id, $featured_video ) {
	update_post_meta( $post_id, '_tcp_featured_video', $featured_video );
}

/**
 * Deletes the featured video URL to a post
 *
 * @since 1.3.2
 * @uses delete_post_meta
 */
function tcp_delete_featured_video( $post_id ) {
	delete_post_meta( $post_id, '_tcp_featured_video' );
}
} // class_exists check