<?php
/**
 * Catalogue settings
 *
 * @package TheCartPress
 * @subpackage Appearance
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

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'TCPLoopSettings' ) ) :

class TCPLoopSettings {

	function __construct() {
		add_action( 'tcp_admin_menu', array( $this, 'tcp_admin_menu' ), 10 );
	}

	function tcp_admin_menu() {
		if ( ! current_user_can( 'tcp_edit_settings' ) ) return;
		global $thecartpress;
		$base = $thecartpress->get_base_appearance();
		$page = add_submenu_page( $base, __( 'Catalogue Settings', 'tcp' ), __( 'Catalogue', 'tcp' ), 'tcp_edit_settings', 'loop_settings', array( &$this, 'admin_page' ) );
		add_action( "load-$page", array( $this, 'admin_load' ) );
		add_action( "load-$page", array( $this, 'admin_action' ) );
	}

	function admin_load() {
		get_current_screen()->add_help_tab( array(
			'id'      => 'overview',
			'title'   => __( 'Overview' ),
			'content' =>
				'<p>' . __( 'Customize your Catalogue look&feel.', 'tcp' ) . '</p>'
		) );
		get_current_screen()->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'tcp' ) . '</strong></p>' .
			'<p>' . __( '<a href="http://thecartpress.com" target="_blank">Documentation on TheCartPress</a>', 'tcp' ) . '</p>' .
			'<p>' . __( '<a href="http://community.thecartpress.com/" target="_blank">Support Forums</a>', 'tcp' ) . '</p>' .
			'<p>' . __( '<a href="http://extend.thecartpress.com/" target="_blank">Extend site</a>', 'tcp' ) . '</p>'
		);
	}

	function admin_page() {
		$settings = get_option( 'ttc_settings' );
?>
<div class="wrap">
	<?php screen_icon( 'tcp-loop-settings' ); ?><h2><?php _e( 'Catalogue Settings', 'tcp' ); ?></h2>

	<p class="description"><?php _e( 'Allows to configure how to display products, or any other post type, in your Catalogues', 'tcp' ); ?></p>

	<?php if ( !empty( $this->updated ) ) : ?>
	<div id="message" class="updated">
		<p><?php _e( 'Settings updated', 'tcp' ); ?></p>
	</div>
	<?php endif; ?>

<?php if ( isset( $_REQUEST['current_post_type'] ) && strlen( trim( $_REQUEST['current_post_type'] ) ) > 0 ) {
	$current_post_type = $_REQUEST['current_post_type'];
	$suffix = '-' . $current_post_type;
} else {
	$suffix = '';
	$current_post_type = '';
}
$see_title				= isset( $settings['see_title' . $suffix] ) ? $settings['see_title' . $suffix] : true;
$title_tag				= isset( $settings['title_tag' . $suffix] ) ? $settings['title_tag' . $suffix] : 'h2';
$see_image				= isset( $settings['see_image' . $suffix] ) ? $settings['see_image' . $suffix] : true;
$image_size				= isset( $settings['image_size' . $suffix] ) ? $settings['image_size' . $suffix] : 'thumbnail';

$see_discount			= isset( $settings['see_discount' . $suffix] ) ? $settings['see_discount' . $suffix] : true;
$see_stock				= isset( $settings['see_stock' . $suffix] ) ? $settings['see_stock' . $suffix] : false;

$see_excerpt			= isset( $settings['see_excerpt' . $suffix] ) ? $settings['see_excerpt' . $suffix] : false;
$excerpt_length			= isset( $settings['excerpt_length' . $suffix] ) ? $settings['excerpt_length' . $suffix] : 10;
$see_content			= isset( $settings['see_content' . $suffix] ) ? $settings['see_content' . $suffix] : false;
$see_author				= isset( $settings['see_author' . $suffix] ) ? $settings['see_author' . $suffix] : false;
$see_price				= isset( $settings['see_price' . $suffix] ) ? $settings['see_price' . $suffix] : false;
$see_buy_button			= isset( $settings['see_buy_button' . $suffix] ) ? $settings['see_buy_button' . $suffix] : true;
$see_posted_on			= isset( $settings['see_posted_on' . $suffix] ) ? $settings['see_posted_on' . $suffix] : false;
$see_taxonomies			= isset( $settings['see_taxonomies' . $suffix] ) ? $settings['see_taxonomies' . $suffix] : false;
$see_meta_utilities		= isset( $settings['see_meta_utilities' . $suffix] ) ? $settings['see_meta_utilities' . $suffix] : false;
$disabled_order_types	= isset( $settings['disabled_order_types' . $suffix] ) ? $settings['disabled_order_types' . $suffix] : array();
$order_type				= isset( $settings['order_type' . $suffix] ) ? $settings['order_type' . $suffix] : 'date';
$order_desc				= isset( $settings['order_desc' . $suffix] ) ? $settings['order_desc' . $suffix] : 'asc';
$see_sorting_panel		= isset( $settings['see_sorting_panel' . $suffix] ) ? $settings['see_sorting_panel' . $suffix] : false;

$number_columns_xs		= isset( $settings['columns_xs' . $suffix] ) ? (int)$settings['columns_xs' . $suffix] : 1; //extra small devices (phones)
$number_columns_sm		= isset( $settings['columns_sm' . $suffix] ) ? (int)$settings['columns_sm' . $suffix] : 2; //small devices (tablets)
$number_columns			= isset( $settings['columns' . $suffix] ) ? (int)$settings['columns' . $suffix] : 3; //medium devices (desktop) md
$number_columns_lg		= isset( $settings['columns_lg' . $suffix] ) ? (int)$settings['columns_lg' . $suffix] : 4; //large devices (large desktops)

$see_pagination			= isset( $settings['see_pagination' . $suffix] ) ? $settings['see_pagination' . $suffix] : false;
$see_first_custom_area	= isset( $settings['see_first_custom_area' . $suffix] )  ? $settings['see_first_custom_area' . $suffix] : false;
$see_second_custom_area	= isset( $settings['see_second_custom_area' . $suffix] ) ? $settings['see_second_custom_area' . $suffix] : false;
$see_third_custom_area	= isset( $settings['see_third_custom_area' . $suffix] ) ? $settings['see_third_custom_area' . $suffix] : false;
$see_jetpack_sharing	= isset( $settings['see_jetpack_sharing' . $suffix] ) ? $settings['see_jetpack_sharing' . $suffix] : false; ?>
	<form method="post" action="">
	<div class="postbox">
	<div class="inside">
		<table class="form-table">
		<tbody>
		<tr valign="top">
			<th scope="row">
				<label for="current_post_type"><?php _e( 'Post type', 'tcp' ); ?></label>:
			</th>
			<td class="tcpf">
				<p>
					<?php $post_types = get_post_types( '', 'object' ); ?>
					<select id="current_post_type" name="current_post_type">
						<option value="" <?php selected( true, $current_post_type ); ?>><?php _e( 'Default', 'tcp'); ?></option>
						<?php foreach( $post_types as $i => $post_type ) : ?>
						<option value="<?php echo $i; ?>" <?php selected( $i, $current_post_type ); ?>
						<?php if ( isset( $settings[ 'title_tag-' . $i] ) ) : ?> style="font-weight: bold;"<?php endif; ?>
						>
						<?php echo $post_type->labels->singular_name; ?>
						<?php if ( isset( $settings[ 'title_tag-' . $i] ) ) : ?> *<?php endif; ?>
						</option>
						<?php endforeach; ?>
					</select>
					<input type="submit" name="load_post_type_settings" value="<?php _e( 'Load post type settings', 'tcp' ); ?>" class="button-secondary"/>
					<input type="submit" name="delete_post_type_settings" value="<?php _e( 'Delete post type settings', 'tcp' ); ?>" class="button-secondary"/>
				</p>
				<?php $config = '';
				foreach( $post_types as $i => $post_type ) {
					if ( isset( $settings[ 'title_tag-' . $i] ) ) {
						$line = '<li>' . $post_type->labels->singular_name;
						$line .= ': <a href="' . add_query_arg( 'current_post_type', $i ) . '">' . __( 'Load settings', 'tcp' ) . '</span></a>';
						$line .= '</li>';
						$config .= $line;
					}
				} ?>
				<?php if ( strlen( $config ) > 0 ) : ?>
				<div class="alert alert-warning">
					<?php  _e( 'There are specific settings for the next Post Types:', 'tcp' ); ?>
					<ul style="padding-left:10px;">
						<?php echo $config; ?>
						<li><a href="<?php echo remove_query_arg( 'current_post_type' ); ?>"><?php _e( 'Load default settings', 'tcp' ); ?></a></li>
					</ul>
				</div>
				<?php endif; ?>

				<p class="description"><?php _e( 'Allows to create different configuration for each Post Type. Products, posts or pages are custom post types.', 'tcp' ); ?></p>
				<span class="description"><?php _e( 'Options in bold have a specific configuration.', 'tcp' ); ?>
				<?php _e( 'Remember to save changes before to load another post type settings, or you\'ll loose the current values.', 'tcp' ); ?>
				</span>
			</td>
		</tr>
		</tbody>
		</table>
	</div><!-- .inside -->
	</div><!-- .postbox -->
	
	<h3><?php _e( 'Layout', 'tcp' ); ?></h3>

	<div class="postbox">
	<div class="inside">
		<table class="form-table">
		<tbody>
		<tr valign="top">
			<th scope="row">
				<label for="order_desc"><?php _e( 'Columns for Extra Small devices', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input id="columns_xs" name="columns_xs" value="<?php echo $number_columns_xs;?>" size="2" maxlength="2" type="text" />
				<span class="description"><?php _e( 'Phones', 'tcp' ); ?></span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="order_desc"><?php _e( 'Columns for Small Devices', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input id="columns_sm" name="columns_sm" value="<?php echo $number_columns_sm;?>" size="2" maxlength="2" type="text" />
				<span class="description"><?php _e( 'Tablets', 'tcp' ); ?></span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="order_desc"><?php _e( 'Columns for Medium Devices', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input id="columns" name="columns" value="<?php echo $number_columns;?>" size="2" maxlength="2" type="text" />
				<span class="description"><?php _e( 'Desktops', 'tcp' ); ?></span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="order_desc"><?php _e( 'Columns for Large Devices', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input id="columns_lg" name="columns_lg" value="<?php echo $number_columns_lg;?>" size="2" maxlength="2" type="text" />
				<span class="description"><?php _e( 'Large Desktops', 'tcp' ); ?></span>
			</td>
		</tr>
		</tbody>
		</table>
	</div><!-- .inside -->
	</div><!-- .postbox -->

	<?php submit_button( null, 'primary', 'save-loop-settings' ); ?>
	<h3><?php _e( 'Visibility', 'tcp' ); ?></h3>

	<div class="postbox">
	<div class="inside">
		<table class="form-table">
		<tbody>
			<tr valign="top">
			<th scope="row">
				<label for="see_image"><?php _e( 'Featured Image', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input type="checkbox" name="see_image" id="see_image" value="yes" <?php checked( $see_image, true ); ?> />
				<label for="see_title"><?php _e( 'Display Image', 'tcp' ); ?></label>,&nbsp;

				<label for="title_tag"><?php _e( 'using size', 'tcp' ); ?>:</label>
				<select id="image_size" name="image_size">
				<?php $imageSizes = get_intermediate_image_sizes();
				foreach( $imageSizes as $imageSize ) : ?>
					<option value="<?php echo $imageSize;?>" <?php selected( $imageSize, $image_size ); ?>><?php echo $imageSize; ?></option>
				<?php endforeach;?>
				</select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="see_title"><?php _e( 'Product Title', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input type="checkbox" name="see_title" id="see_title" value="yes" <?php checked( $see_title, true ); ?> />
				<label for="see_title"><?php _e( 'Display Title', 'tcp' ); ?></label>,&nbsp;

				<label for="title_tag"><?php _e( 'using tag', 'tcp' ); ?>:</label>
				<select id="title_tag" name="title_tag">
					<option value="" <?php selected( $title_tag, '' ); ?>><?php _e( 'No tag', 'tcp' ); ?></option>
					<option value="h2" <?php selected( $title_tag, 'h2' ); ?>>h2</option>
					<option value="h3" <?php selected( $title_tag, 'h3' ); ?>>h3</option>
					<option value="h4" <?php selected( $title_tag, 'h4' ); ?>>h4</option>
					<option value="h5" <?php selected( $title_tag, 'h5' ); ?>>h5</option>
					<option value="h6" <?php selected( $title_tag, 'h6' ); ?>>h6</option>
					<option value="p" <?php selected( $title_tag, 'p' ); ?>>p</option>
					<option value="div" <?php selected( $title_tag, 'div' ); ?>>div</option>
					<option value="span" <?php selected( $title_tag, 'span' ); ?>>span</option>
				</select>
				<p class="description"><?php _e( 'Allows to show or hide product titles in your catalogues, using custom html tag', 'tcp' ); ?></p>
			</td>
		</tr>
		
		<tr valign="top">
			<th scope="row">
				<label for="see_price"><?php _e( 'Display Price', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input type="checkbox" name="see_price" id="see_price" value="yes" <?php checked( $see_price, true ); ?> />
				<a href="<?php echo admin_url( 'admin.php?page=currency_settings' ); ?>"><?php _e( 'Configure Price format', 'tcp' ); ?></a>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="see_buy_button"><?php _e( 'Display Buy Button', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input type="checkbox" name="see_buy_button" id="see_buy_button" value="yes" <?php checked( $see_buy_button, true ); ?> />
				<a href="<?php echo admin_url( 'admin.php?page=thecartpress/TheCartPress.class.php/appearance' ); ?>"><?php _e( 'Configure Buy buttons', 'tcp' ); ?></a>
			</td>
		</tr>
		</tbody>
		</table>
	</div><!-- .inside -->
	</div><!-- .postbox -->

	<?php submit_button( null, 'primary', 'save-loop-settings' ); ?>

	<h3><?php _e( 'Advanced Visibility', 'tcp' ); ?></h3>

	<div class="postbox">
	<div class="inside">
		<table class="form-table">
		<tbody>
		<tr valign="top">
			<th scope="row">
				<label for="see_discount"><?php _e( 'Display Discount', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input type="checkbox" name="see_discount" id="see_discount" value="yes" <?php checked( $see_discount, true ); ?> />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="see_stock"><?php _e( 'Display Stock', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input type="checkbox" name="see_stock" id="see_stock" value="yes" <?php checked( $see_stock, true ); ?> />
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="see_excerpt"><?php _e( 'Display Excerpt', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input type="checkbox" name="see_excerpt" id="see_excerpt" value="yes" <?php checked( $see_excerpt, true ); ?> />
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="excerpt_length"><?php _e( 'Excerpt Length', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input type="numer" min="0" name="excerpt_length" id="excerpt_length" value="<?php echo $excerpt_length; ?>" maxlength="2" size="2" /><span class="description"><?php _e( 'words', 'tcp' ); ?></span>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="see_content"><?php _e( 'Display Content', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input type="checkbox" name="see_content" id="see_content" value="yes" <?php checked( $see_content, true ); ?> />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="see_author"><?php _e( 'Display Author', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input type="checkbox" name="see_author" id="see_author" value="yes" <?php checked( $see_author, true ); ?> />
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label for="see_posted_on"><?php _e( 'Display Posted On', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input type="checkbox" name="see_posted_on" id="see_posted_on" value="yes" <?php checked( $see_posted_on, true ); ?> />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="see_taxonomies"><?php _e( 'Display Taxonomies', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input type="checkbox" name="see_taxonomies" id="see_taxonomies" value="yes" <?php checked( $see_taxonomies, true ); ?> />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="see_meta_utilities"><?php _e( 'Display Meta Utilities', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input type="checkbox" name="see_meta_utilities" id="see_meta_utilities" value="yes" <?php checked( $see_meta_utilities, true ); ?> />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="see_first_custom_area"><?php _e( 'Display First Custom Area', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input type="checkbox" name="see_first_custom_area" id="see_first_custom_area" value="yes" <?php checked( $see_first_custom_area, true ); ?> />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="see_second_custom_area"><?php _e( 'Display Second Custom Area', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input type="checkbox" name="see_second_custom_area" id="see_second_custom_area" value="yes" <?php checked( $see_second_custom_area, true ); ?> />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="see_third_custom_area"><?php _e( 'Display Third Custom Area', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input type="checkbox" name="see_third_custom_area" id="see_third_custom_area" value="yes" <?php checked( $see_third_custom_area, true ); ?> />
			</td>
		</tr>
		<?php if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'sharedaddy' ) ) { ?>
		<?php //if ( method_exists( 'Jetpack', 'is_module_active' ) && Jetpack::is_module_active( 'sharedaddy' ) ) { ?>
		<tr valign="top">
			<th scope="row">
				<label for="see_jetpack_sharing"><?php _e( 'Display JetPack Sharing Area', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input type="checkbox" name="see_jetpack_sharing" id="see_jetpack_sharing" value="yes" <?php checked( $see_jetpack_sharing, true ); ?> />
			</td>
		</tr>
		<?php } ?>
		<?php do_action( 'tcp_loop_settings_page', $settings ); ?>
		</tbody>
		</table>
	</div><!-- .inside -->
	</div><!-- .postbox -->

	<?php submit_button( null, 'primary', 'save-loop-settings' ); ?>

	<h3><?php _e( 'Sorting & Pagination', 'tcp' ); ?></h3>

	<div class="postbox">
	<div class="inside">
		<table class="form-table">
		<tbody>
		<tr valign="top">
			<th scope="row">
				<label for="see_sorting_panel"><?php _e( 'Display Sorting Panel', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input type="checkbox" name="see_sorting_panel" id="see_sorting_panel" value="yes" <?php checked( $see_sorting_panel, true ); ?> />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="order_type_"><?php _e( 'Disabled order types', 'tcp' ); ?>:</label>
			</th>
			<td>
				<?php $sorting_fields = tcp_get_sorting_fields();
				foreach( $sorting_fields as $sorting_field ) : ?>
				<input type="checkbox" id="order_type_<?php echo $sorting_field['value']; ?>" name="disabled_order_types[]" value="<?php echo $sorting_field['value']; ?>" <?php tcp_checked_multiple( $disabled_order_types, $sorting_field['value'] ); ?>/> <?php echo $sorting_field['title']; ?><br/>
				<?php endforeach; ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="see_order_type"><?php _e( 'Order Type', 'tcp' ); ?>:</label>
			</th>
			<td>
				<?php $sorting_fields = tcp_get_sorting_fields(); ?>
				<select id="order_type" name="order_type">
				<?php foreach( $sorting_fields as $sorting_field ) :
					if ( ! in_array( $sorting_field['value'], $disabled_order_types ) ) : ?>
					<option value="<?php echo $sorting_field['value']; ?>" <?php selected( $order_type, $sorting_field['value'] ); ?>><?php echo $sorting_field['title']; ?></option>
					<?php endif;
				endforeach; ?>
				</select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="order_desc"><?php _e( 'Order Desc', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input type="checkbox" name="order_desc" id="order_desc" value="yes" <?php checked( $order_desc, 'desc' );?> />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="see_pagination"><?php _e( 'Display Pagination', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input type="checkbox" name="see_pagination" id="see_pagination" value="yes" <?php checked( $see_pagination, true ); ?> />
			</td>
		</tr>
		
		</tbody>
		</table>
	</div><!-- .inside -->
	</div><!-- .postbox -->
	<?php wp_nonce_field( 'tcp_loop_settings' ); ?>
	<?php submit_button( null, 'primary', 'save-loop-settings' ); ?>
	</form>
</div><!-- .wrap -->
<?php
	}

	function admin_action() {
		if ( empty( $_POST ) ) return;
		check_admin_referer( 'tcp_loop_settings' );
		if ( isset( $_POST['load_post_type_settings'] ) ) return;
		if ( strlen( $_POST['current_post_type'] ) > 0 ) {
			$suffix = '-' . $_POST['current_post_type'];
		} else {
			$suffix = '';
		}
		if ( isset( $_POST['delete_post_type_settings'] ) ) {
			if ( strlen( $suffix ) == 0 ) return;
			$settings = get_option( 'ttc_settings' );
			unset( $settings['see_title' . $suffix] );
			unset( $settings['title_tag' . $suffix] );
			unset( $settings['see_image' . $suffix] );
			unset( $settings['image_size' . $suffix] );
			unset( $settings['see_discount' . $suffix] );
			unset( $settings['see_stock' . $suffix] );
			unset( $settings['see_excerpt' . $suffix] );
			unset( $settings['excerpt_length' . $suffix] );
			unset( $settings['see_content' . $suffix] );
			unset( $settings['see_author' . $suffix] );
			unset( $settings['see_price' . $suffix] );
			unset( $settings['see_buy_button' . $suffix] );
			unset( $settings['see_posted_on' . $suffix] );
			unset( $settings['see_taxonomies' . $suffix] );
			unset( $settings['see_meta_utilities' . $suffix] );
			unset( $settings['disabled_order_types' . $suffix] );
			unset( $settings['order_type' . $suffix] );
			unset( $settings['order_desc' . $suffix] );
			unset( $settings['see_sorting_panel' . $suffix] );

			unset( $settings['columns_xs' . $suffix] );
			unset( $settings['columns_sm' . $suffix] );
			unset( $settings['columns' . $suffix] );//md
			unset( $settings['columns_lg' . $suffix] );//md

			unset( $settings['see_pagination' . $suffix] );
			unset( $settings['see_first_custom_area' . $suffix] );
			unset( $settings['see_second_custom_area' . $suffix] );
			unset( $settings['see_third_custom_area' . $suffix] );
			unset( $settings['see_jetpack_sharing' . $suffix] );
			
			$settings = apply_filters( 'tcp_loop_unset_settings_action', $settings, $suffix );
			update_option( 'ttc_settings', $settings );
			$this->updated = true;
			global $thecartpress;
			$thecartpress->load_settings();
			return;
		}
		$settings = get_option( 'ttc_settings' );
		$settings['see_title' . $suffix]				= isset( $_REQUEST['see_title'] ) ? $_REQUEST['see_title']  == 'yes' : false;
		$settings['title_tag' . $suffix]				= $_REQUEST['title_tag'];
		$settings['see_image' . $suffix]				= isset( $_REQUEST['see_image'] ) ? $_REQUEST['see_image'] == 'yes' : false;
		$settings['image_size' . $suffix]				= $_REQUEST['image_size'];
		$settings['see_discount' . $suffix]				= isset( $_REQUEST['see_discount'] ) ? $_REQUEST['see_discount'] == 'yes' : false;
		$settings['see_stock' . $suffix]				= isset( $_REQUEST['see_stock'] ) ? $_REQUEST['see_stock'] == 'yes' : false;
		$settings['see_excerpt' . $suffix]				= isset( $_REQUEST['see_excerpt'] ) ? $_REQUEST['see_excerpt'] == 'yes' : false;
		$settings['excerpt_length' . $suffix]			= isset( $_REQUEST['excerpt_length'] ) ? (int)$_REQUEST['excerpt_length'] : 20;
		$settings['see_content' . $suffix]				= isset( $_REQUEST['see_content'] ) ? $_REQUEST['see_content'] == 'yes' : false;
		$settings['see_author' . $suffix]				= isset( $_REQUEST['see_author'] ) ? $_REQUEST['see_author'] == 'yes' : false;
		$settings['see_price' . $suffix]				= isset( $_REQUEST['see_price'] ) ? $_REQUEST['see_price'] == 'yes' : false;
		$settings['see_buy_button' . $suffix]			= isset( $_REQUEST['see_buy_button'] ) ? $_REQUEST['see_buy_button']  == 'yes' : false;
		$settings['see_posted_on' . $suffix]			= isset( $_REQUEST['see_posted_on'] ) ? $_REQUEST['see_posted_on']  == 'yes' : false;
		$settings['see_taxonomies' . $suffix]			= isset( $_REQUEST['see_taxonomies'] ) ? $_REQUEST['see_taxonomies']  == 'yes' : false;
		$settings['see_meta_utilities' . $suffix]		= isset( $_REQUEST['see_meta_utilities'] ) ? $_REQUEST['see_meta_utilities']  == 'yes' : false;
		$settings['disabled_order_types' . $suffix] 	= isset( $_REQUEST['disabled_order_types'] ) ? $_REQUEST['disabled_order_types'] : array();
		$settings['order_type' . $suffix]				= $_REQUEST['order_type'];
		$settings['order_desc' . $suffix]				= isset( $_REQUEST['order_desc'] ) ? 'desc' : 'asc';
		$settings['see_sorting_panel' . $suffix]		= isset( $_REQUEST['see_sorting_panel'] ) ? $_REQUEST['see_sorting_panel'] == 'yes' : false;
		
		$settings['columns_xs' . $suffix]				= (int)$_REQUEST['columns_xs'];
		$settings['columns_sm' . $suffix]				= (int)$_REQUEST['columns_sm'];
		$settings['columns' . $suffix]					= (int)$_REQUEST['columns'];
		$settings['columns_lg' . $suffix]				= (int)$_REQUEST['columns_lg'];

		$settings['see_pagination' . $suffix]			= isset( $_REQUEST['see_pagination'] ) ? $_REQUEST['see_pagination']  == 'yes' : false;
		$settings['see_first_custom_area' . $suffix]	= isset( $_REQUEST['see_first_custom_area'] ) ? $_REQUEST['see_first_custom_area']  == 'yes' : false;
		$settings['see_second_custom_area' . $suffix]	= isset( $_REQUEST['see_second_custom_area'] ) ? $_REQUEST['see_second_custom_area']  == 'yes' : false;
		$settings['see_third_custom_area' . $suffix]	= isset( $_REQUEST['see_third_custom_area'] ) ? $_REQUEST['see_third_custom_area']  == 'yes' : false;
		$settings['see_jetpack_sharing' . $suffix]		= isset( $_REQUEST['see_jetpack_sharing'] ) ? $_REQUEST['see_jetpack_sharing']  == 'yes' : false;
		
		$settings = apply_filters( 'tcp_loop_settings_action', $settings, $suffix );
		update_option( 'ttc_settings', $settings );
		$this->updated = true;
	}
}

new TCPLoopSettings();
endif; // class_exists check