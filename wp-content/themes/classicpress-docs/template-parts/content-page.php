<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Susty
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header>
		<?php the_title( '<h1>', '</h1>' ); ?>
	</header><!-- .entry-header -->

	<?php susty_wp_post_thumbnail(); ?>

	<div>
		<?php
		the_content();

		wp_link_pages( array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'susty' ),
			'after'  => '</div>',
		) );
		?>
	</div>

	<?php
	$cpnet_docs_editor = $GLOBALS['cpnet_docs_editor'];
	if ( $cpnet_docs_editor->is_markdown_post( get_post() ) ) {
		echo "<footer>\n";
		printf(
			'<span class="edit-link"><a class="post-edit-link github" href="%s">%s</a></span>',
			esc_attr( $cpnet_docs_editor->get_markdown_edit_link( get_the_ID() ) ),
			esc_html__( 'Edit this page on GitHub', 'classicpress-docs' )
		);
		echo "\n</footer>\n";
	} else if ( get_edit_post_link() ) {
		echo "<footer>\n";
		edit_post_link(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Edit <span class="screen-reader-text">%s</span>', 'susty' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			),
			'<span class="edit-link">',
			'</span>'
		);
		echo "</footer>\n";
	} ?>
</article><!-- #post-<?php the_ID(); ?> -->
