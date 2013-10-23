<?php
/**
 * M3Playes
 *
 * Old implementation for music Files. Not in use
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

return;//not in use
/**
 * 
 * Shows a mp3 player based on HTML5 or flash
 * 
 * @see http://flash-mp3-player.net/players/multi/documentation/
 */
class TCPMP3Player {
	public static $BIG = 'BIG';
	public static $SMALL = 'SMALL';

	static function showPlayer( $post_id = 0, $formato = 'BIG', $echo = true ) {
		if ( $post_id == 0 ) {
			global $post;
			$post_id = $post->ID;
		}	
		$attachments = get_children( 'post_type=attachment&post_mime_type=audio/mpeg&post_parent=' . $post_id );
		if ( is_array( $attachments ) && count( $attachments ) > 0 ) {
			foreach( $attachments as $attachment ) {
				$mp3 = $attachment->guid . '|';
				$title = $attachment->post_title . '|';
			}
			$mp3 = substr( $mp3, 0, strlen( $mp3 ) - 1 );
			$title = substr( $title, 0, strlen( $title ) - 1 );
			$out = TCPMP3Player::showItemPlayer( $formato, $mp3, $title );
			if ( $echo )
				echo $out;
			else
				return $out;
		}
	}

	/**
	 * @param $formato possible values are BIG, SMALL
	 */
	static function showItemPlayer( $format, $mp3, $title ) {
		if ( count( $mp3 ) == 0 ) {
			return;
		} else {
			$html = '<audio controls><source src="' . $mp3 . '" type="audio/mpeg" />';
			if ( $format == TCPMP3Player::$BIG ) {
				$height = 20 + count($mp3) * 10;
				$html .= '<object type="application/x-shockwave-flash" data="' . plugins_url( '/swfs/player_mp3_multi.swf', dirname( __FILE__ ) ) . '" width="200" height="' . $height . '">
							<param name="movie" value="player_mp3_multi.swf" />
							<param name="FlashVars" value="mp3=' . $mp3 . '&' . 'title=' . $title . '&showvolume=1&showlist=1&height=100" />
						</object>';
			} elseif ( $format == TCPMP3Player::$SMALL ) {
				return '<object width="150" height="20" data="' . plugins_url( '/swfs/player_mp3_multi.swf', dirname( __FILE__ ) ) . '" type="application/x-shockwave-flash">
						<param name="movie" value="player_mp3_multi.swf" />
					<param value="mp3=' . $mp3 . '&showstop=0&width=100&showslider=0" name="FlashVars">
					</object>';
			}
			$html .= '</audio>';
			return $html;
		}
	}
}
?>
