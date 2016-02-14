<?php
/**
 * Post attributes rendering template part.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.2.1
 */

$settings = array(
	'show_date' => true,
	'show_author' => true,
	'show_categories' => true,
	'show_comments' => true,
);

$hasComments = $settings['show_comments'] && comments_open() && get_comments_number() > 0;

$authorId = $settings['show_author'] ? get_the_author_meta( 'ID' ) : null;

$categoryLinks = array();
if ( $settings['show_categories'] ) {
	$catIds = wp_get_post_categories( get_post()->ID );
	if ( $catIds ) {
		$categories = get_categories( array( 'include' => $catIds ) );
		foreach ( $categories as $category ) {
			$categoryLinks[] = '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '">' . esc_html( $category->name ) . '</a>';
		}
	}
}
?>
<ul class="article-info padding-left padding-right">
	<?php if ( $settings['show_date'] ) { ?><li><i class="fa fa-calendar"></i><a href="<?php the_permalink(); ?>"><time datetime="<?php the_time('Y-m-d') ?>" itemprop="datePublished"><?php echo get_the_date(); ?></time></a></li><?php } ?>
	<?php if ( $authorId ) { ?><li><i class="fa fa-user"></i><a href="<?php echo esc_url( get_author_posts_url( $authorId ) ) ?>"><span itemprop="author" itemscope itemtype="http://schema.org/Person"><span itemprop="name"><?php echo get_the_author(); ?></span></span></a></li><?php } ?>
	<?php if ( $categoryLinks ) { ?><li><i class="fa fa-pencil-square-o"></i><span itemprop="articleSection"><?php echo join( ', ', $categoryLinks ); ?></span></li><?php } ?>
	<?php if ( $hasComments ) { ?><li><i class="fa fa-comments-o"></i><?php comments_popup_link( esc_html__( 'No Comments', 'adventure-tours' ), esc_html__( '1 Comment', 'adventure-tours' ), esc_html__( '% Comments', 'adventure-tours' ) ); ?></li><?php } ?>
</ul>
