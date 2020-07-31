<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


    
//set_time_limit(30);
//ini_set('max_execution_time', 30); 



// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );

if ( !function_exists( 'chld_thm_cfg_parent_css' ) ):
    function chld_thm_cfg_parent_css() {
        wp_enqueue_style( 'chld_thm_cfg_parent', trailingslashit( get_template_directory_uri() ) . 'style.css', array(  ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'chld_thm_cfg_parent_css', 10 );
         
if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {
        wp_enqueue_style( 'chld_thm_cfg_separate', trailingslashit( get_stylesheet_directory_uri() ) . 'ctc-style.css', array( 'chld_thm_cfg_parent','twentyseventeen-style','twentyseventeen-block-style' ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 10 );


//* Sort Events by date and alphabetically 

function tribe_custom_event_order( $query ) {
    //    if ( tribe_is_list_view() && $query->query['post_type'] == 'tribe_events' ) {
    //        $args =  array(  'start_date' => 'DESC', 'title' => 'ASC' );     // sort date/then title then date
    if ($query->query['post_type'] == 'tribe_events' ) {        
        $args =  array( 'meta_value_num' => 'ASC');
        $query->set( 'orderby', $args );
        $query->set( 'meta_key', '_ecp_custom_9' );
        
    }

    return $query;
}

function tribe_custom_prev_event_order( $query ) {
    //    if ( tribe_is_list_view() && $query->query['post_type'] == 'tribe_events' ) {
    //        $args =  array(  'start_date' => 'DESC', 'title' => 'ASC' );     // sort date/then title then date
    if ($query->query['post_type'] == 'tribe_events' ) {        
        $args =  array( 'meta_value_num' => 'DESC');
        $query->set( 'orderby', $args );
        $query->set( 'meta_key', '_ecp_custom_9' );
        
    }

    return $query;
}


//add_filter( 'pre_get_posts', 'tribe_custom_event_order', 99 );


function  custom_category_specific_single_event_pagination( $args, $post ) {
    $category_ids = tribe_get_event_cat_ids( $post->ID );

    if ( empty( $category_ids ) ) {
        return $args;
    }

    if ( empty( $args['tax_query'] ) ) {
        $args['tax_query'] = array();
    }

    $args['tax_query'][] = array(
        'taxonomy' => Tribe__Events__Main::TAXONOMY,
        'terms' => $category_ids
    );

    return $args;
}

function  CVF_next_category_specific_single_event_pagination( $query, $post ) {
    $cat_ids = tribe_get_event_cat_ids( $post->ID );
    if ($query->query['post_type'] == 'tribe_events' ) {        
        $args =  array( 'category__in'   => $cat_ids);
        $query->set( 'AND', $args );
    };
    return $query;
}

function  CVF_prevd_category_specific_single_event_pagination( $query, $post ) {
    $cat_ids = tribe_get_event_cat_ids( $post->ID );
    if ($query->query['post_type'] == 'tribe_events' ) {        
        $args =  array( 'category__in'   => $cat_ids,
                        'meta_value_num' => 'DESC');
        $query->set( 'orderby', $args );
        $query->set( 'meta_key', '_ecp_custom_9' );
    };
    return $query;
}



//add_filter( 'tribe_events_get_next_event_link', 'custom_category_specific_single_event_pagination', 10, 2 );
//add_filter( 'tribe_events_get_previous_event_link', 'custom_category_specific_single_event_pagination', 10, 2 );
//add_filter( 'tribe_events_get_next_event_link', 'CVF_next_category_specific_single_event_pagination', 10, 2 );
//add_filter( 'tribe_events_get_previous_event_link', 'CVF_prev_category_specific_single_event_pagination', 10, 2 );



function get_closest_event( $mode = 'next' ) {
		global $wpdb;

		$post_obj = get_post( $this->current_event_id );

        $cat_ids = tribe_get_event_cat_ids( $post_obj->ID );
        
		if ( 'previous' === $mode ) {
			$order      = 'DESC';
			$direction  = '<';
		} else {
			$order      = 'ASC';
			$direction  = '>';
			$mode       = 'next';
		}

		$args = [
            'category__in'   => $cat_ids,            
			'posts_per_page' => 1,
			'post__not_in'   => [ $this->current_event_id ],
			'meta_query'     => [
				[
					'key'     => '_EventStartDate',
					'value'   => $post_obj->_EventStartDate,
					'type'    => 'DATETIME',
					'compare' => $direction,
				],
				[
					'key'     => '_EventHideFromUpcoming',
					'compare' => 'NOT EXISTS',
				],
				'relation'    => 'AND',
			],
		];

		$events_orm = tribe_events();

		/**
		 * Allows the query arguments used when retrieving the next/previous event link
		 * to be modified.
		 *
		 * @since 4.6.12
		 *
		 * @param array   $args
		 * @param WP_Post $post_obj
		 */
		$args = (array) apply_filters( "tribe_events_get_{$mode}_event_link", $args, $post_obj );

		$events_orm->order_by( 'event_date', $order );
		$events_orm->by_args( $args );
		$query = $events_orm->get_query();

		// Make sure we are not including same datetime events
		add_filter( 'posts_where', [ $this, 'get_closest_event_where' ] );

		// Fetch the posts
		$query->get_posts();

		// Remove this filter right after fetching the events
		remove_filter( 'posts_where', [ $this, 'get_closest_event_where' ] );

		$results = $query->posts;

		$event = null;

		// If we successfully located the next/prev event, we should have precisely one element in $results
		if ( 1 === count( $results ) ) {
			$event = current( $results );
		}

		/**
		 * Affords an opportunity to modify the event used to generate the event link (typically for
		 * the next or previous event in relation to $post).
		 *
		 * @since 4.6.12
		 *
		 * @param WP_Post $post_obj
		 * @param string  $mode (typically "previous" or "next")
		 */
		return apply_filters( 'tribe_events_get_closest_event', $event, $post_obj, $mode );
	}









// END ENQUEUE PARENT ACTION
//Wrap Wordpress Video Shortcode in Responsive Wrapper ======================================================================================================================== */



/*
function InsertVideoFromId($papernum,$prefix) {

    $output = '     <div class='flex-video' style=\'padding-bottom:56.25%\'>
			     <video  playsinline  class="wp-video-shortcode" id="'.$papernum.'" width="1920" height="1080" preload="metadata" data-controls="controls">
			     <source type="video/mp4" src="'.$prefix.$papernum.'.mp4" />
			     <a href="'.$prefix.$papernum.'.mp4">'.$prefix.$papernum.'.mp4 </a>
							  </video>
							  </div>'
    return $output;

}; 

 */



/*  much faster to use phpmyadmin and do
DELETE FROM wp_posts WHERE post_type='tribe_events';
DELETE FROM wp_postmeta WHERE post_id NOT IN (SELECT id FROM wp_posts);
DELETE FROM wp_term_relationships WHERE object_id NOT IN (SELECT id FROM wp_posts)

*/



/*
//  this is very agresssive.. it will put the site in "recovery mode" but does clear the queue  TB: maybe the tribe_exit() at end is killer so commened out for next test
function TBclear_queues_and_redirect( $location = null ) {
		$clear_queues = tribe_get_request_var( tribe_clear_ea_processes, false );

		if ( empty( $clear_queues ) ) {
			return;
		}

		$cleared = $this->clear_queues();

		// Let's also re-run, forcing it, the feature support check to make sure we still support Async processing.
		tribe( 'feature-detection' )->supports_async_process( true );

		$location = null !== $location
			? $location
			: remove_query_arg( tribe_clear_ea_processes );

		$location = add_query_arg( array( self::CLEAR_RESULT => $cleared ), $location );

		wp_redirect( $location );
        //		tribe_exit();
	}

*/



 // Hooks the above function up so it runs in the single organizer and venue pages
// add_action( 'tribe_events_single_organizer_before_upcoming_events', 'show_wp_custom_fields' );
// add_action( 'tribe_events_single_venue_before_upcoming_events', 'show_wp_custom_fields' );



// allow users only login in once by logging out all other sessioss during login
function wpse_12282015_single_login_authenticate($user, $username, $password) {

    $user =  get_user_by('login', $username);

    if( isset($user->ID) ){

        if(isset($user->roles) && is_array($user->roles)) {

            //check for admins
            if(in_array('administrator', $user->roles)) {

                // admin can log in more than once
                return $user;
            }
        }

        // get all sessions for user
        $sessions = WP_Session_Tokens::get_instance($user->ID);

        // destroy everything since we'll be logging in shortly
        $sessions->destroy_all();
    }

    return $user;
}
//disabled during development uncomment for cvprliveshell
//add_filter('authenticate', 'wpse_12282015_single_login_authenticate', 0, 3);




