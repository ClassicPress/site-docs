<?php

use WordPressdotorg\Markdown\Editor;

class CPDocs_Editor extends Editor {
	/**
	 * Override for main initialization function - we will modify some of the
	 * parent class' behavior.
	 */
	public function init() {
		parent::init();
		// Edit links on the frontend are handled by this site's theme.
		remove_filter( 'the_title', [ $this, 'filter_the_title_edit_link' ] );
		remove_filter( 'get_edit_post_link', [ $this, 'redirect_edit_link_to_github' ] );
		// Disable some features of the post editor.
		add_filter( 'replace_editor', [ $this, 'maybe_disable_edits' ], 10, 2 );
	}

	/**
	 * Override for getting a markdown edit link.
	 *
	 * This override exists to change the method visibility to public, so that
	 * we can use it elsewhere.  Fixing this in the original plugin would be a
	 * backwards-incompatible change.
	 */
	public function get_markdown_edit_link( $post_id ) {
		return parent::get_markdown_edit_link( $post_id );
	}

	/**
	 * New function to determine whether this particular post is handled by the
	 * markdown sync system.
	 */
	public function is_markdown_post( WP_Post $post ) {
		if ( $post->post_type !== $this->importer->get_post_type() ) {
			return false;
		}

		$source = $this->importer->get_markdown_source( $post->ID );
		if ( is_wp_error( $source ) || empty( $source ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Override for rendering an editor warning: we shouldn't do this for all
	 * pages, only the ones where we actually have Markdown content.
	 *
	 * We also modify the text of this warning slightly.
	 *
	 * @param WP_Post $post Post being edited.
	 */
	public function render_editor_warning( WP_Post $post ) {
		if ( ! $this->is_markdown_post( $post ) ) {
			return;
		}

		printf(
			'<div class="notice notice-warning"><p>%s</p><p><a href="%s">%s</a></p></div>',
			'This page is maintained on GitHub, please make any changes to the content, title, and slug there instead.',
			$this->get_markdown_edit_link( $post->ID ),
			'Edit on GitHub'
		);
	}

	/**
	 * New function to disable editing content, title, and slug for posts that
	 * are edited on GitHub.
	 */
	public function maybe_disable_edits( $replace, $post ) {
		if ( $this->is_markdown_post( $post ) ) {
			// Don't return true because that will replace the entire editor
			// interface.  Instead use this hack to disable the content box
			// just in time.
			remove_post_type_support( $post->post_type, 'editor' );
			// Disable editing the title and the slug.  This just disables the
			// UI controls (doesn't actually disable the related POST request)
			// but that is good enough for our use-case.
			add_action(
				'edit_form_top',
				[ $this, 'disable_edits_step1' ]
			);
		}

		return $replace;
	}

	/**
	 * New function to disable editing the title and the permalink (slug).
	 *
	 * Step 1: Setup - enable these hacks just in time.
	 */
	public function disable_edits_step1( $post ) {
		ob_start(); // Catch the HTML for the title edit box.
		add_filter(
			'page_link',
			[ $this, 'disable_edits_step2' ],
			10,
			3
		);
		add_action(
			'edit_form_after_title',
			[ $this, 'disable_edits_step3' ]
		);
	}

	/**
	 * New function to disable editing the title and the permalink (slug).
	 *
	 * Step 2: Disable editing the slug by changing the sample permalink HTML.
	 *
	 * See: https://github.com/ClassicPress/ClassicPress/blob/1.1.2+dev/src/wp-admin/includes/post.php#L1355-L1385
	 */
	public function disable_edits_step2( $link, $post_id, $sample ) {
		if ( ! $sample ) {
			return $link;
		}

		return str_replace(
			'%pagename%',
			get_post( $post_id )->post_name,
			$link
		);
	}

	/**
	 * New function to disable editing the title and the permalink (slug).
	 *
	 * Step 3: Make the title edit box read-only, and unhook temporary filters.
	 *
	 * See: https://github.com/ClassicPress/ClassicPress/blob/1.1.2+dev/src/wp-admin/edit-form-advanced.php#L574
	 */
	public function disable_edits_step3() {
		// Get the currently buffered HTML including the title edit box.
		$html = ob_get_clean();
		// Add a readonly attribute. input[readonly] has background #eee but
		// this is overridden by the CSS rules for the title edit box.
		echo preg_replace(
			'#<input type="text" name="post_title"#',
			'<input type="text" name="post_title" readonly style="background:#eee"',
			$html
		);
		// Remove the temporary filter we used to disable editing the slug.
		remove_filter(
			'page_link',
			[ $this, 'disable_edits_step2' ]
		);
	}
}
