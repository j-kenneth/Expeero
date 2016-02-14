<?php
/**
 * Functions related to different part rendering.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.3.2
 */

// -----------------------------------------------------------------#
// Rendering: helper functions
// -----------------------------------------------------------------#
if ( ! function_exists( 'adventure_tours_get_the_post_thumbnail' ) ) {
	/**
	 * Returns post featured image in requested size.
	 * Returns grey image in case if after import attachments have not been downloaded.
	 *
	 * @param  int    $postId             post id.
	 * @param  string $size               code of the image size.
	 * @param  string $attributes         attributes that should be applied for the img tag.
	 * @param  array  $undefinedSize      size of the image that should be returned (used if $size is undefined).
	 * @return string
	 */
	function adventure_tours_get_the_post_thumbnail($postId = null, $size = 'full', array $attributes = array(), array $undefinedSize = array( 770, 514 )) {
		$result = '';

		if ( null === $postId ) {
			$postId = get_the_ID();
		}

		if ( ! has_post_thumbnail( $postId ) ) {
			return $result;
		}

		if ( $postId ) {
			$result = get_the_post_thumbnail( $postId, $size, $attributes );
		}

		if ( ! $result ) {
			$imageManger = adventure_tours_di( 'image_manager' );
			if ( ! $imageManger ) {
				return $result;
			}

			$sizeDetails = $imageManger->getImageSizeDetails( $size );
			if ( $sizeDetails ) {
				$width = isset( $sizeDetails['width'] ) ? $sizeDetails['width'] : null;
				$height = isset( $sizeDetails['height'] ) ? $sizeDetails['height'] : null;

				if ( $height > $width * 5 ) {
					$height = round( $width * 1.5 );
				}
			} else {
				$width = isset( $undefinedSize[0] ) ? $undefinedSize[0] : null;
				$height = isset( $undefinedSize[1] ) ? $undefinedSize[1] : null;
			}

			if ( $width && $height ) {
				$result = $imageManger->getPlaceholdImage( $width, $height, '', true, $attributes );
			}
			// Else throw new Exception("Image size {$size} not defined."); .
		}

		return $result;
	}
}

if ( ! function_exists( 'adventure_tours_placeholder_img_src' ) ) {
	/**
	 * Returns url to the placeholder image saved in theme options.
	 *
	 * @param  string $size image size code.
	 * @return string
	 */
	function adventure_tours_placeholder_img_src( $size = 'large' ) {
		static $url;
		if ( null === $url ) {
			$url = adventure_tours_get_option( 'placeholder_image', '' );
			if ( ! $url ) {
				$url = get_template_directory_uri() . '/assets/images/placeholder.png';
			}
		}

		return $url;
	}
}

if ( ! function_exists( 'adventure_tours_placeholder_img' ) ) {
	/**
	 * Returns html with img that renders placeholder image.
	 *
	 * @param  string $size image size code.
	 * @return string
	 */
	function adventure_tours_placeholder_img( $size = 'large' ) {
		$dimensions = adventure_tours_di( 'image_manager' )->getImageSizeDetails( $size );
		if ( ! $dimensions ) {
			$dimensions = adventure_tours_di( 'image_manager' )->getImageSizeDetails( 'large' );
		}

		return apply_filters( 'adventure_tours_placeholder_img', '<img src="' . adventure_tours_placeholder_img_src( $size ) . '" alt="' . esc_attr__( 'Placeholder', 'adventure-tours' ) . '" width="' . esc_attr( $dimensions['width'] ) . '" class="woocommerce-placeholder wp-post-image" height="' . esc_attr( $dimensions['height'] ) . '" />', $size, $dimensions );
	}
}

if ( ! function_exists( 'adventure_tours_render_template_part' ) ) {
	/**
	 * Analog for the get_template_part function.
	 * Allows pass params to view file.
	 *
	 * @param  string  $templateName    view name.
	 * @param  string  $templatePostfix optional postfix.
	 * @param  array   $data            assoc array with variables that should be passed to view.
	 * @param  boolean $return          if result should be returned instead of outputting.
	 * @return string
	 */
	function adventure_tours_render_template_part($templateName, $templatePostfix = '', array $data = array(), $return = false) {
		static $app;
		if ( ! $app ) {
			$app = adventure_tours_di( 'app' );
		}
		return $app->renderTemplatePart( $templateName, $templatePostfix, $data, $return );
	}
}

if ( ! function_exists( 'adventure_tours_the_content' ) ) {
	/**
	 * Determines what function should be used for content section rendering the_excerpt or
	 * the_content based on 'is_excerpt' theme option value.
	 *
	 * @return void
	 */
	function adventure_tours_the_content() {
		if ( adventure_tours_get_option( 'is_excerpt' ) ) {
			the_excerpt();
		} else {
			the_content( adventure_tours_get_option( 'excerpt_text' ) );
		}
	}
}

