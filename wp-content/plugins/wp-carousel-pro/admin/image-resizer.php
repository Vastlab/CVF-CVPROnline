<?php
/**
 * The image resizer class
 *
 * @package WP Carousel Pro
 */

if ( ! class_exists( 'Wpcp_Aq_Resize' ) ) {
	/**
	 * The Aq Exception class.
	 */
	class Wpcp_Aq_Exception extends Exception {}

	/**
	 * The Aq Resizer class
	 *
	 * @since 3.0.0
	 */
	class Wpcp_Aq_Resize {
		/**
		 * The singleton instance
		 *
		 * @var mixed $instance The instance.
		 */
		private static $instance = null;
		/**
		 * Should an Wpcp_Aq_Exception be thrown on error?
		 * If false (default), then the error will just be logged.
		 *
		 * @var mixed $throwOnError
		 */
		public $throwOnError = false;
		/**
		 * No initialization allowed
		 */
		private function __construct() {}
		/**
		 * No cloning allowed
		 */
		private function __clone() {}
		/**
		 * For your custom default usage you may want to initialize an Wpcp_Aq_Resize object by yourself and then have own defaults
		 */
		public static function get_instance() {
			if ( null == self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}
		/**
		 * Process funcion.
		 *
		 * @param string  $url The image URL.
		 * @param integer $width Width of the image.
		 * @param integer $height Height of the image.
		 * @param boolean $crop Crop the image.
		 * @param boolean $single single.
		 * @param boolean $upscale Upscale.
		 * @return mix
		 *
		 * @throws Wpcp_Aq_Exception The exception.
		 */
		public function process( $url, $width = null, $height = null, $crop = null, $single = true, $upscale = false ) {
			try {
				// Validate inputs.
				if ( ! $url ) {
					throw new Wpcp_Aq_Exception( '$url parameter is required' );
				}
				if ( ! $width ) {
					throw new Wpcp_Aq_Exception( '$width parameter is required' );
				}
				// Caipt'n, ready to hook.
				if ( true === $upscale ) {
					add_filter( 'image_resize_dimensions', array( $this, 'aq_upscale' ), 10, 6 );
				}
				// Define upload path & dir.
				$upload_info = wp_upload_dir();
				$upload_dir  = $upload_info['basedir'];
				$upload_url  = $upload_info['baseurl'];

				$http_prefix     = 'http://';
				$https_prefix    = 'https://';
				$relative_prefix = '//'; // The protocol-relative URL.

				/**
				 * If the $url scheme differs from $upload_url scheme, make them match if the schemes differ, images don't show up.
				 */
				if ( ! strncmp( $url, $https_prefix, strlen( $https_prefix ) ) ) {
					// if url begins with https:// make $upload_url begin with https:// as well.
					$upload_url = str_replace( $http_prefix, $https_prefix, $upload_url );
				} elseif ( ! strncmp( $url, $http_prefix, strlen( $http_prefix ) ) ) {
					// if url begins with http:// make $upload_url begin with http:// as well.
					$upload_url = str_replace( $https_prefix, $http_prefix, $upload_url );
				} elseif ( ! strncmp( $url, $relative_prefix, strlen( $relative_prefix ) ) ) {
					// if url begins with make $upload_url begin with as well.
					$upload_url = str_replace(
						array(
							0 => "$http_prefix",
							1 => "$https_prefix",
						),
						$relative_prefix,
						$upload_url
					);
				}

				// Check if $img_url is local.
				if ( false === strpos( $url, $upload_url ) ) {
					throw new Wpcp_Aq_Exception( 'Image must be local: ' . $url );
				}
				// Define path of image.
				$rel_path = str_replace( $upload_url, '', $url );
				$img_path = $upload_dir . $rel_path;
				// Check if img path exists, and is an image indeed.
				if ( ! file_exists( $img_path ) || ! getimagesize( $img_path ) ) {
					throw new Wpcp_Aq_Exception( 'Image file does not exist (or is not an image): ' . $img_path );
				}
				// Get image info.
				$info                    = pathinfo( $img_path );
				$ext                     = $info['extension'];
				list( $orig_w, $orig_h ) = getimagesize( $img_path );
				// Get image size after cropping.
				$dims  = image_resize_dimensions( $orig_w, $orig_h, $width, $height, $crop );
				$dst_w = $dims[4];
				$dst_h = $dims[5];
				// Return the original image only if it exactly fits the needed measures.
				if ( ! $dims || ( ( ( null === $height && $orig_w == $width ) xor ( null === $width && $orig_h == $height ) ) xor ( $height == $orig_h && $width == $orig_w ) ) ) {
					$img_url = $url;
					$dst_w   = $orig_w;
					$dst_h   = $orig_h;
				} else {
					// Use this to check if cropped image already exists, so we can return that instead.
					$suffix       = "{$dst_w}x{$dst_h}";
					$dst_rel_path = str_replace( '.' . $ext, '', $rel_path );
					$destfilename = "{$upload_dir}{$dst_rel_path}-{$suffix}.{$ext}";
					if ( ! $dims || ( true == $crop && false == $upscale && ( $dst_w < $width || $dst_h < $height ) ) ) {
						// Can't resize, so return false saying that the action to do could not be processed as planned.
						throw new Wpcp_Aq_Exception( 'Unable to resize image because image_resize_dimensions() failed' );
					} elseif ( file_exists( $destfilename ) && getimagesize( $destfilename ) ) {
						// Else check if cache exists.
						$img_url = "{$upload_url}{$dst_rel_path}-{$suffix}.{$ext}";
					} else {
						// Else, we resize the image and return the new resized image url.
						$editor = wp_get_image_editor( $img_path );
						if ( is_wp_error( $editor ) || is_wp_error( $editor->resize( $width, $height, $crop ) ) ) {
							throw new Wpcp_Aq_Exception( 'Unable to get WP_Image_Editor: ' . $editor->get_error_message() . ' (is GD or ImageMagick installed?)' );
						}
						$resized_file = $editor->save();
						if ( ! is_wp_error( $resized_file ) ) {
							$resized_rel_path = str_replace( $upload_dir, '', $resized_file['path'] );
							$img_url          = $upload_url . $resized_rel_path;
						} else {
							throw new Wpcp_Aq_Exception( 'Unable to save resized image file: ' . $editor->get_error_message() );
						}
					}
				}
				// Okay, leave the ship.
				if ( true === $upscale ) {
					remove_filter( 'image_resize_dimensions', array( $this, 'aq_upscale' ) );
				}
				// Return the output.
				if ( $single ) {
					// str return.
					$image = $img_url;
				} else {
					// array return.
					$image = array(
						0 => $img_url,
						1 => $dst_w,
						2 => $dst_h,
					);
				}
				return $image;
			} catch ( Wpcp_Aq_Exception $ex ) {
				error_log( 'Wpcp_Aq_Resize.process() error: ' . $ex->getMessage() );
				if ( $this->throwOnError ) {
					// Bubble up exception.
					throw $ex;
				} else {
					// Return false, so that this patch is backwards-compatible.
					return false;
				}
			}
		}
		/**
		 * Callback to overwrite WP computing of thumbnail measures
		 *
		 * @param mix     $default The default value.
		 * @param integer $orig_w Original width.
		 * @param integer $orig_h Original Height.
		 * @param integer $dest_w Desired width.
		 * @param integer $dest_h Desired height.
		 * @param boolean $crop Crop boolean.
		 * @return statement
		 */
		public function aq_upscale( $default, $orig_w, $orig_h, $dest_w, $dest_h, $crop ) {
			if ( ! $crop ) {
				return null; // Let the WordPress default function handle this.
			}
			// Here is the point we allow to use larger image size than the original one.
			$aspect_ratio = $orig_w / $orig_h;
			$new_w        = $dest_w;
			$new_h        = $dest_h;
			if ( ! $new_w ) {
				$new_w = intval( $new_h * $aspect_ratio );
			}
			if ( ! $new_h ) {
				$new_h = intval( $new_w / $aspect_ratio );
			}
			$size_ratio = max( $new_w / $orig_w, $new_h / $orig_h );
			$crop_w     = round( $new_w / $size_ratio );
			$crop_h     = round( $new_h / $size_ratio );
			$s_x        = floor( ( $orig_w - $crop_w ) / 2 );
			$s_y        = floor( ( $orig_h - $crop_h ) / 2 );
			return array( 0, 0, (int) $s_x, (int) $s_y, (int) $new_w, (int) $new_h, (int) $crop_w, (int) $crop_h );
		}
	}
}
if ( ! function_exists( 'the_wpcp_aq_resize' ) ) {
	/**
	 * This is just a tiny wrapper function for the class above so that there is no
	 * need to change any code in your own WP themes. Usage is still the same :)
	 *
	 * @param string  $url The image URL.
	 * @param integer $width Width of the image.
	 * @param integer $height Height of the image.
	 * @param boolean $crop Crop the image.
	 * @param boolean $single single.
	 * @param boolean $upscale Upscale.
	 * @return statement
	 */
	function the_wpcp_aq_resize( $url, $width = null, $height = null, $crop = null, $single = true, $upscale = false ) {
		/* WPML Fix */
		if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
			global $sitepress;
			$url = $sitepress->convert_url( $url, $sitepress->get_default_language() );
		}
		/* WPML Fix */
		$wpcp_aq_resize = Wpcp_Aq_Resize::get_instance();
		return $wpcp_aq_resize->process( $url, $width, $height, $crop, $single, $upscale );
	}
}
