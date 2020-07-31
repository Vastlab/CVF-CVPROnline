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




/**
 * Outputs all WP post meta fields (except those prefixed "_"), feel
 * free to tweak the formatting!   */


function show_wp_custom_fields() {
    foreach ( get_post_meta( get_the_ID() ) as $field => $value ) {
         $field = trim( $field );
         if ( is_array( $value ) ) $value = implode( ', ', $value );
         //        if ( 0 === strpos( $field, '_' ) ) continue; // Don't expose "private" fields
         echo '<strong>' . esc_html( $field ) . '</strong> =  ' . esc_html( $value ) . '<br/>';
     }
 }


/**
 * Add support for DISCUZ comments plugin
 */
function my_wpdiscuz_shortcode() {
    if (file_exists(ABSPATH . 'wp-content/plugins/wpdiscuz/templates/comment/comment-form.php')) {
        ob_start();
        include_once ABSPATH . 'wp-content/plugins/wpdiscuz/templates/comment/comment-form.php';
        return ob_get_clean();
    }
}
add_shortcode('wpdiscuz_comments', 'my_wpdiscuz_shortcode');


$eventid=get_the_ID();
function  oldInsert_paper_video($paperid=0, $suffix = ''){
    global $conf_fileprefix;
    global $eventid;
    $postvid = $conf_fileprefix.conf_get_directory($eventid).'/'.$paperid."/".$paperid.$suffix;
    $tstring=conf_get_directory($eventid).'/'.$paperid."/".$paperid.$suffix    ;


    $output = '<div style="width: 95%;" class="wp-video" class="cvf-video"><!--[if lt IE 9]><script>document.createElement(\'video\');</script><![endif]-->
           <video class="wp-video-shortcode" id="video-'.$postvid.'" width="95%" height="95%" preload="metadata" controls="controls"><source type="video/mp4" src="'.$postvid.'"/><a href="'.$postvid.'"</a></video></div>"';
    return $output;
            
}

function  oldInsert_paper_slides($paperid=0, $suffix= ''){
    global $conf_fileprefix;
    global $eventid;
    $postslides = $conf_fileprefix.conf_get_directory($eventid)."/".$paperid."/".$paperid.$suffix;
    //        echo " <h2> <a href=".$postslides."> Slide link for download <a></h2>";

     $shortc ='[pdfjs-viewer url='.$postslides.' viewer_width=90% viewer_height=700px fullscreen=true download=true print=true]';

echo do_shortcode($shortc);
}


$eventid=get_the_ID();
function Insert_paper_video($paperid=0){
    global $conf_fileprefix;
    global $eventid;
    $fbase = conf_get_directory($eventid)."/".$paperid."/".$paperid;
    if(file_exists($fbase."-oral.mp4")){         $postvid= $fbase."-oral.mp4";}
    else if(file_exists($fbase."-1min.mp4")){         $postvid= $fbase."-1min.mp4";}
    else if(file_exists($fbase."-poster.mp4")){         $postvid= $fbase."-poster.mp4";}
    else if(file_exists($fbase."-keynote.mp4")){         $postvid= $fbase."-keynote.mp4";}
    if(strlen($postvid)>0)  {
        $postvid = $conf_fileprefix.$postvid;
        $output = '<div style="width: 95%;" class="wp-video" class="cvf-video"><!--[if lt IE 9]><script>document.createElement(\'video\');</script><![endif]-->
                                <video class="wp-video-shortcode" id="video-'.$postvid.'" width="95%" height="95%" preload="metadata" controls="controls"><source type="video/mp4" src="'.$postvid.'"/><a href="'.$postvid.'"</a></video></div>"';
    } else  {
        $postvid = $conf_fileprefix.$postvid;
        $output = "<p> No video found at ".$postvid." at base ".$fbase." </p> ";
        }
    echo $output;
}

/* load by searching for  files that might be used */  