if ( ! function_exists( 'adventure_tours_wp_title_filter' ) ) {
	/**
	 * Title rendering filter function.
	 *
	 * @param  string $title
	 * @param  string $sep
	 * @param  string $seplocation
	 * @return string
	 */
	function adventure_tours_wp_title_filter( $title, $sep, $seplocation ) {
		if ( is_feed() ) {
			return $title;
		}

		if ( ! $title) {
			$title = get_bloginfo('name', 'display') . ( $sep && 'right' == $seplocation ? " $sep " : '' );
		}

		if (!$sep) {
			return trim($title);
		}

		global $page, $paged;
		$fullSep = $sep ? " $sep " : ' ';
		$firstSep = 'right' == $seplocation ? '' : $fullSep;

		if ( ( is_home() || is_front_page() ) && ($site_description = get_bloginfo( 'description', 'display' ))) {
			$title .= $firstSep . $site_description;
		} else {
			$title .= $firstSep . get_bloginfo( 'name', 'display' );
		}

		// Add a page number if necessary:
		if ( ( $paged > 1 || $page > 1 ) && ! is_404() ) {
			$title .= $fullSep . sprintf( esc_html__( 'Page %s', 'adventure-tours' ), max( $paged, $page ) );
		}

		return $title;
	}
	// add_filter( 'wp_title', 'adventure_tours_wp_title_filter', 10, 3 );
}

// -----------------------------------------------------------------#
// Rendering: excerpt
// -----------------------------------------------------------------#
if ( ! function_exists( 'adventure_tours_do_excerpt' ) ) {
	/**
	 * Custom excerpt text generation function.
	 *
	 * @param  string $string     text that should be truncated.
	 * @param  int    $word_limit max allowed number of words.
	 * @return string
	 */
	function adventure_tours_do_excerpt( $string, $word_limit ) {
		if ( $string ) {
			if ( '0' === $word_limit || 0 === $word_limit ) {
				return '';
			}
			$string = strip_shortcodes( $string );
			$string = $word_limit > 0 ? wp_trim_words( $string, $word_limit, '') : wp_strip_all_tags( $string );
		}

		return $string;
	}
}
if ( ! function_exists( 'adventure_tours_get_short_description' ) ) {
	/**
	 * Returns short description for current post or for the specefied post.
	 *
	 * @param  WP_Post $forPost    optional post object (if empty - current post will be used).
	 * @param  int     $word_limit max allowed words count.
	 * @return string
	 */
	function adventure_tours_get_short_description( $forPost = null, $word_limit = null ) {
		if ( null === $forPost ) {
			$forPost = get_post();
		}

		if ( ! $forPost ) {
			return '';
		}

		$text = $forPost->post_excerpt ? $forPost->post_excerpt : $forPost->post_content;
		if ( $text ) {
			return adventure_tours_do_excerpt( $text, $word_limit );
		} else {
			return $text;
		}
	}
}

if ( ! function_exists( 'adventure_tours_excerpt_more_link' ) ) {
	/**
	 * Filter for formatting excerpt more link.
	 * Depends on theme options.
	 *
	 * @return string
	 */
	function adventure_tours_excerpt_more_link() {
		if ( adventure_tours_di( 'register' )->getVar( 'disable_excerpt_more_link' ) ) {
			return '';
		}
		static $moreText;
		if ( null === $moreText ) {
			$moreText = esc_html( adventure_tours_get_option( 'excerpt_text' ) );
		}

		return sprintf(
			'<div class="padding-top text-center"><a href="%s" class="atbtn atbtn--medium atbtn--rounded atbtn--transparent">%s<i class="atbtn__icon atbtn__icon--right fa fa-long-arrow-right"></i></a></div>',
			esc_url( get_permalink() ),
			esc_html( $moreText )
		);
	}
	add_filter( 'excerpt_more', 'adventure_tours_excerpt_more_link', 9, 1 );
}

if ( ! function_exists( 'adventure_tours_custom_excerpt_length' ) ) {
	/**
	 * Filter for excerpt_length hook.
	 *
	 * @param  int $length current length value.
	 * @return int
	 */
	function adventure_tours_custom_excerpt_length($length) {
		return adventure_tours_get_option( 'excerpt_length' );
	}
	add_filter( 'excerpt_length', 'adventure_tours_custom_excerpt_length', 999 );
}

if ( ! function_exists( 'adventure_tours_content_more_link_filter' ) ) {
	/**
	 * Filter for content more link text.
	 *
	 * @param  string $link     link html.
	 * @param  string $linkText text.
	 * @return string
	 */
	function adventure_tours_content_more_link_filter($link, $linkText = '') {
		if ( ! $link ) {
			return '';
		}

		static $moreText;
		if ( null === $moreText ) {
			$moreText = esc_html( adventure_tours_get_option( 'excerpt_text' ) );
		}

		return sprintf(
			'<div class="padding-top text-center"><a href="%s" class="atbtn atbtn--medium atbtn--rounded atbtn--transparent">%s<i class="atbtn__icon atbtn__icon--right fa fa-long-arrow-right"></i></a></div>',
			esc_url( get_permalink() ),
			esc_html( $moreText )
		);
	}
	add_filter( 'the_content_more_link', 'adventure_tours_content_more_link_filter', 10, 2 );
}

if ( ! function_exists( 'adventure_tours_fix_broken_p' ) ) {
	/**
	 * Removes and fixes broken P tags.
	 *
	 * @param  string $content
	 * @return string
	 */
	function adventure_tours_fix_broken_p( $content ) {
		// to prevent processing content that contains revslider, as force_balance_tags will broke revslider javascript
		if ( strpos( $content, '<script' ) !== false || strpos( $content, 'class="rev_slider' ) !== false ) {
			return $content;
		}

		// removing broken P tags added by wpuatop
		$result = preg_replace(
			array(
				'`<p>\s*<div([^>]*)>(?:\s*</p>)?|<div([^>]*)>\s*</p>`', // <p><div></p>
				'`(<p>\s*)?</div>\s*</p>|<p>\s*</div>`', // <p></div></p>
			),
			array(
				'<div$1$2>',
				'</div>',
			),
			$content
		);
		// fixing unclosed/unopened P tags
		return force_balance_tags( $result );
	}
	add_filter( 'the_content', 'adventure_tours_fix_broken_p', 11, 1 );
}

