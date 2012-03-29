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

require_once( TCP_DAOS_FOLDER . 'RelEntities.class.php' );
require_once( TCP_DAOS_FOLDER . 'Taxes.class.php' );
	
class ProductCustomFieldsMetabox {

	function register_metabox() {
		$saleable_post_types = tcp_get_saleable_post_types();
		if ( is_array( $saleable_post_types ) && count( $saleable_post_types ) )
			foreach( $saleable_post_types as $post_type )
				add_meta_box( 'tcp-product-custom-fields', __( 'Product setup', 'tcp' ), array( $this, 'show' ), $post_type, 'normal', 'high' );
		add_action( 'save_post', array( $this, 'save' ), 1, 2 );
		add_action( 'delete_post', array( $this, 'delete' ) );
	}

	function show() {
		global $post;
		if ( ! tcp_is_saleable_post_type( $post->post_type ) ) return;
		$post_id = tcp_get_default_id( $post->ID, $post->post_type );
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;
		$lang				= isset( $_REQUEST['lang'] ) ? $_REQUEST['lang'] : '';
		$source_lang		= isset( $_REQUEST['source_lang'] ) ? $_REQUEST['source_lang'] : '';//isset( $_REQUEST['lang'] ) ? $_REQUEST['lang'] : '';
		$is_translation		= $lang != $source_lang;
		if ( $is_translation && $post_id == $post->ID ) {
			_e( 'After saving the title and content, you will be able to edit the specific fields of the product.', 'tcp' );
			return;
		}
		$tcp_product_parent_id = isset( $_REQUEST['tcp_product_parent_id'] ) ? $_REQUEST['tcp_product_parent_id'] : 0;
		if ( $tcp_product_parent_id > 0 ) {
			$create_grouped_relation = true;
			$tcp_rel_type = isset( $_REQUEST['rel_type'] ) ? $_REQUEST['rel_type'] : 'GROUPED';
		} else {
			$create_grouped_relation = false;
			$tcp_rel_type = tcp_get_the_product_type();
			if ( $post_id > 0 )
				$tcp_product_parent_id = RelEntities::getParent( $post_id );
		}
		if ( $post->post_type == TCP_PRODUCT_POST_TYPE ) : ?>
		<ul class="subsubsub">
			<?php $count = RelEntities::count( $post_id, 'PROD-PROD' );
			if ( $count > 0 ) $count = ' (' . $count . ')';
			else $count = ''; ?>
			<li><a href="<?php echo TCP_ADMIN_PATH;?>AssignedProductsList.php&post_id=<?php echo $post_id;?>&post_type_to=tcp_product&rel_type=PROD-PROD" title="<?php _e( 'For crossing sell, adds products to the current product', 'tcp' ); ?>"><?php _e( 'related products', 'tcp' );?> <?php echo $count;?></a></li>
			<?php $count = RelEntities::count( $post_id, 'PROD-POST' );
			if ( $count > 0 ) $count = ' (' . $count . ')';
			else $count = ''; ?>
			<li>|</li>
			<li><a href="<?php echo TCP_ADMIN_PATH;?>AssignedProductsList.php&post_id=<?php echo $post_id;?>&post_type_to=post&rel_type=PROD-POST"  title="<?php _e( 'For crossing sell, adds post to the current product', 'tcp' ); ?>"><?php _e( 'related posts', 'tcp' );?> <?php echo $count;?></a></li>
			<?php $count = RelEntities::count( $post_id, 'PROD-CAT_POST' );
			if ( $count > 0 ) $count = ' (' . $count . ')';
			else $count = ''; ?>
			<li>|</li>
			<li><a href="<?php echo TCP_ADMIN_PATH;?>AssignedCategoriesList.php&post_id=<?php echo $post_id;?>&rel_type=PROD-CAT_POST"  title="<?php _e( 'For crossing sell, adds post to the current product', 'tcp' ); ?>"><?php _e( 'related cat. of posts', 'tcp' );?> <?php echo $count;?></a></li>
			<?php $count = RelEntities::count( $post_id, 'PROD-CAT_PROD' );
			if ( $count > 0 ) $count = ' (' . $count . ')';
			else $count = ''; ?>
			<li>|</li>
			<li><a href="<?php echo TCP_ADMIN_PATH;?>AssignedCategoriesList.php&post_id=<?php echo $post_id;?>&rel_type=PROD-CAT_PROD"  title="<?php _e( 'For crossing sell, adds post to the current product', 'tcp' ); ?>"><?php _e( 'related cat. of products', 'tcp' );?> <?php echo $count;?></a></li>
			<!--<li>|</li>
			<li><a href="<?php echo TCP_ADMIN_PATH;?>CopyProduct.php&post_id=<?php echo $post_id;?>"><?php _e( 'copy product', 'tcp' );?></a></li>
			-->
		</ul>
		<div class="clear"></div>
		<?php endif; ?>
		<ul class="subsubsub">
		<?php do_action( 'tcp_product_metabox_toolbar', $post_id );?>
		</ul>
		<?php if ( $create_grouped_relation ): ?>
			<input type="hidden" name="tcp_product_parent_id" value="<?php echo $tcp_product_parent_id; ?>" />
			<input type="hidden" name="tcp_rel_type" value="<?php echo $tcp_rel_type; ?>" />
		<?php endif;?>
		<div class="form-wrap">
			<?php wp_nonce_field( 'tcp_noncename', 'tcp_noncename' );?>
			<table class="form-table"><tbody>
			<tr valign="top">
				<th scope="row"><label for="tcp_type"><?php _e( 'Type', 'tcp' );?>:</label></th>
				<td><?php tcp_html_select( 'tcp_type', tcp_get_product_types(), tcp_get_the_product_type( $post_id ) ); ?></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="tcp_price"><?php _e( 'Price', 'tcp' );?>:</label></th>
				<td><input type="text" min="0" step="any" placeholder="<?php tcp_get_number_format_example(); ?>" name="tcp_price" id="tcp_price" value="<?php echo tcp_number_format( tcp_get_the_price( $post_id ) );?>" class="regular-text tcp_count" style="width:12em" />&nbsp;<?php tcp_the_currency(); ?> <?php tcp_price_include_tax_message();?>
				<p class="description"><?php printf( __( 'Current number format is %s', 'tcp'), tcp_get_number_format_example( 9999.99, false ) ); ?></p></td>
			</tr>
			<?php do_action( 'tcp_product_metabox_custom_fields_after_price', $post_id );?>
			<tr valign="top">
				<th scope="row"><label for="tcp_tax_id"><?php _e( 'Tax', 'tcp' );?>:</label></th>
				<td>
					<select name="tcp_tax_id" id="tcp_tax_id">
						<option value="0"><?php _e( 'No tax', 'tcp' );?></option>
					<?php $tax_id = tcp_get_the_tax_id( $post_id );
					$taxes = Taxes::getAll();
					foreach ( $taxes as $tax ) : ?>
						<option value="<?php echo $tax->tax_id;?>" <?php selected( $tax_id, $tax->tax_id );?>><?php echo $tax->title;?></option>
					<?php endforeach;?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="tcp_weight"><?php _e( 'Weight', 'tcp' );?>:</label></th>
				<td><input type="text" min="0" step="0.01" placeholder="<?php tcp_number_format_example(); ?>" name="tcp_weight" id="tcp_weight" value="<?php echo tcp_number_format( (float)tcp_get_the_weight( $post_id ) );?>" class="regular-text tcp_count" style="width:12em" />&nbsp;<?php tcp_the_unit_weight(); ?>
				<p class="description"><?php printf( __( 'Current number format is %s', 'tcp'), tcp_get_number_format_example( 9999.99, false ) ); ?></p></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="tcp_is_visible"><?php _e( 'Is visible (in loops/catalogue)', 'tcp' );?>:</label></th>
				<td><?php
					if ( $create_grouped_relation ) {
						$is_visible = false;
					} elseif ( tcp_get_the_product_type( $post_id ) == '' ) {
						$is_visible = true; //by default
					} else {
						$is_visible = tcp_is_visible( $post_id );
					}
				?><input type="checkbox" name="tcp_is_visible" id="tcp_is_visible" value="yes" <?php checked( $is_visible, true );?> /></td>
			</tr>
			
			<tr valign="top">
				<th scope="row"><label for="tcp_hide_buy_button"><?php _e( 'Hide buy button', 'tcp' );?>:</label></th>
				<?php $tcp_hide_buy_button = get_post_meta( $post_id, 'tcp_hide_buy_button', true );?>
				<td><input type="checkbox" name="tcp_hide_buy_button" id="tcp_hide_buy_button" <?php checked( $tcp_hide_buy_button, true );?> />
				<p class="description"><?php _e( 'Allow to hide the buy button for this product', 'tcp' ); ?></p></td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="tcp_exclude_range"><?php _e( 'Exclude for range prices', 'tcp' );?>:</label></th>
				<?php $tcp_exclude_range = get_post_meta( $post_id, 'tcp_exclude_range', true );?>
				<td><input type="checkbox" name="tcp_exclude_range" id="tcp_exclude_range" <?php checked( $tcp_exclude_range, true );?> />
				<span class="description"><?php _e( 'If the product is assigned to a Grouped product, this options exclude the product from the range price of the parent product.', 'tcp' );?></span></td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="tcp_order"><?php _e( 'Order (in loops/catalogue)', 'tcp' );?>:</label></th>
				<td><input name="tcp_order" id="tcp_order" value="<?php echo htmlspecialchars( get_post_meta( $post_id, 'tcp_order', true ) );?>" class="regular-text tcp_count" type="text" style="width:4em">
				<span class="description"><?php _e( 'Numerical order.', 'tcp' );?></span></td>
			</tr>
			
			<tr valign="top">
				<th scope="row"><label for="tcp_sku"><?php _e( 'SKU', 'tcp' );?>:</label></th>
				<td><input name="tcp_sku" id="tcp_sku" value="<?php echo htmlspecialchars( get_post_meta( $post_id, 'tcp_sku', true ) );?>" class="regular-text" type="text" style="width:12em"></td>
			</tr>
			<?php do_action( 'tcp_product_metabox_custom_fields', $post_id );?>
			</tbody></table>
		</div> <!-- form-wrap -->
		<?php
	}

