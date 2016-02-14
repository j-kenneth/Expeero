<?php
/**
 * Template Name: Page with sidebar
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.2.0
 */

get_header();
?>

<?php if ( have_posts() ) : ?>
	<?php while ( have_posts() ) { the_post(); ?>
	<div class="page-single row">
		<main class="page-single__content col-md-9" role="main">
			<?php print adventure_tours_get_the_post_thumbnail( null, 'thumb_single' ); ?>
			<div class="section-white-box padding-all"><?php the_content(); ?></div>
		</main>
		<?php get_sidebar(); ?>
	</div>
	<?php } ?>
<?php else : ?>
	<?php get_template_part( 'content', 'none' ); ?>
<?php endif; ?>

<?php get_footer(); ?>
