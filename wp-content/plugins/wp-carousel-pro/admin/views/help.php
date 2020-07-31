<?php
/**
 * The help page for the WP Carousel Pro
 *
 * @package WP Carousel Pro
 * @subpackage wp-carousel-pro/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}  // if direct access.

/**
 * The help class for the WP Carousel Pro
 */
class WP_Carousel_Pro_Help {

	/**
	 * Wp Carousel Pro single instance of the class
	 *
	 * @var null
	 * @since 2.0
	 */
	protected static $_instance = null;

	/**
	 * Main WP_Carousel_Pro_Help Instance
	 *
	 * @since 2.0
	 * @static
	 * @see sp_wpcp_help()
	 * @return self Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Add admin menu.
	 *
	 * @return void
	 */
	public function help_admin_menu() {
		add_submenu_page(
			'edit.php?post_type=sp_wp_carousel', __( 'WP Carousel Help', 'wp-carousel-pro' ), __( 'Help', 'wp-carousel-pro' ), 'manage_options', 'wpcp_help', array(
				$this,
				'help_page_callback',
			)
		);
	}

	/**
	 * The WP Carousel Help Callback.
	 *
	 * @return void
	 */
	public function help_page_callback() {
		echo '
        <div class="wrap about-wrap sp-wpcp-help">
        <h1>' . esc_html__( 'Welcome to WordPress Carousel Pro! ', 'wp-carousel-pro' ) . '</h1>
        </div>
        <div class="wrap about-wrap sp-wpcp-help">
			<p class="about-text">' . esc_html__( 'Thank you for installing WordPress Carousel Pro! You\'re now running the most popular WordPress Carousel plugin.
This video will help you get started with the plugin.', 'wp-carousel-pro' ) . '</p>
			<div class="wp-badge"></div>

			<hr>

			<div class="headline-feature feature-video">
				<iframe width="560" height="315" src="https://www.youtube.com/embed/XMYYgFD7ZIA" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			</div>

			<hr>

			<div class="feature-section three-col">
				<div class="col">
					<div class="sp-wpcp-feature text-center">
						<i class="sp-wpcp-font-icon fa-life-ring"></i>
						<h3>' . esc_html__( 'Need any Assistance?', 'wp-carousel-pro' ) . '</h3>
						<p>' . esc_html__( 'Our Expert Support Team is always ready to help you out promptly.', 'wp-carousel-pro' ) . '</p>
						<a href="https://shapedplugin.com/support/" target="_blank" class="button button-primary">' . esc_html__( 'Contact Support', 'wp-carousel-pro' ) . '</a>
					</div>
				</div>
				<div class="col">
					<div class="sp-wpcp-feature text-center">
						<i class="sp-wpcp-font-icon fa-file-text"></i>
						<h3>' . esc_html__( 'Looking for Documentation?', 'wp-carousel-pro' ) . '</h3>
						<p>' . esc_html__( 'We have detailed documentation on every aspects of WordPress Carousel Pro.', 'wp-carousel-pro' ) . '</p>
						<a href="https://shapedplugin.com/docs/docs/wordpress-carousel-pro/" target="_blank" class="button button-primary">' . esc_html__( 'Documentation', 'wp-carousel-pro' ) . '</a>
					</div>
				</div>
				<div class="col">
					<div class="sp-wpcp-feature text-center">
						<i class="sp-wpcp-font-icon fa-thumbs-up"></i>
						<h3>' . esc_html__( 'Like This Plugin?', 'wp-carousel-pro' ) . '</h3>
						<p>' . esc_html__( 'If you like WordPress Carousel Pro, please leave us a 5 star rating.', 'wp-carousel-pro' ) . '</p>
						<a href="https://shapedplugin.com/plugin/wordpress-carousel-pro/#reviews" target="_blank" class="button button-primary">' . esc_html__( 'Rate The Plugin', 'wp-carousel-pro' ) . '</a>
					</div>
				</div>
			</div>

			<hr>

			<div class="about-wrap plugin-section">
				<div class="sp-plugin-section-title text-center">
					<h2>' . esc_html__( 'Take your website beyond the typical with more premium plugins!', 'wp-carousel-pro' ) . '</h2>
					<h4>' . esc_html__( 'Some more premium plugins are ready to make your website awesome.', 'wp-carousel-pro' ) . '</h4>
				</div>
				<div class="feature-section three-col">
					<div class="col">
						<div class="sp-wpcp-plugin">
							<img src="https://shapedplugin.com/wp-content/uploads/edd/2018/11/WooCommerce-Product-Slider-360x210.png"
							     alt="WooCommerce Product Slider Pro">
							<div class="sp-wpcp-plugin-content">
								<h3>' . esc_html__( 'WooCommerce Product Slider Pro', 'wp-carousel-pro' ) . '</h3>
								<p>' . esc_html__( 'WooCommerce Product Slider Pro is an amazing product slider to slide your Products in a tidy and professional way. It allows you to create easily attractive product slider on your website.', 'wp-carousel-pro' ) . '</p>
								<a href="https://shapedplugin.com/plugin/woocommerce-product-slider-pro/" class="button">' . esc_html__( 'View Details', 'wp-carousel-pro' ) . '</a>
							</div>
						</div>
					</div>

					<div class="col">
						<div class="sp-wpcp-plugin">
							<img src="https://shapedplugin.com/wp-content/uploads/edd/2018/05/Testimonial-Pro-360x210.png"
							     alt="Testimonial Pro">
							<div class="sp-wpcp-plugin-content">
								<h3>' . esc_html__( 'Testimonial Pro', 'wp-carousel-pro' ) . '</h3>
								<p>' . esc_html__( 'Testimonial Pro is a clean, easy-to-use and powerful testimonials management system for WordPress. This plugin will help you to display attractive and eye catching Unlimited testimonials.', 'wp-carousel-pro' ) . '</p>
								<a href="https://shapedplugin.com/plugin/testimonial-pro/" class="button">' . esc_html__( 'View Details', 'wp-carousel-pro' ) . '</a>
							</div>
						</div>
					</div>
					<div class="col">
						<div class="sp-wpcp-plugin">
							<img src="https://shapedplugin.com/wp-content/uploads/edd/2018/11/Thumb-360x210.png"
							     alt="WooCommerce Category Slider Pro">
							<div class="sp-wpcp-plugin-content">
								<h3>' . esc_html__( 'WooCommerce Category Slider Pro', 'wp-carousel-pro' ) . '</h3>
								<p>' . esc_html__( 'WooCommerce Category Slider Pro offers you to showcase your WooCommerce products categories aesthetically.', 'wp-carousel-pro' ) . '</p>
								<a href="https://shapedplugin.com/plugin/woocommerce-category-slider-pro/" class="button">' . esc_html__( 'View Details', 'wp-carousel-pro' ) . '</a>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>';
	}

	/**
	 * Add plugin action menu
	 *
	 * @param array  $links The action link.
	 * @param string $file The file.
	 *
	 * @return array
	 */
	public function add_plugin_action_links( $links, $file ) {

		if ( $file === WPCAROUSEL_BASENAME ) {
			$new_links = array(
				sprintf( '<a href="%s">%s</a>', admin_url( 'post-new.php?post_type=sp_wp_carousel' ), __( 'Create Carousel', 'wp-carousel-pro' ) ),
			);

			return array_merge( $new_links, $links );
		}

		return $links;
	}

}
