<?php
/**
 * The loop that displays products in configurable GRID mode.
 * Boot Store theme is based on Twentytwelve theme. The official WordPress theme.
 *
 * @package TheCartPress
 * @subpackage Theme Compatibility
 * @since TheCartPress 1.3
 */
?>
<?php /* Display navigation to next/previous pages when applicable */ ?>

<?php /* If there are no products to display, such as an empty archive page */ ?>
<?php if ( ! have_posts() ) : ?>
	<article id="post-0" class="post no-results not-found">
		<header class="entry-header">
			<h1 class="entry-title"><?php _e( 'Nothing Found', 'tcp' ); ?></h1>
		</header><!-- .entry-header -->
		<div class="entry-content">
			<p>
				<?php _e( 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'tcp' ); ?>
			</p>
			<?php get_search_form(); ?>
		</div><!-- .entry-content -->
	</article><!-- #post-0 -->
<?php endif; ?>

<?php
$currency = tcp_the_currency( false ); 
if ( ! isset( $instance ) ) $instance = get_option( 'ttc_settings' );
$suffix = '-' . get_post_type( get_the_ID() );
if ( ! isset( $instance['title_tag' . $suffix] ) ) $suffix = '';

$see_title				= isset( $instance['see_title' . $suffix] ) ? $instance['see_title' . $suffix] : true;
$title_tag				= isset( $instance['title_tag' . $suffix] ) ? $instance['title_tag' . $suffix] : 'h2';
$see_image				= isset( $instance['see_image' . $suffix] ) ? $instance['see_image' . $suffix] : true;
$image_size				= isset( $instance['image_size' . $suffix] ) ? $instance['image_size' . $suffix] : 'thumbnail';
$see_discount			= isset( $instance['see_discount' . $suffix ] ) ? $instance['see_discount' . $suffix ] : true;
$see_stock				= isset( $instance['see_stock' . $suffix ] ) ? $instance['see_stock' . $suffix ] : false;
$see_excerpt			= isset( $instance['see_excerpt' . $suffix] ) ? $instance['see_excerpt' . $suffix] : true;
$excerpt_length			= isset( $instance['excerpt_length' . $suffix] ) ? $instance['excerpt_length' . $suffix] : false;
$see_content			= isset( $instance['see_content' . $suffix] ) ? $instance['see_content' . $suffix] : false;
$see_price				= isset( $instance['see_price' . $suffix] ) ? $instance['see_price' . $suffix] : false;
$see_buy_button			= isset( $instance['see_buy_button' . $suffix] ) ? $instance['see_buy_button' . $suffix] : true;
$see_author				= isset( $instance['see_author' . $suffix] ) ? $instance['see_author' . $suffix] : false;
$see_posted_on			= isset( $instance['see_posted_on' . $suffix] ) ? $instance['see_posted_on' . $suffix] : false;
$see_taxonomies			= isset( $instance['see_taxonomies' . $suffix] ) ? $instance['see_taxonomies' . $suffix] : false;
$see_meta_utilities		= isset( $instance['see_meta_utilities' . $suffix] ) ? $instance['see_meta_utilities' . $suffix] : false;
$see_sorting_panel		= isset( $instance['see_sorting_panel' . $suffix] ) ? $instance['see_sorting_panel' . $suffix] : false;
$see_az					= isset( $instance['see_az' . $suffix] ) ? $instance['see_az' . $suffix] : false;
$number_columns			= isset( $instance['columns' . $suffix] ) ? (int)$instance['columns' . $suffix] : 3; //medium devices (desktop) md
$number_columns_xs		= isset( $instance['columns_xs' . $suffix] ) ? (int)$instance['columns_xs' . $suffix] : $number_columns; //extra small devices (phones)
$number_columns_sm		= isset( $instance['columns_sm' . $suffix] ) ? (int)$instance['columns_sm' . $suffix] : $number_columns; //small devices (tablets)
$number_columns_lg		= isset( $instance['columns_lg' . $suffix] ) ? (int)$instance['columns_lg' . $suffix] : $number_columns; //large devices (large desktops)
//custom areas. Usefull to insert other template tag from WordPress or another plugins 
$see_first_custom_area	= isset( $instance['see_first_custom_area' . $suffix] ) ? $instance['see_first_custom_area' . $suffix] : false;
$see_second_custom_area	= isset( $instance['see_second_custom_area' . $suffix] ) ? $instance['see_second_custom_area' . $suffix] : false;
$see_third_custom_area	= isset( $instance['see_third_custom_area' . $suffix] ) ? $instance['see_third_custom_area' . $suffix] : false;
$see_pagination			= isset( $instance['see_pagination' . $suffix] ) ? $instance['see_pagination' . $suffix] : false;
$see_jetpack_sharing	= isset( $instance['see_jetpack_sharing' . $suffix ] ) ? $instance['see_jetpack_sharing' . $suffix ] : false;

