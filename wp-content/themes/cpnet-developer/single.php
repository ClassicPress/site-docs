<?php namespace DevHub;
/**
 * The Template for displaying all single posts.
 *
 * @package wporg-developer
 */

get_header(); ?>

	<div id="content-area" <?php body_class( 'code-reference' ); ?>>

		<?php \Hybrid\Breadcrumbs\Trail::display(); ?>

		<main id="main" class="site-main" role="main">

		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'content', ( is_parsed_post_type() ? 'reference' : get_post_type() ) ); ?>

		<?php endwhile; // end of the loop. ?>

		</main><?php get_sidebar(); ?><!-- #main -->
	</div><!-- #primary -->
<?php get_footer(); ?>