if ( ! function_exists( 'adventure_tours_esc_text' ) ) {
	/**
	 * Escapes multi line text.
	 *
	 * @param  string  $text
	 * @param  string  $context      name of the context
	 * @param  boolean $is_mutliline is multi line text
	 * @return string
	 */
	function adventure_tours_esc_text( $text, $context = 'post', $is_mutliline = false ) {
		$result = '';
		if ($text) {
			$tagsWhiteList = wp_kses_allowed_html( $context );
			$result = wp_kses( $text, $tagsWhiteList );
			if ($is_mutliline) {
				$result = nl2br( $result );
			}
		}
		return $result ? force_balance_tags( $result ) : '';
	}
}

if ( ! function_exists( 'adventure_tours_kses_allowed_html_filter' ) ) {
	/**
	 * Applies specific tags settings based on context.
	 *
	 * @param  assoc  $tags    list of allowed tags.
	 * @param  string $context text content id.
	 * @return assoc
	 */
	function adventure_tours_kses_allowed_html_filter( $tags, $context = '' ) {
		switch ( $context ) {
		case 'option_input':
			$tags['span'] = array(
				'class' => true,
				'id' => true,
			);
			$tags['i'] = array(
				'class' => true,
				'id' => true,
			);
			$tags['a'] = array(
				'title' => true,
				'href' => true,
				'target' => true,
				'class' => true,
				'id' => true,
			);
			break;
		}
		return $tags;
	}
	add_filter( 'wp_kses_allowed_html', 'adventure_tours_kses_allowed_html_filter', 10, 2 );
}

// -----------------------------------------------------------------#
// Renderind: paginations
// -----------------------------------------------------------------#
if ( ! function_exists( 'adventure_tours_render_pagination' ) ) {
	/**
	 * Renders posts pagination.
	 *
	 * @param  string   $before prefix text.
	 * @param  string   $after  postfix text.
	 * @param  WP_Query $query  query.
	 * @return void
	 */
	function adventure_tours_render_pagination( $before = '', $after = '', $query = null ) {
		global $wp_query;
		if ( ! $query ) {
			$query = $wp_query;
		}

		if ( $query->max_num_pages <= 1 ) {
			return '';
		}

		$big = 999999999;
		$current = max( 1, get_query_var( 'paged' ) );
		$prev_link_text = '<i class="fa fa-angle-left"></i>';
		$next_link_text = '<i class="fa fa-angle-right"></i>';

		$paginate = paginate_links( array(
			'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'type' => 'array',
			'total' => $query->max_num_pages,
			'format' => '?paged=%#%',
			'mid_size' => 1,
			'current' => $current,
			'prev_text' => $prev_link_text,
			'next_text' => $next_link_text,
		) );

		$pages_html = '';
		$prev_link = '<div class="navigation__item navigation__prev navigation__item--disable"><span class="prev page-numbers">' . $prev_link_text . '</span></div>';
		$next_link = '<div class="navigation__item navigation__next navigation__item--disable"><span class="next page-numbers">' . $next_link_text . '</span></div>';

		foreach ( $paginate as $index => $page ) {
			if ( preg_match( '/class="prev page-numbers"/', $page ) ) {
				$prev_link = '<div class="navigation__item navigation__prev">' . $page . '</div>';
				continue;
			}

			if ( preg_match( '/class="next page-numbers"/', $page ) ) {
				$next_link = '<div class="navigation__item navigation__next">' . $page . '</div>';
				continue;
			}

			$pages_html .= '<div class="navigation__item">' . $page . '</div>';
		}

		printf(
			 '%s<div class="navigation">' .
				'<div class="navigation__content">%s<div class="navigation__items">%s</div>%s</div>' .
			'</div>%s',
			$before,
			$prev_link,
			$pages_html,
			$next_link,
			$after
		);
	}
}

