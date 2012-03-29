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

class TCPBuyButton {
	static function show( $post_id = 0, $echo = true  ) {
		if ( $post_id == 0 ) $post_id = get_the_ID();
		ob_start();
		$file_name	= 'tcp_buybutton-' . strtolower( tcp_get_the_product_type( $post_id ) ) . '.php';
		$template	= STYLESHEETPATH . '/' . $file_name;
		$template	= apply_filters( 'tcp_get_buybutton_template', $template, $post_id );
		if ( file_exists( $template ) ) {
			include( $template );
		} else {
			$template = get_template_directory() . '/' . $file_name;
			if ( STYLESHEETPATH != get_template_directory() && file_exists( $template ) ) {
				include( $template );
			} else {
				$template = TCP_THEMES_TEMPLATES_FOLDER . $file_name;
				if ( file_exists( $template ) ) {
					include( $template );
				}
			}
		}
		$out = ob_get_clean();
		if ( $echo ) echo $out;
		else return $out;
	}
}
?>
