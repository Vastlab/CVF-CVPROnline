<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

define( 'WP_ALLOW_REPAIR', true );

    
//set_time_limit(30);
//@ini_set('max_execution_time', 15); 
//@ini_set( 'upload_max_size' , '128M' );
//@ini_set( 'upload_max_filesize' , '128M' );
//@ini_set( 'post_max_size', '23M');
//@ini_set( 'memory_limit', '64M' );
//global $upload_space_check_disabled;
//$upload_space_check_disabled=true;
//update_site_option(upload_space_check_disable,"1");
//php_info()




// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'wmpudev_enqueue_icon_stylesheet')):
function wmpudev_enqueue_icon_stylesheet() {
	wp_register_style( 'fontawesome', 'http:////maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css' );
	wp_enqueue_style( 'fontawesome');
}
endif;
add_action( 'wp_enqueue_scripts', 'wmpudev_enqueue_icon_stylesheet' );


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



function cvf_remove_admin_bar() {
if (!current_user_can('administrator') && !is_admin()) {
  show_admin_bar(false);
}
}
add_action('after_setup_theme', 'cvf_remove_admin_bar');



// Enqueue bootstrap
function themebs_enqueue_styles() {
    wp_enqueue_style( 'bootstrap', get_stylesheet_directory_uri() . '/lib/bootstrap/css/bootstrap.min.css' );
    wp_enqueue_style( 'google_fonts', 'https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&family=Montserrat:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&display=swap', false );
    wp_enqueue_style( 'custom_css', get_stylesheet_directory_uri() . '/lib/css/custom.css', array( 'bootstrap', 'google_fonts' ) );
}
add_action( 'wp_enqueue_scripts', 'themebs_enqueue_styles');

function themebs_enqueue_scripts() {
    wp_enqueue_script( 'bootstrap', get_stylesheet_directory_uri() . '/lib/bootstrap/js/bootstrap.bundle.min.js', array( 'jquery' ) );
}
add_action( 'wp_enqueue_scripts', 'themebs_enqueue_scripts');

function my_custom_scripts() {
    wp_enqueue_script( 'custom-js', get_stylesheet_directory_uri() . 'custom.js', array( 'jquery' ),'',true );
}
add_action( 'wp_enqueue_scripts', 'my_custom_scripts' );


add_filter( 'allowed_http_origins', 'add_allowed_origins' );
function add_allowed_origins( $origins ) {
    $origins[] = 'http://d1tz9o43mm5y8k.cloudfront.net';
    $origins[] = 'http://cvpr20.s3-website-us-west-2.amazonaws.com';
    $origins[] = 'http://hk.cvpr20.com';
    $origins[] = 'http://cvpr20.cn';    
    return $origins;
}

//* Sort Events by date and alphabetically 

