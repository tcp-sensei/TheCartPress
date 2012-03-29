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

class TCPDownloadableProducts {

	function tcp_the_add_to_cart_unit_field( $out, $post_id ) {
		if ( tcp_is_downloadable( $post_id ) ) {
			//require_once( TCP_CLASSES_FOLDER . 'MP3Player.class.php' );
			//$out = TCPMP3Player::showPlayer( $post_id, TCPMP3Player::$SMALL, false ); //DEPRECATED
			global $tcp_jplayer;
			$out = $tcp_jplayer->show( $post_id, array( 'echo' => false ) );
			$out .= '<input type="hidden" name="tcp_count[]" id="tcp_count_' . $post_id . '" value="1" />';
		}
		return $out;
	}

	function tcp_the_add_to_cart_items_in_the_cart( $out, $post_id ) {
		if ( tcp_is_downloadable( $post_id ) ) {
			$shopingCart = TheCartPress::getShoppingCart();
			if ( $shopingCart->exists( $post_id ) ) {
				ob_start(); ?>
				<div class="tcp_already_in_cart">
				<?php printf( __( 'The product is in your <a href="%s">cart</a>' ,'tcp' ) , tcp_get_the_shopping_cart_url() ); ?>
				</div><?php
				$out = ob_get_clean();
			}
		}
		return $out;
	}

	function tcp_the_add_to_cart_button( $out, $post_id ) {
		if ( tcp_is_downloadable( $post_id ) ) {
			$shopingCart = TheCartPress::getShoppingCart();
			if ( $shopingCart->exists( $post_id ) ) {
				return '';				
			}
		}
		return $out;
	}

	function tcp_product_metabox_toolbar( $post_id ) {
		if ( tcp_is_downloadable( $post_id ) ) : ?>
			<li>|</li>
			<li><a href="<?php echo TCP_ADMIN_PATH; ?>UploadFiles.php&post_id=<?php echo $post_id; ?>"><?php echo __( 'file upload', 'tcp' ); ?></a></li>
			<!--<li>|</li>
			<li><a href="<?php echo TCP_ADMIN_PATH; ?>FilesList.php&post_id=<?php echo $post_id; ?>"><?php echo __( 'files', 'tcp' ); ?></a></li>-->
		<?php endif;
	}

	function tcp_product_metabox_custom_fields( $post_id ) { ?>
		<tr valign="top">
		<th scope="row"><label for="tcp_is_downloadable"><?php _e( 'Is downloadable', 'tcp' );?>:</label></th>
				<td><input type="checkbox" name="tcp_is_downloadable" id="tcp_is_downloadable" value="yes" <?php if ( get_post_meta( $post_id, 'tcp_is_downloadable', true ) ): ?>checked <?php endif; ?> 
				onclick="if (this.checked) jQuery('.tcp_is_downloadable').show(); else jQuery('.tcp_is_downloadable').hide();"/>
				<?php if ( tcp_is_downloadable( $post_id ) ) : ?>
					<span class="description"><?php _e( 'File','tcp' );?>:<?php echo tcp_get_the_file( $post_id );?></span>
				<?php endif;?>
				</td>
			</tr>
			<?php
			if ( tcp_is_downloadable( $post_id ) )
				$style = '';
			else
				$style = 'style="display:none;"';
			?>
			<tr valign="top" class="tcp_is_downloadable" <?php echo $style;?>>
				<th scope="row"><label for="tcp_max_downloads"><?php _e( 'Max. downloads', 'tcp' );?>:</label></th>
				<td><input name="tcp_max_downloads" id="tcp_max_downloads" value="<?php echo (int)get_post_meta( $post_id, 'tcp_max_downloads', true );?>" class="regular-text tcp_count_min" type="text" min="-1" style="width:4em" maxlength="4" />
				<span class="description"><?php _e( 'If you don\'t want to set a number of maximun downloads, set this value to -1.', 'tcp' );?></span>
				</td>
			</tr>
			<tr valign="top" class="tcp_is_downloadable" <?php echo $style;?>>
				<th scope="row"><label for="tcp_days_to_expire"><?php _e( 'Days to expire', 'tcp' );?>:</label></th>
				<td><input name="tcp_days_to_expire" id="tcp_days_to_expire" value="<?php echo (int)get_post_meta( $post_id, 'tcp_days_to_expire', true );?>" class="regular-text tcp_count_min" type="text" min="-1" style="width:4em" maxlength="4" />
				<span class="description"><?php _e( 'Days to expire from the buying day. You can use -1 value.', 'tcp' );?></span>
				</td>
			</tr>
	<?php }	

