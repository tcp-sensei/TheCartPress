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
 * along with This program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Load and register TCP widgets
 */

add_action( 'widgets_init', 'tcp_widgets_init' );

function tcp_widgets_init() {
	global $thecartpress;
	$disable_ecommerce = $thecartpress->get_setting( 'disable_ecommerce', false );
	if ( ! $disable_ecommerce ) {
		require_once( 'ShoppingCartSummaryWidget.class.php' );
		require_once( 'ShoppingCartWidget.class.php' );
		require_once( 'RelatedListWidget.class.php' );
		require_once( 'CheckoutWidget.class.php' );
		require_once( 'SelectCountryWidget.class.php' );
		require_once( 'BuyButtonWidget.class.php' );
		require_once( 'CrossSellingWidget.class.php' );
		register_widget( 'ShoppingCartSummaryWidget' );
		register_widget( 'ShoppingCartWidget' );
		register_widget( 'RelatedListWidget' );
		register_widget( 'CheckoutWidget' );//TODO At this moment, only for testing purpouse
		register_widget( 'TCPSelectCountryWidget' );
		register_widget( 'BuyButtonWidget' );
		register_widget( 'CrossSellingWidget' );
	}
	require_once( 'LastVisitedWidget.class.php' );
	require_once( 'LoginWidget.class.php' );
	require_once( 'CustomPostTypeListWidget.class.php' );
	require_once( 'CustomValuesWidget.class.php' );
	require_once( 'TaxonomyCloudsPostTypeWidget.class.php' );
	require_once( 'TaxonomyTreesPostTypeWidget.class.php' );
	require_once( 'SortPanelWidget.class.php' );
	require_once( 'CommentsCustomPostTypeWidget.class.php' );
	require_once( 'BrothersListWidget.class.php' );
	require_once( 'ArchivesWidget.class.php' );
	require_once( 'AttributesListWidget.class.php' );
	require_once( 'WishListWidget.class.php' );
	require_once( 'AuthorsWidget.class.php' );
	require_once( 'AuthorWidget.class.php' );
	register_widget( 'LastVisitedWidget' );
	register_widget( 'TCPLoginWidget' );
	register_widget( 'CustomPostTypeListWidget' );
	register_widget( 'CustomValuesWidget' );
	register_widget( 'TaxonomyCloudsPostTypeWidget' );
	register_widget( 'TaxonomyTreesPostTypeWidget' );
	register_widget( 'SortPanelWidget' );
	register_widget( 'CommentsCustomPostTypeWidget' );
	register_widget( 'BrothersListWidget' );
	register_widget( 'TCPArchivesWidget' );
	register_widget( 'AttributesListWidget' );
	register_widget( 'WishListWidget' );
	register_widget( 'TCPAuthorsWidget' );
	register_widget( 'TCPAuthorWidget' );
	//register_widget( 'TCPCalendar' );
}

add_action( 'wp_dashboard_setup', 'tcp_wp_dashboard_setup' );

function tcp_wp_dashboard_setup() {
	//if ( current_user_can( 'tcp_edit_orders' ) || current_user_can( 'tcp_edit_order' ) ) {
		global $thecartpress;
		if ( ! $thecartpress->get_setting( 'disable_ecommerce' ) ) {
			require_once( TCP_WIDGETS_FOLDER . 'OrdersSummaryDashboard.class.php' );
			require_once( TCP_WIDGETS_FOLDER . 'SalesChartDashboard.class.php' );
		}
		global $wp_meta_boxes;
		$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
		$tcp_orders_resume = array( 'tcp_orders_resume' => isset( $normal_dashboard['tcp_orders_resume'] ) ? $normal_dashboard['tcp_orders_resume'] : false );
		unset( $normal_dashboard['tcp_orders_resume'] );
		$sorted_dashboard = array_merge( $tcp_orders_resume, (array)$normal_dashboard);
		$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
	//}
}
?>