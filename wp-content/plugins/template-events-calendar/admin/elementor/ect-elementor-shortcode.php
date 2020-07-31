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
class EctElementorWidget extends Widget_Base {

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
		return __( 'The Events Calendar Shortcode', 'ect2' );
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
		return [ 'The Events Calendar Shortcode' ];
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
        $terms = get_terms(array(
			'taxonomy' => 'tribe_events_cat',
			'hide_empty' => false,
		));
		$ect_categories=array();
		$ect_categories['all'] = __('All Categories','cool-timeline');

		if (!empty($terms) || !is_wp_error($terms)) {
			foreach ($terms as $term) {
				$ect_categories[$term->slug] =$term->name ;
			}
		}
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'The Events Calendar Shortcode', 'ect2' ),
			]
		);
        $this->add_control(
			'event_categories',
			[
				'label' => __( 'Categories', 'cool-timeline' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'all',
				'options' => $ect_categories
				
			]
		);
		$this->add_control(
			'template',
			[
				'label' => __( 'Select Template', 'cool-timeline' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __( 'Default List Layout', 'cool-timeline' ),
					'timeline-view' => __( 'Timeline Layout', 'cool-timeline' ),
				
				]
				
			]
        );
        $this->add_control(
			'style',
			[
				'label' => __( 'Template Style', 'cool-timeline' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1' => __( 'Style 1', 'cool-timeline' ),
                    'style-2' => __( 'Style 2', 'cool-timeline' ),
                    'style-3' => __( 'Style 3', 'cool-timeline' ),
				
				]
				
			]
        );
        $this->add_control(
			'date_formats',
			[
				'label' => __( 'Date Format', 'cool-timeline' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
         
					'default' => __( 'Default (01 January 2019)', 'cool-timeline' ),
                    'MD,Y' => __( 'Md,Y (Jan 01, 2019)', 'cool-timeline' ),
                    'FD,Y' => __( 'Fd,Y (January 01, 2019)', 'cool-timeline' ),
                    'DM' => __( 'dM (01 Jan))', 'cool-timeline' ),
                    'DML' => __( 'dMl (01 Jan Monday)', 'cool-timeline' ),
                    'DF' => __( 'dF (01 January)', 'cool-timeline' ),
                    'MD' => __( 'Md (Jan 01)', 'cool-timeline' ),
                    'MD,YT' => __( 'Md,YT (Jan 01, 2019 8:00am-5:00pm)', 'cool-timeline' ),
                    'full' => __( 'Full (01 January 2019 8:00am-5:00pm)', 'cool-timeline' ),
                    'jMl' => __( 'jMl', 'cool-timeline' ),
                    'd.FY' => __( 'd.FY (01. January 2019)', 'cool-timeline' ),
                    'd.F' => __( 'd.F (01. January)', 'cool-timeline' ),
                    'ldF' => __( 'ldF (Monday 01 January)', 'cool-timeline' ),
                    'Mdl' => __( 'Mdl (Jan 01 Monday)', 'cool-timeline' ),
                    'd.Ml' => __( 'd.Ml (01. Jan Monday)', 'cool-timeline' ),
                    'dFT' => __( 'dFT (01 January 8:00am-5:00pm)', 'cool-timeline' ),
                ],
			]
        );
        $this->add_control(
                'time',
                [
                    'label' => __( 'Show Events', 'cool-timeline' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'future',
                    'options' => [
                        'future' => __( 'Upcoming Events', 'cool-timeline' ),
                        'past' => __( 'Past Events', 'cool-timeline' ),
                        'all' => __( 'All (Upcoming + Past)', 'cool-timeline' ),
                    
                    ]
                ]
            );
        $this->add_control(
                'order',
                [
                    'label' => __( 'Events Order', 'cool-timeline' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'ASC',
                    'options' => [
                        'ASC' => __( 'ASC', 'cool-timeline' ),
                        'DESC' => __( 'DESC', 'cool-timeline' ),
                ]
                ]
            ); 
            $this->add_control(
                'venue',
                [
                    'label' => __( 'Hide Venue', 'cool-timeline' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'no',
                    'options' => [
                        'no' => __( 'NO', 'cool-timeline' ),
                        'yes' => __( 'Yes', 'cool-timeline' ),
                    ]
                ]
            );
            $this->add_control(
                'sharebutton',
                [
                    'label' => __( 'Enable Social Share Buttons?', 'cool-timeline' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'no',
                    'options' => [
                        'no' => __( 'NO', 'cool-timeline' ),
                        'yes' => __( 'Yes', 'cool-timeline' ),
                    ]
                ]
            );
            $this->add_control(
			'limit',
			[
				'label' => __( 'Limit the events', 'cool-timeline' ),
				'type' => Controls_Manager::TEXT,
				'default' => '10',
				
			]
        );
        $this->add_control(
			'start_date',
			[
				'label' => __( 'Start Date | format(YY-MM-DD)', 'cool-timeline' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				
			]
        );
        $this->add_control(
			'end_date',
			[
				'label' => __( 'End Date | format(YY-MM-DD)', 'cool-timeline' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				
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
		$layout=isset($settings['template'])?$settings['template']:"default";
        $style = isset($settings['style'])?$settings['style']:'style-1';
        $date_format=isset($settings['date_formats'])?$settings['date_formats']:"default";
        $start_date = isset( $settings['start_date'] )? $settings['start_date']: '';
        $end_date = isset( $settings['end_date'] )? $settings['end_date']: '';
        $venue=isset($settings['venue'])?$settings['venue']:"venue";
        $sharebutton=isset($settings['sharebutton'])?$settings['sharebutton']:"no";
        $number_of_events=isset($settings['limit'])?$settings['limit']:"10";
        $order=isset($settings['order'])?$settings['order']:"ASC";
        $time=isset($settings['time'])?$settings['time']:"future";
		$ect_categories = isset($settings['event_categories'])?$settings['event_categories']:"all";
		$shortcode = '[events-calendar-templates category="'.$ect_categories.'" template="'.$layout.'" style="'.$style.'" date_format="'.$date_format.'" start_date="'.$start_date.'" end_date="'.$end_date.'" limit="'.$number_of_events.'" order="'.$order.'" hide-venue="'.$venue.'" socialshare="'.$sharebutton.'" time="'.$time.'"]';
		echo'<div class="ect-elementor-shortcode ect-free-addon">';
		 if( is_admin() ){
			echo "<strong>This preview may be a little bit different from actual view</strong><br/>";
		 }
        echo do_shortcode($shortcode);
        echo'</div>';
	}
}