	function save( $post_id, $post ) {
		if ( ! wp_verify_nonce( isset( $_POST['tcp_noncename'] ) ? $_POST['tcp_noncename'] : '', 'tcp_noncename' ) ) return array( $post_id, $post );
		if ( ! tcp_is_saleable_post_type( $post->post_type ) ) return array( $post_id, $post );
		if ( ! current_user_can( 'edit_post', $post_id ) ) return array( $post_id, $post );
		$post_id = tcp_get_default_id( $post_id, $post->post_type );
		$tcp_product_parent_id = isset( $_REQUEST['tcp_product_parent_id'] ) ? $_REQUEST['tcp_product_parent_id'] : 0;
		$create_grouped_relation = $tcp_product_parent_id > 0;
		if ( $create_grouped_relation ) {
			$rel_type = isset( $_REQUEST['tcp_rel_type'] ) ? $_REQUEST['tcp_rel_type'] : 'GROUPED';
			if ( ! RelEntities::exists( $tcp_product_parent_id, $post_id, $rel_type ) ) 
				RelEntities::insert( $tcp_product_parent_id, $post_id, $rel_type );
			$args = array( 'fields' => 'ids' );
			$terms = wp_get_post_terms( $tcp_product_parent_id, TCP_PRODUCT_CATEGORY, array( 'fields' => 'ids' ) );
			wp_set_post_terms( $post_id, $terms, TCP_PRODUCT_CATEGORY );
			$terms = wp_get_post_terms( $tcp_product_parent_id, TCP_PRODUCT_TAG, array( 'fields' => 'names' ) );
			wp_set_post_terms( $post_id, $terms, TCP_PRODUCT_TAG );
			$terms = wp_get_post_terms( $tcp_product_parent_id, TCP_SUPPLIER_TAG, array( 'fields' => 'ids' ) );
			wp_set_post_terms( $post_id, $terms, TCP_SUPPLIER_TAG );
		}
		$tax_id = isset( $_POST['tcp_tax_id'] ) ? (int)$_POST['tcp_tax_id'] : 0;
		if ( $tax_id > 0 ) {
			$tax = Taxes::get( $tax_id );
			update_post_meta( $post_id, 'tcp_tax_id',  $tax_id );
		} else {
			update_post_meta( $post_id, 'tcp_tax_id', 0 );
		}
		update_post_meta( $post_id, 'tcp_hide_buy_button', isset( $_POST['tcp_hide_buy_button'] ) );
		update_post_meta( $post_id, 'tcp_exclude_range', isset( $_POST['tcp_exclude_range'] ) );
		if ( isset( $_POST['tcp_type'] ) ) {
			$type = $_POST['tcp_type'];
			$is_visible = isset( $_POST['tcp_is_visible'] ) ? $_POST['tcp_is_visible'] == 'yes' : false;
		} else {
			$type = 'SIMPLE';
			$is_visible = true;
		}
		update_post_meta( $post_id, 'tcp_type', $type );
		update_post_meta( $post_id, 'tcp_is_visible', $is_visible );
		if ( $type == 'GROUPED' )
			$price = 0;
		else {
			$price = isset( $_POST['tcp_price'] ) ? $_POST['tcp_price'] : 0;
			$price = tcp_input_number( $price );
		}
		update_post_meta( $post_id, 'tcp_price', $price );
		$weight = isset( $_POST['tcp_weight'] ) ? (float)$_POST['tcp_weight'] : 0;
		$weight = tcp_input_number( $weight );
		update_post_meta( $post_id, 'tcp_weight', $weight );
		update_post_meta( $post_id, 'tcp_order', isset( $_POST['tcp_order'] ) ? (int)$_POST['tcp_order'] : '' );
		update_post_meta( $post_id, 'tcp_sku', isset( $_POST['tcp_sku'] ) ? $_POST['tcp_sku'] : '' );
		
		$translations = tcp_get_all_translations( $post_id, get_post_type( $post_id ) );
		if ( is_array( $translations ) && count( $translations ) > 0 )
			foreach( $translations as $translation )
				if ( $translation->element_id != $post_id ) {
					update_post_meta( $translation->element_id, 'tcp_is_visible', isset( $_POST['tcp_is_visible'] ) ? $_POST['tcp_is_visible'] == 'yes' : false );
					update_post_meta( $translation->element_id, 'tcp_hide_buy_button', isset( $_POST['tcp_hide_buy_button'] ) );
					update_post_meta( $translation->element_id, 'tcp_order', isset( $_POST['tcp_order'] ) ? (int)$_POST['tcp_order'] : '' );
					update_post_meta( $translation->element_id, 'tcp_price', isset( $_POST['tcp_price'] ) ? (float)$_POST['tcp_price'] : 0 );
				}
		do_action( 'tcp_product_metabox_save_custom_fields', $post_id );
		$this->refreshMoira();
		return array( $post_id, $post );
	}

