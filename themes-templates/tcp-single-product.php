<?php
/**
 * Outputs the single content of a saleable post type
 *
 * @package TheCartPress
 * @subpackage Theme Compatibility
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

global $post, $thecartpress;

$suffix = '-' . $post->post_type;
$image_size_content = $thecartpress->get_setting( 'image_size_content' . $suffix, 'tcp-none' );
if ( $image_size_content ==  'tcp-none' ) {
	$suffix = '';
	$image_size_content = $thecartpress->get_setting( 'image_size_content' . $suffix, 'large' );
}
$attachments = get_children( array(
	'post_type' => 'attachment',
	'post_mime_type' => 'image',
	'post_parent' => $post->ID
) );
?>

<div class="single-product-container tcp-tcpf">
<div class="tcp-row">

	<div class="single-product-imagen tcp-col-md-7">
		<div class="tcp-single-imagen">
			<?php if ( has_post_thumbnail() ) {
				// $image_title = $attachment->post_title;
				$imageFull = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
				if ( function_exists( 'magictoolbox_WordPress_MagicZoomPlus_init' ) ) { ?>
					<a class="MagicZoomPlus" id="MagicZoom-single-product" href="<?php echo $imageFull[0]; ?>">
						<?php the_post_thumbnail( 'large' ); ?>
					</a>
				<?php } else {
					if ( count( $attachments ) != 1 ) {
						the_post_thumbnail( 'large' );
					} else {
						echo do_shortcode( '[gallery columns="1" link="file" size="large"]' );
					}
				}
			} else { ?>

				<div class="slide-post-thumbnail tcp-no-image">
					<a class="tcp_size-<?php echo $image_size;?>" href="<?php the_permalink(); ?>"><img src="<?php echo get_stylesheet_directory_uri() ?>/images/tcp-no-image.jpg" alt="No image" title="" width="" height="" /></a>
				</div><!-- .entry-post-thumbnail -->

			<?php } ?>
		</div><!-- .tcp-single-imagen -->
		
		<?php if ( count( $attachments ) > 1 ) :
			echo do_shortcode( '[gallery columns="5" link="file"]' );
		else : ?>
		    <!-- Display a single image -->
		<?php endif; ?>

	</div><!-- .single-product-imagen -->

	<div class="single-product-options tcp-col-md-5">

		<?php if ( function_exists( 'tcp_has_discounts' ) && tcp_has_discounts() ) : ?>
			<span class="single-discount">-<?php tcp_the_discount_value(); ?></span>
		<?php endif; ?>

		<?php tcp_the_buy_button(); ?>

		<?php if ( is_active_sidebar( 'sidebar-buying-area' ) ) : ?>
			<div class="widget-area" role="complementary">
				<?php dynamic_sidebar( 'sidebar-buying-area' ); ?>
			</div><!-- #secondary -->
		<?php endif; ?>

	</div><!-- .single-product-options -->

</div><!-- .tcp-row -->
<?php the_content(); ?>
</div><!-- .single-product-container -->