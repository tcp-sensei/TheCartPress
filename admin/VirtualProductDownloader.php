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

$allowed_ext = array(
	'ez'	=> 'application/andrew-inset',
	'hqx'	=> 'application/mac-binhex40',
	'cpt'	=> 'application/mac-compactpro',
	'doc'	=> 'application/msword',
	'bin'	=> 'application/octet-stream',
	'dms'	=> 'application/octet-stream',
	'lha'	=> 'application/octet-stream',
	'lzh'	=> 'application/octet-stream',
	'exe'	=> 'application/octet-stream',
	'class'	=> 'application/octet-stream',
	'so'	=> 'application/octet-stream',
	'dll'	=> 'application/octet-stream',
	'oda'	=> 'application/oda',
	'pdf'	=> 'application/pdf',
	'ai'	=> 'application/postscript',
	'eps'	=> 'application/postscript',
	'ps'	=> 'application/postscript',
	'smi'	=> 'application/smil',
	'smil'	=> 'application/smil',
	'wbxml'	=> 'application/vnd.wap.wbxml',
	'wmlc'	=> 'application/vnd.wap.wmlc',
	'wmlsc'	=> 'application/vnd.wap.wmlscriptc',
	'bcpio'	=> 'application/x-bcpio',
	'vcd'	=> 'application/x-cdlink',
	'pgn'	=> 'application/x-chess-pgn',
	'cpio'	=> 'application/x-cpio',
	'csh'	=> 'application/x-csh',
	'dcr'	=> 'application/x-director',
	'dir'	=> 'application/x-director',
	'dxr'	=> 'application/x-director',
	'dvi'	=> 'application/x-dvi',
	'spl'	=> 'application/x-futuresplash',
	'gtar'	=> 'application/x-gtar',
	'hdf'	=> 'application/x-hdf',
	'js'	=> 'application/x-javascript',
	'skp'	=> 'application/x-koan',
	'skd'	=> 'application/x-koan',
	'skt'	=> 'application/x-koan',
	'skm'	=> 'application/x-koan',
	'latex'	=> 'application/x-latex',
	'nc'	=> 'application/x-netcdf',
	'cdf'	=> 'application/x-netcdf',
	'sh'	=> 'application/x-sh',
	'shar'	=> 'application/x-shar',
	'swf'	=> 'application/x-shockwave-flash',
	'sit'	=> 'application/x-stuffit',
	'sv4cpio'	=> 'application/x-sv4cpio',
	'sv4crc'=> 'application/x-sv4crc',
	'tar'	=> 'application/x-tar',
	'tcl'	=> 'application/x-tcl',
	'tex'	=> 'application/x-tex',
	'texinfo'	=> 'application/x-texinfo',
	'texi'	=> 'application/x-texinfo',
	't'		=> 'application/x-troff',
	'tr'	=> 'application/x-troff',
	'roff'	=> 'application/x-troff',
	'man'	=> 'application/x-troff-man',
	'me'	=> 'application/x-troff-me',
	'ms'	=> 'application/x-troff-ms',
	'ustar'	=> 'application/x-ustar',
	'src'	=> 'application/x-wais-source',
	'xhtml'	=> 'application/xhtml+xml',
	'xht'	=> 'application/xhtml+xml',
	'zip'	=> 'application/zip',
	'au'	=> 'audio/basic',
	'snd'	=> 'audio/basic',
	'mid'	=> 'audio/midi',
	'midi'	=> 'audio/midi',
	'kar'	=> 'audio/midi',
	'mpga'	=> 'audio/mpeg',
	'mp2'	=> 'audio/mpeg',
	'mp3'	=> 'audio/mpeg',
	'aif'	=> 'audio/x-aiff',
	'aiff'	=> 'audio/x-aiff',
	'aifc'	=> 'audio/x-aiff',
	'm3u'	=> 'audio/x-mpegurl',
	'ram'	=> 'audio/x-pn-realaudio',
	'rm'	=> 'audio/x-pn-realaudio',
	'rpm'	=> 'audio/x-pn-realaudio-plugin',
	'ra'	=> 'audio/x-realaudio',
	'wav'	=> 'audio/x-wav',
	'pdb'	=> 'chemical/x-pdb',
	'xyz'	=> 'chemical/x-xyz',
	'bmp'	=> 'image/bmp',
	'gif'	=> 'image/gif',
	'ief'	=> 'image/ief',
	'jpeg'	=> 'image/jpeg',
	'jpg'	=> 'image/jpeg',
	'jpe'	=> 'image/jpeg',
	'png'	=> 'image/png',
	'tiff'	=> 'image/tiff',
	'tif'	=> 'image/tif',
	'djvu'	=> 'image/vnd.djvu',
	'djv'	=> 'image/vnd.djvu',
	'wbmp'	=> 'image/vnd.wap.wbmp',
	'ras'	=> 'image/x-cmu-raster',
	'pnm'	=> 'image/x-portable-anymap',
	'pbm'	=> 'image/x-portable-bitmap',
	'pgm'	=> 'image/x-portable-graymap',
	'ppm'	=> 'image/x-portable-pixmap',
	'rgb'	=> 'image/x-rgb',
	'xbm'	=> 'image/x-xbitmap',
	'xpm'	=> 'image/x-xpixmap',
	'xwd'	=> 'image/x-windowdump',
	'igs'	=> 'model/iges',
	'iges'	=> 'model/iges',
	'msh'	=> 'model/mesh',
	'mesh'	=> 'model/mesh',
	'silo'	=> 'model/mesh',
	'wrl'	=> 'model/vrml',
	'vrml'	=> 'model/vrml',
	'css'	=> 'text/css',
	'html'	=> 'text/html',
	'htm'	=> 'text/html',
	'asc'	=> 'text/plain',
	'txt'	=> 'text/plain',
	'rtx'	=> 'text/richtext',
	'rtf'	=> 'text/rtf',
	'sgml'	=> 'text/sgml',
	'sgm'	=> 'text/sgml',
	'tsv'	=> 'text/tab-seperated-values',
	'wml'	=> 'text/vnd.wap.wml',
	'wmls'	=> 'text/vnd.wap.wmlscript',
	'etx'	=> 'text/x-setext',
	'xml'	=> 'text/xml',
	'xsl'	=> 'text/xml',
	'mpeg'	=> 'video/mpeg',
	'mpg'	=> 'video/mpeg',
	'mpe'	=> 'video/mpeg',
	'qt'	=> 'video/quicktime',
	'mov'	=> 'video/quicktime',
	'mxu'	=> 'video/vnd.mpegurl',
	'avi'	=> 'video/x-msvideo',
	'movie'	=> 'video/x-sgi-movie',
	'ice'	=> 'x-conference-xcooltalk'
);
$wordpress_path = dirname( dirname( dirname( dirname( dirname( __FILE__) ) ) ) ) . '/';
include_once( $wordpress_path . 'wp-config.php' );
include_once( $wordpress_path . 'wp-includes/wp-db.php' );

