<?php

function cpnet_maybe_link_to_headings() {
	if ( ! is_singular() ) {
		return;
	}
	switch ( get_post_type() ) {
		case 'page':
		case 'wp-user-guides':
		case 'wp-developer-guides':
			// OK to proceed with this post type
			add_filter( 'the_content', 'cpnet_link_to_headings' );
			break;
		default:
			// Other post types should not be modified
			return;
	}
}
add_action( 'wp', 'cpnet_maybe_link_to_headings' );

add_shortcode('cpdocs-toc', 'cpnet_toc');

function cpnet_toc() {
	$post_ID = get_the_id();
	if ( $post_ID === false ) {
		return '';
	}
	if( ! in_array( get_post_type( $post_ID ), [ 'wp-user-guides', 'wp-developer-guides' ] ) ) {
		return '';	
	}
	$content = get_the_content ( null, false, $post_ID );
	return cpnet_generate_toc( $content );
}

function cpnet_generate_toc( $content ) {

	$sections = [];
	$content = preg_replace_callback(
		'#<(h[1-3])([^>]*)>([^<>]+)(</h*[1-3][^>]*>)#',
		function( $matches ) use ( &$sections ) {
			$tag = $matches[1];
			$open_extra = $matches[2];
			$content = $matches[3];
			if ( ! stristr( $matches[0], 'id=' ) ) {
				// The heading doesn't have an ID yet; add one.
				$slug = trim( preg_replace(
					'#[^a-z0-9]+#',
					'-',
					strtolower( $content )
				), '-' );
			} else {
				// The heading already has an ID; re-use it.
				$parts = explode( 'id=', $open_extra );
				$parts = explode( substr( $parts[1], 0, 1 ), $parts[1] );
				$slug = $parts[1];
			}
			$sections[] = [
				'level'   => intval( substr( $tag, 1 ) ),
				'slug'    => $slug,
				'content' => wp_strip_all_tags( $content ),
			];
			return '';
		},
		$content
	);

	if ( count( $sections ) < 3 ) {
		return '';
	}

	$toc = '';
	$toc .= '<div class="cpnet-toc">';
	$toc .= 'Contents:';
	$level = 1;
	foreach ( $sections as $section ) {
		while ( $level < $section['level'] ) {
			$toc .= '<ul>';
			$level++;
		}
		while ( $level > $section['level'] ) {
			$toc .= '</ul>';
			$level--;
		}
		$toc .= '<li><a href="#' . $section['slug'] . '">'
			. $section['content']
			. '</a></li>';
	}
	while ( $level > 1 ) {
		$toc .= '</ul>';
		$level--;
	}
	$toc .= '</div>';

	return $toc;

}

function cpnet_link_to_headings( $content ) {
	$sections = [];
	$content = preg_replace_callback(
		'#<(h[1-3])([^>]*)>([^<>]+)(</h*[1-3][^>]*>)#',
		function( $matches ) use ( &$sections ) {
			$tag = $matches[1];
			$open_extra = $matches[2];
			$content = $matches[3];
			$close = $matches[4];
			if ( ! stristr( $matches[0], 'id=' ) ) {
				// The heading doesn't have an ID yet; add one.
				$slug = trim( preg_replace(
					'#[^a-z0-9]+#',
					'-',
					strtolower( $content )
				), '-' );
				$add_id = ' id="' . $slug . '"';
			} else {
				// The heading already has an ID; re-use it.
				$parts = explode( 'id=', $open_extra );
				$parts = explode( substr( $parts[1], 0, 1 ), $parts[1] );
				$slug = $parts[1];
				$add_id = '';
			}
			$sections[] = [
				'level'   => intval( substr( $tag, 1 ) ),
				'slug'    => $slug,
				'content' => wp_strip_all_tags( $content ),
			];
			return (
				'<' . $tag . $add_id . ' ' . trim( $open_extra ) . '>'
					. $content
					. '<a class="cp-heading-link" href="#' . $slug . '">'
						. '<span aria-hidden="true">#</span>'
						. '<span class="screen-reader-text">Link to this section</span>'
					. '</a>'
				. $close
			);
		},
		$content
	);

/*
	// Uncomment this to get ToC in the_content
	if ( count( $sections ) >= 3 ) {
		// Display a small table of contents at the top of the post
		echo '<div class="cpnet-toc">';
		echo 'Contents:';
		$level = 1;
		foreach ( $sections as $section ) {
			while ( $level < $section['level'] ) {
				echo '<ul>';
				$level++;
			}
			while ( $level > $section['level'] ) {
				echo '</ul>';
				$level--;
			}
			echo '<li><a href="#' . $section['slug'] . '">'
				. $section['content']
				. '</a></li>';
		}
		while ( $level > 1 ) {
			echo '</ul>';
			$level--;
		}
		echo '</div>';
	}
*/
	return $content;
}
