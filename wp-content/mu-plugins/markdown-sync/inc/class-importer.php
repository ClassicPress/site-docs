<?php

namespace WordPressdotorg\Markdown;

use WP_CLI;
use WP_Error;
use WP_Post;
use WP_Query;
use WPCom_GHF_Markdown_Parser;

abstract class Importer {
	/**
	 * Meta key to store source in.
	 *
	 * @var string
	 */
	protected $meta_key = 'wporg_markdown_source';

	/**
	 * Meta key to store request ETag in.
	 *
	 * @var string
	 */
	protected $etag_meta_key = 'wporg_markdown_etag';

	/**
	 * Posts per page to query for.
	 *
	 * This needs to be set at least as high as the number of pages being
	 * imported, but should not be unbounded (-1).
	 *
	 * @var int
	 */
	protected $posts_per_page = 350;

	/**
	 * Get base URL for all pages.
	 *
	 * This is used for generating the keys for the existing pages.
	 *
	 * @see static::get_existing_for_post()
	 *
	 * @return string Base URL to strip from page permalink.
	 */
	abstract protected function get_base();

	/**
	 * Get manifest URL.
	 *
	 * This URL should point to a JSON file containing the manifest for the
	 * site's content. (Typically raw.githubusercontent.com)
	 *
	 * @return string URL for the manifest file.
	 */
	abstract protected function get_manifest_url();

	/**
	 * Get post type for the type being imported.
	 *
	 * @return string Post type slug to import as.
	 */
	abstract public function get_post_type();

	/**
	 * Get existing data for a given post.
	 *
	 * @param WP_Post $post Post to get existing data for.
	 * @return array 2-tuple of array key and data.
	 */
	protected function get_existing_for_post( WP_Post $post ) {
		$key = rtrim( str_replace( $this->get_base(), '', get_permalink( $post->ID ) ), '/' );
		if ( empty( $key ) ) {
			$key = 'index';
		}

		$data = array(
			'post_id' => $post->ID,
		);
		return array( $key, $data );
	}

