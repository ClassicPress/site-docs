## Markdown Sync

This system allows managing the content of the ClassicPress documentation site
as a set of Markdown files stored on GitHub.  Much of the documentation on
wordpress.org works similarly.

The code in this directory is comprised of:

- Files from https://github.com/WP-API/markdown-sync (`plugin.php` and the
  `inc` directory)
- Files from
  [Jetpack](https://github.com/Automattic/jetpack/tree/master/_inc/lib/markdown)
  (the `jetpack-markdown` directory)
- Classes that configure and override the markdown sync behavior for this site
  (`class-cpdocs-importer.php` and `class-cpdocs-editor.php`)

The `wp-content/mu-plugins/markdown-sync.php` file is responsible for loading
this code in the appropriate order and registering the `wp markdown-sync`
WP-CLI commands.

Other elements required to make this into a working system:

- The https://github.com/ClassicPress/ClassicPress-docs repository where the
  content and the JSON manifest file live
- Code in the site's theme to show edit links on the frontend
- A cron job to run `wp markdown-sync import-all` regularly.

### Local development

The following are rough steps for making the markdown sync process work on your
local development server.

1. Get a copy of this site running.

2. Clone the https://github.com/ClassicPress/ClassicPress-docs repository and
   make your local web server host its files also.

3. Generate a new `bin/manifest.json` file for local development.

   For example, if this repository is available via your local dev server at
   `http://docs.classicpress.local/docs-content/` then you can run:

   ```sh
   MANIFEST_BASE_URL=http://docs.classicpress.local/docs-content/ php bin/generate-manifest.php
   ```

4. In your `.env` config file, set the `MARKDOWN_MANIFEST_URL` variable to the
   URL of your local manifest.  For example:

   ```sh
    MARKDOWN_MANIFEST_URL=http://docs.classicpress.local/docs-content/bin/manifest.json
   ```

5. You can use the following WP-CLI commands to re-sync the site's content:

   ```sh
   wp markdown-sync import-manifest --debug=markdown-sync
   wp markdown-sync import-content
   # or both steps at once:
   wp markdown-sync import-all
   ```

6. After importing Markdown content, you'll need to update links so that they
   point back to your local installation again:

   ```
   wp search-replace https://docs.classicpress.net http://docs.classicpress.local
   ```

Now you can continue editing your local copy of the Markdown documentation
files, and submit your edits as a pull request on GitHub when your changes are
ready.
