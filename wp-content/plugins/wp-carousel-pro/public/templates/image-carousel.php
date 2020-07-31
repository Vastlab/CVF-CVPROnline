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

$gallery_ids            = $upload_data['wpcp_gallery'];
$is_image_link_nofollow = isset( $shortcode_data['wpcp_logo_link_nofollow'] ) ? $shortcode_data['wpcp_logo_link_nofollow'] : '';
$image_link_nofollow    = true == $is_image_link_nofollow ? ' rel="nofollow"' : '';
if ( empty( $gallery_ids ) ) {
			return;
}
echo '<div id="wpcpro-wrapper" class="wpcp-carousel-wrapper wpcp-wrapper-' . $post_id . '">';
if ( $section_title ) {
	echo '<h2 class="sp-wpcpro-section-title">' . get_the_title( $post_id ) . '</h2>';
}
if ( $preloader ) {
	require WPCAROUSEL_PATH . '/public/templates/preloader.php';
}
echo '<div id="sp-wp-carousel-pro-id-' . $post_id . '" class="' . $carousel_classes . '" ' . ( 'ticker' === $carousel_mode ? $wpcp_bx_config : $wpcp_slick_options ) . $the_rtl . '>';

$attachments = explode( ',', $gallery_ids );
( ( 'rand' == $image_orderby ) ? shuffle( $attachments ) : '' );
if ( is_array( $attachments ) || is_object( $attachments ) ) :
	foreach ( $attachments as $attachment ) {
		$image_data          = get_post( $attachment );
		$image_title         = $image_data->post_title;
		$image_caption       = $image_data->post_excerpt;
		$image_description   = $image_data->post_content;
		$image_alt_titles    = $image_data->_wp_attachment_image_alt;
		$image_alt_title     = ! empty( $image_alt_titles ) ? $image_alt_titles : $image_title;
		$image_light_box_url = wp_get_attachment_image_src( $attachment, 'full' );
		$image_url           = wp_get_attachment_image_src( $attachment, $image_sizes );
		$image_width_attr    = ( $is_variable_width && 'ticker' !== $carousel_mode ) ? 'auto' : $image_url[1];
		$image_height_attr   = $image_url[2];

		$image_linking_meta = wp_get_attachment_metadata( $attachment );
		$image_linking_urls = $image_linking_meta['image_meta'];
		$image_linking_url  = ( ! empty( $image_linking_urls['wpcplinking'] ) ? esc_url( $image_linking_urls['wpcplinking'] ) : '' );

		$the_image_title_attr = ' title="' . $image_title . '"';
		$image_title_attr     = 'true' == $show_image_title_attr ? $the_image_title_attr : '';

		if ( ( 'custom' === $image_sizes ) && ( ! empty( $image_width ) && $image_light_box_url[1] >= ! $image_width ) && ( ! empty( $image_height ) ) && $image_light_box_url[2] >= ! $image_height ) {
			$image_resize_url  = the_wpcp_aq_resize( $image_light_box_url[0], $image_width, $image_height, $image_crop );
			$image_width_attr  = ( $is_variable_width && 'ticker' !== $carousel_mode ) ? 'auto' : $image_width;
			$image_height_attr = $image_height;
		}
		$image_src = ! empty( $image_resize_url ) ? $image_resize_url : $image_url[0];
		if ( 'false' !== $lazy_load_image && 'ticker' !== $carousel_mode ) {
				$image = sprintf( '<img class="wcp-lazy" data-lazy="%1$s" src="%2$s"%3$s alt="%4$s" width="%5$s" height="%6$s">', $image_src, $lazy_load_img, $image_title_attr, $image_alt_title, $image_width_attr, $image_height_attr );
		} else {
			$image = sprintf( '<img src="%1$s"%2$s alt="%3$s" width="%4$s" height="%5$s">', $image_src, $image_title_attr, $image_alt_title, $image_width_attr, $image_height_attr );
		}

		// Image Caption and description.
		$caption = sprintf( '<h2 class="wpcp-image-caption">%1$s</h2>', $image_caption );
		if ( $image_link_show && ! empty( $image_linking_url ) ) {
			$caption = sprintf( '<h2 class="wpcp-image-caption"><a href="%1$s" target="%2$s"%3$s>%4$s</a></h2>', $image_linking_url, $link_target, $image_link_nofollow, $image_caption );
		}
		$description = sprintf( '<p class="wpcp-image-description">%1$s</p>', $image_description );
		if ( ( $show_img_caption && ! empty( $image_caption ) ) || ( $show_img_description && ! empty( $image_description ) ) ) {
			$all_captions = '<div class="wpcp-all-captions">' . ( $show_img_caption && ! empty( $image_caption ) ? $caption : '' ) . ( $show_img_description && ! empty( $image_description ) ? $description : '' ) . '</div>';
		} else {
			$all_captions = '';
		}

		// Single Item.
		echo '<div class="wpcp-single-item">';
		if ( $light_box ) {
			echo sprintf( '<div class="wpcp-slide-image"><a class="wcp-light-box" href="%1$s" data-lightbox-gallery="group-%2$s" title="%5$s">%3$s</a></div>%4$s', esc_url( $image_light_box_url[0] ), $post_id, $image, $all_captions, $image_caption );
		} elseif ( isset( $image_linking_url ) && filter_var( $image_linking_url, FILTER_VALIDATE_URL ) && $image_link_show ) {
			echo sprintf( '<div class="wpcp-slide-image"><a href="%1$s" target="%2$s"%3$s>%4$s</a></div>%5$s', $image_linking_url, $link_target, $image_link_nofollow, $image, $all_captions );
		} else {
			echo sprintf( '<div class="wpcp-slide-image">%1$s</div>%2$s', $image, $all_captions );
		}
		echo '</div>';
	} // End foreach.
endif;
echo '</div>';
echo '</div>'; // Carousel Wrapper.
