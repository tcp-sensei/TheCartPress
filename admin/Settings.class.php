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

require_once( TCP_DAOS_FOLDER . 'Countries.class.php' );
require_once( TCP_DAOS_FOLDER . 'Currencies.class.php' );

class TCPSettings {

	function admin_init() {
		$tcp_settings_page = __FILE__;
		register_setting( 'thecartpress_options', 'tcp_settings', array( $this, 'validate' ) );
		add_settings_section( 'tcp_main_section', __( 'Main', 'tcp' ) , array( $this, 'show_main_section' ), $tcp_settings_page );
		add_settings_field( 'disable_ecommerce', __( 'Disable eCommerce', 'tcp' ), array( $this, 'show_disable_ecommerce' ), $tcp_settings_page , 'tcp_main_section' );
		add_settings_field( 'disable_shopping_cart', __( 'Disable shopping cart', 'tcp' ), array( $this, 'show_disable_shopping_cart' ), $tcp_settings_page , 'tcp_main_section' );
		add_settings_field( 'continue_url', __( 'Continue Shopping in', 'tcp' ), array( $this, 'show_continue_url' ), $tcp_settings_page , 'tcp_main_section' );
		add_settings_field( 'after_add_to_cart', __( 'After adding to cart', 'tcp' ), array( $this, 'show_after_add_to_cart' ), $tcp_settings_page , 'tcp_main_section' );
		add_settings_field( 'hide_downloadable_menu', __( 'Hide downloadable menu', 'tcp' ), array( $this, 'show_hide_downloadable_menu' ), $tcp_settings_page , 'tcp_main_section' );
		add_settings_field( 'downloadable_path', __( 'Downloadable path', 'tcp' ), array( $this, 'show_downloadable_path' ), $tcp_settings_page , 'tcp_main_section' );

		add_settings_section( 'tcp_currency_section', __( 'Currency', 'tcp' ) , array( $this, 'show_currency_section' ), $tcp_settings_page );
		add_settings_field( 'currency', __( 'Currency', 'tcp' ), array( $this, 'show_currency' ), $tcp_settings_page , 'tcp_currency_section' );
		add_settings_field( 'currency_layout', __( 'Currency layout', 'tcp' ), array( $this, 'show_currency_layout' ), $tcp_settings_page , 'tcp_currency_section' );
		add_settings_field( 'decimal_currency', __( 'Currency decimals', 'tcp' ), array( $this, 'show_decimal_currency' ), $tcp_settings_page , 'tcp_currency_section' );
		add_settings_field( 'decimal_point', __( 'Decimal point separator', 'tcp' ), array( $this, 'show_decimal_point' ), $tcp_settings_page , 'tcp_currency_section' );
		add_settings_field( 'thousands_separator', __( 'Thousands separator', 'tcp' ), array( $this, 'show_thousands_separator' ), $tcp_settings_page , 'tcp_currency_section' );
		add_settings_field( 'unit_weight', __( 'Unit weight', 'tcp' ), array( $this, 'show_unit_weight' ), $tcp_settings_page , 'tcp_currency_section' );

		add_settings_section( 'tcp_countries_section', __( 'Countries', 'tcp' ) , array( $this, 'show_countries_section' ), $tcp_settings_page );
		add_settings_field( 'country', __( 'Base country', 'tcp' ), array( $this, 'show_country' ), $tcp_settings_page , 'tcp_countries_section' );
		add_settings_field( 'billing_isos', __( 'Allowed Billing countries', 'tcp' ), array( $this, 'show_countries_for_billing' ), $tcp_settings_page , 'tcp_countries_section' );
		add_settings_field( 'shipping_isos', __( 'Allowed Shipping countries', 'tcp' ), array( $this, 'show_countries_for_shipping' ), $tcp_settings_page , 'tcp_countries_section' );

		add_settings_section( 'tcp_tax_section', __( 'Tax', 'tcp' ) , array( $this, 'show_tax_section' ), $tcp_settings_page );
		add_settings_field( 'default_tax_country', __( 'Default tax country', 'tcp' ), array( $this, 'show_default_tax_country' ), $tcp_settings_page , 'tcp_tax_section' );
		add_settings_field( 'prices_include_tax', __( 'Prices include tax', 'tcp' ), array( $this, 'show_prices_include_tax' ), $tcp_settings_page , 'tcp_tax_section' );
		add_settings_field( 'tax_based_on', __( 'Tax based on', 'tcp' ), array( $this, 'show_tax_based_on' ), $tcp_settings_page , 'tcp_tax_section' );
		add_settings_field( 'shipping_cost_include_tax', __( 'Shipping cost include tax', 'tcp' ), array( $this, 'show_shipping_cost_include_tax' ), $tcp_settings_page , 'tcp_tax_section' );
		add_settings_field( 'tax_for_shipping', __( 'Select tax for shipping/payment/other costs', 'tcp' ), array( $this, 'show_tax_for_shipping' ), $tcp_settings_page , 'tcp_tax_section' );
		//add_settings_field( 'apply_tax_after_discount', __( 'Apply tax after discount', 'tcp' ), array( $this, 'show_apply_tax_after_discount' ), $tcp_settings_page , 'tcp_tax_section' );
		//add_settings_field( 'apply_discount_on_prices_including_tax', __( 'Apply discount on prices including tax', 'tcp' ), array( $this, 'show_apply_discount_on_prices_including_tax' ), $tcp_settings_page , 'tcp_tax_section' );
		//add_settings_field( 'Apply_tax_on', __( 'Apply tax on', 'tcp' ), array( $this, 'show_apply_tax_on' ), $tcp_settings_page , 'tcp_tax_section' );
		//Apply Tax On: Original prices only or Custom price if available
		add_settings_field( 'display_prices_with_taxes', __( 'Display prices with taxes', 'tcp' ), array( $this, 'show_display_prices_with_taxes' ), $tcp_settings_page , 'tcp_tax_section' );
		add_settings_field( 'display_shipping_cost_with_taxes', __( 'Display shipping prices with taxes', 'tcp' ), array( $this, 'show_display_shipping_cost_with_taxes' ), $tcp_settings_page , 'tcp_tax_section' );
		add_settings_field( 'display_full_tax_summary', __( 'Display full tax summary', 'tcp' ), array( $this, 'show_display_full_tax_summary' ), $tcp_settings_page , 'tcp_tax_section' );
		add_settings_field( 'display_zero_tax_subtotal', __( 'Display zero tax subtotal', 'tcp' ), array( $this, 'show_display_zero_tax_subtotal' ), $tcp_settings_page , 'tcp_tax_section' );

		add_settings_section( 'tcp_checkout_section', __( 'Checkout', 'tcp' ) , array( $this, 'show_checkout_section' ), $tcp_settings_page );
		add_settings_field( 'user_registration', __( 'User registration required', 'tcp' ), array( $this, 'show_user_registration' ), $tcp_settings_page , 'tcp_checkout_section' );
		add_settings_field( 'emails', __( '@mails to send orders', 'tcp' ), array( $this, 'show_emails' ), $tcp_settings_page , 'tcp_checkout_section' );
		add_settings_field( 'from_email', __( 'From email', 'tcp' ), array( $this, 'show_from_email' ), $tcp_settings_page , 'tcp_checkout_section' );
		add_settings_field( 'legal_notice', __( 'Checkout notice', 'tcp' ), array( $this, 'show_legal_notice' ), $tcp_settings_page , 'tcp_checkout_section' );
		add_settings_field( 'checkout_successfully_message', __( 'Checkout successfully message', 'tcp' ), array( $this, 'show_checkout_successfully_message' ), $tcp_settings_page , 'tcp_checkout_section' );

		/*add_settings_section( 'tcp_permalinks_section', __( 'Permalinks', 'tcp' ) , array( $this, 'show_permalink_section' ), $tcp_settings_page );
		add_settings_field( 'product_rewrite', __( 'Product base', 'tcp' ), array( $this, 'show_product_rewrite' ), $tcp_settings_page , 'tcp_permalinks_section' );
		add_settings_field( 'category_rewrite', __( 'Category base', 'tcp' ), array( $this, 'show_category_rewrite' ), $tcp_settings_page , 'tcp_permalinks_section' );
		add_settings_field( 'tag_rewrite', __( 'Tag base', 'tcp' ), array( $this, 'show_tag_rewrite' ), $tcp_settings_page , 'tcp_permalinks_section' );
		add_settings_field( 'supplier_rewrite', __( 'Supplier base', 'tcp' ), array( $this, 'show_supplier_rewrite' ), $tcp_settings_page , 'tcp_permalinks_section' );*/

		add_settings_section( 'tcp_theme_compatibility_section', __( 'Theme compatibility', 'tcp' ) , array( $this, 'show_theme_compatibility_section' ), $tcp_settings_page );
		add_settings_field( 'use_default_loop', __( 'Theme templates', 'tcp' ), array( $this, 'show_use_default_loop' ), $tcp_settings_page , 'tcp_theme_compatibility_section' );	
		add_settings_field( 'load_default_buy_button_style', __( 'Load default buy button style', 'tcp' ), array( $this, 'show_load_default_buy_button_style' ), $tcp_settings_page , 'tcp_theme_compatibility_section' );
		add_settings_field( 'load_default_shopping_cart_checkout_style', __( 'Load default shopping cart & checkout style', 'tcp' ), array( $this, 'show_load_default_shopping_cart_checkout_style' ), $tcp_settings_page , 'tcp_theme_compatibility_section' );
		add_settings_field( 'load_default_loop_style', __( 'Load default loop style', 'tcp' ), array( $this, 'show_load_default_loop_style' ), $tcp_settings_page , 'tcp_theme_compatibility_section' );
		add_settings_field( 'responsive_featured_thumbnails', __( 'Use responsive featured thumbnails', 'tcp' ), array( $this, 'show_responsive_featured_thumbnails' ), $tcp_settings_page , 'tcp_theme_compatibility_section' );
		add_settings_field( 'products_per_page', __( 'Product pages show at most', 'tcp' ), array( $this, 'show_products_per_page' ), $tcp_settings_page , 'tcp_theme_compatibility_section' );
//		add_settings_field( 'see_pagination', __( 'See pagination', 'tcp' ), array( $this, 'show_see_pagination' ), $tcp_settings_page , 'tcp_theme_compatibility_section' );

		add_settings_field( 'see_buy_button_in_content', __( 'See buy button in content', 'tcp' ), array( $this, 'show_see_buy_button_in_content' ), $tcp_settings_page , 'tcp_theme_compatibility_section' );
		add_settings_field( 'align_buy_button_in_content', __( 'Align of buy button in content', 'tcp' ), array( $this, 'show_align_buy_button_in_content' ), $tcp_settings_page , 'tcp_theme_compatibility_section' );
		add_settings_field( 'see_price_in_content', __( 'See price in content', 'tcp' ), array( $this, 'show_see_price_in_content' ), $tcp_settings_page , 'tcp_theme_compatibility_section' );
		add_settings_field( 'image_size_grouped_by_button', __( 'Image size grouped buy button', 'tcp' ), array( $this, 'show_image_size_grouped_by_button' ), $tcp_settings_page , 'tcp_theme_compatibility_section' );		
		add_settings_field( 'see_image_in_content', __( 'See image in content', 'tcp' ), array( $this, 'show_see_image_in_content' ), $tcp_settings_page , 'tcp_theme_compatibility_section' );
		add_settings_field( 'image_size_content', __( 'Image size in content', 'tcp' ), array( $this, 'image_size_content' ), $tcp_settings_page , 'tcp_theme_compatibility_section' );
		add_settings_field( 'image_align_content', __( 'Image align in content', 'tcp' ), array( $this, 'image_align_content' ), $tcp_settings_page , 'tcp_theme_compatibility_section' );
		add_settings_field( 'image_link_content', __( 'Image link in content', 'tcp' ), array( $this, 'image_link_content' ), $tcp_settings_page , 'tcp_theme_compatibility_section' );
		add_settings_field( 'see_buy_button_in_excerpt', __( 'See buy button in excerpt', 'tcp' ), array( $this, 'show_see_buy_button_in_excerpt' ), $tcp_settings_page , 'tcp_theme_compatibility_section' );
		add_settings_field( 'align_buy_button_in_excerpt', __( 'Align of buy button in excerpt', 'tcp' ), array( $this, 'show_align_buy_button_in_excerpt' ), $tcp_settings_page , 'tcp_theme_compatibility_section' );
		add_settings_field( 'see_price_in_excerpt', __( 'See price in excerpt', 'tcp' ), array( $this, 'show_see_price_in_excerpt' ), $tcp_settings_page , 'tcp_theme_compatibility_section' );
		add_settings_field( 'see_image_in_excerpt', __( 'See image in excerpt', 'tcp' ), array( $this, 'show_see_image_in_excerpt' ), $tcp_settings_page , 'tcp_theme_compatibility_section' );
		add_settings_field( 'image_size_excerpt', __( 'Image size in excerpt', 'tcp' ), array( $this, 'image_size_excerpt' ), $tcp_settings_page , 'tcp_theme_compatibility_section' );
		add_settings_field( 'image_align_excerpt', __( 'Image align in excerpt', 'tcp' ), array( $this, 'image_align_excerpt' ), $tcp_settings_page , 'tcp_theme_compatibility_section' );
		add_settings_field( 'image_link_excerpt', __( 'Image link in excerpt', 'tcp' ), array( $this, 'image_link_excerpt' ), $tcp_settings_page , 'tcp_theme_compatibility_section' );

		add_settings_section( 'tcp_admin_section', __( 'Admin settings', 'tcp' ) , array( $this, 'show_admin_section' ), $tcp_settings_page );
		add_settings_field( 'hide_visibles', __( 'Hide invisible products', 'tcp' ), array( $this, 'show_hide_visibles' ), $tcp_settings_page , 'tcp_admin_section' );

		add_settings_section( 'tcp_search_engine_section', __( 'eMarketing tools', 'tcp' ) , array( $this, 'show_search_engine_section' ), $tcp_settings_page );
		add_settings_field( 'search_engine_activated', __( 'Search engine activated', 'tcp' ), array( $this, 'show_search_engine_activated' ), $tcp_settings_page , 'tcp_search_engine_section' );
	}

