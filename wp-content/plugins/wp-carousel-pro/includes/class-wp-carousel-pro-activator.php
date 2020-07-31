<?php
/**
 * Fired during plugin activation
 *
 * @link       https://shapedplugin.com
 * @since      3.0.0
 *
 * @package    WP_Carousel_Pro
 * @subpackage WP_Carousel_Pro/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      3.0.0
 * @package    WP_Carousel_Pro
 * @subpackage WP_Carousel_Pro/includes
 * @author     ShapedPlugin<shapedplugin@gmail.com>
 */
class WP_Carousel_Pro_Activator {

	/**
	 * The carousels.
	 *
	 * @var array
	 */
	private $carousels;

	/**
	 * WP Carousel Pro activator.
	 *
	 * Deactivate the free version during the activation of the WP Carousel Pro.
	 *
	 * @since  3.0.0
	 * @return void
	 */
	public static function activate() {
		deactivate_plugins( 'wp-carousel-free/wp-carousel-free.php' );
	}
}
