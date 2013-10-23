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
?>
<html>
<head>
<meta charset="UTF-8" />
<title>

<?php wp_title( '|', true, 'right' ); bloginfo( 'name' ); ?>

</title>
<style>
	.tcp_order_page_name {
		text-align: left;
	}
	body,td,th {
		font-family: Arial, Helvetica, sans-serif;
		font-size: 12px;
		color: #333;
	}
	a:link {
		color: #C00;
		text-decoration: none;
	}
	a:hover {
		color: #960000;
		text-decoration: none;
	}
	a:visited {
		text-decoration: none;
		color: #C00;
	}
	a:active {
		text-decoration: none;
		color: #960000;
	}
	.tcp_shopping_cart_table .tcp_cart_name {
		text-align: left;
		bbackground-color: #f0f0f0;
		width: inherit;
		font-size: 12px;
	}
	.tcp_shopping_cart_table .tcp_cart_name a{
		text-align: left;
		text-decoration: none;
	}
	.tcp_shopping_cart_table {
		width: 90%;
		border:0px;
	}
	.tcp_shopping_cart_table tr th,
	.tcp_shopping_cart_table thead th {
		background-color: #333;
		padding: 4px 10px;
		line-height: 22px;
		color: #CCC;
	}
	.tcp_shopping_cart_table tr td {
		background-color: #f7f7f7;
		font-size: 11px;
		padding: 4px 10px;
		border-top: 1px dotted #ccc;
	}

	.tcp_shopping_cart_table tr.odd td {
		background-color: #FfF7FC;
	}
	
	#shipping_info {
		width: 50%;
		float: left;
	}

	#tcp_order_id th,
	#tcp_order_id td,
	#tcp_status th,
	#tcp_status td {
		text-align: left !important;
	}
</style>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>

<body>

<h1 id="site-title"><?php bloginfo( 'name' ); ?></h1>

<h2 id="site-description"><?php bloginfo( 'description' ); ?></h2>

<?php if ( isset( $_REQUEST['order_id'] ) ) {
	require_once( TCP_CLASSES_FOLDER . 'OrderPage.class.php' );
	$order_id = $_REQUEST['order_id'];
	OrderPage::show( $order_id, array( 'see_sku' => true ), true, true );
}?>

<p>
	<a href="javascript:print();"><?php _e( 'print', 'tcp' );?></a>
	&nbsp;|&nbsp;
	<a href="javascript:close();"><?php _e( 'close', 'tcp' );?></a>
</p>

</body>
</html>