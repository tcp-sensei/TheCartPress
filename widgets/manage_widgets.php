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
 * Load and regiser TCP widgets
 */

global $thecartpress;
$disable_ecommerce = $thecartpress->get_setting( 'disable_ecommerce', false );
if ( ! $disable_ecommerce ) {
	require_once( 'ShoppingCartSummaryWidget.class.php' );
	require_once( 'ShoppingCartWidget.class.php' );
	require_once( 'LastVisitedWidget.class.php' );
	require_once( 'RelatedListWidget.class.php' );
	require_once( 'CheckoutWidget.class.php' );
	require_once( 'SelectCountryWidget.class.php' );
	register_widget( 'ShoppingCartSummaryWidget' );
	register_widget( 'ShoppingCartWidget' );
	register_widget( 'LastVisitedWidget' );
	register_widget( 'RelatedListWidget' );
	register_widget( 'CheckoutWidget' );//TODO At this moment, only for testing purpouse
	register_widget( 'TCPSelectCountryWidget' );
}
require_once( 'CustomPostTypeListWidget.class.php' );
require_once( 'TaxonomyCloudsPostTypeWidget.class.php' );
require_once( 'TaxonomyTreesPostTypeWidget.class.php' );
require_once( 'SortPanelWidget.class.php' );
require_once( 'CommentsCustomPostTypeWidget.class.php' );
require_once( 'BrothersListWidget.class.php' );
require_once( 'ArchivesWidget.class.php' );
require_once( 'AttributesListWidget.class.php' );
register_widget( 'CustomPostTypeListWidget' );
register_widget( 'TaxonomyCloudsPostTypeWidget' );
register_widget( 'TaxonomyTreesPostTypeWidget' );
register_widget( 'SortPanelWidget' );
register_widget( 'CommentsCustomPostTypeWidget' );
register_widget( 'BrothersListWidget' );
register_widget( 'TCPArchivesWidget' );
register_widget( 'AttributesListWidget' );
//register_widget( 'TCPCalendar' );
?>
