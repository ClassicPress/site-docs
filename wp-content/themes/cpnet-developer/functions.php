<?php

namespace DevHub;

/**
 * Registrations (post type, taxonomies, etc).
 */
require __DIR__ . '/inc/registrations.php';

/**
 * HTML head tags and customizations.
 */
require __DIR__ . '/inc/head.php';

/**
 * Custom template tags for this theme.
 */
require __DIR__ . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require __DIR__ . '/inc/extras.php';

/**
 * Class for editing parsed content on the Function, Class, Hook, and Method screens.
 */
require_once( __DIR__ . '/inc/parsed-content.php' );

if ( ! function_exists( 'loop_pagination' ) ) {
	require __DIR__ . '/inc/loop-pagination.php';
}

if ( ! function_exists( 'breadcrumb_trail' ) ) {
	require __DIR__ . '/inc/breadcrumb-trail.php';
}

/**
 * User-submitted content (comments, notes, etc).
 */
require __DIR__ . '/inc/user-content.php';

/**
 * User-submitted content preview.
 */
require __DIR__ . '/inc/user-content-preview.php';

/**
 * Voting for user-submitted content.
 */
require __DIR__ . '/inc/user-content-voting.php';

/**
 * Editing for user-submitted content.
 */
require __DIR__ . '/inc/user-content-edit.php';

/**
 * Explanations for functions. hooks, classes, and methods.
 */
require( __DIR__ . '/inc/explanations.php' );

/**
 * Redirects.
 */
require __DIR__ . '/inc/redirects.php';

/**
 * Content formatting.
 */
require __DIR__ . '/inc/formatting.php';

/**
 * Autocomplete.
 */
require __DIR__ . '/inc/autocomplete.php';

/**
 * Search query.
 */
require __DIR__ . '/inc/search.php';

/**
 * Parser customizations.
 */
require __DIR__ . '/inc/parser.php';

/**
 * Admin area customizations.
 */
if ( is_admin() ) {
	require __DIR__ . '/inc/admin.php';
}

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 640; /* pixels */
}


add_action( 'init', __NAMESPACE__ . '\\init' );
add_action( 'widgets_init', __NAMESPACE__ . '\\widgets_init' );

function init() {

	register_nav_menus();

	add_action( 'pre_get_posts', __NAMESPACE__ . '\\pre_get_posts' );
	add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\theme_scripts_styles' );
	add_action( 'wp_head', __NAMESPACE__ . '\\header_js' );
	add_action( 'add_meta_boxes', __NAMESPACE__ . '\\rename_comments_meta_box', 10, 2 );

	add_filter( 'post_type_link', __NAMESPACE__ . '\\method_permalink', 11, 2 );
	add_filter( 'term_link', __NAMESPACE__ . '\\taxonomy_permalink', 10, 3 );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );

	// Modify default breadcrumbs.
	add_filter( 'breadcrumb_trail_items',  __NAMESPACE__ . '\\breadcrumb_trail_items_for_hooks', 10, 2 );
	add_filter( 'breadcrumb_trail_items',  __NAMESPACE__ . '\\breadcrumb_trail_items_for_handbook_root', 10, 2 );

	add_filter( 'syntaxhighlighter_htmlresult', __NAMESPACE__ . '\\syntaxhighlighter_htmlresult' );
}

/**
 * Fix breadcrumb for hooks.
 *
 * A hook has a parent (the function containing it), which causes the Breadcrumb
 * Trail plugin to introduce trail items related to the parent that shouldn't
 * be shown.
 *
 * @param  array $items The breadcrumb trail items
 * @param  array $args  Original args
 * @return array
 */
