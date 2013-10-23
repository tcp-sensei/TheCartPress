<?php
/**
 * Extends
 *
 * adds a showcase of TheCartPress extend site
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
 * along with This program.  If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'TCPExtend' ) ) {
class TCPExtend {

	static function tcp_admin_menu() {
		global $thecartpress;
		if ( $thecartpress ) {
			$page = add_menu_page( __( 'Extend', 'tcp' ), __( 'Extend', 'tcp' ), 'tcp_edit_products', 'extend', array( __CLASS__, 'admin_page' ), plugins_url( '/images/tcp.png', dirname( __FILE__ ) ), 44 );
		} else {
			$page = add_menu_page( __( 'Extend Store', 'tcp' ), __( 'Extend', 'tcp' ), 'tcp_edit_products', 'extend', array( __CLASS__, 'admin_page' ), plugins_url( '/images/tcp.png', dirname( __FILE__ ) ) );
		}
		add_action( "load-$page", array( __CLASS__, 'admin_load' ) );
	}

	static function admin_load() {
		get_current_screen()->add_help_tab( array(
			'id'	  => 'overview',
			'title'   => __( 'Overview' ),
			'content' =>
				'<p>' . __( 'Extended modules.', 'tcp' ) . '</p>'
		) );

		get_current_screen()->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'tcp' ) . '</strong></p>' .
			'<p>' . __( '<a href="http://thecartpress.com" target="_blank">Documentation on TheCartPress</a>', 'tcp' ) . '</p>' .
			'<p>' . __( '<a href="http://community.thecartpress.com/" target="_blank">Support Forums</a>', 'tcp' ) . '</p>'
		);
		wp_register_script( 'jquery.xmlrpc', plugins_url( 'thecartpress/js/jquery.xmlrpc.js' ) );
		wp_enqueue_script( 'jquery.xmlrpc' );
	}

	static function admin_page() { ?>
<div class="wrap">
	<?php screen_icon( 'tcp-extend' ); ?><h2><?php _e( 'Extend', 'tcp' ); ?><?php tcp_the_feedback_image( 'tcp_extend_feedback' ); ?></h2>
	<div class="tcp-category-list">
		<div class="tcp-category" style="display:none;">
			<div class="tcp-category-title"></div>
			<div class="tcp-product" style="display:none;">
				<img src="" class="tcp-product-img"/>
				<div class="tcp-product-title"></div>
			</div>
		</div>
	</div>

<div class="tcp-products tcp-tcpf">

	<div class="tcp-col-xs-6 tcp-col-sm-4 tcp-col-md-3 tcp-col-lg-2 tcp-product-template tcp-product" style="display: none;">

		<a href="#" class="tcp-extend-link">
			<div class="tcp-inner-product slide">
	
				<img class="tcp-img-responsive tcp-product-img" src="" />
	
				<div class="tcp-product-title"></div><!-- .tcp-product-title -->
	
				<div class="tcp-product-caption">
	
					<h3 class="tcp-product-title"></h3>
	
					<p class="tcp-excerpt"></p>
	
					<p><a href="#" class="tcp-learn-more tcp-extend-link"><?php _e( 'Learn more', 'tcp' ); ?></a></p>
	
			</div><!-- .tcp-inner-product -->
		</a>
	</div><!-- .tcp-product -->

</div><!-- .tcp-products -->

</div><!-- .wrap -->

<script>
var URL = 'http://extend.thecartpress.com/xmlrpc.php';
var TAXONOMY = 'tcp_product_category';

function tcp_get_categories( parent, callback ) {
	jQuery.xmlrpc( {
		url : URL,
		methodName : 'tcp.getCategories',
		params : [ 0, '', '', { taxonomy : TAXONOMY, parent : parent }, true ],
		datatype : 'jsonp',
		success: function( response, status, jqXHR ) {
			callback( response[0] );
			return false;
		},
		error : function( jqXHR, status, error ) {
			//console.log(error);
			return false;
		}
	} );
}

var load_categories = function( terms ) {
	var parent = jQuery( '.tcp-category-list' );
	for( var id in terms ) {
		var term = terms[id];
		var cat = jQuery( '.tcp-category' ).clone();
		cat.removeClass( 'tcp-category' );
		cat.addClass( 'tcp-category-' + term.id );
		var cat_title = cat.find( '.tcp-category-title');
		cat_title.text( term.name );
		parent.append( cat );
		cat.show();
	}
};

function tcp_get_products( term_id, taxonomy, callback ) {
	//window.sessionStorage.removeItem( 'tcp_extend_items' );
	if ( window.sessionStorage ) {
		var products = window.sessionStorage.getItem( 'tcp_extend_items' );
		if ( products ) {
			callback( JSON.parse( products ) );
			return;
		}
	}
	var feedback = jQuery( '.tcp_extend_feedback' );
	feedback.show();
	jQuery.xmlrpc( {
		url : URL,
		methodName : 'tcp.getProducts',
		params : [ 0, '', '', { term_id : term_id, taxonomy : taxonomy }, true ],
		datatype : 'jsonp',
		success: function( response, status, jqXHR ) {
			feedback.hide();
			if ( window.sessionStorage ) {
				window.sessionStorage.setItem( 'tcp_extend_items', JSON.stringify( response[0] ) );
			}
			callback( response[0] );
			return false;
		},
		error : function( jqXHR, status, error ) {
			feedback.hide();
			console.log(error); //TODO
			return false;
		}
	} );
}

var load_products = function( products ) {
	var parent_div = jQuery( '.tcp-products' );
	if ( parent_div ) {
		for( var id in products ) {
			var item = products[id];
			var div = parent_div.find( '.tcp-product-template' ).clone();
			div.removeClass( 'tcp-product-template' );
			div.addClass( 'tcp-product-' + id );
			var img = div.find( '.tcp-product-img' );
			img.attr( 'src', item.thumbnail );
			var title = div.find( '.tcp-product-title' );
			title.text( item.title );
			var a = div.find( '.tcp-extend-link' );
			a.attr( 'href', item.url ).attr( 'target', '_blank' );
			var excerpt = div.find( '.tcp-excerpt' );
			excerpt.html( item.excerpt );
			parent_div.append( div );
			div.show();
		}
	}
};

jQuery('.standard').live( 'mouseenter',  function() {
	jQuery(this).find('.tcp-product-caption').stop(true, true).show();
} );

jQuery('.standard').live( 'mouseleave',  function() {
	jQuery(this).find('.tcp-product-caption').stop(true, true).hide();
} );

jQuery('.fade').live( 'mouseenter',  function() {
	jQuery(this).find('.tcp-product-caption').stop(true, true).fadeIn(250)
} );

jQuery('.fade').live( 'mouseleave',  function() {
	jQuery(this).find('.tcp-product-caption').stop(true, true).fadeOut(250);
} );

jQuery('.slide').live( 'mouseenter',  function() {
	jQuery(this).find('.tcp-product-caption').stop(true, true).slideDown(250)
} );

jQuery('.slide').live( 'mouseleave',  function() {
	jQuery(this).find('.tcp-product-caption').stop(true, true).slideUp(450);
} );

jQuery().ready( function(event, ui) {
	//tcp_get_categories( 43, load_categories );
	tcp_get_products( '10', 'category-price', load_products );
	
} );
</script>
	<?php }
}

add_action( 'tcp_admin_menu', 'TCPExtend::tcp_admin_menu' );
} // class_exists check