<?php

require_once __DIR__ . '/markdown-sync/plugin.php';
require_once __DIR__ . '/markdown-sync/class-cpdocs-editor.php';
require_once __DIR__ . '/markdown-sync/class-cpdocs-importer.php';
require_once __DIR__ . '/markdown-sync/jetpack-markdown/extra.php';
require_once __DIR__ . '/markdown-sync/jetpack-markdown/gfm.php';

$cpnet_docs_importer = new CPDocs_Importer();
$cpnet_docs_editor = new CPDocs_Editor( $cpnet_docs_importer );
add_action( 'init', [ $cpnet_docs_editor, 'init' ] );

if ( class_exists( 'WP_CLI' ) ) {
	WP_CLI::add_command(
		'markdown-sync import-manifest',
		[ $cpnet_docs_importer, 'import_manifest' ]
	);
	WP_CLI::add_command(
		'markdown-sync import-content',
		[ $cpnet_docs_importer, 'import_all_markdown' ]
	);
	WP_CLI::add_command(
		'markdown-sync import-all',
		[ $cpnet_docs_importer, 'import_all' ]
	);
}
