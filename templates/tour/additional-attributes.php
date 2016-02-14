<?php
/**
 * View for rendering tour additional attributes.
 *
 * @param  string $title
 * @param  array  $attributes
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

if ( ! $attributes ) {
	return;
}

$alt = 1;
?>
<div class="margin-top"></div>

<?php if ( isset( $title ) && $title ) {
	printf( '<h2>%s</h2>', esc_html( $title ) );
} ?>

<table class="shop_attributes">
<?php foreach ( $attributes as $attribute ) {
	printf('<tr%s><th>%s</th><td>%s</td></tr>',
		( $alt = ! $alt ) ? ' class="alt"' : '',
		esc_html( $attribute['label'] ),
		esc_html( $attribute['text'] )
	);
}; ?>
</table>