function  Insert_paper_slides($paperid=0){
    global $conf_fileprefix;
    global $eventid;
    $postslides="";
    $fbase =conf_get_directory($eventid)."/".$paperid."/".$paperid;    
    if(file_exists($fbase."-talk.pdf")){         $postslides= $fbase."-talk.pdf";}
    else if(file_exists($fbase."-slides.pdf")){         $postslides= $fbase."-slides.pdf";}
    else if(file_exists($fbase."-poster.pdf")){         $postslides= $fbase."-poster.pdf";}
    if(strlen($postslides)>0)  {
        $postslides = $conf_fileprefix.$postslides;        
        $shortc ='[pdfjs-viewer url='.$postslides.' viewer_width=100% viewer_height=750px fullscreen=true download=true print=true]';
        echo do_shortcode($shortc);}
    else {
        $postslides = $conf_fileprefix.$postslides;        
        echo "<p> No Slides to display at  ".$postslides." at base ".$fbase." </p>"; 
    }
}




function  Insert_paper_videochat($paperid=0, $label = ''){
$shortc ='[zoom_api_link meeting_id="79397434591" link_only="no"]';
echo do_shortcode($shortc);
}


function  Insert_paper_textchat($paperid=0, $label = ''){
    $channel = conf_get_directory($eventid)."-".$paperid;
echo $output= do_shortcode('[wise-chat chat_height=400px channel="'.$channel.'"]');
}


