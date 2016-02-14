<?php
/**
 * Widget recent reviews view.
 *
 * @var array $args
 * @var array $instance
 * @var array $comments
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */
?>

<?php
if ( ! $comments ) {
	return;
}

global $comment;

print $args['before_widget'];

if ( ! empty( $instance['title'] ) ) {
	printf( '%s%s%s', $args['before_title'], esc_html( $instance['title'] ), $args['after_title'] );
}
?>
<ul class="product_list_widget product_list_widget--recent-reviews">
<?php foreach ( (array) $comments as $comment ) : ?>
	<?php $_product = wc_get_product( $comment->comment_post_ID ); ?>
	<li class="product_list_widget__item">
		<div class="product_list_widget__item__image"><?php print $_product->get_image(); ?></div>
		<div class="product_list_widget__item__content">
			<div class="product_list_widget__item__title">
				<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
					<?php echo esc_html( $_product->get_title() ); ?>
				</a>
			</div>
			<?php
				$rating = intval( get_comment_meta( $comment->comment_ID, 'rating', true ) );
				adventure_tours_renders_stars_rating( $rating, array(
					'before' => '<div class="product_list_widget__item__rating">',
					'after' => '</div>',
				) );
			?>
			<div><?php echo _x( 'by ' . get_comment_author(), 'by comment author', 'adventure-tours' ); ?></div>
		</div>
	</li>
<?php endforeach; ?>
</ul>

<?php print $args['after_widget']; ?>