if ( ! function_exists( 'adventure_tours_render_post_pagination' ) ) {
	/**
	 * Renders pagination for post pages.
	 *
	 * @return void
	 */
	function adventure_tours_render_post_pagination() {
		global $page, $numpages;
		if ( $numpages < 2 ) {
			return '';
		}

		$res = wp_link_pages(array(
			'before' => '',
			'after' => '',
			'separator' => "\n",
			'next_or_number' => 'number',
			'echo' => false,
		));

		$parts = explode( "\n", $res );
		if ( count( $parts ) < 2 ) {
			return '';
		}

		$activeIndex = $page -1;
		$itemsHtml = '';

		foreach ( $parts as $index => $itemHtml ) {
			if ( $index === $activeIndex ) {
				$itemsHtml .= '<div class="post-page-navigation__item"><span class="current">' . $page . '</span></div>';
			} else {
				$itemsHtml .= '<div class="post-page-navigation__item">' . $itemHtml . '</div>';
			}
		}

		$prev = $page - 1;
		$prevText = esc_html__( 'Previous', 'adventure-tours' );
		if ( $prev ) {
			$linkPrev = '<div class="post-page-navigation__item post-page-navigation__prev">' . _wp_link_page( $prev ) . $prevText . '</a></div>';
		} else {
			$linkPrev = '<div class="post-page-navigation__item post-page-navigation__item--disable post-page-navigation__prev"><span>' . $prevText . '</span></div>';
		}

		$next = $page + 1;
		$nextText = esc_html__( 'Next', 'adventure-tours' );
		if ( $next <= $numpages ) {
			$linkNext = '<div class="post-page-navigation__item post-page-navigation__next">' . _wp_link_page( $next ) . $nextText . '</a></div>';
		} else {
			$linkNext = '<div class="post-page-navigation__item post-page-navigation__item--disable post-page-navigation__next"><span>' . $nextText . '</span></div>';
		}

		printf(
			'<div class="post-page-navigation">' .
				'%s<div class="post-page-navigation__items">%s</div>%s' .
			'</div>',
			$linkPrev,
			$itemsHtml,
			$linkNext
		);
	}
}

if ( ! function_exists( 'adventure_tours_comments_pagination' ) ) {
	/**
	 * Comments pagination functionality.
	 *
	 * @return string
	 */
	function adventure_tours_comments_pagination() {
		$numpages = get_option( 'page_comments' ) ? get_comment_pages_count() : 0;
		if ( $numpages <= 1 ) {
			return '';
		}

		$prev_next_link_title = 'prev_next_link';
		$paginationLinks = paginate_comments_links(array(
			'show_all' => true,
			'type' => 'array',
			'echo' => false,
			'prev_text' => $prev_next_link_title,
			'next_text' => $prev_next_link_title,
		));

		$linksHtml = '';
		foreach ( $paginationLinks as $link ) {
			if ( false === strpos( $link, $prev_next_link_title ) ) {
				$linksHtml .= '<div class="comments__navigation__item">' . $link . '</div>';
			}
		}

		$nextLink = get_next_comments_link( esc_html__( 'next', 'adventure-tours' ) );
		$prevLink = get_previous_comments_link( esc_html__( 'previous', 'adventure-tours' ) );

		$nextLink = ( ! empty( $nextLink ))
		?
		'<div class="comments__navigation__item comments__navigation__next">' . $nextLink . '</div>'
				:
				'<div class="comments__navigation__item comments__navigation__item--disable comments__navigation__next"><span>' . esc_html__( 'next', 'adventure-tours' ) . '</span></div>';

		$prevLink = ( ! empty( $prevLink ))
		?
		'<div class="comments__navigation__item comments__navigation__prev">' . $prevLink . '</div>'
				:
				'<div class="comments__navigation__item comments__navigation__item--disable comments__navigation__prev"><span>' . esc_html__( 'previous', 'adventure-tours' ) . '</span></div>';

		printf( 
			'<div class="comments__navigation">' .
				'<div class="comments__navigation__content padding-left padding-right">' .
					'%s<div class="comments__navigation__items">%s</div>%s' .
				'</div>' .
			'</div>',
			$prevLink,
			$linksHtml,
			$nextLink
		);
	}
}

if ( ! function_exists( 'adventure_tours_render_tour_search_form' ) ) {
	/**
	 * Renders tours search form.
	 *
	 * @param  boolean $allow_cache
	 * @return string
	 */
	function adventure_tours_render_tour_search_form( $allow_cache = true ) {
		static $cache;

		if ( ! $allow_cache || null === $cache ) {
			$title = adventure_tours_get_option( 'tours_search_form_title' );
			$note = adventure_tours_get_option( 'tours_search_form_note' );

			$cache = do_shortcode( sprintf('[tour_search_form title="%s" note="%s"]',
				$title,
				$note
			) );
		}

		return $cache;
	}
}

if ( ! function_exists( 'adventure_tours_render_tour_booking_form' ) ) {
	/**
	 * Renders tours booking form.
	 *
	 * @param  WC_Product_Tour $product
	 * @return string
	 */
	function adventure_tours_render_tour_booking_form( $product = null ) {
		$booking_form = adventure_tours_di( 'booking_form' );
		if ( $booking_form ) {
			return $booking_form->render( $product );
		}
		return '';
	}
}

if ( ! function_exists( 'adventure_tours_render_tour_categories' ) ) {
	/**
	 * Renders list of tour categories related to current tour category (if page is taxonomy) or top level tour categories.
	 *
	 * @param  array $args assoc that contains rendering settings.
	 * @return void
	 */
	function adventure_tours_render_tour_categories( $args = array() ) {
		if ( ! adventure_tours_check( 'tour_category_taxonomy_exists' ) ) {
			return;
		}

		$args = wp_parse_args( $args, array(
			'before' => '',
			'after' => '',
		) );

		$term = get_queried_object();
		$parent_id = empty( $term->term_id ) ? 0 : $term->term_id;

		$product_categories = get_categories( apply_filters( 'woocommerce_product_subcategories_args', array(
			'parent'       => $parent_id,
			'menu_order'   => 'ASC',
			'hide_empty'   => 0,
			'hierarchical' => 1,
			'taxonomy'     => 'tour_category',
			'pad_counts'   => 1,
		) ) );

		$product_categories = wp_list_filter( $product_categories, array( 'count' => 0 ), 'NOT' );

		if ( $product_categories ) {
			$items_html = '';
			foreach ( $product_categories as $category ) {
				$items_html .= adventure_tours_render_template_part( 'templates/tour/content-tour_category', '', array(
					'category' => $category,
				), true );
			}

			printf(
				'%s%s%s',
				$args['before'],
				$items_html,
				$args['after']
			);
		}
	}
}

