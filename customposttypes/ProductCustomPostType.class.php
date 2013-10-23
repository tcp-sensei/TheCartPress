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

define( 'TCP_PRODUCT_POST_TYPE',	'tcp_product' );
define( 'TCP_PRODUCT_CATEGORY',		'tcp_product_category' );
define( 'TCP_PRODUCT_TAG',			'tcp_product_tag' );

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'ProductCustomPostType' ) ) {

/**
 * Defines the default post type 'tcp_product'
 * 
 * @since 1.0
 */
class ProductCustomPostType {

	function __construct() {
		add_action( 'admin_init', array( &$this, 'admin_init' ) );
		add_filter( 'tcp_custom_values_get_other_values', array( &$this, 'tcp_custom_values_get_other_values' ) );//Supports Custom Values Widget
	}

	function admin_init() {
		add_action( 'admin_head', array( &$this, 'plugin_header') );
		add_filter( 'post_row_actions', array( &$this, 'postRowActions' ) );
		add_action( 'manage_posts_custom_column', array( &$this, 'manage_posts_custom_column' ) );
		add_action( 'restrict_manage_posts', array( &$this, 'restrictManagePosts' ) );
		add_filter( 'parse_query', array( &$this, 'parse_query' ) );
		//for quick edit
		//add_action('quick_edit_custom_box', array( $this, 'quickEditCustomBox' ), 10, 2 );
	}

	function plugin_header() {
		global $post_type;
		if ( ( isset( $_GET['post_type'] ) && $_GET['post_type'] == TCP_PRODUCT_POST_TYPE ) || ( $post_type == TCP_PRODUCT_POST_TYPE ) ) { ?>
<style>
#icon-edit { background:transparent url('<?php echo plugins_url( '/images/tcp_icon_32.png', dirname( __FILE__ ) ); ?>') no-repeat; }
</style><?php
		}
	}

	static function create_default_custom_post_type_and_taxonomies() {
		if ( ! tcp_exist_custom_post_type( TCP_PRODUCT_POST_TYPE ) ) {
			$def = array(
				'name'					=> _x( 'Products', 'post type general name', 'tcp' ),
				'desc'					=> __( 'Default post type for TheCartPress'),
				'activate'				=> true,
				'singular_name'			=> _x( 'Product', 'post type singular name', 'tcp' ),
				'add_new'				=> _x( 'Add New', 'product', 'tcp' ),
				'add_new_item'			=> __( 'Add New', 'tcp' ),
				'edit_item'				=> __( 'Edit Product', 'tcp' ),
				'new_item'				=> __( 'New Product', 'tcp' ),
				'view_item'				=> __( 'View Product', 'tcp' ),
				'search_items'			=> __( 'Search Products', 'tcp' ),
				'not_found'				=> __( 'No products found', 'tcp' ),
				'not_found_in_trash'	=> __( 'No products found in Trash', 'tcp' ),
				'public'				=> true,
				'show_ui'				=> true,
				'show_in_menu'			=> true,
				'can_export'			=> true,
				'show_in_nav_menus'		=> true,
				'query_var'				=> true,
				'supports'				=> array( 'title', 'excerpt', 'editor', 'thumbnail', 'comments' ),
				'rewrite'				=> 'product',
				'has_archive'			=> 'product',
				'is_saleable'			=> true,
				'menu_icon'				=> plugins_url( '/images/tcp.png', dirname( __FILE__ ) ), // 16px16
			);
			tcp_create_custom_post_type( TCP_PRODUCT_POST_TYPE, $def );
		}
		if ( ! tcp_exist_custom_taxonomy( TCP_PRODUCT_CATEGORY ) ) {
			$taxonomy_def = array(
				'post_type'			=> TCP_PRODUCT_POST_TYPE,
				'name'				=> _x( 'Categories', 'taxonomy general name', 'tcp' ),
				'desc'				=> __( 'Categories for products', 'tcp' ),
				'activate'			=> true,
				'singular_name'		=> _x( 'Category', 'taxonomy singular name', 'tcp' ),
				'search_items'		=> __( 'Search Categories', 'tcp' ),
				'all_items'			=> __( 'All Categories', 'tcp' ),
				'parent_item'		=> __( 'Parent Category', 'tcp' ),
				'parent_item_colon'	=> __( 'Parent Category', 'tcp' ),
				'edit_item'			=> __( 'Edit Category', 'tcp' ), 
				'update_item'		=> __( 'Update Category', 'tcp' ),
				'add_new_item'		=> __( 'Add New Category', 'tcp' ),
				'new_item_name'		=> __( 'New Category Name', 'tcp' ),
				'hierarchical'		=> true,
				'query_var'			=> true, //'cat_prods',
				'label'				=> __( 'Category', 'tcp' ),
				'rewrite'			=> 'product_category',
			);
			tcp_create_custom_taxonomy( TCP_PRODUCT_CATEGORY, $taxonomy_def, array( TCP_PRODUCT_POST_TYPE ) );
		}
		if ( ! tcp_exist_custom_taxonomy( TCP_PRODUCT_TAG ) ) {
			$taxonomy_def = array(
				'post_type'			=> TCP_PRODUCT_POST_TYPE,
				'name'				=> __( 'Products Tags', 'tcp' ),
				'desc'				=> __( 'Tags for products', 'tcp'),
				'activate'			=> true,
				'singular_name'		=> __( 'Products', 'tcp' ),
				'search_items'		=> __( 'Search Tags', 'tcp' ),
				'all_items'			=> __( 'All Tags', 'tcp' ),
				'parent_item'		=> __( 'Parent Tag', 'tcp' ),
				'parent_item_colon'	=> __( 'Parent Tag', 'tcp' ),
				'edit_item'			=> __( 'Edit Tag', 'tcp' ), 
				'update_item'		=> __( 'Update Tag', 'tcp' ),
				'add_new_item'		=> __( 'Add New Tag', 'tcp' ),
				'new_item_name'		=> __( 'New Tag Name', 'tcp' ),
				'hierarchical'		=> false,
				'query_var'			=> true,
				'label'				=> __( 'Tag', 'tcp' ),
				'rewrite'			=> 'product_tag',
			);
			tcp_create_custom_taxonomy( TCP_PRODUCT_TAG, $taxonomy_def );
		}
		/*$taxonomy_def = array(
			'post_type'		=> TCP_PRODUCT_POST_TYPE,
			'name'			=> _x( 'Suppliers', 'taxonomy general name', 'tcp' ),
			'desc'			=> __( 'Suppliers for products', 'tcp'),
			'activate'			=> true,
			'singular_name'	=> _x( 'Supplier', 'taxonomy singular name', 'tcp' ),
			'search_items'	=> __( 'Search Suppliers', 'tcp' ),
			'all_items'		=> __( 'All Suppliers', 'tcp' ),
			'edit_item'		=> __( 'Edit Suppliers', 'tcp' ), 
			'update_item'	=> __( 'Update Suppliers', 'tcp' ),
			'add_new_item'	=> __( 'Add New Suppliers', 'tcp' ),
			'new_item_name'	=> __( 'New Suppliers Name', 'tcp' ),
			'hierarchical'	=> true,
			'query_var'		=> true,
			'rewrite'		=> 'product_supplier',
		);
		tcp_create_custom_taxonomy( TCP_SUPPLIER_TAG, $taxonomy_def );*/
	}

	//http://vocecommunications.com/blog/2010/11/adding-rewrite-rules-for-custom-post-types/
	/*static function register_post_type_archives( $post_type, $base_path = '' ) {
		global $wp_rewrite;
		$permalink_prefix = $base_path;
		$permalink_structure = '%year%/%monthnum%/%day%/%' . $post_type . '%/';
		//we use the WP_Rewrite class to generate all the endpoints WordPress can handle by default.
		$rewrite_rules = $wp_rewrite->generate_rewrite_rules( $permalink_prefix . '/' . $permalink_structure, EP_ALL, true, true, true, true, true );
		//build a rewrite rule from just the prefix to be the base url for the post type
		$rewrite_rules = array_merge( $wp_rewrite->generate_rewrite_rules( $permalink_prefix ), $rewrite_rules );
		$rewrite_rules[$permalink_prefix . '/?$'] = 'index.php?paged=1';
		foreach( $rewrite_rules as $regex => $redirect ) {
			if ( strpos( $redirect, 'attachment=' ) === false ) {
				$redirect .= '&post_type=' . $post_type; //add the post_type to the rewrite rule
			}
			//turn all of the $1, $2,... variables in the matching regex into $matches[] form
			if ( 0 < preg_match_all('@\$([0-9])@', $redirect, $matches ) ) {
				for( $i = 0; $i < count( $matches[0] ); $i++ ) {
					$redirect = str_replace( $matches[0][$i], '$matches[' . $matches[1][$i] . ']', $redirect );
				}
			}
			$wp_rewrite->add_rule( $regex, $redirect, 'top' ); //add the rewrite rule to wp_rewrite
		}
	}*/

	/*function quickEditCustomBox( $column_name, $post_type ) {
		if ( $post_type == TCP_PRODUCT_POST_TYPE ) {
			global $post; //TODO
			if ('price' == $column_name)
				echo 'price:', tcp_get_the_price( $post->ID );
		}
	}*/

	function postRowActions( $actions, $post_line = null ) {
		global $post;
		if ( ! tcp_is_saleable_post_type( $post->post_type ) ) return $actions;
		$actions = apply_filters( 'tcp_product_row_actions', $actions, $post );
		return $actions;
	}

	/**
	 * Custom definition for the products list
	 */
	function custom_columns_definition( $columns ) {
		$columns = array(
			'cb'			=> '<input type="checkbox" />',
			'thumbnail'		=> __( 'Thumbnail', 'tcp' ),
			'title'			=> __( 'Name', 'tcp' ),
			'sku'			=> __( 'SKU', 'tcp' ),
			'price'			=> __( 'Price - Type', 'tcp' ),
			//'date'			=> __( 'Date', 'tcp' ),
			//'comments'	=> __('Comments', 'tcp' ),
		);
		global $thecartpress;
		$show_back_end_label = isset( $thecartpress->settings['show_back_end_label'] ) ? $thecartpress->settings['show_back_end_label'] : false;
		if ( ! $show_back_end_label ) unset( $columns['label'] );
		return apply_filters( 'tcp_custom_columns_definition', $columns );
	}

	/**
	 * Prints the custom fields values in the products list
	 */
	function manage_posts_custom_column( $column_name ) {
		global $post;
		if ( tcp_is_saleable_post_type( $post->post_type ) ) {
			if ( 'ID' == $column_name ) {
				echo $post->ID;
			} elseif ( 'thumbnail' == $column_name ) {
				$image = tcp_get_the_thumbnail( $post->ID, 0, 0, array( '50', '50' )  );
				if ( $image == '' ) {
					//$image = '<a href="' . get_admin_url() . '/media-upload.php?post_id=' . $post->ID . '&type=image&TB_iframe=1" class="thickbox" title="' . __( 'Set featured image' ) . '">';
					$image .= '<img src="' . plugins_url( 'images/tcp_icon_gray.png', dirname( __FILE__ ) ) .'" />';
					//$image .= '</a>';
				}
				echo '&nbsp;', $image;
			} elseif ( 'sku' == $column_name ) {
				$sku = tcp_get_the_sku( $post->ID );
				if ( strlen( trim( $sku ) ) == 0 ) $sku = __( 'N/A', 'tcp' );
				echo $sku;
			} elseif ( 'price' == $column_name ) {
				$price = tcp_get_the_price( $post->ID );
				//$price = $post->tcp_price;
				if ( $price > 0 ) echo '<strong>', tcp_format_the_price( $price ), '</strong>';
				$product_type = tcp_get_the_product_type( $post->ID );
				$types = tcp_get_product_types();
				if ( isset( $types[$product_type] ) && $product_type != 'SIMPLE' ) echo '<br/>', $types[$product_type]['label'];
			}
			do_action( 'tcp_manage_posts_custom_column', $column_name, $post );
		}
	}

	/**
	 * Print filtering fields in the products list
	 */
	function restrictManagePosts() {
		global $typenow;
		if ( tcp_is_saleable_post_type( $typenow ) ) {
			global $wp_query;
			wp_dropdown_categories( array(
				'show_option_all'	=> __( 'View all categories', 'tcp' ),
				'taxonomy'			=> TCP_PRODUCT_CATEGORY,
				'name'				=> 'tcp_product_cat',
				'orderby'			=> 'name',
				'selected'			=> isset( $wp_query->query['term'] ) ? $wp_query->query['term'] : '',
				'hierarchical'		=> true,
				'depth'				=> 3,
				'show_count'		=> true,
				'hide_empty'		=> true,
			) );?>
			<label for="tcp_product_type"><?php _e( 'type:', 'tcp' );?></label>
			<?php $product_types = tcp_get_product_types( true, __( 'all', 'tcp' ) );
			$product_type = isset( $_REQUEST['tcp_product_type'] ) ? $_REQUEST['tcp_product_type'] : ''; ?>
			<select name="tcp_product_type" id="tcp_product_type">
			<?php foreach( $product_types as $id => $item ) : ?>
				<option value="<?php echo $id; ?>" <?php selected( $id, $product_type ); ?>><?php echo $item['label']; ?></option>
			<?php endforeach; ?>
			</select>
			<label for="tcp_SKU"><?php _e( 'SKU:', 'tcp' );?></label>
			<input type="text" size="10" name="tcp_sku" value="<?php echo isset( $_REQUEST['tcp_sku'] ) ? $_REQUEST['tcp_sku'] : ''; ?>" />
			<?php
		}
	}

	/**
	 * This function is executed before the admin product list query. WP 3.1
	 */
	function parse_query( $query ) {
		if ( ! is_admin() ) return $query;
		if ( isset( $_REQUEST['tcp_product_cat'] ) && $_REQUEST['tcp_product_cat'] > 0 ) {
			$query->query_vars['tax_query'] = array(
				array(
					'taxonomy'	=> TCP_PRODUCT_CATEGORY,
					'terms'		=> array( $_REQUEST['tcp_product_cat'] ),
					'field'		=> 'id',
				),
			);
		}
		if ( isset( $_REQUEST['tcp_product_type'] ) && $_REQUEST['tcp_product_type'] != '' ) {
			$query->query_vars['meta_query'] = array(
				array(
					'key' => 'tcp_type',
					'value' => $_REQUEST['tcp_product_type'],
					'compare' => '=',
					'type' => 'string',
				),
			);
		}
		if ( isset( $_REQUEST['tcp_sku'] ) && $_REQUEST['tcp_sku'] != '' ) {
			$query->query_vars['meta_query'] = array(
				array(
					'key' => 'tcp_sku',
					'value' => $_REQUEST['tcp_sku'],
					'compare' => '=',
					'type' => 'string',
				),
			);
		}
		if ( isset( $query->query_vars['post_type'] ) && $query->query_vars['post_type'] == TCP_PRODUCT_POST_TYPE ) {
			global $pagenow;
			if ( $pagenow == 'edit.php' ) {
				global $thecartpress;
				$hide_invisible = isset( $thecartpress->settings['hide_visibles'] ) ? (bool)$thecartpress->settings['hide_visibles'] : true;
				if ( $hide_invisible ) {
					$query->query_vars['meta_query'][] = array(
						'key'		=> 'tcp_is_visible',
						'value'		=> 1,
						'compare'	=> '=',
						'type'		=> 'numeric',
					);
				}
			}
		}
		return $query;
	}
	
	function sortable_columns( $columns ) {
		$columns['price'] = 'tcp_price';
		return $columns;
	}

	function price_column_orderby( $vars ) {
		if ( isset( $vars['orderby'] ) && 'tcp_price' == $vars['orderby'] ) {
			$vars = array_merge( $vars, array(
				'orderby' => 'meta_value_num',
				'meta_key' => 'tcp_price',
			) );
		}
		return $vars;
	}

	function tcp_custom_values_get_other_values( $other_values ) {
		$other_values['tcp_price'] = array(
			'label' => __( 'Price', 'tcp' ),
			'callback' => 'tcp_get_the_price_label',
		);
		$other_values['tcp_sku'] = array(
			'label' => __( 'SKU', 'tcp' ),
			'callback' => 'tcp_get_the_SKU',
		);
		$other_values['tcp_weight'] = array(
			'label' => __( 'Weight', 'tcp' ),
			'callback' => 'tcp_get_the_weight_label',
		);
		
		return $other_values;
	}
}

$GLOBALS['productcustomposttype'] = new ProductCustomPostType();

} // class_exists check