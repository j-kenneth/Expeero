<?php
/**
 * Tour single template.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

adventure_tours_di( 'app' )->addBodyClass( 'tour-single' );

get_header( );
?>

<?php while ( have_posts() ) {
	the_post();
	get_template_part( 'templates/tour/content-single' );
} ?>

<?php get_footer(); ?>
