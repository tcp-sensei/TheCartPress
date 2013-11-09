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

?>
<div class="wrap">
<?php
$shortcodes_data = get_option( 'tcp_shortcodes_data' );
$shortcode_id = isset( $_REQUEST['shortcode_id'] ) ? $_REQUEST['shortcode_id'] : -1;

function tcp_exists_shortcode_id( $id ) {
	global $shortcodes_data;
	global $shortcode_id;

	foreach( $shortcodes_data as $i => $data )
		if ( $shortcode_id != $i && $data['id'] == $id )
			return true;
	return false;
}

/*function tcp_shortcode_sorting_fields( $sorting_fields ) {
	$sorting_fields[] = array( 'value' => 'rand', 'title' => __( 'Random', 'tcp' ) );
	return $sorting_fields;
}*/

if ( isset( $_REQUEST['tcp_shortcode_save'] ) ) {
	if ( ! isset( $_REQUEST['id'] ) || strlen( trim( $_REQUEST['id'] ) ) == 0 ) {?>
		<div id="message" class="error"><p>
			<?php _e( 'The field Identifier must be filled', 'tcp' ); ?>
		</p></div><?php
	} elseif ( tcp_exists_shortcode_id( $_REQUEST['id'] ) ) {?>
		<div id="message" class="error"><p>
			<?php _e( 'The field Identifier is repeated', 'tcp' ); ?>
		</p></div><?php
	} else {
		if ( ! $shortcodes_data ) $shortcodes_data = array();
		$shortcodes_data[$shortcode_id] = array (
			'id'					=> isset( $_REQUEST['id'] ) ? str_replace( ' ', '_', trim( $_REQUEST['id'] ) ) : 'id_' . $shortcode_id,
			'title'					=> '', //isset( $_REQUEST['title'] ) ? $_REQUEST['title'] : '',
			'desc'					=> isset( $_REQUEST['desc'] ) ? $_REQUEST['desc'] : '',
			'post_type'				=> isset( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : '',
			'use_taxonomy'			=> isset( $_REQUEST['use_taxonomy'] ),// ? $_REQUEST['use_taxonomy'] == 'yes' : false,
			'taxonomy'				=> isset( $_REQUEST['taxonomy'] ) ? $_REQUEST['taxonomy'] : '',
			'included'				=> isset( $_REQUEST['included'] ) ? $_REQUEST['included'] : array(),
			'term'					=> isset( $_REQUEST['term'] ) ? $_REQUEST['term'] : '',
			'limit'					=> isset( $_REQUEST['limit'] ) ? $_REQUEST['limit'] : '10',
			'see_pagination'		=> isset( $_REQUEST['see_pagination'] ),
			'see_order_panel'		=> isset( $_REQUEST['see_order_panel'] ),
			'loop'					=> isset( $_REQUEST['loop'] ) ? $_REQUEST['loop'] : 'default',
			'order_type'			=> isset( $_REQUEST['order_type'] ) ? $_REQUEST['order_type'] : 'date',
			'order_desc'			=> isset( $_REQUEST['order_desc'] ) ? 'desc' : 'asc',
			'columns'				=> isset( $_REQUEST['columns'] ) ? (int)$_REQUEST['columns'] : 2,

			'columns_xs'			=> isset( $_REQUEST['columns_xs'] ) ? (int)$_REQUEST['columns_xs'] : 2,
			'columns_sm'			=> isset( $_REQUEST['columns_sm'] ) ? (int)$_REQUEST['columns_sm'] : 4,
			'columns_lg'			=> isset( $_REQUEST['columns_lg'] ) ? (int)$_REQUEST['columns_lg'] : 6,

			'see_title'				=> isset( $_REQUEST['see_title'] ),
			'title_tag'				=> isset( $_REQUEST['title_tag'] ) ? $_REQUEST['title_tag'] : '',
			'see_image'				=> isset( $_REQUEST['see_image'] ),
			'image_size'			=> isset( $_REQUEST['image_size'] ) ? $_REQUEST['image_size'] : 'thumbnail',
			'see_content'			=> isset( $_REQUEST['see_content'] ),
			'see_stock'				=> isset( $_REQUEST['see_stock'] ),
			'see_discount'			=> isset( $_REQUEST['see_discount'] ),
			'see_excerpt'			=> isset( $_REQUEST['see_excerpt'] ),
			'excerpt_length'		=> isset( $_REQUEST['excerpt_length'] ) ? (int)$_REQUEST['excerpt_length'] : false,
			'see_author'			=> isset( $_REQUEST['see_author'] ),
			'see_posted_on'			=> isset( $_REQUEST['see_posted_on'] ),
			'see_taxonomies'		=> isset( $_REQUEST['see_taxonomies'] ),
			'see_meta_utilities'	=> isset( $_REQUEST['see_meta_utilities'] ),
			'see_price'				=> isset( $_REQUEST['see_price'] ),
			'see_buy_button'		=> isset( $_REQUEST['see_buy_button'] ),
			'see_first_custom_area' => isset( $_REQUEST['see_first_custom_area'] ),
			'see_second_custom_area'=> isset( $_REQUEST['see_second_custom_area'] ),
			'see_third_custom_area' => isset( $_REQUEST['see_third_custom_area'] ),
		);
		$shortcodes_data = apply_filters( 'tcp_shortcode_generator_settings_action', $shortcodes_data, $shortcode_id );
		update_option( 'tcp_shortcodes_data', $shortcodes_data ); ?>
		<div id="message" class="updated"><p>
			<?php _e( 'Shortcode saved', 'tcp' ); ?>
		</p></div><?php
	}
} elseif ( isset( $_REQUEST['tcp_shortcode_delete'] ) ) {
	unset( $shortcodes_data[$shortcode_id] );
	update_option( 'tcp_shortcodes_data', $shortcodes_data );
	$shortcode_id = -1; ?>
	<div id="message" class="updated"><p>
		<?php _e( 'Shortcode saved', 'tcp' ); ?>
	</p></div><?php
}

if ( $shortcode_id == -1 ) {
	if ( is_array( $shortcodes_data ) && count( $shortcodes_data ) > 0 ) {
		$keys = array_keys( $shortcodes_data );
		$shortcode_id = array_shift( $keys );
		$shortcode_data = $shortcodes_data[$shortcode_id];
	 } else {
		$shortcode_id = 0;
		$shortcode_data = array();
	 }
} elseif ( isset( $shortcodes_data[$shortcode_id] ) ) {
	$shortcode_data = $shortcodes_data[$shortcode_id];
} else {
	$shortcode_data = array();
}
$shortcode_href = TCP_ADMIN_PATH . 'ShortCodeGenerator.php&shortcode_id='; ?>

	<?php screen_icon( 'tcp-shortcode-generator' ); ?><h2><?php _e( 'ShortCode Generator', 'tcp' ); ?></h2>
	<ul class="subsubsub">
	</ul><!-- subsubsub -->
	<div class="clear"></div>
	<div class="instances">
	<?php if ( is_array( $shortcodes_data ) && count( $shortcodes_data ) > 0 ) :
		foreach( $shortcodes_data as $id => $data ) :
			if ( $shortcode_id == $id ) : ?>
			<span><?php echo $data['id']; ?></span>&nbsp;|&nbsp;
			<?php else: ?>
			<a href="<?php echo $shortcode_href, $id; ?>"><?php echo $data['id']; ?></a>&nbsp;|&nbsp;
			<?php endif; ?>
		<?php endforeach; ?>
		<?php if ( isset( $shortcodes_data[$shortcode_id] ) ) :
			$keys = array_keys( $shortcodes_data );
			$last_id = array_pop( $keys ) + 1; ?>
		<a href="<?php echo $shortcode_href, $last_id; ?>"><?php _e( 'new shortcode', 'tcp' ); ?></a>
		<?php endif; ?>
	<?php else: ?>
		<?php _e( 'No shortcodes, create one now:', 'tcp' ); ?>
	<?php endif; 
	$identifier				= isset( $shortcode_data['id'] ) ? $shortcode_data['id'] : '';
	$title					= ''; //isset( $shortcode_data['title'] ) ? $shortcode_data['title'] : '';
	$desc					= isset( $shortcode_data['desc'] ) ? $shortcode_data['desc'] : '';
	$post_type				= isset( $shortcode_data['post_type'] ) ? $shortcode_data['post_type'] : 'tcp_product';
	$taxonomy				= isset( $shortcode_data['taxonomy'] ) ? $shortcode_data['taxonomy'] : 'tcp_product_category';
	$use_taxonomy			= isset( $shortcode_data['use_taxonomy'] ) ? $shortcode_data['use_taxonomy'] == 'yes' : false;
	$included				= isset( $shortcode_data['included'] ) ? $shortcode_data['included'] : array();
	$term					= isset( $shortcode_data['term'] ) ? $shortcode_data['term'] : '';
	$limit					= isset( $shortcode_data['limit'] ) ? $shortcode_data['limit'] : 10;
	$see_pagination			= isset( $shortcode_data['see_pagination'] ) ? $shortcode_data['see_pagination'] : false;
	$see_order_panel		= isset( $shortcode_data['see_order_panel'] ) ? $shortcode_data['see_order_panel'] : false;
	$loop					= isset( $shortcode_data['loop'] ) ? $shortcode_data['loop'] : '';
	$columns				= isset( $shortcode_data['columns'] ) ? $shortcode_data['columns'] : 2;

	$columns_xs				= isset( $shortcode_data['columns_xs'] ) ? $shortcode_data['columns_xs'] : 2;
	$columns_sm				= isset( $shortcode_data['columns_sm'] ) ? $shortcode_data['columns_sm'] : 2;
	$columns_lg				= isset( $shortcode_data['columns_lg'] ) ? $shortcode_data['columns_lg'] : 2;

	$order_type				= isset( $shortcode_data['order_type'] ) ? $shortcode_data['order_type'] : 'date';
	$order_desc				= isset( $shortcode_data['order_desc'] ) ? $shortcode_data['order_desc'] : 'desc';
	$see_title				= isset( $shortcode_data['see_title'] ) ? $shortcode_data['see_title'] == 'yes' : true;
	$title_tag				= isset( $shortcode_data['title_tag'] ) ? $shortcode_data['title_tag'] : '';
	$see_image				= isset( $shortcode_data['see_image'] ) ? $shortcode_data['see_image'] == 'yes' : false;
	$image_size				= isset( $shortcode_data['image_size'] ) ? $shortcode_data['image_size'] : 'thumbnail';
	$see_content			= isset( $shortcode_data['see_content'] ) ? $shortcode_data['see_content'] == 'yes' : false;
	$see_stock				= isset( $shortcode_data['see_stock'] ) ? $shortcode_data['see_stock'] == 'yes' : false;
	$see_discount			= isset( $shortcode_data['see_discount'] ) ? $shortcode_data['see_discount'] == 'yes' : false;
	$see_excerpt			= isset( $shortcode_data['see_excerpt'] ) ? $shortcode_data['see_excerpt'] == 'yes' : false;
	$excerpt_length			= isset( $shortcode_data['excerpt_length'] ) ? $shortcode_data['excerpt_length'] : false;
	$see_author				= isset( $shortcode_data['see_author'] ) ? $shortcode_data['see_author'] == 'yes' : false;
	$see_posted_on			= isset( $shortcode_data['see_posted_on'] ) ? $shortcode_data['see_posted_on'] == 'yes' : false;
	$see_taxonomies			= isset( $shortcode_data['see_taxonomies'] ) ? $shortcode_data['see_taxonomies'] == 'yes' : false;
	$see_meta_utilities		= isset( $shortcode_data['see_meta_utilities'] ) ? $shortcode_data['see_meta_utilities'] == 'yes' : false;
	$see_price				= isset( $shortcode_data['see_price'] ) ? $shortcode_data['see_price'] == 'yes' : false;
	$see_buy_button			= isset( $shortcode_data['see_buy_button'] ) ? $shortcode_data['see_buy_button'] == 'yes' : false;
	$use_taxonomy 			= isset( $shortcode_data['use_taxonomy'] ) ? $shortcode_data['use_taxonomy'] == 'yes' : false;
	$see_first_custom_area	= isset( $shortcode_data['see_first_custom_area'] ) ? $shortcode_data['see_first_custom_area'] : false;
	$see_second_custom_area	= isset( $shortcode_data['see_second_custom_area'] ) ? $shortcode_data['see_second_custom_area'] : false;
	$see_third_custom_area	= isset( $shortcode_data['see_third_custom_area'] ) ? $shortcode_data['see_third_custom_area'] : false;
	if ( $use_taxonomy ) {
		$use_taxonomy_style = '';
		$included_style = 'display: none;';
	} else {
		$use_taxonomy_style = 'display: none;';
		$included_style = '';
	} ?>
	</div>
	<script>
		function tcp_show_taxonomy(checked) {
			if (checked) {
				jQuery('.tcp_taxonomy_controls').show();
				jQuery('.tcp_post_included').hide();
			} else {
				jQuery('.tcp_taxonomy_controls').hide();
				jQuery('.tcp_post_included').show();
			}
		}
	</script>
	<form method="post">
		<input type="hidden" name="shortcode_id" value="<?php echo $shortcode_id; ?>" />
		<table class="form-table">
		<tbody>
		<tr valign="top">
			<th scope="row">
				<label for="id"><?php _e( 'Identifier', 'tcp' ); ?>:</label>
				<br/><span class="description"><?php _e( 'Don\'t use whitespace. For example use the_identifier', 'tcp' ); ?></span>
			</th>
			<td>
				<input type="text" name="id" id="id" value="<?php echo $identifier; ?>" size="40" maxlength="255" />
				<br/><span><?php printf( __( 'Usage: [tcp_list id="%s"]', 'tcp' ), $identifier ); ?></span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="desc"><?php _e( 'Description', 'tcp' ); ?>:</label>
			</th>
			<td>
				<textarea name="desc" id="desc" cols="40" rows="6" maxlength="255" /><?php echo $desc; ?></textarea>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="post_type"><?php _e( 'Post type', 'tcp' ); ?>:</label>
			</th>
			<td>
				<select name="post_type" id="post_type">
				<?php foreach( get_post_types( array( 'show_in_nav_menus' => true ), object ) as $post_type_item ) : ?>
					<option value="<?php echo $post_type_item->name; ?>"<?php selected( $post_type, $post_type_item->name ); ?>><?php echo $post_type_item->labels->name; ?></option>
				<?php endforeach; ?>
				</select>
				<span class="description"><?php _e( 'Save to load the list of taxonomies', 'tcp' ); ?></span>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="use_taxonomy"><?php _e( 'Use Taxonomy', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input type="checkbox" class="checkbox" onclick="tcp_show_taxonomy(this.checked);" id="use_taxonomy" name="use_taxonomy" value="yes" <?php checked( $use_taxonomy, true ); ?> />
				<p class="description"><?php _e( 'If checked, you can select which Taxonomy (category) you want to display. If not checked you can select as many product as you want to show.', 'tcp' ); ?></p>
			</td>
		</tr>
		<tr valign="top" class="tcp_taxonomy_controls" style="<?php echo $use_taxonomy_style; ?>">
			<th scope="row">
				<label for="taxonomy"><?php _e( 'Taxonomy', 'tcp' ); ?>:</label>
			</th>
			<td>
				<?php if ( strlen( $post_type ) > 0 ) : ?>
				<select name="taxonomy" id="taxonomy">
				<option value="" <?php selected( $taxonomy, '' ); ?>><?php _e( 'all', 'tcp' ); ?></option>
				<?php $taxonomies = get_object_taxonomies( $post_type );
				if ( is_array( $taxonomies ) && count( $taxonomies ) > 0 ) foreach( $taxonomies as $taxonomy_item ) : $tax = get_taxonomy( $taxonomy_item ); ?>
					<option value="<?php echo esc_attr( $taxonomy_item ); ?>"<?php selected( $taxonomy, $taxonomy_item ); ?>><?php echo esc_attr( $tax->labels->name ); ?></option>
				<?php endforeach; ?>
				</select>
				<span class="description"><?php _e( 'Save to load the list of terms', 'tcp' ); ?></span>
				<?php endif; ?>
			</td>
		</tr>

		<tr valign="top" class="tcp_taxonomy_controls" style="<?php echo $use_taxonomy_style; ?>">
			<th scope="row">
				<label for="term"><?php _e( 'Term', 'tcp' )?>:</label>
			</th>
			<td>
				<select name="term" id="term">
				<?php if ( strlen( $taxonomy ) > 0 ) : 
					$terms = get_terms( $taxonomy, array( 'hide_empty' => false ) );
					if ( is_array( $terms ) && count( $terms ) )
						foreach( $terms as $term_item ) : 
							if ( $term_item->term_id == tcp_get_default_id( $term_item->term_id, $taxonomy ) ) :?>
								<option value="<?php echo $term_item->slug; ?>"<?php selected( $term, $term_item->slug ); ?>><?php echo esc_attr( $term_item->name ); ?></option>
							<?php endif;
						endforeach;
				endif; ?>
				</select>
			</td>
		</tr>
		<tr valign="top" class="tcp_post_included" style="<?php echo $included_style; ?>">
			<th scope="row">
				<label for="included"><?php _e( 'Included', 'tcp' )?>:</label>
			</th>
			<td>
				<?php $args = array(
					'post_type'			=> $post_type,
					'posts_per_page'	=> -1,
					'post_status'		=> 'publish',
					'fields'			=> 'ids',
				);
				if ( tcp_is_saleable_post_type( $post_type ) ) {
					$args['meta_query'][] = array(
						'key'		=> 'tcp_is_visible',
						'value'		=> 1,
						'compare'	=> '='
					);
				}
				$ids = get_posts( $args ); ?>
				<select name="included[]" id="included" multiple="true" size="8" style="height: auto">
					<option value="" <?php selected( $included, '' ); ?>><?php _e( 'all', 'tcp' ); ?></option>
				<?php if ( is_array( $ids ) && count( $ids ) > 0 ) foreach( $ids as $post_id ) : ?>
					<option value="<?php echo $post_id; ?>"<?php tcp_selected_multiple( $included, $post_id ); ?>><?php echo get_the_title( $post_id ); ?></option>
				<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="id"><?php _e( 'Limit', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input type="text" name="limit" id="limit" value="<?php echo $limit; ?>" size="3" maxlength="4" />
				<br/><span class="description"><?php _e( 'Set -1 to show all possible items.', 'tcp' ); ?></span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="id"><?php _e( 'Pagination', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input type="checkbox" name="see_pagination" id="see_pagination" value="yes" <?php checked( $see_pagination ); ?>/>
				<br/><span class="description"><?php _e( 'Allows to set pagination in the shortcode.', 'tcp' ); ?></span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="id"><?php _e( 'See Order Panel', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input type="checkbox" name="see_order_panel" id="see_order_panel" value="yes" <?php checked( $see_order_panel ); ?>/>
				<br/><span class="description"><?php _e( 'Allows to display an order panel in the shortcode.', 'tcp' ); ?></span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="loop"><?php _e( 'Loop', 'tcp' ); ?>:</label>
				<br/>(<?php _e( 'theme', 'tcp' ); ?>:&nbsp;<?php if ( function_exists('wp_get_theme') ) echo wp_get_theme(); else echo get_template(); ?>)
			</th>
			<td>
				<select name="loop" id="loop">
					<option value="" <?php selected( $loop, '' ); ?>><?php _e( 'default', 'tcp' ); ?></option>
				<?php $files = array();
				$folder = get_stylesheet_directory();

				if ( $handle = opendir( $folder ) ) :
					$folder = stripslashes( str_replace( '\\', "/", $folder ) );
					while ( false !== ( $file = readdir( $handle ) ) ) :
						if ( $file != '.' && $file != '..' && strpos( $file, 'loop' ) === 0 ) : ?>
							<option value="<?php echo $folder . '/' . $file; ?>" <?php selected( $loop, $folder . '/' . $file ); ?>><?php echo $file; ?></option>
						<?php $files[] = $file;
						endif;
					endwhile;
					closedir( $handle );
				endif;			
				$folder = get_template_directory();
				if ( STYLESHEETPATH != $folder )
					if ( $handle = opendir($folder ) ) :
						while ( false !== ( $file = readdir( $handle ) ) ) :
							if ( $file != '.' && $file != '..' && strpos( $file, 'loop' ) === 0 && ! in_array( $file, $files ) ) : ?>
								<option value="<?php echo $folder . '/' . $file; ?>" <?php selected( $loop, $folder . '/' . $file ); ?>>[<?php _e( 'parent', 'tcp' ); ?>] <?php echo $file; ?></option>
							<?php endif;
						endwhile;
					closedir( $handle );
				endif;
				if ( strlen( $loop ) > 0 && ! file_exists( $loop ) ) : ?>
					<option value="<?php echo $loop; ?>" selected="true"><?php printf( __( '"%s" is missing', 'tcp' ), basename( $loop ) ); ?></option>
				<?php endif; ?>
				</select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="order_type"><?php _e( 'Order by', 'tcp' ); ?></label>:
			</th>
			<td>
				<?php //add_filter( 'tcp_sorting_fields', 'tcp_shortcode_sorting_fields' );
				$sorting_fields = tcp_get_sorting_fields();
				//remove_filter( 'tcp_sorting_fields', 'tcp_shortcode_sorting_fields' ); ?>
				<select id="order_type" name="order_type">
				<?php foreach( $sorting_fields as $sorting_field ) : ?>
				<option value="<?php echo $sorting_field['value']; ?>" <?php selected( $order_type, $sorting_field['value'] ); ?>><?php echo $sorting_field['title']; ?></option>
				<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="order_desc"><?php _e( 'Order desc.', 'tcp' ); ?>:</label>
			</th>
			<td>
				<input type="checkbox" name="order_desc" id="order_desc" value="yes" <?php checked( $order_desc, 'desc' ); ?>/>
			</td>
		</tr>
		<?php do_action( 'tcp_shortcode_generator_form', $shortcode_data ); ?>
		</tbody>
		</table>
		<p>
			<input type="button" onclick="jQuery('#advanced').toggle();" value="<?php _e( 'show/hide advanced options', 'tcp' ); ?>" class="button-secondary" />
		</p>
	<div id="advanced" style="display:none;">
		<p>
			<label for="columns_xs"><?php _e( 'N<sup>o</sup> columns for Extra Small Devices (Phones)', 'tcp' ); ?>:</label>
			<input id="columns_xs" name="columns_xs" type="text" value="<?php echo $columns_xs; ?>" size="3" />
		</p>
		<p>
			<label for="columns_sm"><?php _e( 'N<sup>o</sup> columns for Small Devices (Tablets)', 'tcp' ); ?>:</label>
			<input id="columns_sm" name="columns_sm" type="text" value="<?php echo $columns_sm; ?>" size="3" />
		</p>
		<p>
			<label for="columns"><?php _e( 'N<sup>o</sup> columns for Medium Devices (Desktop)', 'tcp' ); ?>:</label>
			<input id="columns" name="columns" type="text" value="<?php echo $columns; ?>" size="3" />
		</p>
		<p>
			<label for="columns_lg"><?php _e( 'N<sup>o</sup> columns for Large Devices (Large Desktop)', 'tcp' ); ?>:</label>
			<input id="columns_lg" name="columns_lg" type="text" value="<?php echo $columns_lg; ?>" size="3" />
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="see_title" name="see_title" value="yes" <?php checked( $see_title ); ?> />
			<label for="see_title"><?php _e( 'Show title', 'tcp' ); ?></label>
		</p>
		<p>
			<label for="title_tag"><?php _e( 'Title tag', 'tcp' ); ?>:</label>
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
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="see_image" name="see_image" value="yes" <?php checked( $see_image ); ?> />
			<label for="see_image"><?php _e( 'Show image', 'tcp' ); ?></label>
		</p>
		<p>
			<label for="image_size"><?php _e( 'Image size', 'tcp' ); ?>:</label>
			<select id="image_size" name="image_size">
			<?php $imageSizes = get_intermediate_image_sizes();
			foreach( $imageSizes as $imageSize ) : ?>
				<option value="<?php echo $imageSize; ?>" <?php selected( $imageSize, $image_size ); ?>><?php echo $imageSize; ?></option>
			<?php endforeach; ?>
			?>
			</select>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="see_content" name="see_content" value="yes" <?php checked( $see_content ); ?> />
			<label for="see_content"><?php _e( 'Show content', 'tcp' ); ?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="see_stock" name="see_stock" value="yes" <?php checked( $see_stock ); ?> />
			<label for="see_stock"><?php _e( 'Show Stock', 'tcp' ); ?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="see_discount" name="see_discount" value="yes" <?php checked( $see_discount ); ?> />
			<label for="see_discount"><?php _e( 'Show Discount', 'tcp' ); ?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="see_excerpt" name="see_excerpt" value="yes" <?php checked( $see_excerpt ); ?> />
			<label for="see_excerpt"><?php _e( 'Show excerpt', 'tcp' ); ?></label>
		</p>
		<p>
			<label for="excerpt_length"><?php _e( 'Excerpt length', 'tcp' ); ?>: </label>
			<input type="number" class="input-mini" id="excerpt_length" name="excerpt_length" value="<?php echo $excerpt_length; ?>" maxlength="3" />
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="see_author" name="see_author" value="yes" <?php checked( $see_author ); ?> />
			<label for="see_author"><?php _e( 'Show about author', 'tcp' ); ?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="see_posted_on" name="see_posted_on" value="yes" <?php checked( $see_posted_on ); ?> />
			<label for="see_posted_on"><?php _e( 'Show posted on', 'tcp' ); ?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="see_taxonomies" name="see_taxonomies" value="yes" <?php checked( $see_taxonomies ); ?> />
			<label for="see_taxonomies"><?php _e( 'Show taxonomies', 'tcp' ); ?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="see_meta_utilities" name="see_meta_utilities" value="yes" <?php checked( $see_meta_utilities ); ?> />
			<label for="see_meta_utilities"><?php _e( 'Show utilities', 'tcp' ); ?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="see_price" name="see_price" value="yes" <?php checked( $see_price ); ?> />
			<label for="see_price"><?php _e( 'Show price', 'tcp' ); ?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="see_buy_button" name="see_buy_button" value="yes" <?php checked( $see_buy_button ); ?> />
			<label for="see_buy_button"><?php _e( 'Show buy button', 'tcp' ); ?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="see_first_custom_area" name="see_first_custom_area" value="yes" <?php checked( $see_first_custom_area ); ?> />
			<label for="see_first_custom_area"><?php _e( 'Show first custom area', 'tcp' ); ?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="see_second_custom_area" name="see_second_custom_area" value="yes" <?php checked( $see_second_custom_area ); ?> />
			<label for="see_second_custom_area"><?php _e( 'Show second custom area', 'tcp' ); ?></label>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="see_third_custom_area" name="see_third_custom_area" value="yes" <?php checked( $see_third_custom_area ); ?> />
			<label for="see_third_custom_area"><?php _e( 'Show third custom area', 'tcp' ); ?></label>
		</p>
	</div>		
		<p>
			<input name="tcp_shortcode_save" value="<?php _e( 'save', 'tcp' ); ?>" type="submit" class="button-primary" />
			<?php if ( isset( $shortcodes_data[$shortcode_id] ) ) : ?><input name="tcp_shortcode_delete" value="<?php _e( 'delete', 'tcp' ); ?>" type="submit" class="button-secondary" /><?php endif; ?>
		</p>
	</form>
</div>