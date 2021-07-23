<?php if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Plugin Name: ClassicPress Docs Table of Contents
 * Description: Provides a table of contents for the ClassicPress Docs site.
 * Forked from Handbook by Automattic, modifed by Nacin.
 * Version:     0.1
 * Author:      ClassicPress
 * Author URI:  https://www.classicpress.net/
 * License:     GPLv2 or later
 * Text Domain: classicpress
 */

/**
 * Converts given content to dynamically add the ToC.
 *
 * @param string $content Content.
 * @return string Modified content.
 */
function cp_docs_add_toc( $content ) {

	if ( ! in_the_loop() ) {
		return $content;
	}

	// Display only on code pages
	if ( ! is_singular( ['single-wp-parser-function', 'single-wp-parser-hook', 'single-wp-parser-class', 'single-wp-parser-method'] ) ) {
		return $content;
	}

	$items = cp_toc_get_tags( 'h([1-4])', $content );

	if ( count( $items ) < 2 ) {
		return $content;
	}

	for ( $i = 1; $i <= 4; $i++ ) {
		$content = cp_toc_add_ids_and_jumpto_links( "h$i", $content );
	}

	$toc = '';

	if ( $items ) {
		$contents_header = 'h' . $items[0][2]; // Duplicate the first <h#> tag in the document.
		$toc .= '<style> .toc-jump { text-align: right; font-size: 12px; } .page .toc-heading { margin-top: -50px; padding-top: 50px !important; }</style>';
		$toc .= '<div class="table-of-contents" role="navigation" aria-labelledby="nav2">';
		$toc .= '<' . $contents_header . ' id="nav2">' . __( 'Contents', 'classicpress' ) . '</' . $contents_header . '><ul class="items">';
		$last_item = false;
		$used_ids = cp_toc_get_reserved_ids();

		foreach ( $items as $item ) {
			if ( $last_item ) {
				if ( $last_item < $item[2] )
					$toc .= "\n<ul>\n";
				elseif ( $last_item > $item[2] )
					$toc .= "\n</ul></li>\n";
				else
					$toc .= "</li>\n";
			}

			$last_item = $item[2];

			$id = sanitize_title( $item[3] );
			// Append unique suffix if anchor ID isn't unique.
			$count = 2;
			$orig_id = $id;
			while ( in_array( $id, $used_ids ) && $count < 50 ) {
				$id = $orig_id . '-' . $count;
				$count++;
			}
			$used_ids[] = $id;

			$toc .= '<li><a href="#' . esc_attr( $id  ) . '">' . $item[3]  . '</a>';
		}
		$toc .= "</ul>\n</div>\n";
	}

	return $toc . $content;
}
add_filter( 'the_content', 'cp_docs_add_toc' );

/**
 * If a used ID is encountered when a ToC section heading ID is being
 * generated, the generated ID is incremented to avoid a conflict.
 * 
 * @return string Modified content.
 */
function cp_toc_add_ids_and_jumpto_links( $tag, $content ) {
	$items = cp_toc_get_tags( $tag, $content );
	$first = true;
	$matches = [];
	$replacements = [];
	$used_ids = cp_toc_get_reserved_ids();

	foreach ( $items as $item ) {
		$replacement = '';
		$matches[] = $item[0];
		$id = sanitize_title( $item[2] );

		// Append unique suffix if anchor ID isn't unique.
		$count = 2;
		$orig_id = $id;
		while ( in_array( $id, $used_ids ) && $count < 50 ) {
			$id = $orig_id . '-' . $count;
			$count++;
		}
		$used_ids[] = $id;
		
		if ( ! $first ) {
			$replacement .= '<p class="toc-jump"><a href="#top">' . __( 'Top &uarr;', 'classicpress' ) . '</a></p>';
		} else {
			$first = false;
		}
		$a11y_text      = sprintf( '<span class="screen-reader-text">%s</span>', $item[2] );
		$anchor         = sprintf( '<a href="#%1$s" class="anchor"><span aria-hidden="true">#</span>%2$s</a>', $id, $a11y_text );
		$replacement   .= sprintf( '<%1$s class="toc-heading" id="%2$s" tabindex="-1">%3$s %4$s</%1$s>', $tag, $id, $item[2], $anchor );
		$replacements[] = $replacement;
	}

	if ( $replacements ) {
		if ( count( array_unique( $matches ) ) !== count( $matches ) ) {
			foreach ( $matches as $i => $match ) {
				$content = preg_replace( '/' . preg_quote( $match, '/' ) . '/', $replacements[ $i ], $content, 1 );
			}
		} else {
			$content = str_replace( $matches, $replacements, $content );
		}
	}

	return $content;
}

/**
 * Returns reserved markup IDs likely to conflict with ToC-generated heading IDs.
 *
 * This list isn't meant to be exhaustive, just IDs that are likely to conflict
 * with ToC-generated section heading IDs.
 *
 * If a reserved ID is encountered when a ToC section heading ID is being
 * generated, the generated ID is incremented to avoid a conflict.
 *
 * @return array
 */
function cp_toc_get_reserved_ids() {
	/**
	 * Filters the array of reserved IDs considered when auto-generating IDs for
	 * ToC sections.
	 *
	 * This is mostly for specifying markup IDs that may appear on the same page
	 * as the ToC for which any ToC-generated IDs would conflict. In such
	 * cases, the first instance of the ID on the page would be the target of
	 * the ToC section permalink which is likely not the ToC section itself.
	 *
	 * By specifying these reserved IDs, any potential use of the IDs by the theme
	 * can be accounted for by incrementing the auto-generated ID to avoid conflict.
	 *
	 * E.g. if the theme has `<div id="main">`, a ToC with a section titled "Main"
	 * would have a permalink that links to the div and not the ToC section.
	 *
	 * @param array $ids Array of IDs.
	 */
	return (array) apply_filters(
		'handbooks_reserved_ids',
		['main', 'masthead', 'menu-header', 'page', 'primary', 'secondary', 'secondary-content', 'site-navigation',	'wordpress-org', 'wp-toolbar', 'wpadminbar', 'wporg-footer', 'wporg-header']
	);
}

/**
 * Find all heading tags in content.
 *
 * @return array
 */
function cp_toc_get_tags( $tag, $content = '' ) {
	if ( empty( $content ) ) {
		$content = get_the_content();
	}
	preg_match_all( "/(<{$tag}>)(.*)(<\/{$tag}>)/", $content, $matches, PREG_SET_ORDER );
	return $matches;
}
