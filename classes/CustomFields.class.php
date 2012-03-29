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

class TCPCustomFields {

	function init() {
		require_once( TCP_TEMPLATES_FOLDER . 'tcp_custom_fields_template.php' );
	}

	function admin_menu() {
		global $thecartpress;
		$base = $thecartpress->get_base_tools();
		add_submenu_page( $base, __( 'Custom fields', 'tcp' ), __( 'Custom fields', 'tcp' ), 'tcp_edit_products', dirname( dirname( __FILE__ ) ) . '/admin/CustomFieldsList.php' );
	}

	function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			require_once( dirname( dirname( __FILE__ ) ) . '/metaboxes/CustomFieldsMetabox.class.php' );
			add_action( 'admin_init', array( new CustomFieldsMetabox(), 'registerMetaBox' ), 99 );
		}
	}
}

new TCPCustomFields();
?>