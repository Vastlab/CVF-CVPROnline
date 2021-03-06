<?php

if (file_exists(ABSPATH . 'wp-content/themes/cvf-twentyseventeen-child/cvf-functions.php')) {
    //    ob_start();
    include_once ABSPATH . 'wp-content/themes/cvf-twentyseventeen-child/cvf-functions.php';
    //     ob_get_clean();
}



$list_style=$attribute['style'];
if($template=="modern-list") {
	$list_style='style-2';
}
else if($template=="classic-list") {
	$list_style='style-3';
}

$ev_post_img=ect_pro_get_event_image($event_id,$size='large');

/*** Default List Style 3 */
if(($style=="style-3" && $template=="default") || $template=="classic-list") {
	$events_html.='<div id="event-'.esc_attr($event_id).'" class="ect-list-post '.esc_attr($list_style).' '.esc_attr($event_type).'">';
	
	$events_html.='<div class="ect-list-date">'.$event_schedule.'</div>';           
	
	$events_html.='<div class="ect-clslist-event-info"> 
				<div class="ect-clslist-inner-container">
				<h2 class="ect-list-title">'.$event_title.'</h2> 
				<div class="ev-smalltime"><span class="ect-icon"><i class="ect-icon-clock"></i></span><span class="cls-list-time">'.$ev_time.'</span></div>
				';
	if (tribe_has_venue($event_id)) {
		$events_html.=$venue_details_html;
	}
	else{
		$events_html.='';
	}
	
	$events_html.='</div>';
	
	$events_html.=$event_cost;	
	$events_html.= $share_buttons;
	$events_html.='</div>';

	$events_html.='<div class="style-3-readmore">
				<a href="'.esc_url( tribe_get_event_link($event_id)).'" class="tribe-events-read-more" rel="bookmark">'.esc_html__( 'Find out more', 'the-events-calendar' ).'
				<i class="ect-icon-right-double"></i>
				</a>
				</div>
				</div>';
}


/*** Default List Style 2 */
else if (($style=="style-2" && $template=="default") || $template=="modern-list") {
	$events_html.='<div id="event-'.esc_attr($event_id).'" class="ect-list-post '.esc_attr($list_style).' '.esc_attr($event_type).'">';
	$event_single_link=esc_url( tribe_get_event_link($event_id));
	$event_title_att=get_the_title($event_id);
	$bg_styles="background-image:url('$ev_post_img');background-size:cover;background-position:bottom center;";

	$events_html.='<div class="ect-list-post-left">
	<div class="ect-list-img" style="'.$bg_styles.'"></div>
	<a class="ect-single-event-link" href="'.$event_single_link.'" title="'.$event_title_att.'">'.$event_title_att.'</a>';	
	$events_html.='</div><!-- left-post close -->';

	$events_html.='<div class="ect-list-post-right">
				<div class="ect-list-post-right-table">
				<div class="ect-list-description">
				<h2 class="ect-list-title">'.$event_title.'</h2>';
	
	if (tribe_has_venue($event_id)) {
		$events_html.=$venue_details_html;
	}
	else{
		$events_html.='';
	}

	$events_html.=$event_cost;	
	$events_html.=$event_content;
	$events_html.=$share_buttons;	
	$events_html.='</div>';

	$events_html .='<div class="modern-list-right-side">
				<div class="ect-list-date">'.$event_schedule.
    //     '<a href="' . \Tribe__Events__Main::instance()->esc_gcal_url( tribe_get_gcal_link() ) . '" title="' . esc_attr__( 'Add to Google Calendar', 'the-events-calendar' ) . '" "> ' . esc_html__( 'Add to Gcal  &nbsp;', 'the-events-calendar' ) . '</a>'.
    //                    '<a href="' . esc_url( tribe_get_single_ical_link() ) . '" title="' . esc_attr__( 'Download .ics file', 'the-events-calendar' ) . '"  class="tribe-events-ical tribe-events-button"> ' . esc_html__( '  Add to iCal', 'the-events-calendar' ) . '</a>'.
				'</div>
				</div>
				</div><!-- right-wrapper close -->
				</div><!-- event-loop-end -->';
}

