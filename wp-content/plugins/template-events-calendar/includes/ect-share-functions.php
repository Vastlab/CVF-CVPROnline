<?php
/**
 * This file is used to share events.
 * 
 * @package the-events-calendar-templates-and-shortcode/includes
 */

function ect_share_button($event_id){
  

  $ect_sharecontent = '';
  $ect_geturl = urlencode(get_permalink($event_id));
  //$ect_geturl = get_permalink($event_id);
  $ect_gettitle = htmlspecialchars(urlencode(html_entity_decode(get_the_title($event_id), ENT_COMPAT, 'UTF-8')), ENT_COMPAT, 'UTF-8');
  $ect_getthumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $event_id ), 'full' );
  $subject= str_replace("+"," ",$ect_gettitle);
  // Construct sharing URL
    $ect_twitterURL = 'https://twitter.com/intent/tweet?text='.$ect_gettitle.'&amp;url='.$ect_geturl.'';
    $ect_whatsappURL = 'https://wa.me/?text='.$ect_gettitle . ' ' . $ect_geturl;
    $ect_facebookurl = 'https://www.facebook.com/sharer/sharer.php?u='.$ect_geturl.'';
    $ect_emailUrl = 'mailto:?Subject='.$subject.'&Body='.$ect_geturl.'';
    //$ect_linkedinUrl = "https://www.linkedin.com/sharing/share-offsite/?mini=true&amp;url=$ect_geturl";
    $ect_linkedinUrl = "http://www.linkedin.com/shareArticle?mini=true&amp;url=$ect_geturl";
    // Add sharing button at the end of page/page content
    $ect_sharecontent .= '<div class="ect-share-wrapper">';
    $ect_sharecontent .= '<i class="ect-icon-share"></i>';
    $ect_sharecontent .= '<div class="ect-social-share-list">';
    $ect_sharecontent .= '<a class="ect-share-link" href="'.$ect_facebookurl.'" target="_blank" title="Facebook" aria-haspopup="true"><i class="ect-icon-facebook"></i></a>';
    $ect_sharecontent .= '<a class="ect-share-link" href="'.$ect_twitterURL.'" target="_blank" title="Twitter" aria-haspopup="true"><i class="ect-icon-twitter"></i></a>';
    $ect_sharecontent .= '<a class="ect-share-link" href="'.$ect_linkedinUrl.'" target="_blank" title="Linkedin" aria-haspopup="true"><i class="ect-icon-linkedin"></i></a>';
    $ect_sharecontent .= '<a class="ect-email" href="'.$ect_emailUrl.' "title="Email" aria-haspopup="true"><i class="ect-icon-mail"></i></a>';
    $ect_sharecontent .= '<a class="ect-share-link" href="'.$ect_whatsappURL.'" target="_blank" title="WhatsApp" aria-haspopup="true"><i class="ect-icon-whatsapp"></i></a>';
    $ect_sharecontent .= '</div></div>';
        return $ect_sharecontent;
}
