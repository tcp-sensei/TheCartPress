<?php
/**
 * UI Improvements
 *
 * Improvements to User Interfce
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

if ( ! class_exists( 'UIImprovements' ) ) {

class UIImprovements {

	function __construct() {
		add_action( 'admin_init'	, array( $this, 'admin_init' ) );
		add_action( 'tcp_init'		, array( $this, 'tcp_init' ) );
	}
	function tcp_init() {
		add_filter( 'tcp_the_currency'		, array( $this, 'tcp_the_currency' ) );
		add_action( 'twentyten_credits'		, array( $this, 'twentyten_credits' ) );
		add_action( 'twentyeleven_credits'	, array( $this, 'twentyten_credits' ) );
		add_action( 'twentytwelve_credits'	, array( $this, 'twentyten_credits' ) );
		add_action( 'wp_meta'				, array( $this, 'wp_meta' ) );
		add_filter( 'post_class'			, array( $this, 'post_class' ), 10, 3 );
		global $thecartpress;
		if ( $thecartpress && $thecartpress->get_setting( 'disable_ecommerce' ) && $thecartpress->get_setting( 'disable_shopping_cart' ) ) add_action( 'admin_bar_menu', array( &$this, 'admin_bar_menu' ), 65 );
		add_action( 'wp_before_admin_bar_render', array( $this, 'wp_before_admin_bar_render' ) );
	}

	function admin_init() {
		//add_action( 'tcp_show_settings', array( $this, 'tcp_show_settings' ) );
		add_filter( 'admin_footer_text'	, array( $this, 'admin_footer_text' ) );
		add_action( 'wp_dashboard_setup', array( $this, 'wp_dashboard_setup' ) );
		add_action( 'admin_head'		, array( $this, 'admin_head' ) );
	}

	function tcp_the_currency( $currency ) {
		if ( $currency == 'EUR' ) return '&euro;';
		elseif ( $currency == 'CHF' ) return 'SFr.';
		elseif ( $currency == 'GBP' ) return '&pound;';
		elseif ( $currency == 'USD' || $currency == 'AUD' || $currency == 'CAD' || $currency == 'HKD' || $currency == 'SGD' ) return '$';
		elseif ( $currency == 'JPY' ) return '&yen;';
		elseif ( $currency == 'IRR' ) return 'ریال';
		elseif ( $currency == 'RUB' ) return 'руб';
		elseif ( $currency == 'ZAR' ) return 'R';
		elseif ( $currency == 'VEB' ) return 'BsF';
		elseif ( $currency == 'IDR' ) return 'Rp.';
		elseif ( $currency == 'DZD' ) return 'د.ج';
		elseif ( $currency == 'ILS' ) return '₪';
		else return $currency;
	}

	function twentyten_credits() { ?>
		<a href="http://thecartpress.com/" title="<?php esc_attr_e( 'eCommerce platform', 'tcp' ); ?>" rel="generator"><?php printf( __( 'Powered by %s.', 'tcp' ), 'TheCartPress' ); ?></a><?php
	}

	function admin_footer_text( $content ) {
		$pos = strrpos( $content, '</a>.' ) + strlen( '</a>' );
		$content = substr( $content, 0, $pos ) . ' and <a href="http://thecartpress.com">TheCartPress</a>' . substr( $content, $pos );
		return $content;
	}

	function wp_meta() {
		echo '<li class="tcp_meta"><a href="http://thecartpress.com" title="', __( 'Powered by TheCartPress, eCommerce platform for WordPress', 'tcp' ), '">TheCartPress.com</a></li>';
	}

	function theCartPressRSSDashboardWidget() {
		wp_widget_rss_output( 'http://thecartpress.com/feed', array( 'items' => 5, 'show_author' => 1, 'show_date' => 1, 'show_summary' => 0 ) );
	}

	function wp_dashboard_setup() {
		wp_add_dashboard_widget( 'tcp_rss_widget', __( 'TheCartPress blog', 'tcp' ), array( &$this, 'theCartPressRSSDashboardWidget' ) );
	}

	function admin_head() { ?>
<script type="text/javascript">
	function tcp_hide_product_fields( product_type ) {
		var speed = 'fast';
		if ('SIMPLE' == product_type) {
			jQuery('#tcp_price').parent().parent().fadeIn(speed);
			jQuery('#tcp_initial_units').parent().parent().fadeIn(speed);
			jQuery('#tcp_tax_id').parent().parent().fadeIn(speed);
			jQuery('#tcp_weight').parent().parent().fadeIn(speed);
			jQuery('#tcp_exclude_range').parent().parent().fadeIn(speed);
			jQuery('#tcp_attribute_sets').parent().parent().fadeIn(speed);
			jQuery('#tcp_is_downloadable').parent().parent().fadeIn(speed);
		}
		<?php do_action( 'tcp_hide_product_fields' ); ?>
	}
	jQuery(document).ready(function() {
		tcp_hide_product_fields(jQuery('#tcp_type option:selected').val());
		
		jQuery('#tcp_type').click(function() {
			tcp_hide_product_fields(jQuery('#tcp_type option:selected').val());
		});
	});
</script>
	<?php }

	function post_class( $classes, $class = '', $post_id = 0 ) {
		//if ( tcp_is_saleable( $post_id ) ) $classes[] = 'tcp_hentry';
		return $classes;
	}

	/**
	 * Improves the user experience in the settings page. DEPRECATED
	 */
	function tcp_show_settings() { ?>
		<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('.form-table').hide();
			jQuery('div.wrap h3').hide();
			var sections = jQuery('<ul class="tabs_section"></ul>');
			sections.insertAfter('div.wrap h2');
			var first_li = true;
			jQuery('div.wrap h3').each( function() {
				var next = jQuery(this).nextAll('.form-table');
				if (next) next = next[0];
				if (next) {
					var a = jQuery('<a href="" class="section_a">' + jQuery(this).text() + '</a>');
					a.attr('id', escape(jQuery(this).text()));
					a.click(function() {
						jQuery('.form-table').hide();
						jQuery(next).toggle();
						jQuery('ul.tabs_section li.section_active').removeClass('section_active');
						jQuery(this).parent().addClass('section_active');
						jQuery('#tcp_active_section').val(escape(jQuery(this).text()));
						return false;
					});
					var li = jQuery('<li></li>');
					if (first_li) {
						first_li = false;
						li.addClass('section_active');
					}
					li.append(a);
					sections.append(li);
				}
			});
			var first_section = jQuery('div.wrap h3');
			if (first_section) first_section = first_section[0];
			var next = jQuery(first_section).nextAll('.form-table');
			if (next) next = next[0];
			jQuery(next).toggle();
		});
		</script><?php
	}

	function admin_bar_menu() {
		global $wp_admin_bar;
		//if ( is_admin_bar_showing() && current_user_can( 'tcp_read_orders' ) ) {
		if ( current_user_can( 'tcp_read_orders' ) ) {
			$wp_admin_bar->add_menu(
				array(
					'id'	=> 'the_cart_press',
					'title'	=> __( 'Shopping', 'tcp' ),
					'href'	=> admin_url( 'admin.php' ) . '?page=thecartpress/admin/OrdersListTable.php',
				)
			);
			$wp_admin_bar->add_menu(
				array(
					'parent'	=> 'the_cart_press',
					'id'		=> 'orders_list',
					'title'		=>__( 'Orders', 'tcp' ),
					'href'		=> admin_url( 'admin.php' ) . '?page=thecartpress/admin/OrdersListTable.php',
				)
			);
			if ( current_user_can( 'tcp_downloadable_products' ) ) {
				$wp_admin_bar->add_menu(
					array(
						'parent'	=> 'the_cart_press',
						'id'		=> 'download_area',
						'title'		=> __( 'Download area', 'tcp' ),
						'href'		=> admin_url( 'admin.php' ) . "?page=thecartpress/admin/DownloadableList.php",
					)
				);
			}
		}
	}

	function wp_before_admin_bar_render() {
		global $wp_admin_bar;
		$tcp_admin_bar_hidden_items = get_option( 'tcp_admin_bar_hidden_items', array() );
		if ( isset( $wp_admin_bar->menu ) ) {
			$menu_bar = $wp_admin_bar->menu;
			foreach( $menu_bar as $id => $menu ) {
				if ( isset( $tcp_admin_bar_hidden_items[$id] ) ) {
					unset( $wp_admin_bar->menu->$id );
				} else {
					foreach( $menu as $id_menu => $menu_item ) {
						if ( $id_menu == 'children' ) {
							foreach( $menu_item as $id_item => $item ) {
								if ( isset( $tcp_admin_bar_hidden_items[$id_item] ) ) {
									unset( $menu_item->$id_item );
								}
							}
						}
					}
				}
			}
		}
	}
}

new UIImprovements();
} // class_exists check