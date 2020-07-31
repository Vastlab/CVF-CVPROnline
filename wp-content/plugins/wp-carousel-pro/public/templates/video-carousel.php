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
$video_sources = $upload_data['carousel_video_source'];
if ( empty( $sp_urls ) ) {
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
( ( 'rand' === $image_orderby ) ? shuffle( $sp_urls ) : '' );

foreach ( $sp_urls as $sp_url ) {

	// Image only source.
	$thumb             = '';
	$image_src         = '';
	$image_width_attr  = '';
	$image_height_attr = '';
	$image_src         = $sp_url['video_thumb_url'];
	if ( ( 'image_only' === $sp_url['video_type'] ) || ( 'self_hosted' === $sp_url['video_type'] ) ) {
		$wp_img_src_thumb  = wp_get_attachment_image_src( $sp_url['video_thumb_url']['id'], $image_sizes );
		$wpcp_img_full     = $sp_url['video_thumb_url']['url'];
		$image_alt_title   = $sp_url['video_thumb_url']['alt'];
		$image_width_attr  = ( $is_variable_width && 'ticker' !== $carousel_mode ) ? 'auto' : $wp_img_src_thumb[1];
		$image_height_attr = $wp_img_src_thumb[2];

		if ( ( 'custom' === $image_sizes ) && ( ! empty( $image_width ) && $sp_url['video_thumb_url']['width'] >= ! $image_width ) && ( ! empty( $image_height ) ) && $sp_url['video_thumb_url']['height'] >= ! $image_height ) {
			$image_resize_url  = the_wpcp_aq_resize( $wpcp_img_full, $image_width, $image_height, $image_crop );
			$image_width_attr  = ( $is_variable_width && 'ticker' !== $carousel_mode ) ? 'auto' : $image_width;
			$image_height_attr = $image_height;
		}
		$image_src = ! empty( $image_resize_url ) ? $image_resize_url : $wp_img_src_thumb[0];

		// Image Link.
		$image_linking_meta = wp_get_attachment_metadata( $sp_url['video_thumb_url']['id'] );
		$image_linking_urls = isset( $image_linking_meta['image_meta'] ) ? $image_linking_meta['image_meta'] : '';
		$image_linking_url  = ( ! empty( $image_linking_urls['wpcplinking'] ) ? esc_url( $image_linking_urls['wpcplinking'] ) : '' );
	}

	if ( filter_var( $image_src, FILTER_VALIDATE_URL ) && 'image_only' !== $sp_url['video_type'] ) {
		$thumb = sprintf( '<a class="wcp-video" href="%1$s"><img src="%2$s" width="%3$s" height="%4$s"><i class="fa fa-play-circle-o" aria-hidden="true"></i></a>', $sp_url['video_url'], $image_src, $image_width_attr, $image_height_attr );
	} elseif ( 'image_only' === $sp_url['video_type'] && ! empty( $image_src ) ) {
		// Image only slide.
		$thumb = sprintf( '<img src="%1$s" alt="%2$s" width="%3$s" height="%4$s">', $image_src, $image_alt_title, $image_width_attr, $image_height_attr );
		if ( 'img_link' === $sp_url['img_click_action'] && ! empty( $image_linking_url ) ) {
			$thumb = sprintf( '<a href="%1$s" target="_blank">%2$s<i class="fa fa-link" aria-hidden="true"></i></a>', $image_linking_url, $thumb );
		} elseif ( 'img_lbox' === $sp_url['img_click_action'] ) {
			$thumb = sprintf( '<a class="wcp-light-box" href="%1$s" data-lightbox-gallery="group-%3$s">%2$s<i class="fa fa-search-plus" aria-hidden="true"></i></a>', $wpcp_img_full, $thumb, $post_id );
		}
	}

	$all_captions = '';
	if ( $show_img_description && ! empty( $sp_url['video_desc'] ) ) {
		$all_captions = '<div class="wpcp-all-captions">' . wp_kses_post( $sp_url['video_desc'] ) . '</div>';
	}
	$carousel_thumb = ! empty( $thumb ) ? '<div class="wpcp-slide-image">' . $thumb . '</div>' : '';

	echo '<div class="wpcp-single-item wcp-video-item">';
	echo $carousel_thumb . $all_captions;
	echo '</div>';
} // End foreach.
echo '</div>
</div>'; // Carousel Wrapper.
