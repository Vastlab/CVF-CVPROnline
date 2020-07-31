<?php
add_action('plugins_loaded', 'ublk_load_plugin');

if (!function_exists('ublk_load_plugin')) {

    function ublk_load_plugin() {

        $user_blocking['promo_time'] = get_option('user_blocking_promo_time');
        if (empty($user_blocking['promo_time'])) {
            $user_blocking['promo_time'] = time();
            update_option('user_blocking_promo_time', $user_blocking['promo_time']);
        }

        if (!empty($user_blocking['promo_time']) && $user_blocking['promo_time'] > 0 && $user_blocking['promo_time'] < (time() - (60 * 60 * 24 * 3))) {
            add_action('admin_notices', 'ublk_promo');
        }

        if (isset($_GET['user_blocking_promo']) && (int) $_GET['user_blocking_promo'] == 0) {
            update_option('user_blocking_promo_time', (0 - time()));
            die('DONE');
        }
    }

}

if (!function_exists('ublk_promo')) {

    // Show the promo
    function ublk_promo() {
        echo '
            <script>
            jQuery(document).ready( function() {
                    (function($) {
                            $("#user_blocking_promo .user_blocking_promo-close").click(function(){
                                    var data;
                                    // Hide it
                                    $("#user_blocking_promo").hide();

                                    // Save this preference
                                    $.post("'.admin_url('?user_blocking_promo=0').'", data, function(response) {
                                    });
                            });
                    })(jQuery);
            });
            </script>
            <style>/* Promotional notice css*/
                .user_blocking_button {
                    background-color: #4CAF50; /* Green */
                    border: none;
                    color: white;
                    padding: 8px 16px;
                    text-align: center;
                    text-decoration: none;
                    display: inline-block;
                    font-size: 16px;
                    margin: 4px 2px;
                    -webkit-transition-duration: 0.4s; /* Safari */
                    transition-duration: 0.4s;
                    cursor: pointer;
                }
                .user_blocking_button:focus{
                    border: none;
                    color: white;
                }
                .user_blocking_button1 {
                    color: white;
                    background-color: #4CAF50;
                    border:3px solid #4CAF50;
                }
                .user_blocking_button1:hover {
                    box-shadow: 0 6px 8px 0 rgba(0,0,0,0.24), 0 9px 25px 0 rgba(0,0,0,0.19);
                    color: white;
                    border:3px solid #4CAF50;
                }
                .user_blocking_button2 {
                    color: white;
                    background-color: #0085ba;
                }
                .user_blocking_button2:hover {
                    box-shadow: 0 6px 8px 0 rgba(0,0,0,0.24), 0 9px 25px 0 rgba(0,0,0,0.19);
                    color: white;
                }
                .user_blocking_button3 {
                    color: white;
                    background-color: #365899;
                }
                .user_blocking_button3:hover {
                    box-shadow: 0 6px 8px 0 rgba(0,0,0,0.24), 0 9px 25px 0 rgba(0,0,0,0.19);
                    color: white;
                }
                .user_blocking_button4 {
                    color: white;
                    background-color: rgb(66, 184, 221);
                }
                .user_blocking_button4:hover {
                    box-shadow: 0 6px 8px 0 rgba(0,0,0,0.24), 0 9px 25px 0 rgba(0,0,0,0.19);
                    color: white;
                }
                .user_blocking_promo-close {
                    float:right;
                    text-decoration:none;
                    margin: 5px 10px 0px 0px;
                }
                .user_blocking_promo-close:hover {
                    color: red;
                }
                </style>
                <div class="notice notice-success" id="user_blocking_promo" style="min-height:120px">
                        <a class="user_blocking_promo-close" href="javascript:" aria-label="Dismiss this Notice">
                                <span class="dashicons dashicons-dismiss"></span> Dismiss
                        </a>
                        <img src="' . UB_PLUGIN_URL . '/images/logo-200.png" style="float:left; margin:10px 20px 10px 10px" width="100" />
                        <p style="font-size:16px">' . __("We are glad you like", "user-blocker")." <strong>".__("User Blocker", "user-blocker")."</strong> ". __("plugin and have been using it since the past few days. It is time to take the next step.", "user-blocker") . '</p>
                        <p>
                                <a class="user_blocking_button user_blocking_button2" target="_blank" href="https://wordpress.org/support/plugin/user-blocker/reviews/?filter=5">'.__("Rate it","user-blocker")." 5★'s".'</a>
                                <a class="user_blocking_button user_blocking_button3" target="_blank" href="https://www.facebook.com/SolwinInfotech/">'.__("Like Us on Facebook","user-blocker").'</a>
                                <a class="user_blocking_button user_blocking_button4" target="_blank" href="https://twitter.com/home?status=' . rawurlencode('I use #userblocker to secure my #WordPress site from spam users.') . '">'.__("Tweet about User Blocker ","user-blocker").'</a>
                        </p>
                </div>';
    }

}