<?php
/**
 * Single Event Template
 * A single event. This displays the event title, description, meta, and
 * optionally, the Google map for the event.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/single-event.php
 *
 * @package TribeEventsCalendar
 * @version 4.6.19

 Modified by Tboult for CVPR2020.  Determien if is poster of oral and renders the required elements for them
 *
 */



if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}



if (file_exists(ABSPATH . 'wp-content/themes/cvf-twentyseventeen-child/cvf-functions.php')) {
    //    ob_start();
    include_once ABSPATH . 'wp-content/themes/cvf-twentyseventeen-child/cvf-functions.php';
    //     ob_get_clean();
}
include_once("wp-includes/wp-db.php");

if( !is_user_logged_in())  {
    wp_redirect( 'http://www.cvpr20.com/wp-login.php' );
    exit;
}




/**
 * Outputs all WP post meta fields (except those prefixed "_"), feel
 * free to tweak the formatting!   */


function show_wp_custom_fields() {
    foreach ( get_post_meta( get_the_ID() ) as $field => $value ) {
         $field = trim( $field );
         if ( is_array( $value ) ) $value = implode( ', ', $value );
         //        if ( 0 === stripos( $field, '_' ) ) continue; // Don't expose "private" fields
         echo '<strong>' . esc_html( $field ) . '</strong> =  ' . esc_html( $value ) . '<br/>';
     }
 }





$eventid=get_the_ID();
function Insert_paper_video($paperid=0){
    $conf_fileprefix=get_conf_fileprefix();
    $eventid = get_the_ID();
    $dir=conf_get_directory($eventid);
    //    $dir =  "CVPR20/".get_post_meta( get_the_ID(), '_EventCurrencySymbol', true );
    $fbase = $dir."/".$paperid."/".$paperid;
    $postvid="";
    if(file_exists($fbase."-oral.mp4")){         $postvid= $fbase."-oral.mp4";}
    else if(file_exists($fbase."-1min.mp4")){         $postvid= $fbase."-1min.mp4";}
    else if(file_exists($fbase."-poster.mp4")){         $postvid= $fbase."-poster.mp4";}

    if(strlen($postvid)>1)  {
        $conf_fileprefix=get_conf_fileprefix();
        $postvid = $conf_fileprefix.$postvid;
        $output = '<div style="width: 95%;" class="wp-video" class="cvf-video"><!--[if lt IE 9]><script>document.createElement(\'video\');</script><![endif]-->
                                <video class="wp-video-shortcode" id="video-'.$postvid.'" width="95%" height="95%" preload="metadata" controls="controls"><source type="video/mp4" src="'.$postvid.'"></video></div><br/>
<p style="text-align: center;">If the embedded video does not play properly click download in the lower right corner of video</p>
';
    } else  {
        $postvid = $conf_fileprefix.$postvid;
        $output="";        
        // $output = "<p> Debug message: No video found at ".$postvid." at base ".$fbase." </p> ";
        }
    echo $output;
}

/* load by searching for  files that might be used */  

function  Insert_paper_slides($paperid=0){
    $conf_fileprefix=get_conf_fileprefix();
    $eventid = get_the_ID();
    $dir=conf_get_directory($eventid);
    $postslides="";
    $fbase =$dir."/".$paperid."/".$paperid;    
    if(file_exists($fbase."-talk.pdf")){         $postslides= $fbase."-talk.pdf";}
    else if(file_exists($fbase."-slides.pdf")){         $postslides= $fbase."-slides.pdf";}
    else if(file_exists($fbase."-poster.pdf")){         $postslides= $fbase."-poster.pdf";}
    if(strlen($postslides)>0)  {
        $conf_fileprefix=get_conf_fileprefix();                
        $postslides = $conf_fileprefix.$postslides;        
        $shortc ='[pdfjs-viewer url='.$postslides.' viewer_width=100% viewer_height=750px fullscreen=true fullscreen_text="View Slide PDF Fullscreen"   download=true print=true]';
        echo do_shortcode($shortc);}
    else {
        $conf_fileprefix=get_conf_fileprefix();        
        $postslides = $conf_fileprefix.$postslides;        
        //        echo "<p> Debug message No Slides to display at  ".$postslides." at base ".$fbase." </p>"; 
    }
}


function  Insert_event_URL($paperid=0, $label = ''){
    $eventid = get_the_ID();
    $URL= get_post_meta( $eventid, '_EventURL', true );
    // *ideally look up via sql   using z20paper id and wid
    if(strlen($URL)>1) {
        echo ' <div class="externalurl">  <p><a href="'.$URL.'" >  <strong> See also </strong> '.$URL.'</a></p></div>'; 
    }
}
                 



