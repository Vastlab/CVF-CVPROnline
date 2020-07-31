<?php
/**
 * Add solwin news dashboard
 */
if (!function_exists('ublk_latest_news_solwin_feed')) {

    function ublk_latest_news_solwin_feed() {
        // Register the new dashboard widget with the 'wp_dashboard_setup' action
        add_action('wp_dashboard_setup', 'ublk_solwin_latest_news_with_product_details');
        if (!function_exists('ublk_solwin_latest_news_with_product_details')) {

            function ublk_solwin_latest_news_with_product_details() {
                add_screen_option('layout_columns', array('max' => 3, 'default' => 2));
                add_meta_box('wp_blog_designer_dashboard_widget', __('News From Solwin Infotech', 'user-blocker'), 'ublk_solwin_dashboard_widget_news', 'dashboard', 'normal', 'high');
            }

        }
        if (!function_exists('ublk_solwin_dashboard_widget_news')) {

            function ublk_solwin_dashboard_widget_news() {
                echo '<div class="rss-widget">'
                . '<div class="solwin-news"><p><strong>' . __('Solwin Infotech News', 'user-blocker') . '</strong></p>';
                wp_widget_rss_output(array(
                    'url' => 'https://www.solwininfotech.com/feed/',
                    'title' => __('News From Solwin Infotech', 'user-blocker'),
                    'items' => 5,
                    'show_summary' => 0,
                    'show_author' => 0,
                    'show_date' => 1
                ));
                echo '</div>';
                $title = $link = $thumbnail = "";
                //get Latest product detail from xml file
                $file = 'https://www.solwininfotech.com/documents/assets/latest_product.xml';
                echo '<div class="display-product">'
                . '<div class="product-detail"><p><strong>' . __('Latest Product', 'user-blocker') . '</strong></p>';
                $response = wp_remote_post($file);
                if (is_wp_error($response)) {
                    $error_message = $response->get_error_message();
                    echo "<p>" . __('Something went wrong', 'user-blocker') . " : $error_message" . "</p>";
                } else {
                    $body = wp_remote_retrieve_body($response);
                    $xml = simplexml_load_string($body);
                    $title = $xml->item->name;
                    $thumbnail = $xml->item->img;
                    $link = $xml->item->link;
                    $allProducttext = $xml->item->viewalltext;
                    $allProductlink = $xml->item->viewalllink;
                    $moretext = $xml->item->moretext;
                    $needsupporttext = $xml->item->needsupporttext;
                    $needsupportlink = $xml->item->needsupportlink;
                    $customservicetext = $xml->item->customservicetext;
                    $customservicelink = $xml->item->customservicelink;
                    $joinproductclubtext = $xml->item->joinproductclubtext;
                    $joinproductclublink = $xml->item->joinproductclublink;
                    echo '<div class="product-name"><a href="' . $link . '" target="_blank">'
                    . '<img alt="' . $title . '" src="' . $thumbnail . '"> </a>'
                    . '<a href="' . $link . '" target="_blank">' . $title . '</a>'
                    . '<p><a href="' . $allProductlink . '" target="_blank" class="button button-default">' . $allProducttext . ' &RightArrow;</a></p>'
                    . '<hr>'
                    . '<p><strong>' . $moretext . '</strong></p>'
                    . '<ul>'
                    . '<li><a href="' . $needsupportlink . '" target="_blank">' . $needsupporttext . '</a></li>'
                    . '<li><a href="' . $customservicelink . '" target="_blank">' . $customservicetext . '</a></li>'
                    . '<li><a href="' . $joinproductclublink . '" target="_blank">' . $joinproductclubtext . '</a></li>'
                    . '</ul>'
                    . '</div>';
                }
                echo '</div></div><div class="clear"></div>'
                . '</div>';
            }

        }
    }

}

/**
 * Add Footer credit link
 */
if (!function_exists('ublk_footer')) {

    function ublk_footer() {
        $screen = get_current_screen();
        if ($screen->id == "toplevel_page_block_user" || $screen->id == "admin_page_block_user_date" || $screen->id == "admin_page_block_user_permenant" || $screen->id == "user-blocker_page_blocked_user_list" || $screen->id == "admin_page_datewise_blocked_user_list" || $screen->id == "admin_page_permanent_blocked_user_list" || $screen->id == "admin_page_all_type_blocked_user_list") {
            add_filter('admin_footer_text', 'ublk_remove_footer_admin'); //change admin footer text
        }
    }

}

/**
 * Add rating html at footer of admin
 * @return html rating
 */
