<?php
$list_style=$attribute['style'];
if($template=="modern-list") {
	$list_style='style-2';
}
else if($template=="classic-list") {
	$list_style='style-3';
}

$ev_post_img=ect_get_event_image($event_id,$size='large');

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

	$events_html.=<div class="ect-list-post-left">
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
				<div class="ect-list-date">'.$event_schedule.'</div>
				</div>
				</div>
				</div><!-- right-wrapper close -->
				</div><!-- event-loop-end -->';
}


/*** Default List Style 1 */
else{
	$events_html.='<div id="event-'.esc_attr($event_id).'" class="ect-list-post style-1 '.esc_attr($event_type).'">';

	$bg_styles="background-image:url('$ev_post_img');background-size:cover;";
	$events_html.='<div class="ect-list-post-left ">
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
