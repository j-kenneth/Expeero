<?php
/**
 * Shortcode [icons_set] view.
 * For more detailed list see list of shortcode attributes.
 *
 * @var array   $items
 * @var integer $row_size
 * @var string  $css_class
 * @var string  $view
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.3.0
 */

if ( ! $items ) {
	return;
}

if ( $row_size < 2 ) {
	$row_size = 2;
} elseif ( $row_size > 4 ) {
	$row_size = 4;
}
$cell_size = 12 / $row_size;
$row_item_counter = 0;

?>
<div class="icons-set<?php if ( ! empty( $css_class ) ) { echo ' ' . esc_attr( $css_class ); }; ?>">
	<div class="row icons-set__row">
	<?php foreach ( $items as $item ) { ?>
		<?php
		if ( 0 != $row_item_counter ) {
			if ( $row_item_counter % $row_size == 0 ) {
				echo '</div><div class="row icons-set__row">';
				$row_item_counter = 0;
			}
		}
		$row_item_counter++;
		?>
		<div class="col-sm-<?php echo esc_attr( $cell_size ); ?>">
			<div class="icons-set__item">
				<?php if( $item['icon'] ) { ?>
					<div class="icons-set__item__field icons-set__item__field--fix-size">
						<div class="icons-set__item__icon-wrap"><i class="icons-set__item__icon<?php echo ' ' . esc_attr( $item['icon'] ); ?>"></i></div>
					</div>
				<?php } ?>
				<div class="icons-set__item__field">
					<?php if ( $item['title'] ) { ?>
						<h3 class="icons-set__item__title"><?php echo esc_html( $item['title'] ); ?></h3>
					<?php } ?>
					<div class="icons-set__item__description"><?php echo do_shortcode( $item['content'] ); ?></div>
				</div>
			</div>
		</div>
	<?php } ?>
	</div>
</div>
