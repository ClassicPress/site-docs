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
