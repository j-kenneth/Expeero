<?php
/**
 * Shortcode [tour_search_form] view.
 * For more detailed list see list of shortcode attributes.
 *
 * @var string  $title
 * @var string  $note
 * @var strign  $style
 * @var string  $css_class
 * @var boolean $hide_text_field
 * @var string  $view
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.2.2
 */

?>
<div class="form-block block-after-indent<?php if ( $css_class ) { echo esc_attr( ' ' . $css_class ); } ?>">
<?php if ( $title ) { ?>
	<h3 class="form-block__title"><?php echo esc_html( $title ); ?></h3>
<?php } ?>

<?php if ( $note ) { ?>
	<div class="form-block__description"><?php echo esc_html( $note ); ?></div>
<?php } ?>

	<form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<input type="hidden" name="toursearch" value="1">

	<?php if ( adventure_tours_check( 'is_wpml_in_use' ) ) { ?>
		<input type="hidden" name="lang" value="<?php echo esc_attr( ICL_LANGUAGE_CODE ); ?>">
	<?php } ?>

	<?php if ( ! $hide_text_field ) { ?>
		<div class="form-block__item form-block__field-width-icon">
			<input type="text" placeholder="<?php echo esc_attr_x( 'Search Tour', 'placeholder', 'adventure-tours' ); ?>" value="<?php echo get_search_query(); ?>" name="s">
			<i class="td-search-1"></i>
		</div>
	<?php } ?>

	<?php
		$form_items_html = AtTourHelper::get_search_form_fields_html( true );
		foreach( $form_items_html as $form_element_config ) {
			$has_icon = !empty( $form_element_config['icon'] ) && 'none' != $form_element_config['icon'];
			printf(
				'<div class="form-block__item%s">%s%s</div>',
				$has_icon ? ' form-block__field-width-icon' : '',
				$form_element_config['html'],
				$has_icon ? sprintf('<i class="%s"></i>', esc_attr( $form_element_config['icon'] ) ) : ''
			);
		}
	?>
		<button type="submit" class="atbtn atbtn--full-width atbtn--primary"><?php esc_attr_e( 'Find Tours', 'adventure-tours' ); ?></button>
	</form>
</div>
