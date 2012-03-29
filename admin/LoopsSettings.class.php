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

class TCPLoopsSettings {

	/*function contextual_help( $contextual_help, $screen_id, $screen ) {
		if ( $screen_id == 'thecartpress_page_tcp_loopssettings_page' ) {
			$contextual_help = 'This is where I would provide help to the user on how everything in my admin panel works. Formatted HTML works fine in here too.';
		}
		return $contextual_help;
	}*/

	function admin_init() {
		register_setting( 'twentytencart_options', 'ttc_settings', array( $this, 'validate' ) );
		add_settings_section( 'ttc_main_section', __( 'Main settings', 'tcp' ) , array( $this, 'show_ttc_main_section' ), __FILE__ );

		add_settings_field( 'see_sorting_panel', __( 'See sorting panel', 'tcp' ), array( $this, 'show_see_sorting_panel' ), __FILE__ , 'ttc_main_section' );
		add_settings_field( 'disabled_order_types', __( 'Disabled order types:', 'tcp' ), array( $this, 'disabled_order_types' ), __FILE__ , 'ttc_main_section' );
		add_settings_field( 'order_type', __( 'Order type:', 'tcp' ), array( $this, 'order_type' ), __FILE__ , 'ttc_main_section' );
		add_settings_field( 'order_desc', __( 'Order desc:', 'tcp' ), array( $this, 'order_desc' ), __FILE__ , 'ttc_main_section' );

		add_settings_field( 'columns', __( 'Columns:', 'tcp' ), array( $this, 'columns' ), __FILE__ , 'ttc_main_section' );	
		add_settings_field( 'see_title', __( 'See title:', 'tcp' ), array( $this, 'see_title' ), __FILE__ , 'ttc_main_section' );
		add_settings_field( 'title_tag', __( 'Title tag:', 'tcp' ), array( $this, 'title_tag' ), __FILE__ , 'ttc_main_section' );
		add_settings_field( 'see_image', __( 'See image:', 'tcp' ), array( $this, 'see_image' ), __FILE__ , 'ttc_main_section' );
		add_settings_field( 'image_size', __( 'Image size:', 'tcp' ), array( $this, 'image_size' ), __FILE__ , 'ttc_main_section' );
		add_settings_field( 'see_excerpt', __( 'See excerpt:', 'tcp' ), array( $this, 'see_excerpt' ), __FILE__ , 'ttc_main_section' );
		add_settings_field( 'see_content', __( 'See content:', 'tcp' ), array( $this, 'see_content' ), __FILE__ , 'ttc_main_section' );
		add_settings_field( 'see_author', __( 'See about author:', 'tcp' ), array( $this, 'see_author' ), __FILE__ , 'ttc_main_section' );
		add_settings_field( 'see_price', __( 'See price:', 'tcp' ), array( $this, 'see_price' ), __FILE__ , 'ttc_main_section' );
		add_settings_field( 'see_buy_button', __( 'See buy button:', 'tcp' ), array( $this, 'see_buy_button' ), __FILE__ , 'ttc_main_section' );
		add_settings_field( 'see_posted_on', __( 'See posted on:', 'tcp' ), array( $this, 'see_posted_on' ), __FILE__ , 'ttc_main_section' );
		add_settings_field( 'see_taxonomies', __( 'See taxonomies:', 'tcp' ), array( $this, 'see_taxonomies' ), __FILE__ , 'ttc_main_section' );
		add_settings_field( 'see_meta_utilities', __( 'See meta utilities:', 'tcp' ), array( $this, 'see_meta_utilities' ), __FILE__ , 'ttc_main_section' );
		add_settings_field( 'see_pagination', __( 'See pagination:', 'tcp' ), array( $this, 'see_pagination' ), __FILE__ , 'ttc_main_section' );
		add_settings_field( 'see_first_custom_area', __( 'See first custom area', 'tcp' ), array( $this, 'see_first_custom_area' ), __FILE__ , 'ttc_main_section' );
		add_settings_field( 'see_second_custom_area', __( 'See second custom area', 'tcp' ), array( $this, 'see_second_custom_area' ), __FILE__ , 'ttc_main_section' );
		add_settings_field( 'see_third_custom_area', __( 'See third custom area', 'tcp' ), array( $this, 'see_third_custom_area' ), __FILE__ , 'ttc_main_section' );
	}

	function admin_menu() {
		global $thecartpress;
		$disable_ecommerce = $thecartpress->get_setting( 'disable_ecommerce', false );
		if ( $disable_ecommerce ) $base = $thecartpress->get_base_tools();
		else $base = $thecartpress->get_base();
		add_submenu_page( $base, __( 'TheCartPress Loop settings', 'tcp' ), __( 'Loop Settings', 'tcp' ), 'tcp_edit_settings', 'ttc_settings_page', array( $this, 'show_settings' ) );
	}