	function admin_menu() {
		global $thecartpress;
		$disable_ecommerce = $thecartpress->get_setting( 'disable_ecommerce' );
		if ( ! $disable_ecommerce )
			$base = $thecartpress->get_base();
		else
			$base = $thecartpress->get_base_tools();
		add_submenu_page( $base, __( 'TheCartPress settings', 'tcp' ), __( 'Settings', 'tcp' ), 'tcp_edit_settings', 'tcp_settings_page', array( $this, 'show_settings' ) );
	}

	function show_settings() {
		global $thecartpress;
		$thecartpress->load_settings(); ?>
		<div class="wrap">
			<h2><?php _e( 'TheCartPress Settings', 'tcp' ); ?></h2>
			<form method="post" action="options.php">
				<?php settings_fields( 'thecartpress_options' ); ?>
				<?php do_settings_sections( __FILE__ ); ?>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" />
				</p>
			</form>
		</div>
		<?php do_action( 'tcp_show_settings' );
	}

	function show_main_section() { ?>
<script>
jQuery(document).ready( function() {
<?php global $thecartpress;
if ( $thecartpress->get_setting( 'use_default_loop', 'none' ) != 'none' ) : ?>
	hide_excerpt();
<?php endif;
if ( ! $thecartpress->get_setting( 'load_default_loop_style', true ) ) : ?>
	jQuery('#responsive_featured_thumbnails').parent().parent().hide();
<?php endif; ?>
	jQuery('#load_default_loop_style').click( function() {
		if ( jQuery(this).attr('checked') ) {
			jQuery('#responsive_featured_thumbnails').parent().parent().show();
		} else {
			jQuery('#responsive_featured_thumbnails').parent().parent().hide();
		}
	});
});

function hide_excerpt() {
	jQuery('#see_buy_button_in_excerpt').parent().parent().hide();
	jQuery('#align_buy_button_in_excerpt').parent().parent().hide();
	jQuery('#see_price_in_excerpt').parent().parent().hide();
	jQuery('#see_image_in_excerpt').parent().parent().hide();
	jQuery('#image_size_excerpt').parent().parent().hide();
	jQuery('#image_align_excerpt').parent().parent().hide();
	jQuery('#image_link_excerpt').parent().parent().hide();
}

function show_excerpt() {
	jQuery('#see_buy_button_in_excerpt').parent().parent().show();
	jQuery('#align_buy_button_in_excerpt').parent().parent().show();
	jQuery('#see_price_in_excerpt').parent().parent().show();
	jQuery('#see_image_in_excerpt').parent().parent().show();
	jQuery('#image_size_excerpt').parent().parent().show();
	jQuery('#image_align_excerpt').parent().parent().show();
	jQuery('#image_link_excerpt').parent().parent().show();
}
</script><?php
	}

