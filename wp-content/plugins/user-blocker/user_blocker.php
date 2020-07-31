<?php
/**
  Plugin Name: User Blocker
  Plugin URI: https://wordpress.org/plugins/user-blocker/
  Description: Block your unwanted site users except admin based on day, time and date or permanently.
  Author: Solwin Infotech
  Author URI: https://www.solwininfotech.com/
  Copyright: Solwin Infotech
  Version: 1.5.1
  Requires at least: 4.0
  Tested up to: 5.4
  License: GPLv2 or later
 */
/**
 * Exit if accessed directly
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Exit if add_action function not found
 */
if (!function_exists('add_action')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

define('UB_PLUGIN_URL', plugin_dir_url(__FILE__));

include plugin_dir_path(__FILE__) . 'includes/user_blocker_common_functions.php';
include plugin_dir_path(__FILE__) . 'includes/user_blocker_blocked_users_list.php';
include plugin_dir_path(__FILE__) . 'includes/user_blocker_block_users.php';

require_once plugin_dir_path(__FILE__) . 'includes/promo_notice.php';

add_action('admin_menu', 'ublk_plugin_setup');
add_action('plugins_loaded', 'ublk_latest_news_solwin_feed');
add_action('current_screen', 'ublk_footer');
add_action('admin_enqueue_scripts', 'ublk_enqueueStyleScript');
add_action('plugins_loaded', 'ublk_load_text_domain');
add_action('admin_enqueue_scripts', 'ublk_admin_scripts');
$plugin = plugin_basename( __FILE__ );
add_filter("plugin_action_links_$plugin", 'ublk_settings_link', 10, 2);
add_action('init', 'ublk_session_start');
add_action('admin_head', 'ublk_subscribe_mail', 10);
add_action('wp_ajax_close_tab', 'wp_ajax_blocker_close_tab');

/**
 * Enqueue admin panel required js
 */
if (!function_exists('ublk_plugin_setup')) {

    function ublk_plugin_setup() {
        $ublk_is_optin = get_option('ublk_is_optin');
        if($ublk_is_optin == 'yes' || $ublk_is_optin == 'no') {
            add_menu_page(esc_html__('User Blocker', 'user-blocker'), esc_html__('User Blocker', 'user-blocker'), 'manage_options', 'block_user', 'ublk_block_user_page', 'dashicons-admin-users');
        }
        else {
            add_menu_page(esc_html__('User Blocker', 'user-blocker'), esc_html__('User Blocker', 'user-blocker'), 'manage_options', 'welcome_block_user', 'ublk_welcome_page', 'dashicons-admin-users');

        }        
        $block_date_page = add_submenu_page('', esc_html__('Block User Date Wise', 'user-blocker'), esc_html__('Date Wise Block User', 'user-blocker'), 'manage_options', 'block_user_date', 'ublk_block_user_date_page', 1);
        $block_permanent = add_submenu_page('', esc_html__('Block User Permanent', 'user-blocker'), esc_html__('Permanently Block User', 'user-blocker'), 'manage_options', 'block_user_permenant', 'ublk_block_user_permenant_page', 2);
        add_submenu_page('block_user', esc_html__('Blocked User list', 'user-blocker'), esc_html__('Blocked User list', 'user-blocker'), 'manage_options', 'blocked_user_list', 'ublk_block_user_list_page', 3);
        $list_user_date = add_submenu_page('', esc_html__('Date Wise Blocked User list', 'user-blocker'), esc_html__('Date Wise Blocked User list', 'user-blocker'), 'manage_options', 'datewise_blocked_user_list', 'ublk_datewise_block_user_list_page', 4);
        $list_user_permanent = add_submenu_page('', esc_html__('Permanent Blocked User list', 'user-blocker'), esc_html__('Permanent Blocked User list', 'user-blocker'), 'manage_options', 'permanent_blocked_user_list', 'ublk_permanent_block_user_list_page', 5);
        $list_user_all = add_submenu_page('', esc_html__('All Type Blocked User list', 'user-blocker'), esc_html__('All Type Blocked User list', 'user-blocker'), 'manage_options', 'all_type_blocked_user_list', 'ublk_all_type_block_user_list_page', 6);

        // Enqueue script in submenu page to fix the current menu indicator
        add_action("admin_footer-$block_date_page", function() {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    jQuery('#toplevel_page_block_user, #toplevel_page_block_user > a')
                            .removeClass('wp-not-current-submenu')
                            .addClass('wp-has-current-submenu');
                    jQuery('#toplevel_page_block_user .wp-submenu-wrap li.wp-first-item')
                            .addClass('current');
                });
            </script>
            <?php
        });
        add_action("admin_footer-$block_permanent", function() {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    jQuery('#toplevel_page_block_user, #toplevel_page_block_user > a')
                            .removeClass('wp-not-current-submenu')
                            .addClass('wp-has-current-submenu');
                    jQuery('#toplevel_page_block_user .wp-submenu-wrap li')
                            .addClass('current');
                    jQuery('#toplevel_page_block_user .wp-submenu-wrap li:first-child')
                            .removeClass('current');
                    jQuery('#toplevel_page_block_user .wp-submenu-wrap li:last-child')
                            .removeClass('current');
                });
            </script>
            <?php
        });
        add_action("admin_footer-$list_user_date", function() {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    jQuery('#toplevel_page_block_user, #toplevel_page_block_user > a')
                            .removeClass('wp-not-current-submenu')
                            .addClass('wp-has-current-submenu');
                    jQuery('#toplevel_page_block_user .wp-submenu-wrap li:last-child')
                            .addClass('current');
                });
            </script>
            <?php
        });
        add_action("admin_footer-$list_user_permanent", function() {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    jQuery('#toplevel_page_block_user, #toplevel_page_block_user > a')
                            .removeClass('wp-not-current-submenu')
                            .addClass('wp-has-current-submenu');
                    jQuery('#toplevel_page_block_user .wp-submenu-wrap li:last-child')
                            .addClass('current');
                });
            </script>
            <?php
        });
        add_action("admin_footer-$list_user_all", function() {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    jQuery('#toplevel_page_block_user, #toplevel_page_block_user > a')
                            .removeClass('wp-not-current-submenu')
                            .addClass('wp-has-current-submenu');
                    jQuery('#toplevel_page_block_user .wp-submenu-wrap li:last-child')
                            .addClass('current');
                });
            </script>
            <?php
        });
    }

}

