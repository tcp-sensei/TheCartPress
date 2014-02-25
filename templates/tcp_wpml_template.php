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
 * WPML Multilanguage support
 */

function tcp_get_current_language_iso() {
	if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
		return ICL_LANGUAGE_CODE;
	} else {
		tcp_get_admin_language_iso();
	}
}

//Given a post_id this function returns the post_id in the default language
function tcp_get_default_id( $post_id, $post_type = false ) {
	global $sitepress;
	if ( $sitepress ) {
		if ( $post_type === false ) $post_type = get_post_type( $post_id );
		$default_language = $sitepress->get_default_language();
		return icl_object_id( $post_id, $post_type, true, $default_language );
	} else {
		return $post_id;
	}
}

//Given a post_id this function returns the equivalent post_id in the current language
function tcp_get_current_id( $post_id, $post_type = false ) {
	global $sitepress;
	if ( $sitepress ) {
		$default_language = $sitepress->get_current_language();
		if ( $post_type === false ) {
			$post_type = get_post_type( $post_id );
		}
		return icl_object_id( $post_id, $post_type, true, $default_language );
	} else {
		return $post_id;
	}
}

/**
 * Returns the list of translations from a given post_id
 * Example of returned array
 * array(2) {	["en"]=> object(stdClass)#45 (6) { ["translation_id"]=> string(2) "11" ["language_code"]=> string(2) "en" ["element_id"]=> string(1)  "9" ["original"]=> string(1) "1" ["post_title"]=> string(21) "Tom Sawyer Adventures"       ["post_status"]=> string(7) "publish" }
 * 				["es"]=> object(stdClass)#44 (6) { ["translation_id"]=> string(2) "12" ["language_code"]=> string(2) "es" ["element_id"]=> string(2) "10" ["original"]=> string(1) "0" ["post_title"]=> string(27) "Las Aventuras de Tom Sawyer" ["post_status"]=> string(7) "publish" } }
 */
function tcp_get_all_translations( $post_id, $post_type = false ) {
	global $sitepress;
	if ( $sitepress ) {
		if ( $post_type == false ) $post_type = get_post_type( $post_id );
		$trid = $sitepress->get_element_trid( $post_id, 'post_' . $post_type );
		return $sitepress->get_element_translations( $trid, 'post_'. $post_type );
	} else {
		return false;
	}
}

function tcp_get_default_language() {
	global $sitepress;
	if ( $sitepress ) {
		return $sitepress->get_default_language();
	} else {
		return '';
	}
}

function tcp_get_current_language() {
	global $sitepress;
	if ( $sitepress ) {
		return $sitepress->get_current_language();
	} else {
		return tcp_get_default_language();
	}
}

/**
 * This function adds a post identified by the $translate_post_id as a translation of the post identified by $post_id
 */
function tcp_add_translation( $post_id, $translate_post_id, $language, $post_type = false ) {
	if ( $post_type == false ) $post_type = get_post_type( $post_id );
	global $sitepress;
	if ( $sitepress ) {
		$trid = $sitepress->get_element_trid( $post_id, 'post_' . $post_type );
		$sitepress->set_element_language_details( $translate_post_id, 'post_' . $post_type, $trid, $language );
	}
}

function tcp_add_term_translation( $term_id, $taxonomy = TCP_PRODUCT_CATEGORY, $language = false ) {
	global $sitepress;
	if ( $language === false ) $language = $sitepress->get_default_language();
//echo 'tcp_add_term_translation ', $term_id, ', ', $taxonomy, ', ', $language, '...<br>';
	$sitepress->set_element_language_details( $term_id, 'tax_' . $taxonomy, null, $language );
}

/**
 * Registers one string to translate.
 */
function tcp_register_string( $context, $name, $value ) {
	if ( function_exists( 'icl_register_string' ) ) icl_register_string( $context, $name, $value );
}

/**
 * Unregisters one string to translate.
 */
function tcp_unregister_string( $context, $name ) {
	if ( function_exists( 'icl_unregister_string' ) ) icl_unregister_string( $context, $name );
}

/**
 * Returns the translation of a string identified by $context and $name
 *
 */
function tcp_string( $context, $name, $value ) {
	if ( function_exists( 'icl_t' ) ) return nl2br( icl_t( $context, $name, $value ) );
	else return $value;
}