<?php
/**
 * Post tags rendering template part.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

if ( ! has_tag() ) {
	return '';
}
?>
<div class="post-tags margin-top">
	<span><i class="fa fa-tags"></i><?php esc_html_e( 'Tags', 'adventure-tours' ); ?>:</span>
	<?php the_tags( '', ' ', '' ); ?>
</div>
