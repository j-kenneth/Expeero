<?php
/**
 * Tour tabs template part.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

add_filter( 'adventure_tours_tour_tabs', 'adventure_tours_filter_tour_tabs', 10 );
$tabs = apply_filters( 'adventure_tours_tour_tabs', array() );

if ( ! $tabs ) {
	return;
}

$tabKeys = array_keys( $tabs );
$activeTabKey = $tabKeys[0];
?>
<div class="tours-tabs">
	<ul class="nav nav-tabs">
	<?php foreach ( $tabs as $key => $tab ) {
		$class = !empty( $tab['tab_css_class'] ) ? esc_attr( $tab['tab_css_class'] ) : '';
		if ( $key == $activeTabKey ) {
			if ( $class ) {
				$class .= ' ';
			}
			$class .= 'active';
		}
		printf( '<li%s><a href="#tab%s" data-toggle="tab">%s</a></li>',
			$class ? ' class="' . $class . '"' : '',
			esc_attr( $key ),
			apply_filters( 'woocommerce_product_' . $key . '_tab_title', $tab['title'], $key )
		);
	} ?>
	</ul>

	<div class="tab-content">
		<?php foreach ( $tabs as $key => $tab ) : ?>
		<div class="tab-pane <?php echo ( $key == $activeTabKey ? 'in active' : 'fade' ); ?>" id="tab<?php echo esc_attr( $key ); ?>">
		<?php if ( ! empty( $tab['top_section_callback'] ) ) {
			call_user_func( $tab['top_section_callback'], $key, $tab );
		} ?>
			<div class="tours-tabs__content padding-all">
			<?php if ( ! empty( $tab['content'] ) ) {
				print( $tab['content'] );
			} else if ( ! empty( $tab['callback'] ) ) {
				call_user_func( $tab['callback'], $key, $tab );
			} ?>
			</div>
		</div>
		<?php endforeach; ?>

		<?php if ( adventure_tours_get_option( 'social_sharing_tour' ) ) {
			get_template_part( 'templates/parts/share-buttons' );
		} ?>
	</div><!-- .tab-content -->
</div><!-- .tour-tabs -->
