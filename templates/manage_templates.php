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


// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

require_once( 'tcp_buybutton_template.php' );
require_once( 'tcp_calendar_template.php' );
require_once( 'tcp_custom_taxonomies.php' );
require_once( 'tcp_general_template.php' );
require_once( 'tcp_ordersmeta_template.php' );
require_once( 'tcp_states_template.php' );
require_once( 'tcp_template.php' );
require_once( 'tcp_template_template.php' );
require_once( 'tcp_template_login.php' );

require_once( TCP_CHECKOUT_FOLDER	. 'tcp_checkout_template.php' );