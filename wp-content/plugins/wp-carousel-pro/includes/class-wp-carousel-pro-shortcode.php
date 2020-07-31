<?php

/**
 * The file that defines the shortcode plugin class.
 *
 * A class definition that define main carousel shortcode of the plugin.
 *
 * @link       https://shapedplugin.com/
 * @since      3.0.0
 *
 * @package    WP_Carousel_Pro
 * @subpackage WP_Carousel_Pro/includes
 */

/**
 * The Shortcode class.
 *
 * This is used to define shortcode, shortcode attributes, and carousel types.
 *
 * @since      3.0.0
 * @package    WP_Carousel_Pro
 * @subpackage WP_Carousel_Pro/includes
 * @author     ShapedPlugin <shapedplugin@gmail.com>
 */
class WP_Carousel_Pro_Shortcode {

	/**
	 * Holds the class object.
	 *
	 * @since 3.0.0
	 * @var object
	 */
	public static $instance;

	/**
	 * Contain the version class object.
	 *
	 * @since 3.0.0
	 * @var object
	 */
	public $version;

	/**
	 * Holds the carousel data.
	 *
	 * @since 3.0.0
	 * @var array
	 */
	public $data;

	/**
	 * YouTube video support.
	 *
	 * @since 3.0.0
	 * @var boolean
	 */
	public $youtube = false;

	/**
	 * Vimeo video support.
	 *
	 * @since 3.0.0
	 * @var boolean
	 */
	public $vimeo = false;

	/**
	 * The post ID.
	 *
	 * @var string $post_id The post id of the carousel shortcode.
	 */
	public $post_id;