	function tcp_product_metabox_save_custom_fields( $post_id ) {
		update_post_meta( $post_id, 'tcp_is_downloadable', isset( $_POST['tcp_is_downloadable'] ) ? $_POST['tcp_is_downloadable'] == 'yes' : false );
		update_post_meta( $post_id, 'tcp_max_downloads', isset( $_POST['tcp_max_downloads'] ) ? (int)$_POST['tcp_max_downloads'] : 0 );
		update_post_meta( $post_id, 'tcp_days_to_expire', isset( $_POST['tcp_days_to_expire'] ) ? (int)$_POST['tcp_days_to_expire'] : 0 );
	}

	function tcp_product_metabox_delete_custom_fields( $post_id ) {
		delete_post_meta( $post_id, 'tcp_is_downloadable' );
		delete_post_meta( $post_id, 'tcp_max_downloads' );
		delete_post_meta( $post_id, 'tcp_days_to_expire' );
	}

	function tcp_add_to_shopping_cart( $sci ) {
		if ( tcp_is_downloadable( $sci->getPostId() ) ) {
			$sci->setDownloadable( true );
			$sci->setCount( 1 );
		} else {
			$sci->setDownloadable( false );
		}
		return $sci;
	}

	function tcp_send_order_mail_to_customer_message( $message, $order_id ) {
		require_once( TCP_DAOS_FOLDER . 'Orders.class.php' );
		$order = Orders::get( $order_id );
		if ( $order && $order->customer_id == 0 && $order->status == tcp_get_completed_order_status() ) {
			require_once( TCP_DAOS_FOLDER . 'OrdersDetails.class.php' );
			$exists_downloadables = false;
			$details = OrdersDetails::getDetails( $order_id );
			$url = get_bloginfo('url') . '/wp-content/plugins/thecartpress/admin/VirtualProductDownloader.php';
			ob_start(); ?>
			<ul>
			<?php foreach( $details as $detail ) : ?>
				<?php if ( tcp_is_downloadable( $detail->post_id ) ) :
					$exists_downloadables = true;
					$uuid = tcp_get_download_uuid( $detail->order_detail_id );
					$link = add_query_arg( 'uuid', $uuid, $url );
					$link = add_query_arg( 'did', $detail->order_detail_id, $link );
					$post = get_post( $detail->post_id ); ?>
					<li><a href="<?php echo $link; ?>" target="_blank"><?php echo esc_html( $post->post_title ); ?></a></li>
				<?php endif; ?>
			<?php endforeach; ?>
			</ul>
			<?php $html = ob_get_clean();
			if ( $exists_downloadables ) {
				$message .= '<p><h2>' . __( 'Downloadable files', 'tcp' ) . '</h2>' . $html . '</p>';
				return $message;
			}
		}
		return $message;
	}

	function tcp_checkout_create_order_insert_detail( $order_id, $orders_details_id, $post_id, $ordersDetails ) {
		if ( tcp_is_downloadable( $post_id ) ) {
			$current_user = wp_get_current_user();
			if ( 0 == $current_user->ID ) {
				tcp_create_download_uuid( $orders_details_id );
			}
		}
	}

	function __construct() {
		if ( is_admin() ) {
			add_action( 'tcp_product_metabox_toolbar', array( $this, 'tcp_product_metabox_toolbar') );
			add_action( 'tcp_product_metabox_custom_fields', array( $this, 'tcp_product_metabox_custom_fields' ) );
			add_action( 'tcp_product_metabox_save_custom_fields', array( $this, 'tcp_product_metabox_save_custom_fields' ) );
			add_action( 'tcp_product_metabox_delete_custom_fields', array( $this, 'tcp_product_metabox_delete_custom_fields' ) );			
		} else {
			add_filter( 'tcp_the_add_to_cart_unit_field', array( $this, 'tcp_the_add_to_cart_unit_field' ), 1, 2 );
			add_filter( 'tcp_the_add_to_cart_items_in_the_cart', array( $this, 'tcp_the_add_to_cart_items_in_the_cart' ), 1, 2 );
			add_filter( 'tcp_the_add_to_cart_button', array( $this, 'tcp_the_add_to_cart_button' ), 1, 2 );
			add_filter( 'tcp_add_to_shopping_cart', array( $this, 'tcp_add_to_shopping_cart' ) );
		}
		add_filter( 'tcp_send_order_mail_to_customer_message', array( $this, 'tcp_send_order_mail_to_customer_message' ), 10, 2 );
		add_action( 'tcp_checkout_create_order_insert_detail', array( $this, 'tcp_checkout_create_order_insert_detail' ), 10, 4 );
	}
}

new TCPDownloadableProducts();

function tcp_create_download_uuid( $order_detail_id ) {
	require_once( TCP_CLASSES_FOLDER . 'UUID.class.php' );
	$uuid = TCPUUID::v4();
	update_order_detail_meta( $order_detail_id, 'tcp_download_uuid', $uuid );
	return $uuid;
}

function tcp_get_download_uuid( $order_detail_id ) {
	return get_order_detail_meta( $order_detail_id, 'tcp_download_uuid', true );
}
?>
