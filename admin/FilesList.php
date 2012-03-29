<?php
/**
 * This file is part of TheCartPress.
 * 
 * TheCartPress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TheCartPress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TheCartPress.  If not, see <http://www.gnu.org/licenses/>.
 */

$post_id = isset( $_REQUEST['post_id'] )  ? $_REQUEST['post_id'] : 0;
$tcp_version = isset( $_REQUEST['tcp_version'] ) ? $_REQUEST['tcp_version'] : false;

function tcp_file_icon( $ext ) {
	echo '<img src="' . plugins_url( 'thecartpress/images/default-file.png' ) .'" border="0" width="20px" height="auto"/>';
}

$downloadable_path = isset( $thecartpress->settings['downloadable_path'] ) ? trim( $thecartpress->settings['downloadable_path'] ) : '';
if ( strlen( $downloadable_path ) == 0 ) {
	wp_die( sprintf( __( 'The path where the downloadable files must be saved is not set. Please, visit the <a href="%s">settings</a> page (Main settings section).', 'tcp' ), admin_url( 'admin.php?page=tcp_settings_page' ) ) );
	return false;
}

$manager_def = apply_filters( 'tcp_get_downloadable_manager', array ( 'path' => dirname( dirname( __FILE__ ) ) . '/classes/DownloadableManager.class.php', 'class' => 'TCPDownloadableManager') );
require_once( $manager_def['path'] );
$manager = new $manager_def['class'];

if ( isset( $_REQUEST['tcp_add_version'] ) ) {
	if ( $tcp_version ) {
		if ( $manager->add_version( $post_id, $tcp_version ) ) : ?>
			<div id="message" class="updated"><p>
			<?php _e( 'New version added.', 'tcp' ); ?>
			</p></div>
		<?php else : ?>
			<div id="message" class="error"><p>
			<?php _e( 'Error creating the new version', 'tcp' ); ?>
			</p></div>
		<?php endif;
	} else { ?>
		<div id="message" class="error"><p>
		<?php _e( 'New version field required', 'tcp' ); ?>
		</p></div>
	<?php }
} elseif ( isset( $_REQUEST['tcp_upload_file'] ) ) {
	if ( strlen( $_FILES['tcp_file']['name'] ) == 0 || strlen( $_FILES['tcp_file']['tmp_name'] ) == 0 ) : ?>
		<div id="message" class="error"><p>
		<?php _e( 'One file must be selected', 'tcp' ); ?>
		</p></div>
	<?php endif;
	if ( $manager->upload_file( $post_id, $tcp_version, $_FILES['tcp_file']['name'], $_FILES['tcp_file']['tmp_name'] ) ) : ?>
		<div id="message" class="updated"><p>
		<?php _e( 'File uploaded succesfully', 'tcp' ); ?>
		</p></div>
	<?php endif;
} elseif ( isset( $_REQUEST['tcp_delete_file'] ) ) {
	$file_path = isset( $_REQUEST['file_path'] ) ? $_REQUEST['file_path'] : false;
	if ( $manager->delete_file( $post_id, $tcp_version, $file_path ) ) : ?>
		<div id="message" class="updated"><p>
		<?php _e( 'File deleted succesfully', 'tcp' ); ?>
		</p></div>
	<?php else : ?>
		<div id="message" class="error"><p>
		<?php _e( 'Error deleting file', 'tcp' ); ?>
		</p></div>
	<?php endif;
} elseif ( isset( $_REQUEST['tcp_delete_version'] ) ) {
	if ( $manager->delete_version( $post_id, $tcp_version ) ) : ?>
		<div id="message" class="updated"><p>
		<?php _e( 'Version deleted succesfully', 'tcp' ); ?>
		</p></div>
	<?php else : ?>
		<div id="message" class="error"><p>
		<?php _e( 'Error deleting version', 'tcp' ); ?>
		</p></div>
	<?php endif;
}

$versions = $manager->get_files( $post_id ); ?>

