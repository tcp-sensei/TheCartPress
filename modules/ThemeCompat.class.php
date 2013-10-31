<?php
/**
 * Theme Compatibility
 *
 * Outputs the single content of a saleable post type
 *
 * @package TheCartPress
 * @subpackage Modules
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'TCPThemeCompat' ) ) {

/**
 * Theme Compatibility approach 1.3.1
 *
 * @since TheCartPress (1.3.1)
 */
class TCPThemeCompat {
	private $post;
	private $posts;

	function __construct() {
		add_action( 'tcp_init', array( $this, 'tcp_init' ) );
	}

	function tcp_init() {
		global $thecartpress;
		//Removes the old the_content hook, used by TheCartPress
		// remove_filter( 'the_content'	, array( $thecartpress, 'the_content' ) );

		//Adds a new hook to create the new template hierarchy
		add_filter( 'template_include'	, array( $this, 'template_include' ) );
	}

	function template_include( $template ) {
		global $post;
		if ( ! $post ) return $template;
		//Catalogue page
		$catalogue_id = get_option( 'tcp_catalogue_page_id' );
		if ( $post->ID == $catalogue_id ) {
			//Template hierarchy
			//TODO Custom templates...
			//Searching for a template
			//TheCartPress loads one of the next ones and Theme Compatibility will inject the content
			$template_names = apply_filters( 'tcp_theme_compat_catalogue_template_names', array(
				'archive-' . $post->post_type . '.php',
				'archive-tcp_saleable.php',
				'thecartpress.php',
				'page.php',
				'index.php'
			), $post );
			$template = locate_template( $template_names );
			//Makes a new query and reset the global one
			global $wp_query, $thecartpress;
			$args = array(
				'post_type'			=> tcp_get_product_post_types(),
				'posts_per_page'	=> $thecartpress->get_setting( 'products_per_page', 10 ),
				'paged'				=> $wp_query->get('paged'),
				'meta_query'		=> array(
					'key'		=> 'tcp_is_visible',
					'value'		=> 1,
					'type'		=> 'NUMERIC',
					'compare'	=> '='
				),
			);
			$wp_query = new WP_Query( apply_filters( 'tcp_theme_compat_catalogue_wp_query_args', $args ) );
			//Reset the global query again
			$this->theme_compatibility_reset_post( array(
				'post_type'		=> 'tcp_product',
				'post_title'	=> $post->post_title,
				//'post_content'	=> $post->post_content,
				'is_archive'	=> true,
			) );
			//Adds a new 'the_content' hook
			add_filter( 'the_content', array( $this, 'the_content' ) );
			return $template;
		}
		//Only apply this hierarchy for saleable post types or taxonomies
		if ( ! tcp_is_saleable_post_type( $post->post_type ) ) return $template;
		//Displaying a saleable post (a product)
		if ( is_single() ) {
			//Template hierarchy
			//TODO Custom templates...
			//If the theme has any of this templates, Theme compatibility is deactivate
			$template_names = apply_filters( 'tcp_theme_compat_single_saleable_template_names', array(
				'single-' . $post->post_type . '.php',
				'single-tcp_saleable.php',
			), $post );
			//Searching for a template
			$template = locate_template( $template_names );
			//If the theme hasn't the previous templates then TheCartPress loads one of this
			//and Theme compatibility will inject the content
			if ( strlen( $template ) == 0 ) {
				$template_names = apply_filters( 'tcp_archive_saleable_default_template_names', array(
					'thecartpress.php',
					'page.php',
					'index.php'
				), $post );
				$this->theme_compatibility_reset_post( array(
					'post_title'	=> $post->post_title,
					'post_content'	=> $post->post_content,
					'is_single'		=> true,
				) );
				$template = locate_template( $template_names );
				//Adds a new 'the_content' hook
				add_filter( 'the_content', array( $this, 'the_content' ) );
			}
		} elseif ( is_tax() ) {
			$taxonomy	= get_query_var( 'taxonomy' );
			$term		= get_query_var( 'term' );
			//Template hierarchy
			//TODO Custom templates...
			//If the theme has any of this templates, Theme compatibility is deactivate
			$template_names = apply_filters( 'tcp_theme_compat_archive_saleable_template_names', array(
				'taxonomy-' . $taxonomy . $term . '.php',
				'taxonomy-' . $taxonomy . '.php',
				'taxonomy-tcp_saleable.php',
				'thecartpress.php',
				'taxonomy.php',
			), $post );
			//Searching for a template
			$template = locate_template( $template_names );
			//If the theme hasn't the previous templates then TheCartPress loads one of this
			//and Theme compatibility will inject the content
			if ( substr( $template, -strlen( 'thecartpress.php' ) ) === 'thecartpress.php' || strlen( $template ) == 0 ) {
				$template_names = apply_filters( 'tcp_theme_compat_taxonomy_saleable_default_template_names', array(
					'thecartpress.php',
					'page.php',
					'index.php'
				), $post );

				$obj = get_taxonomy( $taxonomy );
				$taxonomy_name = isset( $obj->labels->singular_name ) ? $obj->labels->singular_name : $taxonomy;
				$obj = get_term_by( 'slug', $term, $taxonomy );
				$term_name = isset( $obj->name ) ? $obj->name : $term;
				unset( $obj );
				$label = $taxonomy_name . ', ' . $term_name;

				$this->theme_compatibility_reset_post( array(
					'post_title'	=> $label,
					'post_type'		=> $post->post_type,
					'is_tax'		=> true,
					'is_archive'	=> true
				) );
				$template = locate_template( $template_names );
				//Adds a new 'the_content' hook
				add_filter( 'the_content', array( $this, 'the_content' ) );
			}
		} elseif ( is_archive() && ! is_author() ) {
			//Template hierarchy
			//TODO Custom templates...
			//If the theme has any of this templates, Theme compatibility is deactivate
			$template_names = apply_filters( 'tcp_theme_compat_archive_saleable_template_names', array(
				'archive-' . $post->post_type . '.php',
				'archive-tcp_saleable.php',
			), $post );
			//Searching for a template
			$template = locate_template( $template_names );

			//If the theme hasn't the previous templates then TheCartPress loads one of this
			//and Theme compatibility will inject the content
			if ( strlen( $template ) == 0 ) {
				$template_names = apply_filters( 'tcp_theme_compat_archive_saleable_default_template_names', array(
					'thecartpress.php',
					//'archive.php',
					'page.php',
					'index.php'
				), $post );

				$obj = get_post_type_object( $post->post_type );
				$post_type_name = isset( $obj->labels->singular_name ) ? $obj->labels->singular_name : $post->post_type;
				unset( $obj );

				$this->theme_compatibility_reset_post( array(
					'post_title'	=> $post_type_name,
					'post_type'		=> $post->post_type,
					'is_archive'	=> true
				) );
				$template = locate_template( $template_names );
				//Adds a new 'the_content' hook
				add_filter( 'the_content', array( $this, 'the_content' ) );
			}
		}
		return $template;
	}

