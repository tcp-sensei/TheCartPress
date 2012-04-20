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

class TCPStockManagement {
	private $no_stock_enough = false;

	function send_emails_for_low_stock( $order_id ) {
		global $thecartpress;
		$stock_limit = $thecartpress->get_setting( 'stock_limit', 10 );
		require_once( TCP_DAOS_FOLDER . '/OrdersDetails.class.php' );
		$details = OrdersDetails::getDetails( $order_id );
		$low_stock = array();
		foreach( $details as $detail ) {
			$stock = tcp_get_the_stock( $detail->post_id, $detail->option_1_id, $detail->option_2_id );
			if ( $stock > -1 && $stock < $stock_limit ) {
				$low_stocks[] = array(
					'post_id'		=> $detail->post_id,
					'option_1_id'	=> $detail->option_1_id,
					'option_2_id'	=> $detail->option_2_id,
					'title'			=> $detail->name . ' ' . $detail->option_1_name . ' ' . $detail->option_2_name,
					'stock'			=> $stock,
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
						<th><?php _e( 'Stock', 'tcp' ); ?></th>
					</tr>
					<?php foreach( $low_stocks as $low_stock ) :
						$href = get_admin_url() . '/post.php?action=edit&post=' . $low_stock['post_id']; ?>
					<tr>
						<td><a href="<?php echo $href; ?>"><?php echo $low_stock['title']; ?></a></td>
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
				$from	= $thecartpress->get_setting( 'from_email', 'no-response@thecartpress.com' );
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
				$headers .= 'From: ' . $name . ' <' . $from . ">\r\n";
				wp_mail( $to, __( 'Low Stock Notice', 'tcp' ), $html, $headers );
			}
		}
	}

	function admin_menu() {
		global $thecartpress;
		$disable_ecommerce = $thecartpress->get_setting( 'disable_ecommerce' );
		if ( ! $disable_ecommerce ) {
			$base = $thecartpress->get_base_tools();
			add_submenu_page( $base, __( 'Update Stock', 'tcp' ), __( 'Update Stock', 'tcp' ), 'tcp_update_stock', TCP_ADMIN_FOLDER . 'StockUpdate.php' );
		}	
	}

	function admin_init() {
		$tcp_settings_page = TCP_ADMIN_FOLDER . 'Settings.class.php';
		add_settings_field( 'stock_management', __( 'Stock management', 'tcp' ), array( $this, 'show_stock_management' ), $tcp_settings_page , 'tcp_main_section' );
		add_settings_field( 'stock_limit', __( 'Stock limit', 'tcp' ), array( $this, 'show_stock_limit' ), $tcp_settings_page , 'tcp_main_section' );
	}

	function show_stock_management() {
		global $thecartpress;
		$stock_management = $thecartpress->get_setting( 'stock_management' ); ?>
		<input type="checkbox" id="stock_management" name="tcp_settings[stock_management]" value="yes" <?php checked( true, $stock_management ); ?> /><?php
	}

	function show_stock_limit() {
		global $thecartpress;
		$stock_limit = $thecartpress->get_setting( 'stock_limit', 10 ); ?>
		<input type="checkbox" id="stock_limit" name="tcp_settings[stock_limit]" value="yes" <?php checked( true, $stock_limit ); ?> /><?php
	}

	function tcp_validate_settings( $input ) {
		$input['stock_management'] = isset( $input['stock_management'] ) ? $input['stock_management'] == 'yes' : false;
		$input['stock_limit'] = isset( $input['stock_limit'] ) ? (int)$input['stock_limit'] : 10;
		return $input;
	}

	function add_template_class() {
		tcp_add_template_class( 'tcp_error_stock_when_pay', __( 'This notice will be showed when the client is going to pay and there is no stock of any product in the cart.', 'tcp') );
	}

	function tcp_product_metabox_custom_fields( $post_id ) {
		global $thecartpress;
		$stock_management = $thecartpress->get_setting( 'stock_management' ); ?>
		<tr valign="top">
			<th scope="row"><label for="tcp_stock"><?php _e( 'Stock', 'tcp' ); ?>:</label>
			<?php if ( ! $stock_management ) : 
				$path = 'admin.php?page=tcp_settings_page'; ?>
				<span class="description"><?php printf( __( 'Stock management is disabled. See the <a href="%s">settings</a> page to change this value.', 'tcp' ), $path ); ?></span>
			<?php endif; ?>
			</th>
			<td><input name="tcp_stock" id="tcp_stock" value="<?php echo htmlspecialchars( get_post_meta( $post_id, 'tcp_stock', true ) ); ?>" class="regular-text tcp_count_min" type="text" min="-1" style="width:10em" />
			<br /><span class="description"><?php _e( 'Use value -1 (or left blank) for stores/products with no stock management.', 'tcp' ); ?></span></td>
		</tr><?php
	}

	function tcp_product_metabox_save_custom_fields( $post_id ) {
		$tcp_stock = isset( $_POST['tcp_stock'] ) ? $_POST['tcp_stock'] : -1;
		if ( $tcp_stock == '' ) $tcp_stock = -1;
		update_post_meta( $post_id, 'tcp_stock', (int)$tcp_stock );
	}

	function tcp_product_metabox_delete_custom_fields( $post_id ) {
		delete_post_meta( $post_id, 'tcp_stock' );
	}

	function tcp_shopping_cart_widget_form( $widget, $instance ) {
		$see_stock_notice	= isset( $instance['see_stock_notice'] ) ? (bool)$instance['see_stock_notice'] : false; ?>
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
		$stock_management = isset( $thecartpress->settings['stock_management'] ) ? $thecartpress->settings['stock_management'] : false;
		if ( $stock_management ) {
			$see_stock_notice = isset( $instance['see_stock_notice'] ) ? $instance['see_stock_notice'] : false;
			if ( $see_stock_notice ) {
				$stock = tcp_get_the_stock( $item->getPostId(), $item->getOption1Id(), $item->getOption2Id() );
				if ( $stock != -1 && $stock < $item->getCount() ) {
					echo '<span class="tcp_no_stock_enough">';
					if ( $stock > 0 ) {
						printf( __( 'No enough stock for this product. Only %s items available.', 'tcp' ), $stock );
					} else {
						_e( 'Out of stock.', 'tcp' );
					}
					echo '</span>';
				}
			}
		}
	}

	function tcp_cart_units( $item ) {
		global $thecartpress;
		$stock_management = isset( $thecartpress->settings['stock_management'] ) ? $thecartpress->settings['stock_management'] : false;
		if ( $stock_management ) {
			$stock = tcp_get_the_stock( $item->get_post_id(), $item->get_option_1_id(), $item->get_option_2_id() );
			if ( $stock == 0 ) : ?>
				<span class="tcp_no_stock"><?php _e( 'Out of stock', 'tcp' ); ?></span>
			<?php elseif ( $stock != -1 && $stock < $item->get_qty_ordered() ) : ?>
				<span class="tcp_no_stock_enough">
				<?php printf( __( 'No enough stock. Only %s items available.', 'tcp' ), $stock ); ?>
				</span>
			<?php endif;
		}
	}

	function tcp_checkout_manager( $html ) {
		if ( ! tcp_is_stock_in_shopping_cart() ) {
			require_once( TCP_SHORTCODES_FOLDER . 'ShoppingCartPage.class.php' );
			$shoppingCartPage = new TCPShoppingCartPage();
			return $shoppingCartPage->show( __( 'You are trying to check out your order but, at this moment, there are not enough stock of some products. Please review the list of products.', 'tcp' ) );
		}
	}

	function tcp_checkout_create_order_insert_detail( $order_id, $orders_details_id, $post_id, $ordersDetails ) {
		global $thecartpress;
		$stock_management = $thecartpress->get_setting('stock_management', false );
		if ( $stock_management ) {
			$stock = tcp_get_the_stock( $ordersDetails['post_id'], $ordersDetails['option_1_id'], $ordersDetails['option_2_id'] );
			$stock = apply_filters( 'tcp_checkout_stock', $stock );
			if ( $stock == -1 ) {
				$this->no_stock_enough = false;
			} elseif ( $stock >= $ordersDetails['qty_ordered'] ) {
				tcp_set_the_stock( $ordersDetails['post_id'], $ordersDetails['option_1_id'], $ordersDetails['option_2_id'], $stock - $ordersDetails['qty_ordered'] );
				$this->no_stock_enough = false;
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
		,see_stock_notice		: "1"
		<?php endif;
	}

	function tcp_the_add_to_cart_unit_field( $out, $post_id ) {
		if ( tcp_get_the_stock( $post_id ) == 0 )
			$out = '<span class="tcp_no_stock">' . __( 'No stock for this product', 'tcp' ) . '</span>';
		return $out;
	}

	function __construct() {
		if ( is_admin() ) {
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_filter( 'tcp_validate_settings', array( $this, 'tcp_validate_settings' ) );
		}
		global $thecartpress;
		if ( ! empty( $thecartpress ) ) {
			$stock_management = $thecartpress->get_setting( 'stock_management', false );
			if ( $stock_management ) {
				if ( is_admin() ) {
					add_action( 'tcp_product_metabox_custom_fields', array( $this, 'tcp_product_metabox_custom_fields' ) );
					add_action( 'tcp_product_metabox_save_custom_fields', array( $this, 'tcp_product_metabox_save_custom_fields' ) );
					add_action( 'tcp_product_metabox_delete_custom_fields', array( $this, 'tcp_product_metabox_delete_custom_fields' ) );

					add_action( 'tcp_options_metabox_custom_fields', array( $this, 'tcp_product_metabox_custom_fields' ) );
					add_action( 'tcp_options_metabox_save_custom_fields', array( $this, 'tcp_product_metabox_save_custom_fields' ) );
					add_action( 'tcp_options_metabox_delete_custom_fields', array( $this, 'tcp_product_metabox_delete_custom_fields' ) );

					add_action( 'tcp_dynamic_options_metabox_custom_fields', array( $this, 'tcp_product_metabox_custom_fields' ) );
					add_action( 'tcp_dynamic_options_metabox_save_custom_fields', array( $this, 'tcp_product_metabox_save_custom_fields' ) );
					add_action( 'tcp_dynamic_options_metabox_delete_custom_fields', array( $this, 'tcp_product_metabox_delete_custom_fields' ) );
				}

				add_action( 'admin_menu', array( $this, 'admin_menu' ) );
				add_action( 'admin_init', array( $this, 'add_template_class' ) );

				add_action( 'tcp_shopping_cart_widget_form', array( $this, 'tcp_shopping_cart_widget_form'), 10, 2 );
				add_filter( 'tcp_shopping_cart_widget_update', array( $this, 'tcp_shopping_cart_widget_update') , 10, 2 );
				add_action( 'tcp_shopping_cart_widget_units', array( $this, 'tcp_shopping_cart_widget_units' ), 10, 2 );

				add_filter( 'tcp_cart_units', array( $this, 'tcp_cart_units' ), 10, 2 );
				add_filter( 'tcp_custom_columns_definition', array( $this, 'tcp_custom_columns_definition' ) );
				add_action( 'tcp_manage_posts_custom_column', array( $this, 'tcp_manage_posts_custom_column' ), 10, 2 );

				add_filter( 'tcp_checkout_manager', array( $this, 'tcp_checkout_manager' ) );
				add_action( 'tcp_checkout_create_order_insert_detail', array( $this, 'tcp_checkout_create_order_insert_detail' ), 10, 4 );
				add_action( 'tcp_checkout_ok', array( $this, 'tcp_checkout_ok' ) );
				add_action( 'tcp_create_option', array( $this, 'tcp_create_option' ), 10, 2 );

				add_action( 'tcp_shopping_cart_summary_widget_form', array( $this, 'tcp_shopping_cart_summary_widget_form' ), 10, 2 );
				add_filter( 'tcp_shopping_cart_summary_widget_update', array( $this, 'tcp_shopping_cart_summary_widget_update' ) , 10, 2 );
				add_filter( 'tcp_get_shopping_cart_summary', array( $this, 'tcp_get_shopping_cart_summary' ), 10, 2 );
				add_action( 'tcp_show_shopping_cart_summary_widget_params', array( $this, 'tcp_show_shopping_cart_summary_widget_params' ) );
			
				add_filter( 'tcp_the_add_to_cart_unit_field', array( $this, 'tcp_the_add_to_cart_unit_field' ), 10, 2 );
			}
		}
	}
}

$stock_management = new TCPStockManagement();

require_once( TCP_WIDGETS_FOLDER . 'StockSummaryDashboard.class.php' );

function tcp_the_stock( $before = '', $after = '', $echo = true ) {
	$stock = tcp_the_meta( 'tcp_stock', $before, $after, false );
	if ( $echo )
		echo $stock;
	else
		return $stock;
}

function tcp_get_the_stock( $post_id = 0, $option_1_id = 0, $option_2_id = 0 ) {
	if ( $option_2_id > 0) {
		$stock = tcp_get_the_meta( 'tcp_stock', $option_2_id );
		if ( $stock == -1 )
			$stock = tcp_get_the_stock( $post_id, $option_1_id );
	} elseif ( $option_1_id > 0) {
		$stock = tcp_get_the_meta( 'tcp_stock', $option_1_id );
		if ( $stock == -1 )
			$stock = tcp_get_the_stock( $post_id );
	} else {
		$stock = tcp_get_the_meta( 'tcp_stock', $post_id );
		if ( strlen( $stock ) > 0 )
			$stock = (int)$stock;
		else
			$stock = -1;
	}
	$stock = apply_filters( 'tcp_get_the_stock', $stock, $post_id, $option_1_id, $option_2_id );
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
			update_post_meta( $post_id, 'tcp_stock', (int)$stock );
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
?>