<div class="wrap">
<h2><?php printf( __( 'Files of %s', 'tcp' ), get_the_title( $post_id ) ); ?></h2>
<ul class="subsubsub">
	<li><a href="post.php?action=edit&post=<?php echo $post_id; ?>"><?php _e( 'return to the product', 'tcp' ); ?></a></li>
</ul><!-- subsubsub -->
<div class="clear"></div>

<?php if ( is_array( $versions ) && count( $versions ) > 0 ) : ?>
<ul>
<?php $current_version = '';
$open_ul = false;
foreach( $versions as $version ) :
	if ( $current_version != $version['name'] ) :
		if ( $current_version != '' && $open_ul ) : ?></ul></li><?php endif;
		$current_version = $version['name']; ?>
		<li class="tcp_folder">
			<form method="post">
				<span class="tcp_folder_name"><?php echo $current_version; ?></span>
				<?php if ( count( $version['files'] ) == 0 ) : 
				$open_ul = false; ?>
				<span class="tcp_folder_options">
				<input type="hidden" name="post_id" value="<?php echo $post_id; ?>"/>
				<input type="hidden" name="tcp_version" value="<?php echo $current_version; ?>"/>
				<input type="submit" name="tcp_delete_version" value="<?php _e( 'Delete', 'tcp' ); ?>" class="button-secondary"/>
				</span>
			</form>
		</li>
		<?php else : $open_ul = true; ?>
		<ul>
		<?php endif; ?>
	<?php endif;
	foreach( $version['files'] as $file ) : ?>
		<li class="tcp_file">
			<form method="post">
				<span class="tcp_file_name"><?php tcp_file_icon( $file['file_ext'] ); ?> <?php echo $file['file_name']; ?></span>
				<span class="tcp_file_size"><?php echo $file['file_size']; ?></span>
				<span class="tcp_file_options">
				<input type="hidden" name="post_id" value="<?php echo $post_id; ?>"/>
				<input type="hidden" name="tcp_version" value="<?php echo $current_version; ?>"/>
				<input type="hidden" name="file_path" value="<?php echo $file['path']; ?>"/>
				<input type="submit" name="tcp_delete_file" value="<?php _e( 'Delete', 'tcp' ); ?>" class="button-secondary" />
				<input type="submit" name="tcp_download_file" value="<?php _e( 'Download', 'tcp' ); ?>" class="button-secondary" />
				</span>
			</form>
		</li>
	<?php endforeach; ?>
	<li class="tcp_file">
		<form method="post" enctype="multipart/form-data">
		<label for="tcp_file"><?php _e( 'File to upload', 'tcp' ); ?></label>: <input type="file" id="tcp_file" name="tcp_file" size="20" />
		<input type="hidden" name="post_id" value="<?php echo $post_id; ?>"/>
		<input type="hidden" name="tcp_version" value="<?php echo $current_version; ?>"/>
		<input type="submit" name="tcp_upload_file" value="<?php _e( 'Upload file', 'tcp' ); ?>" class="button-secondary" />
		</form>
	</li>
<?php endforeach; ?>
</ul></li>
<li class="tcp_folder">
	<form method="post">
	<input type="hidden" name="post_id" value="<?php echo $post_id; ?>"/>
	<label for="tcp_version"><?php _e( 'New version', 'tcp' ); ?></label>: <input type="text" name="tcp_version" size="20" maxlength="250" />
	<input type="submit" name="tcp_add_version" value="<?php _e( 'Add version', 'tcp' ); ?>" class="button-secondary" />
	</form>
</li>
</ul>
<?php else : ?>
<p><?php _e( 'The list of files is empty', 'tcp' ); ?><p>
<form method="post">
<input type="hidden" name="post_id" value="<?php echo $post_id; ?>"/>
<label for="tcp_version"><?php _e( 'New version', 'tcp' ); ?></label>: <input type="text" name="tcp_version" size="20" maxlength="250" />
<input type="submit" name="tcp_add_version" value="<?php _e( 'Add version', 'tcp' ); ?>" class="button-secondary" />
</form>
<?php endif; ?>
</div>
