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
 * along with This program. If not, see <http://www.gnu.org/licenses/>.
 */

interface IDownloadableManager {

	/**
	 * array(
	 *		array(
	 *			'name' => 'version A',
	 *			'files' => array(
	 *				array( 'file' => 'file_A.png', 'file_name' => 'file A', 'file_ext' => 'png', 'file_size' => '100 kb', 'path' => '' ),
	 *				array( 'file' => 'file_B.pdf', 'file_name' => 'file B', 'file_ext' => 'pdf', 'file_size' => '1.5 Mb', 'path' => '' ),
	 *			),
	 *		),
	 *	);
	 */
	public function get_files( $post_id );

	public function add_version( $post_id, $version_name );
	
	public function delete_version( $post_id, $version );

	public function upload_file( $post_id, $version_name, $file_name, $file_source );

	public function delete_file( $post_id, $version_name, $file_path );

//	public function deleteFiles( $post_id );

}
