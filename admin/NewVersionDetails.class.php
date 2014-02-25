<?php
/**
 * New version detauls page
 *
 * Outputs notice abaout TheCartPress, new features and credits
 *
 * @package TheCartPress
 * @subpackage Admin
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

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'TCPNewVersionDetails' ) ) {

class TCPNewVersionDetails {

	function __construct() {
		add_action( 'tcp_admin_menu'	, array( $this, 'tcp_admin_menu' ) );
		add_action( 'admin_head'		, array( $this, 'admin_head' ) );
		add_action( 'tcp_admin_init'	, array( $this, 'tcp_admin_init' ) );
	}

	public function tcp_admin_menu() {
		//new version page
		$title = __( 'Enjoy TheCartPress', 'tcp' );
		$new_version = add_dashboard_page( $title, $title, 'manage_options', 'tcp-new-version', array( $this, 'new_version_page' ) );
		//add_action( "admin_print_styles-$new_version", array( $this, 'new_version_css' ) );
	}

	public function admin_head() {
		remove_submenu_page( 'index.php', 'tcp-new-version' ); ?>
<style type="text/css">
.tcp-logo {
	position: absolute;
	left: 0;
	top: 0;
}

img.tcp-image {
	border: 1px solid lightgrey;
}
</style>
	<?php }

	public function new_version_page() {
		$version = $this->get_version(); ?>
<div class="wrap about-wrap">
	<div class="tcp-new-version tcpf">
		<div class="text-center">
			<div class="tcp-logo">
				<img src="<?php echo plugins_url( 'images/tcp_icon_100.png', dirname( __FILE__ ) ); ?>" />
			</div>

			<h1><?php printf( __( 'Welcome to TheCartPress %s', 'tcp' ), $version ); ?></h1>
			<p class="lead">
				<?php _e( 'Thank you for updating to the latest version!', 'tcp' ); ?>
			</p>
			<p class="lead">
				<?php printf( __( 'Read more about what\'s new or go to <a href="%s">First time setup</a>', 'tcp' ), admin_url( 'admin.php?page=first_time_setup' ) );?></a>
			</p>

			<h2 class=""><?php _e( 'What\'s new?', 'tcp' ); ?></h2>
			<p class="lead"><?php _e( 'New User interface improvements, to make your day easier.', 'tcp' ); ?></p>
			
			<hr/>

			<h2><?php _e( 'My Account', 'tcp' ); ?></h2>
			<p class=""><?php printf( __( 'New icons, to make it more usable. Use it with <a href="%s">FrontEnd Plugin</a>.', 'tcp' ), 'http://extend.thecartpress.com/products/frontend/' ); ?></p>
			<img class="img-rounded tcp-image" src="<?php echo plugins_url( 'images/new_version/my-account.jpg', dirname( __FILE__ ) ); ?>" class="center" />

			<h2><?php _e( 'Checkout improvements', 'tcp' ); ?></h2>
			<p class=""><?php _e( 'Drag & Drop fields to sorting them.', 'tcp' ); ?></p>
			<img class="img-rounded tcp-image" src="<?php echo plugins_url( 'images/new_version/checkout-billing-address.jpg', dirname( __FILE__ ) ); ?>" class="center" />

			<h2><?php _e( 'Orders list', 'tcp' ); ?></h2>
			<p class=""><?php _e( 'New orders list, to see your orders faster. Take a quick look using "view", localize addresses in map, etc.', 'tcp' ); ?></p>
			<img class="img-rounded tcp-image" src="<?php echo plugins_url( 'images/new_version/orders-list.jpg', dirname( __FILE__ ) ); ?>" class="center" />

			<h2><?php _e( 'Products list', 'tcp' ); ?></h2>
			<p class="">Now, more integrated into your page.</p>
			<img class="img-rounded tcp-image" src="<?php echo plugins_url( 'images/new_version/products-list.jpg', dirname( __FILE__ ) ); ?>" class="center" />

			<h2><?php _e( 'Product Settings', 'tcp' ); ?></h2>
			<p class="">New tab system. Find product fields easier.</p>
			<img class="img-rounded" src="<?php echo plugins_url( 'images/new_version/product-settings.jpg', dirname( __FILE__ ) ); ?>" class="center" />
		</div><!-- .text-center -->
	</div><!-- .tcp-new-version -->

	<div class="tcp-extend tcpf">

	</div><!-- .tcp-extend -->
</div><!-- .wrap about-wrap -->
	<?php }

	public function tcp_admin_init() {
		//if no redirect transient
		if ( ! get_transient( '_tcp_new_version_activated' ) ) return;

		// Delete the redirect transient
		delete_transient( '_tcp_new_version_activated' );

		wp_safe_redirect( admin_url( 'index.php?page=tcp-new-version' ) );
		exit;
	}

	private function get_version() {
		$tcp_version = get_option( 'tcp_version', 132 );
		$version = str_split( $tcp_version );
		$version = implode( '.', $version );
		return $version;
	}
}

new TCPNewVersionDetails();
} // class_exists check