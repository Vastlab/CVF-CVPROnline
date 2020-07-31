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

$events_label_singular = tribe_get_event_label_singular();
$events_label_plural   = tribe_get_event_label_plural();

$event_id = get_the_ID();

$post_id    = is_null( $post_id ) ? get_the_ID() : $post_id;
$defaults   = array(
	'echo'         => false,
	'label'        => null,
	'label_before' => '<div>',
	'label_after'  => '</div>',
	'wrap_before'  => '<ul class="tribe-event-categories">',
	'wrap_after'   => '</ul>',
);
$args       = wp_parse_args( $args, $defaults );
$categories = tribe_get_event_taxonomy( $post_id, $args );
echo $categories;
$is_poster=false;
if(strpos($mystring, 'Poster ') !== false){
	$is_poster=true;
}
$is_oral=false;
if(strpos($mystring, 'Oral ') !== false){
	$is_oral=true;
}
?>

<div id="tribe-events-content" class="tribe-events-single">
<?php the_title( '<h1 class="tribe-events-single-event-title">', '</h1>' ); ?>

<div class="tribe-events-schedule tribe-clearfix">
<?php echo tribe_events_event_schedule_details( $event_id, '<h2>', '</h2>' ); ?>
<?php if ( tribe_get_cost() ) : ?>
PaperID
<?php   $paperid =  str_replace("$","",tribe_get_cost( null, true )); echo $paperid?>

<?php endif; ?>
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



<?php if ( $is_poster)   ?>  

<?php while ( have_posts() ) :  the_post(); ?>
<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<!-- Skip Event featured image 
<?php echo tribe_event_featured_image( $event_id, 'full', false ); ?>  --> 



<div style="width: 95%;" class="wp-video"><!--[if lt IE 9]><script>document.createElement('video');</script><![endif]-->
<video class="wp-video-shortcode" id="video-4186-1" width="95%" height="95%" preload="metadata" controls="controls"><source type="video/mp4" src=
<?php $postvid = '"http://video.vast.uccs.edu/CVPR20/CVPR20/'.$paperid."/".$paperid."-1min.mp4"; echo $postvid;  ?>" /><a href="<?php echo $postvid; ?></a></video></div>




<!-- Event content -->
<?php do_action( 'tribe_events_single_event_before_the_content' ) ?>
<div class="tribe-events-single-event-description tribe-events-content">
<?php the_content(); ?>
</div>
<!-- .tribe-events-single-event-description -->
<?php do_action( 'tribe_events_single_event_after_the_content' ) ?>

<!-- Event meta -->
<?php do_action( 'tribe_events_single_event_before_the_meta' ) ?>
<?php tribe_get_template_part( 'modules/meta' ); ?>
<?php do_action( 'tribe_events_single_event_after_the_meta' ) ?>
</div> <!-- #post-x -->
<?php if ( get_post_type() == Tribe__Events__Main::POSTTYPE && tribe_get_option( 'showComments', false ) ) comments_template() ?>
<?php endwhile ?>

<?php  endif;  ?>  
<?php   if ( $is_oral)  ?>


<?php while ( have_posts() ) :  the_post(); ?>
<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<!-- Skip Event featured image 
<?php echo tribe_event_featured_image( $event_id, 'full', false ); ?>  --> 



<div style="width: 95%;" class="wp-video"><!--[if lt IE 9]><script>document.createElement('video');</script><![endif]-->
<video class="wp-video-shortcode" id="video-4186-1" width="95%" height="95%" preload="metadata" controls="controls"><source type="video/mp4" src=
<?php $postvid = '"http://video.vast.uccs.edu/CVPR20/CVPR20/'.$paperid."/".$paperid."-oral.mp4"; echo $postvid;  ?>" /><a href="<?php echo $postvid; ?></a></video></div>




<!-- Event content -->
<?php do_action( 'tribe_events_single_event_before_the_content' ) ?>
<div class="tribe-events-single-event-description tribe-events-content">
<?php the_content(); ?>
</div>
<!-- .tribe-events-single-event-description -->
<?php do_action( 'tribe_events_single_event_after_the_content' ) ?>

<!-- Event meta -->
<?php do_action( 'tribe_events_single_event_before_the_meta' ) ?>
<?php tribe_get_template_part( 'modules/meta' ); ?>
<?php do_action( 'tribe_events_single_event_after_the_meta' ) ?>
</div> <!-- #post-x -->
<?php if ( get_post_type() == Tribe__Events__Main::POSTTYPE && tribe_get_option( 'showComments', false ) ) comments_template(); ?>
<?php endwhile ?>


<?php  endif; ?>
<? php if (!  ($is_oral || $is_poster)) { ?>      
	<!-- normal single-page  -->

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

		<?php while ( have_posts() ) :  the_post(); ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<!-- Event featured image, but exclude link -->
		<?php echo tribe_event_featured_image( $event_id, 'full', false ); ?>

		<!-- Event content -->
		<?php do_action( 'tribe_events_single_event_before_the_content' ) ?>
		<div class="tribe-events-single-event-description tribe-events-content">
		<?php the_content(); ?>
		</div>
		<!-- .tribe-events-single-event-description -->
		<?php do_action( 'tribe_events_single_event_after_the_content' ) ?>

		<!-- Event meta -->
		<?php do_action( 'tribe_events_single_event_before_the_meta' ) ?>
		<?php tribe_get_template_part( 'modules/meta' ); ?>
		<?php do_action( 'tribe_events_single_event_after_the_meta' ) ?>
		</div> <!-- #post-x -->
		<?php if ( get_post_type() == Tribe__Events__Main::POSTTYPE && tribe_get_option( 'showComments', false ) ) comments_template() ?>
		<?php endwhile; ?>

<?php  endif; >?  


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
