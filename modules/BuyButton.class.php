<?php
/**
 * Buy button
 *
 * Adds important features to the buy buttons, allowing to set a template, allowing to select the buy button color, etc.
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

if ( ! class_exists( 'TCPBuyButton' ) ) {

class TCPBuyButton {

	function __construct() {
		//WordPress hooks
		add_action( 'tcp_admin_menu'			, array( $this, 'tcp_admin_menu' ), 30 );
		add_action( 'tcp_admin_init'			, array( $this, 'tcp_admin_init' ) );

		//TheCartPress hooks
		add_filter( 'tcp_get_buybutton_template', array( $this, 'tcp_get_buybutton_template' ), 10, 2 );
	}

	function tcp_admin_init() {
		//Attach this new setting to the default metabox
		add_action( 'tcp_product_metabox_custom_fields'				, array( $this, 'tcp_product_metabox_custom_fields' ) );
		add_action( 'tcp_product_metabox_save_custom_fields'		, array( $this, 'tcp_product_metabox_save_custom_fields' ) );
		add_action( 'tcp_product_metabox_delete_custom_fields'		, array( $this, 'tcp_product_metabox_delete_custom_fields' ) );

		//Color and size
		add_action( 'tcp_theme_compatibility_settings_page'			, array( $this, 'tcp_theme_compatibility_settings_page' ), 10, 2 );
		add_filter( 'tcp_theme_compatibility_unset_settings_action'	, array( $this, 'tcp_theme_compatibility_unset_settings_action' ), 10, 2 );
		add_filter( 'tcp_theme_compatibility_settings_action'		, array( $this, 'tcp_theme_compatibility_settings_action' ), 10, 2 );
	}

	/**
	 * Allows to display the buy button of the given product id
	 * 
	 * @param number $post_id
	 * @param string $echo
	 * @return string
	 */
	static function show( $post_id = 0, $echo = true  ) {
		//loads the template of the given product 
		$template = TCPBuyButton::get_template( $post_id );
		//Allows to change the template by a third plugin or theme
		$custom_template = apply_filters( 'tcp_get_buybutton_template', $template, $post_id );
		if ( file_exists( $custom_template ) )  $template = $custom_template;
		//Runs the template
		ob_start();
		if ( $template ) include( $template );
		$out = ob_get_clean();
		if ( $echo ) echo $out;
		else return $out;
	}

	/**
	 * Returns the template for the given product
	 * 
	 * @param number $post_id
	 * @return string|boolean
	 */
	static private function get_template( $post_id = 0 ) {
		//if no post id, then get the current post id
		if ( $post_id == 0 ) $post_id = get_the_ID();

		//Search for the next templates
		$post_type		= get_post_type( $post_id );
		$product_type	= strtolower( tcp_get_the_product_type( $post_id ) );//Simple, Grouped, external, etc.
		$templates		= array( 
			'tcp_buybutton-' . $product_type . '-' . $post_type . '.php',
			'tcp_buybutton-' . $product_type . '.php',
			'tcp_buybutton.php',
			
		);
		$template = locate_template( $templates );
		//if the theme has not any of this templates, search for them in TheCartPress
		if ( strlen( $template ) == 0 ) {
			foreach ( $templates as $template ) {
				$template = TCP_THEMES_TEMPLATES_FOLDER . $template;
				if ( file_exists( $template ) ) return $template;
			}
		} else {
			return $template;
		}
		return false;
	}

	function tcp_admin_menu() {
		global $thecartpress;
		if ( ! $thecartpress->get_setting( 'disable_ecommerce' ) ) {
			add_submenu_page( $thecartpress->get_base_appearance(), __( 'Buy Buttons', 'tcp' ), __( 'Buy Buttons', 'tcp' ), 'tcp_edit_orders', TCP_ADMIN_FOLDER . 'BuyButtonList.class.php' );
		}
	}

	static function get_buy_buttons() {
		//Older implementation...locate_template
		$paths = array();
		$paths[] = array(
			'label'	=> __( 'Theme' ),
			'path'	=> get_stylesheet_directory() . '/tcp_buybutton*.php',
		);
		if ( get_stylesheet_directory() != get_template_directory() ) $paths[] = array(
			'label'	=> __( 'Parent theme', 'tcp' ),
			'path'	=> get_template_directory() . '/tcp_buybutton*.php',
		);
		$paths[] = array(
			'label'	=> __( 'Plugin' ),
			'path'	=> TCP_THEMES_TEMPLATES_FOLDER . 'tcp_buybutton*.php',
		);
		$paths = apply_filters( 'tcp_get_buy_buttons_paths', $paths );
		$buy_buttons = array();
		foreach( $paths as $path ) {
			$filenames = glob( $path['path'] );
			if ( $filenames != false ) {
				foreach( $filenames as $filename ) {
					$buy_buttons[] = array(
						'label'	=> $path['label'] . ': ' . basename( $filename, '.php' ),
						'path'	=> $filename,
					);
				}
			}
		}
		return $buy_buttons;
	}

	/**
	 * Allows to add a dropdown list to select the buy button template to display this product 
	 * 
	 * @param $post_id, given product id
	 */
	function tcp_product_metabox_custom_fields( $post_id ) {
		$selected_buy_button = get_post_meta( $post_id, 'tcp_selected_buybutton', true ); ?>
		
<tr valign="top">
	<th scope="row">
		<label for="tcp_selected_buybutton"><?php _e( 'Buy button', 'tcp' );?>:</label>
	</th>
	<td>
		<?php $buy_buttons = TCPBuyButton::get_buy_buttons(); ?>
		<select name="tcp_selected_buybutton" id="tcp_selected_buybutton">
			<option value="" <?php selected( '', $selected_buy_button ); ?>><?php _e( 'Default', 'tcp' ); ?></option>
		<?php foreach( $buy_buttons as $buy_button ) : ?>
			<option value="<?php echo $buy_button['path']; ?>" <?php selected( $buy_button['path'], $selected_buy_button ); ?>>
			<?php echo $buy_button['label']; ?>
			</option>
		<?php endforeach; ?>
		</select>
	</td>
</tr>
	<?php }

	function tcp_product_metabox_save_custom_fields( $post_id ) {
		update_post_meta( $post_id, 'tcp_selected_buybutton', isset( $_POST['tcp_selected_buybutton'] ) ? $_POST['tcp_selected_buybutton'] : '' );
	}

	function tcp_product_metabox_delete_custom_fields( $post_id ) {
		delete_post_meta( $post_id, 'tcp_selected_buybutton' );
	}

	function tcp_get_buybutton_template( $template, $post_id ) {
		$selected_buy_button = get_post_meta( $post_id, 'tcp_selected_buybutton', true );
		if ( $selected_buy_button ) {
			return $selected_buy_button;
		}
		$post_type = get_post_type( $post_id );
		$product_type = tcp_get_the_product_type( $post_id );
		$selected_buy_button = get_option( 'tcp_buy_button_template-' .  $post_type . '-' . $product_type, '' );
		if ( $selected_buy_button ) {
			return $selected_buy_button;
		}
		return $template;
	}

	function tcp_theme_compatibility_settings_page( $suffix, $thecartpress ) {
		$colors = array(
			''							=> __( 'Theme default', 'tcp' ),
			'tcp-btn tcp-btn-default'	=> __( 'Default', 'tcp' ),
			'tcp-btn tcp-btn-primary'	=> __( 'Blue', 'tcp' ),
			'tcp-btn tcp-btn-success'	=> __( 'Green', 'tcp' ),
			'tcp-btn tcp-btn-info'		=> __( 'Cyan', 'tcp' ),
			'tcp-btn tcp-btn-warning'	=> __( 'Orange', 'tcp' ),
			'tcp-btn tcp-btn-danger'	=> __( 'Red', 'tcp' ),
			'tcp-btn tcp-btn-link'		=> __( 'Link', 'tcp' ),
		);
		$colors = apply_filters( 'tcp_buy_buttons_colors', $colors );
		$sizes	= array(
			''				=> __( 'Theme default', 'tcp' ),
			'tcp-btn-xs'	=> __( 'Extra Small', 'tcp' ),
			'tcp-btn-sm'	=> __( 'Small', 'tcp' ),
			'tcp-btn-lg'	=> __( 'Large', 'tcp' ),
		);
		$sizes = apply_filters( 'tcp_buy_buttons_sizes', $sizes );
		global $thecartpress;
		$buy_button_color	= $thecartpress->get_setting( 'buy_button_color' );
		$buy_button_size	= $thecartpress->get_setting( 'buy_button_size' );
		$buy_button_grouped	= $thecartpress->get_setting( 'buy_button_grouped' );
?>
<h3 class="hndle"><?php _e( 'Button Styles', 'tcp' ); ?></h3>

<div class="postbox">

<table class="form-table">
<tbody>

<tr valign="top">
	<th scope="row">
		<label for="buy_button_colors"><?php _e( 'Colors', 'tcp' ); ?></label>
	</th>
	<td>
		<select id="buy_button_color" name="buy_button_color">
		<?php foreach( $colors as $class => $color ) : ?>
			<option value="<?php echo $class; ?>" <?php selected( $class, $buy_button_color ); ?>><?php echo $color; ?></option>
		<?php endforeach; ?>
		</select>
		<script>
		jQuery( '#buy_button_color' ).change( function() {
			var colors = jQuery( '#buy_button_color' ).val();
			var sizes = jQuery( '#buy_button_size' ).val();
			jQuery( '#tcp_btn_example' ).removeClass().addClass( colors ).addClass( sizes );
		} );
		</script>
		<span class="description tcpf">
			<?php _e( 'Example ', 'tcp' ); ?>
			<button id="tcp_btn_example" type="submit" class="<?php echo $buy_button_color, ' ', $buy_button_size; ?>"/><?php _e( 'Add to Cart', 'tcp' ); ?></button>
		</span>
	</td>
</tr>

<tr valign="top">
	<th scope="row">
		<label for="buy_button_size"><?php _e( 'Sizes', 'tcp' ); ?></label>
	</th>
	<td>
		<select id="buy_button_size" name="buy_button_size">
		<?php foreach( $sizes as $class => $size ) : ?>
			<option value="<?php echo $class; ?>" <?php selected( $class, $buy_button_size ); ?>><?php echo $size; ?></option>
		<?php endforeach; ?>
		</select>
		<script>
		jQuery( '#buy_button_size' ).change( function() {
			var colors = jQuery( '#buy_button_color' ).val();
			var sizes = jQuery( '#buy_button_size' ).val();
			jQuery( '#tcp_btn_example' ).removeClass().addClass( colors ).addClass( sizes );
		} );
		</script>
	</td>
</tr>

<!--<tr valign="top">
	<th scope="row">
		<label for="buy_button_grouped"><?php _e( 'Grouped buttons', 'tcp' ); ?></label>
	</th>
	<td>
		<input type="checkbox" id="buy_button_grouped" name="buy_button_grouped" <?php checked( $buy_button_grouped ); ?>>
	</td>
</tr>-->
</tbody>
</table>
</div><!-- .postbox -->
		<?php
	}

	function tcp_theme_compatibility_unset_settings_action( $settings, $suffix ) {
		unset( $settings['buy_button_color' . $suffix] );
		unset( $settings['buy_button_size' . $suffix] );
		unset( $settings['buy_button_grouped' . $suffix] );
		return $settings;
	}

	function tcp_theme_compatibility_settings_action( $settings, $suffix ) {
		$settings['buy_button_color' . $suffix]		= isset( $_POST['buy_button_color'] ) ? $_POST['buy_button_color'] : '';
		$settings['buy_button_size' . $suffix]		= isset( $_POST['buy_button_size'] ) ? $_POST['buy_button_size'] : '';
		$settings['buy_button_grouped' . $suffix]	= isset( $_POST['buy_button_grouped'] );
		return $settings;
	}
}

new TCPBuyButton();
} // class_exists check