function  Insert_paper_links($paperid=0, $label = ''){

$output = '<h2> link to open access go here label='.$label.' </h2>';
return $output;
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
if(strpos($categories, 'Poster') !== false){
    $is_poster=true; //echo "IS POSTER";
}
$is_oral=false;
if(strpos($categories, 'Oral') !== false){
    $is_oral=true; //echo "IS ORAL";
}
$is_live=false;
if(strpos($categories, 'Live') !== false){
    $is_live=true; 
}

$is_keynote=false;
if(strpos($categories, 'Keynote') !== false){
    $is_keynote=true; 
}

//echo  clear_queues_and_redirect();


//echo delete_all_events();

// aggressive stop to import queues. 
//echo clear_queues_and_redirect();

// debuging info
//echo $categories;
// to show all details of added fields, uncoment this
//echo show_wp_custom_fields();

?>

              <div id="tribe-events-footer">
              <!-- Navigation -->
              <nav class="tribe-events-nav-pagination" aria-label="<?php printf( esc_html__( '%s Navigation', 'the-events-calendar' ), $events_label_singular ); ?>">
              <ul class="tribe-events-sub-nav">
                                <li class="tribe-events-nav-previous">Prev:<?php tribe_the_prev_event_link( '<span>&laquo;</span> %title%  <span>&laquo;</span>') ?></li>
                                <li class="tribe-events-nav-next">Next:<?php tribe_the_next_event_link( '<span>&raquo;</span> %title%  <span>&raquo;</span>'  ) ?></li>
              </ul>
              <!-- .tribe-events-sub-nav -->
              </nav><hr/>
              </div>

<div id="tribe-events-content" class="tribe-events-single">                                

<?php $thetitle= str_replace("2nd","<em> 2nd </em>", the_title( '<h1 class="tribe-events-single-event-title">', '</h1>' ));   ?>
<?php   $paperid =  conf_get_paperid($eventid);  $authors=conf_get_authors($eventid);  ?>
                                 <div class="Authors" style="font-size:24px"> Authors:  <?php echo $authors ?> </div>
                               <div class="tribe-events-schedule tribe-clearfix">
               <?php echo $thetitle=tribe_events_event_schedule_details( $eventid, '<h2>  Authors in live Q&A session at:    ', '</h2>' );
                                /*                                if(($tval=strpos($thetitle,"2nd")) === false){
                                    echo  "=".$tval." and again 12 hours later </h2>";
                                }else {
                                    echo "=".$tval."sttrpos</h2>";
                                    };*/ 
                     ?>
                    <?php 
                    echo '<a href="' . \Tribe__Events__Main::instance()->esc_gcal_url( tribe_get_gcal_link() ) . '" title="' . esc_attr__( 'Add to Google Calendar', 'the-events-calendar' ) . '" class="tribe-events-gcal tribe-events-button"> ' . esc_html__( ' &nbsp;  &nbsp;  + Google Calendar &nbsp;  &nbsp;  &nbsp;  &nbsp;', 'the-events-calendar' ) . '</a>';
                    echo '<a href="' . esc_url( tribe_get_single_ical_link() ) . '" title="' . esc_attr__( 'Download .ics file', 'the-events-calendar' ) . '"   class="tribe-events-ical tribe-events-button">+ ' . esc_html__( 'iCal Export', 'the-events-calendar' ) . '</a>'; ?>
               <div class="keywords" style="font-size:16px" >Keywords: <?php echo conf_get_keywords($eventid); ?></div> </br>
                </div>

                               <!-- Event header -->
                               <div id="tribe-events-header" <?php tribe_events_the_header_attributes() ?>>
                               <!-- Navigation -->
                               <nav class="tribe-events-nav-pagination" aria-label="<?php printf( esc_html__( '%s Navigation', 'the-events-calendar' ), $events_label_singular ); ?>">
                               <ul class="tribe-events-sub-nav">
                               <li class="tribe-events-nav-previous"><?php tribe_the_prev_event_link( '<span>&laquo;</span> %title%' ) ?></li>
                               <li class="tribe-events-nav-next"><?php tribe_the_next_event_link( '%title% <span>&raquo;</span>' ) ?></li>
                               </ul>
                               <!-- .tribe-events-sub-nav -->
                               </nav>
                               </div>
                               <!-- #tribe-events-header -->


                                   
<?php   $has_content = $is_poster || $is_oral || $is_live || $is_keynote;  ?>
<?php        while ( have_posts()    &&  $has_content) : the_post(); ?>
                               <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <?php
        if($is_poster){  echo"         <h2>  Poster Talk and slides </h2>"; }
        else   if($is_oral){ echo"            <h2>  Oral Talk and slides </h2>";}
        else   if($is_live){ echo"            <h2>  Live talk using video chat </h2>";}
        else   if($is_keynote){ echo"            <h2>  Keynote Talk and slides </h2>";}


           echo Insert_paper_video($paperid); 
           echo Insert_paper_slides($paperid);
           echo do_shortcode('[wpdiscuz_comments] ');  ?>

           <div class="Authors" style="font-size:24px"> Look in chat for the authors:  <?php echo $authors ?> </div>
                                     
           <div class="row chatrow">
           <div class="column chatcolumn" style="width:65%;box-sizing:border-box;    display:inline-block  "  >
       <?php    echo Insert_paper_textchat($paperid, 'web chat label');?>
           </div>
           <div class="colun vchatcolumn" style="width:25%; ;box-sizing:border-box;    display:inline-block">
       <?php    echo Insert_paper_videochat($paperid, 'video chat label'); ?>
           </div>                                 
           </div>


           <h2>  Paper Links </h2>
       <?php    echo Insert_paper_links($paperid, 'paper label');?>
    
    <h3>  Abstract </h3>
           <!-- Event content -->
       <?php do_action( 'tribe_events_single_event_before_the_content' ) ?>
           <div class="tribe-events-single-event-description tribe-events-content">
       <?php the_content(); ?>
           </div>
           <!-- .tribe-events-single-event-description -->
       <?php do_action( 'tribe_events_single_event_after_the_content' ) ?>
       
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


<h2> Q&A with voting. Asyncronous or live so authors see most requested questions during video Q&A </h2>                                     
<?php echo do_shortcode('[wpdiscuz_comments] ');  ?>
                          
              <!-- Skip Event meta 
<?php do_action( 'tribe_events_single_event_before_the_meta' ) ?>
<?php tribe_get_template_part( 'modules/meta' ); ?>
<?php do_action( 'tribe_events_single_event_after_the_meta' ) ?>      -->
              </div> <!-- #post-x -->
<?php if ( get_post_type() == Tribe__Events__Main::POSTTYPE && tribe_get_option( 'showComments', false ) ) echo comments_template() ?>                                     
<?php endwhile; ?>




              <!-- Event footer -->
              <div id="tribe-events-footer">
              <!-- Navigation -->
              <nav class="tribe-events-nav-pagination" aria-label="<?php printf( esc_html__( '%s Navigation', 'the-events-calendar' ), $events_label_singular ); ?>">
              <ul class="tribe-events-sub-nav">
              <li class="tribe-events-nav-previous"><?php tribe_the_prev_event_link( '<span>&laquo;</span> %title%' ) ?></li>
              <li class="tribe-events-nav-next"><?php tribe_the_next_event_link( '%title% <span>&raquo;</span>' ) ?></li>
              </ul>
              <!-- .tribe-events-sub-nav -->
              </nav>
              </div>
              <!-- #tribe-events-footer -->


              </div><!-- #tribe-events-content -->
