<?php
/**
 * Reports
 *
 * Graphical Reports
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.	If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'TCPReports' ) ) {

class TCPReports {

	static function init() {
		add_action( 'tcp_customize_admin_bar', array( __CLASS__, 'tcp_customize_admin_bar' ) );
		if ( is_admin() ) {
			add_action( 'tcp_admin_menu', array( __CLASS__, 'tcp_admin_menu' ) );
		}
	}

	static function tcp_customize_admin_bar( $wp_admin_bar ) {
		$wp_admin_bar->add_menu( array(
			'id'		=> 'tcp-reports-page',
			'parent'	=> 'tcp-custom-menu',
			'title'		=> __( 'Reports', 'tcp' ),
			'href'		=> admin_url( 'admin.php?page=tcp_reports' ),
		) );
	}

	static function tcp_admin_menu() {
		if ( ! current_user_can( 'tcp_edit_settings' ) ) return;
		$base = thecartpress()->get_base();
		$page = add_submenu_page( $base, __( 'Reports', 'tcp' ), __( 'Reports', 'tcp' ), 'tcp_edit_settings', 'tcp_reports', array( __CLASS__, 'admin_page' ) );
		add_action( "load-$page", array( __CLASS__, 'admin_load' ) );
	}

	static function admin_load() {
		get_current_screen()->add_help_tab( array(
			'id'	  => 'overview',
			'title'   => __( 'Overview' ),
			'content' =>
				'<p>' . __( 'Displys Reports.', 'tcp' ) . '</p>'
		) );

		get_current_screen()->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'tcp' ) . '</strong></p>' .
			'<p>' . __( '<a href="http://thecartpress.com" target="_blank">Documentation on TheCartPress</a>', 'tcp' ) . '</p>' .
			'<p>' . __( '<a href="http://community.thecartpress.com/" target="_blank">Support Forums</a>', 'tcp' ) . '</p>'
		);
		wp_register_script( 'Chart', plugins_url( 'thecartpress/js/Chart.min.js' ) );
		wp_enqueue_script( 'Chart' );
	}

	static function admin_page() { ?>
<div class="wrap">
	<?php screen_icon( 'tcp-reports' ); ?><h2><?php _e( 'Reports', 'tcp' ); ?></h2>

	<?php TCPReports::getOrdersByYear(); ?>

	<?php //TCPReports::getOrdersByProduct(); ?>

</div><!-- .wrap -->
	<?php }

	static function getOrdersByYear() {
	$rows = Orders::getOrdersByYear( 2013 );
	$months = array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0 );
	foreach( $rows as $row ) {
		$month = date( 'm', strtotime( $row->created_at ) );
		$months[$month-1] = $row->price * $row->qty_ordered - $row->discount_amount;
	} ?>
<h3><?php _e( 'Sales By Month', 'tcp' ); ?></h3>
<canvas id="chart_by_year" width="800" height="400"></canvas>
<script>
var ctx = document.getElementById( 'chart_by_year' ).getContext( '2d' );
var data = {
	labels : [ 'Jan','Feb', 'Mar', 'Apr', 'May', 'June', 'July', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic' ],
	datasets : [
		{
			fillColor : "rgba(220,220,220,0.5)",
			strokeColor : "rgba(220,220,220,1)",
			pointColor : "rgba(220,220,220,1)",
			pointStrokeColor : "#fff",
			data : [ <?php foreach( $months as $month ) echo $month, ', '; ?> ]
		}
	]
};
var options = {
	//Boolean - If we show the scale above the chart data			
	scaleOverlay : false,
	//Boolean - If we want to override with a hard coded scale
	scaleOverride : false,
	//** Required if scaleOverride is true **
	//Number - The number of steps in a hard coded scale
	scaleSteps : null,
	//Number - The value jump in the hard coded scale
	scaleStepWidth : null,
	//Number - The scale starting value
	scaleStartValue : null,
	//String - Colour of the scale line	
	scaleLineColor : "rgba(0,0,0,.1)",
	//Number - Pixel width of the scale line	
	scaleLineWidth : 1,
	//Boolean - Whether to show labels on the scale	
	scaleShowLabels : true,
	//Interpolated JS string - can access value
	scaleLabel : "<%=value%>",
	//String - Scale label font declaration for the scale label
	scaleFontFamily : "'Arial'",
	//Number - Scale label font size in pixels	
	scaleFontSize : 12,
	//String - Scale label font weight style	
	scaleFontStyle : "normal",
	//String - Scale label font colour	
	scaleFontColor : "#666",	
	///Boolean - Whether grid lines are shown across the chart
	scaleShowGridLines : true,
	//String - Colour of the grid lines
	scaleGridLineColor : "rgba(0,0,0,.05)",
	//Number - Width of the grid lines
	scaleGridLineWidth : 1,	
	//Boolean - Whether the line is curved between points
	bezierCurve : true,
	//Boolean - Whether to show a dot for each point
	pointDot : true,
	//Number - Radius of each point dot in pixels
	pointDotRadius : 3,
	//Number - Pixel width of point dot stroke
	pointDotStrokeWidth : 1,
	//Boolean - Whether to show a stroke for datasets
	datasetStroke : true,
	//Number - Pixel width of dataset stroke
	datasetStrokeWidth : 2,
	//Boolean - Whether to fill the dataset with a colour
	datasetFill : true,
	//Boolean - Whether to animate the chart
	animation : true,
	//Number - Number of animation steps
	animationSteps : 60,
	//String - Animation easing effect
	animationEasing : "easeOutQuart",
	//Function - Fires when the animation is complete
	onAnimationComplete : null
};
var chart_by_year = new Chart( ctx ).Line( data, options );
</script>
<?php }

}

add_action( 'init', 'TCPReports::init' );
} // class_exists check