if (!function_exists('ublk_remove_footer_admin')) {

    function ublk_remove_footer_admin() {
        ob_start();
        ?>
        <p id="footer-left" class="alignleft">
            <?php _e('If you like', 'user-blocker'); ?>&nbsp;
            <a href="https://wordpress.org/plugins/user-blocker/" target="_blank">
                <strong><?php _e('User Blocker', 'user-blocker'); ?></strong>
            </a>
            <?php _e('please leave us a', 'user-blocker'); ?>
            <a class="bdp-rating-link" data-rated="Thanks :)" target="_blank" href="https://wordpress.org/support/plugin/user-blocker/reviews/?filter=5#new-post">&#x2605;&#x2605;&#x2605;&#x2605;&#x2605;</a>
            <?php _e('rating. A huge thank you from Solwin Infotech in advance!', 'user-blocker'); ?>
        </p>
        <?php
        return ob_get_clean();
    }

}

/**
 * Enqueue admin panel required css
 */
if (!function_exists('ublk_enqueueStyleScript')) {

    function ublk_enqueueStyleScript() {
        global $screen;
        $screen = get_current_screen();
        if ((isset($_GET['page']) && ($_GET['page'] == 'all_type_blocked_user_list' || $_GET['page'] == 'permanent_blocked_user_list' || $_GET['page'] == 'datewise_blocked_user_list' || $_GET['page'] == 'blocked_user_list' || $_GET['page'] == 'block_user' || $_GET['page'] == 'block_user_date' || $_GET['page'] == 'block_user_permenant' || $_GET['page'] == 'welcome_block_user')) || $screen->id == "plugins" ) {
            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-ui-js', 'https://code.jquery.com/ui/1.11.0/jquery-ui.min.js', 'jquery');
            wp_register_script('timepicker-addon-js', plugins_url() . '/user-blocker/script/jquery-ui-timepicker-addon.js', 'jquery');
            wp_enqueue_script('timepicker-addon-js');
            wp_register_script('timepicker-js', plugins_url() . '/user-blocker/script/jquery.timepicker.js', 'jquery');
            wp_enqueue_script('timepicker-js');
            wp_register_script('datepair-js', plugins_url() . '/user-blocker/script/datepair.js', 'jquery');
            wp_enqueue_script('datepair-js');
            wp_register_script('jquery-ui-sliderAccess', plugins_url() . '/user-blocker/script/jquery-ui-sliderAccess.js', 'jquery');
            wp_enqueue_script('jquery-ui-sliderAccess');
            wp_register_script('admin_script', plugins_url() . '/user-blocker/script/admin_script.js');
            wp_enqueue_script('admin_script');
            wp_register_style('timepicker-css', plugins_url() . '/user-blocker/css/jquery.timepicker.css');
            wp_enqueue_style('timepicker-css');
            wp_register_style('jqueryUI', plugins_url() . '/user-blocker/css/jquery-ui.css');
            wp_enqueue_style('jqueryUI');
            wp_register_style('timepicker-addon-css', plugins_url() . '/user-blocker/css/jquery-ui-timepicker-addon.css');
            wp_enqueue_style('timepicker-addon-css');
            wp_register_style('admin_style', plugins_url() . '/user-blocker/css/admin_style.css');
            wp_enqueue_style('admin_style');
        }
        if ($screen->id == 'dashboard') {
            wp_register_style('admin_style', plugins_url() . '/user-blocker/css/admin_style.css');
            wp_enqueue_style('admin_style');
        }
        if (is_rtl()) {
            wp_enqueue_style('admin_style_rtl', plugins_url() . '/user-blocker/css/admin_style_rtl.css');
        }
    }

}

/**
 *
 * @return Display total downloads of plugin
 */
if (!function_exists('ublk_get_user_blocker_total_downloads')) {

    function ublk_get_user_blocker_total_downloads() {
        // Set the arguments. For brevity of code, I will set only a few fields.
        $plugins = $response = "";
        $args = array(
            'author' => 'solwininfotech',
            'fields' => array(
                'downloaded' => true,
                'downloadlink' => true
            )
        );
        // Make request and extract plug-in object. Action is query_plugins
        $response = wp_remote_post(
                'http://api.wordpress.org/plugins/info/1.0/', array(
            'body' => array(
                'action' => 'query_plugins',
                'request' => serialize((object) $args)
            )
                )
        );
        if (!is_wp_error($response)) {
            $returned_object = unserialize(wp_remote_retrieve_body($response));
            $plugins = $returned_object->plugins;
        } else {

        }
        $current_slug = 'user-blocker';
        if ($plugins) {
            foreach ($plugins as $plugin) {
                if ($current_slug == $plugin->slug) {
                    if ($plugin->downloaded) {
                        ?>
                        <span class="total-downloads">
                            <span class="download-number"><?php echo $plugin->downloaded; ?></span>
                        </span>
                        <?php
                    }
                }
            }
        }
    }

}

