<?php



$conf_fileprefix = 'http://lela.vast.uccs.edu/';
$confdir="CVPR20";

set_time_limit ( 20 );  // limit time incase something goes wrong..  may need to adjust it.    When loading csv may need to expand it signficantly. 

//  should get from UI or DB but hard coded for now.
function conf_get_fileprefix( $val) {
    //    global $Conf_Filefprefix;
    return $Conf_Filefprefix= $val;
    
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
function conf_get_paperid($eventid=0) {
    static $lastseventid=0;
    if($eventid !==0) $lasteventid=$eventid;
    foreach ( get_post_meta( $lasteventid ) as $field => $value ) {
         $field = trim( $field );
         if ( is_array( $value ) ) $value = implode( ', ', $value );
         if ( 0 === strpos( $field, '_ecp_custom_3' ) ) return  $value;
         if ( 0 === strpos( $field, '_EventCost' ) ) return  $value;         
     }
 }


// subevent dirctory is in tags symbol  or  is 4th custom items
function conf_get_directory($eventid ) {
    $eventid=get_the_ID();
    global $confdir;
        foreach ( get_post_meta( $eventid ) as $field => $value ) {
         $field = trim( $field );
         if ( is_array( $value ) ) $value = implode( ', ', $value );
         if ( 0 === strcmp( $field, '_EventCurrencySymbol' ) ) return $confdir= "CVPR20/".$value;
         }

        
        if(strlen($confdir)>1) {
            return $confdir;
        }
        else    return "CVPR20/CVPR20";
 }



// where CVPR20 put its teaser
function conf_get_teaserpic($eventid) {
    global $conf_fileprefix;
    if(strlen($conf_fileprefix) < 5) $conf_fileprefix='http://lela.vast.uccs.edu/';
    $paperid =  conf_get_paperid($eventid);
    $dir= conf_get_directory($eventid);
    $postpic =$conf_fileprefix.$dir.'/'.$paperid."/".$paperid."-teaser.gif";
    return $postpic;
}

// where CVPR20 put its teaser
function conf_get_teasertxt($eventid) {
    global $conf_fileprefix;
    if(strlen($conf_fileprefix) < 5) $conf_fileprefix='http://lela.vast.uccs.edu/';
    $paperid =   conf_get_paperid($eventid);
    $dir= conf_get_directory($eventid);
    
    $postpic = substr(file_get_contents($conf_fileprefix.$dir.'/'.$paperid."/".$paperid."-teaser.txt"),0,150);
    return $postpic;
}

// where CVPR20 put its teaser
function conf_get_keywords($eventid) {
    global $conf_fileprefix;
    if(strlen($conf_fileprefix) < 5) $conf_fileprefix='http://lela.vast.uccs.edu/';
    $paperid =   conf_get_paperid($eventid);
    $keywords = $conf_fileprefix.conf_get_directory($eventid).'/'.$paperid."/".$paperid."-keywords.txt";
    //    echo "Key/paper".$keywords." ".$paperid;
    
    $postpic = substr(file_get_contents($keywords),0,250);
    return $postpic;
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
