<?php
/**
 * External products
 *
 * Product type for External/Affiliate products
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

if ( ! class_exists( 'TCPExternalProduct' ) ) {

/**
 * Defines External/Affiliates products
 *
 * @since 1.3.4
 */
class TCPExternalProduct {

	static function tcp_admin_init() {
		add_filter( 'tcp_get_product_types'							, array( __CLASS__, 'tcp_get_product_types' ) );

		//metabox fields
		add_action( 'tcp_product_metabox_custom_tabs'				, array( __CLASS__, 'tcp_product_metabox_custom_tabs' ) );
		add_action( 'tcp_product_metabox_custom_fields_after_price'	, array( __CLASS__, 'tcp_product_metabox_custom_fields_after_price' ) );
		add_action( 'tcp_product_metabox_save_custom_fields'		, array( __CLASS__, 'tcp_product_metabox_save_custom_fields' ) );
		add_action( 'tcp_product_metabox_delete_custom_fields'		, array( __CLASS__, 'tcp_product_metabox_delete_custom_fields' ) );
	}

	/**
	 * Adds the External/Affiliate product type
	 *
	 * @since 1.3.4
	 */
	static function tcp_get_product_types( $types ) {
		$types['EXTERNAL'] = array( 'label' => __( 'External/Affiliates', 'tcp' ) );
		return $types;
	}

	/**
	 * Adds fields for External/Affiliate product type
	 *
	 * @since 1.3.4
	 */
	static function tcp_product_metabox_custom_fields_after_price( $post_id ) {
		$product_url = tcp_get_the_meta( '_tcp_product_url', $post_id );
		$button_text = tcp_get_the_meta( '_tcp_button_text', $post_id ); ?>
<tr id="tcp_product_url-row"valign="top" style="display:none;">
	<th scope="row">
		<label for="tcp_product_url"><?php _e( 'Product URL', 'tcp' ); ?>:</label>
	</th>
	<td>
		<input type="text" placeholder="http://" name="tcp_product_url" id="tcp_product_url" value="<?php echo $product_url; ?>" class="regular-text" />
		<p class="description"><?php _e( 'External URL to the product.', 'tcp' ); ?></p>
	</td>
</tr>
<tr id="tcp_button_text-row"valign="top" style="display:none;">
	<th scope="row">
		<label for="tcp_button_text"><?php _e( 'Button text', 'tcp' ); ?>:</label>
	</th>
	<td>
		<input type="text" placeholder="<?php _e( 'Buy product', 'tcp' ); ?>" name="tcp_button_text" id="tcp_button_text" value="<?php echo stripslashes( $button_text ); ?>" class="regular-text" />
		<p class="description"><?php _e( 'Text to be shown on the button linking to the external product.', 'tcp' ); ?></p>
	</td>
</tr>


<?php
	}

	/**
	 * Adds javascript to show/hide the fields for external/affiliate products
	 *
	 * @since 1.3.4
	 */
	static function tcp_product_metabox_custom_tabs( $post_id ) { ?>
<script>
function tcp_check_for_external_product() {
	var type = jQuery( '#tcp_type' ).val();
	if ( type == 'EXTERNAL' ) {
		jQuery('#tcp_product_url-row').show();
		jQuery('#tcp_button_text-row').show();
		//hide fields not supported by this type of product
		jQuery('#tcp_weight-row').hide();
		jQuery('#tcp_tax_id-row').hide();
		jQuery('#tcp_initial_units-row').hide();
		jQuery('#tcp_product_current_unit-row').hide();
		jQuery('a[target="tcp-stock-options"]').hide();
		jQuery('a[target="tcp-downloadable-options"]').hide();
	} else {
		jQuery('#tcp_product_url-row').hide();
		jQuery('#tcp_button_text-row').hide();
		//hide fields not supported by this type of product
		jQuery('#tcp_weight-row').show();
		jQuery('#tcp_tax_id-row').show();
		jQuery('#tcp_initial_units-row').show();
		jQuery('#tcp_product_current_unit-row').show();
		jQuery('a[target="tcp-stock-options"]').show();
		jQuery('a[target="tcp-downloadable-options"]').show();
	}
}
jQuery( '#tcp_type' ).change( function() { tcp_check_for_external_product(); } );
tcp_check_for_external_product();
</script>
	<?php }


	static function tcp_product_metabox_save_custom_fields( $post_id ) {
		$url	= isset( $_POST['tcp_product_url'] ) ? $_POST['tcp_product_url'] : '';
		$label	= isset( $_POST['tcp_button_text'] ) ? $_POST['tcp_button_text'] : '';
		update_post_meta( $post_id, '_tcp_product_url', $url );
		update_post_meta( $post_id, '_tcp_button_text', $label );
		tcp_register_string( 'TheCartPress', 'external_button_text-' . $post_id, $label );
	}

	static function tcp_product_metabox_delete_custom_fields( $post_id ) {
		delete_post_meta( $post_id, '_tcp_product_url' );
		delete_post_meta( $post_id, '_tcp_button_text' );
		tcp_unregister_string( 'TheCartPress', 'external_button_text-' . $post_id );
	}
}

add_action( 'tcp_admin_init', 'TCPExternalProduct::tcp_admin_init' );

//template functions

/**
 * Outputs the product url
 *
 * @since 1.3.4
 * @param int $post_id
 * @uses tcp_get_the_product_url
 */
function tcp_the_product_url( $post_id = false ) {
	echo tcp_get_the_product_url( $post_id );
}

	/**
	 * Returns the product url
	 *
	 * @since 1.3.4
	 * @param int $post_id
	 * @uses tcp_get_the_meta, apply_filters (tcp_get_the_product_url)
	 * @return string product url
	 */
	function tcp_get_the_product_url( $post_id = false ) {
		$product_url = tcp_get_the_meta( '_tcp_product_url', $post_id );
		return apply_filters( 'tcp_get_the_product_url', $product_url, $post_id );
	}

/**
 * Outputs the buy button text
 *
 * @since 1.3.4
 * @param int $post_id
 * @uses tcp_get_the_buy_button_text
 */
function tcp_the_buy_button_text( $post_id = false ) {
	echo tcp_get_the_product_url( $post_id );
}

	/**
	 * Returns the buy button text
	 *
	 * @since 1.3.4
	 * @param int $post_id
	 * @uses tcp_get_the_meta, apply_filters (tcp_get_the_product_url)
	 * @return string text
	 */
	function tcp_get_the_buy_button_text( $post_id = false ) {
		$text = tcp_get_the_meta( '_tcp_button_text', $post_id );
		return apply_filters( 'tcp_get_the_buy_button_text', $text, $post_id );
	}
} // class_exists check