	/**
	 * Import the manifest.
	 *
	 * Fetches the manifest, parses, and creates pages as needed.
	 */
	public function import_manifest() {
		$options = [
			'headers' => $this->get_manifest_headers(),
		];
		$response = wp_remote_get( $this->get_manifest_url(), $options );
		if ( is_wp_error( $response ) ) {
			if ( class_exists( 'WP_CLI' ) ) {
				WP_CLI::error( $response->get_error_message() );
			}
			return $response;
		} elseif ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			if ( class_exists( 'WP_CLI' ) ) {
				WP_CLI::error( 'Non-200 from Markdown source' );
			}
			return new WP_Error( 'invalid-http-code', 'Markdown source returned non-200 http code.' );
		}
		$manifest = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( ! $manifest ) {
			if ( class_exists( 'WP_CLI' ) ) {
				WP_CLI::error( 'Invalid manifest' );
			}
			return new WP_Error( 'invalid-manifest', 'Manifest did not unfurl properly.' );;
		}
		// Fetch all handbook posts for comparison
		$q = new WP_Query( array(
			'post_type'      => $this->get_post_type(),
			'post_status'    => 'publish',
			'posts_per_page' => $this->posts_per_page,
		) );
		$existing = array();
		foreach ( $q->posts as $post ) {
			list( $key, $data ) = $this->get_existing_for_post( $post );
			$existing[ $key ] = $data;
		}
		$created = $updated = 0;
		foreach ( $manifest as $key => $doc ) {
			// Already exists, update.
			if ( ! empty( $existing[ $key ] ) ) {
				$existing_id = $existing[ $key ]['post_id'];
				if ( $this->update_post_from_manifest_doc( $existing_id, $doc ) ) {
					$updated++;
				}

				continue;
			}
			if ( $this->process_manifest_doc( $doc, $existing, $manifest ) ) {
				$created++;
			}
		}
		if ( class_exists( 'WP_CLI' ) ) {
			WP_CLI::success( "Successfully created {$created} and updated {$updated} handbook pages." );
		}
	}

	/**
	 * Process a document from the manifest.
	 *
	 * @param array $doc Document to process.
	 * @param array $existing List of existing posts, will be added to.
	 * @param array $manifest Manifest data.
	 * @return boolean True if processing succeeded, false otherwise.
	 */
	protected function process_manifest_doc( $doc, &$existing, $manifest ) {
		$post_parent = null;
		if ( ! empty( $doc['parent'] ) ) {
			// Find the parent in the existing set
			if ( empty( $existing[ $doc['parent'] ] ) ) {
				if ( ! $this->process_manifest_doc( $manifest[ $doc['parent'] ], $existing, $manifest ) ) {
					return false;
				}
			}
			if ( ! empty( $existing[ $doc['parent'] ] ) ) {
				$parent = $existing[ $doc['parent'] ];
				$post_parent = $parent['post_id'];
			}
		}
		$post = $this->create_post_from_manifest_doc( $doc, $post_parent );
		if ( $post ) {
			list( $key, $data ) = $this->get_existing_for_post( $post );
			$existing[ $key ] = $data;
			return true;
		}
		return false;
	}

	/**
	 * Create a new handbook page from the manifest document
	 */
	protected function create_post_from_manifest_doc( $doc, $post_parent = null ) {
		if ( $doc['slug'] === 'index' ) {
			$doc['slug'] = $this->get_post_type();
		}
		$post_data = array(
			'post_type'   => $this->get_post_type(),
			'post_status' => 'publish',
			'post_parent' => $post_parent,
			'post_title'  => wp_slash( $doc['slug'] ),
			'post_name'   => sanitize_title( $doc['slug'] ),
		);
		if ( isset( $doc['title'] ) ) {
			$doc['post_title'] = sanitize_text_field( wp_slash( $doc['title'] ) );
		}
		$post_id = wp_insert_post( $post_data );
		if ( ! $post_id ) {
			return false;
		}
		if ( class_exists( 'WP_CLI' ) ) {
			WP_CLI::log( "Created post {$post_id} for {$doc['slug']}." );
		}
		update_post_meta( $post_id, $this->meta_key, esc_url_raw( $doc['markdown_source'] ) );
		return get_post( $post_id );
	}

	/**
	 * Update an existing post from the manifest.
	 *
	 * @param int $post_id Existing post ID.
	 * @param array $doc Document details from the manifest.
	 * @return boolean True if updated, false otherwise.
	 */
	protected function update_post_from_manifest_doc( $post_id, $doc ) {
		$did_update = update_post_meta( $post_id, $this->meta_key, esc_url_raw( $doc['markdown_source'] ) );
		return $did_update;
	}

	/**
	 * Update existing posts from Markdown source.
	 *
	 * Reparses the Markdown for every page.
	 */
	public function import_all_markdown() {
		$q = new WP_Query( array(
			'post_type'      => $this->get_post_type(),
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'posts_per_page' => $this->posts_per_page,
		) );
		$ids = $q->posts;
		$success = 0;
		foreach( $ids as $id ) {
			$ret = $this->update_post_from_markdown_source( $id );
			if ( class_exists( 'WP_CLI' ) ) {
				if ( is_wp_error( $ret ) ) {
					WP_CLI::warning( $ret->get_error_message() );
				} elseif ( false === $ret ) {
					WP_CLI::log( "No updates for {$id}" );
					$success++;
				} else {
					WP_CLI::log( "Updated {$id} from markdown source" );
					$success++;
				}
			}
		}
		if ( class_exists( 'WP_CLI' ) ) {
			$total = count( $ids );
			WP_CLI::success( "Successfully updated {$success} of {$total} pages." );
		}
	}

	/**
	 * Update a post from its Markdown source.
	 *
	 * @param int $post_id Post ID to update.
	 * @return boolean|WP_Error True if updated, false if no update needed, error otherwise.
	 */
	protected function update_post_from_markdown_source( $post_id ) {
		$markdown_source = $this->get_markdown_source( $post_id );
		if ( is_wp_error( $markdown_source ) ) {
			return $markdown_source;
		}
		if ( ! function_exists( 'jetpack_require_lib' ) ) {
			return new WP_Error( 'missing-jetpack-require-lib', 'jetpack_require_lib() is missing on system.' );
		}

		// Transform GitHub repo HTML pages into their raw equivalents
		$markdown_source = $this->get_markdown_download_source( $markdown_source, $post_id );

		// Grab the stored ETag, and use it to deduplicate.
		$args = array(
			'headers' => $this->get_markdown_source_headers( $post_id ),
		);
		$last_etag = get_post_meta( $post_id, $this->etag_meta_key, true );
		if ( ! empty( $last_etag ) ) {
			$args['headers']['If-None-Match'] = $last_etag;
		}

		$response = wp_remote_get( $markdown_source, $args );
		if ( is_wp_error( $response ) ) {
			return $response;
		} elseif ( 304 === wp_remote_retrieve_response_code( $response ) ) {
			// No update required!
			return false;
		} elseif ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return new WP_Error( 'invalid-http-code', 'Markdown source returned non-200 http code.' );
		}

		$etag = wp_remote_retrieve_header( $response, 'etag' );

		$markdown = wp_remote_retrieve_body( $response );

		// Get YAML doc from the header
		preg_match( '#^---(.+)---#Us', $markdown, $yaml_matches );
		if ( $yaml_matches ) {
			$yaml_parser = new Yaml();
			$yaml        = $yaml_parser->loadString( $yaml_matches[1] );
			// Strip YAML doc from the header
			$markdown = mb_substr( $markdown, mb_strlen( $yaml_matches[0] ) );
		} else {
			$yaml = array();
		}

		$title = null;
		if ( preg_match( '/^\n*#\s([^\n]+)/', $markdown, $matches ) ) {
			$title    = $matches[1];
			$markdown = preg_replace( '/^\n*#\s([^\n]+)/', '', $markdown );
		}
		// Allow YAML override.
		if ( isset( $yaml['title'] ) ) {
			$title = $yaml['title'];
		}
		$markdown = trim( $markdown );

		// Steal the first paragraph as the excerpt
		$excerpt = '';
		if ( preg_match( '/^(.*?)\n\n/', $markdown, $matches ) ) {
			$excerpt = $matches[1];
		}
		// Allow YAML override.
		if ( isset( $yaml['description'] ) ) {
			$excerpt = $yaml['description'];
		}

		// Transform to HTML and save the post
		jetpack_require_lib( 'markdown' );
		$parser = new WPCom_GHF_Markdown_Parser();
		$parser->preserve_shortcodes = false;
		$html = $parser->transform( $markdown );
		$post_data = array(
			'ID'           => $post_id,
			'post_content' => wp_kses_post( $html ),
			'post_excerpt' => sanitize_text_field( $excerpt ),
		);
		if ( ! is_null( $title ) ) {
			$post_data['post_title'] = sanitize_text_field( $title );
		}

		/**
		 * Filters the post data saved to the database for a post.
		 *
		 * This can be used to set additional post fields based on YAML front
		 * matter, such as publish data or author.
		 *
		 * @param array $post_data Post data to pass to wp_update_post()
		 * @param array $yaml      The parsed YAML front matter.
		 */
		$post_data = apply_filters( 'wordpressdotorg.markdown_sync.post_data', $post_data, $yaml );
		wp_update_post( wp_slash( $post_data ) );

		// Add meta data from YAML front matter.
		if ( isset( $yaml['meta'] ) && is_array( $yaml['meta'] ) ) {
			/**
			 * Filters a whitelisted set of meta keys found in the markdown
			 * YAML front matter.
			 *
			 * Example markdown:
			 *
			 * ---
			 * title: Hello World
			 * meta:
			 *   foo: bar
			 *   baz:
			 *     - lurhmann
			 * ---
			 * # Markdown starts here
			 *
			 * Anything under `meta:` with a key that's present in $whitelisted_keys will
			 * be imported as post meta. Arrays will be serialised.
			 *
			 * @param array $whitelist_keys The meta keys to be imported if found.
			 * @param array $yaml           The parsed YAML front matter.
			 */
			$whitelisted_keys = apply_filters( 'wordpressdotorg.markdown_sync.meta_whitelist', array(), $yaml );
			$meta = array_intersect_key( $yaml['meta'], array_flip( $whitelisted_keys ) );
			foreach ( $meta as $key => $value ) {
				update_post_meta( $post_id, wp_slash( $key ), wp_slash( $value ) );
			}
		}

		// Set ETag for future updates.
		update_post_meta( $post_id, $this->etag_meta_key, wp_slash( $etag ) );

		/**
		 * Action for any post processing after post update.
		 *
		 * @param int        $post_id The post's ID.
		 * @param array|bool $yaml    The YAML front matter as an array.
		 */
		do_action( 'wordpressdotorg.markdown_sync.update_post', $post_id, $yaml );

		return true;
	}

	/**
	 * Retrieve the markdown source URL for a given post.
	 */
	public function get_markdown_source( $post_id ) {
		$markdown_source = get_post_meta( $post_id, $this->meta_key, true );
		if ( ! $markdown_source ) {
			return new WP_Error( 'missing-markdown-source', 'Markdown source is missing for post.' );
		}

		return $markdown_source;
	}

	/**
	 * Convert the source URL to a download URL.
	 *
	 * @param string $source_url Source URL from {@see get_markdown_source}.
	 * @param int    $post_id Post ID being fetched.
	 * @return string URL to download source from.
	 */
	protected function get_markdown_download_source( $source_url, $post_id ) {
		$source_url = preg_replace( '#https?://github\.com/([^/]+/[^/]+)/blob/(.+)#', 'https://raw.githubusercontent.com/$1/$2', $source_url );
		$source_url = add_query_arg( 'v', time(), $source_url );
		return $source_url;
	}

	/**
	 * Get headers to send to Markdown source URL.
	 *
	 * @param int $post_id Post ID being fetched.
	 * @return array Headers to pass to wp_remote_request()
	 */
	protected function get_markdown_source_headers( $post_id ) {
		return array();
	}

	/**
	 * Get headers to send to manifest URL.
	 *
	 * @return array Headers to pass to wp_remote_request()
	 */
	protected function get_manifest_headers() {
		return array();
	}
}
