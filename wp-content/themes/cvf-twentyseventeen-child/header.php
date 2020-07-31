<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since Twenty Seventeen 1.0
 * @version 1.0
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'twentyseventeen' ); ?></a>
	<header id="masthead" class="site-header container-fluid fixed-top" role="banner">
		<div class="row">
			<div class="col d-md-flex align-items-center">
				<?php custom_navbar_brand(); ?>
				<?php get_template_part( 'template-parts/navigation/navigation', 'top' ); ?>
			</div>
		</div>
	</header>



	<?php if ( is_home() || is_front_page() ): ?>
	<div class="hero container-fluid">
		<div class="row">
			<div class="col-sm-3">
<?php if( !is_user_logged_in()){echo ' <a href="/wp-login.php"    style="font-size:38px;color=blue" class=loginlink> Click Here to Login </a>';}?>    
				<div class="inner-col-content">

					<p>CVPR is the premier annual computer vision event comprising the main conference and several co-located workshops and short courses. With its high quality and low cost, it provides an exceptional value for students, academics and industry researchers.</p>

					<div class="site-footer__social text-center">
						<a href="https://www.facebook.com/CVPR-2020-460123958071182/" target="_blank"><i class="icon-facebook"></i></a>
						<a href="https://twitter.com/CVPR" target="_blank"><i class="icon-twitter"></i></a>
						<a href="https://www.instagram.com/cvfcvpr/" target="_blank"><i class="icon-instagram"></i></a>
					</div>
				</div>
			</div>
			<div class="col-sm-9" style="background:url(http://cvpr20.com/wp-content/uploads/2020/05/cropped-CVPRVirtual-Squareish-600.jpg) no-repeat; background-size: cover; background-position: center;">
			</div>
		</div>
	</div>
	<?php endif; ?>






<?php

/*
 * If a regular post or page, and not the front page, show the featured image.
 * Using get_queried_object_id() here since the $post global may not be set before a call to the_post().
 */
if ( ( is_single() || ( is_page() && ! twentyseventeen_is_frontpage() ) ) && has_post_thumbnail( get_queried_object_id() ) ) :
	echo '<div class="single-featured-image-header">';
echo get_the_post_thumbnail( get_queried_object_id(), 'twentyseventeen-featured-image' ); 
echo '</div>  <!-- .single-featured-image-header -->';
endif;
?>
	<div class="site-content-contain">
		<div id="content" class="site-content">
