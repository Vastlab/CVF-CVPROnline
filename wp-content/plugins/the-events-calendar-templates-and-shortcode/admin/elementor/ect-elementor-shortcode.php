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
		return 'ect-addons';
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
		return __( 'Events Calendar Shortcode and Templates', 'ect2' );
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
		$tags =  get_terms(array(
			'taxonomy' => 'post_tag',
			'hide_empty' => false,
		));
		
		$ect_tags=array();
		$ect_tags[''] = __('Select All Tags','cool-timeline');

		if (!empty($tags) || !is_wp_error($tags)) {
			foreach ($tags as $tag) {
	
				$ect_tags[$tag->slug] =$tag->name ;
				
			}
		}
		
	/**
               *  Get organizer name
               */
              $args = get_posts(array(
                'post_status'=>'publish',
                'post_type'=>'tribe_organizer',
                 'posts_per_page'=>-1
              ));
              
              $ect_org_details=array();
              $ect_org_details[''] = 'all';
              if (!empty($args) || !is_wp_error($args)) {
                foreach ($args as $term) {
                  
                  $ect_org_details[$term->ID] =$term->post_name ;
                }
              }
              /**
               * Get venue detail
               */
              $get_venue = get_posts(array(
                'post_status'=>'publish',
                'post_type'=>'tribe_venue',
                 'posts_per_page'=>-1
              ));
              
              $ect_venue_details=array();
              $ect_venue_details[''] = 'all';
            
              if (!empty($get_venue) || !is_wp_error($get_venue)) {
                foreach ($get_venue as $venues) {
                 
                  $ect_venue_details[$venues->ID] =$venues->post_name ;
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
					'carousel-view' => __( 'Carousel (carousel-view)', 'cool-timeline' ),
					'grid-view' => __( 'Grid (grid-view)', 'cool-timeline' ),
					'slider-view' => __( 'Slider (slider-view)', 'cool-timeline' ),
					'masonry-view' => __( 'Masonry Layout(Categories Filters)', 'cool-timeline' ),
					'accordion-view' => __( 'Toggle List(accordion-view)', 'cool-timeline' ),
				
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
				'label' => __( 'Date formats', 'cool-timeline' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
         
					'default' => __( 'Default (01 January 2019)', 'cool-timeline' ),
                    'MD,Y' => __( 'Md,Y (Jan 01, 2019)', 'cool-timeline' ),
                    'FD,Y' => __( 'Fd,Y (January 01, 2019)', 'cool-timeline' ),
                    'DM' => __( 'dM (01 Jan))', 'cool-timeline' ),
                    'FD' => __( 'FD (January 01)', 'cool-timeline' ),
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
                    'label' => __( 'Events Time', 'cool-timeline' ),
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
                'hidevenue',
                [
                    'label' => __( 'Hide Events Venue', 'cool-timeline' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'no',
                    'options' => [
                        'no' => __( 'NO', 'cool-timeline' ),
                        'yes' => __( 'Yes', 'cool-timeline' ),
                    ]
                ]
			);
			// $this->add_control(
            //     'venue',
            //     [
            //         'label' => __( 'Hide Venue', 'cool-timeline' ),
            //         'type' => Controls_Manager::SELECT,
            //         'default' => 'no',
            //         'options' => [
            //             'no' => __( 'NO', 'cool-timeline' ),
            //             'yes' => __( 'Yes', 'cool-timeline' ),
            //         ]
            //     ]
			// );
			$this->add_control(
                'featured-only',
                [
                    'label' => __( 'Show Only Featured Events', 'cool-timeline' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'false',
                    'options' => [
                        'false' => __( 'NO', 'cool-timeline' ),
                        'true' => __( 'Yes', 'cool-timeline' ),
                    ]
                ]
			);
			$this->add_control(
                'tags',
                [
                    'label' => __( 'Select Tag (* Events by tag.)', 'cool-timeline' ),
                    'type' => Controls_Manager::SELECT,
                    // 'default' => 'no',
                    'options' => $ect_tags,
                    
                ]
			);
			$this->add_control(
                'organizers',
                [
                    'label' => __( 'Select Organizer<br> (*Events by organizer.)', 'cool-timeline' ),
                    'type' => Controls_Manager::SELECT,
                    // 'default' => 'no',
                    'options' => $ect_org_details,
                    
                ]
			);
			$this->add_control(
                'venues',
                [
                    'label' => __( 'Select Venue<br>(* Events by venue.)', 'cool-timeline' ),
                    'type' => Controls_Manager::SELECT,
                    // 'default' => 'no',
                    'options' => $ect_venue_details,
                    
                ]
			);
			$this->add_control(
                'columns',
                [
                    'label' => __( 'Columns', 'cool-timeline' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => '2',
                    'options' => [
                        '2' => __( '2', 'cool-timeline' ),
						'3' => __( '3', 'cool-timeline' ),
						'4' => __( '4', 'cool-timeline' ),
						'6' => __( '6', 'cool-timeline' ),
					],
					'condition'   => [
						'template'   => [
							'grid-view',
							'masonry-view',
							'carousel-view'
					],
					]
                ]
            );
			$this->add_control(
                'autoplay',
                [
                    'label' => __( 'AutoPlay (* For slide function only.)', 'cool-timeline' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'true',
                    'options' => [
                        'true' => __( 'True', 'cool-timeline' ),
						'false' => __( 'False', 'cool-timeline' ),
					
					],
					'condition'   => [
						'template'   => [
							'slider-view',
							'carousel-view'
					],
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
        $venue=isset($settings['hidevenue'])?$settings['hidevenue']:"no";
        $sharebutton=isset($settings['sharebutton'])?$settings['sharebutton']:"no";
        $number_of_events=isset($settings['limit'])?$settings['limit']:"10";
        $order=isset($settings['order'])?$settings['order']:"ASC";
		$time=isset($settings['time'])?$settings['time']:"future";
		$columns=isset($settings['columns'])?$settings['columns']:"2";
        $venues=isset($settings['venues'])?$settings['venues']:"";
		$organizers=isset($settings['organizers'])?$settings['organizers']:"";
		$autoplay = isset($settings['autoplay'])?$settings['autoplay']:"false";
		$tag = isset($settings['tags'])?$settings['tags']:"";
		$featured= isset($settings['featured-only'])?$settings['featured-only']:"";
		$ect_categories = isset($settings['event_categories'])?$settings['event_categories']:"all";
		$slider_pp_id='ect-'.$layout.'-'.$style.rand(1,10);
		 
		//  ect_load_assets($layout,$style,$slider_pp_id,$autoplay, $columns);
		$shortcode = '[events-calendar-templates category="'.$ect_categories.'" template="'.$layout.'" style="'.$style.'" date_format="'.$date_format.'" start_date="'.$start_date.'" end_date="'.$end_date.'" limit="'.$number_of_events.'" order="'.$order.'" hide-venue="'.$venue.'" socialshare="'.$sharebutton.'" time="'.$time.'" columns="'.$columns.'" venues="'.$venues.'" organizers="'.$organizers.'" autoplay="'.$autoplay.'" tags="'.$tag.'" featured-only="'.$featured.'"]';
		echo'<div class="ect-elementor-shortcode ect-free-addon">';
		 if( is_admin() ){
		
		echo "<strong>It is only a shortcode builder. Kindly update/publish the page and check the actually events layout on front-end</strong><br/>";
		  }
        echo $shortcode;
        echo'</div>';
	}
}