/**
 *
 * @param type $user
 * @param type $username
 * @param type $password
 * @return \WP_Error
 */
if (!function_exists('ublk_auth_signon')) {

    function ublk_auth_signon($user, $username, $password) {
        if (!is_wp_error($user) && is_object($user)) {
            $user_id = $user->ID;
            $is_active = get_user_meta($user_id, 'is_active', true);
            $block_day = get_user_meta($user_id, 'block_day', true);
            $block_date = get_user_meta($user_id, 'block_date', true);
            if ($is_active == 'n') {
                $block_msg_permenant = get_user_meta($user_id, 'block_msg_permenant', true);
                add_filter('login_errors','ublk_login_error', 10 );
                return new WP_Error('authentication_failed', '<strong>' . __('ERROR', 'user-blocker') . '</strong>: ' . $block_msg_permenant);
            } else {
                $error_msg = '';
                if (!empty($block_day) && $block_day != 0 && $block_day != '') {
                    $full_date = getdate();
                    $current_day = strtolower($full_date['weekday']);
                    $current_time = current_time('timestamp');
                    if (array_key_exists($current_day, $block_day)) {
                        $from_time = $sfrmtime = $block_day[$current_day]['from'];
                        $to_time = $stotime = $block_day[$current_day]['to'];
                        $from_time = strtotime($from_time);
                        $to_time = strtotime($to_time);
                        if ($current_time >= $from_time && $current_time <= $to_time) {
                            $block_msg_day = get_user_meta($user_id, 'block_msg_day', true);
                            $error_msg = ' '.$block_msg_day;
                        }
                    }
                } else if ($block_date != 0 && $block_date != '' && !empty($block_date)) {
                    $frmdate = $sfrmdate = $block_date['frmdate'];
                    $todate = $stodate = $block_date['todate'];
                    $frmdate = strtotime($frmdate) . '</br>';
                    $todate = strtotime($todate);
                    $current_date = current_time('timestamp');
                    if ($current_date >= $frmdate && $current_date <= $todate) {
                        $block_msg_date = get_user_meta($user_id, 'block_msg_date', true);
                        if ($error_msg == '')
                            $error_msg = ' '.$block_msg_date;
                        else
                            $error_msg .= ' ' . $block_msg_date;
                    }
                }
                if ($error_msg != '') {
                    add_filter('login_errors','ublk_login_error',10 );
                    return new WP_Error('authentication_failed', '<strong>' . __('ERROR', 'user-blocker') . '</strong>:' .' '. $error_msg);
                }
            }
        }
        return $user;
    }

}
add_filter('authenticate', 'ublk_auth_signon', 30, 3);

/**
 *
 * @param type $parm
 * @return \WP_Error
 */