	function delete( $post_id ) {
		$post = get_post( $post_id );
		if ( ! tcp_is_saleable_post_type( $post->post_type ) ) return $post_id;
		if ( !current_user_can( 'edit_post', $post_id ) ) return $post_id;
		$post_id = tcp_get_default_id( $post_id, $post->post_type );
		RelEntities::deleteAll( $post_id );
		RelEntities::deleteAllTo( $post_id );
		delete_post_meta( $post_id, 'tcp_price' );
		delete_post_meta( $post_id, 'tcp_tax_id' );
		delete_post_meta( $post_id, 'tcp_type' );
		delete_post_meta( $post_id, 'tcp_is_visible' );
		delete_post_meta( $post_id, 'tcp_hide_buy_button' );
		delete_post_meta( $post_id, 'tcp_weight' );
		delete_post_meta( $post_id, 'tcp_sku' );
		delete_post_meta( $post_id, 'tcp_order' );
		$translations = tcp_get_all_translations( $post_id, get_post_type( $post_id ) );
		if ( is_array( $translations ) && count( $translations ) > 0 ) {
			foreach( $translations as $translation ) {
				if ( $translation->element_id != $post_id ) {
					wp_delete_post( $post_id );
				}
			}
		}
		$options = RelEntities::select( $post_id, 'OPTIONS' );
		if ( is_array( $options ) ) {
			foreach( $options as $option ) {
				wp_delete_post( $option->id_to, true );
			}
		}
		RelEntities::deleteAll( $post_id, 'OPTIONS' );
		do_action( 'tcp_product_metabox_delete_custom_fields', $post_id );
		$this->refreshMoira();
		return $post_id;
	}

	function refreshMoira() {
		global $thecartpress;
		$search_engine_activated = isset( $thecartpress->settings['search_engine_activated'] ) ? $thecartpress->settings['search_engine_activated'] : true;
		if ( $search_engine_activated ) {
			require_once( dirname( dirname( __FILE__ ) ) . '/classes/TheCartPressSearchEngine.class.php' );
			TheCartPressSearchEngine::refresh();
		}
	}
	
	function __construct() {
		add_action( 'admin_init', array( $this, 'register_metabox' ) );
	}
}

new ProductCustomFieldsMetabox();
?>