/**
 * @return Display rating of plugin
 */
$wp_version = get_bloginfo('version');
if ($wp_version > 3.8) {
    if (!function_exists('ublk_wp_user_blocker_star_rating')) {

        function ublk_wp_user_blocker_star_rating($args = array()) {
            $plugins = $response = "";
            $args = array(
                'author' => 'solwininfotech',
                'fields' => array(
                    'downloaded' => true,
                    'downloadlink' => true
                )
            );
            // Make request and extract plug-in object. Action is query_plugins
            $response = wp_remote_post(
                    'http://api.wordpress.org/plugins/info/1.0/', array(
                'body' => array(
                    'action' => 'query_plugins',
                    'request' => serialize((object) $args)
                )
                    )
            );
            if (!is_wp_error($response)) {
                $returned_object = unserialize(wp_remote_retrieve_body($response));
                $plugins = $returned_object->plugins;
            }
            $current_slug = 'user-blocker';
            if ($plugins) {
                foreach ($plugins as $plugin) {
                    if ($current_slug == $plugin->slug) {
                        $rating = $plugin->rating * 5 / 100;
                        if ($rating > 0) {
                            $args = array(
                                'rating' => $rating,
                                'type' => 'rating',
                                'number' => $plugin->num_ratings,
                            );
                            wp_star_rating($args);
                        }
                    }
                }
            }
        }

    }
}

/**
 * Display html of support section at right side
 */