	function show_user_registration( $user_registration = false ) {
		if ( ! $user_registration ) {
			global $thecartpress;
			$user_registration = $thecartpress->get_setting( 'user_registration' );
		} ?>
		<input type="checkbox" id="user_registration" name="tcp_settings[user_registration]" value="yes" <?php checked( true, $user_registration ); ?> />
		<p class="description"><?php _e( 'Indicates if the clients should be or not registered to buy.', 'tcp' ); ?></p><?php
	}

	function show_emails( $emails = false ) {
		if ( ! $emails ) {
			global $thecartpress;
			$emails = $thecartpress->get_setting( 'emails', get_option('admin_email') );
		}?>
		<input type="text" id="emails" name="tcp_settings[emails]" value="<?php echo $emails; ?>" size="40" maxlength="2550" />
		<span class="description"><?php _e( 'Comma (,) separated mails', 'tcp' ); ?></span>
		<p class="description"><?php _e( 'These emails will receive orders notifications.', 'tcp' ); ?></p><?php
	}

	function show_from_email( $from_email = false ) {
		if ( ! $from_email ) {
			global $thecartpress;
			$from_email = $thecartpress->get_setting( 'from_email', '' );
		}?>
		<input type="text" id="from_email" name="tcp_settings[from_email]" value="<?php echo $from_email; ?>" size="40" maxlength="255" />
		<p class="description"><?php _e( 'Host email. If not set, The emails will be sent to the customer from no-response@thecartpress.com', 'tcp' ); ?></p><?php
	}

	function show_after_add_to_cart() {
		global $thecartpress;
		$after_add_to_cart = $thecartpress->get_setting( 'after_add_to_cart', '' ); ?>
		<select id="after_add_to_cart" name="tcp_settings[after_add_to_cart]">
			<option value="" <?php selected( $after_add_to_cart, '' ); ?>><?php _e( 'Nothing', 'tcp' ); ?></option>
			<option value="ssc" <?php selected( $after_add_to_cart, 'ssc' ); ?>><?php _e( 'Show the Shopping Cart', 'tcp' ); ?></option>
		</select><?php
	}

	function show_disable_ecommerce() {
		global $thecartpress;
		$disable_ecommerce = $thecartpress->get_setting( 'disable_ecommerce' ); ?>
		<input type="checkbox" id="disable_ecommerce" name="tcp_settings[disable_ecommerce]" value="yes" <?php checked( true, $disable_ecommerce ); ?> />
		<span class="description"><?php _e( 'To use TheCartPress as a Framework, disabling all eCommerce functionalities.', 'tcp' ); ?></span><?php
	}

	function show_disable_shopping_cart() {
		global $thecartpress;
		$disable_shopping_cart = $thecartpress->get_setting( 'disable_shopping_cart' ); ?>
		<input type="checkbox" id="disable_shopping_cart" name="tcp_settings[disable_shopping_cart]" value="yes" <?php checked( true, $disable_shopping_cart ); ?> />
		<span class="description"><?php _e( 'To use TheCartPress as a catalog, disabling the Shopping cart and the Checkout.', 'tcp' ); ?></span><?php
	}

	function show_continue_url() {
		global $thecartpress;
		$continue_url = $thecartpress->get_setting( 'continue_url', '' ); ?>
		<input type="text" id="continue_url" name="tcp_settings[continue_url]" value="<?php echo $continue_url; ?>" size="50" maxlength="255" />
		<p class="description"><?php _e( 'This value is used in the Continue shopping link into the Shopping cart page.', 'tcp' ); ?></p>
		<p class="description"><?php _e( 'If the value is left to blank then the "home url" will be used.', 'tcp' ); ?></p><?php
	}

	function show_hide_downloadable_menu() {
		global $thecartpress;
		$hide_downloadable_menu = $thecartpress->get_setting( 'hide_downloadable_menu' ); ?>
		<input type="checkbox" id="hide_downloadable_menu" name="tcp_settings[hide_downloadable_menu]" value="yes" <?php checked( $hide_downloadable_menu, true ); ?> /><?php
	}

	function show_downloadable_path() {
		global $thecartpress;
		$downloadable_path = $thecartpress->get_setting( 'downloadable_path', '' ); ?>
		<input type="text" id="downloadable_path" name="tcp_settings[downloadable_path]" value="<?php echo $downloadable_path; ?>" size="50" maxlength="255" />
		<p class="description"><?php _e( 'To protect the downloadable files, from public download, this path must be non-public directory.', 'tcp' ); ?></p>
		<p class="description"><?php printf( __( 'For example, path for the current page in your server is: %s' , 'tcp' ), dirname( __FILE__ ) ); ?></p><?php
	}

	function show_currency_section() {
	}

	function show_currency( $currency = false ) {
		if ( ! $currency ) {
			global $thecartpress;
			$currency = $thecartpress->get_setting( 'currency', 'EUR' );
		} ?>
		<select id="currency" name="tcp_settings[currency]">
		<?php $currencies = Currencies::getAll();
		foreach( $currencies as $currency_row ) :?>
			<option value="<?php echo $currency_row->iso; ?>" <?php selected( $currency_row->iso, $currency ); ?>><?php echo $currency_row->currency; ?></option>
		<?php endforeach; ?>
		</select><?php
	}
	
	function show_currency_layout( $currency_layout = false) {
		if ( ! $currency_layout ) {
			global $thecartpress;
			$currency_layout = $thecartpress->get_setting( 'currency_layout', '%1$s%2$s (%3$s)' );
		} ?>
		<p><label for="tcp_custom_layouts"><?php _e( 'Default layouts', 'tcp' ); ?>:</label>
		<select id="tcp_custom_layouts" onchange="jQuery('#currency_layout').val(jQuery('#tcp_custom_layouts').val());">
			<option value="%1$s%2$s %3$s" <?php selected( '%1$s%2$s %3$s', $currency_layout); ?>><?php _e( 'Currency sign left, Currency ISO right: $100 USD', 'tcp' ); ?></option>
			<option value="%1$s%2$s" <?php selected( '%1$s%2$s', $currency_layout); ?>><?php _e( 'Currency sign left: $100', 'tcp' ); ?></option>
			<option value="%2$s %1$s" <?php selected( '%2$s %1$s', $currency_layout); ?>><?php _e( 'Currency sign right: 100 &euro;', 'tcp' ); ?></option>
		</select>
		</p>
		<label for="currency_layouts"><?php _e( 'Custom layout', 'tcp' ); ?>:</label>
		<input type="text" id="currency_layout" name="tcp_settings[currency_layout]" value="<?php echo $currency_layout; ?>" size="20" maxlength="25" />
		<p class="description"><?php _e( '%1$s -> Currency; %2$s -> Amount; %3$s -> ISO Code. By default, use %1$s%2$s (%3$s) -> $100 (USD).', 'tcp' ); ?></p>
		<p class="description"><?php _e( 'For Example: For Euro use %2$s %1$s -> 100&euro;.', 'tcp' ); ?></p>
		<p class="description"><?php _e( 'If this value is left to blank, then TheCartPress will take this layout from the languages configuration files (mo files). Look for the literal "%1$s%2$s (%3$s)."', 'tcp' ); ?></p><?php
	}

	function show_decimal_currency( $decimal_currency = false) {
		if ( ! $decimal_currency ) {
			global $thecartpress;
			$decimal_currency = $thecartpress->get_setting( 'decimal_currency', '2' );
		} ?>
		<input type="text" id="decimal_currency" name="tcp_settings[decimal_currency]" value="<?php echo $decimal_currency; ?>" size="1" maxlength="1" class="tcp_count"/><?php
	}

	function show_decimal_point( $decimal_point = false ) {
		if ( ! $decimal_point ) {
			global $thecartpress;
			$decimal_point = $thecartpress->get_setting( 'decimal_point', '.' );
		} ?>
		<input type="text" id="decimal_point" name="tcp_settings[decimal_point]" value="<?php echo $decimal_point; ?>" size="1" maxlength="1" /><?php
	}

	function show_thousands_separator( $thousands_separator = false) {
		if ( ! $thousands_separator ) {
			global $thecartpress;
			$thousands_separator = $thecartpress->get_setting( 'thousands_separator', ',' );
		} ?>
		<input type="text" id="thousands_separator" name="tcp_settings[thousands_separator]" value="<?php echo $thousands_separator; ?>" size="1" maxlength="1" /><?php
	}

