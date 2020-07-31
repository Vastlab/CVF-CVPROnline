<?php
$timeline_style=$attribute['style'];
if($template=="timeline") {
	$timeline_style='style-1';
}
else if($template=="classic-timeline") {
	$timeline_style='style-2';
}

$ev_post_img=ect_get_event_image($event_id,$size='large');

if ($i % 2 == 0) {
	$even_odd = "even";
}
else {
	$even_odd = "odd";
}

if($timeline_style=="style-1")
{
	if($events_date_header !==''){
		$events_html .= '<div class="ect-timeline-year">
						<div class="year-placeholder">' . $events_date_header . '</div>
						</div>';
	}		
									
	$events_html .= '<div id="post-'.esc_attr($event_id) .'" class="ect-timeline-post '.esc_attr($even_odd).' '.esc_attr($event_type).' '.esc_attr($timeline_style).'">';
	$events_html .= '<div class="timeline-meta">';
	$events_html .= $event_schedule;
	$events_html .= $venue_details_html;
	$events_html.= $share_buttons;
	$events_html .= $event_cost ;
	$events_html .= '</div>';	
	$events_html .= '<div class="timeline-dots"></div>';
	$events_html .= '<div class="timeline-content ' .esc_attr($even_odd).'">';
	$events_html .= '<h2 class="content-title">' . $event_title .'</h2>';
	if($ev_post_img) {
		$events_html .= '<a class="timeline-ev-img" href="'.esc_url( tribe_get_event_link($event_id)).'"><img src= "'.$ev_post_img.'"/></a>';
	}
	$events_html .= $event_content;
	$events_html .= '</div>';
	$events_html .= '</div>';
	$i++;
}
elseif($timeline_style=="style-2")
{
	if($events_date_header !==''){
		$events_html .= '<div class="ect-timeline-year">
						<div class="year-placeholder">' . $events_date_header . '</div>
						</div>';
	}		
									
	$events_html .= '<div id="post-'.esc_attr($event_id).'" class="ect-timeline-post even '.esc_attr($event_type).' '.esc_attr($timeline_style).'">';
	$events_html .= '<div class="timeline-meta">';
	$events_html .= $event_schedule;
	$events_html .= $venue_details_html;
	$events_html.= $share_buttons;
	$events_html .= $event_cost ;
	$events_html .= '</div>';	
	$events_html .= '<div class="timeline-dots"></div>';
	$events_html .= '<div class="timeline-content even">';
	if($ev_post_img) {
		$events_html .= '<a class="timeline-ev-img" href="'.esc_url( tribe_get_event_link($event_id)).'"><img src= "'.esc_url($ev_post_img).'"/></a>';
	}
	$events_html .= '<h2 class="content-title">' . $event_title .'</h2>';
	//$events_html .= '<img src= "'.$ev_post_img.'"/>';
	$events_html .= $event_content;
	$events_html .= '</div>';
	$events_html .= '</div>';
}
else {
	if($events_date_header !==''){
		$events_html .= '<div class="ect-timeline-year">
						<div class="year-placeholder">' . $events_date_header . '</div>
						</div>';
	}		
									
	$events_html .= '<div id="post-'.esc_attr($event_id).'" class="ect-timeline-post even '.esc_attr($event_type).' '.esc_attr($timeline_style).'">';	
	$events_html .= '<div class="timeline-dots"></div>';
	$events_html .= '<div class="timeline-content even">';
	$events_html .= '<h2 class="content-title">' . $event_title .'</h2>';
	//$events_html .= '<img src= "'.$ev_post_img.'"/>';
	$events_html .= '<div class="timeline-meta">';
	$events_html .= $event_schedule;
	$events_html .= $venue_details_html;
	$events_html.= $share_buttons;
	$events_html .= $event_cost ;
	$events_html .= '<a href="'.esc_url( tribe_get_event_link($event_id) ).'" class="ect-events-read-more" rel="bookmark">'.esc_html__( 'Find out more', 'the-events-calendar' ).' &raquo;</a>';
	$events_html .= '</div>';
	$events_html .= '</div>';
	$events_html .= '</div>';
}
	
