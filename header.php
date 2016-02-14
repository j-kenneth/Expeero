<?php
/**
 * Header template part.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.0.9
 */

get_template_part( 'header','clean' );

$is_sticky_header = adventure_tours_get_option('sticky-header');

if ( $is_sticky_header ) {
	TdJsClientScript::addScript( 'sticky-header', 'Theme.initStickyHeader();');
	echo '<div class="header-wrap"><div class="header-wrap__backlog"></div>';
}
?>
<header class="header" role="banner">
	<div class="container">
		<?php get_template_part( 'templates/header/info' ); ?>
		<div class="header__content-wrap">
			<div class="row">
				<div class="col-md-12 header__content">
					<?php get_template_part( 'templates/header/logo' ); ?>
					<?php if ( has_nav_menu( 'header-menu' ) ) : ?>
					<nav class="main-nav-header" role="navigation">
						<?php wp_nav_menu(array(
							'theme_location' => 'header-menu',
							'container' => 'ul',
							'menu_class' => 'main-nav',
							'menu_id' => 'navigation',
							'depth' => 3,
						)); ?>
					</nav>
					<?php endif; ?>
					<div class="clearfix"></div>
				</div><!-- .header__content -->
			</div>
		</div><!-- .header__content-wrap -->
	</div><!-- .container -->
</header>
<?php if ( $is_sticky_header ) { echo '</div>'; } ?>
<?php get_template_part( 'templates/header/header-section' ); ?>
<div class="container layout-container margin-top margin-bottom">