	function show_unit_weight( $unit_weight = false ) {
		if ( ! $unit_weight ) {
			global $thecartpress;
			$unit_weight = $thecartpress->get_setting( 'unit_weight', 'gr' );
		} ?>
		<select id="unit_weight" name="tcp_settings[unit_weight]">
			<option value="kg." <?php selected( 'kg.', $unit_weight ); ?>><?php _e( 'Kilogram (kg)', 'tcp' ); ?></option>
			<option value="gr." <?php selected( 'gr.', $unit_weight ); ?>><?php _e( 'Gram (gr)', 'tcp' ); ?></option>
			<option value="T." <?php selected( 'T.', $unit_weight ); ?>><?php _e( 'Ton (t)', 'tcp' ); ?></option>
			<option value="mg." <?php selected( 'mg.', $unit_weight ); ?>><?php _e( 'Milligram (mg)', 'tcp' ); ?></option>
			<option value="ct." <?php selected( 'ct.', $unit_weight ); ?>><?php _e( 'Karat (ct)', 'tcp' ); ?></option>
			<option value="oz." <?php selected( 'oz.', $unit_weight ); ?>><?php _e( 'Ounce (oz)', 'tcp' ); ?></option>
			<option value="lb." <?php selected( 'lb.', $unit_weight ); ?>><?php _e( 'Pound (lb)', 'tcp' ); ?></option>
			<option value="oz t." <?php selected( 'oz t.', $unit_weight ); ?>><?php _e( 'Troy ounce (oz t)', 'tcp' ); ?></option>
			<option value="dwt." <?php selected( 'dwt.', $unit_weight ); ?>><?php _e( 'Pennyweight (dwt)', 'tcp' ); ?></option>
			<option value="gr. (en)" <?php selected( 'gr. (en)', $unit_weight ); ?>><?php _e( 'Grain (gr)', 'tcp' ); ?></option>
			<option value="cwt." <?php selected( 'cwt.', $unit_weight ); ?>><?php _e( 'Hundredweight (cwt)', 'tcp' ); ?></option>
			<option value="st." <?php selected( 'st.', $unit_weight ); ?>><?php _e( 'Ston (st)', 'tcp' ); ?></option>
			<option value="T. (long)" <?php selected( 'T. (long)', $unit_weight ); ?>><?php _e( 'Long ton (T long)', 'tcp' ); ?></option>
			<option value="T. (short)" <?php selected( 'T. (short)', $unit_weight ); ?>><?php _e( 'Short ton (T shors)', 'tcp' ); ?></option>
			<option value="hw. (long)" <?php selected( 'hw. (long)', $unit_weight ); ?>><?php _e( 'Long Hundredweights (hw long)', 'tcp' ); ?></option>
			<option value="hw. (short)" <?php selected( 'hw. (short)', $unit_weight ); ?>><?php _e( 'Short Hundredweights (hw short)', 'tcp' ); ?></option>
			<option value="koku" <?php selected( 'koku', $unit_weight ); ?>><?php _e( 'koku', 'tcp' ); ?></option>
			<option value="kann" <?php selected( 'kann', $unit_weight ); ?>><?php _e( 'kann', 'tcp' ); ?></option>
			<option value="kinn" <?php selected( 'kinn', $unit_weight ); ?>><?php _e( 'kinn', 'tcp' ); ?></option>
			<option value="monnme" <?php selected( 'monnme', $unit_weight ); ?>><?php _e( 'monnme', 'tcp' ); ?></option>
			<option value="tael" <?php selected( 'tael', $unit_weight ); ?>><?php _e( 'tael', 'tcp' ); ?></option>
			<option value="ku ping" <?php selected( 'ku ping', $unit_weight ); ?>><?php _e( 'ku ping', 'tcp' ); ?></option>
		</select><?php
	}

	function show_countries_section() {
	}

	function show_country( $country = false ) {//default country
		if ( ! $country ) {
			global $thecartpress;
			$country = $thecartpress->get_setting( 'country', '' );
		} ?>
		<select id="country" name="tcp_settings[country]">
		<?php $countries = Countries::getAll();
		foreach( $countries as $item ) : ?>
			<option value="<?php echo $item->iso; ?>" <?php selected( $item->iso, $country ); ?>><?php echo $item->name; ?></option>
		<?php endforeach; ?>
		</select>
		<?php
	}

	function show_countries_for_shipping() {
		global $thecartpress;
		$shipping_isos = $thecartpress->get_setting( 'shipping_isos', array() ); ?>
		<p><?php _e( 'All countries', 'tcp' ); ?>:&nbsp;<input type="checkbox" name="all_shipping_isos" id="all_shipping_isos" <?php checked( count( $shipping_isos ), 0 ); ?> value="yes"
		onclick="if (this.checked) { jQuery('.sel_shipping_isos').hide(); jQuery('#shipping_isos option').attr('selected', false);  } else { jQuery('.sel_shipping_isos').show(); }"/>
		</p>
		<div class="sel_shipping_isos" <?php if ( ! $shipping_isos ) echo 'style="display:none;"'; ?>>
			<select id="shipping_isos" name="tcp_settings[shipping_isos][]" style="height:auto" size="8" multiple>
			<?php $countries = Countries::getAll();
			foreach( $countries as $item ) :?>
				<option value="<?php echo $item->iso; ?>" <?php tcp_selected_multiple( $shipping_isos, $item->iso ); ?>><?php echo $item->name; ?></option>
			<?php endforeach; ?>
			</select>
			<p>
			<input type="button" value="<?php _e( 'EU', 'tcp'); ?>" title="<?php _e( 'To select countries from the European Union', 'tcp' ); ?>" onclick="tcp_select_eu('shipping_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'NAFTA', 'tcp'); ?>" title="<?php _e( 'To select countries from the NAFTA', 'tcp' ); ?>" onclick="tcp_select_nafta('shipping_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'CARICOM', 'tcp'); ?>" title="<?php _e( 'To select countries from CARICOM', 'tcp' ); ?>" onclick="tcp_select_caricom('shipping_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'MERCASUR', 'tcp'); ?>" title="<?php _e( 'To select countries from MERCASUR', 'tcp' ); ?>" onclick="tcp_select_mercasur('shipping_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'CAN', 'tcp'); ?>" title="<?php _e( 'To select countries from Andean Comunity', 'tcp' ); ?>" onclick="tcp_select_can('shipping_isos');" class="button-secondary"/>				
			<input type="button" value="<?php _e( 'AU', 'tcp'); ?>" title="<?php _e( 'To select countries from African Union', 'tcp' ); ?>" onclick="tcp_select_au('shipping_isos');" class="button-secondary"/>				
			<input type="button" value="<?php _e( 'APEC', 'tcp'); ?>" title="<?php _e( 'To select countries from Asia-Pacific Economic Cooperation', 'tcp' ); ?>" onclick="tcp_select_apec('shipping_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'ASEAN', 'tcp'); ?>" title="<?php _e( 'To select countries from Association of Southeast Asian Nations', 'tcp' ); ?>" onclick="tcp_select_asean('shipping_isos');" class="button-secondary"/>
			</p>
		</div>
	<?php
	}

	function show_countries_for_billing() {
		global $thecartpress;
		$billing_isos = $thecartpress->get_setting( 'billing_isos', array() ); ?>
		<p><?php _e( 'All countries', 'tcp' ); ?>:&nbsp;<input type="checkbox" name="all_billing_isos" id="all_billing_isos" <?php checked( count( $billing_isos ), 0 ); ?> value="yes"
		onclick="if (this.checked) { jQuery('.sel_billing_isos').hide(); jQuery('#billing_isos option').attr('selected', false);  } else { jQuery('.sel_billing_isos').show(); }"/></p>
		<div class="sel_billing_isos" <?php if ( ! $billing_isos ) echo 'style="display:none;"'; ?> >
			<select id="billing_isos" name="tcp_settings[billing_isos][]" style="height:auto" size="8" multiple>
			<?php $countries = Countries::getAll();
			foreach( $countries as $item ) :?>
				<option value="<?php echo $item->iso; ?>" <?php tcp_selected_multiple( $billing_isos, $item->iso ); ?>><?php echo $item->name; ?></option>
			<?php endforeach; ?>
			</select>
			<p>
			<input type="button" value="<?php _e( 'EU', 'tcp'); ?>" title="<?php _e( 'To select countries from the European Union', 'tcp' ); ?>" onclick="tcp_select_eu('billing_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'NAFTA', 'tcp'); ?>" title="<?php _e( 'To select countries from the NAFTA', 'tcp' ); ?>" onclick="tcp_select_nafta('billing_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'CARICOM', 'tcp'); ?>" title="<?php _e( 'To select countries from CARICOM', 'tcp' ); ?>" onclick="tcp_select_caricom('billing_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'MERCASUR', 'tcp'); ?>" title="<?php _e( 'To select countries from MERCASUR', 'tcp' ); ?>" onclick="tcp_select_mercasur('billing_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'CAN', 'tcp'); ?>" title="<?php _e( 'To select countries from Andean Comunity', 'tcp' ); ?>" onclick="tcp_select_can('billing_isos');" class="button-secondary"/>				
			<input type="button" value="<?php _e( 'AU', 'tcp'); ?>" title="<?php _e( 'To select countries from African Union', 'tcp' ); ?>" onclick="tcp_select_au('billing_isos');" class="button-secondary"/>				
			<input type="button" value="<?php _e( 'APEC', 'tcp'); ?>" title="<?php _e( 'To select countries from Asia-Pacific Economic Cooperation', 'tcp' ); ?>" onclick="tcp_select_apec('billing_isos');" class="button-secondary"/>
			<input type="button" value="<?php _e( 'ASEAN', 'tcp'); ?>" title="<?php _e( 'To select countries from Association of Southeast Asian Nations', 'tcp' ); ?>" onclick="tcp_select_asean('billing_isos');" class="button-secondary"/>
			</p>
		</div>
	<?php
	}

