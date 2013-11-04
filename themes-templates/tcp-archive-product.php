<?php
/**
 * The template for displaying all products or salebale posts.
 *
 * @package TheCartPress
 * @subpackage Theme Compatibility
 * @since TheCartPress 1.3
 */

?>

	<?php if ( have_posts() ) : ?>

		<?php if ( is_active_sidebar( 'sidebar-layered' ) ) : ?>
			<div class="horizontal-layered">
				<?php dynamic_sidebar( 'sidebar-layered' ); ?>
			</div>
		<?php endif; ?>

		<?php tcp_the_loop(); /* Start the Loop */ ?>

	<?php else : ?>
		<?php get_template_part( 'content', 'none' ); ?>
	<?php endif; ?>