	/**
	 * Allows for accessing single instance of class. Class should only be constructed once per call.
	 *
	 * @since 3.0.0
	 * @static
	 * @return WP_Carousel_Pro_Shortcode Shortcode instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * A shortcode for rendering the carousel.
	 *
	 * @param integer $attributes The ID the shortcode.
	 * @return void
	 */
	public function sp_wp_carousel_shortcode( $attributes ) {
		if ( empty( $attributes['id'] ) ) {
			return;
		}

		$post_id = intval( $attributes['id'] );

		// Video Carousel.
		$upload_data = get_post_meta( $post_id, 'sp_wpcp_upload_options', true );
		if ( empty( $upload_data ) ) {
			return;
		}
		$carousel_type = isset( $upload_data['wpcp_carousel_type'] ) ? $upload_data['wpcp_carousel_type'] : '';

		// Carousel Section Settings.
		$shortcode_data = get_post_meta( $post_id, 'sp_wpcp_shortcode_options', true );

		$preloader     = isset( $shortcode_data['wpcp_preloader'] ) ? $shortcode_data['wpcp_preloader'] : true;
		$carousel_mode = isset( $shortcode_data['wpcp_carousel_mode'] ) ? $shortcode_data['wpcp_carousel_mode'] : 'standard';

		// Center Mode.
		$center_mode            = 'center' === $carousel_mode ? 'true' : 'false';
		$center_padding         = '0';
		$center_padding_desktop = '0';
		$center_padding_laptop  = '0';
		$center_padding_tablet  = '0';
		$center_padding_mobile  = '0';
		if ( 'true' === $center_mode ) {
			$center_mode_padding    = isset( $shortcode_data['wpcp_image_center_mode_padding'] ) ? $shortcode_data['wpcp_image_center_mode_padding'] : '';
			$center_padding         = isset( $center_mode_padding['lg_desktop'] ) ? $center_mode_padding['lg_desktop'] : '100';
			$center_padding_desktop = isset( $center_mode_padding['desktop'] ) ? $center_mode_padding['desktop'] : '100';
			$center_padding_laptop  = isset( $center_mode_padding['laptop'] ) ? $center_mode_padding['laptop'] : '70';
			$center_padding_tablet  = isset( $center_mode_padding['tablet'] ) ? $center_mode_padding['tablet'] : '50';
			$center_padding_mobile  = isset( $center_mode_padding['mobile'] ) ? $center_mode_padding['mobile'] : '40';
		}

		// Ticker Mode.
		$slide_width  = isset( $shortcode_data['wpcp_slide_width']['all'] ) && ! empty( $shortcode_data['wpcp_slide_width']['all'] ) ? $shortcode_data['wpcp_slide_width']['all'] : '250';
		$slide_margin = isset( $shortcode_data['wpcp_slide_margin']['all'] ) && is_numeric( $shortcode_data['wpcp_slide_margin']['all'] ) ? $shortcode_data['wpcp_slide_margin']['all'] : '20';

		// Image settings .
		$image_orderby        = isset( $shortcode_data['wpcp_image_order_by'] ) ? $shortcode_data['wpcp_image_order_by'] : '';
		$show_slide_image     = isset( $shortcode_data['show_image'] ) ? $shortcode_data['show_image'] : '';
		$show_img_title       = isset( $shortcode_data['wpcp_post_title'] ) ? $shortcode_data['wpcp_post_title'] : '';
		$show_img_caption     = isset( $shortcode_data['wpcp_image_caption'] ) ? $shortcode_data['wpcp_image_caption'] : '';
		$show_img_description = isset( $shortcode_data['wpcp_image_desc'] ) ? $shortcode_data['wpcp_image_desc'] : '';
		$lazy_load_image      = isset( $shortcode_data['wpcp_image_lazy_load'] ) ? $shortcode_data['wpcp_image_lazy_load'] : 'false';
		$light_box            = isset( $shortcode_data['_image_light_box'] ) ? $shortcode_data['_image_light_box'] : false;
		$group                = isset( $shortcode_data['_image_group'] ) ? $shortcode_data['_image_group'] : '';

		$_image_title_att      = isset( $shortcode_data['_image_title_attr'] ) ? $shortcode_data['_image_title_attr'] : '';
		$show_image_title_attr = ( true == $_image_title_att ) ? 'true' : 'false';
		$image_link_show       = isset( $shortcode_data['wpcp_logo_link_show'] ) ? $shortcode_data['wpcp_logo_link_show'] : true;
		$link_target           = isset( $shortcode_data['wpcp_link_open_target'] ) ? $shortcode_data['wpcp_link_open_target'] : '_blank';
		$image_sizes           = isset( $shortcode_data['wpcp_image_sizes'] ) ? $shortcode_data['wpcp_image_sizes'] : '';
		$image_width           = isset( $shortcode_data['wpcp_image_crop_size']['top'] ) ? $shortcode_data['wpcp_image_crop_size']['top'] : '';
		$image_height          = isset( $shortcode_data['wpcp_image_crop_size']['right'] ) ? $shortcode_data['wpcp_image_crop_size']['right'] : '';
		$do_image_crop         = isset( $shortcode_data['wpcp_image_crop_size']['style'] ) ? $shortcode_data['wpcp_image_crop_size']['style'] : '';
		$image_crop            = 'Hard-crop' === $do_image_crop ? true : false;

		$is_variable_width = isset( $shortcode_data['_variable_width'] ) ? $shortcode_data['_variable_width'] : '';
		$variable_width    = $is_variable_width ? 'true' : 'false';

		// Carousel Column Number Settings.
		$column_number     = isset( $shortcode_data['wpcp_number_of_columns'] ) ? $shortcode_data['wpcp_number_of_columns'] : '';
		$column_lg_desktop = isset( $column_number['lg_desktop'] ) ? $column_number['lg_desktop'] : '5';
		$column_desktop    = isset( $column_number['desktop'] ) ? $column_number['desktop'] : '4';
		$column_laptop     = isset( $column_number['laptop'] ) ? $column_number['laptop'] : '3';
		$column_tablet     = isset( $column_number['tablet'] ) ? $column_number['tablet'] : '2';
		$column_mobile     = isset( $column_number['mobile'] ) ? $column_number['mobile'] : '1';

		// Ticker Carousel.
		$column_number_ticker = isset( $shortcode_data['wpcp_number_of_columns_ticker'] ) ? $shortcode_data['wpcp_number_of_columns_ticker'] : '';
		$max_column           = isset( $column_number_ticker['lg_desktop'] ) ? $column_number_ticker['lg_desktop'] : '5';
		$min_column           = isset( $column_number_ticker['mobile'] ) ? $column_number_ticker['mobile'] : '2';

		// Carousel Settings.
		// // Carousel Normal Settings.
		$is_auto_play = isset( $shortcode_data['wpcp_carousel_auto_play'] ) ? $shortcode_data['wpcp_carousel_auto_play'] : true;
		$auto_play    = $is_auto_play ? 'true' : 'false';

		$autoplay_speed = isset( $shortcode_data['carousel_auto_play_speed']['all'] ) && ! empty( $shortcode_data['carousel_auto_play_speed']['all'] ) ? $shortcode_data['carousel_auto_play_speed']['all'] : '3000';
		$old_speed      = isset( $shortcode_data['standard_carousel_scroll_speed'] ) && is_numeric( $shortcode_data['standard_carousel_scroll_speed'] ) ? $shortcode_data['standard_carousel_scroll_speed'] : '600';
		$speed          = isset( $shortcode_data['standard_carousel_scroll_speed']['all'] ) && ! empty( $shortcode_data['standard_carousel_scroll_speed']['all'] ) ? $shortcode_data['standard_carousel_scroll_speed']['all'] : $old_speed;
		$ticker_speed   = isset( $shortcode_data['ticker_carousel_scroll_speed']['all'] ) && ! empty( $shortcode_data['ticker_carousel_scroll_speed']['all'] ) ? $shortcode_data['ticker_carousel_scroll_speed']['all'] : '8000';

		// // Slide to scroll.
		$slide_to_scroll_lg_desktop = isset( $shortcode_data['slides_to_scroll']['lg_desktop'] ) ? $shortcode_data['slides_to_scroll']['lg_desktop'] : '1';
		$slide_to_scroll_desktop    = isset( $shortcode_data['slides_to_scroll']['desktop'] ) ? $shortcode_data['slides_to_scroll']['desktop'] : '1';
		$slide_to_scroll_laptop     = isset( $shortcode_data['slides_to_scroll']['laptop'] ) ? $shortcode_data['slides_to_scroll']['laptop'] : '1';
		$slide_to_scroll_tablet     = isset( $shortcode_data['slides_to_scroll']['tablet'] ) ? $shortcode_data['slides_to_scroll']['tablet'] : '1';
		$slide_to_scroll_mobile     = isset( $shortcode_data['slides_to_scroll']['mobile'] ) ? $shortcode_data['slides_to_scroll']['mobile'] : '1';

		$is_infinite         = isset( $shortcode_data['carousel_infinite'] ) ? $shortcode_data['carousel_infinite'] : '';
		$infinite            = $is_infinite ? 'true' : 'false';
		$is_pause_on_hover   = isset( $shortcode_data['carousel_pause_on_hover'] ) ? $shortcode_data['carousel_pause_on_hover'] : '';
		$pause_on_hover      = $is_pause_on_hover ? 'true' : 'false';
		$carousel_direction  = isset( $shortcode_data['wpcp_carousel_direction'] ) ? $shortcode_data['wpcp_carousel_direction'] : '';
		$is_slider_animation = isset( $shortcode_data['wpcp_slider_animation'] ) ? $shortcode_data['wpcp_slider_animation'] : '';
		$slider_animation    = ( 'fade' === $is_slider_animation ) ? 'true' : 'false';

		// // Navigation settings.
		$arrow_position = isset( $shortcode_data['wpcp_carousel_nav_position'] ) ? $shortcode_data['wpcp_carousel_nav_position'] : 'vertical_center';
		$wpcp_arrows    = isset( $shortcode_data['wpcp_navigation'] ) ? $shortcode_data['wpcp_navigation'] : '';
		switch ( $wpcp_arrows ) {
			case 'show':
				$arrows        = 'true';
				$arrows_mobile = 'true';
				break;
			case 'hide':
				$arrows        = 'false';
				$arrows_mobile = 'false';
				break;
			case 'hide_mobile':
				$arrows        = 'true';
				$arrows_mobile = 'false';
				break;
		}
		$nav_icons = isset( $shortcode_data['navigation_icons'] ) ? $shortcode_data['navigation_icons'] : 'angle';

		// Pagination settings.
		$wpcp_dots = isset( $shortcode_data['wpcp_pagination'] ) ? $shortcode_data['wpcp_pagination'] : '';
		switch ( $wpcp_dots ) {
			case 'show':
				$dots        = 'true';
				$dots_mobile = 'true';
				break;
			case 'hide':
				$dots        = 'false';
				$dots_mobile = 'false';
				break;
			case 'hide_mobile':
				$dots        = 'true';
				$dots_mobile = 'false';
				break;
		}

		// Miscellaneous Settings.
		$is_adaptive_height = isset( $shortcode_data['wpcp_adaptive_height'] ) ? $shortcode_data['wpcp_adaptive_height'] : true;
		$adaptive_height    = $is_adaptive_height ? 'true' : 'false';
		$is_swipe           = isset( $shortcode_data['slider_swipe'] ) ? $shortcode_data['slider_swipe'] : true;
		$swipe              = $is_swipe ? 'true' : 'false';
		$is_draggable       = isset( $shortcode_data['slider_draggable'] ) ? $shortcode_data['slider_draggable'] : true;
		$draggable          = $is_draggable ? 'true' : 'false';
		$is_swipetoslide    = isset( $shortcode_data['carousel_swipetoslide'] ) ? $shortcode_data['carousel_swipetoslide'] : true;
		$swipetoslide       = $is_swipetoslide ? 'true' : 'false';

		$lazy_load_img = WPCAROUSEL_URL . 'public/css/bx_loader.gif';

		$wpcp_post_detail        = isset( $shortcode_data['wpcp_post_detail_position'] ) ? $shortcode_data['wpcp_post_detail_position'] : '';
		$wpcp_overlay_position   = isset( $shortcode_data['wpcp_overlay_position'] ) ? $shortcode_data['wpcp_overlay_position'] : '';
		$wpcp_overlay_visibility = isset( $shortcode_data['wpcp_overlay_visibility'] ) ? $shortcode_data['wpcp_overlay_visibility'] : '';

		$post_order_by = ( isset( $shortcode_data['wpcp_post_order_by'] ) ? $shortcode_data['wpcp_post_order_by'] : '' );
		$post_order    = ( isset( $shortcode_data['wpcp_post_order'] ) ? $shortcode_data['wpcp_post_order'] : '' );

		// Load Google font.
		if ( wpcp_get_option( 'wpcp_dequeue_google_font', true ) ) :
			$unique_id           = uniqid();
			$enqueue_fonts       = array();
			$wpcpro_typography   = array();
			$wpcpro_typography[] = isset( $shortcode_data['wpcp_section_title_typography'] ) ? $shortcode_data['wpcp_section_title_typography'] : '';
			if ( 'image-carousel' === $carousel_type ) {
				$wpcpro_typography[] = isset( $shortcode_data['wpcp_image_caption_typography'] ) ? $shortcode_data['wpcp_image_caption_typography'] : '';
				$wpcpro_typography[] = isset( $shortcode_data['wpcp_image_desc_typography'] ) ? $shortcode_data['wpcp_image_desc_typography'] : '';
			} elseif ( 'post-carousel' === $carousel_type ) {
				$wpcpro_typography[] = isset( $shortcode_data['wpcp_title_typography'] ) ? $shortcode_data['wpcp_title_typography'] : '';
				$wpcpro_typography[] = isset( $shortcode_data['wpcp_post_content_typography'] ) ? $shortcode_data['wpcp_post_content_typography'] : '';
				$wpcpro_typography[] = isset( $shortcode_data['wpcp_post_cat_typography'] ) ? $shortcode_data['wpcp_post_cat_typography'] : '';
				$wpcpro_typography[] = isset( $shortcode_data['wpcp_post_meta_typography'] ) ? $shortcode_data['wpcp_post_meta_typography'] : '';
			} elseif ( 'product-carousel' === $carousel_type ) {
				$wpcpro_typography[] = isset( $shortcode_data['wpcp_product_name_typography'] ) ? $shortcode_data['wpcp_product_name_typography'] : '';
				$wpcpro_typography[] = isset( $shortcode_data['wpcp_product_desc_typography'] ) ? $shortcode_data['wpcp_product_desc_typography'] : '';
			}
			if ( ! empty( $wpcpro_typography ) ) {
				foreach ( $wpcpro_typography as $font ) {
					if ( isset( $font['type'] ) && 'google' === $font['type'] ) {
						$variant         = ( isset( $font['font-weight'] ) ) ? ':' . $font['font-weight'] : '';
						$subset          = isset( $font['subset'] ) ? ':' . $font['subset'] : '';
						$enqueue_fonts[] = $font['font-family'] . $variant . $subset;
					}
				}
			}

			if ( ! empty( $enqueue_fonts ) ) {
				wp_enqueue_style( 'sp-wpcp-google-fonts' . $unique_id, esc_url( add_query_arg( 'family', rawurlencode( implode( '|', $enqueue_fonts ) ), '//fonts.googleapis.com/css' ) ), array(), $this->version );
			}
		endif; // Google font enqueue dequeue.

		// Magnific Script.
		if ( $light_box || 'video-carousel' === $carousel_type ) {
			if ( wpcp_get_option( 'wpcp_magnific_js', true ) ) {
				wp_enqueue_script( 'wpcp-magnific-popup' );
			}
			wp_enqueue_script( 'wpcp-magnific-config' );
		}

		// Carousel Classes.
		$carousel_classes = 'wpcp-carousel-section sp-wpcp-' . $post_id;
		if ( 'image-carousel' === $carousel_type ) {
			$carousel_classes .= ' wpcp-image-carousel';
		} elseif ( 'video-carousel' === $carousel_type ) {
			$carousel_classes .= ' wpcp-video-carousel';
		} elseif ( 'post-carousel' === $carousel_type ) {
			$carousel_classes .= ' wpcp-post-carousel';
		} elseif ( 'content-carousel' === $carousel_type ) {
			$carousel_classes .= ' wpcp-content-carousel';
		} elseif ( 'product-carousel' === $carousel_type ) {
			$carousel_classes .= ' wpcp-product-carousel';
		}

		// Overlay class.
		if ( 'with_overlay' === $wpcp_post_detail ) {
			$carousel_classes .= ' detail-with-overlay';
			if ( 'lower' === $wpcp_overlay_position ) {
				$carousel_classes .= ' overlay-lower';
			}
			if ( 'on_hover' === $wpcp_overlay_visibility ) {
				$carousel_classes .= ' overlay-on-hover';
			}
		} elseif ( 'on_right' === $wpcp_post_detail ) {
			$carousel_classes .= ' detail-on-right';
		} elseif ( 'on_left' === $wpcp_post_detail ) {
			$carousel_classes .= ' detail-on-left';
		} elseif ( 'top' === $wpcp_post_detail ) {
			$carousel_classes .= ' detail-on-top';
		}

		// Preloader classes.
		if ( $preloader ) {
			wp_enqueue_script( 'wpcp-preloader' );
			$carousel_classes .= ' wpcp-preloader';
		}

		// Navigation arrow classes.
		if ( 'hide' !== $wpcp_arrows && 'ticker' !== $carousel_mode ) {
			switch ( $arrow_position ) {
				case 'top_right':
					$carousel_classes .= ' nav-top-right';
					break;
				case 'top_center':
					$carousel_classes .= ' nav-top-center';
					break;
				case 'top_left':
					$carousel_classes .= ' nav-top-left';
					break;
				case 'bottom_left':
					$carousel_classes .= ' nav-bottom-left';
					break;
				case 'bottom_center':
					$carousel_classes .= ' nav-bottom-center';
					break;
				case 'bottom_right':
					$carousel_classes .= ' nav-bottom-right';
					break;
				case 'vertical_center':
					$carousel_classes .= ' nav-vertical-center';
					break;
				case 'vertical_center_inner':
					$carousel_classes .= ' nav-vertical-center-inner';
					break;
				case 'vertical_center_inner_hover':
					$carousel_classes .= ' nav-vertical-center-inner-hover';
					break;
			}
		}

		// Responsive screen sizes.
		$wpcp_screen_sizes = wpcp_get_option( 'wpcp_responsive_screen_setting' );
		$desktop_size      = isset( $wpcp_screen_sizes['desktop'] ) ? $wpcp_screen_sizes['desktop'] : '1200';
		$laptop_size       = isset( $wpcp_screen_sizes['laptop'] ) ? $wpcp_screen_sizes['laptop'] : '980';
		$tablet_size       = isset( $wpcp_screen_sizes['tablet'] ) ? $wpcp_screen_sizes['tablet'] : '736';
		$mobile_size       = isset( $wpcp_screen_sizes['mobile'] ) ? $wpcp_screen_sizes['mobile'] : '480';

		// RTL class.
		if ( 'ltr' === $carousel_direction ) {
			$carousel_classes .= ' sp-rtl';
		}
		$the_rtl = '';
		if ( 'ticker' === $carousel_mode ) {
			$auto_direction = 'rtl' === $carousel_direction ? 'next' : 'prev';
			if ( wpcp_get_option( 'wpcp_bx_js', true ) ) {
				wp_enqueue_script( 'wpcp-bx-slider' );
			}
			wp_enqueue_script( 'wpcp-bx-slider-config' );
			$carousel_classes .= ' wpcp-ticker';
			$wpcp_bx_config    = ' data-max-slides="' . $max_column . '" data-min-slides="' . $min_column . '" data-hover-pause="' . $pause_on_hover . '" data-speed="' . $ticker_speed . '" data-slide-width="' . $slide_width . '" data-slide-margin="' . $slide_margin . '" data-direction="' . $auto_direction . '"';
		} else {
			$the_rtl = ( 'ltr' === $carousel_direction ) ? ' dir="rtl"' : ' dir="ltr"';
			$rtl     = ( 'ltr' === $carousel_direction ) ? 'true' : 'false';
			if ( wpcp_get_option( 'wpcp_slick_js', true ) ) {
				wp_enqueue_script( 'wpcp-slick' );
			}
			wp_enqueue_script( 'wpcp-slick-config' );
			$carousel_classes  .= ' wpcp-standard';
			$wpcp_slick_options = 'data-slick=\'{ "accessibility":true, "centerMode":' . $center_mode . ', "centerPadding": "' . $center_padding . 'px", "swipeToSlide":' . $swipetoslide . ', "adaptiveHeight":' . $adaptive_height . ', "arrows":' . $arrows . ', "autoplay":' . $auto_play . ', "autoplaySpeed":' . $autoplay_speed . ', "dots":' . $dots . ', "infinite":' . $infinite . ', "speed":' . $speed . ', "pauseOnHover":' . $pause_on_hover . ', "slidesToScroll":' . $slide_to_scroll_lg_desktop . ', "slidesToShow":' . $column_lg_desktop . ', "responsive":[ { "breakpoint":' . $desktop_size . ', "settings": { "slidesToShow":' . $column_desktop . ', "slidesToScroll":' . $slide_to_scroll_desktop . ', "centerPadding": "' . $center_padding_desktop . 'px"} }, { "breakpoint":' . $laptop_size . ', "settings":{ "slidesToShow":' . $column_laptop . ', "slidesToScroll":' . $slide_to_scroll_laptop . ', "centerPadding": "' . $center_padding_laptop . 'px" } }, { "breakpoint":' . $tablet_size . ', "settings": { "slidesToShow":' . $column_tablet . ', "slidesToScroll":' . $slide_to_scroll_tablet . ', "centerPadding": "' . $center_padding_tablet . 'px" } }, {"breakpoint":' . $mobile_size . ', "settings":{ "slidesToShow":' . $column_mobile . ', "slidesToScroll":' . $slide_to_scroll_mobile . ', "arrows": ' . $arrows_mobile . ', "dots": ' . $dots_mobile . ', "centerPadding": "' . $center_padding_mobile . 'px" } } ], "rows":1, "rtl":' . $rtl . ', "variableWidth":' . $variable_width . ', "fade":' . $slider_animation . ', "lazyLoad": "' . $lazy_load_image . '", "swipe": ' . $swipe . ', "draggable": ' . $draggable . ' }\' data-arrowtype="' . $nav_icons . '"' . $the_rtl . '';
		}
		// Dynamic CSS.
		require WPCAROUSEL_PATH . '/public/dynamic-style.php';

		// Carousel Templates.
		if ( 'image-carousel' === $carousel_type ) {
			ob_start();
			echo $the_wpcp_dynamic_css;
			require WPCAROUSEL_PATH . '/public/templates/image-carousel.php';
			$html = ob_get_contents();
			ob_end_clean();

			return apply_filters( 'sp_wpcp_image_carousel', $html, $post_id );
		}
		if ( 'video-carousel' === $carousel_type ) {
			$upload_data   = get_post_meta( $post_id, 'sp_wpcp_upload_options', true );
			$video_sources = $upload_data['carousel_video_source'];
			$sp_urls       = $this->get_video_thumb_url( $video_sources );
			ob_start();
			echo $the_wpcp_dynamic_css;
			require WPCAROUSEL_PATH . '/public/templates/video-carousel.php';
			$html = ob_get_contents();
			ob_end_clean();

			return apply_filters( 'sp_wpcp_video_carousel', $html, $post_id );
		}
		if ( 'post-carousel' === $carousel_type ) {
			ob_start();
			echo $the_wpcp_dynamic_css;
			require WPCAROUSEL_PATH . '/public/templates/post-carousel.php';
			$html = ob_get_contents();
			ob_end_clean();

			return apply_filters( 'sp_wpcp_post_carousel', $html, $post_id );
		}
		if ( 'content-carousel' === $carousel_type ) {
			ob_start();
			echo $the_wpcp_dynamic_css;
			require WPCAROUSEL_PATH . '/public/templates/content-carousel.php';
			$html = ob_get_contents();
			ob_end_clean();

			return apply_filters( 'sp_wpcp_content_carousel', $html, $post_id );
		}
		if ( 'product-carousel' === $carousel_type ) {
			ob_start();
			echo $the_wpcp_dynamic_css;
			require WPCAROUSEL_PATH . '/public/templates/product-carousel.php';
			$html = ob_get_contents();
			ob_end_clean();

			return apply_filters( 'sp_wpcp_product_carousel', $html, $post_id );
		}
		return '';
	}