	/**
	 * Injects code in single and taxonomy/archive templates
	 *
	 * @since 1.3.0
	 * @uses apply_filters (Called using 'tcp_template_single_product' and 'tcp_template_archive_product')
	 */
	function the_content( $content ) {
		global $post;

		//Removes this "the_content" hook, very important to avoid recursion
		$rem = remove_filter( 'the_content', array( $this, 'the_content' ) );
		if ( is_single() ) {
			//Recovers the current post (current product or saleable post)
			$post = $this->post;

			//Set the template to use. It will be, first, searched in your theme
			$template_name = 'tcp-single-product.php';
			$located = locate_template( $template_name );

			//If the theme has not this template, then the template available in TheCartPress will be used
			if ( strlen( $located ) == 0 ) $located = TCP_THEMES_TEMPLATES_FOLDER . $template_name;

			//Applies the template
			ob_start();
			require( apply_filters( 'tcp_template_single_product', $located ) );
			$content = ob_get_clean();
		} elseif ( is_tax() || is_archive() || $post->ID == get_option( 'tcp_catalogue_page_id' ) ) {
			global $wp_query;
			//Recovers the current post (current product or saleable post)
			if ( isset( $this->posts[0] ) ) $post = $this->posts[0];
			$wp_query->posts = $this->posts;
			$wp_query->post_count = count( $this->posts );
			$wp_query->rewind_posts();

			//Set the template to use. It will be, first, searched in your theme
			$template_name = 'tcp-archive-product.php';
			$located = locate_template( $template_name );

			//If the theme has not this template, then the template available in TheCartPress will be used
			//if ( $located == $template_name ) $located = TCP_THEMES_TEMPLATES_FOLDER . $template_name;
			if ( strlen( $located ) == 0 ) $located = TCP_THEMES_TEMPLATES_FOLDER . $template_name;

			//Applies the template
			ob_start();
			require( apply_filters( 'tcp_template_archive_product', $located ) );
			$content = ob_get_clean();
			//Set the query after the last post
			$wp_query->current_post = count( $this->posts );
		}
		add_filter( 'the_content', array( $this, 'the_content' ) );
		return $content;
	}

	protected function theme_compatibility_reset_post( $args = array() ) {
		global $wp_query, $post;
		//Saves the current post (current product or saleable post)
		//It will be recovered in "the_content" hook
		//$this->post = $post;
		$this->post = $wp_query->post;
		$this->posts = $wp_query->posts;

		$args = wp_parse_args( $args, array(
			'ID'					=> 0,//-9999,
			'post_status'			=> 'public',
			'post_author'			=> 0,
			'post_parent'			=> 0,
			'post_type'				=> 'page',
			'post_date'				=> 0,
			'post_date_gmt'			=> 0,
			'post_modified'			=> 0,
			'post_modified_gmt'		=> 0,
			'post_content'			=> '',
			'post_title'			=> '',
			'post_excerpt'			=> '',
			'post_content_filtered'	=> '',
			'post_mime_type'		=> '',
			'post_password'			=> '',
			'post_name'				=> '',
			'guid'					=> '',
			'menu_order'			=> 0,
			'pinged'				=> '',
			'to_ping'				=> '',
			'ping_status'			=> '',
			'comment_status'		=> 'closed',
			'comment_count'			=> 0,
			'filter'				=> 'raw',

			'is_404'				=> false,
			'is_page'				=> false,
			'is_single'				=> false,
			'is_archive'			=> false,
			'is_tax'				=> false,
			'is_home'				=> false,
		) );

		// Set the $post global
		$post = new WP_Post( (object) $args );

		// Copy the new post global into the main $wp_query
		$wp_query->post		= $post;
		$wp_query->posts	= array( $post );

		// Prevent comments form appearing
		$wp_query->post_count	= 1;
		$wp_query->is_404		= $args['is_404'];
		$wp_query->is_page		= $args['is_page'];
		$wp_query->is_single	= $args['is_single'];
		$wp_query->is_archive	= $args['is_archive'];
		$wp_query->is_tax		= $args['is_tax'];
		$wp_query->is_home		= $args['is_home'];

		// Clean up the args post
		unset( $args );

		/**
		 * Force the header back to 200 status if not a deliberate 404
		 */
		if ( ! $wp_query->is_404() ) status_header( 200 );
	}
}

new TCPThemeCompat();
} // class_exists check