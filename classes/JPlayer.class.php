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

/**
 * Shows an audio/video player based on HTML5 or flash
 * 
 * @see http://jplayer.org/
 */

class TCPJPlayer {

	function show( $post_id = 0, $args = null ) {
		if ( $post_id == 0 ) {
			global $post;
			$post_id	= $post->ID;
			$title		= $post->post_title;
		} else {
			$post = get_post( $post_id );
			$title = $post->post_title;
		}
		$defaults = array(
			'echo'	=> true,
		);
		$args = wp_parse_args( $args, $defaults );

		$out = apply_filters( 'tcp_media_player', '', $post_id, $args );
		if ( strlen( $out ) > 0 ) {
			if ( $args['echo'] ) {
				echo $out;
				return;
			} else {
				return $out;
			}
		}
		$uri = $this->get_url( $post_id );
		if ( strlen( $uri ) > 0 ) {
		$playlists = $this->get_playlists( $post_id );
		//if ( count( $playlists ) > 0 ) {
			ob_start(); ?>
			<div id="tcp_jplayer" class="jp-jplayer"></div>
			<script>
jQuery(document).ready(function() {
	var myPlaylist = new jPlayerPlaylist({
		jPlayer: "#tcp_jplayer",
		cssSelectorAncestor: "#tcp_jplayer_container"
	}, [
<?php foreach( $playlists as $playlist ) : ?>
		{
			title:	'<?php echo $playlist['title']; ?>',
			mp3:	'<?php echo $playlist['uri']; ?>',
			//artist:'The Stark Palace',
			//poster: 'http://www.jplayer.org/audio/poster/The_Stark_Palace_640x360.png'
		},
<?php endforeach; ?>
	], {
		playlistOptions: {
			enableRemoveControls: true
		},
		swfPath:	'<?php echo WP_PLUGIN_URL; ?>/thecartpress/js/jquery.jplayer',
		supplied:	'mp3',
		//wmode:	'window',
	});

	/*jQuery('#tcp_jplayer').jPlayer({
		ready			: function() {
			jQuery('#tcp_jplayer').jPlayer('setMedia', {
				mp3: '<?php echo $uri; ?>'
			});
		},
		solution		: 'flash, html',
		swfPath			: '<?php echo WP_PLUGIN_URL; ?>/thecartpress/js/jquery.jplayer',
		supplied		: 'mp3',
		preload			: 'metadata',
		volume			: 0.8,
		muted			: false,
		backgroundColor	: '#000000',
		cssSelectorAncestor: '.jp-audio',
		cssSelector		: {
			videoPlay		: '.jp-video-play',
			play			: '.jp-play',
			pause			: '.jp-pause',
			stop			: '.jp-stop',
			seekBar			: '.jp-seek-bar',
			playBar			: '.jp-play-bar',
			mute			: '.jp-mute',
			unmute			: '.jp-unmute',
			volumeBar		: '.jp-volume-bar',
			volumeBarValue	: '.jp-volume-bar-value',
			volumeMax		: '.jp-volume-max',
			currentTime		: '.jp-current-time',
			duration		: '.jp-duration',
			fullScreen		: '.jp-full-screen',
			restoreScreen	: '.jp-restore-screen',
			repeat			: '.jp-repeat',
			repeatOff		: '.jp-repeat-off',
			gui				: '.jp-gui',
			noSolution		: '.jp-no-solution'
		},
		errorAlerts		: false,
		warningAlerts	: false
	});*/
});
</script>
			<?php echo $this->showItem( $title );
			$out = ob_get_clean();
			if ( $args['echo'] )
				echo $out;
			else
				return $out;
		}
	}

	function get_url( $post_id ) {
		$uri = '';
		$attachments = get_children( 'post_type=attachment&post_mime_type=audio/mpeg&post_parent=' . $post_id );
		if ( is_array( $attachments ) && count( $attachments ) > 0 ) {
			foreach( $attachments as $attachment ) {
				$uri = $attachment->guid;
				if ( strlen( $uri ) == 0 ) $uri = wp_get_attachment_url( $attachment->ID );
				//$title	= $attachment->post_title;
				break;
			}
		}
		return apply_filters( 'tcp_jplayer_get_url', $uri, $post_id );
	}

	function get_playlists( $post_id ) {
		$playlists = array();
		$attachments = get_children( 'post_type=attachment&post_mime_type=audio/mpeg&post_parent=' . $post_id );
		if ( is_array( $attachments ) && count( $attachments ) > 0 ) {
			foreach( $attachments as $attachment ) {
				$uri = $attachment->guid;
				if ( strlen( $uri ) == 0 ) $uri = wp_get_attachment_url( $attachment->ID );
				$playlists[] = array (
					'uri'	=> $uri,
					'title'	=> $attachment->post_title,
				);
			}
		}
		return apply_filters( 'tcp_jplayer_get_playlis', $playlists, $post_id );
	}