	function show_tax_section() {
	}

	function show_default_tax_country() {
		global $thecartpress;
		$default_tax_country = $thecartpress->get_setting( 'default_tax_country', '' );
		if ( $default_tax_country == '' )
			$$default_tax_country = $thecartpress->get_setting( 'country', '' );
		$billing_isos	= $thecartpress->get_setting( 'billing_isos', array() );
		$shipping_isos	= $thecartpress->get_setting( 'shipping_isos', array() );
		$isos			= array_merge( $billing_isos, $shipping_isos ); ?>
		<select id="default_tax_country" name="tcp_settings[default_tax_country]">
		<?php if ( is_array( $isos ) && count( $isos ) > 0 ) {
			$countries = Countries::getSome( $isos );
		} else {
			$countries = Countries::getAll();
		}
		foreach( $countries as $item ) : ?>
			<option value="<?php echo $item->iso; ?>" <?php selected( $item->iso, $default_tax_country ); ?>><?php echo $item->name; ?></option>
		<?php endforeach; ?>
		</select>
		<?php
	}

	function show_prices_include_tax() { //Catalog prices (merchant imputs) include tax: yes or not
		global $thecartpress;
		$prices_include_tax = $thecartpress->get_setting( 'prices_include_tax' ); ?>
		<input type="checkbox" id="prices_include_tax" name="tcp_settings[prices_include_tax]" value="yes" <?php checked( $prices_include_tax, true ); ?> /><?php
	}

	function show_tax_based_on() { //Tax Based On: shipping address , billing address, shipping origin
		global $thecartpress;
		$tax_based_on = $thecartpress->get_setting( 'tax_based_on', '' ); ?>
		<select id="tax_based_on" name="tcp_settings[tax_based_on]">
			<option value="origin" <?php selected( 'origin', $tax_based_on ); ?>><?php _e( 'Default tax country', 'tcp' ); ?></option>
			<option value="billing" <?php selected( 'billing', $tax_based_on ); ?>><?php _e( 'Billing address', 'tcp' ); ?></option>
			<option value="shipping" <?php selected( 'shipping', $tax_based_on ); ?>><?php _e( 'Shipping address', 'tcp' ); ?></option>
		</select>
		<?php
	}

	function show_shipping_cost_include_tax() { //Shipping cost include tax: yes or not
		global $thecartpress;
		$shipping_cost_include_tax = $thecartpress->get_setting( 'shipping_cost_include_tax' ); ?>
		<input type="checkbox" id="shipping_cost_include_tax" name="tcp_settings[shipping_cost_include_tax]" value="yes" <?php checked( $shipping_cost_include_tax, true ); ?> /><?php
	}

	function show_tax_for_shipping() { //Tax Class for Shipping: select tax
		global $thecartpress;
		$tax_for_shipping = $thecartpress->get_setting( 'tax_for_shipping', '' ); ?>
		<select id="tax_for_shipping" name="tcp_settings[tax_for_shipping]">
			<option value="0"><?php _e( 'No tax', 'tcp' ); ?></option>
		<?php 
		require_once( TCP_DAOS_FOLDER . 'Taxes.class.php' );
		$taxes = Taxes::getAll();
		foreach ( $taxes as $tax ) : ?>
			<option value="<?php echo $tax->tax_id; ?>" <?php selected( $tax->tax_id, $tax_for_shipping ); ?>><?php echo $tax->title; ?></option>
		<?php endforeach; ?>
		</select><?php
	}

	function show_apply_tax_after_discount() { //Apply Tax After Discount: yes or not
		global $thecartpress;
		$apply_tax_after_discount = $thecartpress->get_setting( 'apply_tax_after_discount' ); ?>
		<input type="checkbox" id="apply_tax_after_discount" name="tcp_settings[apply_tax_after_discount]" value="yes" <?php checked( $apply_tax_after_discount, true ); ?> /><?php
	}

	function show_apply_discount_on_prices_including_tax() { //Apply Discount On Prices Including Tax: yes or not (Ignored when ‘Apply Tax After Discount’ option is enabled)
		global $thecartpress;
		$apply_discount_on_prices_including_tax = $thecartpress->get_setting( 'apply_discount_on_prices_including_tax' ); ?>
		<input type="checkbox" id="apply_discount_on_prices_including_tax" name="tcp_settings[apply_discount_on_prices_including_tax]" value="yes" <?php checked( $apply_discount_on_prices_including_tax, true ); ?> /><?php
	}

	function show_display_prices_with_taxes() {//Display Product Prices with taxes: yes or not
		global $thecartpress;
		$display_prices_with_taxes = $thecartpress->get_setting( 'display_prices_with_taxes' ); ?>
		<input type="checkbox" id="display_prices_with_taxes" name="tcp_settings[display_prices_with_taxes]" value="yes" <?php checked( $display_prices_with_taxes, true ); ?> /><?php
	}

	function show_display_Cart_Order_prices() {//Display Cart/Order Prices: including tax or excluding tax or Including and excluding tax
	}

	function show_display_full_tax_summary() {//Display full tax summary: yes or not
		global $thecartpress;
		$display_full_tax_summary = $thecartpress->get_setting( 'display_full_tax_summary' ); ?>
		<input type="checkbox" id="display_full_tax_summary" name="tcp_settings[display_full_tax_summary]" value="yes" <?php checked( $display_full_tax_summary, true ); ?> /><?php	
	}

	function show_display_shipping_cost_with_taxes() {//Display Shipping Prices with taxes: yes or not
		global $thecartpress;
		$display_shipping_cost_with_taxes = $thecartpress->get_setting( 'display_shipping_cost_with_taxes' ); ?>
		<input type="checkbox" id="display_shipping_cost_with_taxes" name="tcp_settings[display_shipping_cost_with_taxes]" value="yes" <?php checked( $display_shipping_cost_with_taxes, true ); ?> /><?php	
	}

	function show_display_zero_tax_subtotal() { //Display Zero Tax Subtotal: yes or not
		global $thecartpress;
		$display_zero_tax_subtotal = $thecartpress->get_setting( 'display_zero_tax_subtotal' ); ?>
		<input type="checkbox" id="display_zero_tax_subtotal" name="tcp_settings[display_zero_tax_subtotal]" value="yes" <?php checked( $display_zero_tax_subtotal, true ); ?> /><?php	
	}

	function show_checkout_section() {
	}

	function show_legal_notice() {
		global $thecartpress;
		$legal_notice = $thecartpress->get_setting( 'legal_notice', __( 'Checkout notice', 'tcp' ) ); ?>
		<textarea id="legal_notice" name="tcp_settings[legal_notice]" cols="40" rows="5" maxlength="1020"><?php echo $legal_notice; ?></textarea>
		<p class="description"><?php _e( 'If the checkout notice is blank, the Checkout page will try to use the Notice class called "tcp_checkout_notice"', 'tcp' ); ?></p>
		<p class="description"><?php _e( 'The Notice class allows to create a multilingual notice', 'tcp' ); ?></p>
		<p class="description"><?php _e( 'If the checkout notice is blank and no Notice class is assigned, then the Checkout page will not show the "Accept conditions" check.', 'tcp' ); ?></p><?php
	}

