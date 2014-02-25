<?php
/**
 * This file is part of TheCartPress.
 * 
 * TheCartPress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TheCartPress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TheCartPress.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * qTranslate Multilanguage support
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( function_exists( 'qtrans_convertURL' ) ) {
	add_filter( 'post_type_link', 'qtrans_convertURL' );
}

function tcp_get_current_language_iso() {
	if ( function_exists( 'qtrans_getLanguage' ) ) {
		return qtrans_getLanguage();
	} else {
		return tcp_get_admin_language_iso();
	}
}

//Given a post_id this function returns the post_id in the default language
function tcp_get_default_id( $post_id, $post_type = false ) {
	return $post_id;
}

//Given a post_id this function returns the equivalent post_id in the current language
function tcp_get_current_id( $post_id, $post_type = false ) {
	return $post_id;
}

/**
 * Returns the list of translations from a given post_id
 * Example of returned array
 * array(2) {	["en"]=> object(stdClass)#45 (6) { ["translation_id"]=> string(2) "11" ["language_code"]=> string(2) "en" ["element_id"]=> string(1)  "9" ["original"]=> string(1) "1" ["post_title"]=> string(21) "Tom Sawyer Adventures"       ["post_status"]=> string(7) "publish" }
 * 				["es"]=> object(stdClass)#44 (6) { ["translation_id"]=> string(2) "12" ["language_code"]=> string(2) "es" ["element_id"]=> string(2) "10" ["original"]=> string(1) "0" ["post_title"]=> string(27) "Las Aventuras de Tom Sawyer" ["post_status"]=> string(7) "publish" } }
 */
function tcp_get_all_translations( $post_id, $post_type = false ) {
	return false;
}

function tcp_get_default_language() {
	return tcp_get_current_language_iso();
	//return $this->tcp_get_current_language_iso();
}

function tcp_get_current_language() {
	return tcp_get_current_language_iso();
	//return $this->tcp_get_current_language_iso();
}

/**
 * This function adds a post identified by the $translate_post_id as a translation of the post identified by $post_id
 */
function tcp_add_translation( $post_id, $translate_post_id, $language, $post_type = 'tcp_product' ) {
	return;
}

function tcp_add_term_translation( $term_id, $taxonomy = TCP_PRODUCT_CATEGORY, $language = false ) {
	return;
}

/**
 * Registers one string to translate
 */
function tcp_register_string( $context, $name, $value ) {
}

/**
 * Unregisters one string to translate.
 */
function tcp_unregister_string( $context, $name ) {
}

/**
 * Returns the translation of a string identified by $context and $name
 */
function tcp_string( $context, $name, $value ) {
	return __( $value );
}
