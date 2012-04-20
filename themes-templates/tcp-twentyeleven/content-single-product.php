<?php
/**
 * The template for displaying content in the single.php template
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php if ( has_term( 'beta', 'category-price', '' )) : ?>
            <img src="/wp-content/themes/ecommerce-twentyeleven/images/beta3.jpg" alt="Beta Version" width="66" height="33" class="tcp-beta-single"/>
		<?php endif; ?>
                
		<h1 class="entry-title"><?php the_title(); ?></h1>

			<!--<div class="entry-meta">
				<?php tcp_posted_on(); ?> <?php tcp_posted_by(); ?>
			</div>--><!-- .entry-meta -->
			<?php if ( comments_open() && ! post_password_required() ) : ?>
			<div class="comments-link">
				<?php comments_popup_link( '<span class="leave-reply">' . __( 'Reply', 'twentyeleven' ) . '</span>', _x( '1', 'comments number', 'twentyeleven' ), _x( '%', 'comments number', 'twentyeleven' ) ); ?>
			</div>
			<?php endif; ?>
	</header><!-- .entry-header -->
	

	<div class="entry-content">
        
		<?php the_content(); ?>

	</div><!-- .entry-content -->

	<footer class="entry-meta">
		<span class="product-meta-posted cf">
        
		  <?php
          $taxonomies = get_object_taxonomies( get_post_type(), 'objects' );
          foreach( $taxonomies as $id => $taxonomy ) :
              $terms_list = get_the_term_list( 0, $id, '', ', ' );
              if ( strlen( $terms_list ) > 0 ) : ?>
              <span class="tcp_taxonomy tcp_taxonomy_<?php echo $taxonomy->name;?>"><?php echo $taxonomy->labels->singular_name; ?>:
              <?php echo $terms_list;?>
              </span>
              <?php endif; 
          endforeach;?>
        
        </span>
		<?php edit_post_link( __( 'Edit', 'twentyeleven' ), '<span class="edit-link">', '</span>' ); ?>

	</footer><!-- .entry-meta -->
</article><!-- #post-<?php the_ID(); ?> -->
