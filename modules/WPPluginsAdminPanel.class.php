<?php
/**
 * Plugins panel
 *
 * Allows to add a filter in the Plugins panel, to see only the TheCartPress plugins
 * Those plugins must have a label called parent, with value "TheCartPress"
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

class TCPWPPluginsAdminPanel {

	function __construct() {
		if ( is_admin() ) {
			add_filter( 'extra_plugin_headers'	, array( $this, 'extra_plugin_headers' ) );
			add_filter( 'plugin_row_meta'		, array( $this, 'plugin_row_meta' ) , 10, 4 );
			add_filter( 'views_plugins'			, array( $this, 'views_plugins' ) );
			add_filter( 'all_plugins'			, array( $this, 'all_plugins' ) );
			add_filter( 'plugin_action_links'	, array( $this, 'plugin_action_links' ), 10, 2 );
		}
	}

	//Plugin screen
	function extra_plugin_headers( $headers ) {
		$headers['parent'] = 'Parent';
		return $headers;
	}
	
	function plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {
		if ( isset( $plugin_data['Parent'] ) && strtolower( $plugin_data['Parent'] ) == 'thecartpress' && $plugin_data['Name'] != 'TheCartPress' )
		$plugin_meta[] = __( 'Child of TheCartPress', 'tcp' );
		return $plugin_meta;
	}
	
	function views_plugins( $views ) {
		global $plugins;
		$children = 0;
		foreach( $plugins['all'] as $id => $plugin_data )
		if ( isset( $plugin_data['Parent'] ) && strtolower( $plugin_data['Parent'] ) == 'thecartpress' )
		$children++;
		$views['thecartpress'] = sprintf( '<a href="%s" %s>%s</a>%s',
			add_query_arg( 'plugin_status', 'child_of_thecartpress', 'plugins.php' ),
			' class="child_of_thecartpress"',
			'TheCartPress',
			$children > 0 ? " ($children)" : ''
		);
		return $views;
	}
	
	function plugin_action_links( $links, $file ) {
		if ( $file == 'thecartpress/TheCartPress.class.php' && function_exists( 'admin_url' ) ) {
			//$first_link = '<a href="' . admin_url( 'admin.php?page=thecartpress/admin/FirstTimeSetUp.php' ). '">' . __( 'First time setup', 'tcp' ) . '</a>';
			$settings_link = '<a href="' . admin_url( 'admin.php?page=thecartpress/TheCartPress.class.php' ). '">' . __( 'Settings', 'tcp' ) . '</a>';
			//array_unshift( $links, $first_link, $settings_link );
			array_unshift( $links, $settings_link );
		}
		return $links;
	}
	
	function all_plugins( $plugins ) {
		if ( isset( $_REQUEST['plugin_status'] ) && $_REQUEST['plugin_status'] == 'child_of_thecartpress' )
			foreach( $plugins as $id => $plugin_data )
				if ( ! isset( $plugin_data['Parent'] ) )
					unset( $plugins[$id] );
				elseif( strtolower( $plugin_data['Parent'] ) != 'thecartpress' )
					unset( $plugins[$id] );
		return $plugins;
	}
}

new TCPWPPluginsAdminPanel();
?>
