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
 * along with This program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Allows to generate the xml for TheCartPress search engine
 */
class FeedForSearchEngine {
	function generateXML() {
		global $thecartpress;
		$tcp_guid = isset( $thecartpress->settings['search_engine_guid'] ) ? $thecartpress->settings['search_engine_guid'] : 'A';
		$guid = isset( $_REQUEST['guid'] ) ? $_REQUEST['guid'] : 'B';
		if ( $tcp_guid != $guid ) {
			header('Content-Type: text/xml;', true);
			echo '<?xml version="1.0" encoding="', get_option('blog_charset'), '"?', '>';
			echo '<error>';
			echo '<code>-1</code>';
			echo '<description>', __( 'Identification error', 'tcp'), '</description>';
			echo '</error>';
			return;
		}
		$search_engine_activated = isset( $thecartpress->settings['search_engine_activated'] ) ? $thecartpress->settings['search_engine_activated'] : true;
		if ( $search_engine_activated ) {
			header( 'Content-Type: text/xml;', true );
			header( 'Cache-Control: no-cache, must-revalidate' ); // HTTP/1.1
			header( '"Expires: Sat, 26 Jul 1997 05:00:00 GMT' ); // Date in the past
			//header('Content-Type: text/html;', true);
			echo '<?xml version="1.0" encoding="', get_option('blog_charset'), '"?', '>';
			echo '<catalog>';
			echo '<name>', bloginfo('name'), '</name>';
			echo '<url>', bloginfo('url'), '</url>';
			echo '<desc>', bloginfo("description"), '</desc>';
			echo '<currency>', tcp_get_the_currency(), '</currency>';
			$args = array(
				'post_type'		=> 'tcp_product', //tcp_get_saleable_post_types() TODO
				'numberposts'	=> -1,
				'post_status'	=> 'publish',
				'fields'		=> 'ids',
			); 
			$products = get_posts( $args );
			if ( is_array( $products ) && count( $products ) > 0 ) {
				echo '<prods>';
				foreach ( $products as $id ) {
					$product = get_post( $id );
					echo '<prod>';
					echo '<product_id>', $product->ID, '</product_id>';
					echo '<name><![CDATA[', $product->post_title, ']]></name>';
					echo '<url><![CDATA[', $product->guid, ']]></url>';
					echo '<created>', $product->post_date, '</created>';
					$image_id = get_post_thumbnail_id($product->ID);
					$image_url = wp_get_attachment_image_src($image_id);
					if ($image_url ) {
						$image_url = $image_url[0];
						if ($image_url) echo '<thumbnail>', $image_url, '</thumbnail>';
					}
					echo '<modified>', $product->post_modified, '</modified>';
					//echo '<content><![CDATA[', $product->post_content, ']]></content>';
					echo '<excerpt><![CDATA[', $product->post_excerpt, ']]></excerpt>';
					echo '<type>', tcp_get_the_product_type( $product->ID ), '</type>';
					echo '<price>', tcp_get_the_price( $product->ID ), '</price>';
					echo '<tax>';
					$taxes = tcp_get_the_taxes( $product->ID );
					if ( is_array( $taxes ) && count( $taxes ) > 0 ) foreach( $taxes as $tax )
						echo $tax->rate, '%';
					echo '</tax>';
					echo '<cats>', $this->getCategories( $product->ID ), '</cats>';
					echo '<tags>', $this->getTags( $product->ID ), '</tags>';
					echo '<supps>', $this->getSuppliers( $product->ID ), '</supps>';
					echo '</prod>';
				}
				echo '</prods>';
			}
			echo '</catalog>';
		} else {
			header('Content-Type: text/xml;', true);
			echo '<?xml version="1.0" encoding="', get_option('blog_charset'), '"?', '>';
			echo '<error>';
			echo '<code>-2</code>';
			echo '<description>', __( 'TheCartPress search engine property is deactivate', 'tcp'), '</description>';
			echo '</error>';
		}
	}

	private function getTags( $post_id ) {
		return $this->getTerms( $post_id, 'tcp_product_tag');
	}

	private function getSuppliers( $post_id ) {
		return $this->getTerms( $post_id, 'tcp_product_supplier');
	}

	private function getCategories( $post_id ) {
		return $this->getTerms( $post_id, 'tcp_product_category');
	}

	private function getTerms( $post_id, $taxonomy ) {
		$post_terms = get_the_terms( $post_id, $taxonomy );
		$terms = array();
		if ( is_array( $post_terms ) && count( $post_terms ) > 0 ) {
			foreach( $post_terms as $term )
				$terms[] = $term->name;
			return implode( ',', $terms );
		}
		else return '';
	}
}
?>
