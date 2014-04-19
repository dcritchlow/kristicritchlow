<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that other
 * 'pages' on your WordPress site will use a different template.
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */

get_header(); ?>

    <div id="primary" class="content-area">
        <div id="content" class="site-content" role="main">
            <?php
                $catID = 0;
                if (is_page('Washington DC')) {
                    $catID=2;
                } elseif (is_page('Running')) {
                    $catID=3;
                } elseif (is_page('Personal')) {
                    $catID=5;
                }

                if ($catID) {
                    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                    query_posts("cat=$catID&amp;paged=$paged");
                }
            ?>

            <?php if ( have_posts() ) : ?>

                <?php /* The loop */ ?>
                <?php while ( have_posts() ) : the_post(); ?>
                    <?php get_template_part( 'content', get_post_format() ); ?>
                <?php endwhile; ?>

                <?php twentythirteen_paging_nav(); ?>

            <?php else : ?>
                <?php get_template_part( 'content', 'none' ); ?>
            <?php endif; ?>

        </div><!-- #content -->
    </div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>