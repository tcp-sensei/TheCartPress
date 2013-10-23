<?php
/**
 * The template for displaying all products or salebale posts.
 *
 * @package TheCartPress
 * @subpackage Theme Compatibility
 * @since TheCartPress 1.3
 */

//Set the template to use. It will be, first, searched in your theme
$template_name = 'loop-tcp-grid.php';
$located = locate_template( $template_name );
//If the theme has not this template, then the template available in TheCartPress will be used
if ( strlen( $located ) == 0 ) $located = TCP_THEMES_TEMPLATES_FOLDER . $template_name;
?>

	<?php if ( have_posts() ) : ?>

		<?php if ( is_active_sidebar( 'sidebar-layered' ) ) : ?>
			<div class="horizontal-layered">
				<?php dynamic_sidebar( 'sidebar-layered' ); ?>
			</div>
		<?php endif; ?>

		<?php /* Start the Loop */
		include( $located ); ?>

	<?php else : ?>
		<?php get_template_part( 'content', 'none' ); ?>
	<?php endif; ?>
