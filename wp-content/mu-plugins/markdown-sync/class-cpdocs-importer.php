<?php

use WordPressdotorg\Markdown\Importer;

class CPDocs_Importer extends Importer {
	/**
	 * Mandatory override for base URL.
	 *
	 * This is not actually used anywhere in our setup, since we override
	 * `get_existing_for_post()` below.
	 */
	public function get_base() {
		return WP_SITEURL;
	}

	/**
	 * Mandatory override for manifest URL.
	 */
	public function get_manifest_url() {
		return (
			'https://raw.githubusercontent.com/'
			. 'ClassicPress/ClassicPress-docs/master/bin/manifest.json'
		);
	}

	/**
	 * Mandatory override for post type.
	 */
	public function get_post_type() {
		return 'page';
	}

	/**
	 * Override for linking up a post to a manifest entry.
	 *
	 * The structure of our manifest mirrors the URL structure of this site, so
	 * we just need to look at the post slug and its parent slugs (if any).
	 */
	protected function get_existing_for_post( WP_Post $post ) {
		$key = [];
		$parent = $post;
		do {
			$key[] = $parent->post_name;
			$parent = get_post( $parent->post_parent );
		} while ( ! empty( $parent ) );

		$key = implode( '/', array_reverse( $key ) );

		$data = [ 'post_id' => $post->ID ];

		return [ $key, $data ];
	}

	/**
	 * Additional function run both `import_manifest()` and
	 * `import_all_markdown()`, so that everything can be updated from a single
	 * cron job.
	 */
	public function import_all() {
		$result = $this->import_manifest();
		if ( is_wp_error( $result ) ) {
			wp_die( $result );
		}
		$result = $this->import_all_markdown();
		if ( is_wp_error( $result ) ) {
			wp_die( $result );
		}
	}
}
