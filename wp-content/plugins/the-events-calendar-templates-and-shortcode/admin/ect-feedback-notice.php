<?php

if (!class_exists('ECTProFeedbackNotice')) {
    class ECTProFeedbackNotice {
        /**
         * The Constructor
         */
        public function __construct() {
            // register actions
         
            if(is_admin()){
                add_action( 'admin_notices',array($this,'admin_notice_for_reviews'));
               
                add_action( 'wp_ajax_ect_dismiss_notice',array($this,'ect_dismiss_review_notice' ) );
            }
        }

   
    // ajax callback for review notice
    public function ect_dismiss_review_notice(){
        $rs=update_option( 'ect-ratingDiv','yes' );
        echo  json_encode( array("success"=>"true") );
        exit;
    }
   // admin notice  
    public function admin_notice_for_reviews(){

        if( !current_user_can( 'update_plugins' ) ){
            return;
         }
         // get installation dates and rated settings
         $installation_date = get_option( 'ect-installDate' );
         $alreadyRated =get_option( 'ect-ratingDiv' )!=false?get_option( 'ect-ratingDiv'):"no";

         // check user already rated 
         if( $alreadyRated=="yes") {
              return;
            }

            // grab plugin installation date and compare it with current date
            $display_date = date( 'Y-m-d h:i:s' );
            $install_date= new DateTime( $installation_date );
            $current_date = new DateTime( $display_date );
            $difference = $install_date->diff($current_date);
            $diff_days= $difference->days;
          
            // check if installation days is greator then week
            if (isset($diff_days) && $diff_days>=3) {
                echo $this->create_notice_content();
               }
       }  

       // generated review notice HTML
       function create_notice_content(){
        $ajax_url=admin_url( 'admin-ajax.php' );
        $ajax_callback='ect_dismiss_notice';
        $wrap_cls="notice notice-info is-dismissible";
        $img_path=ECT_PRO_PLUGIN_URL.'assets/images/ect-icon.png';
        $p_name="The Events Calendar Shortcode & Templates PRO";
        $like_it_text='Rate Now! ★★★★★';
        $already_rated_text=esc_html__( 'I already rated it', 'ect' );
        $not_interested=esc_html__( 'Not Interested', 'ect' );
        $not_like_it_text=esc_html__( 'No, not good enough, i do not like to rate it!', 'ect' );
        $p_link=esc_url('https://1.envato.market/calendarReviews');
        $message="We hope our addon helped you to represent <strong>the events calendar</strong> in an awesome design! Please share a few minutes from your valuable time to add a review on <a href='https://1.envato.market/calendarReviews' target='_blank'><strong>codecanyon</strong></a> or <a href='https://wordpress.org/support/plugin/template-events-calendar/reviews/?filter=5' target='_blank'><strong>WP.org</strong></a>, it works as a boost for us to keep working on more <a href='https://coolplugins.net' target='_blank'><strong>Cool Plugins</strong></a> & addons for the events calendar.<br/>";
        // $dont_want_rate=esc_html__( "Close It, I don't Want to Rate", 'ect' ); 
        $html='<div data-ajax-url="%8$s"  data-ajax-callback="%9$s" class="ect-feedback-notice-wrapper %1$s">
        <div class="logo_container"><a href="%5$s"><img src="%2$s" alt="%3$s"></a></div>
        <div class="message_container">%4$s
        <div class="callto_action">
        <ul>
            <li class="love_it"><a href="%5$s" class="like_it_btn button button-primary" target="_new" title="%6$s">%6$s</a></li>
            <li class="already_rated"><a href="javascript:void(0);" class="already_rated_btn button ect_dismiss_notice" title="Already Rated - Close This Box!">%7$s</a></li>
            <li class="already_rated"><a href="javascript:void(0);" class="already_rated_btn button ect_dismiss_notice" title="Not Interested - Close This Box!">%10$s</a></li>     
        </ul>
        <div class="clrfix"></div>
        </div>
        </div>
        </div>';
        $inline_css='<style>.ect-feedback-notice-wrapper.notice.notice-info.is-dismissible {
            padding: 5px;
            display: table;
            width: 100%;
            max-width: 820px;
            clear: both;
            border-radius: 5px;
            border: 2px solid #b7bfc7;
        }
        .ect-feedback-notice-wrapper .logo_container {
            width: 100px;
            display: table-cell;
            padding: 5px;
            vertical-align: middle;
        }
        .ect-feedback-notice-wrapper .logo_container a,
        .ect-feedback-notice-wrapper .logo_container img {
            width:100%;
            height:auto;
            display:inline-block;
        }
        .ect-feedback-notice-wrapper .message_container {
            display: table-cell;
            padding: 5px 20px 5px 5px;
            vertical-align: middle;
        }
        .ect-feedback-notice-wrapper ul li {
            float: left;
            margin: 0px 10px 0 0;
        }
        .ect-feedback-notice-wrapper ul li.already_rated a:before {
            color: #f12945;
            content: "\f153";
            font: normal 18px/22px dashicons;
            display: inline-block;
            vertical-align: middle;
            margin-right: 3px;
        }
        .ect-feedback-notice-wrapper ul li .button-primary {
            background: #008bff;
        }
        .ect-feedback-notice-wrapper ul li .button-primary:hover {
            background: #0f1031;
            border-color: transparent;
        }
        .ect-feedback-notice-wrapper a {
            color: #008bff;
        }
        
        /* This css is for license registration page */
        .ect-notice-red.uninstall {
            max-width: 700px;
            display: block;
            padding: 8px;
            border: 2px solid #157d0f;
            margin: 10px 0;
            background: #13a50b;
            font-weight: bold;
            font-size: 13px;
            color: #ffffff;
        }
        .clrfix{
            clear:both;
        }</style>';
        $inline_js="<script>jQuery(document).ready(function ($) {
            $('.ect_dismiss_notice').on('click', function (event) {
                var thisE = $(this);
                var wrapper=thisE.parents('.ect-feedback-notice-wrapper');
                var ajaxURL=wrapper.data('ajax-url');
                var ajaxCallback=wrapper.data('ajax-callback');
                $.post(ajaxURL, { 'action':ajaxCallback }, function( data ) {
                    wrapper.slideUp('fast');
                  }, 'json');
            });
        });</script>";
 $output =  sprintf($html,
        $wrap_cls,
        $img_path,
        $p_name,
        $message,
        $p_link,
        $like_it_text,
        $already_rated_text,
        $ajax_url,// 8
        $ajax_callback,//9
        // $dont_want_rate,
        $not_interested
        );
        $output.=$inline_css. ' '.$inline_js;
        return $output;
       }

    } //class end

} 



