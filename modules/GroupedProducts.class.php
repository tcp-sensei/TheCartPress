<?php
/**
 * Grouped Products
 *
 * Adds this new products types. Grouped alows to add products as child of other products.
 *
 * @package TheCartPress
 * @subpackage Modules
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

if ( ! class_exists( 'TCPGroupedProducts' ) ) :

require_once( TCP_DAOS_FOLDER . 'RelEntities.class.php' );

class TCPGroupedProducts {

	function __construct() {
		if ( is_admin() ) {
			add_filter( 'tcp_get_product_types'			, array( $this, 'tcp_get_product_types' ) );
			add_filter( 'tcp_product_row_actions'		, array( $this, 'tcp_product_row_actions' ), 10, 2 );
			add_filter( 'tcp_custom_columns_definition'	, array( $this, 'tcp_custom_columns_definition' ) );
			add_filter( 'tcp_product_custom_fields_links', array( $this, 'tcp_product_custom_fields_links' ), 10, 3 );
			//add_action( 'tcp_product_metabox_toolbar'	, array( $this, 'tcp_product_metabox_toolbar' ) );

			add_action( 'tcp_manage_posts_custom_column', array( $this, 'tcp_manage_posts_custom_column' ), 10, 2 );
			add_action( 'tcp_hide_product_fields'		, array( $this, 'tcp_hide_product_fields' ) );
		} else {
			add_filter( 'tcp_get_the_price_label'		, array( $this, 'tcp_get_the_price_label' ) , 10, 2 );
			add_filter( 'tcp_the_add_to_cart_button'	, array( $this, 'tcp_the_add_to_cart_button' ), 10, 2 );
			add_filter( 'post_type_link'				, array( $this, 'post_type_link' ), 10, 4 );
		}
	}

	function tcp_get_product_types( $types ) {
		$types['GROUPED'] = array( 
			'label' => __( 'Grouped', 'tcp' )
		);
		return $types;
	}

	function tcp_product_row_actions( $actions, $post ) {
		if ( tcp_get_the_product_type( $post->ID ) == 'GROUPED' ) {
			$count = RelEntities::count( $post->ID );
			$count = $count > 0 ? ' (' . $count . ')' : '';
			$actions['tcp_assigned'] = '<a href="' . TCP_ADMIN_PATH . 'AssignedProductsList.php&post_id=' . $post->ID . '&rel_type=GROUPED" title="' . esc_attr( __( 'See assigned products', 'tcp' ) ) . '">' . __( 'assigned products', 'tcp' ) . $count . '</a>';
		}
		return $actions;
	}

	function tcp_custom_columns_definition( $columns ) {
		$new_column = array( 'grouped_in' => __( 'Grouped in', 'tcp' ) );
		$columns = array_merge( array_slice( $columns, 0, 3 ), $new_column, array_slice( $columns, 3 ) );
		return $columns;
	}

	function tcp_manage_posts_custom_column( $column_name, $post ) {
		if ( 'grouped_in' == $column_name ) {
			$post_ids = tcp_get_the_parents( $post->ID );
			$titles = '&nbsp;';
			if ( is_array( $post_ids ) && count( $post_ids ) > 0 ) {
				foreach( $post_ids as $post_id ) {
					$titles .= '<a href="post.php?action=edit&post=' . $post_id->id_from . '">' . get_the_title( $post_id->id_from ) . '</a>&nbsp;';
				}
			}
			echo $titles;
		}
	}

	function tcp_product_custom_fields_links( $links, $post_id, $post ) {
		$product_type = tcp_get_the_product_type( $post_id );
		if ( 'GROUPED' != $product_type ) {
			$parents = RelEntities::getParents( $post_id );
			if ( is_array( $parents ) && count( $parents ) > 0 ) {
				$parent = $parents[0]->id_from;
				$links[] = array(
					'url'	=> TCP_ADMIN_PATH . 'AssignedProductsList.php&post_id=' . $parent . '&rel_type=GROUPED',
					'title'	=> '',
					'label'	=> __( 'Grouped product', 'tcp' )
				);
			}
		} elseif ( 'GROUPED' == $product_type ) {
			$count = RelEntities::count( $post_id );
			$count = $count > 0 ? ' (' . $count . ')' : '';
			$links[] = array(
				'url'	=> TCP_ADMIN_PATH . 'AssignedProductsList.php&post_id=' . $post_id . '&rel_type=GROUPED',
				'title'	=> '',
				'label'	=> __( 'Grouped products', 'tcp' ) . $count
			);
		}
		return $links;
	}

	/*function tcp_product_metabox_toolbar( $post_id ) {
		$product_type = tcp_get_the_product_type( $post_id );
		if ( 'SIMPLE' == $product_type ) :
			$parents = RelEntities::getParents( $post_id );
			if ( is_array( $parents ) && count( $parents ) > 0 ) :
				$parent = $parents[0]->id_from; ?>
				<li>|</li>
				<li><a href="<?php echo TCP_ADMIN_PATH;?>AssignedProductsList.php&post_id=<?php echo $parent;?>&rel_type=GROUPED"><?php _e( 'parent\'s assigned products', 'tcp' );?></a></li>
			<?php endif;
		/*elseif ( 'GROUPED' == $product_type ) :
			$count = RelEntities::count( $post_id );
			$count = $count > 0 ? ' (' . $count . ')' : ''; ?>
			<li><a href="<?php echo TCP_ADMIN_PATH;?>AssignedProductsList.php&post_id=<?php echo $post_id;?>&rel_type=GROUPED"><?php _e( 'grouped products', 'tcp' );?><?php echo $count;?></a></li>
			<li>|</li>
			<li><a href="post-new.php?post_type=<?php echo TCP_PRODUCT_POST_TYPE;?>&tcp_product_parent_id=<?php echo $post_id;?>&rel_type=GROUPED"><?php _e( 'create new grouped product', 'tcp' );?></a></li>
		<?php */
		/*endif;
	}*/

	function tcp_hide_product_fields() { ?>
		if ('GROUPED' == product_type) {
			jQuery('#tcp_price').parent().parent().fadeOut(speed);
			jQuery('#tcp_tax_id').parent().parent().fadeOut(speed);
			jQuery('#tcp_initial_units').parent().parent().fadeOut(speed);
			jQuery('#tcp_weight').parent().parent().fadeOut(speed);
			jQuery('#tcp_exclude_range').parent().parent().fadeOut(speed);
			jQuery('#tcp_attribute_sets').parent().parent().fadeOut(speed);
			jQuery('#tcp_is_downloadable').parent().parent().fadeOut(speed);
		}<?php
	}

	function tcp_get_the_price_label( $label, $post_id ) {
		if ( 'GROUPED' == tcp_get_the_product_type( $post_id ) ) {
			$min_max = tcp_get_min_max_price( $post_id );
			if ( is_array( $min_max ) ) {
				$min = $min_max[0];
				$max = $min_max[1];
				if ( $min != $max ) {
					//$label = sprintf( _x( '%s to %s', 'min_price to max_price', 'tcp' ), tcp_format_the_price( $min ), tcp_format_the_price( $max ) );
					$label = sprintf( __( 'From %s', 'tcp' ), tcp_format_the_price( $min ) );
				} else {
					$label = tcp_format_the_price( $min );
				}
			} else {
				$label = '';
			}
		}
		return $label;
	}

	function tcp_the_add_to_cart_button( $out, $post_id ) {
		if ( 'GROUPED' == tcp_get_the_product_type( $post_id ) ) {
			ob_start(); ?>
			<!--<input type="hidden" name="tcp_post_id[]" id="tcp_post_id_<?php echo $post_id; ?>" value="<?php echo $post_id; ?>" />-->
			<button type="submit" name="tcp_add_to_shopping_cart" class="tcp_add_to_shopping_cart tcp_add_to_shopping_cart_<?php echo tcp_get_the_product_type( $post_id ); ?> <?php echo tcp_get_buy_button_color(); ?> <?php echo tcp_get_buy_button_size(); ?>" id="tcp_add_product_<?php echo $post_id; ?>"><?php _e( 'Add selected to cart', 'tcp' ); ?></button>
			<?php return ob_get_clean();
		} else {
			return $out;
		}
	}

	//if a non visible product is displayed, its link will go to the parent
	function post_type_link( $post_link, $post, $leavename, $sample ) {
		if ( ! tcp_is_visible( $post->ID ) ) {
			$parent_id = tcp_get_the_parent( $post->ID );
			if ( $parent_id > 0 ) return get_permalink( $parent_id );
		}
		return $post_link;
	}
}

new TCPGroupedProducts();
endif; // class_exists check