if ( ! function_exists( 'adventure_tours_render_category_thumbnail' ) ) {
	/**
	 * Show subcategory thumbnail.
	 *
	 * @param mixed $category
	 * @subpackage	Loop
	 */
	function adventure_tours_render_category_thumbnail( $category ) {
		$small_thumbnail_size = apply_filters( 'single_product_small_thumbnail_size', 'shop_catalog' );
		$dimensions = wc_get_image_size( $small_thumbnail_size );
		$thumbnail_id = AtTourHelper::get_tour_category_thumbnail( $category );
		$image = null;

		if ( $thumbnail_id ) {
			$image = wp_get_attachment_image_src( $thumbnail_id, $small_thumbnail_size );
			if ( $image ) {
				$image = $image[0];
			}
		}

		if ( ! $image ) {
			$image = wc_placeholder_img_src();
		}

		if ( $image ) {
			// Prevent esc_url from breaking spaces in urls for image embeds
			// Ref: http://core.trac.wordpress.org/ticket/23605
			$image = str_replace( ' ', '%20', $image );

			echo '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( $category->name ) . '" width="' . esc_attr( $dimensions['width'] ) . '" height="' . esc_attr( $dimensions['height'] ) . '" />';
		}
	}
}

if ( ! function_exists( 'adventure_tours_render_tour_icons' ) ) {
	/**
	 * Renders tour categories related to current tour item.
	 *
	 * @param  array $args   assoc that contains rendering settings.
	 * @param  int   $postId optional post id.
	 * @return void
	 */
	function adventure_tours_render_tour_icons( $args = array(), $postId = null ) {
		if ( ! adventure_tours_check( 'tour_category_taxonomy_exists' ) ) {
			return;
		}

		$defaults = array(
			'before' => '',
			'after' => '',
		);

		$args = wp_parse_args( $args, $defaults );

		if ( null === $postId ) {
			$postId = get_the_ID();
		}

		$items = $postId ? wp_get_object_terms( $postId, 'tour_category' ) : null;
		if ( ! $items ) {
			return;
		}

		$items_html = '';
		foreach ( $items as $tourType ) {
			$icon_class = AtTourHelper::get_tour_category_icon_class( $tourType );
			if ( $icon_class ) {
				$items_html .= sprintf('<a href="%s"><i data-toggle="tooltip" title="%s" class="%s"></i></a>',
					esc_url( get_term_link( $tourType->slug, 'tour_category' ) ),
					esc_attr( $tourType->name ),
					esc_attr( $icon_class )
				);
			}
		}

		if ( $items_html ) {
			printf( '%s%s%s',
				$args['before'],
				$items_html,
				$args['after']
			);
		}
	}
}

if ( ! function_exists( 'adventure_tours_render_product_attributes' ) ) {
	/**
	 * Renders product attributes on archive page.
	 *
	 * @param  array $args assoc that contains rendering settings.
	 * @param  int   $postId optional post id.
	 * @return void
	 */
	function adventure_tours_render_product_attributes( $args = array(), $postId = null ) {
		global $product;

		$curProduct = $postId ? wc_get_product( $postId ) : $product;

		$list = AtTourHelper::get_tour_details_attributes( $curProduct, true );
		if ( ! $list ) {
			return;
		}

		$defaults = array(
			'before' => '',
			'after' => '',
			'before_each' => '',
			'after_each' => '',
			'limit' => 5,
		);

		$args = wp_parse_args( $args, $defaults );

		$items_html = '';
		foreach ( $list as $attribute ) {
			$items_html .= $args['before_each'] .
				'<div class="item-attributes__item__content">' .
					( $attribute['icon_class'] ? '<div class="item-attributes__item__content__item"><i class="' . esc_attr( $attribute['icon_class'] ) . '"></i></div>' : '' ) .
					'<div class="item-attributes__item__content__item item-attributes__item__content__item--text"><span>' . $attribute['text'] . '</span></div>' .
				'</div>' .
			$args['after_each'];

			if ( $args['limit'] > 0 ) {
				$args['limit']--;
				if ( $args['limit'] < 1 ) {
					break;
				}
			}
		}

		printf( '%s%s%s',
			$args['before'],
			$items_html,
			$args['after']
		);
	}
}