function  Insert_paper_videochat($paperid=0, $label = ''){
    global $wpdb;
    $eventid = get_the_ID();
    $paperid= intval(get_post_meta( $eventid, '_EventCost', true ));
    $authors=conf_get_authors($eventid);     
    $session =  get_post_meta(  $eventid, '_EventCurrencySymbol', true );
    $zoomroom =  get_post_meta(  $eventid, '_ecp_custom_10', true );
    $starttime = tribe_get_start_date($eventid,true,"Y-m-d H:i");
    $endtime = tribe_get_end_date($eventid,true,"Y-m-d H:i");
    //    date_default_timezone_set('America/Los_Angeles');
    //    $ptime=date();
    $q= 'SELECT join_url FROM  wp_zoom WHERE session = "'.$session.'" and zoom_room =  "'.$zoomroom.'" and  "'.$starttime.'" between startdate and enddate';
    //    echo $q;
    if($wpdb != NULL){
        $zlink = $wpdb->get_var($q);
        // try zero room
        if(strlen($zlink)<2) {
            //                                           echo "DEBUG: First zoom search failed for ".$q;            
                    $q= 'select join_url from  wp_zoom where session = "'.$session.'" and zoom_room = "1" and  "'.$starttime.'" between startdate and enddate';
                    $zlink = $wpdb->get_var($q);
                    //                                  echo "DEBUG: try second zoom search".$q;
        }
    }
    else  echo     "\nNull database in video room lookup";
    //    echo "|".$zlink."|";

    if(strlen($zlink)>2) {
        $output = '<div zoomlink style="text-align:center;"> <br/><a  href="#" class="zoomlink"  aria-label="open window to join zoom with authors '
                .$authors.'  between '.$starttime.' PDT and '  .$endtime
                .' PDT"   onclick=\'window.open("'.$zlink.'");return false;\' style="width:100%;font-size:large;background-color:#4040FF; padding:10px 20px 10px 20px;   border-radius: 15px;     align-content: center; color:white; " > Click between '.$starttime.' PDT and '.
                                                                $endtime.' PDT  to Open  Zoom Window' ;    
        
                   $output.=  '</a></div>';

    } else
        $output=" NO ZOOM FOUND<br/>"; 
    //       $output = ' <br/><a href="http://zoom.us" class=zlink > Click  join video chat between '.$starttime.' PDT and '.$endtime.' PDT</a> ';
    return $output;
 }


function  Insert_paper_textchat($paperid=0, $label = ''){
    //    $eventid=get_the_ID();
    //    $channel = conf_get_directory($eventid)."-".$paperid;
    //    echo $output= do_shortcode('[wise-chat chat_height=400px channel="'.$channel.'"]');
}


function  Insert_paper_links($paperid=0, $label = ''){
    global $wpdb;
$eventid=get_the_ID();
$session  =    get_post_meta(  $eventid, '_EventCurrencySymbol', true );

//echo  "<br/>".  'SELECT paper_link FROM  wp_cvpr_paper_links WHERE paper_id ='. $paperid.' and session = "'.$session.'" ';
if($wpdb != NULL)
    $plink = $wpdb->get_var('SELECT paper_link FROM  wp_cvpr_paper_links WHERE paper_id ='. $paperid.' and session = "'.$session.'" ');
else  echo     "\nNull database in paper lookup";
//echo "|".$plink."|";
if(strlen($plink)>2) 
    $output = ' <a href='.$plink.' class=paperlink     style="font-size:22px"  > Link to associated paper on CVF open access archive </a> ';
else $output="<br/>";
return $output;
}




function Insert_QA(){
//           echo '<h2> Q&A with voting. Asyncronous or live so authors see most requested questions during video Q&A </h2>';                                     
    return  do_shortcode('[wpdiscuz_comments] ');
}




$events_label_singular = tribe_get_event_label_singular();
$events_label_plural   = tribe_get_event_label_plural();


$defaults   = array(
    'echo'         => false,
    'label'        => null,
    'label_before' => '<div>',
    'label_after'  => '</div>',
    'wrap_before'  => '<ul class="tribe-event-categories">',
    'wrap_after'   => '</ul>',
);
$args=null;
$args       = wp_parse_args( $args, $defaults );
$categories = tribe_get_event_taxonomy( $eventid, $args );



