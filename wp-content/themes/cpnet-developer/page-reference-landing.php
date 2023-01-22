<?php namespace DevHub;
/**
 * The template for displaying the Code Reference landing page.
 *
 * Template Name: Reference
 *
 * @package wporg-developer
 */

get_header(); ?>

	<div id="primary" class="content-area">

		<div id="content-area">
			<?php \Hybrid\Breadcrumbs\Trail::display(); ?>
		</div>

		<main id="main" class="site-main" role="main">
			<div class="reference-landing">
				<div class="search-guide section clear">
					<h1>Code Reference</h1>
					<h2 class="ref-intro"><?php _e( 'Want to know what&#39;s going on inside ClassicPress? Search the Code Reference for more information about ClassicPress&#39; functions, classes, methods, and hooks.', 'wporg' ); ?></h2>
					<h3 class="search-intro"><?php _e( 'Try it out:', 'wporg' ); ?></h3>
					<?php get_search_form(); ?>
				</div><!-- /search-guide -->

				<div class="topic-guide section">
					<h4><?php _e( 'Or browse through topics:', 'wporg' ); ?></h4>
					<ul class="unordered-list horizontal-list no-bullets">
						<li><a href="<?php echo get_post_type_archive_link( 'wp-parser-function' ) ?>"><?php _e( 'Functions', 'wporg' ); ?></a></li>
						<li><a href="<?php echo get_post_type_archive_link( 'wp-parser-hook' ) ?>"><?php _e( 'Hooks', 'wporg' ); ?></a></li>
						<li><a href="<?php echo get_post_type_archive_link( 'wp-parser-class' ) ?>"><?php _e( 'Classes', 'wporg' ); ?></a></li>
						<li><a href="<?php echo get_post_type_archive_link( 'wp-parser-method' ) ?>"><?php _e( 'Methods', 'wporg' ); ?></a></li>
					</ul>
				</div><!-- /topic-guide -->

			</div><!-- /reference-landing -->

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>
