<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package classicpress-developer
 */

$GLOBALS['pagetitle'] = wp_get_document_title();


?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="referrer" content="always">
<?php wp_head(); ?>
</head>


<body id="classicpress-org" <?php body_class(); ?>>

<header id="masthead" class="site-header
<?php
if ( is_front_page() ) {
	echo ' home'; }
?>
" role="banner">
	
	<div class="site-branding">
		<a href="<?php echo esc_url( get_home_url() ); ?>" rel="home"><img src="https://docs.classicpress.net/wp-content/themes/cpnet-developer/images/logo-white.svg" width="250" alt="ClassicPress logo"></a>

		<?php if ( is_front_page() ) : ?>
		<h1 class="site-description"><?php esc_html_e( 'Stable. Secure. Instantly Familiar.', 'classicpress' ); ?></h1>
		<?php endif; ?>

		
	</div>
</header><!-- #masthead -->

<div id="page" class="hfeed site devhub-wrap">
	<a href="#main" class="screen-reader-text"><?php _e( 'Skip to content', 'classicpress' ); ?></a>

	<?php do_action( 'before' ); ?>
	<?php
	if ( DevHub\should_show_search_bar() ) :
		?>
		<div id="inner-search">
			<?php get_search_form(); ?>
			<div id="inner-search-icon-container">
				<div id="inner-search-icon">
					<button type="button" class="dashicons dashicons-search top-src-btn"><span class="screen-reader-text"><?php _e( 'Search', 'classicpress' ); ?></span></button>
				</div>
			</div>
		</div>

	<?php endif; ?>
	<div id="content" class="site-content">