	function show_checkout_successfully_message() {
		global $thecartpress;
		$checkout_successfully_message = $thecartpress->get_setting( 'checkout_successfully_message', __( 'The order has been completed successfully', 'tcp' ) ); ?>
		<textarea id="checkout_successfully_message" name="tcp_settings[checkout_successfully_message]" cols="40" rows="5" maxlength="1020"><?php echo $checkout_successfully_message; ?></textarea>
		<p class="description"><?php _e( 'This text will show at the end of the checkout process.', 'tcp' ); ?></p>
		<p class="description"><?php _e( 'If this messages is blank, the Checkout page will try to use the Notice class called "tcp_checkout_end"', 'tcp' ); ?></p><?php
	}

	/*function show_permalink_section() {
	}

	function show_product_rewrite() {
		global $thecartpress;
		$product_rewrite = $thecartpress->get_setting( 'product_rewrite', 'product' ); ?>
		<input type="text" id="product_rewrite" name="tcp_settings[product_rewrite]" value="<?php echo $product_rewrite; ?>" size="50" maxlength="255" /><?php
	}

	function show_category_rewrite() {
		global $thecartpress;
		$category_rewrite = $thecartpress->get_setting( 'category_rewrite', 'product_category' ); ?>
		<input type="text" id="category_rewrite" name="tcp_settings[category_rewrite]" value="<?php echo $category_rewrite; ?>" size="50" maxlength="255" />
		<p class="description"><?php printf( __( 'Category base for post is "%s". Remember to set a different value.', 'tcp' ), get_option( 'category_base' ) ); ?></p><?php
	}

	function show_tag_rewrite() {
		global $thecartpress;
		$tag_rewrite = $thecartpress->get_setting( 'tag_rewrite', 'product_tag' ); ?>
		<input type="text" id="tag_rewrite" name="tcp_settings[tag_rewrite]" value="<?php echo $tag_rewrite; ?>" size="50" maxlength="255" />
		<p class="description"><?php printf( __( 'Tag base for post is "%s". Remember to set a different value.', 'tcp' ), get_option( 'tag_base' ) ); ?></p><?php
	}

	function show_supplier_rewrite() {
		global $thecartpress;
		$supplier_rewrite = $thecartpress->get_setting( 'supplier_rewrite', 'product_supplier' ); ?>
		<input type="text" id="supplier_rewrite" name="tcp_settings[supplier_rewrite]" value="<?php echo $supplier_rewrite; ?>" size="50" maxlength="255" /><?php
	}*/

	function show_theme_compatibility_section() {
	}

	function show_use_default_loop( $use_default_loop = false ) {
		if ( ! $use_default_loop ) {
			global $thecartpress;
			$use_default_loop = $thecartpress->get_setting( 'use_default_loop', 'only_settings' );
		} ?>
		<input type="radio" id="use_default_loop_only" name="tcp_settings[use_default_loop]" value="only_settings" <?php checked( 'only_settings', $use_default_loop ); ?>
		onclick="hide_excerpt();"/> <label for="use_default_loop_only"><strong><?php _e( 'Use configurable TCP loops', 'tcp' ); ?></strong></label>
		<p class="description"><?php _e( 'If this setting is activated you should have a configurable TCP Loop in your theme. (eg: loop-tcp-grid.php)', 'tcp' ); ?></p>
		<p class="description"><?php _e( 'You must configure the grid using the Loop settings menu.', 'tcp' ); ?></p>
		<p class="description"><?php _e( 'Total flexibility for developers and theme constructors.', 'tcp' ); ?></p>
		<input type="radio" id="use_default_loop" name="tcp_settings[use_default_loop]" value="yes" <?php checked( 'yes', $use_default_loop ); ?>
		onclick="hide_excerpt();" /> <label for="use_default_loop"><strong><?php _e( 'Use TCP default Templates (twentyeleven based)', 'tcp' ); ?></strong></label>
		<br/>
		<input type="radio" id="use_default_loop" name="tcp_settings[use_default_loop]" value="yes_2010" <?php checked( 'yes_2010', $use_default_loop ); ?>
		onclick="hide_excerpt();" /> <label for="use_default_loop"><strong><?php _e( 'Use TCP default Templates (twentyten based)', 'tcp' ); ?></strong></label>
		<p class="description"><?php _e( 'To show the product pages with the default/basic template provides by TheCartPress.', 'tcp' ); ?></p>
		<p class="description"><?php _e( 'If this setting is activated you must configure the grid using the Loop settings menu.', 'tcp' ); ?></p>
		<p class="description"><?php _e( 'TheCartPress provides two version of the default template, one for twentyeleven based themes and the other for twentyten based themes.', 'tcp' ); ?></p>
		<input type="radio" id="use_default_loop_none" name="tcp_settings[use_default_loop]" value="none" <?php checked( 'none', $use_default_loop ); ?> 
		onclick="show_excerpt();"/> <label for="use_default_loop_none"><strong><?php _e( 'None', 'tcp' ); ?></strong></label>
		<p class="description"><?php _e( 'Use your own templates. Total flexibility for developers and theme constructors.', 'tcp' ); ?></p><?php
	}

	function show_load_default_buy_button_style() {
		global $thecartpress;
		$load_default_buy_button_style = $thecartpress->get_setting( 'load_default_buy_button_style', true ); ?>
		<input type="checkbox" id="load_default_buy_button_style" name="tcp_settings[load_default_buy_button_style]" value="yes" <?php checked( true, $load_default_buy_button_style ); ?> /><?php
	}

	function show_load_default_shopping_cart_checkout_style() {
		global $thecartpress;
		$load_default_shopping_cart_checkout_style = $thecartpress->get_setting( 'load_default_shopping_cart_checkout_style', true ); ?>
		<input type="checkbox" id="load_default_shopping_cart_checkout_style" name="tcp_settings[load_default_shopping_cart_checkout_style]" value="yes" <?php checked( true, $load_default_shopping_cart_checkout_style ); ?> /><?php
	}

	function show_load_default_loop_style() {
		global $thecartpress;
		$load_default_loop_style = $thecartpress->get_setting( 'load_default_loop_style', true ); ?>
		<input type="checkbox" id="load_default_loop_style" name="tcp_settings[load_default_loop_style]" value="yes" <?php checked( true, $load_default_loop_style ); ?> /><?php
	}

	function show_responsive_featured_thumbnails() {
		global $thecartpress;
		$responsive_featured_thumbnails = $thecartpress->get_setting( 'responsive_featured_thumbnails', true ); ?>
		<input type="checkbox" id="responsive_featured_thumbnails" name="tcp_settings[responsive_featured_thumbnails]" value="yes" <?php checked( true, $responsive_featured_thumbnails ); ?> />
		<p class="description"><?php _e( 'If this option is not checked the original image size or styles defined in your theme will be loaded', 'tcp' ); ?></p><?php
	}

	function show_products_per_page() {
		global $thecartpress;
		$products_per_page = $thecartpress->get_setting( 'products_per_page', '10' ); ?>
		<input type="text" id="products_per_page" name="tcp_settings[products_per_page]" value="<?php echo $products_per_page; ?>" class="small-text tcp_count" maxlength="4"/><?php
		_e( 'products', 'tcp');
	}

/*	function show_see_pagination() {
		global $thecartpress;
		$see_pagination = $thecartpress->get_setting( 'see_pagination', false ); ?>
		<input type="checkbox" id="see_pagination" name="tcp_settings[see_pagination]" value="yes" <?php checked( true, $see_pagination ); ?> /><?php
	}*/

	function show_see_buy_button_in_content() {
		global $thecartpress;
		$see_buy_button_in_content = $thecartpress->get_setting( 'see_buy_button_in_content', true ); ?>
		<input type="checkbox" id="see_buy_button_in_content" name="tcp_settings[see_buy_button_in_content]" value="yes" <?php checked( true, $see_buy_button_in_content ); ?> />
		<p class="description"><?php _e( 'Allows to show the buy button in the product description.', 'tcp' ); ?></p>
		<p class="description"><?php _e( 'The "in content" settings only can be activated if the product single template doesn\'t use the template tags to show data.', 'tcp' ); ?></p><?php
	}

	function show_align_buy_button_in_content() {
		global $thecartpress;
		$align_buy_button_in_content = $thecartpress->get_setting( 'align_buy_button_in_content', 'north' ); ?>
		<select id="align_buy_button_in_content" name="tcp_settings[align_buy_button_in_content]">
			<option value="north" <?php selected( 'north', $align_buy_button_in_content ); ?>><?php _e( 'North', 'tcp' ); ?></option>
			<option value="south" <?php selected( 'south', $align_buy_button_in_content ); ?>><?php _e( 'South', 'tcp' ); ?></option>
		</select><?php
	}