if (!function_exists('ublk_login_error')) {

    function ublk_login_error($parm){
        $username = $user_id = ''; 
        if(isset($_REQUEST)){
            foreach((array) $_REQUEST as $key=>$val){
                if($key == 'log'){
                    $username =  $val;
                    $user = get_user_by('login', $username);
                    $user_id = $user->ID;
                }
            }
        }
        if(!empty($user_id)){
            $is_active = get_user_meta($user_id, 'is_active', true);
            $block_day = get_user_meta($user_id, 'block_day', true);
            $block_date = get_user_meta($user_id, 'block_date', true);
            if ($is_active == 'n') {
                $block_msg_permenant = get_user_meta($user_id, 'block_msg_permenant', true);
                $my_message = '<strong>' . __('ERROR', 'user-blocker') . '</strong>:' .' '. $block_msg_permenant;
            } else {
                $error_msg = '';
                if (!empty($block_day) && $block_day != 0 && $block_day != '') {
                    $full_date = getdate();
                    $current_day = strtolower($full_date['weekday']);
                    $current_time = current_time('timestamp');
                    if (array_key_exists($current_day, $block_day)) {
                        $from_time = $sfrmtime = $block_day[$current_day]['from'];
                        $to_time = $stotime = $block_day[$current_day]['to'];
                        $from_time = strtotime($from_time);
                        $to_time = strtotime($to_time);
                        if ($current_time >= $from_time && $current_time <= $to_time) {
                            $block_msg_day = get_user_meta($user_id, 'block_msg_day', true);
                            $error_msg = ' '.$block_msg_day;
                        }
                    }
                } else if ($block_date != 0 && $block_date != '' && !empty($block_date)) {
                    $frmdate = $sfrmdate = $block_date['frmdate'];
                    $todate = $stodate = $block_date['todate'];
                    $frmdate = strtotime($frmdate) . '</br>';
                    $todate = strtotime($todate);
                    $current_date = current_time('timestamp');
                    if ($current_date >= $frmdate && $current_date <= $todate) {
                        $block_msg_date = get_user_meta($user_id, 'block_msg_date', true);
                        if ($error_msg == '')
                            $error_msg = ' '.$block_msg_date;
                        else
                            $error_msg .= ' ' . $block_msg_date;
                    }
                }
                if ($error_msg != '') {
                    $my_message = '<strong>' . __('ERROR', 'user-blocker') . '</strong>:' .' '. $error_msg;
                }
            }
        }
        return $my_message;
    }
    
}

/**
 *
 * @param type $user_id
 */
if (!function_exists('ublk_when_register')) {

    function ublk_when_register($user_id) {
        $user_id;
        $user_info = get_userdata($user_id);
        $user_role = $user_info->roles[0];
        $permenant_block = get_option($user_role . '_is_active');
        if ($permenant_block == 'n') {
            update_user_meta($user_id, 'is_active', 'n');
            $block_msg_permenant = get_option($user_role . '_block_msg_permenant');
            update_user_meta($user_id, 'block_msg_permenant', $block_msg_permenant);
        } else {
            $day_wise_block = get_option($user_role . '_block_day');
            $date_wise_block = get_option($user_role . '_block_date');
            $day_wise_block_msg = get_option($user_role . '_block_msg_day');
            $date_wise_block_msg = get_option($user_role . '_block_msg_date');
            if ($day_wise_block != 0 && $day_wise_block != '') {
                update_user_meta($user_id, 'block_day', $day_wise_block);
                update_user_meta($user_id, 'block_msg_day', $day_wise_block_msg);
            }
            if ($date_wise_block != 0 && $date_wise_block != '') {
                update_user_meta($user_id, 'block_date', $date_wise_block);
                update_user_meta($user_id, 'block_msg_date', $date_wise_block_msg);
            }
        }
    }

}
add_action('user_register', 'ublk_when_register', 10, 1);

/**
 *
 * @param type $time
 * @return int
 */
