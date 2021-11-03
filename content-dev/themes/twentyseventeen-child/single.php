<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

get_header(); ?>

<div class="wrap">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
<?php bcn_display(); ?>

			<?php
			/* Start the Loop */
			while ( have_posts() ) :
				the_post();

				get_template_part( 'template-parts/post/content', get_post_type() );

				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;

                $terms = get_the_terms( get_the_ID(), 'snippet_categories' );
                usort($terms, function($a, $b) {return $a->parent > $b->parent;});
                $childCatID = end($terms)->term_id;

				// the_post_navigation(
				// 	array(
				// 		'prev_text' => '<span class="screen-reader-text">' . __( 'Previous Post', 'twentyseventeen' ) . '</span><span aria-hidden="true" class="nav-subtitle">' . __( 'Previous', 'twentyseventeen' ) . '</span> <span class="nav-title"><span class="nav-title-icon-wrapper">' . twentyseventeen_get_svg( array( 'icon' => 'arrow-left' ) ) . '</span>%title</span>',
                //         'next_text' => '<span class="screen-reader-text">' . __( 'Next Post', 'twentyseventeen' ) . '</span><span aria-hidden="true" class="nav-subtitle">' . __( 'Next', 'twentyseventeen' ) . '</span> <span class="nav-title">%title<span class="nav-title-icon-wrapper">' . twentyseventeen_get_svg( array( 'icon' => 'arrow-right' ) ) . '</span></span>',
                //         'in_same_term' => true,
                //         'taxonomy' => 'snippet_categories',
				// 	)
				// );

			endwhile; // End of the loop.
                $siblings = get_post_siblings($childCatID);
?>
                <nav class="navigation post-navigation" role="navigation">
        <h2 class="screen-reader-text">Post navigation</h2>
        <div class="nav-links">
<?php if(array_key_exists('prev', $siblings)) {
$prev = $siblings['prev']; ?>
<div class="nav-previous">
<a href="<?= get_permalink($prev->ID) ?> " rel="prev"><span class="screen-reader-text">Previous Post</span><span aria-hidden="true" class="nav-subtitle">Previous</span> <span class="nav-title"><span class="nav-title-icon-wrapper"><svg class="icon icon-arrow-left" aria-hidden="true" role="img"> <use href="#icon-arrow-left" xlink:href="#icon-arrow-left"></use> </svg></span><?= $prev->post_title ?></span></a>
</div>
<?php }
if(array_key_exists('next', $siblings)) {
	$next = $siblings['next'];
?>
<div class="nav-next">
<a href="<?= get_permalink($next->ID) ?>" rel="next"><span class="screen-reader-text">Next Post</span><span aria-hidden="true" class="nav-subtitle">Next</span> <span class="nav-title"><?= $next->post_title ?><span class="nav-title-icon-wrapper"><svg class="icon icon-arrow-right" aria-hidden="true" role="img"> <use href="#icon-arrow-right" xlink:href="#icon-arrow-right"></use> </svg></span></span></a>
</div>
<?php } ?>
</div><!-- nav-links -->
    </nav><!-- nav.post-navigation -->


		</main><!-- #main -->
	</div><!-- #primary -->
	<?php get_sidebar(); ?>
</div><!-- .wrap -->

<?php
get_footer();
