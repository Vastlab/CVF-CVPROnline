
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
    return "CVPR20";
    
        foreach ( get_post_meta( $eventid ) as $field => $value ) {
         $field = trim( $field );
         if ( is_array( $value ) ) $value = implode( ', ', $value );
         if ( 0 === strpos( $field, '_EventCurrencySymbol' ) ) return  $value;         
         //         if ( 0 === strpos( $field, '_ecp_custom_4' ) ) return  $value;
         }
        if(strlen($value)>1) return $value;

        else return $value = tribe_meta_event_tags($lasteventid,"",false);
            
 }



// where CVPR20 put its teaser
function conf_get_teaserpic($eventid) {
    global $conf_fileprefix;
    $paperid =  conf_get_paperid($eventid);
    $dir= conf_get_directory($eventid);
    $postpic =$conf_fileprefix.$dir.'/'.$paperid."/".$paperid."-teaser.gif";
    return $postpic;
}

// where CVPR20 put its teaser
function conf_get_teasertxt($eventid) {
    global $conf_fileprefix;    
    $paperid =   conf_get_paperid($eventid);
    $dir= conf_get_directory($eventid);
    
    $postpic = substr(file_get_contents($conf_fileprefix.$dir.'/'.$paperid."/".$paperid."-teaser.txt"),0,150);
    return $postpic;
}


// where CVPR20 put its teaser
function conf_get_keywords($eventid) {
    global $conf_fileprefix;    
    $paperid =   conf_get_paperid($eventid);
    $postpic = substr(file_get_contents($conf_fileprefix.conf_get_directory($eventid).'/'.$paperid."/".$paperid."-keywords.txt"),0,250);
    return $postpic;
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