$is_poster=false;
if(stripos($categories, 'Poster') !== false){
    $is_poster=true; //echo "IS POSTER";
}
$is_oral=false;
if(stripos($categories, 'Oral') !== false){
    $is_oral=true; //echo "IS ORAL";
}
$is_live=false;
if(stripos($categories, 'Live') !== false){
    $is_live=true; 
}
$is_keynote=false;
if(stripos($categories, 'keynote') !== false){
    $is_keynote=true; 
}
$is_async=false;
if(stripos($categories, 'async') !== false){
    $is_async=true;
}

$is_net=false;
if(stripos($categories, 'net1') !== false){
    $is_net=true; 
} else 
if(stripos($categories, 'net2') !== false){
    $is_net=true; 
}
else if(stripos($categories, 'net3') !== false){
    $is_net=true; 
}

//echo "isnet="$is_net;


//echo  clear_queues_and_redirect();


//echo delete_all_events();

// aggressive stop to import queues. 
//echo clear_queues_and_redirect();

// debugging info
//echo  "DEBUG: cat=". $categories;
// to show all details of added fields, uncoment this
//echo "DEBUGGING INFO:". show_wp_custom_fields();



?>



     <!-- Navigation
          <nav class="tribe-events-nav-pagination" aria-label="<?php printf( esc_html__( '%s Navigation', 'the-events-calendar' ), $events_label_singular ); ?>">
              <ul class="tribe-events-sub-nav">
                                  <li class="tribe-events-nav-previous">Prev:<?php tribe_the_prev_event_link( '<span>&laquo;</span> %title%  <span>&laquo;</span>') ?></li>

                                  <li class="tribe-events-nav-next"(Next (known bug not working)  <?php tribe_the_next_event_link( '<span>&raquo;</span> %title%  <span>&raquo;</span>'  ) ?></li>
     -->
              </ul>

              <!-- .tribe-events-sub-nav -->
              </nav><hr/>
              </div>

<div id="tribe-events-content" class="tribe-events-single">                                

                                                                    
<?php $thetitle= str_replace("2nd","<em> 2nd </em>",
                                                                    the_title( '<h1 class="tribe-events-single-event-title">', '</h1>' ));
   $paperid =  conf_get_paperid($eventid);
   $authors=conf_get_authors($eventid);
                                                                    if(strlen($authors)>2){
                                                                      echo' <div class="Authors" style="font-size:24px; padding:8px"> Authors: '. $authors . '</div>';
                                                                    echo get_favorites_button($eventid)  ;
                                                                    echo "&nbsp;&nbsp;".Insert_paper_links($paperid, 'paper label');
                                                                    } ?>

                                  
                               <div class="tribe-events-schedule tribe-clearfix">
               <?php
                               if(!$is_async & !$is_keynote && (strlen($authors)>2)){ 
                                      echo $thetitle=tribe_events_event_schedule_details( $eventid, '<h2>  Authors in live Q&A session at:    ', '</h2>' );
                               } else if($is_keynote){
                                      echo $thetitle=tribe_events_event_schedule_details( $eventid, '<h2>  Keynote session at:    ', '</h2>' );
                               }
                     ?>
                    <?php
                                                                    //                                                                    $ptime=live_simple_clock("Current  PDT time:",0);                                   
                                                                    //                                                                    echo "&nbsp;(".$ptime.")&nbsp;&nbsp;";
                                                                    
                                                                    echo '<a href="' . \Tribe__Events__Main::instance()->esc_gcal_url( tribe_get_gcal_link() ) . '" title="' . esc_attr__( 'Add to Google Calendar', 'the-events-calendar' ) . '" class="tribe-events-gcal tribe-events-button"> ' . esc_html__( ' &nbsp;  &nbsp;  + Google Calendar &nbsp;  &nbsp;  &nbsp;  &nbsp;', 'the-events-calendar' ) . '</a>';
                                                                    //                                                                    echo '<a href="' . esc_url( tribe_get_single_ical_link() ) . '" title="' . esc_attr__( 'Download .ics file', 'the-events-calendar' ) . '"   class="tribe-events-ical tribe-events-button">+ ' . esc_html__( 'iCal Export', 'the-events-calendar' ) . '</a>';
                               
                                      
                     $keywords=  conf_get_keywords($eventid);
                     if(strlen($keywords)>2) {
                         echo '<div class="keywords" style="font-size:16px" >Keywords: '.$keywords.'</div> </br>
                                  </div>';
                     } ?>
                     

                               <!-- Event header -->
  <!-- Navigation
                                                                    <div id="tribe-events-header" <?php tribe_events_the_header_attributes() ?>>


                               <nav class="tribe-events-nav-pagination" aria-label="<?php printf( esc_html__( '%s Navigation', 'the-events-calendar' ), $events_label_singular ); ?>">
                               <ul class="tribe-events-sub-nav">
                               <li class="tribe-events-nav-previous"><?php tribe_the_prev_event_link( '<span>&laquo;</span> %title%' ) ?></li>
                                                                    <li class="tribe-events-nav-next <?php tribe_the_next_event_link( '<span>&raquo;</span> %title%  <span>&raquo;</span>'  ) ?></li>
                               </ul>
                               </nav>

                               </div>
-->                               
                               <!-- #tribe-events-header -->



<?php Insert_event_URL() ?>                                     

                                   
<?php   $has_content = $is_poster || $is_oral || $is_live || $is_keynote || $is_net;  ?>
<?php        while ( have_posts()    &&  $has_content) : the_post(); ?>
                               <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <?php
        if($is_poster){  echo"         <h2>  Poster Talk and slides </h2>"; }
        else   if($is_oral){ echo"            <h2>  Oral Talk and slides </h2>";}
//        else   if($is_keynote){ echo"            <h2>  Keynote Talk </h2>";}
        else   if($is_async){ echo"            <h2>  Asyncronous  Vidoe and slides -- no vidoe Q&A </h2>";}
        else   if($is_live){ echo"            <div class=vchat> <h2>  Live talk using video chat";
                 echo Insert_paper_videochat($paperid, 'video chat label');
                 echo " </h2></div>";
        } 
          
        echo Insert_paper_video($paperid); 
           echo Insert_paper_slides($paperid);
         if(!$is_keynote)           echo  Insert_QA();
         if($is_oral  || $is_poster || $is_net){ echo  Insert_paper_videochat($paperid, 'video chat label');    }
         if($is_net) {
//          echo Insert_paper_textchat($paperid, 'Open Discussion about topic');
        } 


$abs = get_the_content("",false, $eventid);
         if(strlen($abs)  >2){
             if(!$is_keynote) echo '      <h3>  Abstract </h3>            <!-- Event content -->';
             do_action( 'tribe_events_single_event_before_the_content' );
             echo '           <div class="tribe-events-single-event-description tribe-events-content">'; 
             the_content();
             echo '
           </div>
           <!-- .tribe-events-single-event-description -->
           ';
             do_action( 'tribe_events_single_event_after_the_content' );
         if(!$is_keynote) {
             echo Insert_paper_textchat($paperid, 'Open Discussion (note keynote presenters are not present for discussion');
         }  
               }
?>
       
<?php endwhile ?>



                               <!-- normal single-page  -->

<?php while ( !  $has_content && have_posts() ) :  the_post(); ?>
          <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
              <!-- Event featured image, but exclude link -->
<?php echo tribe_event_featured_image( $eventid, 'full', false ); ?>
<?php 
                               $paperid =  conf_get_paperid($eventid);
                      Insert_paper_video($paperid);
                      Insert_paper_slides($paperid);
?>

                                     

              <!-- Event content -->
<?php do_action( 'tribe_events_single_event_before_the_content' ) ?>
              <div class="tribe-events-single-event-description tribe-events-content">
<?php the_content(); ?>
              </div>
              <!-- .tribe-events-single-event-description -->
<?php do_action( 'tribe_events_single_event_after_the_content' ) ?>


<?php echo  Insert_QA();                      ?>
                          
              <!-- Skip Event meta 
<?php do_action( 'tribe_events_single_event_before_the_meta' ) ?>
<?php tribe_get_template_part( 'modules/meta' ); ?>
<?php do_action( 'tribe_events_single_event_after_the_meta' ) ?>      -->
              </div> <!-- #post-x -->
<?php if ( get_post_type() == Tribe__Events__Main::POSTTYPE && tribe_get_option( 'showComments', false ) ) echo comments_template() ?>                                     
<?php endwhile; ?>




              <!-- Event footer -->
              <div id="tribe-events-footer">
   <!-- Navigation 

              <nav class="tribe-events-nav-pagination" aria-label="<?php printf( esc_html__( '%s Navigation', 'the-events-calendar' ), $events_label_singular ); ?>">
            <ul class="tribe-events-sub-nav">
              <li class="tribe-events-nav-previous"><?php tribe_the_prev_event_link( '<span>&laquo;</span> %title%' ) ?></li>
              <li class="tribe-events-nav-next"><?php tribe_the_next_event_link( '%title% <span>&raquo;</span>' ) ?></li>
              </ul>
              </nav>
-->              
              </div>
              <!-- #tribe-events-footer -->


              </div><!-- #tribe-events-content -->
