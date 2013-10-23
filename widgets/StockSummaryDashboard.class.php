<?php
/**
 * Stock Summary
 *
 * Allows to display the stock summary in the dashboard
 *
 * @package TheCartPress
 * @subpackage Widgets
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

if ( ! class_exists( 'StockSummaryDashboard' ) ) {

class StockSummaryDashboard {

	function __construct() {
		add_action( 'init'				, array( $this, 'init' ) );
		add_action( 'wp_dashboard_setup', array( $this, 'wp_dashboard_setup' ) );
	}

	function init() {
		add_action( 'wp_ajax_tcp_stock_summary_dashboard', array( $this, 'tcp_stock_summary_dashboard' ) );
	}

	function wp_dashboard_setup() {
		if ( current_user_can( 'tcp_edit_orders' ) || current_user_can( 'tcp_edit_order' ) ) {
			wp_add_dashboard_widget( 'tcp_stock_resume', __( 'Stock Summary', 'tcp' ), array( $this, 'show' ) );
		}
	}

	function show() { ?>
<div class="table table_content">
	<table style="width:100%" id="table_stock_summary">
	<tbody>
	<tr class="first">
		<td id="tcp_stock_sumary_no_items" class="first b" colspan="2">
			<img src="<?php echo admin_url( 'images/loading.gif' ); ?>" id="tcp_stock_summary_feedback" />
			<?php _e( 'No items to show', 'tcp' ); ?>
		</td>
	</tr>
	</tbody></table>
	<script>
	jQuery('.tcp_stock_summary_feedback').show();
	jQuery.ajax({
		async	: true,
		type	: "GET",
		url		: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
		data	: {
			action	: 'tcp_stock_summary_dashboard',
		},
		success : function(response) {
			jQuery('#tcp_stock_summary_feedback').hide();
			response = eval(response);
			if (response.length > 0) {
				jQuery('#tcp_stock_sumary_no_items').hide();
				for(i in response) {
					var row = response[i];
					var html = '<tr><td class="first b"><a href="post.php?action=edit&post=' + row['id'] + '">' + row['title'] + '</a></td>';
					html += '<td class="t tcp_stock_' + row['stock'] + '">' + row['stock'] + '</td></tr>';
					jQuery('#table_stock_summary tr:last').after(html);
				}
			}
		},
		error	: function(response) {
			jQuery('.tcp_stock_summary_feedback').hide();
		},
	});
	</script>
</div>
	<?php }

	function tcp_stock_summary_dashboard() {
		if ( current_user_can( 'tcp_edit_orders' ) ) {
			$customer_id = -1;
		} else {
			global $current_user;
			get_currentuserinfo();
			$customer_id = $current_user->ID;
		} 
		$args = array(
			'post_type'		=> tcp_get_saleable_post_types(), //TCP_PRODUCT_POST_TYPE,
			'numberposts'	=> 5,
			'post_status'	=> 'publish',
			'fields'		=> 'ids',
			'meta_query'	=> array(
				array(
					'key'		=>'tcp_stock',
					//'type'		=> 'NUMERIC',
					'compare'	=> '>',
					'value'		=> -1,
				),
			),
			'orderby'		=> 'meta_value_num',
			'meta_key'		=> 'tcp_stock',
			'order'			=> 'asc',
		);
		if ( $customer_id > 0 ) $args['author'] = $customer_id;
		$result = array();
		$ids = get_posts( $args );
		foreach ( $ids as $id ) {
			$result[] = array(
				'id'	=> $id,
				'title'	=> get_the_title( $id ),
				'stock'	=> tcp_get_the_stock( $id ),
			);
		}
		die( json_encode( $result ) );
	}
}

new StockSummaryDashboard();
} // class_exists check