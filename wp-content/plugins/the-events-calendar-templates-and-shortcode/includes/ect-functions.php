<?php 


  	/*** Register style/scripts assets */
    function ect_styles() {
		wp_register_style('ect-common-styles', ECT_PRO_PLUGIN_URL . 'assets/css/ect-common-styles.min.css',null,null,'all' );	
         wp_register_style('ect-timeline-view-styles', ECT_PRO_PLUGIN_URL . 'assets/css/ect-timeline-view.min.css',null,null,'all' );	
         wp_register_style('ect-list-view-styles', ECT_PRO_PLUGIN_URL . 'assets/css/ect-list-view.min.css',null,null,'all' );	
         wp_register_style('ect-grid-view-styles', ECT_PRO_PLUGIN_URL . 'assets/css/ect-grid-view.min.css',null,null,'all' );
        wp_register_style('ect-carousel-view-styles', ECT_PRO_PLUGIN_URL . 'assets/css/ect-carousel-view.min.css',null,null,'all' );
        wp_register_style('ect-slider-view-styles', ECT_PRO_PLUGIN_URL . 'assets/css/ect-slider-view.min.css',null,null,'all' );
        wp_register_style('ect-accordion-view-styles', ECT_PRO_PLUGIN_URL . 'assets/css/ect-accordion-view.min.css',null,null,'all' );

        wp_register_style('ect-grid-view-bootstrap', ECT_PRO_PLUGIN_URL . 'assets/css/bootstrap.min.css',null,null,'all' );
        wp_register_style('ect-slider-slick', ECT_PRO_PLUGIN_URL . 'assets/css/slick.min.css',null,null,'all' );
        wp_register_style('ect-collapse-bootstrap', ECT_PRO_PLUGIN_URL . 'assets/css/collapse-bootstrap-4.0.min.css',null,null,'all' );
                
        wp_register_script('ect-accordion-view-js', ECT_PRO_PLUGIN_URL . 'assets/js/ect-accordion-view.min.js',array('jquery'),null,true);
        wp_register_script( 'ect-slider-slick-js', ECT_PRO_PLUGIN_URL . 'assets/js/slick.min.js',array('jquery'),null,true);
        
        // mansory layout scripts
        wp_register_script('imagesloaded', ECT_PRO_PLUGIN_URL . 'assets/js/imagesloaded.pkgd.min.js');
        wp_register_script('masonry-lib', ECT_PRO_PLUGIN_URL . 'assets/js/masonry-3.1.4.js',array('jquery'),null,true);
        wp_register_script('masonry.filter', ECT_PRO_PLUGIN_URL . 'assets/js/masonry.filter.js',array('jquery','masonry-lib'),null,true);
        wp_register_script('ect-masonry-js', ECT_PRO_PLUGIN_URL . 'assets/js/ect-masonry.js',array('jquery','masonry-lib','masonry.filter','imagesloaded'),null,true);

        //like and share style and script	
        wp_register_script('ect-events_data',ECT_PRO_PLUGIN_URL .'assets/js/ect-sendajax-request.min.js',array('jquery'),null,true);
        wp_register_script('ect-sharebutton',ECT_PRO_PLUGIN_URL .'assets/js/ect-sharebutton.js',array('jquery'),null,true);
        

        wp_register_script('ect-common-scripts',ECT_PRO_PLUGIN_URL .'assets/js/ect.min.js',array('jquery'),null,true);
    }

    /*** Loading required styles/scripts according to the type of layout */
	 function ect_load_assets($template, $style, $slider_pp_id, $autoplay, $carousel_slide_show ) {
			wp_enqueue_style('ect-common-styles');
			
			/*** TIMELINE styles/scripts */
			if(in_array($template,array("timeline","classic-timeline",'timeline-view'))) {
                wp_enqueue_style('ect-timeline-view-styles');
                
                wp_enqueue_script('ect-common-scripts');
			}

			/*** LIST styles/scripts */
			else if(in_array($template,array("default","classic-list",'modern-list'))) {
                wp_enqueue_style('ect-list-view-styles');
                wp_enqueue_script('ect-common-scripts');
			}

			/*** SLIDER styles/scripts */
			else if(in_array($template,array("slider-view"))) {
				wp_enqueue_script('ect-common-scripts');
				wp_enqueue_style('ect-slider-slick');
				wp_enqueue_style('ect-slider-view-styles');
				wp_enqueue_script('ect-slider-slick-js');
				$next_arrow='<div class="ctl-slick-next"><i class="ect-icon-right"></i></div>';
				$prev_arrow='<div class="ctl-slick-prev"><i class="ect-icon-left"></i></div>';
				wp_add_inline_script('ect-slider-slick-js',"
		      		(function($) {
						$('#".$slider_pp_id."').not('.slick-initialized').slick({
							dots: false,
							infinite: true,
							nextArrow:'".$next_arrow."',
							prevArrow:'".$prev_arrow."',
							slidesToShow: 1,
							infinite: $autoplay,
                            autoplay: $autoplay,
                        });
					})(jQuery);
		     	");
			}
	
			/*** GRID styles/scripts */
			else if($template=="grid-view" ) {
				wp_enqueue_style('ect-grid-view-bootstrap');
				wp_enqueue_style('ect-grid-view-styles');
                wp_enqueue_script('ect-common-scripts');
			}
			
			/*** MASONRY styles/scripts */
			elseif($template=="masonry-view"){
				wp_enqueue_script('ect-common-scripts');
				wp_enqueue_style('ect-grid-view-bootstrap');
				wp_enqueue_style('ect-grid-view-styles');
				wp_enqueue_script('masonry-lib');
				wp_enqueue_script('imagesloaded');		
				wp_enqueue_script('masonry.filter');
				wp_enqueue_script('ect-masonry-js');	
			}
			
			/*** CAROUSEL styles/scripts */
			else if(in_array($template,array("carousel-view"))) {
				wp_enqueue_script('ect-common-scripts');
				wp_enqueue_style('ect-slider-slick');
				wp_enqueue_style('ect-carousel-view-styles');
				wp_enqueue_script('ect-slider-slick-js');
				$next_arrow='<div class="ctl-slick-next"><i class="ect-icon-right"></i></div>';
				$prev_arrow='<div class="ctl-slick-prev"><i class="ect-icon-left"></i></div>';
				wp_add_inline_script( 'ect-slider-slick-js',"
					(function($) {
						$('#".$slider_pp_id."').not('.slick-initialized').slick({
							dots: false,
							infinite: $autoplay,
							autoplay: $autoplay,
                            slidesToShow: $carousel_slide_show,
                            arrows:true,
							slidesToScroll: 1,
							nextArrow:'".$next_arrow."',
							prevArrow:'".$prev_arrow."',
							responsive: [
							{
							breakpoint: 950,
							settings: {
								slidesToShow: 2
							}
							},
							{
							breakpoint: 580,
							settings: {
								slidesToShow: 1
							}
							}
							]
						});
					})(jQuery);
				"); 
			}

			/*** ACCORDION styles/scripts */
			elseif($template=="accordion-view") {
				wp_enqueue_style('ect-accordion-view-styles');
				wp_enqueue_style('ect-collapse-bootstrap');
				wp_enqueue_style('ect-custom-icons');
                wp_enqueue_script('ect-common-scripts');
				
			}
		}

    
/**
 * category Filter function
 */
function ect_cats_list() {
        $ect_cat_arr=array();
        if(version_compare(get_bloginfo('version'),'4.5.0', '>=') ){
            $terms = get_terms(array(
            'taxonomy' => 'tribe_events_cat',
            'hide_empty' =>true,
            ));
        }
        else {
            $terms = get_terms('tribe_events_cat', array('hide_empty' => true,) );
        }
        if (!empty($terms) || !is_wp_error($terms)) {
            $allPosts=0;
            
            foreach ($terms as $term) {
                $ect_cat_arr[$term->slug] =array("name"=>$term->name,"count"=>$term->count);
                $allPosts+=$term->count;	
            }
            $ect_cat_arr['all']=array("name"=>__('All','ect'),"count"=>$allPosts);
            }
            return $ect_cat_arr;
                
}
  
// generate category filters list HTML

 function create_cat_filter_html($selected_cat,$post_per_page){
	$ect_all_categories=ect_cats_list();
    $html_output='';
    if(count($ect_all_categories)>1){
        $html_output .='<div class="ect-fitlers-wrapper">
        <ul class="ect-categories">';
        $active_cat='';
        asort($ect_all_categories);
        $prefetch='';
        foreach($ect_all_categories as $slug=> $details){
            $totalPosts=$details['count'];
            if($totalPosts>0){
            if($totalPosts>$post_per_page){
                $pages=ceil($totalPosts/$post_per_page);
            }else{
                $pages=0;
            }
                if($slug==$selected_cat){
                $active_cat='ect-active';
                $prefetch='true';
            }else{
                $active_cat='';
                $prefetch='false';
            }
            if($slug=="all"){
                $slug="";
            }else{
                $slug=$slug;
            }
    
         $html_output .= '<li data-paged="0" data-prefetch="'.$prefetch.'" 
         data-pages="'.$pages.'"
          data-posts="'.$totalPosts.'" class="ect-cat-items '.$active_cat.'"
            data-filter="'. $slug.'">'. $details['name'].'</li>';
        }
          } 
        $html_output.='</ul></div>';
        return $html_output;
    }
 }

 	// admin side timing
      function ect_set_notice_timing(){
        if(version_compare(get_option('ect-v'),'1.7', '<')){		
            set_transient( 'ect-assn-timing', true, DAY_IN_SECONDS);
            }
            if( isset( $_GET['ect_disable_notice'] ) && !empty( $_GET['ect_disable_notice'] ) ){
            $rs=delete_transient( 'ect-assn-timing' );
            update_option('ect-v',ECT_VERSION_CURRENT);
            
                }
        }
        /**
         * Admin for save settings
         * @since 1.7
         */
        function   ect_admin_save_settings_notice(){
            /* Check transient */
            //if(version_compare('1.6','1.7', '<')){
        if(version_compare(get_option('ect-v'),'1.7', '<')){
        
                if( get_transient( 'ect-assn-timing' ) ){
                
                    $dont_disturb_url = esc_url( get_admin_url() . '?ect_disable_notice=1' );
                    ?>
                    <div class="updated notice is-dismissible">
                    <p><strong>
                    Thanks for updating! The Events Calendar Shortcode And Templates Pro.
                    </strong><br/><i style="color:red;">It is a major design & gutenberg friendly update. After update, please clear your cache and update/save template settings & shortcode again for best design results. <a style="font-weight:bold" href="<?php echo esc_url( get_admin_url(null, 'edit.php?post_type=tribe_events&page=edit.php%3Fpost_type%3Dtribe_events-shortcode---template-settings') )?>"> Update Template Settings</a> | <a href="<?php echo $dont_disturb_url ?>" class="ect-review-done "> Already Saved !</a></i>
                    </p>
                    </div>
                    <?php
                    delete_transient( 'ect-assn-timing' );
                
                }
            }
        }
        // remove notice if users has already saved settings
         function ect_remove_wpcalpha() {
            $current_screen = get_current_screen();
            if( $current_screen ->id === "tribe_events_page_edit?post_type=tribe_events-events-template-settings" ) { 
                wp_dequeue_script( 'wp-color-picker-alpha' );
            }
        }

// on plugin activation redirect to the setting page
     function ect_plugin_redirect() {
        if (get_option('ect_do_activation_redirect', false)) {
            delete_option('ect_do_activation_redirect');
            exit( wp_redirect( admin_url( 'edit.php?post_type=tribe_events&page=edit.php%3Fpost_type%3Dtribe_events-shortcode---template-settings' ) ) );

        }
    }


/**
 * This file is used to share events.
 * 
 * @package the-events-calendar-templates-and-shortcode/includes
 */

function ect_pro_share_button($event_id){
  

    $ect_sharecontent = '';
    $ect_geturl = urlencode(get_permalink($event_id));
    //$ect_geturl = get_permalink($event_id);
    $ect_gettitle = htmlspecialchars(urlencode(html_entity_decode(get_the_title($event_id), ENT_COMPAT, 'UTF-8')), ENT_COMPAT, 'UTF-8');
    $ect_getthumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $event_id ), 'full' );
    $subject= str_replace("+"," ",$ect_gettitle);
    // Construct sharing URL
      $ect_twitterURL = 'https://twitter.com/intent/tweet?text='.$ect_gettitle.'&amp;url='.$ect_geturl.'';
      $ect_whatsappURL = 'https://wa.me/?text='.$ect_gettitle . ' ' . $ect_geturl;
      $ect_facebookurl = 'https://www.facebook.com/sharer/sharer.php?u='.$ect_geturl.'';
      $ect_emailUrl = 'mailto:?Subject='.$subject.'&Body='.$ect_geturl.'';
      //$ect_linkedinUrl = "https://www.linkedin.com/sharing/share-offsite/?mini=true&amp;url=$ect_geturl";
      $ect_linkedinUrl = "http://www.linkedin.com/shareArticle?mini=true&amp;url=$ect_geturl";
      // Add sharing button at the end of page/page content
      $ect_sharecontent .= '<div class="ect-share-wrapper">';
      $ect_sharecontent .= '<i class="ect-icon-share"></i>';
      $ect_sharecontent .= '<div class="ect-social-share-list">';
      $ect_sharecontent .= '<a class="ect-share-link" href="'.$ect_facebookurl.'" target="_blank" title="Facebook" aria-haspopup="true"><i class="ect-icon-facebook"></i></a>';
      $ect_sharecontent .= '<a class="ect-share-link" href="'.$ect_twitterURL.'" target="_blank" title="Twitter" aria-haspopup="true"><i class="ect-icon-twitter"></i></a>';
      $ect_sharecontent .= '<a class="ect-share-link" href="'.$ect_linkedinUrl.'" target="_blank" title="Linkedin" aria-haspopup="true"><i class="ect-icon-linkedin"></i></a>';
      $ect_sharecontent .= '<a class="ect-email" href="'.$ect_emailUrl.' "title="Email" aria-haspopup="true"><i class="ect-icon-mail"></i></a>';
      $ect_sharecontent .= '<a class="ect-share-link" href="'.$ect_whatsappURL.'" target="_blank" title="WhatsApp" aria-haspopup="true"><i class="ect-icon-whatsapp"></i></a>';
      $ect_sharecontent .= '</div></div>';
          return $ect_sharecontent;
  }
  
  
  