	/**
	 * The font variants for the Advanced Typography.
	 *
	 * @param string $sp_wpcp_font_variant The typography field ID with.
	 * @return string
	 * @since 3.0.0
	 */
	private function wpcp_the_font_variants( $sp_wpcp_font_variant ) {
		$filter_style  = 'normal';
		$filter_weight = '400';
		switch ( $sp_wpcp_font_variant ) {
			case '100':
				$filter_weight = '100';
				break;
			case '100italic':
				$filter_weight = '100';
				$filter_style  = 'italic';
				break;
			case '200':
				$filter_weight = '200';
				break;
			case '200italic':
				$filter_weight = '200';
				$filter_style  = 'italic';
				break;
			case '300':
				$filter_weight = '300';
				break;
			case '300italic':
				$filter_weight = '300';
				$filter_style  = 'italic';
				break;
			case '500':
				$filter_weight = '500';
				break;
			case '500italic':
				$filter_weight = '500';
				$filter_style  = 'italic';
				break;
			case '600':
				$filter_weight = '600';
				break;
			case '600italic':
				$filter_weight = '600';
				$filter_style  = 'italic';
				break;
			case '700':
				$filter_weight = '700';
				break;
			case '700italic':
				$filter_weight = '700';
				$filter_style  = 'italic';
				break;
			case '800':
				$filter_weight = '800';
				break;
			case '800italic':
				$filter_weight = '800';
				$filter_style  = 'italic';
				break;
			case '900':
				$filter_weight = '900';
				break;
			case '900italic':
				$filter_weight = '900';
				$filter_style  = 'italic';
				break;
			case 'italic':
				$filter_style = 'italic';
				break;
		}
		return 'font-style: ' . $filter_style . '; font-weight: ' . $filter_weight . ';';
	}

