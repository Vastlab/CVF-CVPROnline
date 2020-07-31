<?php


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


// Add a custom category for panel widgets
 add_action( 'elementor/init', function() {
    \Elementor\Plugin::$instance->elements_manager->add_category( 
    	'The Events Calendar Shortcode and Templates Addon',                 // the name of the category
   	[
    		'title' => esc_html__( 'The Events Calendar Shortcode and Templates Addon', 'ect2' ),
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
		add_action( 'elementor/widgets/widgets_registered', array($this, 'ect_on_widgets_registered' ));

		add_action( 'elementor/preview/enqueue_styles', function() {
			wp_enqueue_style('ect-list-styles');
			wp_enqueue_style('ect-common-styles');	
			wp_enqueue_style('ect-sharebutton');
			wp_enqueue_style('ect-sharebutton-css');
			wp_enqueue_style('ect-timeline-styles');			
		} );
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
	}
}

 new EctElementor();