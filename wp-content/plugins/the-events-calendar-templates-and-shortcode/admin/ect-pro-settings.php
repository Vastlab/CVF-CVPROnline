<?php
	/*** ect Option panel */
		function ect_Options() {
			$ect_admin_url = admin_url( 'edit.php?page=tribe-common&tab=display&post_type=tribe_events');
	 		if(is_admin() && !is_customize_preview() && isset($_GET['page'])){
        		if(strpos($_GET['page'], 'gf_new_form') !== false){
        			return;
      			}
			}
			// Initialize Titan & options here
			$titan = TitanFramework::getInstance('ect' );	

			
			$panel = $titan->createAdminPanel( array(
			'name' => 'Shortcode & Template Settings',
			'title'=>'Shortcode & Templates Addon - Settings',
			'desc'=>'<img style="margin-right: 5px;display: inline-block;width: 32px;vertical-align: middle;" src="'.ECT_PRO_PLUGIN_URL.'assets/css/ect-icon.png"> <span style="vertical-align: middle;width: calc(100% - 50px);display: inline-block;">Extend the design limitations of "<a href="http://wordpress.org/plugins/the-events-calendar/">The Events Calendar (by Modern Tribe)</a>" by using this unofficial design addon - <b>The Events Calendar Shortcode &amp; Templates Pro</b>.<br><i style="color:red;font-weight:bold;">After installation or plugin update, please save/update your template settings once for best design results.</i></span>',
			'parent' => 'edit.php?post_type=tribe_events',
			'position'=>'200',
			) );

			$events_panel = $titan->createMetaBox( array(
			'name' => 'Event color',
			'post_type' => 'taxonomy',
			) );

			$events_panel->createOption( array(
				'name' => 'Select event background color',
				'id' => 'ect_event_bgcolor',
				'type' => 'color',
				'context'=>'side',
				'desc' => 'This settings will change the background color of the calendar in calendar view.',
				'default' => '',
				)
			); 
			$events_panel->createOption( array(
				'name' => 'Select event text color',
				'id' => 'ect_event_text_color',
				'type' => 'color',
				'context' => 'side',
				'desc' => 'This color will change the text color of calendar in calendar view.',
				'default' => '',
				)
			);


			$stylingTab= $panel->createTab( array(
				'name' => 'Style Settings'
				) );
			$extraTab= $panel->createTab( array(
				'name' => 'Shortcodes and Extra Settings'
				) );
			$stylingTab->createOption( array(
				'type' => 'save'
				) );
			$extraTab->createOption( array(
				'type' => 'save'
				) );
			$stylingTab->createOption( array(
				'name' => 'Style Settings',
				'type' => 'heading'
				) );

			$stylingTab->createOption( array(
				'name' => 'Select calendar background color',
				'id' => 'ect_calendar_bgcolor',
				'type' => 'color',
				'context'=>'side',
				'desc' => 'This settings will change the background color of the calendar in calendar view.',
				'default' => '',
				)
			); 
			$stylingTab->createOption( array(
				'name' => 'Select calendar text color',
				'id' => 'ect_calendar_text_color',
				'type' => 'color',
				'context' => 'side',
				'desc' => 'This color will change the text color of calendar in calendar view.',
				'default' => '',
				)
			);
			/*--- Main Skin Color - CSS ---*/
			$stylingTab->createOption( array(
				'name' => 'Main Skin Color',
				'id' => 'main_skin_color',
				'type' => 'color',
				'desc' => 'It is a main color scheme for all designs',
				'default' => '#dbf5ff',
				'css'=>'
				.ect-list-post.style-1 .ect-list-post-right .ect-list-venue,
				.ect-list-post.style-2 .modern-list-right-side,
				.ect-list-post.style-3 .ect-list-date,
				.ect-list-post.style-3 .ect-clslist-event-details a:hover,
				div[id*="event-"] .ect-event-category ul.tribe_events_cat li a,

				#ect-grid-wrapper .style-2 .ect-grid-date,

				#ect-slider-wrapper .style-2 .ect-slider-date,
				
				#ect-accordion-wrapper .ect-accordion-event.style-3.ect-simple-event.active-event,
				#ect-accordion-wrapper .ect-accordion-event.style-2 .ect-accordion-date,
				#ect-accordion-wrapper .ect-accordn-slick-prev,
				#ect-accordion-wrapper .ect-accordn-slick-next,

				.ect-share-wrapper i.ect-icon-share:before
				{
					background: value;
				}

				.ect-load-more a.ect-load-more-btn {
					background-color: value;
					background-image: radial-gradient(lighten( $main_skin_color, 5% ), lighten( $main_skin_color, 0% ));
					box-shadow: 0 0 8px -5px $main_skin_color;
				}


				.ect-list-post .ect-list-img {
					background-color: lighten( $main_skin_color, 3% );
				}
				div[id*="event-"] .ect-event-category ul.tribe_events_cat li a {
					border-color: darken( $main_skin_color, 10% );
				}
				.ect-list-post.style-1 .ect-list-post-left .ect-list-date {
					background: rgba( $main_skin_color, .96 );
					box-shadow : inset 2px 0px 14px -2px darken( $main_skin_color, 5% );
				}		
				.ect-list-post.style-1 .ect-list-post-right .ect-list-venue,
				.ect-list-post.style-2 .modern-list-right-side,
				.ect-list-post.style-3 .ect-list-date,
				.ect-list-post.style-3 .ect-clslist-event-details a:hover {
					box-shadow : inset 0px 0px 50px -5px darken( $main_skin_color, 7% );
				}


				#ect-grid-wrapper .style-1 .ect-grid-date {
					background: rgba( $main_skin_color, .95 );
				}
				#ect-grid-wrapper .style-2 .ect-grid-date {
					box-shadow : inset 0px 0px 12px -2px darken( $main_skin_color, 7% );
				}
				#ect-grid-wrapper .style-3 .ect-grid-event-area {
					border-color: value;
				}


				#ect-carousel-wrapper .style-1 .ect-carousel-date,
				#ect-carousel-wrapper .style-2 .ect-carousel-date {
					background: rgba( $main_skin_color, .95 );
					box-shadow : inset 0px 0px 25px -5px darken( $main_skin_color, 7% );
				}
				#ect-carousel-wrapper .style-3 .ect-carousel-event-area {
					border-color: value;
				}
				#ect-carousel-wrapper .style-1 .ect-carousel-date:after {
					border-color: transparent transparent darken( $main_skin_color, 3% );
				}


				#ect-slider-wrapper .style-2 .ect-slider-date {
					box-shadow : inset 0px 0px 12px -2px darken( $main_skin_color, 7% );
				}
				#ect-slider-wrapper .ect-slider-right.ect-slider-image,
				#ect-slider-wrapper .style-3 .ect-slider-left {
					border-color: value;
				}
				#ect-slider-wrapper .style-2 .ect-slider-title:before {
					box-shadow: 0px 2px 30px 1px darken($main_skin_color, 7% );
				}


				#ect-accordion-wrapper .ect-accordion-header:after,
				#ect-accordion-wrapper .ect-share-wrapper .ect-social-share-list a,
				#ect-accordion-wrapper.ect-accordion-view.style-4 span.month-year-box {
					color: value;
				}
				#ect-accordion-wrapper .ect-share-wrapper i.ect-icon-share:before {
					background: value;
				}
				#ect-accordion-wrapper .ect-simple-event.style-3.active-event {
					box-shadow: inset 0px 0px 25px -5px darken( $main_skin_color, 7% );
					border-color: darken( $main_skin_color, 7% );
				}
				#ect-accordion-wrapper .ect-simple-event.style-3.active-event .ect-accordion-header:after {
					color: darken( $main_skin_color, 12% );
				}
				#ect-accordion-wrapper .ect-simple-event.style-3.active-event .ect-accordion-date span.ev-yr,
				#ect-accordion-wrapper .ect-simple-event.style-3.active-event .ect-accordion-date span.ev-time,
				#ect-accordion-wrapper .ect-simple-event.style-3.active-event .ect-accordion-date span.ev-weekday,
				#ect-accordion-wrapper .style-2 .ect-accordion-date span.ev-yr,
				#ect-accordion-wrapper .style-2 .ect-accordion-date span.ev-time,
				#ect-accordion-wrapper .style-2 .ect-accordion-date span.ev-weekday {
					background: darken( $main_skin_color, 12% );
				}
				.ect-load-more:before,
				.ect-load-more:after {
					background: lighten( $main_skin_color, 20% );
				}


				ul.ect-categories li {
					border-color: darken( $main_skin_color, 9%  );
					color: darken( $main_skin_color, 9%  );
				}
				ul.ect-categories li.ect-active, ul.ect-categories li:hover,
				.ect-masonay-load-more a.ect-load-more-btn {
					background-color:value;
					border-color: darken( $main_skin_color, 9%  );
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

				.ect-rate-area span.ect-ticket-info a {
					background: darken( $main_skin_color, 8% );
					background-image: linear-gradient(to right, darken( $main_skin_color, 8% ) 0%, darken( $main_skin_color, 1% ) 51%, darken( $main_skin_color, 8% ) 100%);
					box-shadow: 3px 3px 10px -6px darken( $main_skin_color, 12% );
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

				
			/*--- Main Skin Alternate Color - CSS ---*/
			$stylingTab->createOption( array(
				'name' => 'Main Skin Alternate Color / Font Color',
				'id' => 'main_skin_alternate_color',
				'type' => 'color',
				'desc' => 'Text/Font color where background color is Main Skin.',
				'default' => '',
				'css'=>'
				.ect-list-post.ect-simple-event .ect-list-date .ect-date-area,
				.ect-list-post.ect-simple-event .ect-list-date span.ect-custom-schedule,
				.ect-list-post.ect-simple-event .ect-list-post-left .ect-list-date .ect-date-area,
				.ect-list-post.ect-simple-event .ect-list-post-left .ect-list-date span.ect-custom-schedule,
				.ect-list-post.style-1.ect-simple-event .ect-list-venue .ect-venue-details,
				.ect-list-post.style-1.ect-simple-event .ect-list-venue .ect-icon,
				.ect-list-post.style-1.ect-simple-event .ect-list-venue .ect-google a,
				.ect-list-post.style-3.ect-simple-event .ect-clslist-event-details a:hover,
				.ect-load-more a.ect-load-more-btn,
				div[id*="event-"] .ect-event-category ul.tribe_events_cat li a,

				#ect-grid-wrapper .style-1 .ect-grid-date,
				#ect-grid-wrapper .style-2 .ect-grid-date,

				ul.ect-categories li.ect-active,
				ul.ect-categories li:hover,
				.ect-masonay-load-more a.ect-load-more-btn,

				#ect-carousel-wrapper .style-1 .ect-carousel-date,
				#ect-carousel-wrapper .style-2 .ect-carousel-date,

				#ect-slider-wrapper .style-2 .ect-slider-date,

				#ect-accordion-wrapper .ect-share-wrapper i.ect-icon-share:before,
				#ect-accordion-wrapper .ect-simple-event.style-3.active-event,
				#ect-accordion-wrapper .ect-simple-event.style-3.active-event .ect-accordion-content,
				#ect-accordion-wrapper .ect-simple-event.style-3.active-event .ect-accordion-content p,
				#ect-accordion-wrapper .ect-simple-event.style-3.active-event .ect-accordion-date,
				#ect-accordion-wrapper .ect-simple-event.style-3.active-event .ect-accordion-venue,
				#ect-accordion-wrapper .ect-simple-event.style-3.active-event h3.ect-accordion-title,
				#ect-accordion-wrapper .ect-simple-event.style-3.active-event .ect-accordion-venue .ect-google a,
				#ect-accordion-wrapper .ect-simple-event.style-3.active-event .ect-accordion-content a.ect-events-read-more,
				#ect-accordion-wrapper .ect-simple-event.style-3.active-event:before,
				#ect-accordion-wrapper .ect-simple-event.style-3.active-event .ect-accordion-cost.no-image,
				#ect-accordion-wrapper .ect-simple-event.style-3.active-event .ect-accordion-date-full.no-image,
				#ect-accordion-wrapper .ect-accordion-event.style-2 .ect-accordion-date,
				#ect-accordion-wrapper .ect-accordn-slick-prev .ect-icon-left:before,
				#ect-accordion-wrapper .ect-accordn-slick-next .ect-icon-right:before,
				
				#event-timeline-wrapper.style-1 .ect-timeline-post .ect-date-area,
				#event-timeline-wrapper.style-1 .ect-timeline-post span.ect-custom-schedule,
				#event-timeline-wrapper.style-1 .timeline-meta .ev-time .ect-icon,
				#event-timeline-wrapper.style-1 .timeline-meta .ect-icon,
				#event-timeline-wrapper.style-1 .ect-venue-details,
				#event-timeline-wrapper.style-1 .ect-rate-area .ect-rate,
				#event-timeline-wrapper.style-1 .ect-timeline-post .ect-google a,
				#event-timeline-wrapper .cool-event-timeline .ect-timeline-year .year-placeholder span,
				
				.ect-rate-area span.ect-ticket-info a,
				
				.ect-share-wrapper i.ect-icon-share:before {
					color: $main_skin_alternate_color;
				}

				.ect-load-more a.ect-load-more-btn:hover,
				.ect-rate-area span.ect-ticket-info a:hover {
					color: lighten( $main_skin_alternate_color, 5% ) !important;
				}

				#ect-accordion-wrapper .ect-share-wrapper .ect-social-share-list {
					background: $main_skin_alternate_color;
					border: 1px solid $main_skin_alternate_color;
				}
				#ect-accordion-wrapper .ect-share-wrapper .ect-social-share-list:before {
					border-top-color: $main_skin_alternate_color;
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
				.ect-list-post.ect-featured-event.style-1 .ect-list-post-right .ect-list-venue,
				.ect-list-post.ect-featured-event.style-2 .modern-list-right-side,
				.ect-list-post.ect-featured-event.style-3 .ect-list-date,
				.ect-list-post.ect-featured-event.style-3 .ect-clslist-event-details a,
				div[id*="event-"].ect-featured-event .ect-event-category ul.tribe_events_cat li a,

				#ect-grid-wrapper .ect-featured-event.style-2 .ect-grid-date,
				
				#ect-accordion-wrapper .ect-accordion-event.style-3.ect-featured-event.active-event,
				#ect-accordion-wrapper .ect-featured-event.style-2 .ect-accordion-date
				{
					background: value;
				}



				#ect-slider-wrapper .ect-featured-event:not(.style-2) .ect-slider-title h4 a,
				
				#ect-accordion-wrapper .ect-featured-event:before{
					color: value;
				}


				.ect-list-post.style-1.ect-featured-event .ect-list-post-left .ect-list-date {
					background: rgba( $featured_event_skin_color, .85 );
					box-shadow : inset 2px 0px 14px -2px darken( $featured_event_skin_color, 15% );
				}
				.ect-list-post.ect-featured-event .ect-list-img {
					background-color: lighten( $featured_event_skin_color, 3% );
				}		
				.ect-list-post.ect-featured-event.style-1 .ect-list-post-right .ect-list-venue,
				.ect-list-post.ect-featured-event.style-2 .modern-list-right-side,
				.ect-list-post.ect-featured-event.style-3 .ect-list-date,
				.ect-list-post.ect-featured-event.style-3 .ect-clslist-event-details a, {
					box-shadow : inset -2px 0px 14px -2px darken( $featured_event_skin_color, 7% );
				}
				div[id*="event-"].ect-featured-event .ect-event-category ul.tribe_events_cat li a {
					border-color: darken( $featured_event_skin_color, 10% );
				}
				

				#ect-grid-wrapper .ect-featured-event.style-1 .ect-grid-date {
					background: rgba( $featured_event_skin_color, .95 );
				}
				#ect-grid-wrapper .ect-featured-event.style-3 .ect-grid-event-area {
					border-color: darken( $featured_event_skin_color, 7% );
					background: value;
					box-shadow : inset 0px 0px 12px 2px darken( $featured_event_skin_color, 3% );
				}
				#ect-grid-wrapper .ect-featured-event.style-1 .ect-grid-date,
				#ect-grid-wrapper .ect-featured-event.style-2 .ect-grid-date {
					box-shadow : inset 0px 0px 25px -5px darken( $featured_event_skin_color, 7% );
				}


				#ect-carousel-wrapper .ect-featured-event.style-1 .ect-carousel-date,
				#ect-carousel-wrapper .ect-featured-event.style-2 .ect-carousel-date {
					background: rgba( $featured_event_skin_color, .95 );
				}
				#ect-carousel-wrapper .ect-featured-event.style-3 .ect-carousel-event-area {
					border-color: darken( $featured_event_skin_color, 7% );
					background: value;
					box-shadow : inset 0px 0px 25px -5px darken( $featured_event_skin_color, 7% );
				}
				#ect-carousel-wrapper .ect-featured-event.style-1 .ect-carousel-date:after {
					border-color: transparent transparent darken( $featured_event_skin_color, 7% );
				}
				#ect-carousel-wrapper .ect-featured-event.style-1 .ect-carousel-date,
				#ect-carousel-wrapper .ect-featured-event.style-2 .ect-carousel-date {
					box-shadow : inset 0px 0px 25px -5px darken( $featured_event_skin_color, 7% );
				}


				#ect-slider-wrapper .ect-featured-event.style-2 .ect-slider-date {
					box-shadow : inset 0px 0px 25px -5px darken( $featured_event_skin_color, 7% );
					background: value;
				}
				#ect-slider-wrapper .ect-featured-event .ect-slider-right.ect-slider-image,
				#ect-slider-wrapper .ect-featured-event.style-3 .ect-slider-left,
				#ect-slider-wrapper .ect-featured-event.style-1 .ect-slider-event-area {
					border-color: darken( $featured_event_skin_color, 7% );
					background: rgba( $featured_event_skin_color, .94 );
					box-shadow : inset 0px 0px 25px -5px darken( $featured_event_skin_color, 7% );
				}
				#ect-slider-wrapper .ect-featured-event.style-2 .ect-slider-title:before {
					box-shadow: 0px 3px 20px 1px darken( $featured_event_skin_color, 7% );
				}


				#ect-accordion-wrapper .ect-featured-event.style-3.active-event {
					box-shadow: inset 0px 0px 25px -5px darken( $featured_event_skin_color, 7% );
					border-color: darken( $featured_event_skin_color, 7% );
				}
				#ect-accordion-wrapper .ect-featured-event.style-3.active-event .ect-accordion-header:after,
				#ect-accordion-wrapper .ect-featured-event.style-3.active-event .ect-share-wrapper .ect-social-share-list a {
					color: darken( $featured_event_skin_color, 12% );
				}
				#ect-accordion-wrapper .ect-featured-event.style-3.active-event .ect-accordion-date span.ev-yr,
				#ect-accordion-wrapper .ect-featured-event.style-3.active-event .ect-accordion-date span.ev-time,
				#ect-accordion-wrapper .ect-featured-event.style-3.active-event .ect-accordion-date span.ev-weekday,
				#ect-accordion-wrapper .ect-featured-event.style-3.active-event .ect-share-wrapper i.ect-icon-share:before,
				#ect-accordion-wrapper .ect-featured-event.style-2 .ect-accordion-date span.ev-yr,
				#ect-accordion-wrapper .ect-featured-event.style-2 .ect-accordion-date span.ev-time,
				#ect-accordion-wrapper .ect-featured-event.style-2 .ect-accordion-date span.ev-weekday {
					background: darken( $featured_event_skin_color, 12% );
				}
				#ect-accordion-wrapper .ect-accordion-event.style-1.ect-featured-event,
				#ect-accordion-wrapper .ect-accordion-event.style-2.ect-featured-event {
					border-left-color: value;
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


			/*--- Featured Event Font Color - CSS ---*/
			$stylingTab->createOption( array(
				'name' => 'Featured Event Font Color',
				'id' => 'featured_event_font_color',
				'type' => 'color',
				'desc' => 'This color applies on some fonts of featured events',
				'default' => '#3a2201',
				'css'=>'
				#ect-events-list-content .ect-list-post.ect-featured-event .ect-list-date .ect-date-area,
				.ect-list-post.ect-featured-event .ect-list-date span.ect-custom-schedule,
				.ect-list-post.ect-featured-event .ect-list-date span.ect-custom-schedule
				.ect-list-post.ect-featured-event .ect-list-post-left .ect-list-date .ect-date-area,
				.ect-list-post.ect-featured-event .ect-list-post-right .ect-list-venue .ect-icon,
				.ect-list-post.ect-featured-event .ect-list-post-right .ect-list-venue .ect-venue-details,
				.ect-list-post.ect-featured-event .ect-list-post-right .ect-list-venue .ect-google a,
				.ect-list-post.ect-featured-event .ect-modern-time,
				.ect-list-post.ect-featured-event.style-3 .ect-clslist-event-details a,
				div[id*="event-"].ect-featured-event .ect-event-category ul.tribe_events_cat li a,

				#ect-grid-wrapper .ect-featured-event .ect-grid-date,
				#ect-grid-wrapper .ect-featured-event.style-3 .ect-grid-title h4,
				#ect-grid-wrapper .ect-featured-event.style-3 .ect-grid-title h4 a,
				#ect-grid-wrapper .ect-featured-event.style-3 .ect-grid-venue,
				#ect-grid-wrapper .ect-featured-event.style-3 .ect-grid-cost,

				#ect-carousel-wrapper .ect-featured-event .ect-carousel-date,
				#ect-carousel-wrapper .ect-featured-event.style-3 .ect-carousel-title h4,
				#ect-carousel-wrapper .ect-featured-event.style-3 .ect-carousel-title h4 a,
				#ect-carousel-wrapper .ect-featured-event.style-3 .ect-carousel-venue,
				#ect-carousel-wrapper .ect-featured-event.style-3 .ect-carousel-cost,

				#ect-slider-wrapper .ect-featured-event .ect-slider-date,
				#ect-slider-wrapper .ect-featured-event.style-1 .ect-slider-title h4,
				#ect-slider-wrapper .ect-featured-event.style-1 .ect-slider-title h4 a,
				#ect-slider-wrapper .ect-featured-event.style-1 .ect-slider-venue,
				#ect-slider-wrapper .ect-featured-event.style-1 .ect-slider-cost,
				#ect-slider-wrapper .ect-featured-event.style-1 .ect-slider-description .ect-event-content p,
				#ect-slider-wrapper .ect-featured-event.style-3 .ect-slider-title h4,
				#ect-slider-wrapper .ect-featured-event.style-3 .ect-slider-title h4 a,
				#ect-slider-wrapper .ect-featured-event.style-3 .ect-slider-venue,
				#ect-slider-wrapper .ect-featured-event.style-3 .ect-slider-cost,
				
				#ect-accordion-wrapper .ect-featured-event.style-3.active-event,
				#ect-accordion-wrapper .ect-featured-event.style-3.active-event .ect-accordion-content,
				#ect-accordion-wrapper .ect-featured-event.style-3.active-event .ect-accordion-content p,
				#ect-accordion-wrapper .ect-featured-event.style-3.active-event .ect-accordion-date,
				#ect-accordion-wrapper .ect-featured-event.style-3.active-event .ect-accordion-venue,
				#ect-accordion-wrapper .ect-featured-event.style-3.active-event h3.ect-accordion-title,
				#ect-accordion-wrapper .ect-featured-event.style-3.active-event .ect-share-wrapper i.ect-icon-share:before,
				#ect-accordion-wrapper .ect-featured-event.style-3.active-event .ect-accordion-venue .ect-google a,
				#ect-accordion-wrapper .ect-featured-event.style-3.active-event .ect-accordion-content a.ect-events-read-more,
				#ect-accordion-wrapper .ect-featured-event.style-3.active-event:before,
				#ect-accordion-wrapper .ect-featured-event.style-3.active-event .ect-accordion-cost.no-image,
				#ect-accordion-wrapper .ect-featured-event.style-3.active-event .ect-accordion-date-full.no-image,
				#ect-accordion-wrapper .ect-featured-event.style-2 .ect-accordion-date
				{
					color: value;
				}


				#ect-grid-wrapper .ect-featured-event.style-3 .ect-grid-venue a,
				#ect-grid-wrapper .ect-featured-event.style-3 .ect-grid-readmore a,

				#ect-carousel-wrapper .ect-featured-event.style-3 .ect-carousel-venue a,
				#ect-carousel-wrapper .ect-featured-event.style-3 .ect-carousel-readmore a,

				#ect-slider-wrapper .ect-featured-event.style-1 .ect-slider-venue a,
				#ect-slider-wrapper .ect-featured-event.style-1 .ect-slider-readmore a,
				#ect-slider-wrapper .ect-featured-event.style-3 .ect-slider-venue a,
				#ect-slider-wrapper .ect-featured-event.style-3 .ect-slider-readmore a
				{
					color: darken($featured_event_font_color, 5%);
				}


				#ect-accordion-wrapper .ect-featured-event.style-3.active-event .ect-share-wrapper .ect-social-share-list {
					background: value;
					border-color: value;
				}
				#ect-accordion-wrapper .ect-featured-event.style-3.active-event .ect-share-wrapper .ect-social-share-list:before {
					border-top-color: darken($featured_event_font_color, 1%);
				}


				#event-timeline-wrapper .ect-timeline-post.ect-featured-event:not(.style-2) .ect-date-area,
				#event-timeline-wrapper .ect-timeline-post.ect-featured-event:not(.style-2) span.ect-custom-schedule,
				#event-timeline-wrapper .ect-timeline-post.ect-featured-event:not(.style-2) .ect-venue-details,
				#event-timeline-wrapper .ect-timeline-post.ect-featured-event:not(.style-2) .ect-icon,
				#event-timeline-wrapper .ect-timeline-post.ect-featured-event:not(.style-2) .timeline-meta .ev-time .ect-icon,
				#event-timeline-wrapper .ect-timeline-post.ect-featured-event:not(.style-2) span.ect-rate,
				#event-timeline-wrapper .ect-timeline-post.ect-featured-event.style-2 .timeline-content p,
				#event-timeline-wrapper .ect-timeline-post.ect-featured-event.style-2 .timeline-content a.ect-events-read-more,
				#event-timeline-wrapper .ect-timeline-post.ect-featured-event.style-2 h2.content-title a.ect-event-url,
				#event-timeline-wrapper .ect-timeline-post.ect-featured-event.style-3 h2.content-title a.ect-event-url,
				#event-timeline-wrapper .ect-timeline-post.ect-featured-event.style-3 .timeline-content a.ect-events-read-more
				{
					color: value;
				}
				#event-timeline-wrapper .ect-timeline-post.ect-featured-event:not(.style-2) .ect-google a,
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
				.ect-list-post .ect-list-post-right,
				.ect-list-post .ect-clslist-event-info,

				#ect-grid-wrapper .ect-grid-event-area,

				#ect-carousel-wrapper .ect-carousel-event-area,

				#ect-slider-wrapper .ect-slider-event-area,
				#ect-slider-wrapper .style-2 .ect-slider-left,
				
				#ect-accordion-wrapper .ect-accordion-event {
					background: value;
				}


				.ect-list-post .ect-list-post-right .ect-list-description {
					border-color : darken($event_desc_bg_color, 10%);
					box-shadow : inset 0px 0px 25px -5px darken( $event_desc_bg_color, 10% );
				}
				.ect-list-post .ect-clslist-event-info {
					box-shadow: inset 0px 0px 25px -5px darken( $event_desc_bg_color, 10% );
				}
				.ect-list-post .ect-clslist-event-details {
					background: darken($event_desc_bg_color, 4%);
					box-shadow: inset 0px 0px 25px -5px darken( $event_desc_bg_color, 10% );
				}


				#ect-grid-wrapper .ect-grid-event-area {
					box-shadow: inset 0 0 25px -5px darken( $event_desc_bg_color, 10% );
				}
				#ect-grid-wrapper .ect-grid-image {
					background: darken( $event_desc_bg_color, 10% );
				}
				.ect-grid-categories ul.tribe_events_cat li a {
					background: value;
					border: 1px solid darken( $event_desc_bg_color, 10% );
				}


				#ect-carousel-wrapper .ect-carousel-event-area {
					box-shadow: inset 0 0 25px -5px darken( $event_desc_bg_color, 10% );
				}
				#ect-carousel-wrapper .ect-carousel-image {
					background: darken( $event_desc_bg_color, 10% );
				}
				#ect-carousel-wrapper .ect-events-carousel .slick-arrow i {
					background: value;
					box-shadow: 2px 2px 0px 1px darken( $event_desc_bg_color, 1% );
				}


				#ect-slider-wrapper .ect-slider-event-area,
				#ect-slider-wrapper .style-2 .ect-slider-left,
				#ect-slider-wrapper .style-3 .ect-slider-left {
					box-shadow: inset 0 0 25px -5px darken( $event_desc_bg_color, 10% );
				}
				#ect-slider-wrapper .ect-slider-image {
					background: darken( $event_desc_bg_color, 10% );
				}
				#ect-slider-wrapper .ect-events-slider .slick-arrow i {
					background: value;
					box-shadow: 2px 2px 0px 1px darken( $event_desc_bg_color, 1% );
				}
				#ect-slider-wrapper .style-3 .ect-slider-left {
					background: rgba( $event_desc_bg_color, .94 );
				}


				#ect-accordion-wrapper .ect-accordion-event {
					border-color : darken($event_desc_bg_color, 10%);
					box-shadow : inset 0px 0px 25px -5px darken( $event_desc_bg_color, 7% );
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
				}
				@media (max-width: 790px) {
					.ect-list-post .ect-list-post-right .ect-list-description {
						border-bottom : 1px solid darken($event_desc_bg_color, 10%);
					}
					.ect-list-post .ect-clslist-event-details {
						background: darken($event_desc_bg_color, 4%);
					}
				}			
				'
				) );
				

			/*--- Event Title - CSS ---*/
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
				.ect-list-post h2.ect-list-title,
				.ect-list-post h2.ect-list-title a.ect-event-url,
				.ect-classic-list a.tribe-events-read-more,
				.ect-clslist-event-info .ect-clslist-title a.ect-event-url,

				#ect-grid-wrapper .ect-grid-title h4,
				#ect-grid-wrapper .ect-grid-title h4 a,

				#ect-carousel-wrapper .ect-carousel-title h4,
				#ect-carousel-wrapper .ect-carousel-title h4 a,

				#ect-slider-wrapper .ect-slider-title h4,
				#ect-slider-wrapper .ect-slider-title h4 a,
				
				#ect-accordion-wrapper h3.ect-accordion-title {
					value
				}

				.ect-list-post h2.ect-list-title a:hover {
					color: darken(value-color, 10%); 
				}

				.ect-list-venue .ect-rate-area .ect-rate {
					font-family: value-font-family;
				}		
				.ect-list-post .ect-rate-area span.ect-rate-icon,
				.ect-list-post .ect-list-description .ect-event-content a {
					color: lighten(value-color, 15%);
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
				
			
			/*--- Event Description - CSS ---*/
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
				.ect-list-post .ect-style3-desc .ect-event-content p,
				.ect-list-post .ect-list-post-right .ect-list-description .ect-event-content p,
				.ect-clslist-inner-container .ect-clslist-time,

				#ect-slider-wrapper .ect-slider-description .ect-event-content p,

				#ect-grid-wrapper .ect-grid-description .ect-event-content p,
				#ect-carousel-wrapper .ect-carousel-description .ect-event-content p,
				
				#ect-accordion-wrapper .ect-accordion-content,
				#ect-accordion-wrapper .ect-accordion-content p{
					value
				}
				
				.ect-list-post .ect-clslist-event-details a.tribe-events-read-more {
					color: value-color;
				}


				#ect-carousel-wrapper .ect-events-carousel .slick-arrow {
					color: value-color;
				}


				#ect-slider-wrapper .ect-events-slider .slick-arrow {
					color: value-color;
				}


				#ect-accordion-wrapper .ect-accordion-cost.no-image,
				#ect-accordion-wrapper .ect-accordion-date-full.no-image,
				#ect-accordion-wrapper .ect-accordion-content a.ect-events-read-more {
					color:darken(value-color, 5%);
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
			

			/*--- Event Venue Color - CSS ---*/
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
				.ect-list-post .ect-list-venue .ect-venue-details,
				.ect-list-post .ect-list-venue .ect-google a,
				.modern-list-venue,
				.modern-list-venue .ect-google a,

				#ect-grid-wrapper .ect-grid-venue,

				#ect-carousel-wrapper .ect-carousel-venue,

				#ect-slider-wrapper .ect-slider-venue,
				
				#ect-accordion-wrapper .ect-accordion-venue {
					value
				}


				.ect-list-post .ect-list-venue .ect-icon {
					color:value-color;
					font-size:value-font-size+5;
				}
				.ect-list-post .ect-list-venue .ect-google a,
				.ect-list-post.style-3 .ect-rate-area span.ect-rate-icon,
				.ect-list-post.style-3 .ect-rate-area .ect-rate {
					color: darken(value-color, 3%);
				}


				#ect-grid-wrapper .ect-grid-cost {
					color:value-color;
					font-size:value-font-size+6;
					font-weight:bold;
					font-family: value-font-family;
				}
				#ect-grid-wrapper .ect-grid-venue a,
				#ect-grid-wrapper .ect-grid-readmore a,
				.ect-grid-categories ul.tribe_events_cat li a {
					color: darken(value-color, 6%);
					font-family: value-font-family;
				}
				#ect-grid-wrapper .ect-grid-border:before {
					background: darken(value-color, 6%);
				}


				#ect-carousel-wrapper .ect-carousel-cost {
					color:value-color;
					font-size:value-font-size+6;
					font-weight:bold;
					font-family: value-font-family;
				}
				
				#ect-carousel-wrapper .ect-carousel-venue a,
				#ect-carousel-wrapper .ect-carousel-readmore a {
					color: darken(value-color, 6%);
					font-family: value-font-family;
				}
				#ect-carousel-wrapper .ect-carousel-border:before {
					background: darken(value-color, 6%);
				}


				#ect-slider-wrapper .ect-slider-cost {
					color:value-color;
					font-size:value-font-size+6;
					font-weight:bold;
					font-family: value-font-family;
				}
				#ect-slider-wrapper .ect-slider-venue a,
				#ect-slider-wrapper .ect-slider-readmore a {
					color: darken(value-color, 6%);
					font-family: value-font-family;
				}
				#ect-slider-wrapper .ect-slider-border:before {
					background: darken(value-color, 6%);
				}


				#ect-accordion-wrapper .ect-accordion-venue .ect-icon {
					font-size:value-font-size+5;
				}
				#ect-accordion-wrapper .ect-accordion-venue .ect-google a {
					color: darken(value-color, 5%);
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
				.ect-list-post .ect-list-post-left .ect-list-date .ect-date-area,
				.ect-list-post .ect-list-post-left .ect-list-date span.ect-custom-schedule,
				.modern-list-right-side .ect-list-date .ect-date-area,
				.modern-list-right-side .ect-list-date span.ect-custom-schedule,
				
				.style-3 .ect-list-date .ect-date-area,
				.style-3 .ect-list-date span.ect-custom-schedule,
				.ect-modern-time,

				#ect-grid-wrapper .ect-grid-date,

				#ect-carousel-wrapper .ect-carousel-date,

				#ect-slider-wrapper .ect-slider-date,
				
				#ect-accordion-wrapper .ect-accordion-date,
				#ect-accordion-wrapper.ect-accordion-view span.month-year-box {
					value
				}


				#ect-accordion-wrapper .ect-accordion-date span.ev-yr,
				#ect-accordion-wrapper .ect-accordion-date span.ev-time,
				#ect-accordion-wrapper .ect-accordion-date span.ev-weekday {
					background: lighten( value-color, 32% );
				}


				#event-timeline-wrapper .ect-timeline-post .ect-date-area {
					value
				}
				#event-timeline-wrapper .ect-timeline-post span.ect-custom-schedule{
					value
				}
				#event-timeline-wrapper .ect-timeline-year .year-placeholder span,
				#event-timeline-wrapper .timeline-meta .ev-time .ect-icon {
					font-family: value-font-family;
					color: value-color;
				}
				'
				) );
				
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
							'name' => 'Display category in templates',
							'id' => 'ect_display_categoery',
							'type'=>'select',
							// 'desc' => 'This is our option',
							'options' => array(
								'ect_enable_cat' => 'Enable',
								'ect_disable_cat' => 'Disable',
								
							),
							'default' => 'ect_disable_cat',
							) );
			$extraTab->createOption( array(
				'name' => 'Shortcodes',
				'type' => 'heading',
				) );
			$extraTab->createOption( array(
				'name'=>'Default Shortcode',
				'type' => 'custom',
				'custom' => '<code>[events-calendar-templates template="default" style="style-1" category="all" date_format="default" start_date="" end_date="" limit="10" order="ASC" hide-venue="no" time="future" featured-only="false" columns="2" autoplay="true" tags="" venues="" organizers="" socialshare="no"]</code>'
				) );
			$extraTab->createOption( array(
				'name'=>'Shortcode Attribute',
				'type' => 'custom',
				'custom' => '<style>.tf-custom table tr th, .tf-custom table tr td{border:1px solid #ddd}</style>
							<table>
							<tr><th>Attribute</th><th>Value</th></tr>
							<tr><td>template</td>
							<td><ul>
							<li>default</li>
							<li>grid-view</li>
							<li>carousel-view</li>
							<li>slider-view</li>
							<li>timeline-view</li>
							<li>masonry-view</li>
							<li>accordion-view</li>
							</ul></td></tr>

							<tr><td>style</td>
							<td><ul>
							<li>style-1</li>
							<li>style-2</li>
							<li>style-3</li>
							</ul></td></tr>

							<tr><td>category</td>
							<td><ul>
							<li>all</li>
							<li>category-slug (* You can also add comma separated multiple categories - cat1,cat2,cat3)</li>
							</ul></td></tr>

							<tr><td>date_format</td>
							<td><ul>
							<li><strong>default</strong> (01 January 2019)</li>
							<li><strong>MD,Y</strong> (Jan 01, 2019)</li>
							<li><strong>FD,Y</strong> (January 01, 2019)</li>
							<li><strong>DM</strong> (01 Jan)</li>
							<li><strong>DF</strong> (01 January)</li>
							<li><strong>MD</strong> (Jan 01)</li>
							<li><strong>FD</strong> (January 01)</li>
							<li><strong>MD,YT</strong> (Jan 01, 2019 8:00am-5:00pm)</li>
							<li><strong>full</strong> (01 January 2019 8:00am-5:00pm)</li>
							<li><strong>custom</strong>( Please check TEC settings for custom date format <a href = "'.$ect_admin_url.'">Click here </a>)</li>
							</ul></td></tr>

							<tr><td>start_date<br/>end_date</td>
							<td><ul>
							<li>Show events in between a date interval, add date in this format - YY-MM-DD</li>
							</ul></td></tr>

							<tr><td>limit</td>
							<td><ul>
							<li>10 (show number of events.)</li>
							</ul></td></tr>

							<tr><td>order</td>
							<td><ul>
							<li>ASC</li>
							<li>DESC</li>
							</ul></td></tr>

							<tr><td>hide_venue</td>
							<td><ul>
							<li>yes</li>
							<li>no</li>
							</ul></td></tr>

							<tr><td>time</td>
							<td><ul>
							<li>future (show future events.)</li>
							<li>past (show past events.)</li>
							<li>all (show all events.)</li>
							</ul></td></tr>

							<tr><td>featured-only</td>
							<td><ul>
							<li>true (show only featured events.)</li>
							<li>false</li>
							</ul></td></tr>

							<tr><td>columns</td>
							<td><ul>
							<li>6 (number of columns in grid or carousel view.)</li>
							</ul></td></tr>

							<tr><td>autoplay</td>
							<td><ul>
							<li>true (autoplay slider in carousel or slider template.)</li>
							<li>false</li>
							</ul></td></tr>

							<tr><td>tags</td>
							<td><ul>
							<li>tag-slug (* You can also add comma separated multiple tags - tag1,tag2,tag3)</li>
							</ul></td></tr>

							<tr><td>venues</td>
							<td><ul>
							<li>venue-id</li>
							</ul></td></tr>

							<tr><td>organizers</td>
							<td><ul>
							<li>organizers-id</li>
							</ul></td></tr>

							<tr><td>socialshare</td>
							<td><ul>
							<li>yes<br>no</li>
							</ul></td></tr>
							</table>'
				) );
				
 
			$extraTab->createOption( array(
				'name'=>'Shortcode For Calendar Template',
				'type' => 'custom',
				'custom' => '<code>[ect-calendar-layout date-format="d F Y" show-category-filter="true"]</code><small style="color:red;font-size: 19px;"></small>',
				
			) );
			$extraTab->createOption( array(
				'name'=>'Shortcode Attribute Explained:',
				'type' => 'custom',
				'custom' => '<style>.tf-custom table tr th, .tf-custom table tr td{border:1px solid #ddd}</style>
							<table>
							<tr><th>Attribute</th><th>Value</th></tr>

							<tr><td>limit</td>
							<td><ul>
							<li>Any positive number to limit the fetched events</li>
							<li>Default value:10 will be used if left blank</li>
							</ul></td></tr>
							</table>'
				) );
			$stylingTab->createOption( array(
				'type' => 'save'
				) );
			$extraTab->createOption( array(
				'type' => 'save'
				) );
		}