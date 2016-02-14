<?php
/**
 * The template part for displaying tour category thumbnails within the loops.
 * Based on WooCommerce 1.6.4.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

?>
<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$category_link = get_term_link( $category->slug, 'tour_category' );

$columns = adventure_tours_di( 'register' )->getVar( 'tour_cat_columns', 3 );
if ( $columns < 2 ) {
	$columns = 2;
} else if ( $columns > 4 ) {
	$columns = 4;
}
$wrapper_class = 'product-category-wrap col-xs-6 col-md-' . ( 12 / $columns );

$catIndex = adventure_tours_di( 'register' )->getVar( 'tour_category_el_index', 0 );
if ( $catIndex > 0 && 0 == ($catIndex % $columns) ) {
	echo '<div class="product-category__row-separator clearfix hidden-sm hidden-xs"></div>';
}
if ( $catIndex > 0 && 0 == ($catIndex % 2) ) {
	echo '<div class="product-category__row-separator clearfix visible-sm visible-xs"></div>';
}
$catIndex++;
adventure_tours_di( 'register' )->setVar( 'tour_category_el_index', $catIndex );
?>
<div class="<?php echo esc_attr( $wrapper_class ); ?>">
	<div class="product-category">
		<a href="<?php echo esc_url( $category_link ); ?>" class="product-category__image"><?php echo adventure_tours_render_category_thumbnail( $category ); ?></a>
		<div class="product-category__content">
		<?php printf(
			'<h3 class="product-category__title"><a href="%s">%s</a></h3>',
			esc_url( $category_link ),
			esc_html( $category->name )
		); ?>
		<?php if ( $category->description ) { ?>
			<div class="product-category__description"><?php echo wc_format_content( $category->description ); ?></div>
		<?php } ?>
		</div>
		<div class="product-category__info">
			<?php if ( $category->count > 0 ) { ?>
				<div class="product-category__info__item"><?php echo esc_html( $category->count . ' ' . _n( 'tour', 'tours', $category->count, 'adventure-tours' ) ); ?></div>
			<?php } ?>
			<div class="product-category__info__item product-category__info__item--link">
				<a href="<?php echo esc_url( $category_link ); ?>"><?php esc_html_e( 'See tours', 'adventure-tours' ); ?><i class="fa fa-long-arrow-right"></i></a>
			</div>
		</div>
	</div>
</div>
