<?php
/*
 Plugin Name:The Events Calendar - Shortcode And Templates Pro 
 Plugin URI:https://eventscalendartemplates.com/
 Description:The Events Calendar Shortcode And Templates Pro addon provides events list design templates and shortcode generator functionality for The Events Calendar plugin. It is an unofficial third party addon for <a href="http://wordpress.org/plugins/the-events-calendar/">The Events Calendar (by Modern Tribe)</a> that extends its design limitations.
 Version:2.3.1
 License:GPL2
 Author:Cool Plugins
 Author URI:https://coolplugins.net/
 License URI:https://www.gnu.org/licenses/gpl-2.0.html
 Domain Path:/languages
 Text Domain:ect
*/

if ( !defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}
if (!defined('ECT_VERSION_CURRENT')){
	define('ECT_VERSION_CURRENT', '2.3.1');
}
/*** Defined constent for later use */

	define('ECT_PRO_FILE', __FILE__ );

	if (!defined('ECT_PRO_PLUGIN_URL')){
define('ECT_PRO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
	}
	if (!defined('ECT_PRO_PLUGIN_DIR')){
define('ECT_PRO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	}


//define('ECT_REST_URL',get_rest_url(null, '/tribe/events/v1/'));

/*** EventsCalendarTemplates main class by CoolPlugins.net */
if (!class_exists('EventsCalendarTemplatesPro')) {
	class EventsCalendarTemplatesPro {

		/*** Construct the plugin object  */
		public function __construct() {
		/*** Check The Event Calendar is installled or not */	
    	add_action( 'plugins_loaded', array( $this, 'check_event_Calendar_installed' ));

    	 /*** Load required files */
			add_action( 'plugins_loaded',array($this,'ect_load_files'));

				/*** Include required files */
				require_once(ECT_PRO_PLUGIN_DIR.'admin/gutenberg-block/ect-block.php' );
				require_once(ECT_PRO_PLUGIN_DIR.'/includes/ect-functions.php' );
				
			/*** This hook creates setting panel */
			add_action('tf_create_options','ect_Options');
		
			/*** Enqueued script and styles */
			add_action('wp_enqueue_scripts','ect_styles');
			add_action('admin_enqueue_scripts', array($this,'ect_tc_css'));
			add_action('admin_enqueue_scripts','ect_remove_wpcalpha',99);

			/*** tinymce shortcode generator hook */
			add_action('after_setup_theme', array($this, 'ect_add_tc_button'));

			/*** ECT main shortcode */
			add_shortcode('events-calendar-templates', array( $this,'ect_shortcodes'));
			
			
		
			/*** Template Setting Page Link inside Plugins List */
			add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this,'ect_template_settings_page'));
			
			add_action('admin_head','ect_admin_menu_custom_styles');
			/*** added notice for save settings */
			add_action('admin_init','ect_set_notice_timing');
			add_action('admin_notices','ect_admin_save_settings_notice');
			add_action('admin_init','ect_plugin_redirect');
			// add some js objects in page and posts header 
			foreach (array('post.php','post-new.php') as $hook) {
  				add_action("admin_head-$hook", array( $this,'ect_rest_url'));
			}

			// load more events for masonry layout
			add_action( 'wp_ajax_ect_catfilters_load_more', array( $this, 'ect_catfilters_load_more'));
			add_action( 'wp_ajax_nopriv_ect_catfilters_load_more', array( $this, 'ect_catfilters_load_more'));

			add_action( 'wp_ajax_ect_common_load_more', array( $this, 'ect_common_loadmore_handler'));
			add_action( 'wp_ajax_nopriv_ect_common_load_more', array( $this, 'ect_common_loadmore_handler'));
		
		}
		

	/*** Load required files */
	public function ect_load_files() {
	/*** Check whether the Titan Framework plugin is activated, and notify if it isn't */
		require_once(ECT_PRO_PLUGIN_DIR.'admin/titan-framework/titan-framework-embedder.php' );
		require_once(ECT_PRO_PLUGIN_DIR.'admin/ect-pro-settings.php' );
		require_once(ECT_PRO_PLUGIN_DIR.'/templates/calendar-template.php');
		/*** Plugin review notice file */ 
		require_once(ECT_PRO_PLUGIN_DIR.'admin/ect-feedback-notice.php');
	
		if( file_exists( plugin_dir_path( __DIR__ ) . "elementor/elementor.php"  ) ){
			include_once( ABSPATH . "wp-admin/includes/plugin.php" );
		if( is_plugin_active( "elementor/elementor.php" ) ){
			require_once(ECT_PRO_PLUGIN_DIR. 'admin/elementor/ect-elementor.php' );
			//require_once(ECT_PRO_PLUGIN_DIR. 'admin/elementor/ect-elementor-calender-shortcode.php' );
		}    
	}
	if (  defined( 'WPB_VC_VERSION' ) ) {
	 	require_once(ECT_PRO_PLUGIN_DIR.'admin/visual-composer/ect-class-vc.php');
	 }
		if( is_admin()){
			require_once(ECT_PRO_PLUGIN_DIR .'admin/init-api.php');
		}
	
		new ECTProFeedbackNotice();
	}

		/*** Check The Event Calendar is installled or not. If user has not installed yet then show notice */	
		public function check_event_Calendar_installed() {
			//language translation 
			load_plugin_textdomain('ect', false, basename(dirname(__FILE__)) . '/languages/');
			if ( ! class_exists( 'Tribe__Events__Main' ) or ! defined( 'Tribe__Events__Main::VERSION' )) {
				add_action( 'admin_notices', array( $this, 'Install_ECT_Notice' ) );
			}
		}
		// notice for installation TEC parent plugin installation
		public function Install_ECT_Notice() {
			if ( current_user_can( 'activate_plugins' ) ) {
				$url = 'plugin-install.php?tab=plugin-information&plugin=the-events-calendar&TB_iframe=true';	
				$title = __( 'The Events Calendar', 'tribe-events-ical-importer' );
				echo '<div class="error CTEC_Msz"><p>' . sprintf( __( 'In order to use this addon, Please first install the latest version of <a href="%s" class="thickbox" title="%s">%s</a> and add an event.', 'ect' ), esc_url( $url ), esc_attr( $title ),esc_attr( $title ) ) . '</p></div>';
			}
		}

	
		/*** Admin side shortcode generator style CSS */
		public function ect_tc_css() {
			wp_enqueue_style('sg-btn-css', plugins_url('assets/css/shortcode-generator.css', __FILE__));
		}
		
		/*** Integrate shortcode generator in tinymce editor */
		public function ect_add_tc_button() {
			global $typenow;
			/*** check user permissions */
			if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
				return;
			}
			/*** check if WYSIWYG is enabled */
			if ( get_user_option('rich_editing') == 'true') {
			    add_filter("mce_external_plugins",array($this,"ect_add_tinymce_plugin"));
			    add_filter('mce_buttons',array($this,'ect_register_tc_button'));
			}
		}
		// shortcode geneator button integartion in classic editor
		public function ect_add_tinymce_plugin($plugin_array) {
			$plugin_array['ect_tc_button'] = plugins_url( 'assets/js/shortcode-generator.js', __FILE__ ); 
			return $plugin_array;
		}		
		// register button in editor
		public function ect_register_tc_button($buttons) {
			array_push($buttons, "ect_tc_button");
			return $buttons;
		}


		/*** Add links in plugin list page */
		public function ect_template_settings_page($links){
			$links[] = '<a style="font-weight:bold" href="'. esc_url( get_admin_url(null, 'edit.php?post_type=tribe_events&page=edit.php%3Fpost_type%3Dtribe_events-shortcode---template-settings') ) .'">Template Settings</a>';
			//$links[] = '<a  style="font-weight:bold" href="https://eventscalendartemplates.com/" target="_blank">View Demos</a>';
			return $links;
		}


		/*** ECT main shortcode */
		public function ect_shortcodes($atts) {
    	if ( !function_exists( 'tribe_get_events' ) ) {
				return;
			}
			global $wp_query, $post;
			global $more;
			$more = false;
			$output = '';
			$events_html = '';
			$build_query = array();
			/*** Set shortcode default attributes */
			$attribute = shortcode_atts( apply_filters( 'ect_shortcode_atts', array(
			'template' => 'default',
			'style' => 'style-1',
			'category' => 'all',
			'date_format' => 'default',
			'start_date' => '',
			'end_date' => '',
			'time' => 'future',
			'order' => 'ASC',
			'limit' => '10',
			'columns'=>'3',
			'hide-venue' => 'no',
			'autoplay'=>'false',
			'featured-only'=>'',
			'show-description'=>'',

			'event_tax' => '',
			'month' => '',
			'icons' => '',

			'tags'=> '',
			'venues'=> '',
			'organizers'=> '',
			'socialshare'=>'no'
			), $atts ), $atts);

			$selected_cat=$attribute['category'];
			$show_description = !empty($attribute['show-description'])?$attribute['show-description']:'';
			// var_dump($show_description);
			$count =0;
			$no_events='';
			$template=isset($attribute['template'])?$attribute['template']:'default';
			$date_format=$attribute['date_format'];
			
			$tabs_menu_html=''; $tabs_cont_html='';	$ev_cost=''; $car_sl_styles='';
			$activetb=1;
			$slider_pp_id='ect-'.$attribute['template'].'-'.$attribute['style'].rand(1,10);
		
			if($attribute['style']!='') {
				$car_sl_styles='-'.$attribute['style'];
			}
			$hide_venue=$attribute['hide-venue'];
			$ect_carousel_id='ect-events-carousel'.$car_sl_styles;
			$ect_grid_id='ect-grid-view-'.$attribute['style'];
			$ect_masonry_cls='ect-masonry-view-'.$attribute['style'];
    		$ect_slider_templates_id='ect-events-slider'.$car_sl_styles;
			$design='default-design';
			$autoplay=$attribute['autoplay'];
 			$carousel_slide_show=isset($attribute['columns'])?$attribute['columns']:'';
 			$style=isset($attribute['style'])?$attribute['style']:'default';
			$socialshare=$attribute['socialshare'];
			$m_output='';
			// load assets according to the type and layout
			ect_load_assets($template,$style,$slider_pp_id,$autoplay, $carousel_slide_show);
		
				if($socialshare=="yes"){
					wp_enqueue_script('ect-sharebutton',ECT_PRO_PLUGIN_URL .'assets/js/ect-sharebutton.js',array('jquery'),null,true);
					wp_enqueue_style('ect-sharebutton-css',ECT_PRO_PLUGIN_URL .'assets/css/ect-sharebutton.css',null, null,'all');
				}
			/* 
			Build ECT query
			*/

			$prev_event_month='';
			$prev_event_year='';
			$meta_date_compare = '>=';
		 if ($attribute['time']=='past') {
				$meta_date_compare = '<';
			}
			else if($attribute['time']=='all'){
				$meta_date_compare = '';
			}
$attribute['key'] = '_EventStartDate' ;
$attribute['meta_date'] = '';
$meta_date_date = '';
		if($meta_date_compare!=''){
			$meta_date_date = current_time( 'Y-m-d H:i:s' );
			$attribute['key']='_EventStartDate';
			$attribute['meta_date'] = array(
			array(
				'key' =>'_EventEndDate',
				'value' => $meta_date_date,
				'compare' => $meta_date_compare,
				'type' => 'DATETIME'
			));
		}
		 $featured_only='';
		 if($attribute['featured-only']=="true"){
			$featured_only=true;
		 }
		 elseif($attribute['featured-only']=="false"){
			$featured_only='';
		 }
		 
$ect_args=apply_filters( 'ect_args_filter', array(
				'post_status' => 'publish',
				'posts_per_page' => $attribute['limit'],
				'meta_key' => $attribute['key'],
				 'orderby' => 'event_date',
				'order' => $attribute['order'],
				'featured'=>$featured_only,
				'meta_query' =>$attribute['meta_date'],
				), $attribute, $meta_date_date, $meta_date_compare );
			

				if($attribute['tags']!="") {
						if ( strpos( $attribute['tags'], "," ) !== false ) {
							$ect_args['tag'] = explode( ",", $attribute['tags'] );
						}else{
							$ect_args['tag']=$attribute['tags'];
						}
					}
					if($attribute['venues']!="") {
						if ( strpos( $attribute['venues'], "," ) !== false ) {
							$ect_args['venue'] = explode( ",", $attribute['venues'] );
						}else{
							$ect_args['venue']=$attribute['venues'];
						}
					}
					if($attribute['organizers']!="") {
						if ( strpos( $attribute['organizers'], "," ) !== false ) {
							$ect_args['organizer'] = explode( ",",$attribute['organizers'] );
						}else{
							$ect_args['organizer']=$attribute['organizers'];
						}
					}

				if($attribute['category']!="all") {
					if ( $attribute['category'] ) {
						if ( strpos( $attribute['category'], "," ) !== false ) {
							$attribute['category'] = explode( ",", $attribute['category'] );
							$attribute['category'] = array_map( 'trim',$attribute['category'] );
						}
						else {
							$attribute['category'] = $attribute['category'];
						}
			
						 $ect_args['tax_query'] = array(
						array(
							'taxonomy' => 'tribe_events_cat',
							'field' => 'slug',
							'terms' =>$attribute['category'],
						));
					 }
				}
				if (!empty($attribute['start_date'])) {
					$ect_args['start_date'] =$attribute['start_date'];
				 }
				 if (!empty($attribute['end_date'])) {
					$ect_args['end_date'] =$attribute['end_date'];
				 } 
				 $grid_style=$attribute['style'];
				 $ect_grid_columns=$attribute['columns'];
			
				 /*
				 	end main query 
				 */

				

			/*
    		  Fetch Events data
			*/
		
			$excludePosts=array();
			if($template=="accordion-view" && $style=="style-4"){
				$ect_args['posts_per_page']=-1;	
			}
			$all_events = tribe_get_events($ect_args);
			$ect_args['posts_per_page']=-1;
			$total_events =count(tribe_get_events($ect_args));
			$ect_args['posts_per_page']=$attribute['limit'];
			$i = 0;
			if ( $all_events ) {

		 		foreach( $all_events as $post ):setup_postdata( $post );
		 		$event_description='';$event_cost='';$event_title='';$event_schedule='';$event_venue='';$event_img='';$event_content='';$events_date_header='';$no_events='';$event_day='';$event_address='';
				$event_id=$post->ID;
				$excludePosts[]=$event_id;
		 		$show_headers = apply_filters( 'tribe_events_list_show_date_headers', true );
				 if ( $show_headers ) {
					$event_year= tribe_get_start_date($event_id, false, 'Y' );
					$event_month= tribe_get_start_date($event_id, false, 'm' );
					$month_year_format= tribe_get_date_option( 'monthAndYearFormat', 'M Y' );
					if ($prev_event_month != $event_month || ( $prev_event_month == $event_month && $prev_event_year != $event_year ) ) {		
						$prev_event_month=$event_month;
						$prev_event_year= $event_year;
						$date_header= sprintf( "<span class='month-year-box'><span>%s</span></span>", tribe_get_start_date( $post, false,'M Y') );
						$events_date_header.='<!-- Month / Year Headers -->';
						$events_date_header.=$date_header;	
						
					}
		 		}
		 	
				$post_parent = '';
				if ( $post->post_parent ) {
					$post_parent = ' data-parent-post-id="' . absint( $post->post_parent ) . '"';
				}
				$event_type = tribe( 'tec.featured_events' )->is_featured( $post->ID ) ? 'ect-featured-event' : 'ect-simple-event';

				// Venue
				$venue_details = tribe_get_venue_details($event_id);
				$has_venue_address = (!empty( $venue_details['address'] ) ) ? 'location' : '';
				$venue_details_html='';

				/*** Get Event Categories Colors */
				$cat_bgcolor = $cat_txtcolor = $cat_bg_styles = $cat_txt_styles = $cat_colors_attr = '';
				$event_cats = get_the_terms($event_id, 'tribe_events_cat');
				if( !empty($event_cats) && $event_type != 'ect-featured-event'){
					foreach ($event_cats as $category) {
						if(!empty(get_term_meta( $category->term_taxonomy_id, '_event_bgColor', true ))){
							$cat_bgcolor = get_term_meta( $category->term_taxonomy_id, '_event_bgColor', true );
							$cat_txtcolor = get_term_meta( $category->term_taxonomy_id, '_event_textColor', true );
							$cat_colors_attr = 'data-cat-bgcolor="'.$cat_bgcolor.'" data-cat-txtcolor="'.$cat_txtcolor.'"';
							$cat_bg_styles = 'style="background:#'.$cat_bgcolor.';color:#'.$cat_txtcolor.';box-shadow:none;"';
							$cat_txt_styles = 'style="color:#'.$cat_bgcolor.';box-shadow:none;"';
						}
					}
				}

		 		// Setup an array of venue details for use later in the template	 		
				if($attribute['hide-venue']!="yes"){
					if($template=="modern-list" || ($template=="default" && $style=="style-2")){
						$venue_details_html.='<div class="modern-list-venue">';
					}
					else if($template=="classic-list" || ($template=="default" && $style!="style-2")) {
						$venue_details_html.='<div class="ect-list-venue '.$template.'-venue">';
					}
					else {
						$venue_details_html.='<div class="'.$template.'-venue">';
					}
					if (tribe_has_venue()) :
				
					if(!empty($venue_details['address']) && isset($venue_details['linked_name'])){
						$venue_details_html.='<span class="ect-icon"><i class="ect-icon-location" aria-hidden="true"></i></span>';
					}
			
					$venue_details_html.='<!-- Venue Display Info -->
					<span class="ect-venue-details ect-address" itemprop="location" itemscope itemtype="http://schema.org/Place">
					<meta itemprop="name" content="'.tribe_get_venue($event_id).'">
					<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
					<meta itemprop="name" content="'.tribe_get_venue($event_id).'">';
					$venue_details_html.=implode(',', $venue_details );
					$venue_details_html.='</div>';
					if ( tribe_get_map_link() ) {
						$venue_details_html.='<span class="ect-google">'.tribe_get_map_link_html().'</span>';
					}
					$venue_details_html.='</span>';
			
					endif ;
					$venue_details_html.='</div>';
				}

			
				if ( tribe_get_cost() ) : 
					$ev_cost='<div class="ect-rate-area" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
					<span class="ect-icon"><i class="ect-icon-ticket" aria-hidden="true"></i></span>
					<span class="ect-rate" itemprop="price" content="'.tribe_get_cost(null, false ).'">'.tribe_get_cost(null, true ).'</span>
					<meta itemprop="priceCurrency" content="'.tribe_get_event_meta( $event_id, '_EventCurrencySymbol', true ).'" />';
					
					if( class_exists('Tribe__Tickets__Main') ){
					$ev_cost.='<span class="ect-ticket-info">';	
					$ev_cost.=ect_tribe_tickets_buy_button(false,$event_id);
					$ev_cost.='</span>';
					}

					$ev_cost.='</div>';
					
				endif;

				$event_schedule=$this->ect_event_schedule($event_id,$date_format,$template);
				$ev_time=$this->ect_tribe_event_time($event_id,false);
				// Organizer
				$organizer = tribe_get_organizer();

				if ( tribe_get_cost() ) : 
					$event_cost='<!-- Event Cost -->
					<div class="ect-event-cost">
					<span>'.tribe_get_cost(null, true ).'</span>
					</div>';
				endif;

				if($template=="classic-list" || $template=="default" && $style=='style-3'){
					$event_title='<a itemprop="name" class="ect-event-url" href="'.esc_url( tribe_get_event_link()).'" rel="bookmark"><i class="ect-icon-bell-alt"></i>'. get_the_title().'</a>';
				}
				elseif( $template=="accordion-view"){
					$event_title='<h3 class="ect-accordion-title">'. get_the_title($event_id).'</h3>';
				
				}
				else {
					$event_title='<a itemprop="name" class="ect-event-url" href="'.esc_url( tribe_get_event_link()).'" rel="bookmark">'. get_the_title().'</a>';
				}
				/**
				 * Event Description without Find out More
				 */
				if(tribe_events_get_the_excerpt($event_id)){
					$event_description='<!-- Event Description --><div class="ect-event-content" itemprop="description" content="'.esc_attr(wp_strip_all_tags( tribe_events_get_the_excerpt($event_id), true )).'">';
					$event_description.= tribe_events_get_the_excerpt($event_id, wp_kses_allowed_html( 'post' ) );
					$event_description.= '</div>';
				}
				

				$event_content='<!-- Event Content --><div class="ect-event-content" itemprop="description" content="'.esc_attr(wp_strip_all_tags( tribe_events_get_the_excerpt($event_id), true )).'">';
				$event_content.=tribe_events_get_the_excerpt($event_id, wp_kses_allowed_html( 'post' ) );
 				$event_content.='<a href="'.esc_url( tribe_get_event_link($event_id) ).'" class="ect-events-read-more" rel="bookmark">'.esc_html__( 'Find out more', 'the-events-calendar' ).' &raquo;</a></div>';
 			
				//event day
				$event_day='<span class="event-day">'.tribe_get_start_date($event_id, true, 'l').'</span>';
				//Address
				$venue_details = tribe_get_venue_details($event_id);
				$event_address = (!empty( $venue_details['address'] ) ) ?$venue_details['address'] : '';
		
				// load layouts based upon template type

				if(in_array($template,array("timeline","classic-timeline",'timeline-view'))) {
					include(ECT_PRO_PLUGIN_DIR.'/templates/timeline-template.php');	
				}
				else if(in_array($template,array("default","classic-list",'modern-list'))) {
					include(ECT_PRO_PLUGIN_DIR.'/templates/list-template.php');	
				}
				else if(in_array($template,array("slider-view"))) {
					include(ECT_PRO_PLUGIN_DIR.'/templates/slider-template.php');	
				}
				else if($template=="grid-view") {
					$ect_grid_columns=$attribute['columns'];
					$grid_style=$attribute['style'];
					$hide_venue=$attribute['hide-venue'];
					include(ECT_PRO_PLUGIN_DIR.'/templates/grid-template.php');	
				}
				else if(in_array($template,array("carousel-view"))) {
					include(ECT_PRO_PLUGIN_DIR.'/templates/carousel-template.php');	
				}else if(in_array($template,array("masonry-view"))){
						$grid_style=$attribute['style'];
						$ect_grid_columns=$attribute['columns'];
						include(ECT_PRO_PLUGIN_DIR.'/templates/masonry-template.php');	
				}
				else if($template=="accordion-view") {
					$ect_compare='';
					include(ECT_PRO_PLUGIN_DIR.'/templates/accordion-template.php');	
				}

				$ev_cost='';
				endforeach;
				wp_reset_postdata();
					 		
			}
			else { 
				$tect_settings = TitanFramework::getInstance( 'ect' );
				$no_event_found_text = $tect_settings->getOption( 'events_not_found' );
				// var_dump($no_event_found_text);
				if(!empty($no_event_found_text)){
					$no_events='<div class="ect-no-events"><p>'.filter_var($no_event_found_text,FILTER_SANITIZE_STRING).'</p></div>';
				}else{
				$no_events='<div class="ect-no-events"><p>'.__('There are no upcoming events at this time.','ect').'</p></div>';
				}
			} 
			//}	//default loop end
			//$main_title='<h2 class="ect-events-page-title">'.tribe_get_events_title() .'</h2>';
		
			$catCls=is_array($attribute['category'])?implode(",",$attribute['category']):$attribute['category'];
			//$wrp_id='ect-'.$template.'-'.$style;
			if(in_array($template,array("timeline","classic-timeline",'timeline-view'))) {
				if($template=="timeline") {
					$style='style-1';
				}
				else if($template=="classic-timeline") {
					$style='style-2';
				}
				/*** Gerneral options */
			// create wrapper elements for all layouts
				$wrp_cls='';
				$layout_cls = '';
				$layout_wrp = 'both-sided-wrapper';
				$wrp_cls='default-layout';
				$wrapper_cls = 'white-timeline-wrapper';

				$output .='<!=========Events Timeline Template '.ECT_VERSION_CURRENT.'=========>';
				$output .= '<div id="event-timeline-wrapper" class="'. $catCls.' '.$style.'">';
				$output .= '<div class="cool-event-timeline">';
				$output .=$events_html;
				$output .= '</div></div>';
			}
			else if(in_array($template,array("slider-view"))) {
				$output .='<!=========Slider View Template '.ECT_VERSION_CURRENT.'=========>';
				$output.='<div id="ect-slider-wrapper" class="'.$ect_slider_templates_id.' '. $catCls.'">';
				$output.='<div class="ect-slider-outr ect-events-slider"> 
				<section id="'.$slider_pp_id.'" class="ect-slider-view ect-events-slider" data-sizes="50vw">';
				$output.=$events_html;
				$output.='</section></div></div>';
			}
			else if($template=="grid-view") {
				$output .='<!=========Grid View Template '.ECT_VERSION_CURRENT.'=========>';
				$output.='<div id="ect-grid-wrapper" class="tect-grid-wrapper '.$ect_grid_id.' '. $catCls.'">'; 
				$output.='<div class="row">';
				$output.=$events_html;
				$output.='</div>';
				if ( $all_events && $total_events>$attribute['limit']) {
					$settings=array("hide_venue"=>$hide_venue,
					"date_format"=>$date_format,
					"socialshare"=>$socialshare,
					"template"=>$template,
					"style"=>$style,
					"show_description"=>$show_description,
					"ect_grid_columns"=>$attribute['columns'],
				);
				$output .='<div class="ect-load-more '.$style.'">
					<a href="#" class="ect-load-more-btn">
					<img class="ect-preloader" style="display:none;" src="'.ECT_PRO_PLUGIN_URL.'assets/images/preloader.svg"> <span class="ect-btn-text">'.__('Load More','ect').'</span></a>
					<section data-exclude-events="'.json_encode($excludePosts).'"  id="ect-lm-settings" data-load-more="'.__('Load more','ect').'"  data-loaded="'.__('No Event Found','ect').'" 
					data-loading="'.__('Loading','ect').'" data-settings='.json_encode($settings).' data-ajax-url="'.admin_url('admin-ajax.php').'">
					</section>
					<script type="application/json" id="ect-query-arg">'.json_encode($ect_args).'</script>
					</div>'; 
					}		
				$output.='</div>';	
			}
			else if(in_array($template,array("carousel-view"))) {
				$output .='<!=========Carousel View Template '.ECT_VERSION_CURRENT.'=========>';				
				$output.='<div id="ect-carousel-wrapper" class="'.$ect_carousel_id.' '. $catCls.'">';
			
				$output.='<div class="ect-carousel-outer ect-events-carousel"><section id="'.$slider_pp_id.'" class="ect-carousel ect-events-carousel" data-sizes="50vw">';
				$output.=$events_html;
				$output.='</section></div></div>';
			}
			else if($template=="masonry-view") {
				$template="masonary";
				$settings=array("hide_venue"=>$hide_venue,
				"grid_style"=>$grid_style,
				"ect_grid_columns"=>$ect_grid_columns,
				"date_format"=>$date_format,
				"socialshare"=>$socialshare,
				"show_description"=>$show_description
				);
	
				$post_per_page=$attribute['limit'];
				$totalPosts=0;
				$pages=0;

			
				/*
					Category filters for masonry layout
				*/
				$output.='<div class="ect-masonry-template-cont">';
				$output .='<!=========masonry View Template '.ECT_VERSION_CURRENT.'=========>';
				$output.=create_cat_filter_html($selected_cat,$post_per_page);

				$output.= '<div  id="ect-grid-wrapper" class="ect-masonary-cont '.$ect_masonry_cls.'">';
				$output .= $events_html;
				$output.='</div>';
			
				if ( $all_events) {
				unset($ect_args['tax_query']);
				$output .='<div class="ect-masonay-load-more">';
			
				if ( $total_events>$attribute['limit']){
					$output .='<a href="#" class="ect-load-more-btn">
					<img class="ect-preloader" style="display:none;" src="'.ECT_PRO_PLUGIN_URL.'assets/images/preloader.svg"> <span class="ect-btn-text">'.__('Load More','ect').'</span></a>';
				}
			
				$output .='<section data-exclude-events="'.json_encode($excludePosts).'"  id="ect-lm-settings" data-load-more="'.__('Load more','ect').'"  data-loaded="'.__('No Event Found','ect').'" 
				data-loading="'.__('Loading','ect').'" data-settings='.json_encode($settings).' data-ajax-url="'.admin_url('admin-ajax.php').'">
				</section>
				<script type="application/json" id="ect-query-arg">'.json_encode($ect_args).'</script></div>';
				}
				$output .='</div>';
			}
		else if($template=="accordion-view") {
				$output .='<!=========Accordion View Template '.ECT_VERSION_CURRENT.'=========>';
				$output.='<div id="ect-accordion-wrapper" class="ect-accordion-view '.$style.' ect-cat-'. $catCls.'">';
				$output.='<div class="ect-accordion-container">';

                if($style=="style-4"){
                    $arrows='';
					$arrows .='<div class="ect-accordion-arrows ect-accordion-'.$slider_pp_id.'">
					<div class="ect-accordn-slick-prev ect-accordn-slick-prev-'.$slider_pp_id.'"><i class="ect-icon-left"></i></div>
					<div class="ect-accordn-slick-next ect-accordn-slick-next-'.$slider_pp_id.'"><i class="ect-icon-right"></i></div>
                    </div>';
                    $output.=$arrows;
                    $output.='<section id="'.$slider_pp_id.'" class="ect-accordion-view ect-events-accordion">';
                   $output.=$events_html;
                    if(end($all_events)){
                     $output.='</div><!--close end element div!-->';	
                     }
                    $output.='</section>';
                }   
                else{
                    $output.=$events_html;
                }
				$output.='</div>';	
				if($style!="style-4"){
				if ( $all_events && $total_events>$attribute['limit']) {
					$settings=array("hide_venue"=>$hide_venue,
					"date_format"=>$date_format,
					"socialshare"=>$socialshare,
					"template"=>$template,
					"style"=>$style,
					"show_description"=>$show_description
					
				);
					$output .='<div class="ect-load-more '.$style.'">
					<a href="#" class="ect-load-more-btn">
					<img class="ect-preloader" style="display:none;" src="'.ECT_PRO_PLUGIN_URL.'assets/images/preloader.svg"> <span class="ect-btn-text">'.__('Load More','ect').'</span></a>
					<section data-exclude-events="'.json_encode($excludePosts).'"  id="ect-lm-settings" data-load-more="'.__('Load more','ect').'"  data-loaded="'.__('No Event Found','ect').'" 
					data-loading="'.__('Loading','ect').'" data-settings='.json_encode($settings).' data-ajax-url="'.admin_url('admin-ajax.php').'">
					<div id="ect-cat-load-more" style="display:none;"><img class="ect-preloader"  src="'.ECT_PRO_PLUGIN_URL.'assets/images/preloader.svg"> <span class="ect-btn-text"></span></div>
					</section>
					<script type="application/json" id="ect-query-arg">'.json_encode($ect_args).'</script></div>';
					}	
				}
				$output.='</div>';	
			}
			else {
                
				$output .='<!=========list Template '.ECT_VERSION_CURRENT.'=========>';
				$output.='<div id="ect-events-list-content" class="ectt-list-wrapper">';
				$output.='<div id="list-wrp" class="ect-list-wrapper '. $catCls.'">';
				$output.=$events_html;
				$output.='</div>';
			
				if ( $all_events && $total_events>$attribute['limit']) {
					$settings=array("hide_venue"=>$hide_venue,
					"date_format"=>$date_format,
					"socialshare"=>$socialshare,
					"template"=>$template,
					"style"=>$style,
					"show_description"=>$show_description,
					
				);
					$output .='<div class="ect-load-more '.$style.'">
					<a href="#" class="ect-load-more-btn">
					<img class="ect-preloader" style="display:none;" src="'.ECT_PRO_PLUGIN_URL.'assets/images/preloader.svg"> <span class="ect-btn-text">'.__('Load More','ect').'</span></a>
					<section data-exclude-events="'.json_encode($excludePosts).'"  id="ect-lm-settings" data-load-more="'.__('Load more','ect').'"  data-loaded="'.__('No Event Found','ect').'" 
					data-loading="'.__('Loading','ect').'" data-settings='.json_encode($settings).' data-ajax-url="'.admin_url('admin-ajax.php').'">
					<div id="ect-cat-load-more" style="display:none;"><img class="ect-preloader"  src="'.ECT_PRO_PLUGIN_URL.'assets/images/preloader.svg"> <span class="ect-btn-text"></span></div>
					</section>
					<script type="application/json" id="ect-query-arg">'.json_encode($ect_args).'</script></div>';
					}	
					$output.='</div>';	
			}
		
			return $output.$no_events;		
		}


		// generate events dates html
		public function ect_event_schedule($event_id,$date_format,$template){
				/*Date Format START*/
				$event_schedule='';
		
				$ev_time=$this->ect_tribe_event_time($event_id,false);
				if($date_format=="DM") {
					$event_schedule='<div class="ect-date-area '.$template.'-schedule"  itemprop="startDate" content="'.tribe_get_start_date($event_id, false, 'Y-m-dTg:i').'">
									<span class="ev-day">'.tribe_get_start_date($event_id, false, 'd' ).'</span>
									<span class="ev-mo">'.tribe_get_start_date($event_id, false, 'M' ).'</span>
									</div>
									<meta itemprop="endDate" content="'.tribe_get_end_date($event_id, false, 'Y-m-dTg:i').'">';
				}
				else if($date_format=="MD") {
					$event_schedule='<div class="ect-date-area '.$template.'-schedule"  itemprop="startDate" content="'.tribe_get_start_date($event_id, false, 'Y-m-dTg:i').'">
									<span class="ev-mo">'.tribe_get_start_date($event_id, false, 'M' ).'</span>
									<span class="ev-day">'.tribe_get_start_date($event_id, false, 'd' ).'</span>
									</div>
									<meta itemprop="endDate" content="'.tribe_get_end_date($event_id, false, 'Y-m-dTg:i').'">';
				}
				else if($date_format=="FD") {
					$event_schedule='<div class="ect-date-area '.$template.'-schedule"  itemprop="startDate" content="'.tribe_get_start_date($event_id, false, 'Y-m-dTg:i').'">
									<span class="ev-mo">'.tribe_get_start_date($event_id, false, 'F' ).'</span>
									<span class="ev-day">'.tribe_get_start_date($event_id, false, 'd' ).'</span>
									</div>
									<meta itemprop="endDate" content="'.tribe_get_end_date($event_id, false, 'Y-m-dTg:i').'">';
				}
				else if($date_format=="DF") {
					$event_schedule='<div class="ect-date-area '.$template.'-schedule"  itemprop="startDate" content="'.tribe_get_start_date($event_id, false, 'Y-m-dTg:i').'">
									<span class="ev-day">'.tribe_get_start_date($event_id, false, 'd' ).'</span>
									<span class="ev-mo">'.tribe_get_start_date($event_id, false, 'F' ).'</span>
									</div>
									<meta itemprop="endDate" content="'.tribe_get_end_date($event_id, false, 'Y-m-dTg:i').'">';
				}
				else if($date_format=="FD,Y") {
					$event_schedule='<div class="ect-date-area '.$template.'-schedule"  itemprop="startDate" content="'.tribe_get_start_date($event_id, false, 'Y-m-dTg:i').'">
									<span class="ev-mo">'.tribe_get_start_date($event_id, false, 'F' ).'</span>
									<span class="ev-day">'.tribe_get_start_date($event_id, false, 'd' ).', </span>
									<span class="ev-yr">'.tribe_get_start_date($event_id, false, 'Y' ).'</span>
									</div>
									<meta itemprop="endDate" content="'.tribe_get_end_date($event_id, false, 'Y-m-dTg:i').'">';
				}
				else if($date_format=="MD,Y") {
					$event_schedule='<div class="ect-date-area '.$template.'-schedule"  itemprop="startDate" content="'.tribe_get_start_date($event_id, false, 'Y-m-dTg:i').'">
									<span class="ev-mo">'.tribe_get_start_date($event_id, false, 'M' ).'</span>
									<span class="ev-day">'.tribe_get_start_date($event_id, false, 'd' ).', </span>
									<span class="ev-yr">'.tribe_get_start_date($event_id, false, 'Y' ).'</span>
									</div>
									<meta itemprop="endDate" content="'.tribe_get_end_date($event_id, false, 'Y-m-dTg:i').'">';
				}
				else if($date_format=="MD,YT") {
					$event_schedule='<div class="ect-date-area '.$template.'-schedule" itemprop="startDate" content="'.tribe_get_start_date($event_id, false, 'Y-m-dTg:i').'">
									<span class="ev-mo">'.tribe_get_start_date($event_id, false, 'M' ).'</span>
									<span class="ev-day">'.tribe_get_start_date($event_id, false, 'd' ).', </span>
									<span class="ev-yr">'.tribe_get_start_date($event_id, false, 'Y' ).'</span>
									<span class="ev-time"><span class="ect-icon"><i class="ect-icon-clock" aria-hidden="true"></i></span> '.$ev_time.'</span>
									</div>
									<meta itemprop="endDate" content="'.tribe_get_end_date($event_id, false, 'Y-m-dTg:i').'">';
				}
				else if($date_format=="jMl") {
					$event_schedule='<div class="ect-date-area '.$template.'-schedule" itemprop="startDate" content="'.tribe_get_start_date($event_id, false, 'Y-m-dTg:i').'">
									<span class="ev-day">'.tribe_get_start_date($event_id, false, 'j' ).'</span>
									<span class="ev-mo">'.tribe_get_start_date($event_id, false, 'M' ).'</span>
									<span class="ev-weekday">'.tribe_get_start_date($event_id, false, 'l' ).'</span>
									</div>
									<meta itemprop="endDate" content="'.tribe_get_end_date($event_id, false, 'Y-m-dTg:i').'">';
				}
				else if($date_format=="full") {
					$event_schedule='<div class="ect-date-area '.esc_attr($template).'-schedule" itemprop="startDate" content="'.tribe_get_start_date($event_id, false, 'Y-m-dTg:i').'">
									<span class="ev-day">'.tribe_get_start_date($event_id, false, 'd' ).'</span>
									<span class="ev-mo">'.tribe_get_start_date($event_id, false, 'F' ).'</span>
									<span class="ev-yr">'.tribe_get_start_date($event_id, false, 'Y' ).'</span>
									<span class="ev-time"><span class="ect-icon"><i class="ect-icon-clock" aria-hidden="true"></i></span> '.$ev_time.'</span>
									</div>
									<meta itemprop="endDate" content="'.tribe_get_end_date($event_id, false, 'Y-m-dTg:i').'">';
                }
                else if($date_format=="d.FY") {
					$event_schedule='<div class="ect-date-area '.esc_attr($template).'-schedule" itemprop="startDate" content="'.tribe_get_start_date($event_id, false, 'Y-m-dTg:i').'">
									<span class="ev-day">'.tribe_get_start_date($event_id, false, 'd' ).'. </span>
									<span class="ev-mo">'.tribe_get_start_date($event_id, false, 'F' ).'</span>
									<span class="ev-yr">'.tribe_get_start_date($event_id, false, 'Y' ).'</span>
									</div>
									<meta itemprop="endDate" content="'.tribe_get_end_date($event_id, false, 'Y-m-dTg:i').'">';
                }
                else if($date_format=="d.F") {
					$event_schedule='<div class="ect-date-area '.esc_attr($template).'-schedule" itemprop="startDate" content="'.tribe_get_start_date($event_id, false, 'Y-m-dTg:i').'">
									<span class="ev-day">'.tribe_get_start_date($event_id, false, 'd' ).'. </span>
									<span class="ev-mo">'.tribe_get_start_date($event_id, false, 'F' ).'</span>
									</div>
									<meta itemprop="endDate" content="'.tribe_get_end_date($event_id, false, 'Y-m-dTg:i').'">';
                }
                else if($date_format=="d.Ml") {
					$event_schedule='<div class="ect-date-area '.esc_attr($template).'-schedule" itemprop="startDate" content="'.tribe_get_start_date($event_id, false, 'Y-m-dTg:i').'">
									<span class="ev-day">'.tribe_get_start_date($event_id, false, 'd' ).'. </span>
									<span class="ev-mo">'.tribe_get_start_date($event_id, false, 'M' ).'</span>
									<span class="ev-yr">'.tribe_get_start_date($event_id, false, 'l' ).'</span>
									</div>
									<meta itemprop="endDate" content="'.tribe_get_end_date($event_id, false, 'Y-m-dTg:i').'">';
                }
                else if($date_format=="ldF") {
					$event_schedule='<div class="ect-date-area '.esc_attr($template).'-schedule" itemprop="startDate" content="'.tribe_get_start_date($event_id, false, 'Y-m-dTg:i').'">
									<span class="ev-day">'.tribe_get_start_date($event_id, false, 'l' ).'</span>
									<span class="ev-mo">'.tribe_get_start_date($event_id, false, 'd' ).'</span>
									<span class="ev-yr">'.tribe_get_start_date($event_id, false, 'F' ).'</span>
									</div>
									<meta itemprop="endDate" content="'.tribe_get_end_date($event_id, false, 'Y-m-dTg:i').'">';
                }
                else if($date_format=="Mdl") {
					$event_schedule='<div class="ect-date-area '.esc_attr($template).'-schedule" itemprop="startDate" content="'.tribe_get_start_date($event_id, false, 'Y-m-dTg:i').'">
									<span class="ev-day">'.tribe_get_start_date($event_id, false, 'M' ).'</span>
									<span class="ev-mo">'.tribe_get_start_date($event_id, false, 'd' ).'</span>
									<span class="ev-yr">'.tribe_get_start_date($event_id, false, 'l' ).'</span>
									</div>
									<meta itemprop="endDate" content="'.tribe_get_end_date($event_id, false, 'Y-m-dTg:i').'">';
                }
                else if($date_format=="dFT") {
					$event_schedule='<div class="ect-date-area '.esc_attr($template).'-schedule" itemprop="startDate" content="'.tribe_get_start_date($event_id, false, 'Y-m-dTg:i').'">
									<span class="ev-day">'.tribe_get_start_date($event_id, false, 'd' ).'</span>
									<span class="ev-mo">'.tribe_get_start_date($event_id, false, 'F' ).'</span>
									<span class="ev-time"><span class="ect-icon"><i class="ect-icon-clock" aria-hidden="true"></i></span> '.$ev_time.'</span>
									</div>
									<meta itemprop="endDate" content="'.tribe_get_end_date($event_id, false, 'Y-m-dTg:i').'">';
				}
				elseif($date_format=="custom"){
					$event_schedule = '<span class="ect-custom-schedule">'.tribe_events_event_schedule_details($event_id).'</span>';
					}
				else {
				
					$event_schedule='<div class="ect-date-area '.esc_attr($template).'-schedule" itemprop="startDate" content="'.tribe_get_start_date($event_id, false, 'Y-m-dTg:i').'">
									<span class="ev-day">'.tribe_get_start_date($event_id, false, 'd' ).'</span>
									<span class="ev-mo">'.tribe_get_start_date($event_id, false, 'F' ).'</span>
									<span class="ev-yr">'.tribe_get_start_date($event_id, false, 'Y' ).'</span>
									</div>
									<meta itemprop="endDate" content="'.tribe_get_end_date($event_id, false, 'Y-m-dTg:i').'">';
				}
				/*Date Format END*/
				return $event_schedule;
		}

		// grab events time for later use
		public function ect_tribe_event_time($post_id, $display = true ) {
			$event =$post_id;
			if ( tribe_event_is_all_day( $event ) ) { // all day event
				if ( $display ) {
					_e( 'All day', 'the-events-calendar' );
				}
				else {
					return __( 'All day', 'the-events-calendar' );
				}
			}
			elseif ( tribe_event_is_multiday( $event ) ) { // multi-date event
				$start_date = tribe_get_start_date(  $event, false, false );
				$end_date = tribe_get_end_date(  $event, false, false );
				if ( $display ) {
					printf( __( '%s - %s', 'ect' ), $start_date, $end_date );
				}
				else {
					return sprintf( __( '%s - %s', 'ect' ), $start_date, $end_date );
				}
			}
			else {
				$time_format = get_option( 'time_format' );
				$start_date = tribe_get_start_date( $event, false, $time_format );
				$end_date = tribe_get_end_date( $event, false, $time_format );
				if ( $start_date !== $end_date ) {
					if ( $display ) {
						printf( __( '%s - %s', 'ect' ), $start_date, $end_date );
					}
					else {
						return sprintf( __( '%s - %s', 'ect' ), $start_date, $end_date );
					}
				}
				else {
					if ( $display ) {
						printf( '%s', $start_date );
					}
					else {
						return sprintf( '%s', $start_date );
					}
				}
			}
		}
		// grab recurring event detials
		public function ect_tribe_event_recurringinfo( $before = '', $after = '', $link_all = true ) {
			if ( !function_exists('tribe_is_recurring_event') ) {
				return false;
			}
			global $post;
			$info = '';
			if ( tribe_is_recurring_event( $post->ID ) ) {
				if ( function_exists( 'tribe_get_recurrence_text' ) ) {
					$info .= tribe_get_recurrence_text( $post->ID );
				}
				if ( $link_all && function_exists( 'tribe_all_occurences_link' ) ) {
					$info .= sprintf( ' <a href="%s">%s</a>', esc_url( tribe_all_occurences_link( $post->ID, false ) ), __( '(See All)', 'ect' ) );
				}
			}
			if ( $info ) {
				$info = $before.$info.$after;
			}
			return $info;
		}

	/*
	On activation save some settings for later use
	*/
 		public static function activate() {
			if( file_exists( plugin_dir_path( __DIR__ ) . "template-events-calendar/events-calendar-templates.php"  ) ){
				include_once( ABSPATH . "wp-admin/includes/plugin.php" );
			if( is_plugin_active( "template-events-calendar/events-calendar-templates.php" ) ){
				deactivate_plugins('template-events-calendar/events-calendar-templates.php');
				
			}  
		}
			update_option("ect-v",ECT_VERSION_CURRENT);
			update_option("ect-type","PRO");
			update_option("ect-installDate",date('Y-m-d h:i:s') );
			update_option("ect-ratingDiv","no");
			update_option('ect_do_activation_redirect', true);
			
		}
// set rest url object for geneator data
		public function ect_rest_url() {
			?>
			<!-- TinyMCE Shortcode Plugin -->
			<script type='text/javascript'>
			var ectRestUrl='<?php echo get_rest_url(null, '/tribe/events/v1/');?>'
			</script>
			<!-- TinyMCE Shortcode Plugin -->
			<?php
		} 


/*
Masonry Layout load more handlers
*/
function ect_common_loadmore_handler(){
	$ect_args=$_POST['query'];

	$settings=$_POST['settings'];
	$date_format=$settings['date_format'];
	$hide_venue=$settings['hide_venue'];
	$styles=$settings['style'];
	$style=$settings['style'];
	$template=$settings['template'];
	$response=array();
	$events_html='';
	$output='';
	$no_events='';
	$response_type="ajax";
	$socialshare=$settings['socialshare'];
	$show_description=$settings['show_description'];
	
	//$ect_args['paged']=(int)$_POST['paged'];
	$ect_args['post__not_in']=json_decode($_POST['exclude_events']);
	$excludePosts=array();
	include(ECT_PRO_PLUGIN_DIR.'/includes/ect-load-more-handler.php' );
	echo json_encode($response);
	wp_die();
}	
		
/*
		Masonry Layout load more handlers
*/
function ect_catfilters_load_more(){
	
			$template="masonry-view";
			$grid_style='';
			$date_format='';
			$ect_args=$_POST['query'];
			$settings=$_POST['settings'];
			$ect_grid_columns=$settings['ect_grid_columns'];
		
			$grid_style=$settings['grid_style'];
			$date_format=$settings['date_format'];
			$hide_venue=$settings['hide_venue'];
			$ect_args['paged']=(int)$_POST['paged'];
			$ect_args['post__not_in']=json_decode($_POST['exclude_events']);
			$excludePosts=array();
			$cat=$_POST['cat'];
			$response=array();
			$events_html='';
			$output='';
			$no_events='';
			$response_type="ajax";
			$socialshare=$settings['socialshare'];
			$show_description = $settings['show_description'];
			
	if($cat==""){
		unset($ect_args['tax_query']);
	}else{
			$ect_args['tax_query'] = array(
				'relation' => 'OR',
				array(
					'taxonomy' => 'tribe_events_cat',
					'field' => 'name',
					'terms' =>$cat,
				),
				array(
					'taxonomy' => 'tribe_events_cat',
					'field' => 'slug',
					'terms' =>$cat,
				));
	}		
	
				unset($ect_args['featured']);
				//	unset($ect_args['posts_per_page']);
			include(ECT_PRO_PLUGIN_DIR.'/includes/ect-masonry-loop.php' );
			echo json_encode($response);
			wp_die();
}


	} //class end here
}  



// Installation and uninstallation hooks
register_activation_hook(__FILE__, array('EventsCalendarTemplatesPro', 'activate'));
$ect=new EventsCalendarTemplatesPro;

 	
