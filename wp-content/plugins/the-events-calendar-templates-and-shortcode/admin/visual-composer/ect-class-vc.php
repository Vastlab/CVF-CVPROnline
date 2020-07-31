<?php
if (!class_exists('EctVCAddon')) {

    class EctVCAddon
    {
        /**
         * The Constructor
         */
        public function __construct()
        {
            // We safely integrate with VC with this hook
            add_action( 'init', array($this, 'ect_vc_addon' ) );
        }

        function ect_vc_addon(){
          $ect_calendar_date_format = array(
             __( 'Default (01 January 2019)', 'cool-timeline' )=> 'd F Y',
             __( 'Md,Y (Jan 01, 2019)', 'cool-timeline' ) =>'M D, Y',
              __( 'Fd,Y (January 01, 2019)', 'cool-timeline' )=>'F D, Y',
             __( 'dM (01 Jan))', 'cool-timeline' ) =>'D M',
          
             __( 'dF (01 January)', 'cool-timeline' ) =>'D F',
              __( 'Md (Jan 01)', 'cool-timeline' )=>'M D',
              __( 'Fd (January 01)', 'cool-timeline' )=>'F D',
              __( 'jMl (1 Jan Monday)', 'cool-timeline' )=>'j M l',
              __( 'd.FY (01. January 2019)', 'cool-timeline' )=>'d. F Y',
              __( 'd.F (01. January)', 'cool-timeline' )=>'d. F',
              __( 'd.Ml (01. Jan Monday)', 'cool-timeline' )=>'d. M l',
             __( 'Mdl (Jan 01 Monday)', 'cool-timeline' ) =>'M d l',
              __( 'ldF (Monday 01 January)', 'cool-timeline' )=>'l d F',
          );
         
              /**
               *  Get organizer name
               */
              $args = get_posts(array(
                'post_status'=>'publish',
                'post_type'=>'tribe_organizer',
                 'posts_per_page'=>-1
              ));
              
              $ect_org_details=array();
              $ect_org_details['all'] = '';
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
              $ect_venue_details['all'] = '';
            
              if (!empty($get_venue) || !is_wp_error($get_venue)) {
                foreach ($get_venue as $venues) {
                 
                  $ect_venue_details[$venues->ID] =$venues->post_name ;
                }
              }
              
              $terms = get_terms(array(
                    'taxonomy' => 'tribe_events_cat',
                    'hide_empty' => false,
                ));
                $ect_categories=array();
                $ect_categories['all'] = __('all','ect2');
        
                if (!empty($terms) || !is_wp_error($terms)) {
                    foreach ($terms as $term) {
                        $ect_categories[$term->slug] =$term->name ;
                    }
                }
                $tags =  get_terms(array(
                  'taxonomy' => 'post_tag',
                  'hide_empty' => false,
                ));
                $ect_tags='';
                $ect_tags=array();
                $ect_tags["All"] ='';
            
                if (!empty($tags) || !is_wp_error($tags)) {
                  foreach ($tags as $tag) {
              
                    $ect_tags[$tag->slug] =$tag->name ;
                    
                  }
                }
               $date_formats= array(
                   
					  __( 'Default (01 January 2019)', 'ect2' )=>'default',
                      __( 'Md,Y (Jan 01, 2019)', 'ect2' )=>'MD,Y',
                     __( 'Fd,Y (January 01, 2019)', 'ect2' )=>'FD,Y',
                    __( 'dM (01 Jan))', 'ect2' )=> 'DM',
                      __( 'FD (January 01)', 'cool-timeline' )=>'FD',
                     
                     __( 'dF (01 January)', 'ect2' )=>'DF',
                     __( 'Md (Jan 01)', 'ect2' )=>'MD',
                   __( 'Md,YT (Jan 01, 2019 8:00am-5:00pm)', 'ect2' )=> 'MD,YT',
                    __( 'Full (01 January 2019 8:00am-5:00pm)', 'ect2' )=>'full',
                    __( 'jMl', 'ect2' )=> 'jMl',
                     __( 'd.FY (01. January 2019)', 'ect2' )=>'d.FY',
                     __( 'd.F (01. January)', 'ect2' )=>'d.F',
                     __( 'ldF (Monday 01 January)', 'ect2' )=>'ldF',
                    __( 'Mdl (Jan 01 Monday)', 'ect2' )=>'Mdl',
                    __( 'd.Ml (01. Jan Monday)', 'ect2' )=>'d.Ml',
                    __( 'dFT (01 January 8:00am-5:00pm)', 'ect2' )=>  'dFT',
                 
                    );
                    $templates=  array(
                                __( "Default",'ect2' ) => "default",
                                __( "Timeline Layout",'ect2') => "timeline-view",
                                __( 'Carousel (carousel-view)', 'ect2' )=>"carousel-view",
                                __( 'Grid (grid-view)', 'ect2') =>"grid-view",
                                __( 'Slider (slider-view)', 'ect2' )=>"slider-view",
                                __( 'Masonry Layout(Categories Filters)', 'ect2' )=>"masonry-view",
                                __( 'Toggle List(accordion-view)', 'ect2' )=>"accordion-view"
                               
                            );
                            $styles=  array(
                                __( "Style 1",'ect2' ) => "style-1",
                                __( "Style 2",'ect2') => "style-2",
                                __( "Style 3",'ect2') => "style-3",
                               
                            );

             
                vc_map(array(
                    "name" => __("The Events Calendar Shortcode", 'ect2'),
                    // "description" => __("Create Stories Timeline", 'ect2'),
                    "base" => "events-calendar-templates",
                    "class" => "",
                    "controls" => "full",
                     "icon" => plugins_url('../../assets/images/ect-icon.png', __FILE__), // or css class name which you can reffer in your css file later. Example: "ect2_my_class"
                    "category" => __('The Events Calendar Shortcode', 'ect2'),
                    //'admin_enqueue_js' => array(plugins_url('assets/ect2.js', __FILE__)), // This will load js file in the VC backend editor
                    //'admin_enqueue_css' => array(plugins_url('assets/ect2_admin.css', __FILE__)), // This will load css file in the VC backend editor
                    "params" => array(
                        array(
                            "type" => "dropdown",
                            "class" => "",
                            "heading" => __( "Select Events Category",'ect2'),
                            "param_name" => "category",
                            "value" =>$ect_categories,
                            // "description" => __( "Create Category Specific Timeline (By Default - All Categories)",'ect2' ),

                            'save_always' => true,
                        ),
                    array(
                            "type" => "dropdown",
                         "class" => "",
                           "heading" => __( "Select Templates",'ect2'),
                             "param_name" => "template",
                            "value" => $templates,
                           
                    //         "description" => __('','ect2' ),
                             'save_always' => true,
                         ),
                        
                         array(
                            "type" => "dropdown",
                         "class" => "",
                           "heading" => __( "Select Styles",'ect2'),
                             "param_name" => "style",
                            "value" => $styles,
                           
                    //         "description" => __('','ect2' ),
                             'save_always' => true,
                         ),
                         array(
                            "type" => "dropdown",
                            "class" => "",
                            "heading" => __( "Date Format",'ect2'),
                            
                            "param_name" => "date_format",
                            "value" =>$date_formats,
                            // "description" => __( "Create Category Specific Timeline (By Default - All Categories)",'ect2' ),

                            'save_always' => true,
                        ),
                         array(
                            "type" => "dropdown",
                         "class" => "",
                           "heading" => __( "Events Order",'ect2'),
                             "param_name" => "order",
                             "value" => array(
                                             __( "ASC",'ect2' ) => "ASC",
                                             __( "DESC",'ect2') => "DESC",
                                            
                                           ),
                            
                           
                    //         "description" => __('','ect2' ),
                             'save_always' => true,
                         ),
                         array(
                            "type" => "dropdown",
                         "class" => "",
                           "heading" => __( "Hide Venue",'ect2'),
                             "param_name" => "hide-venue",
                             "value" => array(
                                             __( "no",'ect2' ) => "no",
                                             __( "Yes",'ect2') => "yes",
                                            
                                           ),
                           
                             'save_always' => true,
                         ),
                         array(
                            "type" => "dropdown",
                         "class" => "",
                           "heading" => __( "Enable Social Share Buttons",'ect2'),
                             "param_name" => "socialshare",
                             "value" => array(
                                             __( "no",'ect2' ) => "no",
                                             __( "Yes",'ect2') => "yes",
                                            
                                           ),
                           
                             'save_always' => true,
                         ),
                         array(
                            "type" => "dropdown",
                         "class" => "",
                           "heading" => __( "Show Events",'ect2'),
                             "param_name" => "time",
                             "value" => array(
                                             __( "Upcoming Events",'ect2' ) => "future",
                                             __( "Past Events",'ect2') => "past",
                                             __( "All (Upcoming + Past)",'ect2') => "all",
                                            
                                           ),
                            'save_always' => true,
                         ),
                         array(
                          "type" => "dropdown",
                       "class" => "",
                         "heading" => __( "Show Only Featured Events",'ect2'),
                           "param_name" => "featured-only",
                           "value" => array(
                             __( 'NO', 'ect2' )=>'false',
                             __( 'Yes', 'ect2' ) =>'true',
                            ),
                          'save_always' => true,
                       ),
                       array(
                        "type" => "dropdown",
                     "class" => "",
                       "heading" => __( "AutoPlay (* For slide function only.",'ect2'),
                         "param_name" => "autoplay",
                         "value" => array(
                           __( 'False', 'ect2' )=>'false',
                           __( 'True', 'ect2' ) =>'true',
                          ),
                        'save_always' => true,
                     ),
                     array(
                      "type" => "dropdown",
                   "class" => "",
                     "heading" => __( "Columns",'ect2'),
                       "param_name" => "columns",
                       "value" => array(
                         __( '2', 'ect2' )=>'2',
                         __( '3', 'ect2' ) =>'3',
                         __( '4', 'ect2' ) =>'4',
                         __( '6', 'ect2' ) =>'6',
                        ),
                      'save_always' => true,
                   ),
                       array(
                        "type" => "dropdown",
                        "class" => "",
                        "heading" => __( "Select Tag (* Events by tag.)",'ect2'),
                        "param_name" => "tags",
                        "value" =>$ect_tags,
                        // "description" => __( "Create Category Specific Timeline (By Default - All Categories)",'ect2' ),

                        'save_always' => true,
                    ),
                    array(
                      "type" => "dropdown",
                      "class" => "",
                      "heading" => __( "Select Organizer (* Events by organizer.)",'ect2'),
                      "param_name" => "organizers",
                      "value" =>$ect_org_details,
                      // "description" => __( "Create Category Specific Timeline (By Default - All Categories)",'ect2' ),

                      'save_always' => true,
                  ),
                  array(
                    "type" => "dropdown",
                    "class" => "",
                    "heading" => __( "Select Venue (* Events by venue.)",'ect2'),
                    "param_name" => "venues",
                    "value" =>$ect_venue_details,
                    // "description" => __( "Create Category Specific Timeline (By Default - All Categories)",'ect2' ),

                    'save_always' => true,
                ),
                         array(
                            "type" => "textfield",
                         "class" => "",
                           "heading" => __( "Limit the events",'ect2'),
                             "param_name" => "limit",
                             "value" => '10',
                           
                             'save_always' => true,
                         ),
                         array(
                            "type" => "textfield",
                         "class" => "",
                           "heading" => __( "Start Date | format(YY-MM-DD)",'ect2'),
                             "param_name" => "start_date",
                             "value" => '',
                           
                             'save_always' => true,
                         ),
                         array(
                            "type" => "textfield",
                         "class" => "",
                           "heading" => __( "End Date | format(YY-MM-DD)",'ect2'),
                             "param_name" => "end_date",
                             "value" => '',
                           
                             'save_always' => true,
                         ),

                 

                    )
                ));
                vc_map(array(
                  "name" => __("Events Calendar Layouts", 'ect2'),
                  // "description" => __("Create Stories Timeline", 'ect2'),
                  "base" => "ect-calendar-layout",
                  "class" => "",
                  "controls" => "full",
                   "icon" => plugins_url('../../assets/images/ect-icon.png', __FILE__), // or css class name which you can reffer in your css file later. Example: "ect2_my_class"
                  "category" => __('The Events Calendar Shortcode', 'ect2'),
                  //'admin_enqueue_js' => array(plugins_url('assets/ect2.js', __FILE__)), // This will load js file in the VC backend editor
                  //'admin_enqueue_css' => array(plugins_url('assets/ect2_admin.css', __FILE__)), // This will load css file in the VC backend editor
                  "params" => array(
                      array(
                          "type" => "dropdown",
                          "class" => "",
                          "heading" => __( 'Date formats', 'cool-timeline'),
                          "param_name" => "date-format",
                          "value" =>$ect_calendar_date_format,
                          // "description" => __( "Create Category Specific Timeline (By Default - All Categories)",'ect2' ),

                          'save_always' => true,
                      ),
                  array(
                          "type" => "dropdown",
                       "class" => "",
                         "heading" => __( 'Show Category Filter', 'cool-timeline'),
                           "param_name" => "show-category-filter",
                           "value" => array(
                            __( 'Yes', 'ect2' )=>'true',
                            __( 'No', 'ect2' ) =>'false',
                           ),
                         'save_always' => true,
                 
                       ),
                  )
                      ));



            }
        }// vc function end
    
}
new EctVCAddon();