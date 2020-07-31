<?php
/**
 * The image carousel template.
 *
 * @package WP_Carousel_Pro
 * @subpackage WP_Carousel_Pro/public/templates
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$wpcp_post_type         = ( isset( $upload_data['wpcp_post_type'] ) ? $upload_data['wpcp_post_type'] : 'post' );
$post_from              = ( isset( $upload_data['wpcp_display_posts_from'] ) ? $upload_data['wpcp_display_posts_from'] : '' );
$specific_post_ids      = ( isset( $upload_data['wpcp_specific_posts'] ) ? $upload_data['wpcp_specific_posts'] : '' );
$post_taxonomy          = ( isset( $upload_data['wpcp_post_taxonomy'] ) ? $upload_data['wpcp_post_taxonomy'] : '' );
$post_taxonomy_terms    = ( isset( $upload_data['wpcp_taxonomy_terms'] ) ? $upload_data['wpcp_taxonomy_terms'] : '' );
$post_taxonomy_operator = ( isset( $upload_data['taxonomy_operator'] ) ? $upload_data['taxonomy_operator'] : '' );
$number_of_total_posts  = ( isset( $upload_data['number_of_total_posts'] ) ? $upload_data['number_of_total_posts'] : '' );


$show_post_content   = $shortcode_data['wpcp_post_content_show'];
$wpcp_content_type   = $shortcode_data['wpcp_post_content_type'];
$wpcp_word_limit     = isset( $shortcode_data['wpcp_post_content_words_limit'] ) ? $shortcode_data['wpcp_post_content_words_limit'] : 30;
$show_read_more      = isset( $shortcode_data['wpcp_post_readmore_button_show'] ) ? $shortcode_data['wpcp_post_readmore_button_show'] : '';
$post_read_more_text = isset( $shortcode_data['wpcp_post_readmore_text'] ) ? $shortcode_data['wpcp_post_readmore_text'] : '';

$show_post_category = isset( $shortcode_data['wpcp_post_category_show'] ) ? $shortcode_data['wpcp_post_category_show'] : true;
$show_post_date     = isset( $shortcode_data['wpcp_post_date_show'] ) ? $shortcode_data['wpcp_post_date_show'] : true;
$show_post_author   = isset( $shortcode_data['wpcp_post_author_show'] ) ? $shortcode_data['wpcp_post_author_show'] : true;
$show_post_tags     = isset( $shortcode_data['wpcp_post_tags_show'] ) ? $shortcode_data['wpcp_post_tags_show'] : true;
$show_post_comment  = isset( $shortcode_data['wpcp_post_comment_show'] ) ? $shortcode_data['wpcp_post_comment_show'] : true;
if ( empty( $wpcp_post_type ) ) {
	return;
}
	$args = array(
		'post_type'           => $wpcp_post_type,
		'post_status'         => 'publish',
		'order'               => $post_order,
		'orderby'             => $post_order_by,
		'ignore_sticky_posts' => 1,
		'posts_per_page'      => $number_of_total_posts,
		'post__not_in'        => array( get_the_ID() ),
	);
	if ( 'specific_post' === $post_from ) {
		$wpcp_get_specific = array( 'post__in' => $specific_post_ids );
		$args              = array_merge( $args, $wpcp_get_specific );
	}
	if ( 'taxonomy' === $post_from && ! empty( $post_taxonomy ) && ! empty( $post_taxonomy_terms ) ) {
		$args['tax_query'][] = array(
			'taxonomy' => $post_taxonomy,
			'field'    => 'term_id',
			'terms'    => $post_taxonomy_terms,
			'operator' => $post_taxonomy_operator,
		);
	}

	// Carousel Wrapper Start.
	echo '<div id="wpcpro-wrapper" class="wpcp-carousel-wrapper wpcp-wrapper-' . $post_id . '">';
	if ( $section_title ) {
		echo '<h2 class="sp-wpcpro-section-title">' . get_the_title( $post_id ) . '</h2>';
	}
	if ( $preloader ) {
		require WPCAROUSEL_PATH . '/public/templates/preloader.php';
	}
	echo '<div id="sp-wp-carousel-pro-id-' . $post_id . '" class="' . $carousel_classes . '" ' . ( 'ticker' === $carousel_mode ? $wpcp_bx_config : $wpcp_slick_options ) . $the_rtl . '>';
	$post_query = new WP_Query( $args );
	if ( $post_query->have_posts() ) {
		while ( $post_query->have_posts() ) :
			$post_query->the_post();

			$wpcp_get_post_title_attr = the_title_attribute( 'echo=0' );
			$wpcp_get_post_title      = get_the_title();
			$image                    = '';
			if ( has_post_thumbnail( $post_query->post->ID ) && $show_slide_image ) {
				$image_id             = get_post_thumbnail_id();
				$image_light_box_url  = wp_get_attachment_image_src( $image_id, 'full' );
				$image_url            = wp_get_attachment_image_src( $image_id, $image_sizes );
				$image_alt_text       = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
				$the_image_title_attr = ' title="' . $wpcp_get_post_title_attr . '"';
				$image_title_attr     = 'true' == $show_image_title_attr ? $the_image_title_attr : '';
				$image_width_attr     = ( $is_variable_width && 'ticker' !== $carousel_mode ) ? 'auto' : $image_url[1];
				$image_height_attr    = $image_url[2];

				if ( ( 'custom' === $image_sizes ) && ( ! empty( $image_width ) && $image_light_box_url[1] >= ! $image_width ) && ( ! empty( $image_height ) ) && $image_light_box_url[2] >= ! $image_height ) {
					$image_resize_url  = the_wpcp_aq_resize( $image_light_box_url[0], $image_width, $image_height, $image_crop );
					$image_width_attr  = ( $is_variable_width && 'ticker' !== $carousel_mode ) ? 'auto' : $image_width;
					$image_height_attr = $image_height;
				}
				$image_src         = ! empty( $image_resize_url ) ? $image_resize_url : $image_url[0];
				$image_width_attr  = ( ( 'custom' === $image_sizes ) ? $image_width : $image_url[1] );
				$image_height_attr = ( ( 'custom' === $image_sizes ) ? $image_height : $image_url[2] );

				if ( 'false' !== $lazy_load_image && 'ticker' !== $carousel_mode ) {
					$post_thumb = sprintf( '<img class="wpcp-post-thumb" data-lazy="%1$s" src="%2$s"%3$s alt="%4$s" width="%5$s" height="%6$s">', $image_src, $lazy_load_img, $image_title_attr, $image_alt_text, $image_width_attr, $image_height_attr );
				} else {
					$post_thumb = sprintf( '<img class="wpcp-post-thumb" src="%1$s"%2$s alt="%3$s" width="%4$s" height="%5$s">', $image_src, $image_title_attr, $image_alt_text, $image_width_attr, $image_height_attr );
				}

				$image = sprintf( '<div class="wpcp-slide-image"><a href="%2$s" target="%3$s">%1$s</a></div>', $post_thumb, get_the_permalink(), $link_target );
			} // End of Has post thumbnail.

			$wpcp_title      = sprintf( '<h2 class="wpcp-post-title"><a href="%1$s" target="%2$s">%3$s</a></h2>', get_the_permalink(), $link_target, get_the_title() );
			$wpcp_post_title = ( $show_img_title && ! empty( $wpcp_get_post_title ) ) ? $wpcp_title : '';

			$the_post_author_name = sprintf( '<li><a href="%1$s">%2$s%3$s</a></li>', get_author_posts_url( get_the_author_meta( 'ID' ) ), __( ' by ', 'wp-carousel-pro' ), get_the_author() );
			$post_author_name     = $show_post_author ? $the_post_author_name : '';

			$post_update_date = sprintf( '<time class="updated wpcp-hidden" datetime="%1$s">%2$s</time>', get_the_modified_date( 'c' ), get_the_modified_date() );
			$wpcp_post_date   = sprintf( '<li><time class="entry-date published updated" datetime="%1$s">%2$s</time></li>', get_the_date( 'c' ), get_the_date() );
			$post_date        = $show_post_date ? $wpcp_post_date : '';

			if ( 'post' === $wpcp_post_type ) {
				$wpcp_post_cat_term = get_the_category_list( '', ', ' );
				$wpcp_post_tags     = sprintf( '<li>%1$s</li>', get_the_tag_list( '', ', ' ) );
			} else {
				$wpcp_post_cat_term = sprintf( '<p class="wpcp-post-cat">%1$s</p>', get_the_term_list( get_the_ID(), $post_taxonomy, '', ', ' ) );
				$wpcp_post_tags     = sprintf( '<li>%1$s</li>', get_the_term_list( get_the_ID(), $post_taxonomy, '', ', ' ) );
			}
			$post_cat_term = $show_post_category ? $wpcp_post_cat_term : '';
			$post_tags     = $show_post_tags ? $wpcp_post_tags : '';

			$wpcp_comments  = '';
			$comment_number = get_comments_number();
			if ( comments_open() && $show_post_comment ) {
				if ( '0' === $comment_number ) {
					$comments_num = '0';
				} elseif ( '1' === $comment_number ) {
					$comments_num = __( '1', 'wp-carousel-pro' );
				} else {
					$comments_num = $comment_number . '';
				}
				$wpcp_comments = sprintf( '<li><a href="%1$s"> %2$s</a></li>', get_comments_link(), $comments_num );
			}

			$wpcp_post_meta = '';
			if ( $show_post_date | $show_post_author | $show_post_tags | $show_post_comment ) {
				$wpcp_post_meta = sprintf( '<ul class="wpcp-post-meta">%1$s%2$s%3$s%4$s</ul>', $post_date, $post_author_name, $post_tags, $wpcp_comments );
			}

			// Post Content.
			$post_content = apply_filters( 'the_content', get_the_content() );
			if ( 'excerpt' === $wpcp_content_type ) {
				$wpcp_post_content = sprintf( '<p>%1$s</p>', get_the_excerpt() );
			} elseif ( 'content' === $wpcp_content_type ) {
				$wpcp_post_content = $post_content;
			} else {
				$wpcp_post_content = sprintf( '<p>%1$s</p>', wp_trim_words( $post_content, $wpcp_word_limit, '...' ) );
			}
			if ( $show_read_more && 'content' !== $wpcp_content_type ) {
				$wpcp_read_more_button = sprintf( '<div class="sp-wpcp-read-more"><a class="wpcp_readmore" href="%1$s">%2$s</a></div>', get_the_permalink(), $post_read_more_text );
			} else {
				$wpcp_read_more_button = '';
			}

			if ( $show_img_title || $show_post_content ) {
				$all_captions = '<div class="wpcp-all-captions">' . $post_cat_term . $wpcp_post_title . ( $show_post_content ? $wpcp_post_content : '' ) . ( $show_read_more ? $wpcp_read_more_button : '' ) . $wpcp_post_meta . '</div>';
			} else {
				$all_captions = '';
			}

			if ( $image || $all_captions ) {
				echo '<div class="wpcp-single-item">';
				echo $image . $all_captions;
				echo '</div>';
			}
		endwhile;
		wp_reset_postdata();
	} else {
		echo '<h2 class="wpcp-no-post-found" >' . esc_html__( 'No posts found', 'wp-carousel-pro' ) . '</h2>';
	}
	echo '</div>';
	echo '</div>'; // Carousel Wrapper.
