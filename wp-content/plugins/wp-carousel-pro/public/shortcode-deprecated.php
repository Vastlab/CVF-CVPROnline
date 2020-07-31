<?php
/**
 * Registering shortcode
 *
 * @package WP Carousel Pro
 */

if ( ! function_exists( 'sp_wp_carousel_pro_shortcode' ) ) {

	/**
	 * Shortcode main function.
	 *
	 * @param mixed $attr The attributes of the shortcode.
	 * @return statement
	 */
	function sp_wp_carousel_pro_shortcode( $attr ) {
		extract(
			shortcode_atts(
				array(
					'ids'                        => '',
					'items'                      => '5',
					'items_desktop'              => '3',
					'items_desktop_small'        => '3',
					'items_tablet'               => '2',
					'items_mobile'               => '1',
					'items_margin'               => '10px',
					'slides_to_scroll'           => '1',
					'slides_to_scroll_on_mobile' => '1',
					'auto_play'                  => 'true',
					'auto_play_speed'            => '3000',
					'speed'                      => '300',
					'infinite_loop'              => 'true',
					'bullets'                    => 'false',
					'bullets_color'              => '#cccccc',
					'bullets_active_color'       => '#5dc8d0',
					'nav'                        => 'true',
					'nav_color'                  => '#888888',
					'nav_hover_color'            => '#5dc8d0',
					'nav_border_color'           => '#fff',
					'adaptive_height'            => 'false',
					'pause_on_hover'             => 'true',
					'draggable'                  => 'true',
					'fade'                       => 'false',
					'ticker'                     => 'false',
					'rtl'                        => 'false',
					'caption'                    => '',
					'caption_color'              => '#333333',
					'description'                => 'false',
					'description_color'          => '#333333',
					'overlay'                    => 'false',
					'overlay_bg'                 => '#0006',
					'width'                      => '600',
					'height'                     => '450',
					'crop'                       => 'false',
					'grayscale'                  => '',
					'grayscale_on_mobile_tablet' => 'true',
					'link'                       => '',
					'link_target'                => '_blank',
					'title_attr'                 => '',
					'lightbox'                   => 'false',
					'group'                      => 'gallery',
				), $attr, 'gallery'
			)
		);

		$id = uniqid();
		global $post;
		static $instance = 0;
		$instance ++;
		if ( ! function_exists( 'get_match' ) ) {
			/**
			 * Helper function to return shortcode regex match on instance occurring on page or post.
			 *
			 * @param pattern $regex The regex pattern.
			 * @param subject $content The subject to match.
			 * @param match   $instance The match.
			 * @return statement
			 */
			function get_match( $regex, $content, $instance ) {
				preg_match_all( $regex, $content, $matches );

				return $matches[1][ $instance ];
			}
		}

		// Extract the shortcode arguments from the $page or $post.
		$shortcode_args = shortcode_parse_atts( get_match( '/\[wcpgallery\s(.*)\]/isU', $post->post_content, $instance - 1 ) );

		// Get the ids specified in the shortcode call.
		if ( is_array( $ids ) ) {
			$ids = $shortcode_args['ids'];
		}

		// Bullet CSS.
		if ( 'true' == $bullets ) {
			$the_nav_margin = '-40px';
		} else {
			$the_nav_margin = '-15px';
		}

		if ( 'true' == $nav ) {
			$carousel_area_padding = '0 40px';
		} else {
			$carousel_area_padding = '0';
		}

		// Ticker.
		if ( 'true' == $ticker ) {
			$auto_play_speeds = 0;
		} else {
			$auto_play_speeds = $auto_play_speed;
		}
		if ( 'true' == $ticker || 'true' == $fade ) {
			$animation = 'linear';
		} else {
			$animation = 'ease';
		}

		$overlay_class = '';
		if ( 'true' == $overlay ) {
			$overlay_class = ' detail-with-overlay overlay-lower';
		}
		echo "<style type='text/css'>
        #sp-wp-carousel-pro-id-$id .wpcp-all-captions .wpcp-image-caption {
            color: $caption_color;
        }
        #sp-wp-carousel-pro-id-$id .wpcp-all-captions .wpcp-image-description {
            color: $description_color;
        }
        #sp-wp-carousel-pro-id-$id.detail-with-overlay.overlay-lower .wpcp-all-captions {
            background: $overlay_bg;
        }
		#sp-wp-carousel-pro-id-$id.wpcp-carousel-section.nav-vertical-center .slick-prev, 
		#sp-wp-carousel-pro-id-$id.wpcp-carousel-section.nav-vertical-center .slick-next {
			margin-top: $the_nav_margin;
			border-color: $nav_border_color;
		}
		#sp-wp-carousel-pro-id-$id.wpcp-carousel-section .slick-prev i, 
		#sp-wp-carousel-pro-id-$id.wpcp-carousel-section .slick-next i{
			color: $nav_color;
		}
		#sp-wp-carousel-pro-id-$id.wpcp-carousel-section .slick-prev:hover, 
		#sp-wp-carousel-pro-id-$id.wpcp-carousel-section .slick-next:hover{
			color: $nav_hover_color;
		}
		#sp-wp-carousel-pro-id-$id.wpcp-carousel-section ul.slick-dots li button{
			background-color: $bullets_color;
		}
		#sp-wp-carousel-pro-id-$id.wpcp-carousel-section ul.slick-dots li.slick-active button{
			background-color: $bullets_active_color;
		}
		#sp-wp-carousel-pro-id-$id.wpcp-carousel-section .slick-slide{
			margin-right: $items_margin;
        }
        #sp-wp-carousel-pro-id-$id.wpcp-carousel-section .slick-list{
			margin-right: -$items_margin;
		}
		#sp-wp-carousel-pro-id-$id.wpcp-carousel-section .wpcp-single-item img{
			-webkit-transition: ease all 0.3s;
			-moz-transition: ease all 0.3s;
			-ms-transition: ease all 0.3s;
			-o-transition: ease all 0.3s;
			transition: ease all 0.3s;
			max-width: 100%;
		}
		#sp-wp-carousel-pro-id-$id.wpcp-carousel-section .wpcp-single-item .wcp_s_item_caption .wcp_s_item_overlay{
			background: none repeat scroll 0 0 $overlay_bg;
		}";

		/*
		 * Gray Scale
		 *
		 */
		if ( 'always_gray' == $grayscale ) {
			echo '
			#sp-wp-carousel-pro-id-' . $id . '.wpcp-carousel-section .wpcp-single-item img{
				filter: gray;
				-webkit-filter: grayscale(1);
				-o-filter: grayscale(1);
				filter: grayscale(1); /* Microsoft Edge and Firefox 35+ */
			}
		';
		}
		if ( 'gray_with_color' == $grayscale ) {
			echo '
			#sp-wp-carousel-pro-id-' . $id . '.wpcp-carousel-section .wpcp-single-item img{
				filter: gray;
				-webkit-filter: grayscale(1);
				-o-filter: grayscale(1);
				filter: grayscale(1); /* Microsoft Edge and Firefox 35+ */
			}
			#sp-wp-carousel-pro-id-' . $id . '.wpcp-carousel-section .wpcp-single-item:hover img{
				filter: none;
				-webkit-filter: grayscale(0);
				-o-filter: grayscale(0);
			}';
		}
		if ( 'gray_on_hover' == $grayscale ) {
			echo '
			#sp-wp-carousel-pro-id-' . $id . '.wpcp-carousel-section .wpcp-single-item:hover img{
				filter: gray;
				-webkit-filter: grayscale(1);
				-o-filter: grayscale(1);
				filter: grayscale(1); /* Microsoft Edge and Firefox 35+ */
			}
		';
		}
		if ( 'gray_with_color' == $grayscale && 'true' == $grayscale_on_mobile_tablet ) {
			echo '
		@media screen and (max-width: 736px) {
			#sp-wp-carousel-pro-id-' . $id . '.wpcp-carousel-section .wpcp-single-item img{
				filter: gray;
				-webkit-filter: grayscale(1);
				-o-filter: grayscale(1);
				filter: grayscale(1); /* Microsoft Edge and Firefox 35+ */
			}
		}
		';
		} elseif ( 'gray_with_color' == $grayscale && 'false' == $grayscale_on_mobile_tablet ) {
			echo '
		@media screen and (max-width: 736px) {
			#sp-wp-carousel-pro-id-' . $id . '.wpcp-carousel-section .wpcp-single-item img{
				filter: none;
				-webkit-filter: grayscale(0);
				-o-filter: grayscale(0);
			}
		}';
		}
		echo '</style>';

		echo "<script type='text/javascript'>
		jQuery(document).ready(function() {
		    jQuery('#sp-wp-carousel-pro-id-$id').slick({
					swipeToSlide: true,
			        infinite: " . $infinite_loop . ',
			        slidesToShow: ' . $items . ',
			        slidesToScroll: ' . $slides_to_scroll . ",
			        prevArrow: \"<div class='slick-prev'><i class='fa fa-angle-left'></i></div>\",
	                nextArrow: \"<div class='slick-next'><i class='fa fa-angle-right'></i></div>\",
			        dots: " . $bullets . ',
			        arrows: ' . $nav . ',
			        pauseOnHover: ' . $pause_on_hover . ',
			        autoplay: ' . $auto_play . ',
			        autoplaySpeed: ' . $auto_play_speeds . ',
			        speed: ' . $speed . ',
			        adaptiveHeight: ' . $adaptive_height . ',
			        draggable: ' . $draggable . ',
			        rtl: ' . $rtl . ",
			        cssEase: '" . $animation . "',
			        fade: " . $fade . ',
		            responsive: [
						    {
						      breakpoint: 1199,
						      settings: {
						        slidesToShow: ' . $items_desktop . '
						      }
						    },
						    {
						      breakpoint: 979,
						      settings: {
						        slidesToShow: ' . $items_desktop_small . '
						      }
						    },
						    {
						      breakpoint: 767,
						      settings: {
						        slidesToShow: ' . $items_tablet . '
						      }
						    },
						    {
						      breakpoint: 479,
						      settings: {
						        slidesToShow: ' . $items_mobile . ',
						        slidesToScroll: ' . $slides_to_scroll_on_mobile . "
						      }
						    }
						  ]
		        });
		 var carouselss = ('#sp-wp-carousel-pro-id-$id');
			jQuery(carouselss).magnificPopup({
				type: 'image',
				closeOnContentClick: false,
	            closeBtnInside: false,
	            mainClass: 'mfp-with-zoom mfp-img-mobile',
	            gallery: {
	                enabled: true
	            },
	            delegate: 'a.wcp-light-box',
	            zoom: {
	                enabled: true,
	                duration: 300, // don't foget to change the duration also in CSS
	                opener: function(element) {
	                    return element.find('img');
	                }
	            },
			});
		});
		</script>
		<div id='sp-wp-carousel-pro-id-$id' class='wpcp-carousel-section nav-vertical-center" . $overlay_class . "' style='padding:$carousel_area_padding;'>";
		$attachments = explode( ',', $ids );
		if ( is_array( $attachments ) && ! is_wp_error( $attachments ) ) :
			foreach ( $attachments as $attachment ) {
				$image_data = get_post( $attachment );
				if ( empty( $image_data ) ) {
					return;
				}
				$image_title       = $image_data->post_title;
				$image_caption     = $image_data->post_excerpt;
				$image_description = $image_data->post_content;
				$image_alt_title   = $image_data->_wp_attachment_image_alt;

				$image_linking_meta = wp_get_attachment_metadata( $attachment );
				$image_linking_urls = $image_linking_meta['image_meta'];
				$image_link         = ( ! empty( $image_linking_urls['wpcplinking'] ) ? esc_url( $image_linking_urls['wpcplinking'] ) : '' );
				$image_url          = wp_get_attachment_image_src( $attachment, 'full' );

				if ( ( ! empty( $width ) && $image_url[1] >= ! $width ) && ( ! empty( $height ) && $image_url[2] >= ! $height ) ) {
					$image_resize_url = the_wpcp_aq_resize( $image_url[0], $width, $height, $crop );
				}
				$image_src      = ! empty( $image_resize_url ) ? $image_resize_url : $image_url[0];
				$img_title_attr = '';
				if ( 'true' == $title_attr ) {
					$img_title_attr = 'title="' . $image_title . '"';
				}
				$image = sprintf( '<img src="%1$s" %2$s alt="%3$s">', $image_src, $img_title_attr, $image_alt_title );

				$img_caption     = sprintf( '<h2 style="color:' . $caption_color . '" class="wpcp-image-caption">%1$s</h2>', $image_caption );
				$img_description = sprintf( '<p class="wpcp-image-description">%1$s</p>', $image_description );

				if ( ( $caption && ! empty( $image_caption ) ) || ( $description && ! empty( $image_description ) ) ) {
					$all_captions = '<div class="wpcp-all-captions text-center">' . ( $caption && ! empty( $image_caption ) ? $img_caption : '' ) . ( $description && ! empty( $image_description ) ? $img_description : '' ) . '</div>';
				} else {
					$all_captions = '';
				}

				echo '<div class="wpcp-single-item">';
				if ( 'true' == $lightbox ) {
					echo sprintf( '<div class="wpcp-slide-image"><a class="wcp-light-box" href="%1$s" data-lightbox-gallery="%2$s">%3$s</a></div>%4$s', esc_url( $image_url[0] ), $group, $image, $all_captions );
				} elseif ( isset( $image_link ) && filter_var( $image_link, FILTER_VALIDATE_URL ) && ( 'false' !== $link ) ) {
					echo sprintf( '<div class="wpcp-slide-image"><a href="%1$s" target="%2$s">%3$s</a></div>%4$s', esc_url( $image_link ), $link_target, $image, $all_captions );
				} else {
					echo sprintf( '<div class="wpcp-slide-image">%1$s</div>%2$s', $image, $all_captions );
				}
				echo '</div>';
			} // End of foreach.s
	endif;
		echo '</div>';
	}

	add_shortcode( 'wcpgallery', 'sp_wp_carousel_pro_shortcode' );
}
