<?php
/**
 * Gallery rendering view for default mode.
 *
 * @var string $galleryId
 * @var bool   $is_filter
 * @var bool   $is_pagination
 * @var array  $full_categories_list contains categories for all images
 * @var array  $gallery_images contains information about the picture, obtained from adventure-tours-post-gallery-filter.php
 *             Contains: link_full, link_custom_size, title, alt, (array) $categories slug => name.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

// wp_enqueue_style( 'animate' );
wp_enqueue_style( 'swipebox' );
wp_enqueue_script( 'swipebox' );

TdJsClientScript::addScript( 'initGallery_' . $galleryId, 'new Theme.Gallery("#'.$galleryId.'");' );
?>

<div class="row gallery__items">
	<?php foreach ( $gallery_images as $image ) : ?>
		<?php
		$dataFilters = '';
		$category = '';
		if ( ! empty( $image['categories'] ) ) {
			foreach ( $image['categories'] as $slug => $name ) {
				$dataFilters .= ' ' . $slug;
				$category .= ' ' . $name;
			}
		}
		?>
		<div class="col-sm-4 gallery__item-wrap" data-filterid="<?php echo esc_attr( $dataFilters ); ?>">
			<a href="<?php echo esc_url( $image['link_full'] ); ?>" class="swipebox gallery__item" title="<?php echo esc_attr( $image['title'] ); ?>">
				<img src="<?php echo esc_url( $image['link_custom_size'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>">
				<span class="gallery__item__info">
					<span class="gallery__item__info__content">
						<span class="gallery__item__title"><?php echo esc_html( $image['title'] ); ?></span>
					<?php if ( ! empty( $category ) ) { ?>
						<span class="gallery__item__delimiter"></span>
						<span class="gallery__item__description"><?php echo esc_html( $category ); ?></span>
					<?php } ?>
					</span>
					<span class="gallery__item__shadow"></span>
				</span>
			</a>
		</div>
	<?php endforeach; ?>
</div>