	function show_see_buy_button_in_excerpt() {
		global $thecartpress;
		$see_buy_button_in_excerpt = $thecartpress->get_setting( 'see_buy_button_in_excerpt' ); ?>
		<input type="checkbox" id="see_buy_button_in_excerpt" name="tcp_settings[see_buy_button_in_excerpt]" value="yes" <?php checked( true, $see_buy_button_in_excerpt ); ?> />
		<p class="description"><?php _e( 'Allows to show the buy button in the product lists.', 'tcp' ); ?></p>
		<p class="description"><?php _e( 'The "in excerpt" settings only can be activated if the products template doesn\'t use the template tags to show data.', 'tcp' ); ?></p><?php

	}

	function show_align_buy_button_in_excerpt() {
		global $thecartpress;
		$align_buy_button_in_excerpt = $thecartpress->get_setting( 'align_buy_button_in_excerpt', 'north' ); ?>
		<select id="align_buy_button_in_excerpt" name="tcp_settings[align_buy_button_in_excerpt]">
			<option value="north" <?php selected( 'north', $align_buy_button_in_excerpt ); ?>><?php _e( 'North', 'tcp' ); ?></option>
			<option value="south" <?php selected( 'south', $align_buy_button_in_excerpt ); ?>><?php _e( 'South', 'tcp' ); ?></option>
		</select><?php
	}

	function show_image_size_grouped_by_button() {
		global $thecartpress;
		$image_size_grouped_by_button = $thecartpress->get_setting( 'image_size_grouped_by_button', 'thumbnail' ); ?>
		<select id="image_size_grouped_by_button" name="tcp_settings[image_size_grouped_by_button]">
			<option value="none" <?php selected( 'none', $image_size_grouped_by_button ); ?>><?php _e( 'No image', 'tcp' ); ?></option>
			<option value="thumbnail" <?php selected( 'thumbnail', $image_size_grouped_by_button ); ?>><?php _e( 'Thumbnail', 'tcp' ); ?></option>
			<option value="64" <?php selected( '64', $image_size_grouped_by_button ); ?>><?php _e( '64x64', 'tcp' ); ?></option>
			<option value="48" <?php selected( '48', $image_size_grouped_by_button ); ?>><?php _e( '48x48', 'tcp' ); ?></option>
			<option value="32" <?php selected( '32', $image_size_grouped_by_button ); ?>><?php _e( '32x32', 'tcp' ); ?></option>
		</select><?php
	}

	function show_see_price_in_content() {
		global $thecartpress;
		$see_price_in_content = $thecartpress->get_setting( 'see_price_in_content' ); ?>
		<input type="checkbox" id="see_price_in_content" name="tcp_settings[see_price_in_content]" value="yes" <?php checked( true, $see_price_in_content ); ?> /><?php
	}

	function show_see_price_in_excerpt() {
		global $thecartpress;
		$see_price_in_excerpt = $thecartpress->get_setting( 'see_price_in_excerpt' ); ?>
		<input type="checkbox" id="see_price_in_excerpt" name="tcp_settings[see_price_in_excerpt]" value="yes" <?php checked( true, $see_price_in_excerpt ); ?> /><?php
	}

	function show_see_image_in_content() {
		global $thecartpress;
		$see_image_in_content = $thecartpress->get_setting( 'see_image_in_content' ); ?>
		<input type="checkbox" id="see_image_in_content" name="tcp_settings[see_image_in_content]" value="yes" <?php checked( true, $see_image_in_content ); ?> /><?php
	}

	function image_size_content() {
		global $thecartpress;
		$image_size_content = $thecartpress->get_setting( 'image_size_content', 'thumbnail' );
		$image_sizes = get_intermediate_image_sizes(); ?>
		<select id="image_size_content" name="tcp_settings[image_size_content]">
		<?php foreach( $image_sizes as $image_size ) : ?>
			<option value="<?php echo $image_size; ?>" <?php selected( $image_size, $image_size_content ); ?>><?php echo $image_size; ?></option>
		<?php endforeach; ?>
		</select><?php
	}

	function image_align_content() {
		global $thecartpress;
		$image_align_content = $thecartpress->get_setting( 'image_align_content' ); ?>
		<select id="image_align_content" name="tcp_settings[image_align_content]">
			<option value="" <?php selected( '', $image_align_content ); ?>><?php _e( 'None', 'tcp' ); ?></option>
			<option value="alignleft" <?php selected( 'alignleft', $image_align_content ); ?>><?php _e( 'Align Left', 'tcp' ); ?></option>
			<option value="aligncenter" <?php selected( 'aligncenter', $image_align_content ); ?>><?php _e( 'Align Center', 'tcp' ); ?></option>
			<option value="alignright" <?php selected( 'alignright', $image_align_content ); ?>><?php _e( 'Align Right', 'tcp' ); ?></option>
		</select><?php
	}

	function image_link_content() {
		global $thecartpress;
		$image_link_content = $thecartpress->get_setting( 'image_link_content' ); ?>
		<select id="image_link_content" name="tcp_settings[image_link_content]">
			<option value="" <?php selected( '', $image_link_content ); ?>><?php _e( 'None', 'tcp' ); ?></option>
			<option value="file" <?php selected( 'file', $image_link_content ); ?>><?php _e( 'File url', 'tcp' ); ?></option>
			<option value="post" <?php selected( 'post', $image_link_content ); ?>><?php _e( 'Post url', 'tcp' ); ?></option>
		</select><?php
	}

	function show_see_image_in_excerpt() {
		global $thecartpress;
		$see_image_in_excerpt = $thecartpress->get_setting( 'see_image_in_excerpt' ); ?>
		<input type="checkbox" id="see_image_in_excerpt" name="tcp_settings[see_image_in_excerpt]" value="yes" <?php checked( true, $see_image_in_excerpt ); ?> /><?php
	}

	function image_size_excerpt() {
		global $thecartpress;
		$image_size_excerpt = $thecartpress->get_setting( 'image_size_excerpt', 'thumbnail' );
		$image_sizes = get_intermediate_image_sizes(); ?>
		<select id="image_size_excerpt" name="tcp_settings[image_size_excerpt]">
		<?php foreach( $image_sizes as $image_size ) : ?>
			<option value="<?php echo $image_size; ?>" <?php selected( $image_size, $image_size_excerpt ); ?>><?php echo $image_size; ?></option>
		<?php endforeach; ?>
		</select><?php
	}

	function image_align_excerpt() {
		global $thecartpress;
		$image_align_excerpt = $thecartpress->get_setting( 'image_align_excerpt' ); ?>
		<select id="image_align_excerpt" name="tcp_settings[image_align_excerpt]">
			<option value="" <?php selected( '', $image_align_excerpt ); ?>><?php _e( 'None', 'tcp' ); ?></option>
			<option value="alignleft" <?php selected( 'alignleft', $image_align_excerpt ); ?>><?php _e( 'Align Left', 'tcp' ); ?></option>
			<option value="aligncenter" <?php selected( 'aligncenter', $image_align_excerpt ); ?>><?php _e( 'Align Center', 'tcp' ); ?></option>
			<option value="alignright" <?php selected( 'alignright', $image_align_excerpt ); ?>><?php _e( 'Align Right', 'tcp' ); ?></option>
		</select><?php
	}

	function image_link_excerpt() {
		global $thecartpress;
		$image_link_excerpt = $thecartpress->get_setting( 'image_link_excerpt' ); ?>
		<select id="image_link_excerpt" name="tcp_settings[image_link_excerpt]">
			<option value="" <?php selected( '', $image_link_excerpt ); ?>><?php _e( 'None', 'tcp' ); ?></option>
			<option value="file" <?php selected( 'file', $image_link_excerpt ); ?>><?php _e( 'File url', 'tcp' ); ?></option>
			<option value="post" <?php selected( 'post', $image_link_excerpt ); ?>><?php _e( 'Post url', 'tcp' ); ?></option>
		</select><?php
	}

	function show_admin_section() {
	}

	function show_hide_visibles() {
		global $thecartpress;
		$hide_visibles = $thecartpress->get_setting( 'hide_visibles' ); ?>
		<input type="checkbox" id="hide_visibles" name="tcp_settings[hide_visibles]" value="yes" <?php checked( true, $hide_visibles ); ?> />
		<p class="description"><?php _e( 'Hide the invisible products in the back-end.', 'tcp' ); ?></p><?php
	}

	function show_back_end_label() {
		global $thecartpress;
		$show_back_end_label = $thecartpress->get_setting( 'show_back_end_label' ); ?>
		<input type="checkbox" id="show_back_end_label" name="tcp_settings[show_back_end_label]" value="yes" <?php checked( true, $show_back_end_label ); ?> /><?php
	}

