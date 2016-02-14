<?php
/**
 * Content template part.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

$is_single = is_single();
$thumbnail = adventure_tours_get_the_post_thumbnail( get_the_ID(), 'thumb_single' );
$thumbnail_url = wp_get_attachment_url( get_post_thumbnail_id() );
$classWidthoutImage = empty( $thumbnail ) ? ' blog__item--without-image' : '';
$permalink = get_the_permalink();
?>
<article id="<?php echo get_post_type(); ?>-<?php the_ID(); ?>" <?php post_class( 'blog__item margin-bottom' . $classWidthoutImage ); ?> itemscope itemtype="http://schema.org/BlogPosting">
	<div class="blog__item__box">
	<?php if ( is_sticky() ) : ?>
		<div class="blog__item__sticky">
			<div class="blog__item__sticky__bg"><i class="fa fa-bookmark"></i></div>
			<div class="blog__item__sticky__content"><i class="fa fa-star"></i></div>
		</div>
	<?php endif; ?>
		<div class="blog__item__info padding-top">
		<?php if ( $is_single ) : ?>
			<h2 class="blog__item__title padding-left padding-right" itemprop="headline"><?php the_title(); ?></h2>
		<?php else : ?>
			<h2 class="blog__item__title padding-left padding-right" itemprop="headline"><a href="<?php echo esc_url( $permalink ); ?>"><?php the_title(); ?></a></h2>
		<?php endif; ?>
		<?php get_template_part( 'templates/parts/article-info' ); ?>
		<?php if ( $permalink ) { ?>
			<meta itemprop="url" content="<?php echo esc_url( $permalink ); ?>">
		<?php } ?>
		<?php if ( ! empty( $thumbnail ) ) { ?>
			<div class="blog__item__thumbnail">
			<?php if ( $thumbnail_url ) { ?>
				<meta itemprop="image" content="<?php echo esc_url( $thumbnail_url ); ?>">
			<?php } ?>
			<?php if ( $is_single ) {
				print( $thumbnail );
			} else {
				printf( '<a href="%s">%s</a>',
					esc_url( get_permalink() ),
					$thumbnail
				);
			} ?>
			</div>
		<?php } ?>
		</div>
	<?php if ( $is_single ) : ?>
		<div class="blog-single__content padding-all">
			<div itemprop="articleBody"><?php the_content(); ?></div>
			<div class="margin-top"><?php adventure_tours_render_post_pagination(); ?></div>
			<?php if ( adventure_tours_get_option( 'post_tags' ) ) {
				get_template_part( 'templates/parts/post-tags' );
			} ?>
		</div>
		<?php if ( adventure_tours_get_option( 'social_sharing_blog_single' ) ) {
			get_template_part( 'templates/parts/share-buttons' );
		} ?>
	<?php else : ?>
		<meta itemprop="description" content="<?php echo esc_attr( adventure_tours_get_short_description( null, 300 ) ); ?>">
		<div class="blog__item__content <?php echo get_the_content() ? ' padding-all' : ' padding-top'; ?>"><?php adventure_tours_the_content(); ?></div>
		<?php if ( adventure_tours_get_option( 'social_sharing_blog' ) ) {
			get_template_part( 'templates/parts/share-buttons' );
		} ?>
	<?php endif; ?>
	</div>
	<?php if ( $is_single ) : ?>
		<?php if ( adventure_tours_get_option( 'about_author' ) ) {
			get_template_part( 'templates/parts/about-author' );
		} ?>
		<?php get_template_part( 'templates/parts/post-navigation' ); ?>
		<?php comments_template(); ?>
	<?php endif; ?>
</article>
