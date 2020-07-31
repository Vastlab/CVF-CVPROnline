<?php



$conf_fileprefix="";
$confdir="CVPR20";

set_time_limit ( 30 );  // limit time incase something goes wrong..  may need to adjust it.    When loading csv may need to expand it signficantly. 

//  should get from UI or DB but hard coded for now.
function get_conf_fileprefix() {
    //RemoveForChina  return "http://cvpr.cn/";
	//                return     "https://cvpr20.s3-us-west-2.amazonaws.com/CVPR20/";
    //    	return "http://d1tz9o43mm5y8k.cloudfront.net/";
     return "http://cvpr20.s3-website-us-west-2.amazonaws.com/";
    //       return "http://cvpr20.com/";
}

// authros is either second field in custom or  in event  descriptio 
function conf_get_authors( $eventid) {
    return substr(tribe_events_get_the_excerpt($eventid),3,-5); // when hiding in event exerpt its got a <p> and </p> aorund it
    foreach ( get_post_meta( $eventid ) as $field => $value ) {
         $field = trim( $field );
         if ( is_array( $value ) ) $value = implode( ', ', $value );
         if ( 0 === strpos( $field, '_ecp_custom_2' ) ) return  $value;
     }
 }


// paperid is 3rd custom items (but also in cost )
function conf_get_paperid($eventid) {
//     $eventid=get_the_ID();
    return  intval(get_post_meta( $eventid, '_EventCost', true ));              
    /*    foreach ( get_post_meta( $eventid ) as $field => $value ) {
         $field = trim( $field );
         if ( is_array( $value ) ) $value = implode( ', ', $value );
         if ( 0 === strpos( $field, '_ecp_custom_3' ) ) return  $value;
         if ( 0 === strpos( $field, '_EventCost' ) ) return  $value;         
         }*/
 }


// subevent dirctory is in tags symbol  or  is 4th custom items
function conf_get_directory( $eventid) {
    $dir =  "CVPR20/".get_post_meta(  $eventid, '_EventCurrencySymbol', true );          
     return $dir;
 }



// where CVPR20 put its teaser
function conf_get_teaserpic($eventid) {
    $conf_fileprefix = get_conf_fileprefix();
    $paperid =  conf_get_paperid($eventid);
    $dir= conf_get_directory($eventid);
    $postpic =$conf_fileprefix.$dir.'/'.$paperid."/".$paperid."-teaser.gif";
    if(!file_exists($dir.'/'.$paperid."/".$paperid."-teaser.gif")){
        $postpic =$conf_fileprefix."cvpr-defaultteaser.gif";    
    }
    return $postpic;
}

// where CVPR20 put its teaser
function conf_get_teasertxt($eventid) {
    $conf_fileprefix = get_conf_fileprefix();
    $postslides="";
    $paperid =  conf_get_paperid($eventid);    
    $fbase =conf_get_directory($eventid)."/".$paperid."/".$paperid;    
    $fname = $fbase."-teaser.txt";
    if(file_exists($fname)){
        $posttxt= substr(file_get_contents($fname),0,150);
    }
    else $posttxt="";
    return $posttxt;
}

// where CVPR20 put its teaser
function conf_get_keywords($eventid) {
    $conf_fileprefix = get_conf_fileprefix();
    $paperid =  conf_get_paperid($eventid);
    $fbase =conf_get_directory($eventid)."/".$paperid."/".$paperid;    
    $fname = $fbase."-keywords.txt";
    if(file_exists($fname)){
        $keywords= substr(file_get_contents($fname),0,250);
    }
    else $keywords="";

    return $keywords;
}


//  editors don't see admin bar.. messes with view
add_action('after_setup_theme', 'remove_admin_bar');

function remove_admin_bar() {
    if (!current_user_can('administrator') && !is_admin()) {
        show_admin_bar(false);
    }
}

/*
 * Possible solution for Single Event page 404 errors where the WP_Query has an attachment set
 * IMPORTANT: Flush permalinks after pasting this code: http://tri.be/support/documentation/troubleshooting-404-errors/
 * Updated to work with post 3.10 versions

function tribe_attachment_404_fix () {
    if (class_exists('Tribe__Events__Main')) {
        remove_action( 'init', array( Tribe__Events__Main::instance(), 'init' ), 10 );
        add_action( 'init', array( Tribe__Events__Main::instance(), 'init' ), 1 );
    }
}

add_action( 'after_setup_theme', 'tribe_attachment_404_fix' );
 */

?>

