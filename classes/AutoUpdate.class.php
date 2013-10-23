<?php
/**
 * AutoUpdate
 *
 * Manage Extend Plugin updates (Beta version)
 *
 * @package TheCartPress
 * @subpackage Classes
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

//Code based on https://github.com/omarabid/Self-Hosted-WordPress-Plugin-repository/blob/master/wp_autoupdate.php

class TCPAutoUpdate {
	/**
	 * The plugin current version
	 * @var string
	 */
	public $current_version;

	/**
	 * The plugin remote update path
	 * @var string
	 */
	public $update_path;

	/**
	 * Plugin Slug (plugin_directory/plugin_file.php)
	 * @var string
	 */
	public $plugin_slug;

	/**
	 * Plugin name (plugin_file)
	 * @var string
	 */
	public $slug;

	/**
	 * Initialize a new instance of the WordPress Auto-Update class
	 * @param string $current_version
	 * @param string $update_path
	 * @param string $plugin_slug
	 * @change by TCP, the order of the parameters
	 */
	function __construct( $plugin_file, $update_path = 'http://extend.thecartpress.com/xmlrpc.php' ) {
		$plugin_data = get_plugin_data( $plugin_file );
		$this->current_version = $plugin_data['Version'];
		$this->update_path = $update_path;
		$plugin = plugin_basename( $plugin_file );
		$this->plugin_slug = $plugin;
		list ($t1, $t2) = explode('/', $plugin);
		$this->slug = str_replace('.php', '', $t2);
		add_filter( 'pre_set_site_transient_update_plugins', array( &$this, 'check_update') );
		add_filter( 'plugins_api', array( &$this, 'check_info' ), 10, 3 );
	}

	/**
	 * Add our self-hosted autoupdate plugin to the filter transient
	 *
	 * @param $transient
	 * @return object $transient
	 */
	public function check_update( $transient ) {
		if ( empty( $transient->checked ) ) {
			return $transient;
		}
		$remote_version = $this->getRemote_version();
		// If a newer version is available, add the update
		if ( version_compare( $this->current_version, $remote_version, '<' ) ) {
			$obj = new stdClass();
			$obj->slug = $this->slug;
			$obj->new_version = $remote_version;
			$obj->url = $this->update_path;
			$obj->package = $this->update_path;
			$transient->response[$this->plugin_slug] = $obj;
		}
		return $transient;
	}

	/**
	 * Add our self-hosted description to the filter
	 *
	 * @param boolean $false
	 * @param array $action
	 * @param object $arg
	 * @return bool|object
	 */
	public function check_info($false, $action, $arg)
	{
		if ($arg->slug === $this->slug) {
			$information = $this->getRemote_information();
			return $information;
		}
		return false;
	}

	/**
	 * Return the remote version
	 * @return string $remote_version
	 */
	public function getRemote_version() {
		$request = $this->get_remote_post( 'tcp.getPluginVersion' );
		return $request;

	}

	/**
	 * Get information about the remote version
	 * @return bool|object
	 */
	public function getRemote_information() {
		$request = $this->get_remote_post( 'tcp.getPluginInfo' );
		return unserialize( $request );
	}

	/**
	 * Return the status of the plugin licensing
	 * @return boolean $remote_license
	 */
	public function getRemote_license() {
		$request = $this->get_remote_post( 'tcp.getPluginLicense' );
		return $request;
	}

	public function get_remote_post( $method ) {
		if ( function_exists ( 'xmlrpc_encode_request' ) ) {
			//$xml = xmlrpc_encode_request( $method, array( 'plugin_slug' => $this->slug ) );
			$xml = xmlrpc_encode_request( $method, array( 'plugin_slug' => $this->plugin_slug ) );
			$curl_hdl = curl_init();
			curl_setopt( $curl_hdl, CURLOPT_URL, $this->update_path );
			curl_setopt( $curl_hdl, CURLOPT_HEADER, 0 ); 
			curl_setopt( $curl_hdl, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $curl_hdl, CURLOPT_POST, true );
			curl_setopt( $curl_hdl, CURLOPT_POSTFIELDS, $xml );
			// Invoke RPC command
			$response = curl_exec( $curl_hdl );
			curl_close( $curl_hdl );
			$result = xmlrpc_decode_request( $response, $method );
			return $result;
		}
	}

	static function tcp_uploaded_file( $file_path, $post_id ) {
		$temp_dir = tempnam( sys_get_temp_dir(), '' );
		if ( file_exists( $temp_dir ) ) unlink( $temp_dir );
		mkdir( $temp_dir );
		function __tcp_return_direct() { return 'direct'; }
		add_filter( 'filesystem_method', '__tcp_return_direct' );
		WP_Filesystem();
		remove_filter( 'filesystem_method', '__tcp_return_direct' );
		if ( is_dir( $temp_dir ) && unzip_file( $file_path, $temp_dir ) === true ) {
			$files = TCPAutoUpdate::get_files_from_plugin( $temp_dir );
			if ( is_array( $files ) && count ( $files ) > 0 ) {
				$plugin_data = reset( $files );
				$plugin_slug = key( $files );
				update_post_meta( $post_id, 'tcp_plugin_slug', $plugin_slug );
				update_post_meta( $post_id, 'tcp_plugin_new_version', $plugin_data['Version'] ); //1.1
				//update_post_meta( $post_id, 'tcp_plugin_requires', $plugin_data['tcp_plugin_requires'] );//3.1
				//update_post_meta( $post_id, 'tcp_plugin_tested', $plugin_data['tcp_plugin_tested'] ); //3.5.1
				update_post_meta( $post_id, 'tcp_plugin_last_updated', $hoy = date( 'Y-m-d' ) );//'2012-01-12'
			}
		}
	}

	static function get_files_from_plugin( $plugins_dir ) {
		$plugins = array();
		$plugin_files = array();
		$pdir = @opendir( $plugins_dir );
		if ( $pdir ) {
			while ( ( $file = readdir( $pdir ) ) !== false ) {
				if ( substr( $file, 0, 1) == '.' ) continue;
				if ( is_dir( $plugins_dir . '/' . $file ) ) {
					$plugins_subdir = @ opendir( $plugins_dir . '/' . $file );
					if ( $plugins_subdir ) {
						while ( ( $subfile = readdir( $plugins_subdir ) ) !== false ) {
							if ( substr($subfile, 0, 1) == '.' ) continue;
							if ( substr($subfile, -4) == '.php' ) $plugin_files[] = "$file/$subfile";
						}
						closedir( $plugins_subdir );
					}
				} else {
					if ( substr($file, -4) == '.php' ) $plugin_files[] = $file;
				}
			}
			closedir( $pdir );
		}
		foreach ( $plugin_files as $plugin_file ) {
			if ( ! is_readable( "$plugins_dir/$plugin_file" ) ) continue;
			$plugin_data = get_plugin_data( "$plugins_dir/$plugin_file", false, false ); //Do not apply markup/translate as it'll be cached.
			if ( empty ( $plugin_data['Name'] ) ) continue;
			$plugins[plugin_basename( $plugin_file )] = $plugin_data;
		}
		return $plugins;
	}
}

add_action( 'tcp_uploaded_file', array( 'TCPAutoUpdate', 'tcp_uploaded_file' ), 10, 2 );
?>