<?php
/**
 * Reference Template: Source Information
 *
 * @package wporg-developer
 * @subpackage Reference
 */

namespace DevHub;

$source_file = get_source_file();
if ( ! empty( $source_file ) ) :
	?>
	<hr />
	<section class="source-content">
		<h2><?php _e( 'Source', 'wporg' ); ?></h2>
		<p>
			<?php
			printf(
				__( 'File: %s', 'wporg' ),
				'<a href="' . esc_url( get_source_file_archive_link( $source_file ) ) . '">' . esc_html( $source_file ) . '</a>'
			);
			?>
		</p>

		<?php if ( post_type_has_source_code() ) : ?>
			<div class="source-code-container">
				<pre id="code-ref-source" class="line-numbers folded"><code class="language-php"><?php echo htmlentities( get_source_code() ); ?></code></pre>
			</div>
			<p class="source-action-links">
				<span>
					<a href="#" id="toggle-complete-source" class="show-hide-source"><?php _e( 'Expand Source Code ', 'wporg' ); ?></a> 
				</span>
				<span><a href="<?php echo get_source_file_link(); ?>"><?php _e( 'View on GitHub', 'wporg' ); ?></a></span>
			</p>
		<?php else : ?>
			<p>
				<a href="<?php echo get_source_file_link(); ?>"><?php _e( 'View on GitHub', 'wporg' ); ?></a>
			</p>
		<?php endif; ?>
	</section>
	<script type="text/javascript">
		jQuery(document).ready(function(){
			if( jQuery('#code-ref-source code').height() > 309) {
				jQuery('#toggle-complete-source').show();
			}
			jQuery("#toggle-complete-source").click(function(e){
				e.preventDefault();
				  if( jQuery('#code-ref-source').hasClass('folded') && jQuery('#code-ref-source code').height() > 309) {
					jQuery('#code-ref-source').removeClass('folded');
					jQuery("#toggle-complete-source").text('Collapse Source Code ');
				  } else if( jQuery('#code-ref-source').height() > 309 ) { 
					jQuery('#code-ref-source').addClass('folded');
					jQuery("#toggle-complete-source").text('Expand Source Code ');
				  }
			});
		});
	</script>
<?php endif; ?>
