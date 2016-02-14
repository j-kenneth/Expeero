<?php
/**
 * Index template.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

get_header();
?>

<?php if ( is_active_sidebar( 'sidebar' ) ) : ?>
<div class="row">
	<main class="col-md-9" role="main"><?php get_template_part( 'loop' ); ?></main>
	<?php get_sidebar(); ?>
</div>
<?php else : ?>
	<?php get_template_part( 'loop' ); ?>
<?php endif; ?>

<?php get_footer(); ?>
