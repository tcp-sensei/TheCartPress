<?php
/**
 * Manage Modules
 *
 * Loads modules to extend the core funtionality
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

require_once( 'AdminBarMenu.class.php' );
require_once( 'AdvancedCommunication.class.php' );
require_once( 'Ajax.class.php' );
//require_once( 'Bootstrap.class.php' );
require_once( 'BuddyPress.class.php' );
require_once( 'BuyButton.class.php' );

require_once( 'CopyOrder.class.php' );
//require_once( 'CheckoutPermalinks.class.php' );

require_once( 'CustomFields.class.php' );
require_once( 'CustomJavascript.class.php' );
require_once( 'CustomStyles.class.php' );
require_once( 'CustomTemplates.class.php' );
require_once( 'FilterNavigation.class.php' );
require_once( 'GroupedProducts.class.php' );
require_once( 'LastLogin.class.php' );
require_once( 'LoginRegister.class.php' );
require_once( 'StockManagement.class.php' );
require_once( 'TaxonomyImages.class.php' );

require_once( 'ThemeCompat.class.php' );

require_once( 'TopSellers.class.php' );
require_once( 'UIImprovements.class.php' );
require_once( 'UnderConstruction.class.php' );
require_once( 'UnitsInBuyButton.class.php' );
require_once( 'WishList.class.php' );
require_once( 'WPPluginsAdminPanel.class.php' );
?>