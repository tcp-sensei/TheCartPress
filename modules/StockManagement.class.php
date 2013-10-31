<?php
/**
 * Stock Management
 *
 * Allows to add stock management into TheCartPress
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
if ( !defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'TCPStockManagement' ) ) {

class TCPStockManagement {
	private $no_stock_enough = false;

	function __construct() {
		add_action( 'tcp_init'		, array( $this, 'tcp_init' ), 1 );
		add_action( 'admin_init'	, array( $this, 'admin_init' ) );
	}

	function tcp_init( $thecartpress ) {
		$stock_management = $thecartpress->get_setting( 'stock_management', false );
		if ( $stock_management ) {
			add_action( 'admin_init'									, array( $this, 'add_template_class' ) );

			add_action( 'tcp_shopping_cart_widget_form'					, array( $this, 'tcp_shopping_cart_widget_form'), 10, 2 );
			add_filter( 'tcp_shopping_cart_widget_update'				, array( $this, 'tcp_shopping_cart_widget_update') , 10, 2 );
			add_action( 'tcp_shopping_cart_widget_units'				, array( $this, 'tcp_shopping_cart_widget_units' ), 10, 2 );

			add_filter( 'tcp_cart_units'								, array( $this, 'tcp_cart_units' ), 10, 2 );

			add_action( 'tcp_shopping_cart_summary_widget_form'			, array( $this, 'tcp_shopping_cart_summary_widget_form' ), 10, 2 );
			add_filter( 'tcp_shopping_cart_summary_widget_update'		, array( $this, 'tcp_shopping_cart_summary_widget_update' ) , 10, 2 );
			add_filter( 'tcp_get_shopping_cart_summary'					, array( $this, 'tcp_get_shopping_cart_summary' ), 10, 2 );
			add_action( 'tcp_show_shopping_cart_summary_widget_params'	, array( $this, 'tcp_show_shopping_cart_summary_widget_params' ) );

			//add_filter( 'tcp_the_add_to_cart_unit_field'				, array( $this, 'tcp_the_add_to_cart_unit_field' ), 10, 2 );

			add_filter( 'tcp_the_add_to_cart_button'					, array( $this, 'tcp_the_add_to_cart_button' ), 10, 2 );

			add_filter( 'tcp_apply_filters_for_saleables'				, array( $this, 'tcp_apply_filters_for_saleables' ) );

			add_filter( 'tcp_checkout_manager'							, array( $this, 'tcp_checkout_manager' ) );
			add_action( 'tcp_checkout_create_order_insert_detail'		, array( $this, 'tcp_checkout_create_order_insert_detail' ), 10, 4 );
			add_action( 'tcp_checkout_ok'								, array( $this, 'tcp_checkout_ok' ) );
			add_action( 'tcp_completed_ok_stockadjust'					, array( $this, 'tcp_completed_ok_stockadjust' ) );

			add_action( 'tcp_admin_menu'								, array( $this, 'tcp_admin_menu' ) );

			add_filter( 'tcp_get_dynamic_options'						, array( $this, 'tcp_get_dynamic_options' ), 10, 2 );
			add_filter( 'tcp_custom_list_widget_args'					, array( $this, 'tcp_custom_list_widget_args' ) );
			add_filter( 'tcp_custom_values_get_other_values'			, array( $this, 'tcp_custom_values_get_other_values' ) );
		}
	}

	function admin_init() {
		add_action( 'tcp_main_settings_after_page'	, array( $this, 'tcp_main_settings_after_page' ) );
		add_filter( 'tcp_main_settings_action'		, array( $this, 'tcp_main_settings_action' ) );
		global $thecartpress;
		if ( ! empty( $thecartpress ) ) {
			$stock_management = $thecartpress->get_setting( 'stock_management', false );
			if ( $stock_management ) {
				if ( is_admin() ) {
					// Adding Stock managemet to Products (to saleable post types)
					//add_action( 'tcp_product_metabox_custom_fields'						, array( $this, 'tcp_product_metabox_custom_fields' ) );//Old Impementation, before tabs (1.3.2)
					add_filter( 'tcp_product_custom_fields_tabs'						, array( $this, 'tcp_product_custom_fields_tabs' ), 9 );//since 1.3.2
					add_action( 'tcp_product_metabox_custom_tabs'						, array( $this, 'tcp_product_metabox_custom_tabs' ) );
					add_action( 'tcp_product_metabox_save_custom_fields'				, array( $this, 'tcp_product_metabox_save_custom_fields' ) );
					add_action( 'tcp_product_metabox_delete_custom_fields'				, array( $this, 'tcp_product_metabox_delete_custom_fields' ) );

					add_action( 'tcp_options_metabox_custom_fields'						, array( $this, 'tcp_product_metabox_custom_fields' ) );
					add_action( 'tcp_options_metabox_save_custom_fields'				, array( $this, 'tcp_product_metabox_save_custom_fields' ) );
					add_action( 'tcp_options_metabox_delete_custom_fields'				, array( $this, 'tcp_product_metabox_delete_custom_fields' ) );

					add_action( 'tcp_product_metabox_save_custom_fields_translations'	, array( $this, 'tcp_product_metabox_save_custom_fields_translations' ), 10, 2 );

					// Adding Stock managemet to Dynamic Options
					add_action( 'tcp_dynamic_options_metabox_column_headers'			, array( $this, 'tcp_dynamic_options_metabox_column_headers' ) );
					add_action( 'tcp_dynamic_options_metabox_value_rows'				, array( $this, 'tcp_dynamic_options_metabox_value_rows' ) );
					add_action( 'tcp_dynamic_options_lists_header_new'					, array( $this, 'tcp_dynamic_options_lists_header_new' ) );
					add_action( 'tcp_dynamic_options_lists_row_new'						, array( $this, 'tcp_dynamic_options_lists_row_new' ) );
					add_action( 'tcp_dynamic_options_lists_header'						, array( $this, 'tcp_dynamic_options_lists_header' ) );
					add_action( 'tcp_dynamic_options_lists_row'							, array( $this, 'tcp_dynamic_options_lists_row' ) );
					add_filter( 'tcp_dynamic_options_option_to_save'					, array( $this, 'tcp_dynamic_options_option_to_save' ), 10, 3 );
					add_action( 'tcp_update_option'										, array( $this, 'tcp_update_option' ), 10, 2 );
					add_action( 'tcp_insert_option'										, array( $this, 'tcp_update_option' ), 10, 2 );
					add_action( 'tcp_delete_option'										, array( $this, 'tcp_delete_option' ) );

					//Adding Stock data to the products table
					add_filter( 'tcp_custom_columns_definition'							, array( $this, 'tcp_custom_columns_definition' ) );
					add_action( 'tcp_manage_posts_custom_column'						, array( $this, 'tcp_manage_posts_custom_column' ), 10, 2 );

					add_action( 'tcp_create_option'										, array( $this, 'tcp_create_option' ), 10, 2 );

					//Stock management when orders change
					add_filter( 'tcp_order_edit_before'									, array( $this, 'tcp_order_edit_before' ), 10, 5 );
					add_filter( 'tcp_order_edit_status_before'							, array( $this, 'tcp_order_edit_status_before' ), 10, 4 );
					add_filter( 'tcp_order_quick_edit_before'							, array( $this, 'tcp_order_quick_edit_before' ), 10, 3 );
							
					$saleable_post_types = tcp_get_saleable_post_types();
					foreach( $saleable_post_types  as $post_type )
						add_filter( 'manage_edit-' . $post_type . '_sortable_columns'	, array( $this, 'stock_column_sortable_column' ) );
					add_filter( 'request', array( &$this, 'stock_column_orderby' ) );
				}
			}
		}
	}

	function tcp_dynamic_options_metabox_column_headers( $parent ) { ?>
		<th scope="col" class="manage-column">
			<?php _e( 'Stock', 'tcp' ); ?>
		</th><?php
	}

	function tcp_dynamic_options_metabox_value_rows( $post_id ) { ?>
		<td class="tcp_do_stock">
			<?php echo tcp_get_the_stock( $post_id ); ?>
		</td><?php

	}

	function tcp_dynamic_options_lists_header_new( $parent ) { ?>
		<th class="tcp_do_stock"><?php _e( 'Stock', 'tcp' ); ?></th><?php
	}

	function tcp_dynamic_options_lists_row_new( $parent ) { ?>
		<td class="tcp_do_stock">
			<input type="number" min="-1" step="any" name="tcp_stock[]" class="tcp_stock" size="5" maxlength="9" />
		</td><?php
	}

	function tcp_dynamic_options_lists_header( $parent ) { ?>
		<th class="tcp_do_stock"><?php _e( 'Stock', 'tcp' ); ?></th><?php
	}

	function tcp_dynamic_options_lists_row( $post_id ) { ?>
		<td class="tcp_do_stock">
			<input type="number" min="-1" step="any" name="tcp_stock[]" class="tcp_stock" size="5" maxlength="9" value="<?php echo tcp_get_the_stock( $post_id ); ?>" />
		</td><?php
	}

	function tcp_dynamic_options_option_to_save( $option, $id, $data ) {
		if ( isset( $data['tcp_stock'][$id] ) ) $option['stock'] = sanitize_text_field( $data['tcp_stock'][$id] );
		else $option['stock'] = -1;
		return $option;
	}

	function tcp_update_option( $post_id, $args ) {
		if ( strlen( $args['stock'] ) == 0 ) $args['stock'] = -1;
		update_post_meta( $post_id, 'tcp_stock', $args['stock'] );
	}

	function tcp_delete_option( $post_id ){
		delete_post_meta( 'tcp_stock', $post_id );
	}

	function tcp_order_edit_status_before( $order_id, $new_status, $transaction_id, $internal_comment ) {
		return $this->tcp_order_quick_edit_before( $order_id, $new_status );
	}

	function tcp_order_edit_before( $order_id, $new_status, $new_code_tracking, $comment, $internal_comment ) {
		return $this->tcp_order_quick_edit_before( $order_id, $new_status );
	}
	
	function tcp_order_quick_edit_before( $order_id, $new_status, $new_code_tracking = '' ) {
		$old_status = Orders::getStatus( $order_id );
		global $thecartpress;
		$stock_adjustment = $thecartpress->get_setting( 'stock_adjustment', 1 );
		if ( $stock_adjustment == 3 ) {  /* option is 3 if adjustment is on order set to COMPLETED  */
			$this->stock_adjust_manual( $order_id, $new_status, $old_status );
		}
		return $order_id;
	}

	private function stock_adjust_manual( $order_id, $new_status, $old_status = '' ) {
		global $thecartpress;
		$status_to_adjust = $thecartpress->get_setting( 'stock_status_to_adjust', Orders::$ORDER_COMPLETED );
		if ( $old_status != $new_status ) {  /* status changed */
			if ( $old_status == $status_to_adjust ) {
				if ( tcp_is_greather_status( $status_to_adjust, $new_status ) ) {
					$this->no_stock_enough = TCPStockManagement::stockAdjust( $order_id, false ); /* increment stock */
				}
			} elseif ( $new_status == $status_to_adjust ) {
				if ( tcp_is_greather_status( $status_to_adjust, $old_status ) ) {
					$this->no_stock_enough = TCPStockManagement::stockAdjust( $order_id );  /* decrement stock */
				}
			} elseif ( tcp_is_greather_status( $new_status, $status_to_adjust ) ) {
				$this->no_stock_enough = TCPStockManagement::stockAdjust( $order_id);  /* decrement stock */
			} elseif ( tcp_is_greather_status( $old_status, $status_to_adjust ) ) {
				$this->no_stock_enough = TCPStockManagement::stockAdjust( $order_id, false );  /* increment stock */
			}
		}
	}

	/**
	 * @author Lincoln Phipps Open Mutual Limited
	 * This allows decrement or increment of stock from an order 
	 * @Returns True is stock is not enough
	 */
	static function stockAdjust( $order_id, $decrement = true ) {
		$orderDetails = OrdersDetails::getDetails( $order_id );
		//$no_stock_enough = false;  // Seed off by assume it is all ok
		/*if ( $decrement ) foreach ( $orderDetails as $ordersDetail ) {
			$stock = tcp_get_the_stock( $ordersDetail->post_id, $ordersDetail->option_1_id, $ordersDetail->option_2_id );
			$stock = apply_filters( 'tcp_checkout_stock', $stock, $ordersDetail->post_id, $ordersDetail->option_1_id, $ordersDetail->option_2_id );		
//echo 'Checking to decrement stock ', $ordersDetail->post_id, ' stock=', $stock, ' + ', $ordersDetail->qty_ordered, '<br>';
			if ( $stock > -1 && $stock < $ordersDetail->qty_ordered ) {
				//if ( apply_filters( 'tcp_stock_adjust_throw_exception', false ) ) {
				//	throw new Exception( __( 'Stock has not been adjusted. One or more products have not enough stock.', 'tcp' ) );
				//} else {
					return true;
				//}
			}
			//when decrementing if one of the products has not enough stock the process is stopped
			//if the order is suspended then all quantities are incremented and this is an error
		}*/

		foreach ( $orderDetails as $ordersDetail ) {
			$stock = tcp_get_the_stock( $ordersDetail->post_id, $ordersDetail->option_1_id, $ordersDetail->option_2_id );
			$stock = apply_filters( 'tcp_checkout_stock', $stock, $ordersDetail->post_id, $ordersDetail->option_1_id, $ordersDetail->option_2_id );
			if ( $stock == -1 ) continue;//return false;
			if ( ! $decrement ) {  /* if here then we ADD the stock back to the */
				$stock_to_add = tcp_get_order_detail_meta( $ordersDetail->order_detail_id, 'tcp_stock', true );
				if ( $stock_to_add == '' ) $stock_to_add = $ordersDetail->qty_ordered;
//echo 'Add stock ', $ordersDetail->post_id, ' stock=', $stock, ' + ', $stock_to_add, '<br>';
				tcp_set_the_stock( $ordersDetail->post_id, $ordersDetail->option_1_id, $ordersDetail->option_2_id, $stock + $stock_to_add );
			} else {
				if ( $stock >= $ordersDetail->qty_ordered ) {
//echo 'Remove stock ', $ordersDetail->post_id, ' stock=', $stock, ' - ', $ordersDetail->qty_ordered, '<br>';
					tcp_set_the_stock( $ordersDetail->post_id, $ordersDetail->option_1_id, $ordersDetail->option_2_id, $stock - $ordersDetail->qty_ordered );
					tcp_delete_order_detail_meta( $ordersDetail->order_detail_id, 'tcp_stock' );
				} else { //more units to decrement than units in stock
//echo 'Remove stock completely', $ordersDetail->post_id, ' stock=', $stock, ' - ', $stock, '<br>';
					tcp_update_order_detail_meta( $ordersDetail->order_detail_id, 'tcp_stock', $stock );
					tcp_set_the_stock( $ordersDetail->post_id, $ordersDetail->option_1_id, $ordersDetail->option_2_id, 0 );
				}
			}
		}
		return false;
	}

	function tcp_admin_menu() {
		global $thecartpress;
		if ( ! $thecartpress->get_setting( 'disable_ecommerce' ) ) {
			$base = $thecartpress->get_base();
			add_submenu_page( $base, __( 'Update Stock', 'tcp' ), __( 'Update Stock', 'tcp' ), 'tcp_update_stock', TCP_ADMIN_FOLDER . 'StockUpdate.php' );
		}
	}

	function tcp_main_settings_after_page() {
		global $thecartpress;
		$stock_management		= $thecartpress->get_setting( 'stock_management', false );
		$stock_adjustment		= $thecartpress->get_setting( 'stock_adjustment', 1 );  /* we set stock_adjustment to 1 because that is the TCP default before I started to add functionilty */
		$stock_status_to_adjust	= $thecartpress->get_setting( 'stock_status_to_adjust', Orders::$ORDER_COMPLETED );
		$stock_limit			= $thecartpress->get_setting( 'stock_limit', 10 );
		$hide_out_of_stock		= $thecartpress->get_setting( 'hide_out_of_stock', false ); ?>

<h3><?php _e( 'Stock', 'tcp' ); ?></h3>

<div class="postbox">
<table class="form-table">
<tbody>
<tr valign="top">
	<th scope="row">
	<label for="stock_management"><?php _e( 'Stock management', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="checkbox" id="stock_management" name="stock_management" value="yes" <?php checked( true, $stock_management ); ?> />
		<span class="description"><?php _e( 'Activates the stock management tools', 'tcp' ); ?></span>
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="stock_adjustment_1"><?php _e( 'When is stock adjusted', 'tcp' );  ?></label>
	</th>
	<td>
		<input type="radio" id="stock_adjustment_0" name="stock_adjustment" value="0" <?php checked( 0, $stock_adjustment ); ?>/>
		<span class="description"><?php _e( 'Never automatically adjusted', 'tcp' ); ?></span><br/>
		<input type="radio" id="stock_adjustment_1" name="stock_adjustment" value="1" <?php checked( 1, $stock_adjustment ); ?>/>
		<span class="description"><?php _e( 'On checkout (default)', 'tcp' ); ?></span><br/>
		<input type="radio" id="stock_adjustment_2" name="stock_adjustment" value="2" <?php checked( 2, $stock_adjustment ); ?>/>
		<span class="description"><?php _e( 'On OK from payment gateway', 'tcp' ); ?></span><br/>
		<input type="radio" id="stock_adjustment_3" name="stock_adjustment" value="3" <?php checked( 3, $stock_adjustment ); ?>/> 
		<span class="description"><?php _e( 'Order status set to ', 'tcp' ); ?>
		<select id="stock_status_to_adjust" name="stock_status_to_adjust"> 
		<?php $order_status_list = tcp_get_order_status();
		foreach ( $order_status_list as $order_status ) : ?>
			<option value="<?php echo $order_status['name'];?>"<?php selected( $order_status['name'], $stock_status_to_adjust );?>><?php echo $order_status['label']; ?></option>
		<?php endforeach; ?>
		</select>
		</span>
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="stock_limit"><?php _e( 'Stock limit', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="number" id="stock_limit" name="stock_limit" value="<?php echo $stock_limit; ?>" size="4" maxlength="4"/>
		<span class="description"><?php _e( 'Sets the upper limit to send emails for low stock', 'tcp' ); ?></span>
	</td>
</tr>
<tr valign="top">
	<th scope="row">
	<label for="hide_out_of_stock"><?php _e( 'Hide out of stock products', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="checkbox" id="hide_out_of_stock" name="hide_out_of_stock" value="yes" <?php checked( $hide_out_of_stock ); ?> />
		<span class="description"><?php _e( 'Allows to hide out of Stock products', 'tcp' ); ?></span>
	</td>
</tr>
<?php do_action( 'tcp_main_settings_page_stock' ); ?>
</tbody>
</table>
</div><!-- .postbox -->
<script>
jQuery(document).ready(function() {
	jQuery('#stock_management').click(function() {
		show_hide_stock_management();
	});
	show_hide_stock_management();
});

function show_hide_stock_management() {
	if (jQuery('#stock_management').is(':checked')) {
		jQuery('#stock_adjustment').parent().parent().fadeIn('fast');
		jQuery('#stock_adjustment_1').parent().parent().fadeIn('fast');
		jQuery('#stock_limit').parent().parent().fadeIn('fast');
		jQuery('#hide_out_of_stock').parent().parent().fadeIn('fast');
	} else {
		jQuery('#stock_adjustment').parent().parent().hide();
		jQuery('#stock_adjustment_1').parent().parent().hide();
		jQuery('#stock_limit').parent().parent().hide();
		jQuery('#hide_out_of_stock').parent().parent().hide();
	}
}
</script><?php
	}

	function tcp_main_settings_action( $settings ) {
		$settings['stock_management'] = isset( $_POST['stock_management'] );// ? $_POST['stock_management'] == 'yes' : false;
		$settings['stock_adjustment'] = isset( $_POST['stock_adjustment'] ) ? (int)$_POST['stock_adjustment'] : 1;
		$settings['stock_status_to_adjust'] = isset( $_POST['stock_status_to_adjust'] ) ? $_POST['stock_status_to_adjust'] : Orders::$ORDER_COMPLETED;
		$settings['stock_limit'] = isset( $_POST['stock_limit'] ) ? (int)$_POST['stock_limit'] : 10;
		$settings['hide_out_of_stock'] = isset( $_POST['hide_out_of_stock'] );
		return $settings;
	}

	function send_emails_for_low_stock( $order_id ) {
		global $thecartpress;
		$stock_limit = $thecartpress->get_setting( 'stock_limit', 10 );
		require_once( TCP_DAOS_FOLDER . '/OrdersDetails.class.php' );
		$details = OrdersDetails::getDetails( $order_id );
		$low_stocks = array();
		foreach( $details as $detail ) {
			$stock = tcp_get_the_stock( $detail->post_id, $detail->option_1_id, $detail->option_2_id );
			if ( $stock > -1 && $stock < $stock_limit ) {
				$low_stocks[] = array(
					'post_id' => $detail->post_id,
					'option_1_id' => $detail->option_1_id,
					'option_2_id' => $detail->option_2_id,
					'title' => $detail->name . ' ' . $detail->option_1_name . ' ' . $detail->option_2_name,
					'sku' => $detail->sku,
					'stock' => $stock,
				);
			}
		}
		if ( is_array( $low_stocks ) & count( $low_stocks ) > 0 ) {
			ob_start(); ?>
			<h1><?php _e(' Low Stock', 'tcp' ); ?></h1>
			<table>
				<tbody>
					<tr>
						<th><?php _e( 'Name', 'tcp' ); ?></th>
						<th><?php _e( 'SKU', 'tcp' ); ?></th>
						<th><?php _e( 'Stock', 'tcp' ); ?></th>
					</tr>
					<?php foreach( $low_stocks as $low_stock ) :
						$href = get_admin_url() . '/post.php?action=edit&post=' . $low_stock['post_id']; ?>
					<tr>
						<td><a href="<?php echo $href; ?>"><?php echo $low_stock['title']; ?></a></td>
						<td><a href="<?php echo $href; ?>"><?php echo $low_stock['sku']; ?></a></td>
						<td><a href="<?php echo $href; ?>"><?php echo $low_stock['stock']; ?></a></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<?php $html = ob_get_clean();
			global $thecartpress;
			$to	= $thecartpress->get_setting( 'emails', '' );
			if ( strlen( $to ) ) {
				$name = get_bloginfo( 'name' );
				$from = $thecartpress->get_setting( 'from_email', 'no-response@thecartpress.com' );
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
				$headers .= 'From: ' . $name . ' <' . $from . ">\r\n";
				wp_mail( $to, __( 'Low Stock Notice', 'tcp' ), $html, $headers );
			}
		}
	}

	/**
	 * Adding a template hook displayed when buyers try to pay and not stock is available
	 *
	 * @uses tcp_add_template_class
	 */
	function add_template_class() {
		tcp_add_template_class( 'tcp_error_stock_when_pay', __( 'This notice will be showed when the client is going to pay and there is no stock of any product in the cart.', 'tcp') );
	}

	function tcp_product_metabox_custom_fields( $post_id ) {
		global $thecartpress;
		$stock_management = $thecartpress->get_setting( 'stock_management' ); ?>
		<tr valign="top">
			<th scope="row">
				<label for="tcp_stock"><?php _e( 'Stock', 'tcp' ); ?>:</label>
			<?php if ( ! $stock_management ) : //not in use
				$path = 'admin.php?page=tcp_settings_page'; ?>
				<span class="description"><?php printf( __( 'Stock management is disabled. See the <a href="%s">settings</a> page to change this value.', 'tcp' ), $path ); ?></span>
			<?php endif; ?>
			</th>
			<td>
				<?php $stock = get_post_meta( $post_id, 'tcp_stock', true );
				if ( strlen( $stock ) == 0 ) $stock = -1; ?>
				<input name="tcp_stock" id="tcp_stock" value="<?php echo htmlspecialchars( $stock ); ?>" class="regular-text tcp_count_min" type="text" min="-1" style="width:10em" />
				<span class="description"><?php _e( 'Use value -1 (or left blank) for stores/products with no stock management.', 'tcp' ); ?></span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="tcp_initial_stock"><?php _e( 'Initial Stock', 'tcp' ); ?>:</label>
			<?php if ( ! $stock_management ) : //not in use
				$path = 'admin.php?page=tcp_settings_page'; ?>
				<span class="description"><?php printf( __( 'Stock management is disabled. See the <a href="%s">settings</a> page to change this value.', 'tcp' ), $path ); ?></span>
			<?php endif; ?>
			</th>
			<td>
				<input name="tcp_initial_stock" id="tcp_initial_stock" value="<?php echo htmlspecialchars( get_post_meta( $post_id, 'tcp_initial_stock', true ) ); ?>" class="regular-text tcp_count_min" type="text" min="-1" style="width:10em" />
			</td>
		</tr><?php
	}

	function tcp_product_custom_fields_tabs( $tabs ) {
		$tabs['tcp-stock-options'] = __( 'Stock', 'tcp' );
		return $tabs;
	}

	function tcp_product_metabox_custom_tabs( $post_id ) { ?>
<div id="tcp-stock-options" style="display: none;">
	<table class="form-table">
		<tbody>
		<?php $this->tcp_product_metabox_custom_fields( $post_id ); ?>
		</tbody>
	</table>
</div><!-- #tcp-advanced-options -->
	<?php }

	function tcp_product_metabox_save_custom_fields( $post_id ) {
		$tcp_stock = isset( $_POST['tcp_stock'] ) ? (int)$_POST['tcp_stock'] : -1;
		$tcp_initial_stock = isset( $_POST['tcp_initial_stock'] ) ? (int)$_POST['tcp_initial_stock'] : 0;
		update_post_meta( $post_id, 'tcp_stock', $tcp_stock );
		update_post_meta( $post_id, 'tcp_initial_stock', $tcp_initial_stock );
	}

	function tcp_product_metabox_save_custom_fields_translations( $post_id, $base_post_id ) {
		$this->tcp_product_metabox_save_custom_fields( $post_id );
	}

	function tcp_product_metabox_delete_custom_fields( $post_id ) {
		delete_post_meta( $post_id, 'tcp_stock' );
		delete_post_meta( $post_id, 'tcp_initial_stock' );
	}

	function tcp_shopping_cart_widget_form( $widget, $instance ) {
		$see_stock_notice = isset( $instance['see_stock_notice'] ) ? (bool)$instance['see_stock_notice'] : false; ?>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $widget->get_field_id( 'see_stock_notice' ); ?>" name="<?php echo $widget->get_field_name( 'see_stock_notice' ); ?>"<?php checked( $see_stock_notice ); ?> />
			<label for="<?php echo $widget->get_field_id( 'see_stock_notice' ); ?>"><?php _e( 'See stock notices', 'tcp' ); ?></label>
		</p><?php
	}

	function tcp_shopping_cart_widget_update( $instance, $new_instance ) {
		$instance['see_stock_notice'] = isset( $new_instance['see_stock_notice'] );
		return $instance;
	}

	function tcp_shopping_cart_widget_units( $item, $instance ) {
		global $thecartpress;
		$stock_management = $thecartpress->get_setting( 'stock_management', false );
		if ( $stock_management ) {
			$see_stock_notice = isset( $instance['see_stock_notice'] ) ? $instance['see_stock_notice'] : false;
			if ( $see_stock_notice ) {
				$stock = tcp_get_the_stock( $item->getPostId(), $item->getOption1Id(), $item->getOption2Id() );
				ob_start();
				if ( $stock != -1 && $stock < $item->getCount() ) : ?>
					<span class="tcp_no_stock_enough">
					<?php if ( $stock > 0 ) : ?>
						<?php printf( __( 'No enough stock for this product. Only %s available items.', 'tcp' ), $stock ); ?>
					<?php else : ?>
						<?php _e( 'Out of stock', 'tcp' ); ?>
					<?php endif; ?>
					</span>
				<?php endif;
				echo apply_filters( 'tcp_stock_shopping_cart_widget_units', ob_get_clean(), $item->getPostId() );
			}
		}
	}

	function tcp_cart_units( $item ) {
		global $thecartpress;
		$stock_management = $thecartpress->get_setting( 'stock_management', false );
		if ( $stock_management ) {
			$stock = tcp_get_the_stock( $item->get_post_id(), $item->get_option_1_id(), $item->get_option_2_id() );
			ob_start();
			if ( $stock == 0 ) : ?>
				<span class="tcp_no_stock"><?php _e( 'Out of stock', 'tcp' ); ?></span>
			<?php elseif ( $stock != -1 && $stock < $item->get_qty_ordered() ) : ?>
				<span class="tcp_no_stock_enough">
				<?php printf( __( 'No enough stock. Only %s available items.', 'tcp' ), $stock ); ?>
				</span>
			<?php endif;
			echo apply_filters( 'tcp_stock_cart_units', ob_get_clean(), $item->get_post_id() );
		}
	}

	function tcp_checkout_manager( $html ) {
		if ( ! tcp_is_stock_in_shopping_cart() ) {
			require_once( TCP_SHORTCODES_FOLDER . 'ShoppingCartPage.class.php' );
			return TCPShoppingCartPage::show( __( 'You are trying to check out your order but, at this moment, there are not enough stock of some products. Please, review the list of products.', 'tcp' ) );
		}
	}

	function tcp_checkout_create_order_insert_detail( $order_id, $orders_details_id, $post_id, $ordersDetails ) {
		global $thecartpress;
		$stock_management = $thecartpress->get_setting('stock_management', false );
		$stock_adjustment = $thecartpress->get_setting('stock_adjustment', 1 );
		if ( $stock_management ) {
			$stock = tcp_get_the_stock( $ordersDetails['post_id'], $ordersDetails['option_1_id'], $ordersDetails['option_2_id'] );
			$stock = apply_filters( 'tcp_checkout_stock', $stock );
			if ( $stock == -1 ) {
				$this->no_stock_enough = false;
			} elseif ( $stock >= $ordersDetails['qty_ordered'] ) {
				if ($stock_adjustment == 1) {   /* if this is 1 then we adjust stock here (which is on checkout not  payment or other option ) */
					tcp_set_the_stock( $ordersDetails['post_id'], $ordersDetails['option_1_id'], $ordersDetails['option_2_id'], $stock - $ordersDetails['qty_ordered'] );
					$this->no_stock_enough = false;
				} else {
					$this->no_stock_enough = false;
				}
			} else {
				$this->no_stock_enough = true;//the next hook is "tcp_checkout_ok"
			}
		}
	}

	function tcp_checkout_ok( $order_id ) {
		if ( $this->no_stock_enough ) {
			Orders::editStatus( $order_id, Orders::$ORDER_PENDING, __( 'Not enough stock in order at check-out', 'tcp' ) );
			$message = tcp_do_template( 'tcp_error_stock_when_pay', false );
			if ( strlen( $message ) == 0 ) : ?>
				<p><?php _e( 'There was an error when creating the order. Please contact with the seller.', 'tcp' ); ?></p>
			<?php else :
				echo $message;
			endif;
		}
		$this->send_emails_for_low_stock( $order_id );
	}

/* 
*  @author Lincoln Phipps Open Mutual Limited
*  This is an enhancement that allows you to only decrement stock on a completed sale rather than the checkout. Use this if 
*  single unique items are being sold (so stock is 1) and you have no-stock-hiding set or where you have many abandoned orders. 
*  There is a risk of two completed sales fighting for the same single stock item. They will BOTH be in PROCESSING but the 
*  second will not be able to be picked. 
*  This function can safely be added after payment OK on any kind of order because it is never used unless 
*  the option $stock_adjustment is set to 2
*/ 
	function tcp_completed_ok_stockadjust( $order_id ) {
		global $thecartpress;
		$totalOrderDetails = OrdersDetails::getDetails( $order_id );
		$order = Orders::get( $order_id );
		$_additional = $order->comment_internal;
		$additional = __( 'No stock after payment for:', 'tcp' );
		$stock_management = $thecartpress->get_setting('stock_management', false );
		$stock_adjustment = $thecartpress->get_setting('stock_adjustment', 1 );

		if ( $stock_management ) {
			if ( $stock_adjustment == 2 ) {  /*  that is we are using stock management and only on payment completed */
				$this->no_stock_enough = false;  // Seed off by assume it is all ok
				//TCPStockManagement::stockAdjust( $order_id )
				foreach ( $totalOrderDetails as $ordersDetails) {  
					$stock = tcp_get_the_stock( $ordersDetails->post_id, $ordersDetails->option_1_id, $ordersDetails->option_2_id );
					$stock = apply_filters( 'tcp_checkout_stock', $stock );
					if ( $stock == -1 ) {
						$this->no_stock_enough = $this->no_stock_enough;
					} elseif ( $stock >= $ordersDetails->qty_ordered ) {
							tcp_set_the_stock( $ordersDetails->post_id, $ordersDetails->option_1_id, $ordersDetails->option_2_id, $stock - $ordersDetails->qty_ordered );
							$this->no_stock_enough = $this->no_stock_enough;
					} else {
						$additional .= strip_tags($ordersDetails->name." ".$ordersDetails->option_1_name." ".$ordersDetails->option_2_name.",");
						$this->no_stock_enough = true; /* Ahhh one of possibly many items out of stock. TODO backordering could be triggered here */
					}
				} /* foreach  */
			} elseif ( $stock_adjustment == 3 ) {
				//$new_status = Orders::getStatus( $order_id );
				$new_status = $order->status;
				$this->stock_adjust_manual( $order_id, $new_status );
			}
			if ( $this->no_stock_enough ) {
				Orders::editStatus( $order_id, Orders::$ORDER_PROCESSING, $order->transaction_id, $_additional."\n".$additional );
				$message = tcp_do_template( 'tcp_error_stock_when_pay', false );
				if ( strlen( $message ) == 0 ) : ?>
					<p><?php _e( 'There was an error when creating the order. Seller will contact you regarding your order.', 'tcp' ); ?></p>
				<?php else :
					echo $message;
				endif;
			}
			$this->send_emails_for_low_stock( $order_id );
		}
	}

	function tcp_get_dynamic_options( $posts, $parent_id ) {
		$res = array();
		foreach( $posts as $id => $post_id ) {
			if ( tcp_get_the_stock( $post_id ) != 0 ) $res[] = $post_id;
		}
		return $res;
	}

	function tcp_custom_list_widget_args( $loop_args ) {
		global $thecartpress;
		if ( $thecartpress->get_setting( 'hide_out_of_stock' ) )
			$loop_args['meta_query'][] = array(
				'key'		=> 'tcp_stock',
				'value'		=> 0,
				'type'		=> 'NUMERIC',
				'compare'	=> '!='
			);
		return $loop_args;
	}

	function tcp_custom_values_get_other_values( $other_values ) {
		$other_values['tcp_stock'] = array(
			'label' => __( 'Units in stock', 'tcp' ),
			'callback' => 'tcp_get_the_stock_label',
		);
		$other_values['tcp_init_stock'] = array(
			'label' => __( 'Initial stock', 'tcp' ),
			'callback' => 'tcp_get_the_initial_stock',
		);
		return $other_values;
	}

	//ProductCustomPostType
	function tcp_custom_columns_definition( $columns ) {
		$columns['stock'] = __( 'Stock', 'tcp' );
		return $columns;
	}

	function tcp_manage_posts_custom_column( $column_name, $post ) {
		if ( 'stock' == $column_name ) {
			$stock = tcp_get_the_stock( $post->ID );
			global $thecartpress;
			$stock_limit = $thecartpress->get_setting( 'stock_limit', 10 );
			if ( $stock == -1 ) {
				$options_1 = RelEntities::select( $post->ID, 'OPTIONS' );
				if ( is_array( $options_1 ) && count( $options_1 ) > 0 ) {
					$stock = '';
					foreach( $options_1 as $option_1 ) {
						$options_2 = RelEntities::select( $option_1->id_to, 'OPTIONS' );
						if ( is_array( $options_2 ) && count( $options_2 ) > 0 ) {
							foreach( $options_2 as $option_2 ) {
								$option_stock = tcp_get_the_stock( $post->ID, $option_1->id_to, $option_2->id_to );
								if ( $option_stock != -1 ) {
									$stock .= sprintf( '%d for %s, ', $option_stock, get_the_title( $option_1->id_to ) . ' ' . get_the_title( $option_2->id_to ) );
								}
							}
						} else {
							$option_stock = tcp_get_the_stock( $post->ID, $option_1->id_to );
							if ( $option_stock != -1 ) {
								$stock .= sprintf( '%d for %s, ', $option_stock, get_the_title( $option_1->id_to ) );
							}
						}
					}
					if ( $stock == '' ) $stock = __( 'N/A', 'tcp' );
					else $stock = substr( $stock, 0, strlen( $stock ) -2 );
				} else {
					$stock = __( 'N/A', 'tcp' );
				}
			} else if ( $stock < $stock_limit ) {
				$stock = sprintf( '<span class="tcp_low_stock" style="color: red">%s</span>', $stock );
			}
			echo $stock;
		}
	}

	//OptionList.php
	function tcp_create_option( $option_id, $new_option_id ) {
		$stock = tcp_get_the_stock( $option_id );
		add_post_meta( $new_option_id, 'tcp_stock',  $stock );
	}

	//ShoppingCartSummaryWidget.class.php
	function tcp_shopping_cart_summary_widget_form( $widget, $instance ) {
		$see_stock_notice = isset( $instance['see_stock_notice'] ) ? (bool)$instance['see_stock_notice'] : false; ?>
		<br />
		<input type="checkbox" class="checkbox" id="<?php echo $widget->get_field_id( 'see_stock_notice ' ); ?>" name="<?php echo $widget->get_field_name( 'see_stock_notice' ); ?>"<?php checked( $see_stock_notice ); ?> />
		<label for="<?php echo $widget->get_field_id( 'see_stock_notice' ); ?>"><?php _e( 'See stock notice', 'tcp ' ); ?></label><?php
	}
	
	function tcp_shopping_cart_summary_widget_update( $instance, $new_instance ) {
		$instance['see_stock_notice']	= isset( $new_instance['see_stock_notice'] );
		return $instance;
	}

	function tcp_get_shopping_cart_summary( $summary, $args ) {
		if (isset( $args['see_stock_notice'] ) ? $args['see_stock_notice'] : false )
			if ( ! tcp_is_stock_in_shopping_cart() )
				$summary .= '<li><span class="tcp_no_stock_nough">' . sprintf( __( 'No enough stock for some products. Visit the <a href="%s">Shopping Cart</a> to see more details.', 'tcp' ), tcp_get_the_shopping_cart_url() ) . '</span></li>';
		return $summary;
	}

	function tcp_show_shopping_cart_summary_widget_params( $instance ) { 
		if ( $instance['see_stock_notice'] ) : ?>
		,see_stock_notice	: "1"
		<?php endif;
	}

	function tcp_the_add_to_cart_unit_field( $out, $post_id ) {
		if ( tcp_get_the_stock( $post_id ) == 0 ) return '<span class="tcp_no_stock">' . __( 'Out of stock', 'tcp' ) . '</span>';
		return $out;
	}

	function tcp_the_add_to_cart_button( $out, $post_id ) {
		if ( tcp_get_the_stock( $post_id ) == 0 ) return '';
		return $out;
	}

	function tcp_apply_filters_for_saleables( $query ) {
		global $thecartpress;
		if ( $thecartpress->get_setting( 'hide_out_of_stock' ) ) {
			$meta_query = $query->get( 'meta_query' );
			$meta_query[] = array(
				'key'		=> 'tcp_stock',
				'value'		=> 0,
				'type'		=> 'NUMERIC',
				'compare'	=> '!='
			);
			$query->set( 'meta_query', $meta_query );
		}
		return $query;
	}

	function stock_column_sortable_column( $columns ) {
		$columns['stock'] = 'tcp_stock';
		return $columns;
	}

	function stock_column_orderby( $vars ) {
		if ( isset( $vars['orderby'] ) && 'tcp_stock' == $vars['orderby'] ) {
			$vars = array_merge( $vars, array(
				'orderby' => 'meta_value_num',
				'meta_key' => 'tcp_stock',
			) );
		}
		return $vars;
	}
}

$GLOBALS['stock_management'] = new TCPStockManagement();
//global $thecartpress;
//if ( $thecartpress ) $thecartpress->addGlobalVariable( 'stock_management', $stock_management );

require_once( TCP_WIDGETS_FOLDER . 'StockSummaryDashboard.class.php' );

function tcp_the_stock( $before = '', $after = '' ) {
	$stock = tcp_get_the_meta( 'tcp_stock' );
	echo $before . $stock . $after;
}

	function tcp_get_the_stock( $post_id = 0, $option_1_id = 0, $option_2_id = 0 ) {
		if ( $option_2_id > 0 ) {
			$stock = tcp_get_the_meta( 'tcp_stock', $option_2_id );
			if ( $stock == -1 )
				$stock = tcp_get_the_stock( $post_id, $option_1_id );
		} elseif ( $option_1_id > 0) {
			$stock = tcp_get_the_meta( 'tcp_stock', $option_1_id );
			if ( $stock == -1 )
				$stock = tcp_get_the_stock( $post_id );
		} else {
			$post_id = tcp_get_default_id( $post_id );
			$stock = tcp_get_the_meta( 'tcp_stock', $post_id );
			if ( strlen( $stock ) > 0 )
				$stock = (int)$stock;
			else
				$stock = -1;
		}
		return apply_filters( 'tcp_get_the_stock', $stock, $post_id, $option_1_id, $option_2_id );
	}

/**
 * Returns the current stock.
 * Current store is the stock of the product minus possible units of the same product in the shopping cart
 *
 * @since 1.3.0
 */
function tcp_get_the_current_stock( $post_id = 0 ) {
	$stock = tcp_get_the_stock( $post_id );
	$sc = TheCartPress::getShoppingCart();
	$item = $sc->getItem( $post_id );
	if ( $item !== false && $item->getUnits() > $stock ) $stock = 0;
	return $stock;
}

/**
 * Equal to 'tcp_get_the_stock' but return blank if -1
 */
function tcp_get_the_stock_label( $post_id = 0, $option_1_id = 0, $option_2_id = 0 ) {
	$stock = tcp_get_the_stock( $post_id, $option_1_id, $option_2_id );
	if ( $stock == -1 ) $stock = '';
	return $stock;
}

function tcp_set_the_stock( $post_id, $option_1_id = 0, $option_2_id = 0, $stock = -1 ) {
	if ( (int)$stock > -1 ) {
		if ( $option_2_id > 0) {
			$old_stock = tcp_get_the_meta( 'tcp_stock', $option_2_id );
			if ( $old_stock == -1 ) {
				return tcp_set_the_stock( $post_id, $option_1_id, 0, $stock );
			} else {
				update_post_meta( $option_2_id, 'tcp_stock', (int)$stock );
				return true;
			}
		} elseif ( $option_1_id > 0) {
			$old_stock = tcp_get_the_meta( 'tcp_stock', $option_1_id );
			if ( $old_stock == -1 ) {
				return tcp_set_the_stock( $post_id, 0, 0, $stock );
			} else {
				update_post_meta( $option_1_id, 'tcp_stock', (int)$stock );
				return true;
			}
		} else {
			$post_id = tcp_get_default_id( $post_id );
//echo 'Updated stock ', $post_id, ' stock=', $stock, '<br>';
			update_post_meta( $post_id, 'tcp_stock', (int)$stock );
			$translations = tcp_get_all_translations( $post_id, get_post_type( $post_id ) );
			if ( is_array( $translations ) && count( $translations ) > 0 )
				foreach( $translations as $translation )
					if ( $translation->element_id != $post_id )
						update_post_meta( $translation->element_id, 'tcp_stock', (int)$stock );
			return true;
		}
	} else return false;
}

function tcp_is_stock_in_shopping_cart( $post_id = 0, $option_1_id = 0, $option_2_id = 0 ) {
	if ( $post_id == 0 ) {
		$shoppingCart = TheCartPress::getShoppingCart();
		$items = $shoppingCart->getItems();
		foreach( $items as $item ) {
			$stock = tcp_get_the_stock( $item->getPostId(), $item->getOption1Id(), $item->getOption2Id() );
			if ( $stock == 0 || ( $stock > -1 && $stock < $item->getCount() ) )
				return false;
		}
		return true;
	} else {
		$stock = tcp_get_the_stock( $post_id, $option_1_id, $option_2_id );
		if ( $stock == 0 || ( $stock > -1 && $stock < $item->getCount() ) )
			return false;
		else
			return true;
	}
}

function tcp_the_initial_stock( $before = '', $after = '', $echo = true ) {
	//$stock = tcp_the_meta( 'tcp_initial_stock', $before, $after, false );
	//if ( $echo ) echo $stock;
	//else return $stock;
	$stock = tcp_get_the_initial_stock();
	if ( $stock > 0 ) echo $before . $stock . $after;
}

function tcp_get_the_initial_stock( $post_id = 0, $option_1_id = 0, $option_2_id = 0 ) {
	if ( $option_2_id > 0) {
		$initial_stock = tcp_get_the_meta( 'tcp_initial_stock', $option_2_id );
		if ( $initial_stock == -1 )
			$initial_stock = tcp_get_the_initial_stock( $post_id, $option_1_id );
	} elseif ( $option_1_id > 0) {
		$initial_stock = tcp_get_the_meta( 'tcp_initial_stock', $option_1_id );
		if ( $initial_stock == -1 )
			$initial_stock = tcp_get_the_initial_stock( $post_id );
	} else {
		$post_id = tcp_get_default_id( $post_id );
		$initial_stock = tcp_get_the_meta( 'tcp_initial_stock', $post_id );
		if ( strlen( $initial_stock ) > 0 )
			$initial_stock = (int)$initial_stock;
		else
			$initial_stock = -1;
	}
	return apply_filters( 'tcp_get_the_initial_stock', $initial_stock, $post_id, $option_1_id, $option_2_id );
}

} // class_exists check