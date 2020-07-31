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
if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

$wpcp_product_from        = $upload_data['wpcp_display_product_from'];
$specific_product_ids     = isset( $upload_data['wpcp_specific_product'] ) ? $upload_data['wpcp_specific_product'] : '';
$product_category_terms   = isset( $upload_data['wpcp_taxonomy_terms'] ) ? $upload_data['wpcp_taxonomy_terms'] : '';
$product_terms_operator   = isset( $upload_data['wpcp_product_category_operator'] ) ? $upload_data['wpcp_product_category_operator'] : '';
$number_of_total_products = $upload_data['wpcp_total_products'];

$show_product_image           = $shortcode_data['show_image'];
$show_product_name            = $shortcode_data['wpcp_product_name'];
$show_product_price           = $shortcode_data['wpcp_product_price'];
$show_product_rating          = $shortcode_data['wpcp_product_rating'];
$show_product_cart            = $shortcode_data['wpcp_product_cart'];
$show_product_desc            = isset( $shortcode_data['wpcp_product_desc'] ) ? $shortcode_data['wpcp_product_desc'] : true;
$limit_product_content        = isset( $shortcode_data['wpcp_limit_product_desc'] ) ? $shortcode_data['wpcp_limit_product_desc'] : '';
$product_content_limit_number = isset( $shortcode_data['wpcp_product_desc_limit_number'] ) ? $shortcode_data['wpcp_product_desc_limit_number'] : '';
$show_product_readmore        = isset( $shortcode_data['wpcp_product_readmore_show'] ) ? $shortcode_data['wpcp_product_readmore_show'] : '';
$product_readmore_text        = isset( $shortcode_data['wpcp_product_readmore_text'] ) ? $shortcode_data['wpcp_product_readmore_text'] : '';

	$default_args = array(
		'post_type'           => 'product',
		'post_status'         => 'publish',
		'ignore_sticky_posts' => 1,
		'posts_per_page'      => $number_of_total_products,
		'order'               => $post_order,
		'orderby'             => $post_order_by,
		'meta_query'          => array(
			array(
				'key'     => '_stock_status',
				'value'   => 'outofstock',
				'compare' => 'NOT IN',
			),
		),
	);
	if ( 'latest' === $wpcp_product_from ) {
		$args = $default_args;
	} elseif ( 'specific_products' === $wpcp_product_from ) {
		$args = array(
			'post__in' => $specific_product_ids,
		);
		$args = array_merge( $default_args, $args );
	} elseif ( 'taxonomy' === $wpcp_product_from ) {

		$args['tax_query'][] = array(
			'taxonomy' => 'product_cat',
			'field'    => 'term_id',
			'terms'    => $product_category_terms,
			'operator' => $product_terms_operator,
		);
		$args                = array_merge( $default_args, $args );
	}
	$product_query = new WP_Query( $args );

		// Carousel Wrapper Start.
		echo '<div id="wpcpro-wrapper" class="wpcp-carousel-wrapper wpcp-wrapper-' . $post_id . '">';
	if ( $section_title ) {
		echo '<h2 class="sp-wpcpro-section-title">' . get_the_title( $post_id ) . '</h2>';
	}
	if ( $preloader ) {
		require WPCAROUSEL_PATH . '/public/templates/preloader.php';
	}
	echo '<div id="sp-wp-carousel-pro-id-' . $post_id . '" class="' . $carousel_classes . '" ' . ( 'ticker' === $carousel_mode ? $wpcp_bx_config : $wpcp_slick_options ) . $the_rtl . '>';
	if ( $product_query->have_posts() ) {
		while ( $product_query->have_posts() ) :
			$product_query->the_post();
			global $product, $woocommerce;
			echo '<div class="wpcp-single-item">';
			// Product Thumbnail.
			$wpcp_product_image = '';
			if ( has_post_thumbnail() && $show_product_image ) {
				$product_thumb_id       = get_post_thumbnail_id();
				$image_light_box_url    = wp_get_attachment_image_src( $product_thumb_id, 'full' );
				$product_thumb_alt_text = get_post_meta( $product_thumb_id, '_wp_attachment_image_alt', true );
				$image_url              = wp_get_attachment_image_src( $product_thumb_id, $image_sizes );
				$image_width_attr     = ( $is_variable_width && 'ticker' !== $carousel_mode ) ? 'auto' : $image_url[1];
				$image_height_attr    = $image_url[2];

				if ( ( 'custom' === $image_sizes ) && ( ! empty( $image_width ) && $image_light_box_url[1] >= ! $image_width ) && ( ! empty( $image_height ) && $image_light_box_url[2] >= ! $image_height ) ) {
					$image_resize_url = the_wpcp_aq_resize( $image_light_box_url[0], $image_width, $image_height, $image_crop );
					$image_width_attr  = ( $is_variable_width && 'ticker' !== $carousel_mode ) ? 'auto' : $image_width;
					$image_height_attr = $image_height;
				}
				$image_src = isset( $image_resize_url ) ? $image_resize_url : $image_url[0];

				$the_image_title_attr = ' title="' . get_the_title() . '"';
				$image_title_attr     = 'true' == $show_image_title_attr ? $the_image_title_attr : '';

				if ( $image_src ) {
					if ( 'false' !== $lazy_load_image && 'ticker' !== $carousel_mode ) {
						$wpcp_product_thumb = sprintf( '<img class="wpcp-product-image" data-lazy="%1$s" src="%2$s"%3$s alt="%4$s" width="%5$s" height="%6$s">', $image_src, $lazy_load_img, $image_title_attr, $product_thumb_alt_text, $image_width_attr, $image_height_attr );
					} else {
						$wpcp_product_thumb = sprintf( '<img class="wpcp-product-image" src="%1$s"%2$s alt="%3$s" width="%4$s" height="%5$s">', $image_src, $image_title_attr, $product_thumb_alt_text, $image_width_attr, $image_height_attr );
					}

					if ( $light_box ) {
						$wpcp_product_image = sprintf( '<div class="wpcp-slide-image"><a href="%1$s" class="wcp-light-box" data-lightbox-gallery="group-%2$s" title="%4$s">%3$s</a></div>', $image_light_box_url[0], $post_id, $wpcp_product_thumb, get_the_title() );
					} else {
						$wpcp_product_image = sprintf( '<div class="wpcp-slide-image"><a href="%1$s">%2$s</a></div>', get_the_permalink(), $wpcp_product_thumb );
					}
				}
			}

			// Product name.
			$wpcp_product_name = sprintf( '<h2 class="wpcp-product-title"><a href="%1$s">%2$s</a></h2>', get_the_permalink(), get_the_title() );

			$price_html = $product->get_price_html();
			if ( $price_html ) {
				$wpcp_product_price = sprintf( '<div class="wpcp-product-price">%1$s</div>', $price_html );
			}

			// Product rating.
			$av_rating      = $product->get_average_rating();
			$average_rating = ( $av_rating / 5 ) * 100;
			if ( $average_rating > 0 ) {
				$wpcp_product_rating = sprintf( '<div class="wpcp-product-rating woocommerce"><div class="woocommerce-product-rating"><div class="star-rating" title="%1$s %2$s %3$s"><span style="width:%4$s"></span></div></div></div>', __( 'Rated ', 'wp-carousel-pro' ), $av_rating, __( ' out of 5', 'wp-carousel-pro' ), $average_rating . '%' );
			}

			// Add to cart button.
				$wpcp_cart        = apply_filters( 'wpcp_filter_product_cart', do_shortcode( '[add_to_cart id="' . get_the_ID() . '" show_price="false" style="none"]' ) );
				$wpcp_cart_button = sprintf( '<div class="wpcp-cart-button">%1$s</div>', $wpcp_cart );

			// Product content & Read more button.
			if ( true == $limit_product_content ) {
				$product_content = wp_trim_words( get_the_content(), $product_content_limit_number, '...' );
			} else {
				$product_content = get_the_content();
			}

			$content_count = str_word_count( get_the_content() );
			if ( $content_count >= $product_content_limit_number && $show_product_readmore ) {
				$product_content_more_button = sprintf( '<div class="wpcp-product-more-content"><a href="%1$s">%2$s</a></div>', get_the_permalink(), $product_readmore_text );
			} else {
				$product_content_more_button = '';
			}
			$wpcp_product_content = sprintf( '<div class="wpcp-product-content">%1$s %2$s</div>', do_shortcode( $product_content ), $product_content_more_button );

			if ( $show_product_name || $show_product_rating || $show_product_price || $show_product_desc || $show_product_cart ) {
				$wpcp_product_details = '<div class="wpcp-all-captions">' . ( ( $show_product_name ) && isset( $wpcp_product_name ) ? $wpcp_product_name : '' ) . ( $show_product_price && isset( $wpcp_product_price ) ? $wpcp_product_price : '' ) . ( $show_product_rating && isset( $wpcp_product_rating ) ? $wpcp_product_rating : '' ) . ( $show_product_desc ? $wpcp_product_content : '' ) . ( $show_product_cart ? $wpcp_cart_button : '' ) . '</div>';
			}
			echo $wpcp_product_image . $wpcp_product_details;
			echo '</div>';

		endwhile;
		wp_reset_postdata();
	} else {
		$outline .= '<h2 class="sp-not-found-any-post" >' . esc_html__( 'No products found', 'wp-carousel-pro' ) . '</h2>';
	}
	echo '</div>
</div>'; // Carousel Wrapper.
