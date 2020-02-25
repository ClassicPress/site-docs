<?php

require_once __DIR__ . '/markdown-sync/plugin.php';
require_once __DIR__ . '/markdown-sync/class-cpdocs-importer.php';

$cpnet_docs_importer = new CPDocs_Importer();
$cpnet_docs_editor = new WordPressdotorg\Markdown\Editor( $cpnet_docs_importer );

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
