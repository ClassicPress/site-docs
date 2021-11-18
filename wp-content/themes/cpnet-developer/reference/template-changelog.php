<?php
/**
 * Reference Template: Changelog
 *
 * @package wporg-developer
 * @subpackage Reference
 */

namespace DevHub;

$changelog_data = get_changelog_data();
if ( ! empty( $changelog_data ) ) :
	?>
	<hr />
	<section class="changelog">
		<h2><?php _e( 'Changelog', 'wporg' ); ?></h2>

		<table>
			<caption class="screen-reader-text"><?php _e( 'Changelog', 'wporg' ); ?></caption>
			<thead>
				<tr>
					<th class="changelog-version"><?php _e( 'Version', 'wporg' ); ?></th>
					<th class="changelog-desc"><?php _e( 'Description', 'wporg' ); ?></th>
				</tr>
			</thead>

			<tbody>
				<?php
				/**
				 * Sort the changelog data by version. In the ClassicPress
				 * codebase, ClassicPress versions do not have a prefix, and
				 * WordPress versions are prefixed with "WP-" like "WP-1.2.3".
				 * The fork point for ClassicPress was around WP-4.9.8 so the
				 * sort order is as follows:
				 *
				 *  - WP-4.0.0
				 *  - WP-4.9.8
				 *  - 1.0.0-alpha1 (etc)
				 *  - 1.0.0
				 *  - 1.2.0 (etc)
				 *  - WP-4.9.9
				 *  - WP-5.2.0 (etc)
				 *
				 * This is certainly not perfect! Each `@since WP-5.2.0`
				 * corresponds to a specific ClassicPress version, and this
				 * information needs to be made available either automatically
				 * (by parsing multiple ClassicPress versions) or by updating
				 * the `@since` tags in the code.
				 */
				uksort( $changelog_data, function( $a, $b ) {
					$a_is_wp = ( substr( $a, 0, 3 ) === 'WP-' );
					$b_is_wp = ( substr( $b, 0, 3 ) === 'WP-' );
					if ( $a_is_wp && $b_is_wp ) {
						return version_compare( $a, $b );
					} else if ( $a_is_wp ) {
						return version_compare( $a, 'WP-4.9.8' );
					} else if ( $b_is_wp ) {
						return version_compare( 'WP-4.9.8', $b );
					} else {
						return version_compare( $a, $b );
					}
				} );

				$changelog_data = array_reverse( $changelog_data );

				$count = count( $changelog_data );
				$i = 0;

				foreach ( $changelog_data as $version => $data ) : ?>
					<?php
					// Add "Introduced." for the initial version description,
					// last since the array is reversed.
					if ( $i === $count - 1 ) {
						$description = __( 'Introduced.', 'wporg' ) . ' ';
					} else {
						$description = '';
					}
					$description .= $data['description'];

					if ( substr( $version, 0, 3 ) === 'WP-' ) {
						$version_description = "WordPress " . substr( $version, 3 );
					} else {
						$version_description = "ClassicPress $version";
					}

					$version_link = sprintf( '<a href="%1$s" alt="%2$s" title="%3$s">%4$s</a>',
						esc_url( $data['since_url'] ),
						esc_attr( $version_description ),
						esc_attr( $version_description ),
						esc_html( $version )
					);

					$i++;
					?>

					<tr>
						<td><?php echo $version_link; ?></td>
						<td><?php echo $description; ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</section>
<?php endif; ?>

