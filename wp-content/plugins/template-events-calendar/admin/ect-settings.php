<?php 
/*--- Admin Side Template Settings Panel ---*/
function ect_Options() {

	if(is_admin() && !is_customize_preview() && isset($_GET['page'])){	
		$page_name = filter_var($_GET['page'], FILTER_SANITIZE_STRING);
		if(strpos($page_name, 'tribe_events-events-template-settings') === false){
			return;
		}	
	}

	/*--- Initialize Titan Framework & Options Here ---*/
	$titan = TitanFramework::getInstance('ect' );		
	$panel = $titan->createAdminPanel( array(
		'name' => 'Events Template Settings',
		'title'=>'Shortcode & Templates Addon - Settings',
		'desc'=>'<span class="ectadmin-description"><img src="'.ECT_PLUGIN_URL.'assets/css/ect-icon.png"><strong>The Events Calendar Shortcode & Templates</strong> plugin extends the design and shortcode limitations of <strong>The Events Calendar (by MODERN TRIBE)</strong>. <a href="https://wordpress.org/support/plugin/template-events-calendar/reviews/#new-post" target="_blank">Submit Review! ★★★★★</a> | <a href="https://1.envato.market/a4avW" target="_blank">Check Pro Version</a></span>',
		'parent' => 'edit.php?post_type=tribe_events',
		'position'=>'200'
	) );
	
	/*--- Create Setting Panel TABS ---*/
	$stylingTab= $panel->createTab( array(
		'name' => 'Style Settings'
	) );
	$extraTab= $panel->createTab( array(
		'name' => 'Extra Settings'
	) );
	$buynowTab= $panel->createTab( array(
		'name' => 'Buy PRO'
	) );

	$stylingTab->createOption( array(
		'type' => 'save'		
	) );
	$extraTab->createOption( array(
		'type' => 'save'
	) );

	/*--- Style Tab Options ---*/
	$stylingTab->createOption( array(
		'name' => 'Style Settings',
		'type' => 'heading'
	) );

	/*--- Main Skin Color - CSS ---*/
	$stylingTab->createOption( array(
		'name' => 'Main Skin Color',
		'id' => 'main_skin_color',
		'type' => 'color',
		'desc' => 'It is a main color scheme for all designs',
		'default' => '#dbf5ff',
		'css'=>'
		#ect-events-list-content .style-1 .ect-list-post-right .ect-list-venue,
		#ect-events-list-content .style-2 .modern-list-right-side,
		#ect-events-list-content .style-3 .ect-list-date,
		#ect-events-list-content .style-3 .style-3-readmore a:hover {
			background: value;
		}
		#ect-events-list-content .ect-list-img {
			background-color: lighten( $main_skin_color, 3% );
		}		
		#ect-events-list-content .style-1 .ect-list-post-left .ect-list-date {
			background: rgba( $main_skin_color, .96 );
			box-shadow : inset 2px 0px 14px -2px darken( $main_skin_color, 5% );
		}		
		#ect-events-list-content .style-1 .ect-list-post-right .ect-list-venue,
		#ect-events-list-content .style-2 .modern-list-right-side,
		#ect-events-list-content .style-3 .ect-list-date,
		#ect-events-list-content .style-3 .style-3-readmore a:hover {
			box-shadow : inset 0px 0px 50px -5px darken( $main_skin_color, 7% );
		}


		#event-timeline-wrapper .ect-timeline-year {
			background: darken( $main_skin_color, 10% );
			background: radial-gradient(circle farthest-side, darken( $main_skin_color, 0% ), darken( $main_skin_color, 10% ));
		}
		#event-timeline-wrapper .ect-timeline-post .timeline-dots {
			background: darken( $main_skin_color, 10% );
		}
		#event-timeline-wrapper .ect-timeline-post.style-1.even .timeline-meta {
			background: value;
			background-image:linear-gradient(
			to right,
			darken( $main_skin_color, 8% ),
			lighten( $main_skin_color, 2% ),
			);
		}
		#event-timeline-wrapper .ect-timeline-post.style-1.odd .timeline-meta {
			background: value;
			background-image:linear-gradient(
			to left,
			darken( $main_skin_color, 8% ),
			lighten( $main_skin_color, 2% ),
			);
		}
		#event-timeline-wrapper .ect-timeline-post.even .timeline-meta:before {
			border-left-color: lighten( $main_skin_color, 2% );
		}
		#event-timeline-wrapper .ect-timeline-post.odd .timeline-meta:before {
			border-right-color: lighten( $main_skin_color, 2% );
		}
		

		@media (max-width: 700px) {
			#event-timeline-wrapper .ect-timeline-post.style-1 .timeline-meta:before {
				border-right-color: lighten( $main_skin_color, 2% ) !important;
			}
			#event-timeline-wrapper .ect-timeline-post.style-1 .timeline-meta {
				background-image:linear-gradient(
				to left,
				darken( $main_skin_color, 8% ),
				lighten( $main_skin_color, 2% ),
				) !important;
			}
		}
		'
	) );

	/*--- Featured Event Color - CSS ---*/
	$stylingTab->createOption( array(
		'name' => 'Featured Event Skin Color',
		'id' => 'featured_event_skin_color',
		'type' => 'color',
		'desc' => 'This skin color applies on featured events',
		'default' => '#f19e59',
		'css'=>'
		#ect-events-list-content .ect-featured-event.style-1 .ect-list-post-right .ect-list-venue,
		#ect-events-list-content .ect-featured-event.style-2 .modern-list-right-side,
		#ect-events-list-content .ect-featured-event.style-3 .ect-list-date,
		#ect-events-list-content .ect-featured-event.style-3 .style-3-readmore a:hover {
			background: value;
		}
		#ect-events-list-content .ect-featured-event h2.ect-list-title,
		#ect-events-list-content .ect-featured-event h2.ect-list-title a.ect-event-url,
		#ect-events-list-content .ect-featured-event .ect-list-description .ect-event-content a,
		#ect-events-list-content .ect-featured-event a.tribe-events-read-more,
		#ect-events-list-content .ect-featured-event .ect-rate-area,
		#ect-events-list-content .ect-featured-event.style-1 .ect-rate-area,
		#ect-events-list-content .ect-featured-event.style-2 .ect-list-venue .ect-icon,
		#ect-events-list-content .ect-featured-event.style-2 .ect-list-venue .ect-venue-details,
		#ect-events-list-content .ect-featured-event.style-2 .ect-list-venue .ect-venue-details .ect-google a,
		#ect-events-list-content .ect-featured-event.style-3 .ev-smalltime,
		#ect-events-list-content .ect-featured-event.style-3 .ect-list-venue .ect-icon,
		#ect-events-list-content .ect-featured-event.style-3 .ect-list-venue .ect-venue-details,
		#ect-events-list-content .ect-featured-event.style-3 .ect-list-venue .ect-venue-details .ect-google a {
			color: value;
		}
		#ect-events-list-content .style-1.ect-featured-event .ect-list-post-left .ect-list-date {
			background: rgba( $featured_event_skin_color, .85 );
			box-shadow : inset 2px 0px 14px -2px darken( $featured_event_skin_color, 15% );
		}
		#ect-events-list-content .ect-featured-event .ect-list-img {
			background-color: lighten( $featured_event_skin_color, 3% );
		}		
		#ect-events-list-content .ect-featured-event.style-1 .ect-list-post-right .ect-list-venue,
		#ect-events-list-content .ect-featured-event.style-2 .modern-list-right-side,
		#ect-events-list-content .ect-featured-event.style-3 .ect-list-date,
		#ect-events-list-content .ect-featured-event.style-3 .style-3-readmore a:hover, {
			box-shadow : inset -2px 0px 14px -2px darken( $featured_event_skin_color, 7% );
		}


		#event-timeline-wrapper .ect-timeline-post.ect-featured-event .timeline-dots {
			background: value;
		}
		#event-timeline-wrapper .ect-timeline-post.ect-featured-event.style-1.even .timeline-meta {
			background: value;
			background-image:linear-gradient(
			to right,
			darken( $featured_event_skin_color, 8% ),
			lighten( $featured_event_skin_color, 3% ),
			);
		}
		#event-timeline-wrapper .ect-timeline-post.ect-featured-event.style-1.odd .timeline-meta {
			background: value;
			background-image:linear-gradient(
			to left,
			darken( $featured_event_skin_color, 8% ),
			lighten( $featured_event_skin_color, 3% ),
			);
		}
		#event-timeline-wrapper .ect-timeline-post.ect-featured-event.even .timeline-meta:before {
			border-left-color: lighten( $featured_event_skin_color, 3% );
		}
		#event-timeline-wrapper .ect-timeline-post.ect-featured-event.odd .timeline-meta:before {
			border-right-color: lighten( $featured_event_skin_color, 3% );
		}
		#event-timeline-wrapper .ect-timeline-post.ect-featured-event.style-2 .timeline-content,
		#event-timeline-wrapper .ect-timeline-post.ect-featured-event.style-3 .timeline-content {
			background: value;
			background-image:linear-gradient(
			to left,
			lighten( $featured_event_skin_color, 3% ),
			darken( $featured_event_skin_color, 8% ),
			);
		}
		#event-timeline-wrapper .ect-timeline-post.ect-featured-event.style-2 .timeline-content:before,
		#event-timeline-wrapper .ect-timeline-post.ect-featured-event.style-3 .timeline-content:before {
			border-right-color: darken( $featured_event_skin_color, 8% );
		}
		#event-timeline-wrapper .ect-timeline-post.ect-featured-event.style-2 .ect-date-area,
		#event-timeline-wrapper .ect-timeline-post.ect-featured-event.style-2 .ect-venue-details,
		#event-timeline-wrapper .ect-timeline-post.ect-featured-event.style-2 .ect-icon,
		#event-timeline-wrapper .ect-timeline-post.ect-featured-event.style-2 .timeline-meta .ev-time .ect-icon,
		#event-timeline-wrapper .ect-timeline-post.ect-featured-event.style-2 span.ect-rate {
			color: value;
		}
		#event-timeline-wrapper .ect-timeline-post.ect-featured-event.style-2 .ect-google a {
			color: darken( $featured_event_skin_color, 10% ); 
		}


		@media (max-width: 700px) {
			#event-timeline-wrapper .ect-timeline-post.ect-featured-event.style-1 .timeline-meta:before {
				border-right-color: lighten( $featured_event_skin_color, 3% ) !Important;
			}
			#event-timeline-wrapper .ect-timeline-post.ect-featured-event.style-1 .timeline-meta {
				background-image:linear-gradient(
				to left,
				darken( $featured_event_skin_color, 8% ),
				lighten( $featured_event_skin_color, 3% ),
				) !important;
			}
		}
		'
	) );

	/*--- Featured Event Alternate / Text Color - CSS ---*/
	$stylingTab->createOption( array(
		'name' => 'Featured Event Font Color',
		'id' => 'featured_event_font_color',
		'type' => 'color',
		'desc' => 'This color applies on some fonts of featured events',
		'default' => '#3a2201',
		'css'=>'
		#ect-events-list-content .ect-featured-event .ect-list-date .ect-date-area,
		#ect-events-list-content .ect-featured-event .ect-list-venue .ect-icon,
		#ect-events-list-content .ect-featured-event .ect-list-venue .ect-venue-details,
		#ect-events-list-content .ect-featured-event .ect-list-venue .ect-venue-details .ect-google a,
		#ect-events-list-content .ect-featured-event.style-3 .style-3-readmore a:hover
		{
			color: value;
		}

	
		#event-timeline-wrapper .ect-timeline-post.ect-featured-event .ect-date-area,
		#event-timeline-wrapper .ect-timeline-post.ect-featured-event .ect-venue-details,
		#event-timeline-wrapper .ect-timeline-post.ect-featured-event .ect-icon,
		#event-timeline-wrapper .ect-timeline-post.ect-featured-event .timeline-meta .ev-time .ect-icon,
		#event-timeline-wrapper .ect-timeline-post.ect-featured-event span.ect-rate,
		#event-timeline-wrapper .ect-timeline-post.ect-featured-event.style-2 .timeline-content p,
		#event-timeline-wrapper .ect-timeline-post.ect-featured-event.style-2 .timeline-content a.ect-events-read-more,
		#event-timeline-wrapper .ect-timeline-post.ect-featured-event.style-2 h2.content-title a.ect-event-url,
		#event-timeline-wrapper .ect-timeline-post.ect-featured-event.style-3 h2.content-title a.ect-event-url,
		#event-timeline-wrapper .ect-timeline-post.ect-featured-event.style-3 .timeline-content a.ect-events-read-more
		{
			color: value;
		}
		#event-timeline-wrapper .ect-timeline-post.ect-featured-event .ect-google a,
		#event-timeline-wrapper .ect-timeline-post.ect-featured-event.style-2 h2.content-title a.ect-event-url:hover,
		#event-timeline-wrapper .ect-timeline-post.ect-featured-event.style-3 h2.content-title a.ect-event-url:hover {
			color: darken( $featured_event_font_color, 10% ); 
		}
		#event-timeline-wrapper .ect-timeline-post.ect-featured-event.style-3 .timeline-content a.ect-events-read-more {
			border-color: darken( $featured_event_font_color, 10% );
		}
		'
	) );

	/*--- Event Background Color - CSS ---*/
	$stylingTab->createOption( array(
		'name' => 'Event Background Color',
		'id' => 'event_desc_bg_color',
		'type' => 'color',
		'desc' => 'This skin color applies on background of event description area.',
		'default' => '#f4fcff',
		'css'=>'
		#ect-events-list-content .ect-list-post-right,
		#ect-events-list-content .ect-clslist-event-info {
			background: value;
		}
		#ect-events-list-content .ect-list-post-right .ect-list-description {
			border-color : darken($event_desc_bg_color, 10%);
			box-shadow : inset 0px 0px 25px -5px darken( $event_desc_bg_color, 10% );
		}
		#ect-events-list-content .ect-clslist-event-info {
			box-shadow: inset 0px 0px 25px -5px darken( $event_desc_bg_color, 10% );
		}
		#ect-events-list-content .style-3-readmore {
			background: darken($event_desc_bg_color, 4%);
			box-shadow: inset 0px 0px 25px -5px darken( $event_desc_bg_color, 10% );
		}

		
		#event-timeline-wrapper .ect-timeline-post .timeline-content {
			background: value;
			border: 1px solid darken($event_desc_bg_color, 5%);
		}
		#event-timeline-wrapper .ect-timeline-post.even .timeline-content:before {
			border-right-color: darken($event_desc_bg_color, 5%);
		}
		#event-timeline-wrapper .ect-timeline-post.odd .timeline-content:before {
			border-left-color: darken($event_desc_bg_color, 5%);
		}
		#event-timeline-wrapper .cool-event-timeline:before {
			background-color: darken($event_desc_bg_color, 5%);
		}
		#event-timeline-wrapper .ect-timeline-year { 
			-webkit-box-shadow: 0 0 0 4px white, 0 0 0 8px darken($event_desc_bg_color, 5%);
			box-shadow: 0 0 0 4px white, 0 0 0 8px darken($event_desc_bg_color, 5%);
		}
		#event-timeline-wrapper:before,
		#event-timeline-wrapper:after {
			background-color: darken($event_desc_bg_color, 5%) !important;
		}
		#event-timeline-wrapper .ect-timeline-post.style-3 .timeline-content {
			background: value;
			background-image:linear-gradient(
			to right,
			darken( $event_desc_bg_color, 5% ),
			lighten( $event_desc_bg_color, 0% ),
			);
		}


		@media (max-width: 860px) {
			#event-timeline-wrapper .ect-timeline-post .timeline-meta:before {
				border-right-color: darken($event_desc_bg_color, 5%) !important;
			}

			#ect-events-list-content .ect-list-post-right .ect-list-description {
				border-bottom : 1px solid darken($event_desc_bg_color, 10%);
			}
			#ect-events-list-content .style-3-readmore {
				background: darken($event_desc_bg_color, 4%);
			}
		}			
		'
	) );


	/*--- Event Title Styles - CSS ---*/
	$stylingTab->createOption( array(
		'name' => 'Event Title Styles',
		'id' => 'ect_title_styles',
		'type' => 'font',
		'desc' => 'Select a style',
		'show_letter_spacing' => false,
		'show_text_transform' => false,
		'show_font_variant' => false,
		'show_text_shadow' => false,
		'default' => array(
		'color' => '#00445e',
		'font-family' => 'Monda',
		'font-size' => '18px',
		'line-height' => '1.5em',
		'font-weight' => 'bold',
		),
		'css'=>'
		#ect-events-list-content h2.ect-list-title,
		#ect-events-list-content h2.ect-list-title a.ect-event-url,
		.ect-classic-list a.tribe-events-read-more,
		.ect-clslist-event-info .ect-clslist-title a.ect-event-url,
		#ect-no-events p {
			value
		}
		#ect-events-list-content .ect-list-description .ect-event-content a {
			color: lighten(value-color, 10%);
		}
		#ect-events-list-content .style-1 .ect-rate-area {
			color:value-color;
		}
		#ect-events-list-content h2.ect-list-title a:hover {
			color: darken(value-color, 10%); 
		}


		#event-timeline-wrapper .ect-timeline-post h2.content-title,
		#event-timeline-wrapper .ect-timeline-post h2.content-title a.ect-event-url {
			value
		}
		#event-timeline-wrapper .ect-timeline-post h2.content-title a.ect-event-url:hover {
			color: darken(value-color, 10%); 
		}
		#event-timeline-wrapper .cool-event-timeline .ect-timeline-post .timeline-content .content-details a,
		#event-timeline-wrapper .ect-timeline-post.style-3 .timeline-content a.ect-events-read-more {
			color: value-color;
		}
		'
	) );

	/*--- Event Description Styles - CSS ---*/
	$stylingTab->createOption( array(
		'name' => 'Events Description Styles',
		'id' => 'ect_desc_styles',
		'type' => 'font',
		'desc' => 'Select Styles',
		'show_letter_spacing' => false,
		'show_text_transform' => false,
		'show_font_variant' => false,
		'show_text_shadow' => false,
		'show_font_style'=>false,
		'default' => array(
		'color' => '#515d64',
		'font-family' => 'Open Sans',
		'font-size' => '15px',
		'line-height' => '1.5em',
		),
		'css'=>'
		#ect-events-list-content .ect-list-post-right .ect-list-description .ect-event-content,
		#ect-events-list-content .ect-list-post-right .ect-list-description .ect-event-content p,
		#ect-events-list-content .style-3 .ev-smalltime {
			value
		}

		#event-timeline-wrapper .ect-timeline-post .timeline-content,
		#event-timeline-wrapper .ect-timeline-post .timeline-content p {
			value
		}
		#event-timeline-wrapper .ect-timeline-post .timeline-content a {
			color:darken(value-color, 10%);
		}
		#event-timeline-wrapper .ect-timeline-post .timeline-content a:hover {
			color:lighten(value-color, 1%);
		}
		'
	) );
	
	/*--- Event Venue Styles - CSS ---*/
	$stylingTab->createOption( array(
		'name' => 'Event Venue Styles',
		'id' => 'ect_desc_venue',
		'type' => 'font',
		'desc' => 'Select a style',
		'show_letter_spacing' => false,
		'show_text_transform' => false,
		'show_font_variant' => false,
		'show_text_shadow' => false,
		'default' => array(
		'color' => '#00445e',
		'font-family' => 'Open Sans',
		'font-size' => '15px',
		'font-style' => 'italic',
		'line-height' => '1.5em',
		),
		'css'=>'
		#ect-events-list-content .ect-list-venue .ect-venue-details,
		#ect-events-list-content .ect-list-venue .ect-venue-details a {
			value
		}
		#ect-events-list-content .ect-list-venue .ect-icon {
			color:value-color;
		}
		#ect-events-list-content .ect-list-venue .ect-venue-details .ect-google a {
			color: darken(value-color, 3%);
		}
		#ect-events-list-content .ect-rate-area {
			font-family: value-font-family;
			font-size: value-font-size + value-font-size/3 ;
			color: value-color;
		}


		#event-timeline-wrapper .ect-venue-details {
			value
		}	
		#event-timeline-wrapper .ect-rate-area .ect-rate {
			font-size: value-font-size + value-font-size/3 ;
			font-family: value-font-family;
		}
		#event-timeline-wrapper .timeline-meta .ect-icon,
		#event-timeline-wrapper .ect-rate-area .ect-icon,
		#event-timeline-wrapper .ect-rate-area .ect-rate {
			color: value-color;
		}
		#event-timeline-wrapper .ect-timeline-post .ect-google a {
			color: darken( value-color, 5% );
		}
		'
	) );

	/*--- Event Dates Styles - CSS ---*/
	$stylingTab->createOption( array(
		'name' => 'Event Dates Styles',
		'id' => 'ect_dates_styles',
		'type' => 'font',
		'desc' => 'Select a style',
		'show_letter_spacing' => false,
		'show_text_transform' => false,
		'show_font_variant' => false,
		'show_text_shadow' => false,
		'default' => array(
		'color' => '#00445e',
		'font-family' => 'Monda',
		'font-size' => '36px',
		'font-weight' => 'bold',
		'line-height' => '1em',
		),
		'css'=>'
		#ect-events-list-content .ect-list-date .ect-date-area {
			value
		}
		#ect-events-list-content .style-3-readmore a.tribe-events-read-more {
			font-family: value-font-family;
		}

		#event-timeline-wrapper .ect-timeline-post .ect-date-area {
			value
		}
		#event-timeline-wrapper .ect-timeline-year .year-placeholder span,
		#event-timeline-wrapper .timeline-meta .ev-time .ect-icon {
			font-family: value-font-family;
			color: value-color;
		}
		'
	) );
	
	/*--- Buy PRO ---*/
	$stylingTab->createOption( array(
		'name'=>'Buy PRO',
		'type' => 'custom',
		'custom' => '<a href="https://1.envato.market/a4avW" target="_blank"><img style="max-width:100%;height:auto;" src="'.ECT_PLUGIN_URL.'assets/images/events-calendar-templates-ad.png"/></a>'
	) );

	
	/*--- Custom CSS / Shortcode info tab ---*/
	$extraTab->createOption( array(
		'name' => 'Extra Settings',
		'type' => 'heading',
	) );
	$extraTab->createOption( array(
		'name' => 'Custom CSS',
		'id' => 'custom_css',
		'type' => 'code',
		'desc' => 'Put your custom CSS rules here',
		'lang' => 'css',
	) );
	$extraTab->createOption( array(
		'name' => 'No Event Text (Message to show if no event will available)',
		'id' => 'events_not_found',
		'default'=>'There are no upcoming events at this time',
		'type' => 'text',
		'desc' => ''
		) );
		$extraTab->createOption( array(
			'name' => 'Default Image (select a default image, if no featured image for the event)',
			'id' => 'ect_no_featured_img',
			'type' => 'upload',
    		'desc' => 'Upload your image'
			) );

	$extraTab->createOption( array(
		'name' => 'Shortcodes',
		'type' => 'heading',
	) );
	$extraTab->createOption( array(
		'name'=>'Default Shortcode',
		'type' => 'custom',
		'custom' => '<code>[events-calendar-templates category="all" template="default" style="style-1" date_format="default" start_date="" end_date="" limit="10" order="ASC" hide-venue="no" time="future"]</code>'
	) );
	$extraTab->createOption( array(
		'name'=>'Shortcode Attribute',
		'type' => 'custom',
		'custom' => '<style>.tf-custom table tr th, .tf-custom table tr td{border:1px solid #ddd}</style>
					<table>
					<tr><th>Attribute</th><th>Value</th></tr>
					<tr><td>template</td>
					<td><ul>
					<li><strong>default</strong></li>
					<li><strong>timeline-view</strong></li>
					<li><strong>grid-view</strong> (<a href="https://eventscalendartemplates.com/events-grid-demo/" target="_blank">Premium Template</a>)</li>
					<li><strong>carousel-view</strong> (<a href="https://eventscalendartemplates.com/events-carousel-demo/#top" target="_blank">Premium Template</a>)</li>
					<li><strong>slider-view</strong> (<a href="https://eventscalendartemplates.com/events-slider-demo/" target="_blank">Premium Template</a>)</li>
					<li><strong>accordion-view</strong> (<a href="https://eventscalendartemplates.com/events-accordion-demo/" target="_blank">Premium Template</a>)</li>
					</ul></td></tr>

					<tr><td>style</td>
					<td><ul>
					<li><strong>style-1</strong></li>
					<li><strong>style-2</strong></li>
					<li><strong>style-3</strong></li>
					</ul></td></tr>

					<tr><td>category</td>
					<td><ul>
					<li><strong>all</strong></li>
					<li><strong>custom-slug</strong> (events category slug)</li>
					</ul></td></tr>

					<tr><td>date_format</td>
					<td><ul>
					<li><strong>default</strong> (01 January 2019)</li>
					<li><strong>MD,Y</strong> (Jan 01, 2019)</li>
					<li><strong>MD,Y</strong> (January 01, 2019)</li>
					<li><strong>DM</strong> (01 Jan)</li>
					<li><strong>DML</strong> (01 Jan Monday)</li>
					<li><strong>DF</strong> (01 January)</li>
					<li><strong>MD</strong> (Jan 01)</li>
					<li><strong>FD</strong> (January 01)</li>
					<li><strong>MD,YT</strong> (Jan 01, 2019 8:00am-5:00pm)</li>
					<li><strong>full</strong> (01 January 2019 8:00am-5:00pm)</li>
					</ul></td></tr>

					<tr><td>start_date<br/>end_date</td>
					<td><ul>
					<li><strong>YY-MM-DD</strong> (show events in between a date interval)</li>
					</ul></td></tr>

					<tr><td>limit</td>
					<td><ul>
					<li><strong>10</strong> (number of events to show)</li>
					</ul></td></tr>

					<tr><td>order</td>
					<td><ul>
					<li><strong>ASC</strong></li>
					<li><strong>DESC</strong></li>
					</ul></td></tr>

					<tr><td>hide_venue</td>
					<td><ul>
					<li><strong>yes</strong></li>
					<li><strong>no</strong></li>
					</ul></td></tr>

					<tr><td>time</td>
					<td><ul>
					<li><strong>future</strong> (show future events)</li>
					<li><strong>past</strong> (show past events)</li>
					</ul></td></tr>

					</table>'
	) );

	/*--- Buy PRO tab ---*/
	$buynowTab->createOption( array(
		'name' => 'Buy PRO',
		'type' => 'heading',
	) );
	$buynowTab->createOption( array(
		'name'=>'Special Events Listing Templates',
		'type' => 'custom',
		'custom' => '<a href="https://1.envato.market/a4avW" target="_blank"><img style="max-width:100%;height:auto;" src="'.ECT_PLUGIN_URL.'assets/images/ect-buy-pro.png"/></a>'
	) );
	$stylingTab->createOption( array(
		'type' => 'save'
	) );			
	$extraTab->createOption( array(
		'type' => 'save'
	) );	

}
