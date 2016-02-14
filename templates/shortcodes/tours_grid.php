<?php
/**
 * Shortcode [tours_grid] view.
 * For more detailed list see list of shortcode attributes.
 *
 * @var string  $title
 * @var boolean $title_underline
 * @var string  $sub_title
 * @var string  $image_size
 * @var string  $image_size_mobile
 * @var string  $btn_more_text           text for more button
 * @var string  $btn_more_link           url address for more button
 * @var string  $price_style             allowed values are: 'default', 'highlighted',
 * @var string  $description_words_limit limit for words that should be outputed for each item
 * @var string  $tour_category
 * @var string  $tour_category_ids
 * @var boolean $show_categories
 * @var string  $tour_ids
 * @var int     $number
 * @var string  $columns
 * @var string  $css_class
 * @var string  $orderby
 * @var string  $order
 * @var string  $view
 * @var array   $items                   collection of tours that should be rendered.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.2.3
 */

if ( ! $items ) {
	return;
}

// $max_col = wp_is_mobile() ? 2 : 4;
$max_col = 4;

$column_size = !empty( $columns ) ? $columns : $number;
if ( $column_size > $max_col || $column_size < 1 ) {
	$column_size = $max_col;
}

$item_wrapper_class = 'col-md-'.( 12 / $column_size ).' col-xs-6 atgrid__item-wrap';

if ( $image_size_mobile && wp_is_mobile() ) {
	$image_size = $image_size_mobile;
}

if ( $column_size > 3 ) {
	if ( $css_class ) {
		$css_class .= ' ';
	}
	$css_class .= 'atgrid--small';
}

$placeholder_image = adventure_tours_placeholder_img( $image_size );
?>

<div class="atgrid<?php if ( $css_class ) echo ' ' . esc_attr( $css_class ); ?>">
<?php if ( $btn_more_link && ( $title || $sub_title ) ) { ?>
	<div class="title-block-link title-block-link--with-button">
		<div class="title-block-link__text-wrap">
		<?php if ( $title ) { ?>
			<h3 class="title-block-link__title"><?php echo esc_html( $title ); ?></h3>
		<?php } ?>
		<?php if ( $sub_title ) { ?>
			<div class="title-block-link__description"><?php echo esc_html( $sub_title ); ?></div>
		<?php } ?>
		</div>
		<div class="title-block-link__button-wrap">
			<a href="<?php echo esc_url( $btn_more_link ); ?>" class="atbtn atbtn--rounded"><?php echo esc_html( $btn_more_text ); ?><i class="atbtn__icon atbtn__icon--right fa fa-long-arrow-right"></i></a>
		</div>
	</div>
<?php } elseif ( $title || $sub_title ) { ?>
	<?php echo do_shortcode( '[title text="' . $title . '" subtitle="' . $sub_title . '" size="big" position="center" decoration="on" underline="' . $title_underline . '" style="dark"]' ); ?>
<?php } ?>
	<div class="row atgrid__row">
	<?php foreach ( $items as $item_index => $item ) : ?>
		<?php
		$post_id = $item->id;
		$item_url = get_permalink( $post_id );
		$image_html = adventure_tours_get_the_post_thumbnail( $post_id, $image_size );
		$price_html = $item->get_price_html();

		if ( $item_index > 0 && $item_index % $column_size == 0 ) {
			// echo '</div><div class="row atgrid__row">';
			echo '<div class="atgrid__row-separator clearfix hidden-sm hidden-xs"></div>';
		}
		if ( $item_index > 0 && $item_index % 2 == 0 ) {
			echo '<div class="atgrid__row-separator clearfix visible-sm visible-xs"></div>';
		}
		?>
		<div class="<?php echo esc_attr( $item_wrapper_class ); ?>">
			<div class="atgrid__item">
				<div class="atgrid__item__top">
					<?php printf('<a href="%s" class="atgrid__item__top__image">%s</a>',
						esc_url( $item_url ),
						$image_html ? $image_html : $placeholder_image
					); ?>
					<?php if ( 'highlighted' == $price_style ) { ?>
						<?php
						$badge = adventure_tours_di( 'tour_badge_service' )->get_tour_badge( $post_id );
						printf('<a href="%s" class="price-round"%s><span class="price-round__content">%s</span></a>',
							esc_url( $item_url ),
							isset( $badge['color'] ) ? ' style="background-color:' . esc_attr( $badge['color'] ) . '"' : '',
							$price_html
						);
						?>
					<?php } else { ?>
						<?php adventure_tours_renders_tour_badge( array(
							'tour_id' => $post_id,
							'wrap_css_class' => 'atgrid__item__angle-wrap',
							'css_class' => 'atgrid__item__angle',
						) ); ?>
						<?php if ( $price_html ) {
							printf('<div class="atgrid__item__price"><a href="%s" class="atgrid__item__price__button">%s</a></div>',
								esc_url( $item_url ),
								$price_html
							);
						} ?>
					<?php } ?>
					<?php adventure_tours_renders_stars_rating($item->get_average_rating(), array(
						'before' => '<div class="atgrid__item__rating">',
						'after' => '</div>',
					)); ?>
					<?php if ( $show_categories ) {
						adventure_tours_render_tour_icons(array(
							'before' => '<div class="atgrid__item__icons">',
							'after' => '</div>',
						), $post_id );
					} ?>
				</div>
				<div class="atgrid__item__content">
					<h3 class="atgrid__item__title"><a href="<?php echo esc_url( $item_url ); ?>"><?php echo esc_html( $item->post->post_title ); ?></a></h3>
				<?php if ( $description_words_limit > 0 ) { ?>
					<div class="atgrid__item__description"><?php echo adventure_tours_get_short_description( $item->post, $description_words_limit ); ?></div>
				<?php } ?>
				</div>
				<div class="item-attributes">
					<?php adventure_tours_render_product_attributes(array(
						'before_each' => '<div class="item-attributes__item">',
						'after_each' => '</div>',
						'limit' => 2,
					), $post_id ); ?>
					<div class="item-attributes__item"><a href="<?php echo esc_url( $item_url ); ?>" class="item-attributes__link"><i class="fa fa-long-arrow-right"></i></a></div>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
	</div>
</div>