	function show_settings() {?>
		<div class="wrap">
			<h2><?php _e( 'TheCartPress Loop Settings', 'tcp' );?></h2>
			<form method="post" action="options.php">
				<?php settings_fields( 'twentytencart_options' ); ?>
				<?php do_settings_sections( __FILE__ ); ?>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'tcp' ) ?>" />
				</p>
			</form>
		</div><?php
	}

	function show_ttc_main_section() { ?>
	<p class="description"><?php _e( 'This set of settings allows to manage the data to be displayed in the default grid provided by TheCartPress.', 'tcp' ); ?></p><?php
	}

	function see_title() {
		$settings = get_option( 'ttc_settings' );
		$see_title = isset( $settings['see_title'] ) ? $settings['see_title'] : true;?>
		<input type="checkbox" name="ttc_settings[see_title]" id="see_title" value="yes" <?php checked( $see_title, true );?> /><?php
	}

	function title_tag() {
		$settings = get_option( 'ttc_settings' );
		$title_tag = isset( $settings['title_tag'] ) ? $settings['title_tag'] : true;?>
		<select id="title_tag" name="ttc_settings[title_tag]">
			<option value="" <?php selected( $title_tag, '' ); ?>><?php _e( 'No tag', 'tcp' );?></option>
			<option value="h2" <?php selected( $title_tag, 'h2' ); ?>>h2</option>
			<option value="h3" <?php selected( $title_tag, 'h3' ); ?>>h3</option>
			<option value="h4" <?php selected( $title_tag, 'h4' ); ?>>h4</option>
			<option value="h5" <?php selected( $title_tag, 'h5' ); ?>>h5</option>
			<option value="h6" <?php selected( $title_tag, 'h6' ); ?>>h6</option>
			<option value="p" <?php selected( $title_tag, 'p' ); ?>>p</option>
			<option value="div" <?php selected( $title_tag, 'div' ); ?>>div</option>
			<option value="span" <?php selected( $title_tag, 'span' ); ?>>span</option>
		</select><?php
	}

	function see_image() {
		$settings = get_option( 'ttc_settings' );
		$see_image = isset( $settings['see_image'] ) ? $settings['see_image'] : true;?>
		<input type="checkbox" name="ttc_settings[see_image]" id="see_image" value="yes" <?php checked( $see_image, true );?> /><?php
	}

	function image_size() {
		$settings = get_option( 'ttc_settings' );
		$image_size = isset( $settings['image_size'] ) ? $settings['image_size'] : 'thumbnail';?>
		<select id="image_size" name="ttc_settings[image_size]"><?php
		$imageSizes = get_intermediate_image_sizes();
		foreach( $imageSizes as $imageSize ) : ?>
			<option value="<?php echo $imageSize;?>" <?php selected( $imageSize, $image_size );?>><?php echo $imageSize;?></option>
		<?php endforeach;?>
		</select>
		<?php
	}

	function see_excerpt() {
		$settings = get_option( 'ttc_settings' );
		$see_excerpt = isset( $settings['see_excerpt'] ) ? $settings['see_excerpt'] : true;?>
		<input type="checkbox" name="ttc_settings[see_excerpt]" id="see_excerpt" value="yes" <?php checked( $see_excerpt, true );?> /><?php
	}

	function see_content() {
		$settings = get_option( 'ttc_settings' );
		$see_content = isset( $settings['see_content'] ) ? $settings['see_content'] : false;?>
		<input type="checkbox" name="ttc_settings[see_content]" id="see_content" value="yes" <?php checked( $see_content, true );?> /><?php
	}

	function see_author() {
		$settings = get_option( 'ttc_settings' );
		$see_author = isset( $settings['see_author'] ) ? $settings['see_author'] : false;?>
		<input type="checkbox" name="ttc_settings[see_author]" id="see_author" value="yes" <?php checked( $see_author, true );?> /><?php
	}

	function see_price() {
		$settings = get_option( 'ttc_settings' );
		$see_price = isset( $settings['see_price'] ) ? $settings['see_price'] : true;?>
		<input type="checkbox" name="ttc_settings[see_price]" id="see_price" value="yes" <?php checked( $see_price, true );?> /><?php
	}

	function see_buy_button() {
		$settings = get_option( 'ttc_settings' );
		$see_buy_button = isset( $settings['see_buy_button'] ) ? $settings['see_buy_button'] : false;?>
		<input type="checkbox" name="ttc_settings[see_buy_button]" id="see_buy_button" value="yes" <?php checked( $see_buy_button, true );?> /><?php
	}

	function see_posted_on() {
		$settings = get_option( 'ttc_settings' );
		$see_posted_on = isset( $settings['see_posted_on'] ) ? $settings['see_posted_on'] : false;?>
		<input type="checkbox" name="ttc_settings[see_posted_on]" id="see_posted_on" value="yes" <?php checked( $see_posted_on, true );?> /><?php
	}

	function see_taxonomies() {
		$settings = get_option( 'ttc_settings' );
		$see_taxonomies = isset( $settings['see_taxonomies'] ) ? $settings['see_taxonomies'] : false;?>
		<input type="checkbox" name="ttc_settings[see_taxonomies]" id="see_taxonomies" value="yes" <?php checked( $see_taxonomies, true );?> /><?php
	}

	function see_meta_utilities() {
		$settings = get_option( 'ttc_settings' );
		$see_meta_utilities = isset( $settings['see_meta_utilities'] ) ? $settings['see_meta_utilities'] : false;?>
		<input type="checkbox" name="ttc_settings[see_meta_utilities]" id="see_meta_utilities" value="yes" <?php checked( $see_meta_utilities, true );?> /><?php
	}

	function disabled_order_types() {
		$settings = get_option( 'ttc_settings' );
		$disabled_order_types = isset( $settings['disabled_order_types'] ) ? $settings['disabled_order_types'] : array();
		$sorting_fields = tcp_get_sorting_fields();
		foreach( $sorting_fields as $sorting_field ) : ?>
		<input type="checkbox" id="order_type_<?php echo $sorting_field['value']; ?>" name="ttc_settings[disabled_order_types][]" value="<?php echo $sorting_field['value']; ?>" <?php tcp_checked_multiple( $disabled_order_types, $sorting_field['value'] ); ?>/> <?php echo $sorting_field['title']; ?><br/>
		<?php endforeach;
	}

	function order_type() {
		$settings = get_option( 'ttc_settings' );
		$order_type = isset( $settings['order_type'] ) ? $settings['order_type'] : 'date';
		$disabled_order_types = isset( $settings['disabled_order_types'] ) ? $settings['disabled_order_types'] : array();
		$sorting_fields = tcp_get_sorting_fields(); ?>
		<select id="order_type" name="ttc_settings[order_type]">
		<?php foreach( $sorting_fields as $sorting_field ) :
			if ( ! in_array( $sorting_field['value'], $disabled_order_types ) ) : ?>
			<option value="<?php echo $sorting_field['value']; ?>" <?php selected( $order_type, $sorting_field['value'] ); ?>><?php echo $sorting_field['title']; ?></option>
			<?php endif;
		endforeach; ?>
		</select><?php
	}

	function order_desc() {
		$settings = get_option( 'ttc_settings' );
		$order_desc = isset( $settings['order_desc'] ) ? $settings['order_desc'] : 'desc';?>
		<input type="checkbox" name="ttc_settings[order_desc]" id="order_desc" value="yes" <?php checked( $order_desc, 'desc' );?> /><?php
	}

	function show_see_sorting_panel() {
		$settings = get_option( 'ttc_settings' );
		$see_sorting_panel = isset( $settings['see_sorting_panel'] ) ? $settings['see_sorting_panel'] : false;?>
		<input type="checkbox" id="see_sorting_panel" name="ttc_settings[see_sorting_panel]" value="yes" <?php checked( true, $see_sorting_panel );?> /><?php
	}

	function columns() {
		$settings = get_option( 'ttc_settings' );
		$columns = isset( $settings['columns'] ) ? (int)$settings['columns'] : 2;?>
		<input id="columns" name="ttc_settings[columns]" value="<?php echo $columns;?>" size="2" maxlength="2" type="text" /><?php
	}

	function see_pagination() {
		$settings = get_option( 'ttc_settings' );
		$see_pagination = isset( $settings['see_pagination'] ) ? $settings['see_pagination'] : false;?>
		<input type="checkbox" name="ttc_settings[see_pagination]" id="see_paginationsee_pagination" value="yes" <?php checked( $see_pagination, true );?> /><?php
	}

	function see_first_custom_area() {
		$settings = get_option( 'ttc_settings' );
		$see_first_custom_area = isset( $settings['see_first_custom_area'] ) ? $settings['see_first_custom_area'] : false;?>
		<input type="checkbox" name="ttc_settings[see_first_custom_area]" id="see_first_custom_area" value="yes" <?php checked( $see_first_custom_area, true );?> /><?php
	}

	function see_second_custom_area() {
		$settings = get_option( 'ttc_settings' );
		$see_second_custom_area = isset( $settings['see_second_custom_area'] ) ? $settings['see_second_custom_area'] : false;?>
		<input type="checkbox" name="ttc_settings[see_second_custom_area]" id="see_second_custom_area" value="yes" <?php checked( $see_second_custom_area, true );?> /><?php
	}

	function see_third_custom_area() {
		$settings = get_option( 'ttc_settings' );
		$see_third_custom_area = isset( $settings['see_third_custom_area'] ) ? $settings['see_third_custom_area'] : false;?>
		<input type="checkbox" name="ttc_settings[see_third_custom_area]" id="see_third_custom_area" value="yes" <?php checked( $see_third_custom_area, true );?> /><?php
	}

	function validate( $input ) {
		$input['see_title']				= isset( $input['see_title'] ) ? $input['see_title']  == 'yes' : false;
		$input['see_image']				= isset( $input['see_image'] ) ? $input['see_image'] == 'yes' : false;
		$input['see_excerpt']			= isset( $input['see_excerpt'] ) ? $input['see_excerpt'] == 'yes' : false;
		$input['see_content']			= isset( $input['see_content'] ) ? $input['see_content'] == 'yes' : false;
		$input['see_author']			= isset( $input['see_author'] ) ? $input['see_author'] == 'yes' : false;
		$input['see_price']				= isset( $input['see_price'] ) ? $input['see_price'] == 'yes' : false;
		$input['see_buy_button']		= isset( $input['see_buy_button'] ) ? $input['see_buy_button']  == 'yes' : false;
		$input['see_posted_on']			= isset( $input['see_posted_on'] ) ? $input['see_posted_on']  == 'yes' : false;
		$input['see_taxonomies']		= isset( $input['see_taxonomies'] ) ? $input['see_taxonomies']  == 'yes' : false;
		$input['see_meta_utilities']	= isset( $input['see_meta_utilities'] ) ? $input['see_meta_utilities']  == 'yes' : false;
		$input['order_desc']			= isset( $input['order_desc'] ) ? 'desc' : 'asc';
		$input['see_sorting_panel']		= isset( $input['see_sorting_panel'] ) ? $input['see_sorting_panel'] == 'yes' : false;
		$input['columns']				= (int)$input['columns'];
		$input['see_pagination']		= isset( $input['see_pagination'] ) ? $input['see_pagination']  == 'yes' : false;
		$input['see_first_custom_area']	= isset( $input['see_first_custom_area'] ) ? $input['see_first_custom_area']  == 'yes' : false;
		$input['see_second_custom_area']= isset( $input['see_second_custom_area'] ) ? $input['see_second_custom_area']  == 'yes' : false;
		$input['see_third_custom_area']	= isset( $input['see_third_custom_area'] ) ? $input['see_third_custom_area']  == 'yes' : false;
		return $input;
	}

	function template_include( $template ) {
//var_dump($template);
		global $wp_query;
		if ( isset( $wp_query->tax_query ) ) {
			foreach ( $wp_query->tax_query->queries as $tax_query ) { //@See Query.php: 1530
				if ( tcp_is_saleable_taxonomy( $tax_query['taxonomy'] ) ) {
					$settings = get_option( 'tcp_settings' );
					if ( $settings['use_default_loop'] == 'yes' ) {
						$template = WP_PLUGIN_DIR . '/thecartpress/themes-templates/tcp-twentyeleven/taxonomy.php';
						break;
					} elseif ( $settings['use_default_loop'] == 'yes_2010' ) {
						$template = WP_PLUGIN_DIR . '/thecartpress/themes-templates/tcp-twentyten/taxonomy.php';
						break;
					}
				}
			}
		}
		return $template;
	}

	function __construct() {
		$settings = get_option( 'tcp_settings' );
		/*if ( is_admin() ) {
			if ( isset( $settings['use_default_loop'] ) && $settings['use_default_loop'] != 'none' ) {
				add_action( 'admin_init', array( $this, 'admin_init' ) );
				add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			}
		} elseif ( isset( $settings['use_default_loop'] ) && $settings['use_default_loop'] == 'yes' ) {
			add_filter( 'template_include', array( $this, 'template_include' ) );
		}*/
		
		if ( isset( $settings['use_default_loop'] ) ) {
			if ( is_admin() && $settings['use_default_loop'] != 'none' ) {
				add_action( 'admin_init', array( $this, 'admin_init' ) );
				add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			} elseif ( $settings['use_default_loop'] == 'yes' || $settings['use_default_loop'] == 'yes_2010' ) {
				add_filter( 'template_include', array( $this, 'template_include' ) );
			}
		}
	}
}

new TCPLoopsSettings();
?>
