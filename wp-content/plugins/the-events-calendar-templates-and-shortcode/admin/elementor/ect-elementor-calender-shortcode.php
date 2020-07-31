<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor EctElementorWidget
 *
 * Elementor widget for EctElementorWidget
 *
 * @since 1.0.0
 */
class EctCalendarElementorWidget extends Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'ect-addon';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Events Calendar Layouts', 'ect2' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-calendar';
	}


	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'The Events Calendar Shortcode and Templates Addon' ];
	}

	/**
	 * Retrieve the list of scripts the widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	// public function get_script_depends() {
	// 	return [ 'ctla' ];
	// }

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function _register_controls() {
       
		 
		//  var_dump($ect_venue_details);
	
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'The Events Calendar Shortcode', 'ect2' ),
			]
		);

        $this->add_control(
			'date_formats',
			[
				'label' => __( 'Date formats', 'cool-timeline' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'd F Y',
				'options' => [
         
					'd F Y' => __( 'Default (01 January 2019)', 'cool-timeline' ),
                    'M D, Y' => __( 'Md,Y (Jan 01, 2019)', 'cool-timeline' ),
                    'F D, Y' => __( 'Fd,Y (January 01, 2019)', 'cool-timeline' ),
                    'D M' => __( 'dM (01 Jan))', 'cool-timeline' ),
                  
                    'D F' => __( 'dF (01 January)', 'cool-timeline' ),
                    'M D' => __( 'Md (Jan 01)', 'cool-timeline' ),
                    'F D' => __( 'Fd (January 01)', 'cool-timeline' ),
                    'j M l' => __( 'jMl (1 Jan Monday)', 'cool-timeline' ),
                    'd. F Y' => __( 'd.FY (01. January 2019)', 'cool-timeline' ),
                    'd. F' => __( 'd.F (01. January)', 'cool-timeline' ),
                    'd. M l' => __( 'd.Ml (01. Jan Monday)', 'cool-timeline' ),
                    'M d l' => __( 'Mdl (Jan 01 Monday)', 'cool-timeline' ),
                    'l d F' => __( 'ldF (Monday 01 January)', 'cool-timeline' ),
                    
                ],
			]
        );
      
			$this->add_control(
                'catFilter',
                [
                    'label' => __( 'Show Category Filter', 'cool-timeline' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'true',
                    'options' => [
                        'false' => __( 'NO', 'cool-timeline' ),
                        'true' => __( 'Yes', 'cool-timeline' ),
                    ]
                ]
			);
		
		
        $this->end_controls_section();
    }

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings();
		
        $date_format=isset($settings['date_formats'])?$settings['date_formats']:"default";
        $catFilter = isset( $settings['catFilter'] )? $settings['catFilter']: 'true';
      
		$shortcode = '[ect-calendar-layout date-format="'.$date_format.'" show-category-filter="'.$catFilter.'"]';
		echo'<div class="ect-elementor-shortcode ect-free-addon">';
		if( is_admin() ){
			
			echo "<strong>It is only a shortcode builder. Kindly update/publish the page and check the actually Calendar Layouts  on front-end</strong><br/>";
		}
		
        echo ($shortcode);
        echo'</div>';
	}
}