if ( ! function_exists( 'adventure_tours_post_gallery_filter' ) ) {
	/**
	 * Filter used to customize galleries output.
	 *
	 * @param  string $empty
	 * @param  assoc  $attr gallery shortcode attributes
	 * @return string
	 */
	function adventure_tours_post_gallery_filter( $empty, $attr ) {
		global $post;

		extract( shortcode_atts( array(
			'order' => 'ASC',
			'orderby' => 'menu_order ID',
			'id' => $post ? $post->ID : 0,
			'include' => '',
			'exclude' => '',
			// custom attributes
			'layout' => 'default',
			'pagination' => '',
			'filter' => '',
			'single_page' => '',
		), $attr, 'gallery' ) );

		// get attachments set
		$queryArgs = array(
			'post_status' => 'inherit',
			'post_type' => 'attachment',
			'post_mime_type' => 'image',
			'order' => $order,
			'orderby' => $orderby,
		);

		$id = intval( $id );

		if ( $include ) {
			$queryArgs['include'] = $include;
		} else {
			$queryArgs['post_parent'] = $id;
			if ( $exclude ) {
				$queryArgs['exclude'] = $exclude;
			}
		}

		$attachments = get_posts( $queryArgs );
		if ( ! $attachments ) {
			if ( $include && ! is_feed() ) {
				// allow to render gallery with placeholders (for galleries that have been imported with content)
			} else {
				return '';
			}
		}
		// For RSS
		if ( is_feed() ) {
			$output = "\n";
			foreach ( $attachments as $attachment ) {
				$output .= wp_get_attachment_link( $attachment->ID ) . "\n";
			}
			return $output;
		}

		$defaultThumbSize = 'thumb_gallery';
		$defaultFullSize = 'full';

		$galleryLayouts = array(
			'default' => array(
				'showCategories' => true,
				'allowPagination' => true,
				'thumbSize' => $defaultThumbSize,
			),
		);

		if ( ! $layout || ! isset( $galleryLayouts[$layout] ) ) {
			$layout = 'default';
		}

		$layoutConfig = $galleryLayouts[$layout];
		$thumbSize = isset( $layoutConfig['thumbSize'] ) ? $layoutConfig['thumbSize'] : $defaultThumbSize;
		$fullSize = isset( $layoutConfig['fullSize'] ) ? $layoutConfig['fullSize'] : $defaultFullSize;

		$showCategories = ! empty( $layoutConfig['showCategories'] ) && adventure_tours_check( 'media_category_taxonomy_exists' );
		$is_filter = adventure_tours_di( 'shortcodes_helper' )->attribute_is_true( $filter );
		if ( $is_filter && ! $showCategories ) {
			$is_filter = false;
		}

		$is_pagination = adventure_tours_di( 'shortcodes_helper' )->attribute_is_true( $pagination );
		if ( empty( $layoutConfig['allowPagination'] ) && $is_pagination ) {
			$is_pagination = false;
		}

		$gallery_images = array();
		$full_categories_list = array();
		if ( $attachments ) {
			foreach ( $attachments as $attachment ) {
				$idAttch = $attachment->ID;

				// Get image link to a specific sizes
				// Image attribute [0] => url [1] => width [2] => height
				$image_attributes_full = wp_get_attachment_image_src( $idAttch, $fullSize );
				$image_attributes_custom_size = wp_get_attachment_image_src( $idAttch, $thumbSize );
				$link_full = ! empty( $image_attributes_full[0] ) ? $image_attributes_full[0] : '';
				$link_custom_size = ! empty( $image_attributes_custom_size[0] ) ? $image_attributes_custom_size[0] : '';

				// categories
				$image_categories = array();
				if ( $showCategories ) {
					$taxonomies = get_the_terms( $idAttch, 'media_category' ); // 'category'
					if ( $taxonomies ) {
						foreach ( $taxonomies as $taxonomy ) {
							$full_categories_list[$taxonomy->slug] = $taxonomy->name;
							$image_categories[$taxonomy->slug] = $taxonomy->name;
						}
					}
				}

				$alt = get_post_meta( $idAttch, '_wp_attachment_image_alt', true );

				$gallery_images[] = array(
					'link_full' => $link_full,
					'link_custom_size' => $link_custom_size,
					'title' => $attachment->post_title,
					'categories' => $image_categories,
					'alt' => $alt ? $alt : $attachment->post_title,
				);
			}
		} elseif ( $include ) {
			$imageManager = adventure_tours_di( 'image_manager' );
			$fullSizeDetails = $imageManager->getImageSizeDetails( $fullSize == 'full' ? 'large' : $fullSize );
			$thumbSizeDetails = $imageManager->getImageSizeDetails( $thumbSize );

			$includeIds = explode( ',', trim( $include,', ' ) );
			foreach ( $includeIds as $attachemntId ) {
				$dummyTitle = '#' . $attachemntId;
				$placeholdText = urlencode( $dummyTitle ); // find why additional encode is required

				$placeholdThumbUrl = $imageManager->getPlaceholdImage( $thumbSizeDetails['width'], $thumbSizeDetails['height'], $placeholdText );

				$fullImageUrl = $fullSizeDetails
					? $imageManager->getPlaceholdImage( $fullSizeDetails['width'], $fullSizeDetails['height'], $placeholdText )
					: $placeholdThumbUrl;

				$gallery_images[] = array(
					'link_full' => $fullImageUrl,
					'link_custom_size' => $placeholdThumbUrl,
					'title' => $dummyTitle,
					'categories' => array(),
					'alt' => $dummyTitle,
				);
			}
		}

		if ( ! $gallery_images ) {
			return '';
		}

		$output = '';

		// get gallery id
		static $galleryCounter;
		if ( null == $galleryCounter ) {
			$galleryCounter = 1;
		} else {
			$galleryCounter++;
		}
		$galleryId = 'gallery_' . $galleryCounter;

		$classWithBanner = adventure_tours_di( 'register' )->getVar( 'is_banner' ) ? ' gallery--withbanner' : '';
		$classSinglePageMode = adventure_tours_di( 'shortcodes_helper' )->attribute_is_true( $single_page ) && $is_filter ? ' gallery--page' : '';
		$output .= '<div id="' . esc_attr( $galleryId ) . '" class="gallery' . esc_attr( $classSinglePageMode ) . esc_attr( $classWithBanner ) . '">';

		if ( $is_filter && $full_categories_list ) {
			$filterHtml = '<div class="gallery__navigation margin-bottom">' .
				'<ul>' .
					'<li class="gallery__navigation__item-current"><a href="#" data-filterid="all">' . esc_html__( 'all', 'adventure-tours' ) . '</a></li>';

			foreach ( $full_categories_list as $category_slug => $category_name ) {
				$filterHtml .= '<li><a href="#" data-filterid="' . esc_attr( $category_slug ) . '">' . esc_html( $category_name ) . '</a></li>';
			}

			$filterHtml .= '</ul></div>';

			$output .= $filterHtml;
		}

		ob_start();
		include locate_template( 'templates/gallery/' . $layout . '.php' );
		$output .= ob_get_clean();

		if ( $is_pagination ) {
			wp_enqueue_script( 'jPages' );
			$output .= '<div class="pagination margin-top"></div>';
		}

		$output .= '</div>';

		return $output;
	}

	add_filter( 'post_gallery', 'adventure_tours_post_gallery_filter', 10, 2 );
}

