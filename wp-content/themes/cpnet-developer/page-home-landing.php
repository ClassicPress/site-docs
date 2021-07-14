<?php
/**
 * The template for displaying the Code Reference landing page.
 *
 * Template Name: Home
 *
 * @package classicpress-developer
 */

get_header(); ?>

	<div id="primary" class="content-area">

		<div class="home-landing">

			<div class="inner-wrap section">

				<div class="box box-code-usage">
					<h3 class="widget-title"><div class="dashicons dashicons-admin-users"></div><?php esc_html_e( 'User Guides', 'classicpress' ); ?></h3>
					<p class="widget-description"><?php esc_html_e( 'Looking for documentation on how to use ClassicPress?', 'classicpress' ); ?></p>
					<a href="<?php echo esc_url( home_url( '/user-guides' ) ); ?>" class="go"><?php esc_html_e( 'Visit the User Guides', 'classicpress' ); ?></a>
				</div>

				<div class="box box-code-ref">
					<h3 class="widget-title"><div class="dashicons dashicons-editor-code"></div><?php esc_html_e( 'Code Reference', 'classicpress' ); ?></h3>
					<p class="widget-description"><?php esc_html_e( 'Looking for documentation for the codebase?', 'classicpress' ); ?></p>
					<a href="<?php echo esc_url( home_url( '/code-reference' ) ); ?>" class="go"><?php esc_html_e( 'Visit the reference', 'classicpress' ); ?></a>
				</div>

				<div class="box box-plugins">
					<h3 class="widget-title"><div class="dashicons dashicons-admin-plugins"></div><?php esc_html_e( 'Plugin Guidelines', 'classicpress' ); ?></h3>
					<p class="widget-description"><?php esc_html_e( 'Ready to dive deep into the world of plugin authoring?', 'classicpress' ); ?></p>
					<a href="<?php echo esc_url( home_url( '/plugin-guidelines' ) ); ?>" class="go"><?php esc_html_e( 'Visit the Plugin Guidelines ', 'classicpress' ); ?></a>
				</div>

			</div>

			<div class="search-guide inner-wrap section">

				<div class=" box"></div>
				

				<div class="box">
					<h3 class="widget-title"><?php esc_html_e( 'Contribute', 'classicpress' ); ?></h3>
					<ul class="unordered-list no-bullets">
						<li><a href="https://www.classicpress.net/community/" class="make-wp-link"><?php esc_html_e( 'Help Make ClassicPress', 'classicpress' ); ?></a></li>
					</ul>
				</div>

				
				<div class=" box"></div>
				

			</div>

		</div><!-- /home-landing -->
	</div><!-- #primary -->

<?php get_footer(); ?>

