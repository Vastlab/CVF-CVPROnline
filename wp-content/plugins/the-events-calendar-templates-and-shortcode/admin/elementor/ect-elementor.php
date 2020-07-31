<?php


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


// Add a custom category for panel widgets
 add_action( 'elementor/init', function() {
    \Elementor\Plugin::$instance->elements_manager->add_category( 
    	'The Events Calendar Shortcode and Templates Addon',                 // the name of the category
   	[
    		'title' => esc_html__( 'Events Calendar Shortcode and Templates', 'ect2' ),
    		'icon' => 'fa fa-header', //default icon
    	],
    	1 // position
    );
 } );

/**
 * Main Plugin Class
 *
 * Register new elementor widget.
 *
 * @since 1.0.0
 */
class EctElementor {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function __construct() {
		$this->ect_add_actions();
	}

	/**
	 * Add Actions
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function ect_add_actions() {
		// $load_assets = ect_load_assets();
		// var_Dump($load_assets);
		add_action( 'elementor/widgets/widgets_registered', array($this, 'ect_on_widgets_registered' ));

		// add_action( 'elementor/preview/enqueue_styles', function() {
		// 	wp_enqueue_style('ect-list-view-styles');
		// 	wp_enqueue_script('ect-common-scripts');
		// 	wp_enqueue_style('ect-timeline-view-styles');
                
		// 	wp_enqueue_script('ect-common-scripts');
		// 	wp_enqueue_style('ect-slider-slick');
		// 	wp_enqueue_style('ect-slider-view-styles');
		// 	wp_enqueue_script('ect-slider-slick-js');
		// 	wp_enqueue_style('ect-grid-view-bootstrap');
		// 		wp_enqueue_style('ect-grid-view-styles');
		// 		wp_enqueue_script('ect-common-scripts');
		// 		wp_enqueue_script('ect-common-scripts');
		// 		wp_enqueue_style('ect-grid-view-bootstrap');
		// 		wp_enqueue_style('ect-grid-view-styles');
		// 		wp_enqueue_script('masonry-lib');
		// 		wp_enqueue_script('imagesloaded');		
		// 		wp_enqueue_script('masonry.filter');
		// 		wp_enqueue_script('ect-masonry-js');	
		// 		wp_enqueue_script('ect-common-scripts');
		// 		wp_enqueue_style('ect-slider-slick');
		// 		wp_enqueue_style('ect-carousel-view-styles');
		// 		wp_enqueue_script('ect-slider-slick-js');
		// 		wp_enqueue_style('ect-accordion-view-styles');
		// 		wp_enqueue_style('ect-collapse-bootstrap');
		// 		wp_enqueue_style('ect-custom-icons');
        //         wp_enqueue_script('ect-common-scripts');
		// 		wp_enqueue_script('ect-sharebutton');
		// 		wp_enqueue_style('ect-sharebutton-css');

						
		// } );
	}

	/**
	 * On Widgets Registered
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function ect_on_widgets_registered() {
		$this->ect_includes();
		$this->ect_register_widget();
	}

	/**
	 * Includes
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function ect_includes() {
		require __DIR__ . '/ect-elementor-shortcode.php';
		require __DIR__ . '/ect-elementor-calender-shortcode.php';
	}

	/**
	 * Register Widget
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function ect_register_widget() {
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new EctElementorWidget() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new EctCalendarElementorWidget() );
	}
}

 new EctElementor();