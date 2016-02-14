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
 * @version   1.2.4
 */

$attributesRequest = isset( $_REQUEST['tourtax'] ) ? $_REQUEST['tourtax'] : array();

$configs_map = array(
	'default' => array(),
	'style1' => array(
		'css' => 'form-block--light-style form-block--with-border',
	),
	'style2' => array(
		'css' => 'form-block--with-border',
	),
	'style3' => array(
		'css' => 'form-block--full-width form-block--big-indent form-block--with-label',
		'show_label' => true,
	),
	'style4' => array(
		'css' => 'form-block--full-width',
	),
);

$style_config = $style && isset( $configs_map[ $style ] ) ? $configs_map[ $style ] : $configs_map['default'];
$is_show_label = ! empty( $style_config['show_label']);


$form_items_html = AtTourHelper::get_search_form_fields_html( true, $is_show_label );


$block_css_classes = 'form-block form-block--horizontal';
if ( !empty( $style_config['css'] ) ) {
	$block_css_classes .= ' ' . $style_config['css'];
}

if ( $title ) {
	$block_css_classes .= ' form-block--with-title';
}

if ( $css_class ) {
	$block_css_classes .= ' ' . $css_class;
}

$search_field_cells = 4;
$field_cells = 2;
$left_cells = 12;
?>
<div class="<?php echo esc_attr( $block_css_classes ); ?>">
	<?php if ( $title || $note ) {
		echo do_shortcode( '[title text="' . $title . '" subtitle="' . $note . '" size="small" position="center" decoration="on" underline="on" style="dark"]' );
	} ?>

	<form class="form-block__form" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<input type="hidden" name="toursearch" value="1">
	<?php if ( adventure_tours_check( 'is_wpml_in_use' ) ) { ?>
		<input type="hidden" name="lang" value="<?php echo esc_attr( ICL_LANGUAGE_CODE ); ?>">
	<?php } ?>

		<div class="row">
		<?php
			if ( ! $hide_text_field ) {
				$text_field_label = _x( 'Search Tour', 'placeholder', 'adventure-tours' );

				printf(
					'<div class="%s">%s' . 
						'<div class="form-block__item form-block__field-width-icon">' .
							'<input type="text" %svalue="%s" name="s"><i class="td-search-1"></i>' .
						'</div>' .
					'</div>',
					esc_attr( 'col-sm-' . $search_field_cells ),
					$is_show_label ? sprintf( '<div class="form-block__item-label">%s</div>', esc_html( $text_field_label ) ) : '',
					$is_show_label ? '' : sprintf( 'placeholder="%s" ', esc_attr( $text_field_label ) ),
					get_search_query()
				);
				$left_cells -= $search_field_cells;
			}

			foreach( $form_items_html as $form_element_config ) {
				$has_icon = !empty( $form_element_config['icon'] ) && 'none' != $form_element_config['icon'];

				printf( '<div class="%s">%s<div class="form-block__item%s">%s%s</div></div>',
					esc_attr( 'col-sm-' . $field_cells ),
					$is_show_label ? sprintf( '<div class="form-block__item-label">%s</div>', esc_html( $form_element_config['label'] ) ) : '',
					$has_icon ? ' form-block__field-width-icon' : '',
					$form_element_config['html'],
					$has_icon ? sprintf('<i class="%s"></i>', esc_attr( $form_element_config['icon'] ) ) : ''
				);

				$left_cells -= $field_cells;
				if ( $left_cells < $field_cells ) {
					echo '</div><div class="row">';
					$left_cells = 12;
				}
			}

			printf( 
				'<div class="%s"><button type="submit" class="atbtn atbtn--full-width atbtn--primary">%s</button></div>',
				esc_attr( 'col-sm-' . $left_cells ),
				esc_html__( 'Find Tours', 'adventure-tours' )
			);
		?>
		</div>
	</form>
</div>