if ( isset( $instance['title_tag'] ) && $instance['title_tag'] != '' ) {
	$title_tag = '<' . $instance['title_tag'] . ' class="entry-title">';
	$title_end_tag = '</' . $instance['title_tag'] . '>';
} else {
	$title_tag = '';
	$title_end_tag = '';
}
?>

<div class="tcp-product-list tcpf">

	<?php if ( $see_sorting_panel ) tcp_the_sort_panel(); ?>
	<?php if ( function_exists( 'tcp_the_az_panel' ) && $see_az ) {
		$see_az_name = isset( $args['widget_id']) ? 'tcp_az_' . $args['widget_id'] : 'tcp_az';
		tcp_the_az_panel( $see_az_name );
	} ?>

	<div class="tcp-product-ls">
	<?php while ( have_posts() ) : the_post(); ?>

		<div class="tcp-product-<?php the_ID(); ?> tcp_col media">


				<?php if ( $see_image ) : ?>
						<?php if (has_post_thumbnail()) :  ?>
									<div class="entry-post-thumbnail pull-left">
										<a class="media-objet tcp_size-<?php echo $image_size;?> media-object" href="<?php the_permalink(); ?>"><?php if ( function_exists( 'the_post_thumbnail' ) ) the_post_thumbnail($image_size); ?></a>
									</div><!-- .entry-post-thumbnail -->
						<?php else : ?>
								<div class="entry-post-thumbnail tcp-no-image pull-left">
									<a class="media-objet tcp_size-<?php echo $image_size;?>" href="<?php the_permalink(); ?>"><img src="<?php echo get_template_directory_uri() ?>/images/tcp-no-image.jpg" alt="No image" title="" width="" height="" /></a>
								</div><!-- .entry-post-thumbnail -->
						<?php endif; ?>	
				<?php endif; ?>


			<div class="media-body">

				<?php if ( $see_title ) : ?>
					<div class="tcp-product-title">
					<?php echo $title_tag;?><a href="<?php the_permalink( );?>"><?php the_title(); ?></a>
					<?php echo $title_end_tag;?>
					</div><!-- .tcp-product-title -->
				<?php endif; ?>

				<?php if ( tcp_is_saleable() ) : ?>
					<div class="tcp-product-price">
						<?php if ( $see_price ) :?>
							<?php tcp_the_price_label();?>
						<?php endif;?>

						<?php if ( $see_discount && function_exists( 'tcp_has_discounts' ) ) :
							if ( tcp_has_discounts() ) {
								$discount = tcp_get_the_discount_value();
								ob_start(); ?>
								<div class="tcp-product-discount"><span class="label label-success">-<?php echo $discount; ?></span></div>
								<?php $out = ob_get_clean();
								echo apply_filters( 'tcp_loop_tcp_grid_discount', $out, $discount, get_the_ID() );
							}
						endif; ?>

					</div><!-- .tcp-product-price -->
						<?php if ( $see_stock && function_exists( 'tcp_get_the_stock' ) ) :
							$stock = tcp_get_the_stock( get_the_ID() );
							ob_start();
							if ( $stock == 0 ) { ?>
							<div class="tcp-product-outstock"><span class="label label-danger"><?php _e( 'Out of stock', 'tcp' ); ?></span></div>
							<?php }
							$out = ob_get_clean();
							echo apply_filters( 'tcp_loop_tcp_grid_stock', $out, $stock, get_the_ID() );
						endif; ?>
				<?php endif; ?>

					<?php 
						remove_filter( 'the_excerpt', 'sharing_display',19 );
					?>

				<?php if ( $see_excerpt ) : ?>
					<div class="tcp-product-summary">
						<?php //the_excerpt();
						tcp_the_excerpt( get_the_ID(), $excerpt_length ); ?>
					</div><!-- .tcp-product-summary -->
				<?php endif; ?>

				<?php if ( tcp_is_saleable() && $see_buy_button ) : ?>
					<div class="tcp-product-buybutton">
						<?php tcp_the_buy_button(); ?>
					</div>
				<?php endif;?>

				<?php if ( $see_third_custom_area && is_active_sidebar( 'sidebar-loop-details' ) ) : ?>
					<div class="tcp-product-customvalues clearfix">
						<?php dynamic_sidebar( 'sidebar-loop-details' ); ?>
					</div>
				<?php endif;?>

				<?php if ( $see_content ) : ?>
					<div class="tcp-product-content">
						<?php the_content( __( 'More <span class="meta-nav">&rarr;</span>', 'tcp' ) ); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'tcp' ), 'after' => '</div>' ) ); ?>
					</div><!-- .tcp-product-content -->
				<?php endif; ?>

				<?php if ( $see_jetpack_sharing ) : ?>
					<div class="tcp-product-jetpackshare clearfix">
						<?php if ( function_exists( 'sharing_display' ) ) {
						    sharing_display( '', true );
						}
 						?>
					</div>
				<?php endif;?>
				<div class="tcp-product-meta">

				<?php if ( $see_author ) :?>
					<div class="tcp-product-author-info media clearfix">
						<div class="tcp-product-author-avatar pull-left">
							<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'tcp_author_bio_avatar_size', 60 ) ); ?>
						</div><!-- #author-avatar -->

						<div class="tcp-product-author-description media-body">
							<p class="tcp-product-author-user"><a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php printf( esc_attr__( 'By %s', 'tcp' ), get_the_author_meta('display_name') ); ?></a></p>
							<!--<?php if ( get_the_author_meta( 'description') ) : // If a user has filled out their description, show a bio on their products  ?>
								<?php the_author_meta( 'description'); ?>
							<?php endif; ?> -->
						</div><!-- .tcp-product-author-description -->
					</div><!-- .tcp-product-author-info -->
				<?php endif; ?>

				<?php if ( $see_meta_utilities ) : ?>
					<div class="tcp-product-utilities">

					<?php if ( comments_open() ) : ?>
						<?php if ( isset( $show_sep) && $show_sep ) : ?>
						<span class="sep"> | </span>
						<?php endif; // End if $show_sep ?>
						<span class="comments-link"><?php comments_popup_link( '<span class="leave-reply">' . __( 'Leave a review', 'tcp' ) . '</span>', __( '<b>1</b> Review', 'tcp' ), __( '<b>%</b> Reviews', 'tcp' ) ); ?></span>
						<?php endif; // End if comments_open() ?>

						<?php edit_post_link( __( 'Edit', 'tcp' ), '<span class="edit-link">', '</span>' ); ?>
					</div><!-- .entry-utility -->
				<?php endif; ?>

				<?php if ( $see_posted_on ) : ?>
					<div class="tcp-product-meta clearfix">
						<?php tcp_posted_on(); ?>
					</div><!-- .tcp-product-meta -->
				<?php endif; ?>

				<?php if ( $see_taxonomies ) : ?>
					<div class="tcp-product-taxonomies clearfix">
						<span class="tcp_taxonomies">
						<?php $taxonomies = get_object_taxonomies( get_post_type(), 'objects' );
						foreach( $taxonomies as $id => $taxonomy ) :
							$terms_list = get_the_term_list( 0, $id, '', ', ' );
							if ( strlen( $terms_list ) > 0 ) : ?>
							<span class="tcp-product-taxonomy tcp-product-taxonomy-<?php echo $taxonomy->name;?>"><?php echo $taxonomy->labels->singular_name; ?>:
							<?php echo $terms_list;?>
							</span>
							<?php endif; 
						endforeach;?>
						</span>
					</div><!-- tcp-product-taxonomies -->
				<?php endif;?>



				<?php if ( $see_first_custom_area ) : ?>
				<?php endif;?>

				</div><!-- .tcp-product-meta -->
				<?php do_action( 'tcp_after_loop_tcp_grid_item', get_the_ID() ); ?>
			</div><!-- media-body -->
		</div><!-- .tcp-product -->
<?php endwhile; // End the loop ?>

	</div><!-- .tcpf .entry-content -->
</div><!-- .tcp-product-list .tcp-product-grid -->

<?php /* Display pagination */
if ( $see_pagination ) tcp_get_the_pagination(); ?>