/*** CVPR conf Boult List Style 4 */
else if (($style=="style-4" && $template=="default") || $template=="modern-list") {
    $paperid = conf_get_paperid($event_id);
    $teaserpic = conf_get_teaserpic($event_id);
    //    $paperid = tribe_get_cost($event_id,true);

	$events_html.='<div id="event-'.esc_attr($event_id).'" class="tb-list-post '.esc_attr($list_style).' '.esc_attr($event_type).'">';
	$event_single_link=esc_url( tribe_get_event_link($event_id));
	$event_title_att=get_the_title($event_id);
    $teasertxt = conf_get_teasertxt($event_id);

    	$events_html.='<div class="tb-list-post-left">';
    	$bg_styles="";
    //	$bg_styles="background-image:url('$teaserpic');background-size:128x 128px; background-repeat: no-repeat;background-position:bottom center;";
        //       $bg_styles="background-image:url('$teaserpic');background-size:128x 128px; background-repeat: no-repeat;background-position: center;";
        //       $bg_styles="background-image:url('$teaserpic');background-size:512x 512px; background-repeat: no-repeat;background-position: center;";           
    	$events_html.=	' <a class="ect-single-event-link" href="'.$event_single_link.'" title="'.$event_title_att.'" label="'.$event_title_att.'">';
        $events_html.= ' <div class="ect-list-img" style="'.$bg_styles.'"><img src="'.$teaserpic.'" width=128px height=128px class=center alt="Teaser picture for paper".$event_title_att."></div>';
              $events_html.= '</a>';


	$events_html.='</div><!-- left-post close -->';


	$events_html.='<div class="tb-list-post-right">
				<div class="tb-list-post-right-table">
				<div class="tb-list-description">
				<div class="tb-list-title" style="font-size:22px">'.$event_title.' </div>'
    . '<div class="Teasertxtlist" style="font-size:18px" > '. $teasertxt. '<div class="Authorlist"  style="font-size:16px">';
    $authors= conf_get_authors($event_id);
    if(strlen($authors)>2){
        $events_html.= '  &nbsp; &nbsp; Authors: '. $authors. '&nbsp; &nbsp;';
    }
    $keywords= conf_get_keywords($event_id);
    if(strlen($keywords)>2){
        $events_html.= ' <br/> &nbsp; &nbsp;  <span class="keywords" style="font-size:14px" > <em> Keywords: &nbsp;'.$keywords.'</em></span>';
    }                                
	$events_html.= '</div></div> ';      //do teaser text instead of contents. allows smaller boxes. 


     //	$events_html.=$event_content;
    //	$events_html.=$share_buttons;	
	$events_html.='</div>';
    $ev_day=tribe_get_start_date($event_id, false, 'd' );
    $ev_month=tribe_get_start_date($event_id, false, 'M' );

    $event_schedule= '<span class="ev-weekday">'.substr(tribe_get_start_date($event_id, false, 'l' ),0,4).'</span>
                                      <span class="ev-mo">'.$ev_month.'</span><span class="ev-day">'.$ev_day.'</span>  &nbsp;<br/>
                                      <span class="ev-time">'.$ev_time.'</span><br/>';
    
	$events_html .='<div class="tb-list-right-side">
				<div class="tb-list-date">'.$event_schedule
    //                    '<a style="font-size:12px;text-decoration:underline" href="' . \Tribe__Events__Main::instance()->esc_gcal_url( tribe_get_gcal_link() ) . '" title="' . esc_attr__( 'Add to Google Calendar', 'the-events-csyalendar' ) . '" class="tribe-events-gcal tribe-events-button"> '
    //                                                                                 . esc_html__( 'Add to Gcal &nbsp;   ', 'the-events-calendar' ) . '</a> &nbsp; &nbsp; &nbsp; &nbsp;  <a style="font-size:12px;text-decoration:underline" href="'
    //                                                                                 . esc_url( tribe_get_single_ical_link() ) . '" title="' . esc_attr__( 'Download .ics file', 'the-events-calendar' ) . '" class="tribe-events-ical tribe-events-button" > ' . esc_html__( 'Add to iCal', 'the-events-calendar' ) . '</a>'.
                .get_favorites_button($event_id).'</div>
				</div>
				</div>
				</div><!-- right-wrapper close -->
				</div><!-- event-loop-end -->';
}


/*** Default List Style 1 */
else{
	$events_html.='<div id="event-'.esc_attr($event_id).'" class="ect-list-post style-1 '.esc_attr($event_type).'">';

	$bg_styles="background-image:url('$ev_post_img');background-size:cover;";
	$events_html.='<div class="tb-list-post-left ">
				<div class="ect-list-img" style="'.$bg_styles.'">';
	$events_html.='<a href="'.esc_url( tribe_get_event_link($event_id)).'" alt="'.esc_attr(get_the_title($event_id)).'" rel="bookmark">';
	$events_html .='<div class="ect-list-date">'.$event_schedule.'</div></a>';
	$events_html.='</div></div><!-- left-post close -->';
	$events_html.='<div class="ect-list-post-right">
				<div class="ect-list-post-right-table">';

			
	if (tribe_has_venue($event_id)) {
		$events_html.='<div class="ect-list-description">';
	}else{
		$events_html.='<div class="ect-list-description" style="width:100%;">';
	}
	$events_html.='<h2 class="ect-list-title">'.$event_title.'</h2>';
	$events_html.=$event_content;
	$events_html.=$event_cost;
	$events_html.= $share_buttons;
	$events_html.='</div>';
	if (tribe_has_venue($event_id)) {
		
		$events_html.=$venue_details_html;
	}else{
		$events_html.='';
	}
	
	$events_html.='</div></div><!-- right-wrapper close -->';
	$events_html.='</div><!-- event-loop-end -->';
}