if (!function_exists('ublk_validate_time')) {

    function ublk_validate_time($time) {
        $splitBySpace = explode(" ", $time);
        $firstPart = $splitBySpace[0];
        $secondPart = $splitBySpace[1];
        if ($secondPart == 'AM' || $secondPart == 'PM') {
            $timeIntSplit = explode(":", $firstPart);
            if (strlen($timeIntSplit[0]) == 2 && strlen($timeIntSplit[1]) == 2) {
                $timeFirst = intval($timeIntSplit[0]);
                $timeSecond = intval($timeIntSplit[1]);
                if ($timeSecond >= 0 || $timeSecond < 60) {
                    if ($timeSecond >= 1 || $timeSecond < 13) {
                        return 1;
                    } else {
                        return 0;
                    }
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

}

/**
 *
 * @param type $day
 * @param type $block_day
 * @return html Display block time
 */
if (!function_exists('ublk_display_block_time')) {

    function ublk_display_block_time($day, $block_day) {
        if (is_array($block_day)) {
            if (array_key_exists($day, $block_day)) {
                $from_time = $block_day[$day]['from'];
                $to_time = $block_day[$day]['to'];
                if ($from_time != '' && $to_time != '') {
                    echo '<div class="days">';
                    echo '<span>' . strtoupper($day) . '</span>';
                    echo '<span>' . ublk_timeToTwelveHour($from_time) . ' ' . __('to', 'user-blocker') . ' ' . ublk_timeToTwelveHour($to_time) . '</span>';
                    echo '</div>';
                }
            }
        }
    }

}

/**
 *
 * @param type $role
 * @param type $old_block_day
 * @param type $block_day
 * @param type $old_block_msg_day
 * @param type $block_msg_day
 */
if (!function_exists('ublk_block_role_users_day')) {

    function ublk_block_role_users_day($role, $old_block_day, $block_day, $old_block_msg_day, $block_msg_day) {
        //Update all users of this role
        $role_usr_qry = get_users(array('role' => $role));
        $curr_role_usr = wp_list_pluck($role_usr_qry, 'ID');
        if (count($curr_role_usr) > 0) {
            foreach ($curr_role_usr as $u_id) {
                $own_block_day = get_user_meta($u_id, 'block_day', true);
                $own_block_msg_day = get_user_meta($u_id, 'block_msg_day', true);
                if ((empty($own_block_day) || ($own_block_day == $old_block_day)) && ( empty($own_block_msg_day) || $old_block_msg_day == $own_block_msg_day)) {
                    //Not update already date wise blocked users
                    $is_active = get_user_meta($u_id, 'is_active', true);
                    if ($is_active != 'n') {
                        update_user_meta($u_id, 'block_day', $block_day);
                        update_user_meta($u_id, 'block_msg_day', $block_msg_day);
                    }
                }
            }
        }
    }

}

/**
 *
 * @param type $role
 * @param type $old_block_date
 * @param type $block_date
 * @param type $old_block_msg_date
 * @param type $block_msg_date
 */
if (!function_exists('ublk_block_role_users_date')) {

    function ublk_block_role_users_date($role, $old_block_date, $block_date, $old_block_msg_date, $block_msg_date) {
        //Update all users of this role
        $role_usr_qry = get_users(array('role' => $role));
        $curr_role_usr = wp_list_pluck($role_usr_qry, 'ID');
        if (count($curr_role_usr) > 0) {
            foreach ($curr_role_usr as $u_id) {
                $own_block_date = get_user_meta($u_id, 'block_date', true);
                $own_block_msg_date = get_user_meta($u_id, 'block_msg_date', true);
                if ((empty($own_block_date) || ($own_block_date == $old_block_date)) && ( empty($own_block_msg_date) || $old_block_msg_date == $own_block_msg_date)) {
                    //Not update already date wise blocked users
                    $is_active = get_user_meta($u_id, 'is_active', true);
                    if ($is_active != 'n') {
                        update_user_meta($u_id, 'block_date', $block_date);
                        update_user_meta($u_id, 'block_msg_date', $block_msg_date);
                    }
                }
            }
        }
    }

}

/**
 *
 * @param type $role
 * @param type $is_active
 * @param type $old_block_msg_permenant
 * @param type $block_msg_permenant
 */
if (!function_exists('ublk_block_role_users_permenant')) {

    function ublk_block_role_users_permenant($role, $is_active, $old_block_msg_permenant, $block_msg_permenant) {
        //Update all users of this role
        $role_usr_qry = get_users(array('role' => $role));
        $curr_role_usr = wp_list_pluck($role_usr_qry, 'ID');
        if (count($curr_role_usr) > 0) {
            foreach ($curr_role_usr as $u_id) {
                $is_active_a = get_user_meta($u_id, 'is_active', true);
                $own_block_msg_permenant = get_user_meta($u_id, 'block_msg_permenant', true);
                if ((isset($is_active_a) && $is_active_a == '') || $own_block_msg_permenant == $old_block_msg_permenant) {
                    //Not update already date wise blocked users
                    update_user_meta($u_id, 'is_active', $is_active);
                    update_user_meta($u_id, 'block_msg_permenant', $block_msg_permenant);
                }
            }
        }
    }

}

/**
 *
 * @param type $vars
 * @return query Adding group by in get user query
 */
if (!function_exists('ublk_sort_by_member_number')) {

    function ublk_sort_by_member_number($vars) {
        $vars->query_orderby = 'group by user_login ' . $vars->query_orderby;
    }

}

/**
 *
 * @param type $user_id
 * @return show all block data view
 */
if (!function_exists('ublk_all_block_data_view')) {

    function ublk_all_block_data_view($user_id) {
        $is_active = get_user_meta($user_id, 'is_active', true);
        $block_day = get_user_meta($user_id, 'block_day', true);
        $block_date = get_user_meta($user_id, 'block_date', true);
        if ($is_active == 'n') {
            ?>
            <img src="<?php echo plugins_url() . '/user-blocker/images/inactive.png'; ?>" title="<?php _e('Permanently Blocked', 'user-blocker'); ?>" />
            <?php
        } else {
            ?>
            <a data-href='<?php echo $user_id; ?>' href='' class="view_block_data">
                <img src="<?php echo plugins_url() . '/user-blocker/images/view.png'; ?>" title="<?php _e('View Block Date Time', 'user-blocker'); ?>" />
            </a>
            <?php
        }
    }

}

/**
 *
 * @param type $key
 * @return type show all block data view role
 */
if (!function_exists('ublk_all_block_data_view_role')) {

    function ublk_all_block_data_view_role($key) {
        $is_active = get_option($key . '_is_active');
        $block_day = get_option($key . '_block_day');
        $block_date = get_option($key . '_block_date');
        if ($is_active == 'n') {
            ?>
            <img src="<?php echo plugins_url() . '/user-blocker/images/inactive.png'; ?>" title="<?php _e('Permanently Blocked', 'user-blocker'); ?>" />
            <?php
        } else {
            ?>
            <a href='' class="view_block_data">
                <img src="<?php echo plugins_url() . '/user-blocker/images/view.png'; ?>" title="<?php _e('View Block Date Time', 'user-blocker'); ?>" />
            </a>
            <?php
        }
    }

}

/**
 *
 * @param type $user_id
 * @return type show all block data table
 */
if (!function_exists('ublk_all_block_data_table')) {

    function ublk_all_block_data_table($user_id) {
        $is_active = get_user_meta($user_id, 'is_active', true);
        $block_day = get_user_meta($user_id, 'block_day', true);
        $block_date = get_user_meta($user_id, 'block_date', true);
        if ($is_active != 'n') {
            ?>
            <tr id='view_block_day_tr_<?php echo $user_id; ?>' class="view_block_data_tr">
                <td colspan="7" class='date_detail_row'>
                    <table class="view_block_table form-table tbl-timing">
                        <tbody>
                            <?php
                            if (isset($block_day) && !empty($block_day) && $block_day != '') {
                                ?>
                                <tr>
                                    <td colspan='7'>
                                        <label><?php _e('Blocked Day Detail', 'user-blocker'); ?></label>
                                    </td>
                                </tr>
                                <tr>
                                    <th align="center"><?php _e('Sunday', 'user-blocker'); ?></th>
                                    <th align="center"><?php _e('Monday', 'user-blocker'); ?></th>
                                    <th align="center"><?php _e('Tuesday', 'user-blocker'); ?></th>
                                    <th align="center"><?php _e('Wednesday', 'user-blocker'); ?></th>
                                    <th align="center"><?php _e('Thursday', 'user-blocker'); ?></th>
                                    <th align="center"><?php _e('Friday', 'user-blocker'); ?></th>
                                    <th align="center"><?php _e('Saturday', 'user-blocker'); ?></th>
                                </tr>
                                <tr>
                                    <td align="center"><?php ublk_get_time_record('sunday', $block_day); ?></td>
                                    <td align="center"><?php ublk_get_time_record('monday', $block_day); ?></td>
                                    <td align="center"><?php ublk_get_time_record('tuesday', $block_day); ?></td>
                                    <td align="center"><?php ublk_get_time_record('wednesday', $block_day); ?></td>
                                    <td align="center"><?php ublk_get_time_record('thursday', $block_day); ?></td>
                                    <td align="center"><?php ublk_get_time_record('friday', $block_day); ?></td>
                                    <td align="center"><?php ublk_get_time_record('saturday', $block_day); ?></td>
                                </tr>
                                <?php
                            }
                            if (isset($block_date) && !empty($block_date) && $block_date != '') {
                                ?>
                                <tr>
                                    <td class="" colspan='7'>
                                        <label><?php _e('Blocked Date Detail:', 'user-blocker'); ?></label>
                                        <?php echo ublk_dateTimeToTwelveHour($block_date['frmdate']) . ' ' . __('to', 'user-blocker') . ' ' . ublk_dateTimeToTwelveHour($block_date['todate']); ?>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </td>
            </tr>
            <?php
        }
    }

}

/**
 *
 * @param type $key
 * @return type show all block data table role
 */
if (!function_exists('ublk_all_block_data_table_role')) {

    function ublk_all_block_data_table_role($key) {
        $is_active = get_option($key . '_is_active');
        $block_day = get_option($key . '_block_day');
        $block_date = get_option($key . '_block_date');
        if ($is_active != 'n') {
            ?>
            <tr id='view_block_day_tr_<?php echo $key; ?>' class="view_block_data_tr">
                <td colspan="3" class='date_detail_row'>
                    <table class="view_block_table form-table tbl-timing">
                        <tbody>
                            <?php
                            if (isset($block_day) && !empty($block_day) && $block_day != '') {
                                ?>
                                <tr>
                                    <td colspan='7'>
                                        <label><?php _e('Blocked Day Detail', 'user-blocker'); ?></label>
                                    </td>
                                </tr>
                                <tr>
                                    <th align="center"><?php _e('Sunday', 'user-blocker'); ?></th>
                                    <th align="center"><?php _e('Monday', 'user-blocker'); ?></th>
                                    <th align="center"><?php _e('Tuesday', 'user-blocker'); ?></th>
                                    <th align="center"><?php _e('Wednesday', 'user-blocker'); ?></th>
                                    <th align="center"><?php _e('Thursday', 'user-blocker'); ?></th>
                                    <th align="center"><?php _e('Friday', 'user-blocker'); ?></th>
                                    <th align="center"><?php _e('Saturday', 'user-blocker'); ?></th>
                                </tr>
                                <tr>
                                    <td align="center"><?php ublk_get_time_record('sunday', $block_day); ?></td>
                                    <td align="center"><?php ublk_get_time_record('monday', $block_day); ?></td>
                                    <td align="center"><?php ublk_get_time_record('tuesday', $block_day); ?></td>
                                    <td align="center"><?php ublk_get_time_record('wednesday', $block_day); ?></td>
                                    <td align="center"><?php ublk_get_time_record('thursday', $block_day); ?></td>
                                    <td align="center"><?php ublk_get_time_record('friday', $block_day); ?></td>
                                    <td align="center"><?php ublk_get_time_record('saturday', $block_day); ?></td>
                                </tr>
                                <?php
                            }
                            if (isset($block_date) && !empty($block_date) && $block_date != '') {
                                ?>
                                <tr>
                                    <td class="" colspan='7'>
                                        <label><?php _e('Blocked Date Detail:', 'user-blocker'); ?></label>
                                        <?php echo ublk_dateTimeToTwelveHour($block_date['frmdate']) . ' ' . __('to', 'user-blocker') . ' ' . ublk_dateTimeToTwelveHour($block_date['todate']); ?>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </td>
            </tr>
            <?php
        }
    }

}

/**
 *
 * @param type $user_id
 * @return type Message of all block data
 */
if (!function_exists('ublk_all_block_data_msg')) {

    function ublk_all_block_data_msg($user_id) {
        $is_active = get_user_meta($user_id, 'is_active', true);
        $block_day = get_user_meta($user_id, 'block_day', true);
        $block_date = get_user_meta($user_id, 'block_date', true);
        if ($is_active == 'n') {
            echo ublk_disp_msg(get_user_meta($user_id, 'block_msg_permenant', true));
        } else {
            if (isset($block_day) && !empty($block_day) && $block_day != '') {
                echo ublk_disp_msg(get_user_meta($user_id, 'block_msg_day', true));
            }
            if (isset($block_date) && !empty($block_date) && $block_date != '') {
                echo ublk_disp_msg(get_user_meta($user_id, 'block_msg_date', true));
            }
        }
    }

}

/**
 *
 * @param type $key
 * @return all block data message role
 */
if (!function_exists('ublk_all_block_data_msg_role')) {

    function ublk_all_block_data_msg_role($key) {
        $is_active = get_option($key . '_is_active');
        $block_day = get_option($key . '_block_day');
        $block_date = get_option($key . '_block_date');
        if ($is_active == 'n') {
            echo ublk_disp_msg(get_option($key . '_block_msg_permenant'));
        } else {
            if (isset($block_day) && !empty($block_day) && $block_day != '') {
                echo ublk_disp_msg(get_option($key . '_block_msg_day'));
            }
            if (isset($block_date) && !empty($block_date) && $block_date != '') {
                echo ublk_disp_msg(get_option($key . '_block_msg_date'));
            }
        }
    }

}

/**
 *
 * @param type $data
 * @param type $default_val
 * @return type Get data
 */
if (!function_exists('ublk_get_data')) {

    function ublk_get_data($data, $default_val = '') {
        $return_val = '';
        if ($data != '') {
            if (isset($_GET[$data]) && $_GET[$data] != '')
                $return_val = $_GET[$data];
            else if (isset($_POST[$data]) && $_POST[$data] != '')
                $return_val = $_POST[$data];
            else
                $return_val = $default_val;
        }
        return $return_val;
    }

}

/**
 * WP-MEMBERS plugin change login filed message for widget login
 */
if (!function_exists('ublk_wp_member_plugin_login_failed_sb_msg')) {

    function ublk_wp_member_plugin_login_failed_sb_msg($str) {
        // The generated html for the login failed message
        // is passed to this filter as $str and includes the
        // formatting tags. You can change it or append to it.
        if ($_POST['log'] && $_POST['pwd']) {
            // Get username and sanitize.
            $user_login = sanitize_user($_POST['log']);
            // Assemble login credentials.
            $creds = array();
            $creds['user_login'] = $user_login;
            $creds['user_password'] = trim( wp_unslash( $_POST['pwd'] ) );
            $user = wp_signon($creds, is_ssl());
            if (isset($user->errors['invalid_username'][0])) {
                return $str;
            }
            if (isset($user->errors['authentication_failed'][0])) {
                $logfail = __('Login Failed!', 'user-blocker');
                $str = "<p class='err'>$logfail<br>" . $user->errors['authentication_failed'][0] . "</p>";
                return $str;
            }
        }
        return $str;
    }

}
add_filter('wpmem_login_failed_sb', 'ublk_wp_member_plugin_login_failed_sb_msg');

/**
 * WP-MEMBERS plugin change login failed message for login shortcode
 */
if (!function_exists('ublk_wp_member_plugin_login_failed_msg')) {

    function ublk_wp_member_plugin_login_failed_msg($str) {
        // The generated html for the login failed message
        // is passed to this filter as $str and includes the
        // formatting tags. You can change it or append to it.
        if ($_POST['log'] && $_POST['pwd']) {
            // Get username and sanitize.
            $user_login = sanitize_user($_POST['log']);
            // Assemble login credentials.
            $creds = array();
            $creds['user_login'] = $user_login;
            $creds['user_password'] = trim( wp_unslash( $_POST['pwd'] ) );
            $user = wp_signon($creds, is_ssl());
            if (isset($user->errors['invalid_username'][0])) {
                return $str;
            }
            if (isset($user->errors['authentication_failed'][0])) {
                $logfail = __('Login Failed!', 'user-blocker');
                $str = "<div align='center' id='wpmem_msg'>
                            <h2>$logfail</h2>
                            <p>" . $user->errors['authentication_failed'][0] . "</p>
                        </div>";
                return $str;
            }
        }
        return $str;
    }

}
add_filter('wpmem_login_failed', 'ublk_wp_member_plugin_login_failed_msg');

/**
 * Display links
 */
if (!function_exists('ublk_plugin_links')) {

    function ublk_plugin_links($links) {
        $links[] = '<a class="documentation_ublk_plugin" target="_blank" href="' . esc_url('https://www.solwininfotech.com/documents/wordpress/user-blocker/') . '">' . __('Documentation', 'user-blocker') . '</a>';
        return $links;
    }

}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'ublk_plugin_links');


/**
 *
 * @return Loads plugin textdomain
 */
if (!function_exists('ublk_load_text_domain')) {

    function ublk_load_text_domain() {

        if( in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) ) {
            unload_textdomain('default');
        }

        load_plugin_textdomain('user-blocker', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

}

/**
 * Submit optin data
 */
add_action('wp_ajax_ublk_submit_optin','ublk_submit_optin');
if(!function_exists('ublk_submit_optin')) {
    function ublk_submit_optin() {
        global $wpdb, $wp_version;
        $ublk_submit_type = '';
        if(isset($_POST['email'])) {
            $ublk_email = sanitize_email($_POST['email']);
        }
        else {
            $ublk_email = get_option('admin_url');
        }
        if(isset($_POST['type'])) {
            $ublk_submit_type = sanitize_text_field($_POST['type']);
        }
        if($ublk_submit_type == 'submit') {
            $status_type = get_option('ublk_is_optin');
            $theme_details = array();
            if ( $wp_version >= 3.4 ) {
                $active_theme                   = wp_get_theme();
                $theme_details['theme_name']    = strip_tags( $active_theme->name );
                $theme_details['theme_version'] = strip_tags( $active_theme->version );
                $theme_details['author_url']    = strip_tags( $active_theme->{'Author URI'} );
            }
            $active_plugins = (array) get_option( 'active_plugins', array() );
            if (is_multisite()) {
                $active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
            }
            $plugins = array();
            if (count($active_plugins) > 0) {
                $get_plugins = array();
                foreach ($active_plugins as $plugin) {
                    $plugin_data = @get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);

                    $get_plugins['plugin_name'] = strip_tags($plugin_data['Name']);
                    $get_plugins['plugin_author'] = strip_tags($plugin_data['Author']);
                    $get_plugins['plugin_version'] = strip_tags($plugin_data['Version']);
                    array_push($plugins, $get_plugins);
                }
            }

            $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/user-blocker/user_blocker.php', $markup = true, $translate = true);
            $current_version = $plugin_data['Version'];

            $plugin_data = array();
            $plugin_data['plugin_name'] = 'User Blocker';
            $plugin_data['plugin_slug'] = 'user-blocker';
            $plugin_data['plugin_version'] = $current_version;
            $plugin_data['plugin_status'] = $status_type;
            $plugin_data['site_url'] = home_url();
            $plugin_data['site_language'] = defined( 'WPLANG' ) && WPLANG ? WPLANG : get_locale();
            $current_user = wp_get_current_user();
            $f_name = $current_user->user_firstname;
            $l_name = $current_user->user_lastname;
            $plugin_data['site_user_name'] = esc_attr( $f_name ).' '.esc_attr( $l_name );
            $plugin_data['site_email'] = false !== $ublk_email ? esc_attr($ublk_email) : get_option( 'admin_email' );
            $plugin_data['site_wordpress_version'] = $wp_version;
            $plugin_data['site_php_version'] = esc_attr( phpversion() );
            $plugin_data['site_mysql_version'] = $wpdb->db_version();
            $plugin_data['site_max_input_vars'] = ini_get( 'max_input_vars' );
            $plugin_data['site_php_memory_limit'] = ini_get( 'max_input_vars' );
            $plugin_data['site_operating_system'] = ini_get( 'memory_limit' ) ? ini_get( 'memory_limit' ) : 'N/A';
            $plugin_data['site_extensions']       = get_loaded_extensions();
            $plugin_data['site_activated_plugins'] = $plugins;
            $plugin_data['site_activated_theme'] = $theme_details;
            $url = 'https://www.solwininfotech.com/analytics/';
            $response = wp_safe_remote_post(
                $url, array(
                    'method'      => 'POST',
                    'timeout'     => 45,
                    'redirection' => 5,
                    'httpversion' => '1.0',
                    'blocking'    => true,
                    'headers'     => array(),
                    'body'        => array(
                        'data'    => maybe_serialize( $plugin_data ),
                        'action'  => 'plugin_analysis_data',
                    ),
                )
            );
            update_option( 'ublk_is_optin', 'yes' );
        }
        elseif($ublk_submit_type == 'cancel') {
            update_option( 'ublk_is_optin', 'no' );
        }
        elseif($ublk_submit_type == 'deactivate') {
            $status_type = get_option('ublk_is_optin');
            $theme_details = array();
            if ( $wp_version >= 3.4 ) {
                $active_theme                   = wp_get_theme();
                $theme_details['theme_name']    = strip_tags( $active_theme->name );
                $theme_details['theme_version'] = strip_tags( $active_theme->version );
                $theme_details['author_url']    = strip_tags( $active_theme->{'Author URI'} );
            }
            $active_plugins = (array) get_option( 'active_plugins', array() );
            if (is_multisite()) {
                $active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
            }
            $plugins = array();
            if (count($active_plugins) > 0) {
                $get_plugins = array();
                foreach ($active_plugins as $plugin) {
                    $plugin_data = @get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);
                    $get_plugins['plugin_name'] = strip_tags($plugin_data['Name']);
                    $get_plugins['plugin_author'] = strip_tags($plugin_data['Author']);
                    $get_plugins['plugin_version'] = strip_tags($plugin_data['Version']);
                    array_push($plugins, $get_plugins);
                }
            }

            $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/user-blocker/user_blocker.php', $markup = true, $translate = true);
            $current_version = $plugin_data['Version'];

            $plugin_data = array();
            $plugin_data['plugin_name'] = 'User Blocker';
            $plugin_data['plugin_slug'] = 'user-blocker';
            $reason_id = sanitize_text_field($_POST['selected_option_de']);
            $plugin_data['deactivation_option'] = $reason_id;
            $plugin_data['deactivation_option_text'] = sanitize_text_field($_POST['selected_option_de_text']);
            if ($reason_id == 7) {
                $plugin_data['deactivation_option_text'] = sanitize_text_field($_POST['selected_option_de_other']);
            }
            $plugin_data['plugin_version'] = $current_version;
            $plugin_data['plugin_status'] = $status_type;
            $plugin_data['site_url'] = home_url();
            $plugin_data['site_language'] = defined( 'WPLANG' ) && WPLANG ? WPLANG : get_locale();
            $current_user = wp_get_current_user();
            $f_name = $current_user->user_firstname;
            $l_name = $current_user->user_lastname;
            $plugin_data['site_user_name'] = esc_attr( $f_name ).' '.esc_attr( $l_name );
            $plugin_data['site_email'] = false !== $ublk_email ? esc_attr($ublk_email) : get_option( 'admin_email' );
            $plugin_data['site_wordpress_version'] = $wp_version;
            $plugin_data['site_php_version'] = esc_attr( phpversion() );
            $plugin_data['site_mysql_version'] = $wpdb->db_version();
            $plugin_data['site_max_input_vars'] = ini_get( 'max_input_vars' );
            $plugin_data['site_php_memory_limit'] = ini_get( 'max_input_vars' );
            $plugin_data['site_operating_system'] = ini_get( 'memory_limit' ) ? ini_get( 'memory_limit' ) : 'N/A';
            $plugin_data['site_extensions']       = get_loaded_extensions();
            $plugin_data['site_activated_plugins'] = $plugins;
            $plugin_data['site_activated_theme'] = $theme_details;
            $url = 'https://www.solwininfotech.com/analytics/';
            $response = wp_safe_remote_post(
                $url, array(
                    'method'      => 'POST',
                    'timeout'     => 45,
                    'redirection' => 5,
                    'httpversion' => '1.0',
                    'blocking'    => true,
                    'headers'     => array(),
                    'body'        => array(
                        'data'    => maybe_serialize( $plugin_data ),
                        'action'  => 'plugin_analysis_data_deactivate',
                    ),
                )
            );
            update_option( 'ublk_is_optin', '' );
        }
        exit();
    }
}

/**
 * Redirection on welcome page
 */
add_action('activated_plugin', 'ublk_activated_plugin');
if(!function_exists('ublk_activated_plugin')) {
    function ublk_activated_plugin($plugin) {
        if( $plugin == plugin_basename( __FILE__ ) ) {
            $ublk_is_optin = get_option('ublk_is_optin');
            if($ublk_is_optin == 'yes' || $ublk_is_optin == 'no') {
                exit( wp_redirect( admin_url( 'admin.php?page=block_user' ) ) );
            }
            else {
                exit( wp_redirect( admin_url( 'admin.php?page=welcome_block_user' ) ) );
            }
        }
    }
}

/**
 * Delete optin on deactivation of plugin
 */
register_deactivation_hook(__FILE__, 'ublk_UpdateOptin');
if (!function_exists('ublk_UpdateOptin')) {

    function ublk_UpdateOptin() {
        update_option('ublk_is_optin','');
    }

}