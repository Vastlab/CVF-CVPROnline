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
               $date_formats= array(
                   
					  __( 'Default (01 January 2019)', 'ect2' )=>'default',
                      __( 'Md,Y (Jan 01, 2019)', 'ect2' )=>'MD,Y',
                     __( 'Fd,Y (January 01, 2019)', 'ect2' )=>'FD,Y',
                    __( 'dM (01 Jan))', 'ect2' )=> 'DM',
                     __( 'dMl (01 Jan Monday)', 'ect2' )=>'DML',
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
                     "icon" => plugins_url('../../assets/images/ect-icon.png', __FILE__), // or css class name which you can reffer in your css file later. Example: "cool-timeline_my_class"
                    "category" => __('The Events Calendar Shortcode', 'ect2'),
                    //'admin_enqueue_js' => array(plugins_url('assets/ect2.js', __FILE__)), // This will load js file in the VC backend editor
                    //'admin_enqueue_css' => array(plugins_url('assets/cool-timeline_admin.css', __FILE__)), // This will load css file in the VC backend editor
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
                            // "param_name" => "category",
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
                            
                           
                    //         "description" => __('','ect2' ),
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



            }
        }// vc function end
    
}
new EctVCAddon();