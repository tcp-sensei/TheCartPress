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

/**
 * Author: Joy Reynolds and TheCartPress team
 */
class SalesChartDashboard {

	function show() {
		if ( current_user_can( 'tcp_edit_orders' ) ) {
			$settings		= get_option( 'tcp_chart' );
			$days_prev		= isset( $settings['days_prev'] ) ? $settings['days_prev'] : 7;
			$chart_type		= isset( $settings['chart_type'] ) ? $settings['chart_type'] : 'LineChart';
			$status			= isset( $settings['order_status'] ) ? $settings['order_status'] : '';
			$orderdata		= Orders::getCounts( $status, $days_prev );
		} else {
			global $current_user;
			get_currentuserinfo();
			$customer_id	= $current_user->ID;
			$chart_type		= 'LineChart';
			$days_prev		= 7;
			$status			= Orders::$ORDER_COMPLETED;
			$orderdata		= Orders::getCounts( $status, $days_prev, $customer_id );
		}
		$sales_color = "green";
		$order_color = "#567";
		$days = array();
		$date = date( 'Y-m-d', strtotime( date( 'Y-m-d' ) . ' +1 day' ) );
		for( $d = $days_prev; $d > 0; $d-- ) {
			$date = date( 'Y-m-d', strtotime( $date . ' -1 day' ) );
			$days[$date] = array( $date, 0, 0 );
		}
		foreach( $orderdata as $order ) {
			$order->sales = Orders::getAmountByDay( $order->thedate, $status );
			$days[$order->thedate] = array( $order->thedate, $order->sales, $order->count );
		}
		$script = '';
		if ( $chart_type == 'Table' || $chart_type == 'Gauge' ) {
			$data_column  = 'data.addColumn("date", "' . __( 'Date', 'tcp' ) . '");' . "\n";
			$data_column .= 'data.addColumn("number", "' . __( 'Sales amount', 'tcp' ) . '");' . "\n";
			$data_column .= 'data.addColumn("number", "' . __( 'Orders', 'tcp' ) . '");' . "\n";
		    $r = 0;
		    $script = 'data.addRows(' . count($days) . ');' . "\n";
		    foreach( $days as $day ) {
				$script .= 'data.setCell(' . $r . ', 0, new Date("'. date( 'M j, Y', strtotime( $day[0] ) ) . '"));' . "\n";
				$script .= 'data.setCell(' . $r . ', 1, '. $day[1] . ');' . "\n";
				$script .= 'data.setCell(' . $r . ', 2, '. $day[2] . ');' . "\n";
				$r++;
			}
			if ( $chart_type == 'Table') {
				$options = 'var options = { showRowNumber: true };' . "\n";
			} elseif ( $chart_type == 'Gauge' ) {
				$options = 'var options = {
					width: "100%",
					height: 300,
					redFrom: 20,
					redTo: 30,
					yellowFrom:10,
					yellowTo: 20,
					minorTicks: 5,
					max: 30
				};';
			}
		} elseif ( $chart_type == 'MotionChart' ) {
			$data_column  = 'data.addColumn("string", "' . __( 'Type', 'tcp' ) . '");' . "\n";
			$data_column .= 'data.addColumn("date", "' . __( 'Date', 'tcp' ) . '");' . "\n";
			$data_column .= 'data.addColumn("number", "' . __( 'Amount', 'tcp' ) . '");' . "\n";
				
			foreach( $days as $day ) {
				$script = "\t['Sales', new Date('" . date( 'M j, Y', strtotime( $day[0] ) ) . "'), " . $day[1] . "],\n" . $script;
				$script = "\t['Orders', new Date('" . date( 'M j, Y', strtotime( $day[0] ) ) . "'), " . $day[2] . "],\n" . $script;
			}
			$script = 'data.addRows([' . rtrim( $script, ",\n" ) . ']);';
			$options = 'var options = {
				width: "100%",
				height: 300
			};';
		} else {
			$data_column  = 'data.addColumn("date", "' . __( 'Date', 'tcp' ) . '");' . "\n";
			$data_column .= 'data.addColumn("number", "' . __( 'Sales amount', 'tcp' ) . '");' . "\n";
			$data_column .= 'data.addColumn("number", "' . __( 'Orders', 'tcp' ) . '");' . "\n";
			foreach( $days as $day ) {
				$script = "\t[new Date('". date( 'M j, Y', strtotime( $day[0] ) ) . "'), " . $day[1] . ", " . $day[2] . "],\n" . $script;
			}
			$script = 'data.addRows([' . rtrim( $script, ",\n" ) . ']);';
			if ( $chart_type == 'AreaChart' ) {
				$options = 'var options = {
					width: "100%",
					height: 300,
					title: "' . __( 'Sales and Orders', 'tcp' ) . '",
					hAxis: {
						title: "dates",
						titleTextStyle: {
							"color": "#FF0000"
						}
					}
				};';
            } else {
				$options = 'var options = {
					width: "100%",
					height: 300,
					title: "' . __( 'Sales and Orders', 'tcp' ) . '",
					series: [{
						color: "' . $sales_color .'",
						pointSize: 2
					}, {
						color: "' . $order_color . '",
						pointSize: 2,
						targetAxisIndex: 1
					}],
					vAxes: {
						0: {
							title: "' . __( 'Sales', 'tcp' ) . '",
							textStyle: {
							 	color: "' . $sales_color . '"
							}
						},
						1:{
							title: "' . __( 'Orders', 'tcp' ) . '",
							textStyle: {
								color: "' . $order_color . '"
							}
						}
					}
				};';
			}
		} ?>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
	google.load('visualization', '1', {packages: ['corechart']});
	<?php if ( $chart_type == 'Table' ) : ?>google.load('visualization', '1', {packages: ['table']});<?php endif; ?>
	<?php if ( $chart_type == 'Gauge' ) : ?>google.load('visualization', '1', {packages: ['Gauge']});<?php endif; ?>
	<?php if ( $chart_type == 'MotionChart' ) : ?>google.load('visualization', '1', {packages: ['MotionChart']});<?php endif; ?>
	google.setOnLoadCallback(drawChart);
	function drawChart() {
		var data = new google.visualization.DataTable();
		<?php echo $data_column; ?>
		<?php echo $script; ?>
		var chart = new google.visualization.<?php echo $chart_type; ?>(document.getElementById('tcp_sales_chart_area'));
		<?php echo $options; ?>
		chart.draw(data, options);
	}
	</script>
	<div id="tcp_sales_chart_area"></div><?php
	}

	function show_form() {
		$settings = get_option( 'tcp_chart' );
		if ( ! $settings ) $settings = array();
		if ( isset( $_REQUEST['save_chart'] ) ) {
			$settings['chart_type']	= isset( $_REQUEST['chart_type'] ) ? $_REQUEST['chart_type'] : 'LineChart';
			$settings['days_prev']	= isset( $_REQUEST['days_prev'] ) ? $_REQUEST['days_prev'] : 7;
			$settings['order_status']	= isset( $_REQUEST['order_status'] ) ? $_REQUEST['order_status'] : '';
			update_option( 'tcp_chart', $settings );
		}
		$chart_type		= isset( $settings['chart_type'] ) ? $settings['chart_type'] : 'LineChart';
		$days_prev		= isset( $settings['days_prev'] ) ? $settings['days_prev'] : 7;
		$order_status	= isset( $settings['order_status'] ) ? $settings['order_status'] : '';
?>
<div class="tcp_chart_form">
<input type="hidden" name="save_chart" value="save_chart">
<p>
	<label for="chart_type"><?php _e( 'Chart type', 'tcp' ); ?>:
		<select id="chart_type" name="chart_type">
			<option value="LineChart" <?php selected( $chart_type, 'LineChart' );?>><?php _e( 'Line Chart', 'tcp' ); ?></option>
			<!--<option value="BarChart" <?php selected( $chart_type, 'BarChart' );?>><?php _e( 'Bar Chart', 'tcp' ); ?></option>-->
			<!--<option value="ColumnChart" <?php selected( $chart_type, 'ColumnChart' );?>><?php _e( 'Column Chart', 'tcp' ); ?></option>-->
			<option value="AreaChart" <?php selected( $chart_type, 'AreaChart' );?>><?php _e( 'Area Chart', 'tcp' ); ?></option>
			<!--<option value="ComboChart" <?php selected( $chart_type, 'ComboChart' );?>><?php _e( 'Combo Chart', 'tcp' ); ?></option>-->
			<option value="MotionChart" <?php selected( $chart_type, 'MotionChart' );?>><?php _e( 'Motion Chart', 'tcp' ); ?></option>
			<option value="ScatterChart" <?php selected( $chart_type, 'ScatterChart' );?>><?php _e( 'Scatter', 'tcp' ); ?></option>
			<option value="Table" <?php selected( $chart_type, 'Table' );?>><?php _e( 'Table', 'tcp' ); ?></option>
			<option value="Gauge" <?php selected( $chart_type, 'Gauge' );?>><?php _e( 'Gauge', 'tcp' ); ?></option>
		</select>
	</label>
</p>
<p>
	<label for="days_prev"><?php _e( 'Days prev', 'tcp' ); ?>:
		<input type="text" id="days_prev" name="days_prev" value="<?php echo $days_prev; ?>" min="0" max="31" size="2" maxlength="2"/>
	</label>
</p>
<p>
	<label for="order_status"><?php _e( 'Order status', 'tcp' ); ?>:
	<select id="order_status" name="order_status">
		<option value=""<?php selected( $status['name'], '' );?>><?php _e( 'All', 'tcp'); ?></option>
	<?php $order_status_list = tcp_get_order_status();
	foreach ( $order_status_list as $status ) : ?>
		<option value="<?php echo $status['name'];?>"<?php selected( $status['name'], $order_status );?>><?php echo $status['label']; ?></option>
	<?php endforeach; ?>
	</select>
	</label>
</p>
</div><?php
	}

	function __construct() {
		if ( current_user_can( 'tcp_edit_orders' ) ) {
			wp_add_dashboard_widget( 'tcp_sales_chart', __( 'Sales and Orders', 'tcp' ), array( $this, 'show' ), array( $this, 'show_form' ) );
		} else {
			wp_add_dashboard_widget( 'tcp_sales_chart', __( 'Sales and Orders', 'tcp' ), array( $this, 'show' ) );
		}
	}
}

new SalesChartDashboard();
?>