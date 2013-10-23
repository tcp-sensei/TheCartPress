<?php
/**
 * Product custom fields Metabox
 *
 * Ouputs the fields associated to a product, as price, SKU, etc.
 *
 * @package TheCartPress
 * @subpackage Metaboxes
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

if ( ! class_exists( 'ProductCustomFieldsMetabox' ) ) {

require_once( TCP_DAOS_FOLDER . 'RelEntities.class.php' );
require_once( TCP_DAOS_FOLDER . 'Taxes.class.php' );
	
class ProductCustomFieldsMetabox {

	function __construct() {
		add_action( 'admin_init', array( $this, 'register_metabox' ) );
	}

	function register_metabox() {
		$saleable_post_types = tcp_get_saleable_post_types();
		if ( is_array( $saleable_post_types ) && count( $saleable_post_types ) )
			foreach( $saleable_post_types as $post_type )
				add_meta_box( 'tcp-product-custom-fields', __( 'Product setup', 'tcp' ), array( &$this, 'show' ), $post_type, 'normal', 'high' );
		add_action( 'save_post'		, array( $this, 'save' ), 10, 2 );
		add_action( 'delete_post'	, array( $this, 'delete' ) );
	}

	function show() {
		global $post;
		if ( ! tcp_is_saleable_post_type( $post->post_type ) ) return;
		$post_id = tcp_get_default_id( $post->ID, $post->post_type );
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;
		$lang = isset( $_REQUEST['lang'] ) ? $_REQUEST['lang'] : '';
		$source_lang = isset( $_REQUEST['source_lang'] ) ? $_REQUEST['source_lang'] : '';//isset( $_REQUEST['lang'] ) ? $_REQUEST['lang'] : '';
		$is_translation = $lang != $source_lang;
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
		if ( tcp_is_saleable_post_type( $post->post_type ) ) { 
			$links = array();
			$count = RelEntities::count( $post_id, 'PROD-PROD' );
			if ( $count > 0 ) $count = ' (' . $count . ')';
			else $count = '';
			$links[] = array(
				'url'	=> TCP_ADMIN_PATH . 'AssignedProductsList.php&post_id=' . $post_id . '&post_type_to=' . $post->post_type . '&rel_type=PROD-PROD"',
				'title'	=> __( 'For crossing sell, adds products to the current product', 'tcp' ),
				'label'	=> __( 'Related Products', 'tcp' ) . $count
			);

			$count = RelEntities::count( $post_id, 'PROD-POST' );
			if ( $count > 0 ) $count = ' (' . $count . ')';
			else $count = '';
			$links[] = array(
				'url'	=> TCP_ADMIN_PATH . 'AssignedProductsList.php&post_id=' . $post_id . '&post_type_to=post&rel_type=PROD-POST',
				'title'	=> __( 'For crossing sell, adds Post to the current product', 'tcp' ),
				'label'	=> __( 'Related Posts', 'tcp' ) . $count
			);

			$count = RelEntities::count( $post_id, 'PROD-CAT_POST' );
			if ( $count > 0 ) $count = ' (' . $count . ')';
			else $count = '';
			$links[] = array(
				'url'	=> TCP_ADMIN_PATH . 'AssignedCategoriesList.php&post_id=' . $post_id . '&rel_type=PROD-CAT_POST',
				'title'	=> __( 'For crossing sell, adds Categories of Post to the current product', 'tcp' ),
				'label'	=> __( 'Related Cat. of Posts', 'tcp' ) . $count
			);

			$count = RelEntities::count( $post_id, 'PROD-CAT_PROD' );
			if ( $count > 0 ) $count = ' (' . $count . ')';
			else $count = '';
			$links[] = array(
				'url'	=> TCP_ADMIN_PATH . 'AssignedCategoriesList.php&post_id=' . $post_id . '&rel_type=PROD-CAT_PROD',
				'title'	=> __( 'For crossing sell, adds Post to the current product', 'tcp' ),
				'label'	=> __( 'Related Cat. of Products', 'tcp' ) . $count
			);
			$links = apply_filters( 'tcp_product_custom_fields_links', $links, $post_id, $post );
			?>
<ul class="subsubsub">
<?php $separator = '';
foreach( $links as $link ) {
	echo $separator; ?>
	<li><a href="<?php echo $link['url']; ?>" title="<?php echo $link['title']; ?>"><?php echo $link['label']; ?></a></li>
	<?php if ( $separator == '' ) $separator = '<li>|</li>';
} ?>
</ul>
<div class="clear"></div>

<?php } ?>

<!--<ul class="subsubsub">
<?php //do_action( 'tcp_product_metabox_toolbar', $post_id ); ?>
</ul>
<div class="clear"></div>-->

<?php if ( $create_grouped_relation ): ?>
	<input type="hidden" name="tcp_product_parent_id" value="<?php echo $tcp_product_parent_id; ?>" />
	<input type="hidden" name="tcp_rel_type" value="<?php echo $tcp_rel_type; ?>" />
<?php endif; ?>

<?php $tabs = array(
	'tcp-price-options' => __( 'Prices', 'tcp' ),
	'tcp-advanced-options' => __( 'Advanced', 'tcp' ),
);
$tabs = apply_filters( 'tcp_product_custom_fields_tabs', $tabs );
?>
<div class="wrap">

<h4 class="nav-tab-wrapper">
<?php $active = ' nav-tab-active';
foreach( $tabs  as $tab_id => $tab ) { ?>
	<a href="#" class="nav-tab tcp-nav-tab<?php echo $active; ?>" target="<?php echo $tab_id; ?>"><?php echo $tab; ?></a>
	<?php $active = '';
} ?>
</h4>

<div class="form-wrap">
	<?php wp_nonce_field( 'tcp_noncename', 'tcp_noncename' ); ?>

<div id="tcp-price-options">
	<table class="form-table">
	<tbody>
	<tr valign="top">
		<th scope="row">
			<label for="tcp_type"><?php _e( 'Type', 'tcp' ); ?>:</label>
		</th>
		<td>
			<?php $types_for = array();
			foreach( tcp_get_product_types() as $id => $type ) $types_for[$id] = $type['label'];
			tcp_html_select( 'tcp_type', $types_for, tcp_get_the_product_type( $post_id ) ); ?>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<label for="tcp_price"><?php _e( 'Price', 'tcp' ); ?>:</label>
		</th>
		<td>
			<input type="text" min="0" step="any" placeholder="<?php tcp_get_number_format_example(); ?>" name="tcp_price" id="tcp_price" value="<?php echo tcp_number_format( tcp_get_the_price( $post_id, false ) ); ?>" class="regular-text" style="width:12em !important" />&nbsp;<?php tcp_the_currency(); ?> <?php tcp_price_include_tax_message(); ?>
			<p class="description"><?php printf( __( 'Current number format is %s', 'tcp' ), tcp_get_number_format_example( 9999.99, false ) ); ?></p>
		</td>
	</tr>

	<?php do_action( 'tcp_product_metabox_custom_fields_after_price', $post_id ); ?>

	<tr valign="top">
		<th scope="row"><label for="tcp_tax_id"><?php _e( 'Tax', 'tcp' ); ?>:</label></th>
		<td>
			<select name="tcp_tax_id" id="tcp_tax_id">
				<option value="0"><?php _e( 'No tax', 'tcp' ); ?></option>
			<?php $tax_id = tcp_get_the_tax_id( $post_id );
			$taxes = Taxes::getAll();
			foreach ( $taxes as $tax ) : ?>
				<option value="<?php echo $tax->tax_id; ?>" <?php selected( $tax_id, $tax->tax_id ); ?>><?php echo $tax->title; ?></option>
			<?php endforeach; ?>
			</select>
			<a href="admin.php?page=thecartpress/admin/TaxesList.php"><?php _e( 'Taxes', 'tcp' ); ?></a>
		</td>
	</tr>
	</tbody>
	</table>
</div><!-- #tcp-price-options -->

<div id="tcp-advanced-options" style="display:none;">
	<table class="form-table">
	<tbody>

	<tr valign="top">
		<th scope="row"><label for="tcp_sku"><?php _e( 'SKU', 'tcp' ); ?>:</label></th>
		<td><input name="tcp_sku" id="tcp_sku" value="<?php echo htmlspecialchars( get_post_meta( $post_id, 'tcp_sku', true ) ); ?>" class="regular-text" type="text" style="width:12em"></td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="tcp_weight"><?php _e( 'Weight', 'tcp' ); ?>:</label></th>
		<td><input type="text" min="0" step="0.01" placeholder="<?php tcp_number_format_example(); ?>" name="tcp_weight" id="tcp_weight" value="<?php echo tcp_number_format( (float)tcp_get_the_weight( $post_id ) ); ?>" class="regular-text" style="width:12em" />&nbsp;<?php tcp_the_unit_weight(); ?>
		<span class="description"><?php printf( __( 'Current number format is %s', 'tcp'), tcp_get_number_format_example( 9999.99, false ) ); ?></span></td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="tcp_order"><?php _e( 'Order (in loops/catalogue)', 'tcp' ); ?>:</label></th>
		<td><input name="tcp_order" id="tcp_order" value="<?php echo htmlspecialchars( get_post_meta( $post_id, 'tcp_order', true ) ); ?>" class="regular-text" type="text" style="width:4em">
		<span class="description"><?php _e( 'Numerical position to sort the product in lists of products.', 'tcp' ); ?></span></td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="tcp_initial_units"><?php _e( 'Initial Quantity', 'tcp' ); ?>:</label></th>
		<td><input type="text" min="0" placeholder="1" name="tcp_initial_units" id="tcp_initial_units" value="<?php tcp_the_initial_units(); ?>" class="regular-text" style="width:12em" />
		<span class="description"><?php _e( 'Initial number of units to display in the buy button. If the product is displayed inside a grouped product this value will be omitted, using the unit field defined in the grouped list.', 'tcp'); ?></span></td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="tcp_is_visible"><?php _e( 'Is visible (in loops/catalogue)', 'tcp' ); ?>:</label></th>
		<td>
			<?php if ( $create_grouped_relation ) {
				$is_visible = false;
			} elseif ( tcp_get_the_product_type( $post_id ) == '' ) {
				$is_visible = true; //by default
			} else {
				$is_visible = tcp_is_visible( $post_id );
			} ?>
			<input type="checkbox" name="tcp_is_visible" id="tcp_is_visible" value="yes" <?php checked( $is_visible, true ); ?> />
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="tcp_hide_buy_button"><?php _e( 'Hide buy button', 'tcp' ); ?>:</label></th>
		<?php $tcp_hide_buy_button = get_post_meta( $post_id, 'tcp_hide_buy_button', true ); ?>
		<td><input type="checkbox" name="tcp_hide_buy_button" id="tcp_hide_buy_button" <?php checked( $tcp_hide_buy_button, true ); ?> />
		<span class="description"><?php _e( 'Allow to hide the buy button for this product', 'tcp' ); ?></span></td>
	</tr>

	<?php $tcp_exclude_range = get_post_meta( $post_id, 'tcp_exclude_range', true ); ?>
	<tr valign="top">
		<th scope="row"><label for="tcp_exclude_range"><?php _e( 'Exclude for range prices', 'tcp' ); ?>:</label></th>
		<td>
			<input type="checkbox" name="tcp_exclude_range" id="tcp_exclude_range" value="yes" <?php checked( $tcp_exclude_range, true ); ?> />
			<span class="description"><?php _e( 'If the product is assigned to a Grouped product, this option excludes the product from the range price of the parent product.', 'tcp' ); ?></span>
		</td>
	</tr>

	<?php do_action( 'tcp_product_metabox_custom_fields', $post_id ); ?>

	</tbody>
	</table>
</div><!-- #tcp-advanced-options -->

<?php do_action( 'tcp_product_metabox_custom_tabs', $post_id ); ?>

</div> <!-- form-wrap -->
</div> <!-- wrap -->
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
			//$terms = wp_get_post_terms( $tcp_product_parent_id, TCP_SUPPLIER_TAG, array( 'fields' => 'ids' ) );
			//wp_set_post_terms( $post_id, $terms, TCP_SUPPLIER_TAG );
		}
		$tax_id = isset( $_POST['tcp_tax_id'] ) ? (int)$_POST['tcp_tax_id'] : 0;
		if ( $tax_id > 0 ) update_post_meta( $post_id, 'tcp_tax_id',  $tax_id );
		else update_post_meta( $post_id, 'tcp_tax_id', 0 );
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
		if ( 'GROUPED' == $type) {
			$price = 0;
			$tcp_initial_units = 0;
		} else {
			$price = isset( $_POST['tcp_price'] ) ? $_POST['tcp_price'] : 0;
			$price = tcp_input_number( $price );
			$tcp_initial_units = isset( $_POST['tcp_initial_units'] ) ? (int)$_POST['tcp_initial_units'] : 0;
		}
		update_post_meta( $post_id, 'tcp_price', $price );
		update_post_meta( $post_id, 'tcp_initial_units', $tcp_initial_units );

		$weight = isset( $_POST['tcp_weight'] ) ? $_POST['tcp_weight'] : 0;
		$weight = tcp_input_number( $weight );
		update_post_meta( $post_id, 'tcp_weight', $weight );
		update_post_meta( $post_id, 'tcp_order', isset( $_POST['tcp_order'] ) ? (int)$_POST['tcp_order'] : '' );
		update_post_meta( $post_id, 'tcp_sku', isset( $_POST['tcp_sku'] ) ? $_POST['tcp_sku'] : '' );

		$translations = tcp_get_all_translations( $post_id, get_post_type( $post_id ) );
		if ( is_array( $translations ) && count( $translations ) > 0 )
			foreach( $translations as $translation )
				if ( $translation->element_id != $post_id ) {
					update_post_meta( $translation->element_id, 'tcp_is_visible', $is_visible );// $_POST['tcp_is_visible'] ) ? $_POST['tcp_is_visible'] == 'yes' : false );
					update_post_meta( $translation->element_id, 'tcp_hide_buy_button', isset( $_POST['tcp_hide_buy_button'] ) );
					update_post_meta( $translation->element_id, 'tcp_order', isset( $_POST['tcp_order'] ) ? (int)$_POST['tcp_order'] : '' );
					update_post_meta( $translation->element_id, 'tcp_price', isset( $_POST['tcp_price'] ) ? (float)$_POST['tcp_price'] : 0 );
					do_action( 'tcp_product_metabox_save_custom_fields_translations', $translation->element_id, $post_id );
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
		delete_post_meta( $post_id, 'tcp_initial_units' );
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
			require_once( TCP_CLASSES_FOLDER . 'TheCartPressSearchEngine.class.php' );
			TheCartPressSearchEngine::refresh();
		}
	}
}

new ProductCustomFieldsMetabox();
} // class_exists check