function breadcrumb_trail_items_for_hooks( $items, $args ) {
	$post_type = 'wp-parser-hook';

	// Bail early when not the single archive for hook
	if ( ! is_singular() || $post_type !== get_post_type() || ! isset( $items[4] ) ) {
		return $items;
	}

	$post_type_object = get_post_type_object( $post_type );

	// Replaces 'Functions' archive link with 'Hooks' archive link
	$items[2] = '<a href="' . get_post_type_archive_link( $post_type ) . '">' . $post_type_object->labels->name . '</a>';
	// Replace what the plugin thinks is the parent with the hook name
	$items[3] = $items[4];
	// Unset the last element since it shifted up in trail hierarchy
	unset( $items[4] );

	return $items;
}

/**
 * Fix breadcrumb for handbook root pages.
 *
 * The handbook root/landing pages do not need a duplicated breadcrumb trail
 * item that simply links to the currently loaded page. The trailing breadcrumb
 * item is already the unlinked handbook name, which is sufficient.
 *
 * @param  array $items The breadcrumb trail items
 * @param  array $args  Original args
 * @return array
 */
function breadcrumb_trail_items_for_handbook_root( $items, $args ) {
	// Bail early if not a handbook landing page.
	if ( ! function_exists( 'wporg_is_handbook_landing_page' ) || ! wporg_is_handbook_landing_page() ) {
		return $items;
	}

	// Unset link to current handbook.
	unset( $items[1] );

	return $items;
}

/**
 * widgets_init function.
 *
 * @access public
 * @return void
 */
function widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'wporg' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="box gray widget %2$s">',
		'after_widget'  => '</div></aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1><div class="widget-content">',
	) );

	register_sidebar( array(
		'name'          => __( 'Landing Page Footer - Left', 'wporg' ),
		'id'            => 'landing-footer-1',
		'description'   => __( 'Appears in footer of the primary landing page', 'wporg' ),
		'before_widget' => '<div id="%1$s" class="widget box %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );

	register_sidebar( array(
		'name'          => __( 'Landing Page Footer - Center', 'wporg' ),
		'id'            => 'landing-footer-2',
		'description'   => __( 'Appears in footer of the primary landing page', 'wporg' ),
		'before_widget' => '<div id="%1$s" class="widget box %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );
}

/**
 * @param \WP_Query $query
 */
function pre_get_posts( $query ) {

	if ( $query->is_main_query() && $query->is_post_type_archive() ) {
		$query->set( 'orderby', 'title' );
		$query->set( 'order', 'ASC' );
	}

	if ( $query->is_main_query() && $query->is_tax() && $query->get( 'wp-parser-source-file' ) ) {
		$query->set( 'wp-parser-source-file', str_replace( array( '.php', '/' ), array( '-php', '_' ), $query->query['wp-parser-source-file'] ) );
	}

	// For search query modifications see DevHub_Search.
}

function register_nav_menus() {

	\register_nav_menus( array(
		'devhub-menu' => __( 'Developer Resources Menu', 'wporg' ),
		'devhub-cli-menu' => __( 'WP-CLI Commands Menu', 'wporg' ),
		'reference-home-api' => __( 'Reference API Menu', 'wporg' ),
	) );
}

/**
 * Filters the permalink for a wp-parser-method post.
 *
 * @param string   $link The post's permalink.
 * @param \WP_Post $post The post in question.
 * @return string
 */
function method_permalink( $link, $post ) {
	global $wp_rewrite;

	if ( ! $wp_rewrite->using_permalinks() || ( 'wp-parser-method' !== $post->post_type ) ) {
		return $link;
	}

	$parts  = explode( '-', $post->post_name );
	$method = array_pop( $parts );
	$class  = implode( '-', $parts );

	return home_url( user_trailingslashit( "reference/classes/$class/$method" ) );
}

function taxonomy_permalink( $link, $term, $taxonomy ) {
	global $wp_rewrite;

	if ( ! $wp_rewrite->using_permalinks() ) {
		return $link;
	}

	if ( $taxonomy === 'wp-parser-source-file' ) {
		$slug = $term->slug;
		if ( substr( $slug, -4 ) === '-php' ) {
			$slug = substr( $slug, 0, -4 ) . '.php';
			$slug = str_replace( '_', '/', $slug );
		}
		$link = home_url( user_trailingslashit( "reference/files/$slug" ) );
	} elseif ( $taxonomy === 'wp-parser-since' ) {
		$link = str_replace( $term->slug, str_replace( '-', '.', $term->slug ), $link );
	}
	return $link;
}

/**
 * Outputs JavaScript intended to appear in the head of the page.
 */
function header_js() {
	// Output CSS to hide markup with the class 'hide-if-js'. Ensures the markup is visible if JS is not present.
	// Add class 'js' to the body element if JavaScript is enabled
	echo "
	<script type=\"text/javascript\">
		jQuery( '<style>.hide-if-js { display: none; }</style>' ).appendTo( 'head' );
		jQuery( function($) {
			$( 'body' ).addClass('js');
		} );
	</script>\n";
}

function theme_scripts_styles() {
	wp_enqueue_style( 'dashicons' );
	wp_enqueue_style( 'open-sans', '//fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,400,300,600' );
	wp_enqueue_style( 'wporg-developer-style', get_stylesheet_uri(), array(), '3' );
	wp_enqueue_style( 'wp-dev-sass-compiled', get_template_directory_uri() . '/stylesheets/main.css', array( 'wporg-developer-style' ), '20200713' );
	wp_enqueue_script( 'wporg-developer-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );
	wp_enqueue_script( 'wporg-developer-search', get_template_directory_uri() . '/js/search.js', array(), '20150430', true );
	wp_enqueue_script( 'wporg-developer-chapters', get_template_directory_uri() . '/js/chapters.js', array( 'jquery' ), '20190603' );
}

/**
 * Rename the 'Comments' meta box to 'User Contributed Notes' for reference-editing screens.
 *
 * @param string  $post_type Post type.
 * @param WP_Post $post      WP_Post object for the current post.
 */
function rename_comments_meta_box( $post_type, $post ) {
	if ( is_parsed_post_type( $post_type ) ) {
		remove_meta_box( 'commentsdiv', $post_type, 'normal' );
		add_meta_box( 'commentsdiv', __( 'User Contributed Notes', 'wporg' ), 'post_comment_meta_box', $post_type, 'normal', 'high' );
	}
}

/**
 * If a syntax highlighted code block exceeds a given number of lines, wrap the
 * markup with other markup to trigger the code expansion/collapse JS handling
 * already implemented for the code reference.
 *
 * @param string  $text The pending result of the syntax highlighting.
 * @return string
 */
function syntaxhighlighter_htmlresult( $text ) {

	// is_admin() is true in front end AJAX requests.
	if ( is_admin() && !( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		return $text;
	}

	$new_text      = '';
	// Collapse is handled for >10 lines. But just go ahead and show the full
	// code if that is just barely being exceeded (no one wants to expand to
	// see one or two more lines).
	$lines_to_show = 12;
	$do_collapse   = ( substr_count( $text, "\n" ) - 1 ) > $lines_to_show;

	if ( $do_collapse )  {
		$new_text .= '<section class="source-content">';
		$new_text .= '<div class="source-code-container">';
	}

	$new_text .= $text;

	if ( $do_collapse ) {
		$new_text .= '</div>';
		$new_text .= '<p class="source-code-links"><span>';
		$new_text .= '<a href="#" class="show-complete-source">' . __( 'Expand full source code', 'wporg' ) . '</a>';
		$new_text .= '<a href="#" class="less-complete-source">' . __( 'Collapse full source code', 'wporg' ) . '</a>';
		$new_text .= '</span></p>';
		$new_text .= '</section>';
	}

	return $new_text;
}

/**
 * Add a Results View.
 *
 * @since 1.0.0
 * @param array $atts ShortCode attributes.
 * @param mixed $content ShortCode Enclosed Content.
 */
function cpnet_docs_view( $atts, $content = null ) {
	$atts = shortcode_atts(
		array(
			'p'                      => 0,
			'name'                   => '',
			'page_id'                => 0,
			'pagename'               => '',
			'post_parent'            => '',
			'post_type'              => '',
			'post_status'            => '',
			'has_password'           => false,
			'author'                 => '',
			'author_name'            => '',
			's'                      => '',
			'nopaging'               => false,
			'paged'                  => 0,
			'posts_per_page'         => 10,
			'ignore_sticky_posts'    => true,
			'offset'                 => 0,
			'order'                  => 'ASC',
			'orderby'                => 'modified',
			'cache_results'          => true,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => true,
		),
		$atts,
		'cpnet_docs_view'
	);

	// Attributes.
	$atts['post_type']   = explode( ',', $atts['post_type'] );
	$atts['post_status'] = explode( ',', $atts['post_status'] );

	$wp_query_results = new \WP_Query( $atts );
	$loop = '';

	if ( 0 == $wp_query_results->post_count ) {
		$loop = $wp_query_results->post_count . ' Posts Found.';
	}

	foreach ( $wp_query_results->posts as $post_object ) {
		$loop_item = str_replace( '{{ID}}', sanitize_post_field( 'ID', $post_object->ID, $post_object->ID, 'display' ), $content );
		$loop_item = str_replace( '{{post_title}}', sanitize_post_field( 'post_title', $post_object->post_title, $post_object->ID, 'display' ), $loop_item );
		$loop_item = str_replace( '{{post_content}}', sanitize_post_field( 'post_content', $post_object->post_content, $post_object->ID, 'display' ), $loop_item );
		$loop_item = str_replace( '{{post_name}}', sanitize_post_field( 'post_name', $post_object->post_name, $post_object->ID, 'display' ), $loop_item );
		$loop_item = str_replace( '{{guid}}', sanitize_post_field( 'guid', $post_object->guid, $post_object->ID, 'display' ), $loop_item );
		$loop_item = str_replace( '{{permalink}}', esc_url( get_permalink( $post_object->ID ) ), $loop_item );
		$loop .= $loop_item;
	}

	$loop = wp_kses( $loop, 'post' );

	return $loop;

}
add_shortcode( 'cpnet_docs_view', __NAMESPACE__ . '\\cpnet_docs_view' );

/**
 * Add a Search Form.
 *
 * @since 1.0.0
 * @param array $atts ShortCode attributes.
 */
function cpnet_docs_css_src( $atts ) {

	$atts = shortcode_atts(
		array(
			'search_id'     => 'search_input',
			'placeholder'   => 'Search...',
			'title'         => 'Search...',
			'results_id'    => 'serch_results',
		),
		$atts,
		'cpnet_docs_search_form'
	);

	$form   = '<input style="margin:auto;" type="text" id="' . esc_attr( (int) $atts['search_id'] ) . '" onkeyup="cpnet_docs_src_form()" placeholder="' . esc_attr( $atts['placeholder'] ) . '" title="' . esc_attr( $atts['title'] ) . '">';

	$script = '<script>
		function cpnet_docs_src_form() {
			var input, filter, ul, li, a, i, txtValue;
			input = document.getElementById("' . esc_js( (int) $atts['search_id'] ) . '");
			filter = input.value.toUpperCase();
			ul = document.getElementById("' . esc_js( $atts['results_id'] ) . '");
			li = ul.getElementsByTagName("li");
			for (i = 0; i < li.length; i++) {
				a = li[i].getElementsByTagName("a")[0];
				txtValue = a.textContent || a.innerText;
				if (txtValue.toUpperCase().indexOf(filter) > -1) {
					li[i].style.display = "";
				} else {
					li[i].style.display = "none";
				}
			}
		}
	</script>';

	$out = $form . $script;

	return $out;

}
add_shortcode( 'cpnet_docs_search_form', __NAMESPACE__ . '\\cpnet_docs_css_src' );

/**
 * Remove WPAutop
 * @see https://forums.classicpress.net/t/shortcodes-on-user-guide-page/3318/5
 */
remove_filter( 'the_content', 'wpautop', 9);
add_filter( 'the_content', 'wpautop', 11);