if (!function_exists('ublk_display_support_section')) {

    function ublk_display_support_section() {
        ?>
        <div class="td-admin-sidebar">
            <div class="td-help">
                <h2><?php _e('Help to improve this plugin!', 'user-blocker'); ?></h2>
                <div class="help-wrapper">
                    <span><?php _e('Enjoyed this plugin?', 'user-blocker'); ?></span>
                    <span><?php _e(' You can help by', 'user-blocker'); ?>
                        <a href="https://wordpress.org/support/plugin/user-blocker/reviews/?filter=5#new-post" target="_blank">
                            <?php _e(' rating this plugin on wordpress.org', 'user-blocker'); ?>
                        </a>
                    </span>
                    <div class="td-total-download">
                        <?php _e('Downloads:', 'user-blocker'); ?><?php ublk_get_user_blocker_total_downloads(); ?>
                        <?php
                        $wp_version = get_bloginfo('version');
                        if ($wp_version > 3.8) {
                            ublk_wp_user_blocker_star_rating();
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="useful_plugins">
                <h2><?php _e('Our Other Works', 'user-blocker'); ?></h2>
                <div class="help-wrapper">
                    <div class="pro-content">
                        <strong class="ual_advertisement_legend"><?php _e('Blog Designer Pro', 'user-blocker'); ?></strong>
                        <ul class="advertisementContent">
                            <li><?php _e("50 Beautiful Blog Templates", 'user-blocker') ?></li>
                            <li><?php _e("10+ Unique Timeline Templates", 'user-blocker') ?></li>
                            <li><?php _e("200+ Blog Layout Variations", 'user-blocker') ?></li>
                            <li><?php _e("Single Post Design options", 'user-blocker') ?></li>
                            <li><?php _e("Category, Tag, Author Layouts", 'user-blocker') ?></li>
                            <li><?php _e("Post Type & Taxonomy Filter", 'user-blocker') ?></li>
                            <li><?php _e("800+ Google Font Support", 'user-blocker') ?></li>
                            <li><?php _e("600+ Font Awesome Icons Support", 'user-blocker') ?></li>
                        </ul>
                        <p class="pricing_change"><?php _e('Now only at', 'user-blocker'); ?> <ins>$39</ins></p>
                    </div>
                    <div class="pre-book-pro">
                        <a href="https://codecanyon.net/item/blog-designer-pro-for-wordpress/17069678?ref=solwin" target="_blank">
                            <?php _e('Buy Now on Codecanyon', 'user-blocker'); ?>
                        </a>
                    </div>
                </div>
            </div>
            <div class="td-support">
                <h3><?php _e('Need Support?', 'user-blocker'); ?></h3>
                <div class="help-wrapper">
                    <span><?php _e('Check out the', 'user-blocker') ?>
                        <a href="https://wordpress.org/plugins/user-blocker/faq/" target="_blank"><?php _e('FAQs', 'user-blocker'); ?></a>
                        <?php _e('and', 'user-blocker') ?>
                        <a href="https://wordpress.org/support/plugin/user-blocker/" target="_blank"><?php _e('Support Forums', 'user-blocker') ?></a>
                    </span>
                </div>
            </div>
            <div class="td-support">
                <h3><?php _e('Share & Follow Us', 'user-blocker'); ?></h3>
                <div class="help-wrapper">
                    <!-- Twitter -->
                    <div style='display:block;margin-bottom:8px;'>
                        <a href="https://twitter.com/solwininfotech" class="twitter-follow-button" data-show-count="true" data-show-screen-name="true" data-dnt="true">Follow @solwininfotech</a>
                        <script>!function (d, s, id) {
                                var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
                                if (!d.getElementById(id)) {
                                    js = d.createElement(s);
                                    js.id = id;
                                    js.src = p + '://platform.twitter.com/widgets.js';
                                    fjs.parentNode.insertBefore(js, fjs);
                                }
                            }(document, 'script', 'twitter-wjs');</script>
                    </div>
                    <!-- Facebook -->
                    <div style='display:block;margin-bottom:10px;'>
                        <div id="fb-root"></div>
                        <script>(function (d, s, id) {
                                var js, fjs = d.getElementsByTagName(s)[0];
                                if (d.getElementById(id))
                                    return;
                                js = d.createElement(s);
                                js.id = id;
                                js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.5";
                                fjs.parentNode.insertBefore(js, fjs);
                            }(document, 'script', 'facebook-jssdk'));</script>
                        <div class="fb-share-button" data-href="https://wordpress.org/plugins/user-blocker/" data-layout="button_count"></div>
                    </div>
                    <!-- Google Plus -->
                    <div style='display:block;margin-bottom:8px;'>
                        <!-- Place this tag where you want the +1 button to render. -->
                        <div class="g-plusone" data-href="https://wordpress.org/plugins/user-blocker/"></div>
                        <!-- Place this tag after the last +1 button tag. -->
                        <script type="text/javascript">
                            (function () {
                                var po = document.createElement('script');
                                po.type = 'text/javascript';
                                po.async = true;
                                po.src = 'https://apis.google.com/js/platform.js';
                                var s = document.getElementsByTagName('script')[0];
                                s.parentNode.insertBefore(po, s);
                            })();
                        </script>
                    </div>
                    <div style='display:block;margin-bottom:8px'>
                        <script src="//platform.linkedin.com/in.js" type="text/javascript"></script>
                        <script type="IN/Share" data-url="https://wordpress.org/plugins/user-blocker/" data-counter="right" data-showzero="true"></script>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

}

/**
 *
 * @param type $time
 * @return time
 */
if (!function_exists('ublk_timeToTwentyfourHour')) {

    function ublk_timeToTwentyfourHour($time) {
        if ($time != '') {
            $time = date('H:i:s', strtotime($time));
        }
        return $time;
    }

}

/**
 *
 * @param type $time
 * @return time
 */
if (!function_exists('ublk_timeToTwelveHour')) {

    function ublk_timeToTwelveHour($time) {
        if ($time != '') {
            $time = date('h:i A', strtotime($time));
        }
        return $time;
    }

}

/**
 *
 * @param type $date_time
 * @return date time
 */
if (!function_exists('ublk_dateTimeToTwelveHour')) {

    function ublk_dateTimeToTwelveHour($date_time) {
        if ($date_time != '') {
            $date_time = date('m/d/Y h:i A', strtotime($date_time));
        }
        return $date_time;
    }

}

/**
 *
 * @param type $msg
 * @return text
 */
if (!function_exists('ublk_disp_msg')) {

    function ublk_disp_msg($msg) {
        $msg = stripslashes(nl2br($msg));
        return $msg;
    }

}

/**
 *
 * @param type $day
 * @param type $block_day
 * @return type Get time record
 */
if (!function_exists('ublk_get_time_record')) {

    function ublk_get_time_record($day, $block_day) {
        if (array_key_exists($day, $block_day)) {
            $from_time = $block_day[$day]['from'];
            $to_time = $block_day[$day]['to'];
            if ($from_time == '') {
                echo __('not set', 'user-blocker');
            } else {
                echo ublk_timeToTwelveHour($from_time);
            }
            if ($from_time != '' && $to_time != '') {
                echo ' ' . __('to', 'user-blocker') . ' ' . ublk_timeToTwelveHour($to_time);
            }
        } else {
            echo __('not set', 'user-blocker');
        }
    }

}

/**
 * Call Admin Scripts
 */
if (!function_exists('ublk_admin_scripts')) {

    function ublk_admin_scripts() {
        $screen = get_current_screen();
        $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/user-blocker/user_blocker.php', $markup = true, $translate = true);
        $current_version = $plugin_data['Version'];
        $old_version = get_option('ublk_version');
        if ($old_version != $current_version) {
            update_option('is_user_subscribed_cancled', '');
            update_option('ublk_version', $current_version);
        }
        if (get_option('is_user_subscribed') != 'yes' && get_option('is_user_subscribed_cancled') != 'yes') {
            wp_enqueue_script('thickbox');
            wp_enqueue_style('thickbox');
        }
    }

}

/**
 *
 * @param $actions for take a action for redirection setting
 * @param $plugin_file give path of plugin file
 * @return action for setting link
 */
if (!function_exists('ublk_settings_link')) {

    function ublk_settings_link($actions) {
        $settings_link = '<a href="' . admin_url('admin.php?page=block_user') . '">' . __('Settings', 'user-blocker') . '</a>';
        array_unshift($actions, $settings_link);
        return $actions;
    }

}

/**
 * Start session if not started
 */
if (!function_exists('ublk_session_start')) {

    function ublk_session_start() {
        if (session_id() == '') {
            session_start(['read_and_close' => true]);
            // session_start();
        }
    }

}

/**
 * Subscribe email form
 */
if (!function_exists('ublk_subscribe_mail')) {

    function ublk_subscribe_mail() {
        ?>
        <div id="sol_deactivation_widget_cover_ublk" style="display:none;">
            <div class="sol_deactivation_widget">
                <h3><?php _e('If you have a moment, please let us know why you are deactivating. We would like to help you in fixing the issue.', 'user-blocker'); ?></h3>
                <form id="frmDeactivationublk" name="frmDeactivation" method="post" action="">
                    <ul class="sol_deactivation_reasons_ublk">
                        <?php $i = 1; ?>                        
                        <li>
                            <input class="sol_deactivation_reasons" name="sol_deactivation_reasons_ublk" type="radio" value="<?php echo $i; ?>" id="ublk_reason_<?php echo $i; ?>">
                            <label for="ublk_reason_<?php echo $i; ?>"><?php _e('The plugin suddenly stopped working', 'user-blocker'); ?></label>
                        </li>
                        <?php $i++; ?>
                        <li>
                            <input class="sol_deactivation_reasons" name="sol_deactivation_reasons_ublk" type="radio" value="<?php echo $i; ?>" id="ublk_reason_<?php echo $i; ?>">
                            <label for="ublk_reason_<?php echo $i; ?>"><?php _e('The plugin was not working', 'user-blocker'); ?></label>
                        </li>
                        <?php $i++; ?>
                        <li>
                            <input class="sol_deactivation_reasons" name="sol_deactivation_reasons_ublk" type="radio" value="<?php echo $i; ?>" id="ublk_reason_<?php echo $i; ?>">
                            <label for="ublk_reason_<?php echo $i; ?>"><?php _e("Found other better plugin than this plugin", 'user-blocker'); ?></label>
                        </li>
                        <?php $i++; ?>
                        <li>
                            <input class="sol_deactivation_reasons" name="sol_deactivation_reasons_ublk" type="radio" value="<?php echo $i; ?>" id="ublk_reason_<?php echo $i; ?>">
                            <label for="ublk_reason_<?php echo $i; ?>"><?php _e('The plugin broke my site completely', 'user-blocker'); ?></label>
                        </li>
                        <?php $i++; ?>
                        <li>
                            <input class="sol_deactivation_reasons" name="sol_deactivation_reasons_ublk" type="radio" value="<?php echo $i; ?>" id="ublk_reason_<?php echo $i; ?>">
                            <label for="ublk_reason_<?php echo $i; ?>"><?php _e('No any reason', 'user-blocker'); ?></label>
                        </li>
                        <?php $i++; ?>
                        <li>
                            <input class="sol_deactivation_reasons" name="sol_deactivation_reasons_ublk" type="radio" value="<?php echo $i; ?>" id="ublk_reason_<?php echo $i; ?>">
                            <label for="ublk_reason_<?php echo $i; ?>"><?php _e('Other', 'user-blocker'); ?></label><br/>
                            <input style="display:none;width: 90%" value="" type="text" name="sol_deactivation_reason_other_ublk" class="sol_deactivation_reason_other_ublk" />
                        </li>
                    </ul>
                    <p>
                        <input type='checkbox' class='ublk_agree' id='ublk_agree_gdpr_deactivate' value='1' />
                        <label for='ublk_agree_gdpr_deactivate' class='ublk_agree_gdpr_lbl'><?php echo esc_attr(__('By clicking this button, you agree with the storage and handling of your data as mentioned above by this website. (GDPR Compliance)', 'user-blocker')); ?></label>
                    </p>
                    <a onclick='ublk_submit_optin("deactivate")' class="button button-secondary"><?php _e('Submit', 'user-blocker'); echo ' &amp; '; _e('Deactivate', 'user-blocker'); ?></a>
                    <input type="submit" name="sbtDeactivationFormClose" id="sbtDeactivationFormCloseual" class="button button-primary" value="<?php _e('Cancel', 'user-blocker'); ?>" />
                    <a href="javascript:void(0)" class="ublk-deactivation" aria-label="<?php _e('Deactivate User Blocker','user-blocker'); ?>"><?php _e('Skip', 'user-blocker'); echo ' &amp; '; _e(' Deactivate', 'user-blocker'); ?></a>
                </form>
                <div class="support-ticket-section">
                    <h3><?php _e('Would you like to give us a chance to help you?', 'user-blocker'); ?></h3>
                    <img src="<?php echo UB_PLUGIN_URL . '/images/support-ticket.png'; ?>">
                    <a target='_blank' href="<?php echo esc_url('http://support.solwininfotech.com/'); ?>"><?php _e('Create a support ticket','user-blocker'); ?></a>
                </div>
            </div>
        </div>
        <a style="display:none" href="#TB_inline?height=800&inlineId=sol_deactivation_widget_cover_ublk" class="thickbox" id="deactivation_thickbox_ublk"></a>
        <?php
    }

}


if (!function_exists('ublk_user_category_dropdown')) {

    function ublk_user_category_dropdown($cmbUserBy) {
        ?>
        <label><strong><?php _e('Select User/Category: ', 'user-blocker') ?></strong></label>
        <select name="cmbUserBy" id="cmbUserBy" onchange="changeUserBy();">
            <option <?php echo selected($cmbUserBy, 'username'); ?> value="username"><?php _e('Username', 'user-blocker'); ?></option>
            <option <?php echo selected($cmbUserBy, 'role'); ?> value="role" ><?php _e('Role', 'user-blocker'); ?></option>
        </select><?php
    }

}

if (!function_exists('ublk_blocked_user_category_dropdown')) {

    function ublk_blocked_user_category_dropdown($display) {
        ?>
        <label><strong><?php _e('Select User/Category: ', 'user-blocker') ?></strong></label>
        <select name="display" id="display_status">
            <option value="users" <?php echo selected($display, 'users') ?> ><?php _e('Users', 'user-blocker'); ?></option>
            <option value="roles" <?php echo selected($display, 'roles') ?>><?php _e('Roles', 'user-blocker'); ?></option>
        </select><?php
    }

}

if (!function_exists('ublk_role_selection_dropdown')) {

    function ublk_role_selection_dropdown($display_users, $get_roles, $srole) {
        ?>
        <div style="margin-left: 15px; display: inline-block; vertical-align: middle;">
            <div class="filter_div" <?php
            if ($display_users == 1) {
                echo 'style="display: block;"';
            } else
                echo 'style="display: none;"';
            ?>>
                <label><strong><?php _e('Select Role: ', 'user-blocker') ?></strong></label>
                <select id="srole" name="srole" onchange="searchUser();">
                    <option value=""><?php _e('All Roles', 'user-blocker'); ?></option>
                    <?php
                    if ($get_roles) {
                        foreach ($get_roles as $key => $value) {
                            if ($key == 'administrator')
                                continue;
                            ?>
                            <option <?php echo selected($key, $srole); ?> value="<?php echo $key; ?>"><?php echo ucfirst($value['name']); ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
        </div><?php
    }

}

if (!function_exists('ublk_blocked_role_selection_dropdown')) {

    function ublk_blocked_role_selection_dropdown($display, $get_roles, $srole) {
        ?>
        <div style="margin-left: 15px; display: inline-block; vertical-align: middle;">
            <div class="filter_div" <?php if ($display == 'roles') echo 'style="display: none"'; ?>>
                <label><strong><?php _e('Select Role: ', 'user-blocker') ?></strong></label>
                <select id="srole" name="role" onchange="searchUser();">
                    <option value=""><?php _e('All Roles', 'user-blocker'); ?></option>
                    <?php
                    if ($get_roles) {
                        foreach ($get_roles as $key => $value) {
                            if ($key == 'administrator')
                                continue;
                            ?>
                            <option <?php echo selected($key, $srole); ?> value="<?php echo $key; ?>"><?php echo ucfirst($value['name']); ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
        </div><?php
    }

}

if (!function_exists('ublk_pagination')) {

    function ublk_pagination($display_users, $total_pages, $total_items, $paged, $prev_page, $next_page, $srole, $txtUsername, $orderby, $order, $tab) {
        ?>
        <div class="filter_div" style="float: right; <?php
        if ($display_users == 1) {
            echo 'display: block;';
        } else {
            echo 'display: none;';
        }
        ?>">
            <div class="tablenav-pages" <?php
            if ((int) $total_pages <= 1) {
                echo 'style="display: none;"';
            }
            ?>>
                <span class="displaying-num"><?php echo $total_items . ' ';
         _e('items', 'user-blocker');
            ?></span>
                <span class="pagination-links">
                    <a class="first-page <?php if ($paged == '1') echo 'disabled'; ?>" href="<?php echo '?page=' . $tab . '&paged=1&srole=' . $srole . '&txtUsername=' . $txtUsername; ?>&orderby=<?php echo $orderby; ?>&order=<?php echo $order; ?>" title="<?php _e('Go to the first page', 'user-blocker'); ?>">&laquo;</a>
                    <a class="prev-page <?php if ($paged == '1') echo 'disabled'; ?>" href="<?php echo '?page=' . $tab . '&paged=' . $prev_page . '&srole=' . $srole . '&txtUsername=' . $txtUsername; ?>&orderby=<?php echo $orderby; ?>&order=<?php echo $order; ?>" title="<?php _e('Go to the previous page', 'user-blocker'); ?>">&lsaquo;</a>
                    <span class="paging-input">
                        <input class="current-page" type="text" size="1" value="<?php echo $paged; ?>" name="paged" title="<?php _e('Current page', 'user-blocker'); ?>">
        <?php _e('of', 'user-blocker'); ?>
                        <span class="total-pages"><?php echo $total_pages; ?></span>
                    </span>
                    <a class="next-page <?php if ($paged == $total_pages) echo 'disabled'; ?>" href="<?php echo '?page=' . $tab . '&paged=' . $next_page . '&srole=' . $srole . '&txtUsername=' . $txtUsername; ?>&orderby=<?php echo $orderby; ?>&order=<?php echo $order; ?>" title="<?php _e('Go to the next page', 'user-blocker'); ?>">&rsaquo;</a>
                    <a class="last-page <?php if ($paged == $total_pages) echo 'disabled'; ?>" href="<?php echo '?page=' . $tab . '&paged=' . $total_pages . '&srole=' . $srole . '&txtUsername=' . $txtUsername; ?>&orderby=<?php echo $orderby; ?>&order=<?php echo $order; ?>" title="<?php _e('Go to the last page', 'user-blocker'); ?>">&raquo;</a>
                </span>
                <input style="display: none;" id="sbtPages" class="button" type="submit" value="sbtPages" name="filter_action">
            </div>
        </div><?php
    }

}

if (!function_exists('ublk_blocked_pagination')) {

    function ublk_blocked_pagination($total_pages, $total_items, $paged, $prev_page, $next_page, $srole, $txtUsername, $orderby, $order, $tab) {
        ?>
        <div class="tablenav-pages" <?php
        if ((int) $total_pages <= 1) {
            echo 'style="display: none;"';
        }
        ?>>
            <span class="displaying-num"><?php echo $total_items; ?> <?php _e('items', 'user-blocker'); ?></span>
            <span class="pagination-links">
                <a class="first-page <?php if ($paged == '1') echo 'disabled'; ?>" href="<?php echo '?page=blocked_user_list&paged=1&role=' . $srole . '&txtUsername=' . $txtUsername; ?>&orderby=<?php echo $orderby; ?>&order=<?php echo $order; ?>" title="<?php _e('Go to the first page', 'user-blocker'); ?>">&laquo;</a>
                <a class="prev-page <?php if ($paged == '1') echo 'disabled'; ?>" href="<?php echo '?page=blocked_user_list&paged=' . $prev_page . '&role=' . $srole . '&txtUsername=' . $txtUsername; ?>&orderby=<?php echo $orderby; ?>&order=<?php echo $order; ?>" title="<?php _e('Go to the previous page', 'user-blocker'); ?>">&lsaquo;</a>
                <span class="paging-input">
                    <input class="current-page" type="text" size="1" value="<?php echo $paged; ?>" name="paged" title="Current page">
        <?php _e('of', 'user-blocker'); ?>
                    <span class="total-pages"><?php echo $total_pages; ?></span>
                </span>
                <a class="next-page <?php if ($paged == $total_pages) echo 'disabled'; ?>" href="<?php echo '?page=blocked_user_list&paged=' . $next_page . '&role=' . $srole . '&txtUsername=' . $txtUsername; ?>&orderby=<?php echo $orderby; ?>&order=<?php echo $order; ?>" title="<?php _e('Go to the next page', 'user-blocker'); ?>">&rsaquo;</a>
                <a class="last-page <?php if ($paged == $total_pages) echo 'disabled'; ?>" href="<?php echo '?page=blocked_user_list&paged=' . $total_pages . '&role=' . $srole . '&txtUsername=' . $txtUsername; ?>&orderby=<?php echo $orderby; ?>&order=<?php echo $order; ?>" title="<?php _e('Go to the last page', 'user-blocker'); ?>">&raquo;</a>
            </span>
            <input style="display: none;" id="sbtPages" class="button" type="submit" value="sbtPages" name="filter_action">
        </div><?php
    }

}

if (!function_exists('ublk_search_field')) {

    function ublk_search_field($display, $txtUsername, $tab) {
        ?>
        <div class="actions">
            <div class="filter_div" <?php if ($display == 'roles') echo 'style="display: none"'; ?>>
                <input type="hidden" value="<?php echo $tab; ?>" name="page" />
                <input type="text" id="txtUsername" value="<?php echo $txtUsername; ?>" placeholder="<?php esc_attr_e('username or email or first name', 'user-blocker'); ?>" name="txtUsername" />
                <input id="filter_action" class="button" type="submit" value="<?php _e('Search', 'user-blocker'); ?>" name="filter_action">
                <a class="button" href="<?php echo '?page=' . $tab; ?>" style="margin-left: 10px;"><?php _e('Reset', 'user-blocker'); ?></a>
            </div>
        </div><?php
    }

}

if (!function_exists('ublk_user_search_field')) {

    function ublk_user_search_field($display_users, $txtUsername, $tab) {
        ?>
        <div class="actions">
            <div class="filter_div" <?php
            if ($display_users == 1) {
                echo 'style="display: block;"';
            } else
                echo 'style="display: none;"';
            ?>>
                <input type="hidden" value="<?php echo $tab; ?>" name="page" />
                <input type="text" id="txtUsername" value="<?php echo $txtUsername; ?>" placeholder="<?php esc_attr_e('username or email or first name', 'user-blocker'); ?>" name="txtUsername" />
                <input id="filter_action" class="button" type="submit" value="<?php esc_attr_e('Search', 'user-blocker'); ?>" name="filter_action">
                <a class="button" href="<?php echo '?page=' . $tab . '&resetsearch=1'; ?>" style="margin-left: 10px;"><?php _e('Reset', 'user-blocker'); ?></a>
            </div>
        </div><?php
    }

}

add_action('delete_user', 'ublk_update_block_user_role', 15, 1);
add_action('user_register', 'ublk_update_block_user_role', 20, 1);
add_action('edit_user_profile_update','ublk_update_block_user_role', 25, 1);

if(!function_exists('ublk_update_block_user_role')){
    function ublk_update_block_user_role($user_id){

        $user_data = get_user_by('id', $user_id);
        if(isset($_POST['role'])) {
            $role = esc_attr($_POST['role']);
        }
        if($role == '') {
            if (!empty($user_data->roles) && is_array($user_data->roles)) {
                $role = $user_data->roles[0];
            }
        }
        
        $block_msg_day = get_option($role . '_block_msg_day');
        $block_msg_date = get_option($role . '_block_msg_date');
        
        $is_active = get_user_meta($user_id, 'is_active', true);
        if ($is_active != 'n') {
            if (!empty($block_msg_day)) {
                update_user_meta($user_id, 'block_msg_day', sanitize_textarea_field($block_msg_day));
            }else{
                update_user_meta($user_id, 'block_msg_day', '');
            }
            if (!empty($block_msg_date)) {
                update_user_meta($user_id, 'block_msg_date', sanitize_textarea_field($block_msg_date));
            }else{
                update_user_meta($user_id, 'block_msg_date', '');
            }
        }

//        $role_usr_qry = get_users(array('role' => $role));
//        $curr_role_usr = wp_list_pluck($role_usr_qry, 'ID');
//        if (count($curr_role_usr) > 0) {
//            foreach ($curr_role_usr as $u_id) {
//                $is_active = get_user_meta($u_id, 'is_active', true);
//                if ($is_active != 'n') {
//                    if (!empty($block_day)) {
//                        //Not update already date wise blocked users
//                        update_user_meta($u_id, 'block_day', $block_day);
//                    }else{
//                        update_user_meta($u_id, 'block_day', '');
//                    }
//                    if ((!empty($block_msg_day))) {
//                        update_user_meta($u_id, 'block_msg_day', $block_msg_day);
//                    }else{
//                        update_user_meta($u_id, 'block_msg_day', '');
//                    }
//                }
//            }
//        }
    }
}
