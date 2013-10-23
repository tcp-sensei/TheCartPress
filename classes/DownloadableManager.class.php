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

if ( ! class_exists( 'TCPDownloadableManager' ) ) {

require_once( dirname( __FILE__ ) . '/IDownloadableManager.interface.php' );

class TCPDownloadableManager implements IDownloadableManager {

	public function get_files( $post_id ) {
		$downloadable_path = $this->get_downloadable_path( $post_id );
		$versions = array();
		if ( $downloadable_path ) {
			return $this->load_folders( $downloadable_path );
		} else {
			return true;
		}
	}

	public function add_version( $post_id, $version ) {
		$downloadable_path = $this->get_downloadable_path( $post_id );
		$path = $downloadable_path . '/' . $version;
		if ( ! wp_mkdir_p( $path ) ) {
			return false;
		} else {
			return true;
		}
	}

	public function upload_file( $post_id, $version, $file_name, $file_source ) {
		$downloadable_path = $this->get_downloadable_path( $post_id );
		$file_path = $downloadable_path . '/' . $version . '/' . $file_name;
		if ( move_uploaded_file( $file_source, $file_path ) ) {
			$stat = stat( dirname( $file_path ));
			$perms = $stat['mode'] & 0000666;
			@chmod( $file_path, $perms );
			return true;
		} else {
			return false;
		}
	}

	public function delete_file( $post_id, $version, $file_path ) {
		//echo "$post_id, $version, $file_path<br>";
		return false;
	}
	
	public function delete_version( $post_id, $version ) {
		$downloadable_path = $this->get_downloadable_path( $post_id );
		$path = $downloadable_path . '/' . $version;
		rmdir( $path );
		return true;
	}
	/*public function getFiles( $post_id ) {
		return array(
			array(
				'name' => 'version A',
				'files' => array(
					array( 'file' => 'file_A.png', 'file_name' => 'file A', 'file_ext' => 'png', 'file_size' => '100 kb', 'path' => '' ),
					array( 'file' => 'file_B.pdf', 'file_name' => 'file B', 'file_ext' => 'pdf', 'file_size' => '1.5 Mb', 'path' => '' ),
				),
			),
			array(
				'name' => 'version B',
				'files' => array(),
			),
		);
	}*/

	private function get_downloadable_path( $post_id, $create_if_not_exists = true ) {
		$path = $this->tcp_get_downloadable_path() . '/tcp_downloadable_' . $post_id;
		if ( $path && $create_if_not_exists ) wp_mkdir_p( $path ); //mkdir( $path, 077, true );
		return $path;
	}

	private function tcp_get_downloadable_path() {
		global $thecartpress;
		return $thecartpress->get_setting( 'downloadable_path', false );
	}

	private function load_folders( $folder, $name = '' ) {
		$folder = $folder . $name . '/';
		$folders = array();
		if ( $handle = opendir( $folder ) ) {
			while ( false !== ( $file = readdir( $handle ) ) ) {
				if ( $file != '..' && $file != '.' ) {
					if ( is_dir( $folder . $file ) ) {
						$folders[] = array(
							'name'	=> $file,
							'files'	=> $this->load_folders( $folder, $file ),
							'path'	=> $folder . $file,
							'url'	=> $this->path_to_url( $folder . $file ),
						);
					} else {
						$folders[] = array(
							'file'		=> $file,
							'file_name'	=> $file,
							'file_ext'	=> $file,
							'file_size'	=> '100Kb',
							'path'		=> $folder . $file,
							'url'		=> $this->path_to_url( $folder . $file ),
						);
					}
				}
			}
		}
		return $folders;
	}

	private function path_to_url( $file ) {
		$downloadable_path = $this->tcp_get_downloadable_path();
		if ( strpos( $file, $downloadable_path ) !== FALSE ) {
			return home_url() . substr( $file, strlen( $downloadable_path ) );
		} else {
			return home_url() . $file;
		}
	}
}
} // class_exists check