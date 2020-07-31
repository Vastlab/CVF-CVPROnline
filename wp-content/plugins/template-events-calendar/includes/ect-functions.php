<?php 
// events custom date format creator function 
function ect_custom_date_formats($date_format,$template,$event_id,$ev_time){
        /*Date Format START*/
        $ev_day=tribe_get_start_date($event_id, false, 'd' );
        $ev_month=tribe_get_start_date($event_id, false, 'M' );
        $ev_full_month=tribe_get_start_date($event_id, false, 'F' );
        $ev_year=tribe_get_start_date($event_id, false, 'Y' );
        $output='';
 
				if($date_format=="DM") {
					$event_schedule='<span class="ev-day">'. $ev_day.'</span>
					<span class="ev-mo">'.$ev_month.'</span>';
				}
				else if($date_format=="dT") {
					$event_schedule= '<span class="ev-weekday">'.substr(tribe_get_start_date($event_id, false, 'l' ),0,4).'</span>
                                      <span class="ev-mo">'.$ev_month.'</span><span class="ev-day">'.$ev_day.'</span><br/>
                                      <span class="ev-time">'.$ev_time.'</span>';
				}
				else if($date_format=="MD") {
					$event_schedule='<span class="ev-mo">'.$ev_month.'</span>
									<span class="ev-day">'.$ev_day.'</span>';
				}
				else if($date_format=="dT") {//$short_day = esc_attr(get_weekday_abbrev( $ev_day ) );
                    $short_day = substr(tribe_get_start_date($event_id, false, 'l' ),0,3);
                    //                    $short_day = tribe_get_start_date($event_id, false, 'l' );
					$event_schedule= '<span class="ev-weekday">'.$short_day.'</span>
                                      <span class="ev-mo">'.$ev_month.'</span><span class="ev-day"> '.$ev_day.'</span><br/>
                                      <span class="ev-time">'.$ev_time.'</span>';
				}
				else if($date_format=="FD") {
					$event_schedule='<span class="ev-mo">'.$ev_full_month.'</span>
									<span class="ev-day">'.$ev_day.'</span>
									';
				}
				else if($date_format=="DF") {
					$event_schedule='<span class="ev-day">'.$ev_day.'</span>
									<span class="ev-mo">'.$ev_full_month.'</span>
									';
				}
				else if($date_format=="FD,Y") {
					$event_schedule='<span class="ev-mo">'.$ev_full_month.'</span>
									<span class="ev-day">'.$ev_day.'<i class="date-comma">, </i></span>
									<span class="ev-yr">'.$ev_year.'</span>
									';
				}
				else if($date_format=="MD,Y") {
					$event_schedule='<span class="ev-mo">'.$ev_month.'</span>
									<span class="ev-day">'.$ev_day.'<i class="date-comma">, </i></span>
									<span class="ev-yr">'.$ev_year.'</span>
									';
				}
				else if($date_format=="MD,YT") {
					$event_schedule='<span class="ev-mo">'.$ev_month.'</span>
									<span class="ev-day">'.$ev_day.'<i class="date-comma">, </i></span>
									<span class="ev-yr">'.$ev_year.'</span>
									<span class="ev-time"><span class="ect-icon"><i class="ect-icon-clock"></i></span> '.$ev_time.'</span>
									';
				}
				else if($date_format=="DML") {
					$event_schedule='<span class="ev-day">'.$ev_day.'</span>
									<span class="ev-mo">'.$ev_month.'</span>
									<span class="ev-time">'.tribe_get_start_date($event_id, false, 'l' ).'</span>
									';
				}
				else if($date_format=="D.M.Y") {
					$event_schedule='<span class="ev-day">'.$ev_day.'<i class="date-comma">. </i></span>
									<span class="ev-mo">'.tribe_get_start_date($event_id, false, 'm' ).'<i class="date-comma">. </i></span>
									<span class="ev-yr">'.$ev_year.'</span>
									';
				}
				else if($date_format=="full") {
					$event_schedule='<span class="ev-day">'.$ev_day.'</span>
									<span class="ev-mo">'.$ev_full_month.'</span>
									<span class="ev-yr">'.$ev_year.'</span>
									<span class="ev-time"><span class="ect-icon"><i class="ect-icon-clock"></i></span> '.$ev_time.'</span>
									';
				}
				else if($date_format=="jMl") {
					$event_schedule='<span class="ev-day">'.tribe_get_start_date($event_id, false, 'j' ).'</span>
									<span class="ev-mo">'.$ev_month.'</span>
									<span class="ev-weekday">'.tribe_get_start_date($event_id, false, 'l' ).'</span>
									';
				}
				else if($date_format=="d.FY") {
					$event_schedule='<span class="ev-day">'.$ev_day.'. </span>
									<span class="ev-mo">'.$ev_full_month.'</span>
									<span class="ev-yr">'.$ev_year.'</span>
									';
				}
				else if($date_format=="d.F") {
					$event_schedule='<span class="ev-day">'.$ev_day.'. </span>
									<span class="ev-mo">'.$ev_full_month.'</span>
									';
                }
                else if($date_format=="d.Ml") {
					$event_schedule='<span class="ev-day">'.$ev_day.'. </span>
									<span class="ev-mo">'.$ev_month.'</span>
									<span class="ev-yr">'.tribe_get_start_date($event_id, false, 'l' ).'</span>
									';
                }
                else if($date_format=="ldF") {
					$event_schedule='<span class="ev-day">'.tribe_get_start_date($event_id, false, 'l' ).'</span>
									<span class="ev-mo">'.$ev_day.'</span>
									<span class="ev-yr">'.$ev_full_month.'</span>';
                }     
				else if($date_format=="dFT") {
					$event_schedule='<span class="ev-day">'.$ev_day.'</span>
									<span class="ev-mo">'.$ev_full_month.'</span>
									<span class="ev-time"><span class="ect-icon"><i class="ect-icon-clock" aria-hidden="true"></i></span> '.$ev_time.'</span>
									';
				}
				else if($date_format=="Mdl") {
                $event_schedule='<span class="ev-day">'.$ev_month.'</span>
                        <span class="ev-mo">'.$ev_day.'</span>
                        <span class="ev-yr">'.tribe_get_start_date($event_id, false, 'l' ).'</span>
                        ';
                }
				else {
					$event_schedule='<span class="ev-day">'.$ev_day.'</span>
					<span class="ev-mo">'.$ev_full_month.'</span>
					<span class="ev-yr">'.$ev_year.'</span>';
				}
				 $set_template = esc_attr($template);
                $output.='<div class="ect-date-area '.$set_template.'-schedule">';
                $output.=$event_schedule;
                $output.='</div>';
                return $output;
                /*Date Format END*/
}
    //grab event image
    function ect_get_event_image($event_id,$size){
        $ev_post_img='';
        $feat_img_url = wp_get_attachment_image_src(get_post_thumbnail_id($event_id),$size);
        if(isset($feat_img_url) && $feat_img_url[0] !=false){
            $ev_post_img = $feat_img_url[0];
            }elseif ($feat_img_url==''|| $feat_img_url==false){
                $tect_settings = TitanFramework::getInstance( 'ect' );
                $non_feat_img_url = $tect_settings->getOption( 'ect_no_featured_img' );
                if ($non_feat_img_url!='' && is_numeric( $non_feat_img_url ) )
                 {
                $imageAttachment = wp_get_attachment_image_src( $non_feat_img_url,$size);
                $ev_post_img= $imageAttachment[0];
                }else{
                    $ev_post_img=ECT_PLUGIN_URL."assets/images/event-template-bg.png";
                }
            }else{
                $ev_post_img=ECT_PLUGIN_URL."assets/images/event-template-bg.png";
            }
            return $ev_post_img;
    }
    // custom css for admin menu
  function ect_admin_menu_custom_styles() {
		echo '<style>
		.wp-submenu a[href*="events-template-settings"] {
			font-size: 11.8px !important;
			background: #4f555c;
			color: #fff !important;
			position: relative;
			padding-left: 16px !IMPORTANT;
		}
		.wp-submenu a[href*="events-template-settings"]:hover,
		.wp-submenu a[href*="events-template-settings"].current {
			background: #0073aa !important;
		}
		.wp-submenu a[href*="events-template-settings"]:before {
			content: " - ";
			position: absolute;
			left: 5px;
			top: 10px;
			font-size: 18px;
	  	}
		</style>';
   }