if ( ! function_exists( 'adventure_tours_renders_stars_rating' ) ) {
	/**
	 * Renders stars rating element.
	 *
	 * @param  int   $rating rating value in the [1-5] range.
	 * @param  array $args   rendering options.
	 * @return void
	 */
	function adventure_tours_renders_stars_rating( $rating, $args = array() ) {
		if ( $rating <= 0 ) {
			return;
		}

		$args = wp_parse_args( $args, array(
			'before' => '',
			'after' => '',
			'before_each' => '',
			'after_each' => '',
		) );

		$stars_html = '';
		for ( $i = 0; $i < $rating; $i++ ) {
			$stars_html .= $args['before_each'] . '<i class="fa fa-star"></i>' . $args['after_each'];
		}
		printf(
			'%s%s%s',
			$args['before'],
			$stars_html,
			$args['after']
		);
	}
}

if ( ! function_exists( 'adventure_tours_renders_tour_badge' ) ) {
	/**
	 * Renders badge for specefied tour.
	 *
	 * @param  array $args assoc that contains rendering settings.
	 * @return void
	 */
	function adventure_tours_renders_tour_badge( $args = array() ) {
		$defaults = array(
			'tour_id' => null,
			'text_before' => '',
			'text_after' => '',
			'wrap_css_class' => '',
			'wrap_container_tag' => 'div',
			'css_class' => '',
			'container_tag' => 'div',
		);

		$args = wp_parse_args( $args, $defaults );

		extract( $args );

		if ( null === $tour_id ) {
			$tour_id = get_the_ID();
		}

		$badge = adventure_tours_di( 'tour_badge_service' )->get_tour_badge( $tour_id );
		if ( $badge ) {
			echo strtr('<{wrap_tag} class="{wrap_class}"><{tag} class="{class}" style="background-color:{bg_color}">{text_before}{text}{text_after}</{tag}></{wrap_tag}>', array(
				'{wrap_tag}' => $wrap_container_tag ? $wrap_container_tag : 'div',
				'{wrap_class}' => esc_attr( $wrap_css_class ),
				'{tag}' => $container_tag ? $container_tag : 'div',
				'{class}' => esc_attr( $css_class ),
				'{bg_color}' => $badge['color'],
				'{text}' => esc_html( $badge['title'] ),
				'{text_before}' => $text_before,
				'{text_after}' => $text_after,
			) );
		}
	}
}

if ( ! function_exists( 'adventure_tours_get_footer_columns' ) ) {
	/**
	 * Returns number of columns that should be rendered in the footer.
	 *
	 * @return int
	 */
	function adventure_tours_get_footer_columns() {
		$footerLayout = adventure_tours_get_option( 'footer_layout' );
		$laoutToColumns = array(
			'3columns' => 3,
			'2columns' => 2,
		);
		return isset( $laoutToColumns[$footerLayout] ) ? $laoutToColumns[$footerLayout] : 4;
	}
}

