<?php
/**
 * Shortcode [latest_posts] view.
 * For more detailed list see list of shortcode attributes.
 *
 * @var string  $title
 * @var boolean $title_underline
 * @var string  $number
 * @var string  $translate
 * @var srting  $read_more_text
 * @var string  $words_limit
 * @var boolean $ignore_sticky_posts
 * @var string  $view
 * @var array   $items
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.2.2
 */

if ( ! $items ) {
	return '';
}

$render_limit = isset( $number ) && $number > 0 ? $number : 0;
$allow_shadow = true;
?>

<div class="last-posts">
<?php if ( $title ) {
	echo do_shortcode( '[title text="' . $title . '" size="small" position="center" decoration="on" underline="' . $title_underline . '" style="dark"]' );
} ?>
<?php foreach ( $items as $post ) : ?>
	<?php
		$image = adventure_tours_get_the_post_thumbnail( $post->ID, 'thumb_last_posts_shortcode' );
		$post_link = get_the_permalink( $post->ID );
		$post_link_escaped = esc_url( $post_link );
	?>
	<div class="last-posts__item<?php if ( $image ) echo ' last-posts__item--with-images'; ?>">
		<div class="last-posts__item__container<?php if ( $allow_shadow && $image ) echo ' last-posts__item__container--flex'; ?>">
		<?php
			if ( $image ) {
				printf( '<a href="%s" class="last-posts__item__image-wrap">%s</a>',
					$post_link_escaped,
					$image
				);
			}

			$item_content_html = sprintf(
				'<div class="last-posts__item__content">
					<h3 class="last-posts__item__title"><a href="%1$s">%2$s</a></h3>
					<div class="last-posts__item__description">%3$s</div>
					<a href="%1$s" class="atbtn atbtn--rounded atbtn--medium atbtn--light">%4$s<i class="atbtn__icon atbtn__icon--right fa fa-long-arrow-right"></i></a>
				</div>', 
				$post_link_escaped,
				esc_html( $post->post_title ),
				adventure_tours_do_excerpt( $post->post_content, $words_limit ),
				esc_html( $read_more_text )
			);
			echo $item_content_html;
		?>
		</div>
		<?php if ( $allow_shadow && $image ) {
			printf( '<div class="last-posts__item__content-flex-shadow">%s</div>', $item_content_html );
		} ?>
	</div>
	<?php if ( $render_limit > 0 && --$render_limit < 1 ) {
		break;
	} ?>
<?php endforeach; ?>
</div>