	/**
	 * Get video URL and Thumbnail.
	 *
	 * @param array $_video_sources video sources.
	 * @return statement
	 */
	private function get_video_thumb_url( array $_video_sources ) {
		$vid_url = array();
		foreach ( $_video_sources as $_video_source ) {
			$video_type            = $_video_source['carousel_video_source_type'];
			$wpcp_video_id         = $_video_source['carousel_video_source_id'];
			$wpcp_video_desc       = $_video_source['carousel_video_description'];
			$wpcp_video_thumb_url  = '';
			$wpcp_img_click_action = '';
			$wpcp_video_url        = '';
			switch ( $video_type ) {
				case 'youtube':
					$wpcp_video_url       = 'https://www.youtube.com/watch?v=' . $wpcp_video_id;
					$wpcp_video_thumb_url = 'https://img.youtube.com/vi/' . $wpcp_video_id . '/maxresdefault.jpg';
					break;
				case 'vimeo':
					$data_url = wp_remote_get( "https://vimeo.com/api/v2/video/$wpcp_video_id.json" );
					if ( isset( $data_url ) ) {
						$thumb_data           = json_decode( wp_remote_retrieve_body( $data_url ), true );
						$wpcp_video_thumb_url = isset( $thumb_data[0]['thumbnail_large'] ) ? $thumb_data[0]['thumbnail_large'] : null;
					}
					$wpcp_video_url = 'https://vimeo.com/' . $wpcp_video_id;
					break;
				case 'dailymotion':
					$wpcp_video_url       = 'https://www.dailymotion.com/video/' . $wpcp_video_id;
					$wpcp_video_thumb_url = 'https://dailymotion.com/thumbnail/video/' . $wpcp_video_id;
					break;
				case 'self_hosted':
					$wpcp_video_url       = $_video_source['carousel_video_source_upload'];
					$wpcp_video_thumb_url = $_video_source['carousel_video_source_thumb'];
					break;
				case 'image_only':
					$wpcp_video_thumb_url = $_video_source['carousel_video_source_thumb'];

					$wpcp_img_click_action = $_video_source['carousel_image_only_link_action'];
					break;
				default:
					$wpcp_video_thumb_url = 'https://via.placeholder.com/650x450';
			}
			$vid_url[] = array(
				'video_type'       => $video_type,
				'img_click_action' => $wpcp_img_click_action,
				'video_id'         => $wpcp_video_id,
				'video_url'        => $wpcp_video_url,
				'video_thumb_url'  => $wpcp_video_thumb_url,
				'video_desc'       => $wpcp_video_desc,
			);
		} // End foreach.
		return $vid_url;
	}
}
