<?php
/**
 * Post related content Metabox
 *
 * Displays related info
 *
 * @package TheCartPress
 * @subpackage Metaboxes
 */

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

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'TCPPostMetabox' ) ) :

require_once( TCP_DAOS_FOLDER . 'RelEntities.class.php' );
		
class TCPPostMetabox {

	function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}

	function admin_init() {
		add_meta_box( 'tcp-post-related-content', __( 'Related content', 'tcp' ), array( $this, 'show' ), 'post', 'normal', 'high' );
		add_action( 'delete_post', array( $this, 'delete_post' ) );
	}

	function show() {
		global $post;
		if ( $post->post_type != 'post' ) return;
		if ( ! current_user_can( 'edit_post', $post->ID ) ) return;
		$lang = isset( $_REQUEST['lang'] ) ? $_REQUEST['lang'] : '';
		$source_lang = isset( $_REQUEST['source_lang'] ) ? $_REQUEST['source_lang'] : '';
		$is_translation = $lang != $source_lang;
		$post_id = tcp_get_default_id( $post->ID, 'post' );
		if ( $is_translation && $post_id == $post->ID) {
			_e( 'After saving the title and content, you will be able to edit these relations.', 'tcp' );
			return;
		} ?>
<?php wp_nonce_field( 'tcp_pm_noncename', 'tcp_pm_noncename' );?>
<ul class="subsubsub">
	<?php $count = RelEntities::count( $post_id, 'POST-PROD' );
	if ( $count > 0 ) $count = ' (' . $count . ')';
	else $count = '';?>
	<li><a href="<?php echo TCP_ADMIN_PATH;?>AssignedProductsList.php&post_id=<?php echo $post_id;?>&rel_type=POST-PROD&post_type_to=tcp_product"><?php _e( 'Related Products', 'tcp' );?> <?php echo $count;?></a></li>
	<?php $count = RelEntities::count( $post_id, 'POST-POST' );
	if ( $count > 0 ) $count = ' (' . $count . ')';
	else $count = '';?>
	<li>|</li>
	<li><a href="<?php echo TCP_ADMIN_PATH;?>AssignedProductsList.php&post_id=<?php echo $post_id;?>&rel_type=POST-POST&post_type_to=post"><?php _e( 'Related Posts', 'tcp' );?> <?php echo $count;?></a></li>
	<?php $count = RelEntities::count( $post_id, 'POST-CAT_PROD' );
	if ( $count > 0 ) $count = ' (' . $count . ')';
	else $count = ''; ?>
	<li>|</li>
	<li><a href="<?php echo TCP_ADMIN_PATH;?>AssignedCategoriesList.php&post_id=<?php echo $post_id;?>&rel_type=POST-CAT_PROD"  title="<?php _e( 'For crossing sell, adds post to the current product', 'tcp' ); ?>"><?php _e( 'Related Cat. of Products', 'tcp' );?> <?php echo $count;?></a></li>
	<?php do_action( 'tcp_template_metabox_show', $post );?>
</ul>
<div class="clear"></div>
	<?php }

	function delete_post( $post_id ) {
		$post = get_post( $post_id );
		if ( ! wp_verify_nonce( isset( $_POST['tcp_pm_noncename'] ) ? $_POST['tcp_pm_noncename'] : '', 'tcp_pm_noncename' ) ) return array( $post_id, $post );
		if ( $post->post_type != 'post' ) return array( $post_id, $post );
		if ( ! current_user_can( 'edit_post', $post_id ) ) return array( $post_id, $post );
		RelEntities::deleteAll( $post_id, 'POST_PROD' );
		do_action( 'tcp_template_metabox_delete', $post_id );
		return $post_id;
	}
}

new TCPPostMetabox();
endif; // class_exists check