// -----------------------------------------------------------------#
// Renderind: tour page details, tabs rendering
// -----------------------------------------------------------------#
if ( ! function_exists( 'adventure_tours_filter_tour_tabs' ) ) {
	/**
	 * Tour details page, tabs filter function.
	 * Defines what tabs should be rendered.
	 *
	 * @return void
	 */
	function adventure_tours_filter_tour_tabs( $tabs ) {
		global $product;
		if ( empty( $product ) ) {
			return $tabs;
		}

		$tabs['description'] = array(
			'title' => esc_html__( 'Details', 'adventure-tours' ),
			'priority' => 10,
			'top_section_callback' => 'adventure_tours_render_tab_description_top_section',
			'callback' => 'adventure_tours_render_tab_description',
		);

		$additionalTabs = vp_metabox( 'tour_tabs_meta.tabs' );
		if ( $additionalTabs ) {
			foreach ( $additionalTabs as $key => $tabFields ) {
				$tabContent = apply_filters( 'the_content', $tabFields['content'] );
				if ( $tabContent ) {
					$tabs[ 'atab' . $key ] = array(
						'title' => esc_html( $tabFields['title'] ),
						'content' => $tabContent,
					);
				}
			}
		}

		// Photos tab rendering.
		ob_start();
		adventure_tours_render_tab_photos();
		$photosTabContent = ob_get_clean();
		if ( $photosTabContent ) {
			$tabs['photos'] = array(
				'title' => esc_html__( 'Photos', 'adventure-tours' ),
				'priority' => 25,
				'content' => $photosTabContent,
			);
		}

		/*
		if ( comments_open() ) {
			$tabs['reviews'] = array(
				'title'    => sprintf( esc_html__( 'Reviews (%d)', 'adventure-tours' ), $product->get_review_count() ),
				'priority' => 30,
				'callback' => 'comments_template',
			);
		}*/

		$booking_dates = adventure_touts_get_tour_booking_dates( $product->id );
		if ( $booking_dates ) {
			$tabs['booking_form'] = array(
				'title' => apply_filters( 'adventure_tours_booking_form_title', esc_html__( 'Book the tour', 'adventure-tours'), $product ),
				'tab_css_class' => 'visible-xs booking-form-scroller',
				'priority' => 35,
				'content' => ''
			);
		}

		return $tabs;
	}
}

if ( ! function_exists( 'adventure_tours_render_tab_description_top_section' ) ) {
	/**
	 * Tour details page, tab description icons/attributes rendeing function.
	 *
	 * @return void
	 */
	function adventure_tours_render_tab_description_top_section() {
		global $product;
		$all_attributes = AtTourHelper::get_tour_details_attributes( $product, null );
		$header_attributes = $all_attributes ? AtTourHelper::get_tour_details_attributes( $product, true ) : array();
		if ( $header_attributes ) {
			$count = count( $header_attributes );
			$count_to_batch_size = $count < 5 ? $count : (
				$count % 3 == 0 ? 3 : ( $count % 5 == 0 ? 5 : 4 )
			);
			/*
			$count_to_batch_size = $count < 4 ? $count : (
				$count % 4 == 0 ? 4 : 3
			);*/
			$attributes_batches = array_chunk( $header_attributes, $count_to_batch_size );
			foreach ( $attributes_batches as $attrib_batch ) {
				echo '<div class="tours-tabs__info">';
				foreach ( $attrib_batch as $attribute ) {
					echo strtr('<div class="tours-tabs__info__item">
						<div class="tours-tabs__info__item__content">
							<div class="tours-tabs__info__item__icon"><i class="{icon_class}"></i></div>
							<div class="tours-tabs__info__item__title">{value}</div>
							<div class="tours-tabs__info__item__description">{label}</div>
						</div>
					</div>', array(
						'{icon_class}' => esc_attr( $attribute['icon_class'] ),
						'{value}' => $attribute['text'],
						'{label}' => $attribute['label'],
					));
				}
				echo '</div>';
			}
		}

		$additional_attributes = $all_attributes && $header_attributes ? array_diff_key( $all_attributes, $header_attributes ) : $all_attributes;
		if ( $additional_attributes ) {
			$GLOBALS['_tour_additional_attributes'] = $additional_attributes;
		}
	}
}

if ( ! function_exists( 'adventure_tours_render_tab_description' ) ) {
	/**
	 * Tour details page, tab description rendeing function.
	 *
	 * @return void
	 */
	function adventure_tours_render_tab_description() {
		global $product;

		if ( adventure_tours_check( 'tour_category_taxonomy_exists' ) ) {
			$taxonomy = 'tour_category';
			$terms = get_the_terms( $product->ID, $taxonomy );
			if ( $terms ) {
				echo '<ul class="list-block list-block--tour-tabs">';
				foreach ( $terms as $term ) {
					echo '<li><a href="' . get_term_link( $term->slug, $taxonomy ) . '">' . $term->name . '</a></li>';
				}
				echo '</ul>';
			}
		}

		the_content();

		if ( ! empty( $GLOBALS['_tour_additional_attributes'] ) ) {
			adventure_tours_render_template_part( 'templates/tour/additional-attributes', '', array(
				'title' => esc_html__( 'Additional information', 'adventure-tours' ),
				'attributes' => $GLOBALS['_tour_additional_attributes'],
			) );
		}
	}
}

if ( ! function_exists( 'adventure_tours_render_tab_photos' ) ) {
	/**
	 * Tour details page, tab photos rendeing function.
	 *
	 * @return void
	 */
	function adventure_tours_render_tab_photos() {
		$thumbnail = adventure_tours_get_the_post_thumbnail();
		if ( $thumbnail ) {
			wp_enqueue_style( 'swipebox' );
			wp_enqueue_script( 'swipebox' );
			TdJsClientScript::addScript( 'initProductSwiper', "jQuery('.woocommerce-main-image.swipebox,.thumbnails .swipebox').swipebox({useSVG : true, hideBarsDelay : 0});" );
			echo sprintf( '<div class="row product-thumbnails"><div class="col-md-12"><a href="%s" class="swipebox" title="%s">%s</a></div></div>',
				esc_url( wp_get_attachment_url( get_post_thumbnail_id() ) ),
				esc_attr( get_the_title() ),
				$thumbnail
			);
		}
		woocommerce_show_product_thumbnails( );
	}
}
