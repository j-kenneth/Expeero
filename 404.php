<?php
/**
 * Template page for 404 page.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.0
 */

adventure_tours_di( 'app' )->addBodyClass( 'page-404' );

get_header(); ?>

<?php get_template_part('content', 'none'); ?>

<?php get_footer(); ?>