function tribe_custom_event_order( $query ) {
    //    if ( tribe_is_list_view() && $query->query['post_type'] == 'tribe_events' ) {
    //        $args =  array(  'start_date' => 'DESC', 'title' => 'ASC' );     // sort date/then title then date

    if ( tribe_is_list_view()
       && $query->query['post_type'] == 'tribe_events' ) {        
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


add_filter( 'pre_get_posts', 'tribe_custom_event_order', 99 );


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




/**
 * Redirect user after successful login.
  *
   * @param string $redirect_to URL to redirect to.
    * @param string $request URL the user is coming from.
     * @param object $user Logged user's data.
      * @return string
       */
function my_login_redirect( $redirect_to, $request, $user ) {
    //is there a user to check?
    if ( isset( $user->roles ) && is_array( $user->roles ) ) {
        //check for admins
        if ( in_array( 'administrator', $user->roles ) ) {
            // redirect them to the default place
            return $redirect_to;
        } else {
            return home_url();
        }
    } else {
        return $redirect_to;
    }
}                                                                                                   

add_filter( 'login_redirect', 'my_login_redirect', 10, 3 );

/**
 * Add support for DISCUZ comments plugin  V7
 */

function cvf_wpdiscuz_shortcode() {
    $html = "";
    if (file_exists(ABSPATH . "wp-content/plugins/wpdiscuz/themes/default/comment-form.php")) {
        ob_start();
        include_once ABSPATH . "wp-content/plugins/wpdiscuz/themes/default/comment-form.php";
        $html = ob_get_clean();
    }
    return $html;
}


add_shortcode("wpdiscuz_comments", "cvf_wpdiscuz_shortcode");




function wpbsearchform( $form ) {

    $form = '<form role="search" method="get" id="searchform" action="' . home_url( '/' ) . '" >
     <div><label class="screen-reader-text" for="s">' . __('Search for:') . '</label>
     <input type="text" value="' . get_search_query() . '" name="s" id="s" />
     <input type="submit" id="searchsubmit" value="'. esc_attr__('Search') .'" />
     </div>
     </form>';

    return $form;
}

add_shortcode('wpbsearch', 'wpbsearchform');


/* sql hacks to enable comments on sponros and events (so discus works)

UPDATE wp_posts SET comment_status = 'closed' WHERE `post_type` = 'sponsor';
UPDATE wp_posts SET comment_status = 'open' WHERE `post_type` = 'tribe_events';

 */




/*
function live_simple_clock($title="",$fuseau = 0)
{
	//css

	$font = esc_attr(get_option('tka_lsc_font'));
	$font_size = esc_attr(get_option('tka_lsc_font_size'));
	$font_weight = esc_attr(get_option('tka_lsc_font_weight'));
	$font_color = esc_attr(get_option('tka_lsc_font_color'));

	$codeCSS = '';
	if ($font != '') {
		$codeCSS .= 'font-family:' . $font . ';';
	}
	if ($font_size != '') {
		$codeCSS .= 'font-size:' . $font_size . ';';
	}
	if ($font_weight != '') {
		$codeCSS .= 'font-weight:' . $font_weight . ';';
	}
	if ($font_color != '') {
		$codeCSS .= 'color:' . $font_color . ';';
	}
	//end css

	$format = esc_attr(get_option('tka_lsc_format'));
 	if ($fuseau == '') {
		$fuseau = '0';
	}


 	$hidesecond = esc_attr(get_option('tka_lsc_hidesecond'));

	if ($format == 12) {
		$temp = 'ampm = h >= 12 ? "pm" : "am";h=h % 12;h=h ? h : 12;';
	} else {
		$temp = '';
	}

	$clock_html = '<span id="tka_time" class="tka_style" style="' . $codeCSS . '"></span>';

	$clock_script = '
<script>
	function checkTime(i) {
  if (i < 10) {
    i = "0" + i;
  }
  return i;
}

function startTime() {
  var ampm="";
  var today = new Date();

  var n = today.getTimezoneOffset();
  var temp=(' . $fuseau . '*60)/60;
  var h = today.getHours();
  	h=(temp+h);' . $temp . '
  
  var m = today.getMinutes();
  var s = today.getSeconds();
  // add a zero in front of numbers<10
  m = checkTime(m);
  s = checkTime(s);';

	if ($hidesecond == true) {
		$clock_script .= ' document.getElementById("tka_time").innerHTML ="' . $title . ' "+ h + ":" + m +" "+ampm;';
	} else {
		$clock_script .= ' document.getElementById("tka_time").innerHTML ="' . $title . ' "+ h + ":" + m + ":" + s +" "+ampm;';
	}



	$clock_script .= '
  t = setTimeout(function() {
    startTime()
  }, 500);
}


startTime();

</script>';
	return $clock_html . $clock_script;
}
*/



// Generate custom navbar brand markup
function custom_navbar_brand() {
    $custom_logo_id = get_theme_mod( 'custom_logo' );
    $logo = wp_get_attachment_image_src( $custom_logo_id , 'full' );

    
    if ( has_custom_logo() ) {
            echo ' <a class="navbar-brand flex-shrink-0" href="' . get_home_url() . '"><img src="' . esc_url( $logo[0] ) . '" alt="home image" /></a>';
    } else {
            echo '<h1>'. get_bloginfo( 'name' ) .'</h1>';
    }
}





add_filter( 'wp_get_nav_menu_items',
            function( $items, $menu, $args )
            {
                $file = '/tmp/nav.log'; // Edit this filepath to your needs.
                if( file_exists( $file ) && is_writeable( $file ) )
                {
                    $s = sprintf( " %s - menu: %s - count: %d %s" , date( "c" ),
                                  $menu->slug,
                                  $menu->count,
                                                  PHP_EOL
                    );
                    file_put_contents( $file, $s, FILE_APPEND | LOCK_EX );
                }
                return $items;
            }
            , PHP_INT_MAX, 3 );


add_filter( 'wp_new_user_notification_email', 'custom_wp_new_user_notification_email', 10, 3 );

function custom_wp_new_user_notification_email( $wp_new_user_notification_email, $user, $blogname ) {
    $key = get_password_reset_key( $user );
    $message = sprintf(__('Welcome to the CVPR2020 Virtual,')) . "\r\n\r\n";
    $message .= 'Login at  cvpr20.com using this email and registration reference number as the password. ';
    $message .= "After this you can enjoy the full conference!" . "\r\n\r\n";
    $message .= "Kind regards," . "\r\n";
    $message .= "cvprsupport  team" . "\r\n";
    $wp_new_user_notification_email['message'] = $message;
    $wp_new_user_notification_email['headers'] = 'From: CVPRSupport<cvprsupport@vast.uccs.edu>'; 

    return $wp_new_user_notification_email;
}




/*
$inipath = php_ini_loaded_file();

if ($inipath) {
    echo 'Loaded php.ini: ' . $inipath;
    phpinfo();   
} else {
    echo 'A php.ini file is not loaded';
}
*/
