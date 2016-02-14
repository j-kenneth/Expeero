<?php
/**
 * Template Name: FAQ
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.1.2
 */

get_header();

$cat_tax_name = 'faq_category';
$mode_all = false;
$cat_taxonomies = array();

TdJsClientScript::addScript( 'faqAccorsionChagesIconInit', 'Theme.faqAccordionCahgesIcon();' );

$is_sidebar = is_active_sidebar( 'faq-sidebar' );
$show_question_form = adventure_tours_get_option( 'faq_show_question_form' );
$is_show_col = ( $is_sidebar || $show_question_form );
$accordion_id = 1;
$accordion_item_id = 1;

if ( adventure_tours_check( 'faq_taxonomies' ) ) {
	if ( is_tax( $cat_tax_name ) ) {
		// query for particular category
		$cur_category = get_queried_object();
		if ( $cur_category ) {
			$cat_taxonomies[] = $cur_category;
		}
	} elseif ( have_posts() ) {
		// page template is used
		$mode_all = true;
		the_post();
		$cat_taxonomies = get_terms( $cat_tax_name );
	}

	$cat_ids = array();
	if ( $cat_taxonomies ) {
		foreach ( $cat_taxonomies as $category ) {
			$cat_ids[] = $category->term_id;
		}
	} elseif ( $mode_all ) {
		$uncategorized = new stdClass();
		$uncategorized->slug = 'uncategorized';
		array_unshift( $cat_taxonomies, $uncategorized );
	}
}
?>

<?php if ( ! empty( $cat_taxonomies )  ) : ?>
	<div class="row faq">
		<main class="<?php echo ($is_show_col) ? 'col-md-9' : 'col-md-12'; ?>" role="main">
		<?php
			foreach ( $cat_taxonomies as $category ) :
				$tax_query = array();
				if ( 'uncategorized' == $category->slug ) {
					$tax_query = array(
						'taxonomy' => $cat_tax_name,
						'field' => 'id',
						'terms' => $cat_ids,
						'operator' => 'NOT IN',
					);
				} else {
					$tax_query = array(
						'taxonomy' => $cat_tax_name,
						'field' => 'slug',
						'terms' => $category->slug,
					);
				}

				$query = new WP_Query(array(
					'post_type' => 'faq',
					'posts_per_page' => -1,
					'tax_query' => array( $tax_query ),
					'orderby' => 'menu_order', // comment this out to order items by a publish date
					// 'order' => 'ASC', // uncomment this to reverse order
				));
				$posts = $query->get_posts();

				if ( empty( $posts ) ) {
					if ( $mode_all ) {
						continue;
					}
				}
			?>

			<div class="faq__item">
				<?php printf( '<a name="%s"></a>', esc_attr( $category->slug ) ); ?>
				<?php if ( isset( $category->name ) ) { ?>
					<div class="section-title title title--small title--center title--decoration-bottom-center title--underline">
						<h2 class="title__primary"><?php echo esc_html( $category->name ); ?></h2>
					</div>
				<?php } ?>
				<div class="padding-left padding-right">
					<div class="panel-group faq__accordion" id="faq-accordion<?php echo esc_attr( $accordion_id ); ?>">
						<?php foreach ( $posts as $post ) { ?>
							<div class="panel faq__accordion__item">
								<div class="faq__accordion__heading">
									<i class="fa"></i>
									<a class="faq__accordion__title" data-toggle="collapse" data-parent="#faq-accordion<?php echo esc_attr( $accordion_id ); ?>" href="#faq-accrodiotn-item<?php echo esc_attr( $accordion_item_id ); ?>"><?php echo esc_html( $post->post_title ); ?></a>
								</div>
								<div id="faq-accrodiotn-item<?php echo esc_attr( $accordion_item_id ); ?>" class="collapse faq__accordion__content-wrap">
									<div class="faq__accordion__content"><?php echo apply_filters( 'the_content', $post->post_content ); ?></div>
								</div>
							</div>
						<?php $accordion_item_id++; } ?>
					</div>
				</div>
			</div>
			<?php $accordion_id++;
			endforeach; ?>
		</main>
	<?php if ( $is_show_col ) : ?>
		<aside class="col-md-3 sidebar" role="complementary">
			<?php if ( $show_question_form ) { get_template_part( 'templates/parts/faq-question-form' ); } ?>
			<?php if ( $is_sidebar ) { dynamic_sidebar( 'faq-sidebar' ); } ?>
		</aside>
	<?php endif; ?>
	</div>
<?php else : ?>
	<?php get_template_part( 'content', 'none' ); ?>
<?php endif; ?>

<?php get_footer(); ?>
