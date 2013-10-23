<?php
/**
 * Advanced Menus
 *
 * Adds special menus to WordPress menu features. It's in progress
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.	If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'TCPAdvancedMenus' ) ) {

class TCPAdvancedMenus {

	static function init() {
		add_action( 'admin_init', array( __CLASS__, 'nav_menu_metaboxes' ) );
	}

	static function nav_menu_metaboxes() {
		global $pagenow;
		if ( $pagenow != 'nav-menus.php' ) return;
		add_meta_box( 'tcp-author-menu', __( 'Author Menu', 'tcp' ), array( __CLASS__, 'tcp_author_menu' ), 'nav-menus', 'side', 'default' );
	}

	static function tcp_author_menu() {
		global $_nav_menu_placeholder, $nav_menu_selected_id;
		$_nav_menu_placeholder = 0 > $_nav_menu_placeholder ? $_nav_menu_placeholder - 1 : -1;

		$current_tab = 'create';
		if ( isset( $_REQUEST['customlink-tab'] ) && in_array( $_REQUEST['customlink-tab'], array('create', 'all') ) ) {
			$current_tab = $_REQUEST['customlink-tab'];
		}

		$removed_args = array(
			'action',
			'customlink-tab',
			'edit-menu-item',
			'menu-item',
			'page-tab',
			'_wpnonce',
		); ?>
<div class="customauthordiv" id="customauthordiv">

	<input type="hidden" value="custom" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" />
	<p id="menu-item-url-wrap">
		<label class="howto" for="custom-menu-item-url">
			<span><?php _e('URL'); ?></span>
			<input id="custom-menu-item-url" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-url]" type="text" class="code menu-item-textbox" value="#" />
		</label>
	</p>

	<p id="menu-item-name-wrap">
		<label class="howto" for="custom-menu-item-name">
			<span><?php _e('Label'); ?></span>
			<input id="custom-menu-item-name" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-title]" type="text" class="regular-text menu-item-textbox input-with-default-title" title="<?php esc_attr_e('Menu Item'); ?>" />
		</label>
	</p>

	<p class="button-controls">
		<span class="add-to-menu">
			<input type="submit"<?php disabled( $nav_menu_selected_id, 0 ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e('Add to Menu'); ?>" name="add-custom-author-menu" id="submit-customauthordiv" />
			<span class="spinner"></span>
		</span>
	</p>

</div><!-- /.customauthordiv -->
		<?php
	}
}

TCPAdvancedMenus::init();
} // class_exists check