	function showItem( $titles ) { 
		if ( ! is_array( $titles ) ) $titles = array( $titles );?>
	
		<div id="tcp_jplayer_container" class="jp-audio">
			<div class="jp-type-single">
				<div class="jp-gui jp-interface">
					<ul class="jp-controls">
						
						<li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>
						<li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>
						<li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>
						
						<li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>
						<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>
						<li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>
					</ul>
					<div class="jp-progress">
						<div class="jp-seek-bar">
							<div class="jp-play-bar"></div>
						</div>
					</div>
					<div class="jp-volume-bar">
						<div class="jp-volume-bar-value"></div>
					</div>
					<div class="jp-time-holder">
						<div class="jp-current-time"></div>
						<div class="jp-duration"></div>
						<ul class="jp-toggles">
							<li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat">repeat</a></li>
							<li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off">repeat off</a></li>
						</ul>
					</div>
				</div>
				<div class="jp-title">
					<?php //if ( count( $titles ) > 0 ) : ?>
						<ul>
						<?php //foreach( $titles as $title ) : ?>
							<li><?php //echo $title; ?></li>
						<?php //endforeach; ?>
						</ul>
					<?php //endif; ?>
				</div>
				<div class="jp-playlist">
					<ul>
						<!-- The method Playlist.displayPlaylist() uses this unordered list -->
						<li></li>
					</ul>
				</div>
				<div class="jp-no-solution">
					<span><?php _e( 'Update Required', 'tcp' ); ?></span>
					<?php _e( 'To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.', 'tcp' ); ?>
				</div>
			</div>
		</div>
	<?php }

	function admin_init() {
		$tcp_settings = TCP_ADMIN_FOLDER . 'Settings.class.php';
		add_settings_section( 'tcp_jplayer_section', __( 'jPlayer skins', 'tcp' ) , array( $this, 'show_jplayer_section' ), $tcp_settings );
		add_settings_field( 'jplayer_skin', __( 'Select a skin', 'tcp' ), array( $this, 'show_jplayer_skin' ), $tcp_settings , 'tcp_jplayer_section' );
		//add_filter( 'tcp_validate_settings', array( $this, 'tcp_validate_settings' ) );
	}

	function show_jplayer_section() {
	}

	function show_jplayer_skin() {
		global $thecartpress;
		$jplayer_skin = isset( $thecartpress->settings['jplayer_skin'] ) ? $thecartpress->settings['jplayer_skin'] : 'tcp.black';
		if ( $handle = opendir( WP_PLUGIN_DIR . '/thecartpress/js/jquery.jplayer/skins/' ) ) {
		    while ( false !== ( $entry = readdir( $handle ) ) ) : 
		    	if ( $entry != '.' && $entry != '..' ) : ?>
		    <div class="tcp_jplayer_skins">
				<label class="tcp_jplayer_skin_title"><input type="radio" name="tcp_settings[jplayer_skin]" value="<?php echo $entry; ?>" <?php checked( $jplayer_skin, $entry ); ?>/> <?php echo $entry; ?></label>
				<div class="tcp_jplayer_skin_detail">
					<img src="<?php echo WP_PLUGIN_URL, '/thecartpress/js/jquery.jplayer/skins/', $entry; ?>/screenshot.png" />
				</div>
			</div>
	    		<?php endif;
    		endwhile;
		    closedir( $handle );
		    do_action( 'tcp_jplayer_skins', $jplayer_skin );
		}
	}

	function init() {
		wp_register_script( 'tcp_jplayer',  WP_PLUGIN_URL . '/thecartpress/js/jquery.jplayer/jquery.jplayer.min.js' );
		wp_enqueue_script( 'tcp_jplayer' );
		wp_register_script( 'tcp_jplayer_playlist',  WP_PLUGIN_URL . '/thecartpress/js/jquery.jplayer/add-on/jplayer.playlist.min.js' );
		wp_enqueue_script( 'tcp_jplayer_playlist' );
		global $thecartpress;
		if ( $thecartpress ) {
			$jplayer_skin = $thecartpress->get_setting( 'jplayer_skin', 'tcp-black' );
			$url = WP_PLUGIN_URL . '/thecartpress/js/jquery.jplayer/skins/' . $jplayer_skin . '/style.css';
			$url = apply_filters( 'tcp_jplayer_skin_current_skin_url', $url, $jplayer_skin );
			wp_enqueue_style( 'tcp_jplayer_skin', $url );
		}
	}

	function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}
}

$tcp_jplayer = new TCPJPlayer();
?>