	function show_search_engine_section() {
	}

	function show_search_engine_activated() {
		global $thecartpress;
		$search_engine_activated = $thecartpress->get_setting( 'search_engine_activated', true ); ?>
		<input type="checkbox" id="search_engine_activated" name="tcp_settings[search_engine_activated]" value="yes" <?php checked( true, $search_engine_activated ); ?> /><?php
	}

	function validate( $input ) {
		$input['currency_layout']			= isset( $input['currency_layout'] ) ? $input['currency_layout'] : ''; //'%1$s%2$s (%3$s)';
		$input['decimal_point']				= isset( $input['decimal_point'] ) ? $input['decimal_point'] : '.';
		$input['thousands_separator']		= isset( $input['thousands_separator'] ) ? $input['thousands_separator'] : ',';
		$input['legal_notice']				=  wp_filter_nohtml_kses( isset( $input['legal_notice'] ) ? $input['legal_notice'] : '' );
		$input['from_email']				=  wp_filter_nohtml_kses( isset( $input['from_email'] ) ? $input['from_email'] : '' );
		$input['emails']					=  wp_filter_nohtml_kses( isset( $input['emails'] ) ? $input['emails'] : get_option('admin_email') );
		$input['hide_downloadable_menu']	= isset( $input['hide_downloadable_menu'] ) ? $input['hide_downloadable_menu'] == 'yes' : false;
		$input['disable_ecommerce']			= isset( $input['disable_ecommerce'] ) ? $input['disable_ecommerce'] == 'yes' : false;
		if ( $input['disable_ecommerce'] )
			$input['disable_shopping_cart'] = true;
		else
			$input['disable_shopping_cart']	= isset( $input['disable_shopping_cart'] ) ? $input['disable_shopping_cart'] == 'yes' : false;
		$input['user_registration']			= isset( $input['user_registration'] ) ? $input['user_registration'] == 'yes' : false;

		if ( isset( $input['all_shipping_isos'] ) && $input['all_shipping_isos'] == 'yes' ) $input['shipping_isos'] = array();
		if ( isset( $input['all_billing_isos'] ) && $input['all_billing_isos'] == 'yes' ) $input['billing_isos'] = array();

		$input['prices_include_tax']		= isset( $input['prices_include_tax'] ) ? $input['prices_include_tax'] == 'yes' : false;
		$input['shipping_cost_include_tax']	= isset( $input['shipping_cost_include_tax'] ) ? $input['shipping_cost_include_tax'] == 'yes' : false;
		$input['apply_tax_after_discount']	= isset( $input['apply_tax_after_discount'] ) ? $input['apply_tax_after_discount'] == 'yes' : false;
		$input['apply_discount_on_prices_including_tax'] = isset( $input['apply_discount_on_prices_including_tax'] ) ? $input['apply_discount_on_prices_including_tax'] == 'yes' : false;

		$input['display_prices_with_taxes']	= isset( $input['display_prices_with_taxes'] ) ? $input['display_prices_with_taxes'] == 'yes' : false;
		$input['display_Cart_Order_prices']	= isset( $input['display_Cart_Order_prices'] ) ? $input['display_Cart_Order_prices'] == 'yes' : false;
		$input['display_shipping_cost_with_taxes']	= isset( $input['display_shipping_cost_with_taxes'] ) ? $input['display_shipping_cost_with_taxes'] == 'yes' : false;
		$input['display_full_tax_summary']	= isset( $input['display_full_tax_summary'] ) ? $input['display_full_tax_summary'] == 'yes' : false;
		$input['display_zero_tax_subtotal']	= isset( $input['display_zero_tax_subtotal'] ) ? $input['display_zero_tax_subtotal'] == 'yes' : false;

		$input['products_per_page']			= isset( $input['products_per_page'] ) ? (int)$input['products_per_page'] : 10;
//		$input['see_pagination']			= isset( $input['see_pagination'] ) ? $input['see_pagination'] == 'yes' : false;
		$input['see_buy_button_in_content']	= isset( $input['see_buy_button_in_content'] ) ? $input['see_buy_button_in_content'] == 'yes' : false;
		$input['see_buy_button_in_excerpt']	= isset( $input['see_buy_button_in_excerpt'] ) ? $input['see_buy_button_in_excerpt'] == 'yes' : false;
		$input['align_buy_button_in_excerpt'] = isset( $input['align_buy_button_in_excerpt'] ) ? $input['align_buy_button_in_excerpt']: 'thumbnail';
		$input['see_price_in_content']		= isset( $input['see_price_in_content'] ) ? $input['see_price_in_content'] == 'yes' : false;
		$input['see_price_in_excerpt']		= isset( $input['see_price_in_excerpt'] ) ? $input['see_price_in_excerpt'] == 'yes' : false;
		$input['see_image_in_content']		= isset( $input['see_image_in_content'] ) ? $input['see_image_in_content'] == 'yes' : false;
		$input['see_image_in_excerpt']		= isset( $input['see_image_in_excerpt'] ) ? $input['see_image_in_excerpt'] == 'yes' : false;
		$input['downloadable_path']			= wp_filter_nohtml_kses( isset( $input['downloadable_path'] ) ? $input['downloadable_path'] : '' );
		$input['continue_url']				= wp_filter_nohtml_kses( isset( $input['continue_url'] ) ? $input['continue_url'] : '' );
		/*$input['product_rewrite']			= wp_filter_nohtml_kses( isset( $input['product_rewrite'] ) ? $input['product_rewrite'] : '' );
		$input['category_rewrite']			= wp_filter_nohtml_kses( isset( $input['category_rewrite'] ) ? $input['category_rewrite'] : '' );
		$input['tag_rewrite']				= wp_filter_nohtml_kses( isset( $input['tag_rewrite'] ) ? $input['tag_rewrite'] : '' );
		$input['supplier_rewrite']			= wp_filter_nohtml_kses( isset( $input['supplier_rewrite'] ) ? $input['supplier_rewrite'] : '' );*/
		//$input['use_tcp_loop']				= isset( $input['use_tcp_loop'] ) ? $input['use_tcp_loop'] == 'yes' : false;
		$input['use_default_loop']			= isset( $input['use_default_loop'] ) ? $input['use_default_loop'] : 'only_settings';
		$input['checkout_successfully_message']			= wp_filter_nohtml_kses( isset( $input['checkout_successfully_message'] ) ? $input['checkout_successfully_message'] : '' );
		$input['load_default_buy_button_style']			= isset( $input['load_default_buy_button_style'] ) ? $input['load_default_buy_button_style'] == 'yes' : false;
		$input['load_default_shopping_cart_checkout_style']	= isset( $input['load_default_shopping_cart_checkout_style'] ) ? $input['load_default_shopping_cart_checkout_style'] == 'yes' : false;
		$input['load_default_loop_style']				= isset( $input['load_default_loop_style'] ) ? $input['load_default_loop_style'] == 'yes' : false;
		if ( $input['load_default_loop_style'] ) {
			$input['responsive_featured_thumbnails']	= isset( $input['responsive_featured_thumbnails'] ) ? $input['responsive_featured_thumbnails'] == 'yes' : false;
		} else {
			$input['responsive_featured_thumbnails']	= false;
		}
		$input['hide_visibles']				= isset( $input['hide_visibles'] ) ? $input['hide_visibles'] == 'yes' : false;
		$input['show_back_end_label']		= isset( $input['show_back_end_label'] ) ? $input['show_back_end_label'] == 'yes' : false;
		$input['search_engine_activated']	= isset( $input['search_engine_activated'] ) ? $input['search_engine_activated'] == 'yes' : false;
		//validations
		if ( $input['decimal_point'] == '' ) {
			if ( $input['thousands_separator'] == '.' ) {
				$input['decimal_point'] = ',';
			} else {
				$input['decimal_point'] = '.';
			}
		} elseif ( $input['decimal_point'] == $input['thousands_separator'] ) {
			if ( $input['thousands_separator'] == '.' ) {
				$input['decimal_point'] = ',';
			} else {
				$input['decimal_point'] = '.';
			}
		}
//		if ( get_option( 'category_base' ) == $input['category_rewrite'] ) $input['category_rewrite'] = 'tcp_' . $input['category_rewrite'];
//		if ( get_option( 'tag_base' ) == $input['tag_rewrite'] ) $input['tag_rewrite'] = 'tcp_' . $input['tag_rewrite'];
		$input = apply_filters( 'tcp_validate_settings', $input );
		return $input;
	}

	function __construct( $register = true ) {
		if ( $register && is_admin() ) {
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		}
	}
}

new TCPSettings();
?>