if ( ! function_exists( 'ect_tribe_tickets_buy_button' ) ) {

	/**
	 * Echos Remaining Ticket Count and Purchase Buttons for an Event
	 *
	 * @since  4.5
	 *
	 * @param bool $echo Whether or not we should print
	 *
	 * @return string
	 */
	function ect_tribe_tickets_buy_button( $echo = true,$event_id ) {
		//$event_id = get_the_ID();

		// check if there are any tickets on sale
		if ( ! tribe_events_has_tickets_on_sale( $event_id ) ) {
			return null;
		}

		// get an array for ticket and rsvp counts
		$types = Tribe__Tickets__Tickets::get_ticket_counts( $event_id );

		// if no rsvp or tickets return
		if ( ! $types ) {
			return null;
		}

		$html = array();
		$parts = array();

		// If we have tickets or RSVP, but everything is Sold Out then display the Sold Out message
		foreach ( $types as $type => $data ) {
			if ( ! $data['count'] ) {
				continue;
			}

			if ( ! $data['available'] ) {
				$parts[ $type . '-stock' ] = '<span class="tribe-out-of-stock">' . esc_html_x( 'Sold out', 'list view stock sold out', 'ect' ) . '</span>';

				// Only re-apply if we don't have a stock yet
				if ( empty( $html['stock'] ) ) {
					$html['stock'] = $parts[ $type . '-stock' ];
				}
			} else {
				$stock = $data['stock'];
				if ( $data['unlimited'] || ! $data['stock'] ) {
					// if unlimited tickets, tickets with no stock and rsvp, or no tickets and rsvp unlimited - hide the remaining count
					$stock = false;
				}

				$stock_html = '';

				if ( $stock ) {
					$threshold = Tribe__Settings_Manager::get_option( 'ticket-display-tickets-left-threshold', 0 );

					/**
					 * Overwrites the threshold to display "# tickets left".
					 *
					 * @param int   $threshold Stock threshold to trigger display of "# tickets left"
					 * @param array $data      Ticket data.
					 * @param int   $event_id  Event ID.
					 *
					 * @since 4.10.1
					 */
					$threshold = absint( apply_filters( 'tribe_display_tickets_left_threshold', $threshold, $data, $event_id ) );

					if ( ! $threshold || $stock <= $threshold ) {

						$number = number_format_i18n( $stock );
						if ( 'rsvp' === $type ) {
							$text = _n( '%s spot left', '%s spots left', $stock, 'ect' );
						} else {
							$text = _n( '%s ticket left', '%s tickets left', $stock, 'ect' );
						}

						$stock_html = '<span class="tribe-tickets-left">'
							. esc_html( sprintf( $text, $number ) )
							. '</span>';
					}
				}

				$parts[ $type . '-stock' ] = $html['stock'] = $stock_html;

				if ( 'rsvp' === $type ) {
					$button_label  = __( 'RSVP Now','ect' );
					$button_anchor = '#rsvp-now';
				} else {
					$button_label  = __( 'Buy Now','ect' );
					$button_anchor = '#tpp-buy-tickets';
				}

				$permalink = get_the_permalink( $event_id );
				$query_string = parse_url( $permalink, PHP_URL_QUERY );
				$query_params = empty( $query_string ) ? array() : (array) explode( '&', $query_string );

			//	$button = '<form method="get" action="' . esc_url( $permalink . $button_anchor ) . '">';
		
				$html['link']= '<a href="'.esc_url( $permalink . $button_anchor ).'">' . $button_label . '</a>';
			
				
			}
		}

		/**
		 * Filter the ticket count and purchase button
		 *
		 * @since  4.5
		 *
		 * @param array $html     An array with the final HTML
		 * @param array $parts    An array with all the possible parts of the HTMl button
		 * @param array $types    Ticket and RSVP count array for event
		 * @param int   $event_id Post Event ID
		 */
		$html = apply_filters( 'tribe_tickets_buy_button', $html, $parts, $types, $event_id );
		$html = implode( "\n", $html );

		if ( $echo ) {
			echo $html;
		}

		return $html;
	}
}
function ect_pro_get_event_image($event_id,$size){
	$ev_post_img='';
	$feat_img_url = wp_get_attachment_image_src(get_post_thumbnail_id($event_id),$size);
	if(!empty($feat_img_url) && $feat_img_url[0] !=false){
		$ev_post_img = $feat_img_url[0];
		}elseif ($feat_img_url==''|| $feat_img_url==false){
			$tect_settings = TitanFramework::getInstance( 'ect' );
			$non_feat_img_url = $tect_settings->getOption( 'ect_no_featured_img' );
			if ($non_feat_img_url!='' && is_numeric( $non_feat_img_url ) )
			 {
			$imageAttachment = wp_get_attachment_image_src( $non_feat_img_url,$size);
			$ev_post_img= $imageAttachment[0];
			}else{
				$ev_post_img=ECT_PRO_PLUGIN_URL."assets/images/event-template-bg.png";
			}
		}else{
			$ev_post_img=ECT_PRO_PLUGIN_URL."assets/images/event-template-bg.png";
		}
		return $ev_post_img;
}
function ect_display_category($event_id){
	$ect_cate = '';
	$tect_settings = TitanFramework::getInstance( 'ect' );
	$ect_cate_sett = $tect_settings->getOption( 'ect_display_categoery' );
	if($ect_cate_sett=='ect_enable_cat'){
		$ect_cate = get_the_term_list($event_id, 'tribe_events_cat', '<ul class="tribe_events_cat"><li>', '</li><li>', '</li></ul>' );
	}
	return $ect_cate;
}

// custom css for admin menu
function ect_admin_menu_custom_styles() {
	echo '<style>
	.wp-submenu a[href*="events-shortcode---template-settings"],
	.wp-submenu a[href*="ect-settings"] {
		font-size: 11.8px !important;
		background: #4f555c;
		color: #fff !important;
		position: relative;
		padding-left: 16px !IMPORTANT;
	}
	.wp-submenu a[href*="events-shortcode---template-settings"]:hover,
	.wp-submenu a[href*="events-shortcode---template-settings"].current,
	.wp-submenu a[href*="ect-settings"]:hover,
	.wp-submenu a[href*="ect-settings"].current {
		background: #0073aa !important;
	}
	.wp-submenu a[href*="events-shortcode---template-settings"]:before,
	.wp-submenu a[href*="ect-settings"]:before {
		content: " - ";
		position: absolute;
		left: 5px;
		top: 10px;
		font-size: 18px;
	  }
	</style>';
}