if ( isset( $_REQUEST['order_detail_id'] ) || ( isset( $_REQUEST['uuid'] ) && isset( $_REQUEST['did'] ) ) ) {
	$order_detail_id = isset( $_REQUEST['order_detail_id'] ) ? $_REQUEST['order_detail_id'] : $_REQUEST['did'];
	global $wpdb;
	global $current_user;
	get_currentuserinfo();
	$customer_id = $current_user->ID;
	require_once( dirname( dirname( __FILE__ ) ) . '/templates/tcp_template.php' );
	require_once( dirname( dirname( __FILE__ ) ) . '/daos/Orders.class.php' );
	if ( Orders::isProductDownloadable( $customer_id, $order_detail_id ) ) {
		if ( $customer_id == 0 ) {
			$uuid = isset( $_REQUEST['uuid'] ) ? $_REQUEST['uuid'] : '';
			require_once( dirname( dirname( __FILE__ ) ) . '/classes/DownloadableProducts.class.php' );
			if ( $uuid != tcp_get_download_uuid( $order_detail_id ) ) {
				wp_die( __( 'You do not have sufficient permissions to access this page.', 'tcp' ) );
			}
		}
		$order_detail = OrdersDetails::get( $order_detail_id );
		$post_id = $order_detail->post_id;
		$file_path = tcp_get_the_file( $post_id );
		do_action( 'tcp_download_file', $file_path );
		if ( ! file_exists( $file_path ) ) {
			wp_die( __( 'The file doesn\'t exists.', 'tcp' ) );
			return;
		}
		$file_size = filesize( $file_path );
		$path = explode( '/', $file_path );
		$file_name = $path[count( $path ) - 1];
		$file_ext = strtolower( substr( strrchr( $file_name, "." ), 1 ) );

		if ( array_key_exists( $file_ext, $allowed_ext ) )
			$mime_type = $allowed_ext[$file_ext];
		elseif ( function_exists( 'mime_content_type' ) )
			$mime_type = mime_content_type( $file_path );
		else if ( function_exists( 'finfo_file' ) ) {
			$file_info = finfo_open( FILEINFO_MIME );
			$mime_type = finfo_file( $file_info, $file_path );
			finfo_close( $file_info );
		}
		else $mime_type = 'application/force-download';
		$file_name = get_the_title( $post_id ) . '.' . $file_ext . '';
		$file_name = str_replace( ' ', '_', $file_name );
		//$file_name = esc_html( $file_name );
		// set headers
		header( 'Pragma: public' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Cache-Control: public' );
		header( 'Content-Description: File Transfer' );
		header( 'Content-Type: '. $mime_type );
		header( 'Content-Disposition: attachment; filename=' . $file_name );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Content-Length: ' . $file_size );

		$file = @fopen( $file_path, "rb" );
		if ( $file ) {
			while( ! feof( $file ) ) {
				print( fread( $file, 1024 * 8 ) );
				flush();
				$connection_status = connection_status();
				if ( $connection_status != 0 ) {
					@fclose( $file );
					_e( 'The file cannot be downloaded. Error number ' . $connection_status, 'tcp' );
					return;
				}
			}
			@fclose( $file );
			Orders::takeAwayDownload( $order_detail_id );
			do_action( 'tcp_download_file_ended', $file_path );
		} else {
			wp_die( __( 'The file doesn\'t exists.', 'tcp' ) );
		}
	} else {
		wp_die( __( 'You do not have sufficient permissions to access this page.', 'tcp' ) );
	}
} else {
	wp_die( __( 'You do not have sufficient permissions to access this page.', 'tcp' ) );
}
?>
