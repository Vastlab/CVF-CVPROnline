<?php
/**
 * Exit if accessed directly
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 *
 * @global type $wpdb
 * @global type $wp_roles
 * @return html Display block user list
 */
if (!function_exists('ublk_block_user_list_page')) {

    function ublk_block_user_list_page() {
        global $wpdb;
        global $wp_roles;
        $txtUsername        = '';
        $role               = '';
        $srole              = '';
        $msg_class          = '';
        $msg                = '';
        $total_pages        = '';
        $next_page          = '';
        $prev_page          = '';
        $search_arg         = '';
        $records_per_page   = 10;
        $paged              = 1;
        $sr_no              = 1;
        $orderby            = 'user_login';
        $order              = 'ASC';
        
        $msg = (isset($_GET['msg']) && $_GET['msg'] != '') ? esc_attr($_GET['msg']) : '';
        $msg_class = (isset($_GET['msg_class']) && $_GET['msg_class'] != '') ? esc_attr($_GET['msg_class']) : '';
        $orderby = (isset($_GET['orderby']) && $_GET['orderby'] != '') ? esc_attr($_GET['orderby']) : 'user_login';
        $order = (isset($_GET['order']) && $_GET['order'] != '') ? esc_attr($_GET['order']) : 'ASC';
        $paged = isset($_GET['paged']) ? esc_attr($_GET['paged']) : 1;
        
        if (!is_numeric($paged))
            $paged = 1;
        if (isset($_REQUEST['filter_action'])) {
            if ($_REQUEST['filter_action'] == 'Search') {
                $paged = 1;
            }
        }
        
        $offset = ($paged - 1) * $records_per_page;
        //Only for roles
        $get_roles = $wp_roles->roles;
        //Reset users
        if (isset($_GET['reset']) && $_GET['reset'] == '1') {
            if (isset($_GET['username']) && $_GET['username'] != '') {
                $r_username = esc_attr( $_GET['username'] );
                $user_data = new WP_User($r_username);
                if (get_userdata($r_username) != false) {
                    delete_user_meta($r_username, 'block_day');
                    delete_user_meta($r_username, 'block_msg_day');
                    $msg_class = 'updated';
                    $msg = $user_data->user_login . '\'s ' . __('blocking time is successfully reset.', 'user-blocker');
                } else {
                    $msg_class = 'error';
                    $msg = __('Invalid user for reset blocking time.','user-blocker');
                }
            }
            if (isset($_GET['role']) && $_GET['role'] != '') {
                $reset_roles = get_users(array('role' => $_GET['role']));
                if (!empty($reset_roles)) {
                    foreach ($reset_roles as $single_reset_role) {
                        $own_value = get_user_meta($single_reset_role->ID, 'block_day', true);
                        $role_value = get_option(esc_attr($_GET['role']) . '_block_day');
                        $own_msg = get_user_meta($single_reset_role->ID, 'block_msg_day', true);
                        $role_msg = get_option(esc_attr($_GET['role']) . '_block_msg_day');
                        if ($own_value == $role_value && $own_msg == $role_msg) {
                            delete_user_meta($single_reset_role->ID, 'block_day');
                            delete_user_meta($single_reset_role->ID, 'block_msg_day');
                        }
                    }
                }
                delete_option(esc_attr($_GET['role']) . '_block_day');
                delete_option(esc_attr($_GET['role']) . '_block_msg_day');
                $msg_class = 'updated';
                $msg = esc_attr($_GET['role']) . '\'s '.__('blocking time is successfully reset.','user-blocker');
            }
        }
        if (isset($_GET['txtUsername']) && trim($_GET['txtUsername']) != '') {
            $txtUsername = esc_attr($_GET['txtUsername']);
            $filter_ary['search'] = '*' . $txtUsername . '*';
            $filter_ary['search_columns'] = array(
                'user_login',
                'user_nicename',
                'user_email',
                'display_name'
            );
        }
        if ($txtUsername == '') {
            if (isset($_GET['role']) && $_GET['role'] != '' && !isset($_GET['reset'])) {
                $filter_ary['role'] = esc_attr($_GET['role']);
                $srole = esc_attr($_GET['role']);
            }
        }
        //end
        if ((isset($_GET['display']) && $_GET['display'] == 'roles') || (isset($_GET['role']) && $_GET['role'] != '' && isset($_GET['reset']) && $_GET['reset'] == '1') || (isset($_GET['role_edited']) && $_GET['role_edited'] != '' && isset($_GET['msg']) && $_GET['msg'] != '')) {
            $display = "roles";
        } else {
            $display = "users";
        }
        add_filter('pre_user_query', 'ublk_sort_by_member_number');
        $meta_query_array[] = array('relation' => 'AND');
        $meta_query_array[] = array('key' => 'block_day');
        $meta_query_array[] = array(
            array(
                'relation' => 'OR',
                array(
                    'key' => 'is_active',
                    'compare' => 'NOT EXISTS'
                ),
                array(
                    'key' => 'is_active',
                    'value' => 'n',
                    'compare' => '!='
                )
            )
        );
        $filter_ary['orderby'] = $orderby;
        $filter_ary['order'] = $order;
        $filter_ary['meta_query'] = $meta_query_array;
        //Query for counting results
        $get_users_u1 = new WP_User_Query($filter_ary);
        $total_items = $get_users_u1->total_users;
        $total_pages = ceil($total_items / $records_per_page);
        $next_page = (int) $paged + 1;
        if ($next_page > $total_pages)
            $next_page = $total_pages;
        $filter_ary['number'] = $records_per_page;
        $filter_ary['offset'] = $offset;
        $prev_page = (int) $paged - 1;
        if ($prev_page < 1)
            $prev_page = 1;
        if (isset($paged) && $paged > 1) {
            $sr_no = ( $records_per_page * ( $paged - 1 ) + 1);
        }
        //Main query
        $get_users_u = new WP_User_Query($filter_ary);
        remove_filter('pre_user_query', 'ublk_sort_by_member_number');
        $get_users = $get_users_u->get_results();
        ?>
        <div class="wrap" id="blocked-list">
            <h2 class="ublocker-page-title"><?php _e('Blocked User list', 'user-blocker') ?></h2>
            <?php
            //Display success/error messages
            if ($msg != '') {
                ?>
                <div class="ublocker-notice <?php echo $msg_class; ?>">
                    <p><?php echo $msg; ?></p>
                </div>
            <?php } ?>
            <?php if (isset($_SESSION['success_msg'])) { ?>
                <div class="updated is-dismissible notice settings-error">
                    <p><?php echo $_SESSION['success_msg']; ?></p>
                    <?php
                    unset($_SESSION['success_msg']);
                    ?></div>
            <?php } ?>
            <div class="tab_parent_parent">
                <div class="tab_parent">
                    <ul>
                        <li><a href="?page=blocked_user_list" class="current"><?php _e('Blocked User List By Time', 'user-blocker'); ?></a></li>
                        <li><a href="?page=datewise_blocked_user_list"><?php _e('Blocked User List By Date', 'user-blocker'); ?></a></li>
                        <li><a href="?page=permanent_blocked_user_list"><?php _e('Blocked User List Permanently', 'user-blocker'); ?></a></li>
                        <li><a href="?page=all_type_blocked_user_list"><?php _e('Blocked User List', 'user-blocker'); ?></a></li>
                    </ul>
                </div>
            </div>
            <div class="cover_form">
                <div class="search_box">
                    <div class="tablenav top">
                        <form id="frmSearch" name="frmSearch" method="get" action="<?php echo home_url() . '/wp-admin/admin.php'; ?>">
                            <div class="actions">
                                <?php
                                ublk_blocked_user_category_dropdown($display);
                                ublk_blocked_role_selection_dropdown($display, $get_roles, $srole);
                                ublk_blocked_pagination($total_pages, $total_items, $paged, $prev_page, $next_page, $srole, $txtUsername, $orderby, $order, 'blocked_user_list');
                                ?>
                            </div>
                            <?php ublk_search_field($display, $txtUsername, 'blocked_user_list'); ?>
                        </form>
                    </div>
                </div>
                <table class="widefat post role_records striped" <?php if ($display == 'roles') echo 'style="display: table"'; ?>>
                    <thead>
                        <tr>
                            <th class="th-role"><?php _e('Role', 'user-blocker'); ?></th>
                            <th class="th-time"><?php _e('Sunday', 'user-blocker'); ?></th>
                            <th class="th-time"><?php _e('Monday', 'user-blocker'); ?></th>
                            <th class="th-time"><?php _e('Tuesday', 'user-blocker'); ?></th>
                            <th class="th-time"><?php _e('Wednesday', 'user-blocker'); ?></th>
                            <th class="th-time"><?php _e('Thursday', 'user-blocker'); ?></th>
                            <th class="th-time"><?php _e('Friday', 'user-blocker'); ?></th>
                            <th class="th-time"><?php _e('Saturday', 'user-blocker'); ?></th>
                            <th style="text-align:center"><?php _e('Message', 'user-blocker'); ?></th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th class="th-role"><?php _e('Role', 'user-blocker'); ?></th>
                            <th class="th-time"><?php _e('Sunday', 'user-blocker'); ?></th>
                            <th class="th-time"><?php _e('Monday', 'user-blocker'); ?></th>
                            <th class="th-time"><?php _e('Tuesday', 'user-blocker'); ?></th>
                            <th class="th-time"><?php _e('Wednesday', 'user-blocker'); ?></th>
                            <th class="th-time"><?php _e('Thursday', 'user-blocker'); ?></th>
                            <th class="th-time"><?php _e('Friday', 'user-blocker'); ?></th>
                            <th class="th-time"><?php _e('Saturday', 'user-blocker'); ?></th>
                            <th style="text-align:center"><?php _e('Message', 'user-blocker'); ?></th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        $no_data = 1;
                        if ($get_roles) {
                            $k = 1;
                            foreach ($get_roles as $key => $value) {
                                $block_day = get_option($key . '_block_day');
                                $block_permenant = get_option($key . '_block_permenant');
                                if ($k % 2 == 0)
                                    $alt_class = 'alt';
                                else
                                    $alt_class = '';
                                if (($key == 'administrator') || ( $block_day == '' ) || ($block_permenant != ''))
                                    continue;
                                $no_data = 0;
                                ?>
                                <tr class="<?php echo $alt_class; ?>">
                                    <td class="user-role"><?php echo $value['name']; ?>
                                        <div class="row-actions">
                                            <span class="trash">
                                                <a title="<?php _e('Reset this item', 'user-blocker'); ?>" href="?page=blocked_user_list&reset=1&role=<?php echo $key; ?>">
                                                    <?php _e('Reset', 'user-blocker'); ?>
                                                </a>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                        $block_day = get_option($key . '_block_day');
                                        if (isset($block_day) && !empty($block_day) && $block_day != '') {
                                            if (array_key_exists('sunday', $block_day)) {
                                                $from_time = $block_day['sunday']['from'];
                                                $to_time = $block_day['sunday']['to'];
                                                if ($from_time == '') {
                                                    echo __('not set', 'user-blocker');
                                                } else {
                                                    echo ublk_timeToTwelveHour($from_time);
                                                }
                                                if ($from_time != '' && $to_time != '') {
                                                    echo __(' to ', 'user-blocker') . ublk_timeToTwelveHour($to_time);
                                                }
                                            } else {
                                                echo __('not set', 'user-blocker');
                                            }
                                        } else {
                                            echo __('not set', 'user-blocker');
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (!empty($block_day)) {
                                            if (array_key_exists('monday', $block_day)) {
                                                $from_time = $block_day['monday']['from'];
                                                $to_time = $block_day['monday']['to'];
                                                if ($from_time == '') {
                                                    echo __('not set', 'user-blocker');
                                                } else {
                                                    echo ublk_timeToTwelveHour($from_time);
                                                }
                                                if ($from_time != '' && $to_time != '') {
                                                    echo __(' to ', 'user-blocker') . ublk_timeToTwelveHour($to_time);
                                                }
                                            } else {
                                                echo __('not set', 'user-blocker');
                                            }
                                        } else {
                                            echo __('not set', 'user-blocker');
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (!empty($block_day)) {
                                            if (array_key_exists('tuesday', $block_day)) {
                                                $from_time = $block_day['tuesday']['from'];
                                                $to_time = $block_day['tuesday']['to'];
                                                if ($from_time == '') {
                                                    echo __('not set', 'user-blocker');
                                                } else {
                                                    echo ublk_timeToTwelveHour($from_time);
                                                }
                                                if ($from_time != '' && $to_time != '') {
                                                    echo __(' to ', 'user-blocker') . ublk_timeToTwelveHour($to_time);
                                                }
                                            } else {
                                                echo __('not set', 'user-blocker');
                                            }
                                        } else {
                                            echo __('not set', 'user-blocker');
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (!empty($block_day)) {
                                            if (array_key_exists('wednesday', $block_day)) {
                                                $from_time = $block_day['wednesday']['from'];
                                                $to_time = $block_day['wednesday']['to'];
                                                if ($from_time == '') {
                                                    echo __('not set', 'user-blocker');
                                                } else {
                                                    echo ublk_timeToTwelveHour($from_time);
                                                }
                                                if ($from_time != '' && $to_time != '') {
                                                    echo __(' to ', 'user-blocker') . ublk_timeToTwelveHour($to_time);
                                                }
                                            } else {
                                                echo __('not set', 'user-blocker');
                                            }
                                        } else {
                                            echo __('not set', 'user-blocker');
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (!empty($block_day)) {
                                            if (array_key_exists('thursday', $block_day)) {
                                                $from_time = $block_day['thursday']['from'];
                                                $to_time = $block_day['thursday']['to'];
                                                if ($from_time == '') {
                                                    echo __('not set', 'user-blocker');
                                                } else {
                                                    echo ublk_timeToTwelveHour($from_time);
                                                }
                                                if ($from_time != '' && $to_time != '') {
                                                    echo __(' to ', 'user-blocker') . ublk_timeToTwelveHour($to_time);
                                                }
                                            } else {
                                                echo __('not set', 'user-blocker');
                                            }
                                        } else {
                                            echo __('not set', 'user-blocker');
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (!empty($block_day)) {
                                            if (array_key_exists('friday', $block_day)) {
                                                $from_time = $block_day['friday']['from'];
                                                $to_time = $block_day['friday']['to'];
                                                if ($from_time == '') {
                                                    echo __('not set', 'user-blocker');
                                                } else {
                                                    echo ublk_timeToTwelveHour($from_time);
                                                }
                                                if ($from_time != '' && $to_time != '') {
                                                    echo __(' to ', 'user-blocker') . ublk_timeToTwelveHour($to_time);
                                                }
                                            } else {
                                                echo __('not set', 'user-blocker');
                                            }
                                        } else {
                                            echo __('not set', 'user-blocker');
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (!empty($block_day)) {
                                            if (array_key_exists('saturday', $block_day)) {
                                                $from_time = $block_day['saturday']['from'];
                                                $to_time = $block_day['saturday']['to'];
                                                if ($from_time == '') {
                                                    echo __('not set', 'user-blocker');
                                                } else {
                                                    echo ublk_timeToTwelveHour($from_time);
                                                }
                                                if ($from_time != '' && $to_time != '') {
                                                    echo __(' to ', 'user-blocker') . ublk_timeToTwelveHour($to_time);
                                                }
                                            } else {
                                                echo __('not set', 'user-blocker');
                                            }
                                        } else {
                                            echo __('not set', 'user-blocker');
                                        }
                                        ?>
                                    </td>
                                    <td style="text-align:center">
                                        <?php
                                        $block_msg_day = get_option($key . '_block_msg_day');
                                        echo ublk_disp_msg($block_msg_day);
                                        ?>
                                    </td>
                                </tr>
                                <?php
                                $k++;
                            }
                            if ($no_data == 1) {
                                ?>
                                <tr><td colspan="9" style="text-align:center"><?php echo __('No records Found.', 'user-blocker'); ?></td></tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr><td colspan="9" style="text-align:center"><?php echo __('No records Found.', 'user-blocker'); ?></td></tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
                <table class="widefat post fixed users_records striped" <?php if ($display == 'roles') echo 'style="display:none"'; ?>>
                    <thead>
                        <tr>
                            <th class="sr-no"><?php _e('S.N.', 'user-blocker'); ?></th>
                            <?php
                            $linkOrder = 'ASC';
                            if (isset($order)) {
                                if ($order == 'ASC') {
                                    $linkOrder = 'DESC';
                                } else if ($order == 'DESC') {
                                    $linkOrder = 'ASC';
                                }
                            }
                            ?>
                            <th class="th-username sortable <?php echo strtolower($order); ?>">
                                <a href="?page=blocked_user_list&orderby=user_login&order=<?php echo $linkOrder; ?>&txtUsername=<?php echo $txtUsername; ?>&srole=<?php echo $srole; ?>">
                                    <span><?php _e('Username', 'user-blocker'); ?></span>
                                    <span class="sorting-indicator"></span>
                                </a>
                            </th>
                            <th class="th-role"><?php _e('Role', 'user-blocker'); ?></th>
                            <th class="th-time"><?php _e('Sunday', 'user-blocker'); ?></th>
                            <th class="th-time"><?php _e('Monday', 'user-blocker'); ?></th>
                            <th class="th-time"><?php _e('Tuesday', 'user-blocker'); ?></th>
                            <th class="th-time"><?php _e('Wednesday', 'user-blocker'); ?></th>
                            <th class="th-time"><?php _e('Thursday', 'user-blocker'); ?></th>
                            <th class="th-time"><?php _e('Friday', 'user-blocker'); ?></th>
                            <th class="th-time"><?php _e('Saturday', 'user-blocker'); ?></th>
                            <th style="text-align:center"><?php _e('Message', 'user-blocker'); ?></th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th class="sr-no"><?php _e('S.N.', 'user-blocker'); ?></th>
                            <?php
                            $linkOrder = 'ASC';
                            if (isset($order)) {
                                if ($order == 'ASC') {
                                    $linkOrder = 'DESC';
                                } else if ($order == 'DESC') {
                                    $linkOrder = 'ASC';
                                }
                            }
                            ?>
                            <th class="th-username sortable <?php echo strtolower($order); ?>">
                                <a href="?page=blocked_user_list&orderby=user_login&order=<?php echo $linkOrder; ?>&txtUsername=<?php echo $txtUsername; ?>&srole=<?php echo $srole; ?>">
                                    <span><?php _e('Username', 'user-blocker'); ?></span>
                                    <span class="sorting-indicator"></span>
                                </a>
                            </th>
                            <th class="th-role"><?php _e('Role', 'user-blocker'); ?></th>
                            <th class="th-time"><?php _e('Sunday', 'user-blocker'); ?></th>
                            <th class="th-time"><?php _e('Monday', 'user-blocker'); ?></th>
                            <th class="th-time"><?php _e('Tuesday', 'user-blocker'); ?></th>
                            <th class="th-time"><?php _e('Wednesday', 'user-blocker'); ?></th>
                            <th class="th-time"><?php _e('Thursday', 'user-blocker'); ?></th>
                            <th class="th-time"><?php _e('Friday', 'user-blocker'); ?></th>
                            <th class="th-time"><?php _e('Saturday', 'user-blocker'); ?></th>
                            <th style="text-align:center"><?php _e('Message', 'user-blocker'); ?></th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        if ($get_users) {
                            foreach ($get_users as $user) {
                                if ($sr_no % 2 == 0)
                                    $alt_class = 'alt';
                                else
                                    $alt_class = '';
                                ?>
                                <tr class="<?php echo $alt_class; ?>">
                                    <td align="center"><?php echo $sr_no; ?></td>
                                    <td><?php echo $user->user_login; ?>
                                        <div class="row-actions">
                                            <span class="trash">
                                                <a title="<?php _e('Reset this item', 'user-blocker'); ?>" href="?page=blocked_user_list&reset=1&paged=<?php echo $paged; ?>&username=<?php echo $user->ID; ?>&role=<?php echo $srole; ?>&txtUsername=<?php echo $txtUsername; ?>&orderby=<?php echo $orderby; ?>&order=<?php echo $order; ?>">
                                                    <?php _e('Reset', 'user-blocker'); ?>
                                                </a>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="user-role"><?php echo ucfirst(str_replace('_', ' ', $user->roles[0])); ?></td>
                                    <td>
                                        <?php
                                        $block_day = get_user_meta($user->ID, 'block_day', true);
                                        if ($block_day == '' || $block_day == '0') {
                                            $block_day = get_option($user->roles[0] . '_block_day');
                                        }
                                        if (!empty($block_day)) {
                                            if (array_key_exists('sunday', $block_day)) {
                                                $from_time = $block_day['sunday']['from'];
                                                $to_time = $block_day['sunday']['to'];
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
                                        } else {
                                            echo __('not set', 'user-blocker');
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (!empty($block_day)) {
                                            if (array_key_exists('monday', $block_day)) {
                                                $from_time = $block_day['monday']['from'];
                                                $to_time = $block_day['monday']['to'];
                                                if ($from_time == '') {
                                                    echo __('not set', 'user-blocker');
                                                } else {
                                                    echo ublk_timeToTwelveHour($from_time);
                                                }
                                                if ($from_time != '' && $to_time != '') {
                                                    echo __(' to ', 'user-blocker') . ublk_timeToTwelveHour($to_time);
                                                }
                                            } else {
                                                echo __('not set', 'user-blocker');
                                            }
                                        } else {
                                            echo __('not set', 'user-blocker');
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (!empty($block_day)) {
                                            if (array_key_exists('tuesday', $block_day)) {
                                                $from_time = $block_day['tuesday']['from'];
                                                $to_time = $block_day['tuesday']['to'];
                                                if ($from_time == '') {
                                                    echo __('not set', 'user-blocker');
                                                } else {
                                                    echo ublk_timeToTwelveHour($from_time);
                                                }
                                                if ($from_time != '' && $to_time != '') {
                                                    echo __(' to ', 'user-blocker') . ublk_timeToTwelveHour($to_time);
                                                }
                                            } else {
                                                echo __('not set', 'user-blocker');
                                            }
                                        } else {
                                            echo __('not set', 'user-blocker');
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (!empty($block_day)) {
                                            if (array_key_exists('wednesday', $block_day)) {
                                                $from_time = $block_day['wednesday']['from'];
                                                $to_time = $block_day['wednesday']['to'];
                                                if ($from_time == '') {
                                                    echo __('not set', 'user-blocker');
                                                } else {
                                                    echo ublk_timeToTwelveHour($from_time);
                                                }
                                                if ($from_time != '' && $to_time != '') {
                                                    echo __(' to ', 'user-blocker') . ublk_timeToTwelveHour($to_time);
                                                }
                                            } else {
                                                echo __('not set', 'user-blocker');
                                            }
                                        } else {
                                            echo __('not set', 'user-blocker');
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (!empty($block_day)) {
                                            if (array_key_exists('thursday', $block_day)) {
                                                $from_time = $block_day['thursday']['from'];
                                                $to_time = $block_day['thursday']['to'];
                                                if ($from_time == '') {
                                                    echo __('not set', 'user-blocker');
                                                } else {
                                                    echo ublk_timeToTwelveHour($from_time);
                                                }
                                                if ($from_time != '' && $to_time != '') {
                                                    echo __(' to ', 'user-blocker') . ublk_timeToTwelveHour($to_time);
                                                }
                                            } else {
                                                echo __('not set', 'user-blocker');
                                            }
                                        } else {
                                            echo __('not set', 'user-blocker');
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (!empty($block_day)) {
                                            if (array_key_exists('friday', $block_day)) {
                                                $from_time = $block_day['friday']['from'];
                                                $to_time = $block_day['friday']['to'];
                                                if ($from_time == '') {
                                                    echo __('not set', 'user-blocker');
                                                } else {
                                                    echo ublk_timeToTwelveHour($from_time);
                                                }
                                                if ($from_time != '' && $to_time != '') {
                                                    echo __(' to ', 'user-blocker') . ublk_timeToTwelveHour($to_time);
                                                }
                                            } else {
                                                echo __('not set', 'user-blocker');
                                            }
                                        } else {
                                            echo __('not set', 'user-blocker');
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (!empty($block_day)) {
                                            if (array_key_exists('saturday', $block_day)) {
                                                $from_time = $block_day['saturday']['from'];
                                                $to_time = $block_day['saturday']['to'];
                                                if ($from_time == '') {
                                                    echo __('not set', 'user-blocker');
                                                } else {
                                                    echo ublk_timeToTwelveHour($from_time);
                                                }
                                                if ($from_time != '' && $to_time != '') {
                                                    echo __(' to ', 'user-blocker') . ublk_timeToTwelveHour($to_time);
                                                }
                                            } else {
                                                echo __('not set', 'user-blocker');
                                            }
                                        } else {
                                            echo __('not set', 'user-blocker');
                                        }
                                        ?>
                                    </td>
                                    <td style="text-align:center">
                                        <?php
                                        $block_msg_day = get_user_meta($user->ID, 'block_msg_day', true);
                                        echo ublk_disp_msg($block_msg_day);
                                        ?>
                                    </td>
                                </tr>
                                <?php
                                $sr_no++;
                            }
                        } else {
                            ?>
                            <tr><td colspan="11" style="text-align:center"><?php _e("No records found.", "user-blocker"); ?></td></tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }

}

/**
 *
 * @global type $wpdb
 * @global type $wp_roles
 * @return html Display datewise block user list
 */
if (!function_exists('ublk_datewise_block_user_list_page')) {

    function ublk_datewise_block_user_list_page() {
        global $wpdb;
        global $wp_roles;
        $txtUsername = '';
        $role = '';
        $srole = '';
        $msg_class = '';
        $msg = '';
        $total_pages = '';
        $next_page = '';
        $prev_page = '';
        $search_arg = '';
        $records_per_page = 10;
        $paged = 1;
        $orderby = 'user_login';
        $order = 'ASC';
        
        
        $msg = (isset($_GET['msg']) && $_GET['msg'] != '') ? esc_attr($_GET['msg']) : '';
        $msg_class = (isset($_GET['msg_class']) && $_GET['msg_class'] != '') ? esc_attr($_GET['msg_class']) : '';
        $orderby = (isset($_GET['orderby']) && $_GET['orderby'] != '') ? esc_attr($_GET['orderby']) : 'user_login';
        $order = (isset($_GET['order']) && $_GET['order'] != '') ? esc_attr($_GET['order']) : 'ASC';
        $paged = isset($_GET['paged']) ? esc_attr($_GET['paged']) : 1;

        if (!is_numeric($paged))
            $paged = 1;
        if (isset($_REQUEST['filter_action'])) {
            if ($_REQUEST['filter_action'] == 'Search') {
                $paged = 1;
            }
        }
        
        $offset = ($paged - 1) * $records_per_page;
        //Only for roles
        
        $get_roles = $wp_roles->roles;
        //Reset users
        if (isset($_GET['reset']) && $_GET['reset'] == '1') {
            if (isset($_GET['username']) && $_GET['username'] != '') {
                $r_username = esc_attr($_GET['username']);
                $user_data = new WP_User($r_username);
                if (get_userdata($r_username) != false) {
                    delete_user_meta($r_username, 'block_date');
                    delete_user_meta($r_username, 'block_msg_date');
                    $msg_class = 'updated';
                    $msg = $user_data->user_login . '\'s '.__('blocking date is successfully reset.','user-blocker');
                } else {
                    $msg_class = 'error';
                    $msg = 'Invalid user for reset blocking time.';
                }
            }
            if (isset($_GET['role']) && $_GET['role'] != '') {
                $reset_roles = get_users(array('role' => esc_attr($_GET['role'])));
                if (!empty($reset_roles)) {
                    foreach ($reset_roles as $single_reset_role) {
                        $own_value = get_user_meta($single_reset_role->ID, 'block_date', true);
                        $role_value = get_option(esc_attr($_GET['role']) . '_block_date');
                        if ($own_value == $role_value) {
                            delete_user_meta($single_reset_role->ID, 'block_date');
                            delete_user_meta($single_reset_role->ID, 'block_msg_date');
                        }
                    }
                }
                delete_option(esc_attr($_GET['role']) . '_block_date');
                delete_option(esc_attr($_GET['role']) . '_block_msg_date');
            }
        }
        if (isset($_GET['txtUsername']) && trim($_GET['txtUsername']) != '') {
            $txtUsername = esc_attr($_GET['txtUsername']);
            $filter_ary['search'] = '*' . esc_attr($txtUsername) . '*';
            $filter_ary['search_columns'] = array(
                'user_login',
                'user_nicename',
                'user_email',
                'display_name'
            );
        }
        if ($txtUsername == '') {
            if (isset($_GET['role']) && $_GET['role'] != '' && !isset($_GET['reset'])) {
                $filter_ary['role'] = esc_attr($_GET['role']);
                $srole = esc_attr($_GET['role']);
            }
        }
        if ((isset($_GET['display']) && $_GET['display'] == 'roles') || (isset($_GET['role']) && $_GET['role'] != '' && isset($_GET['reset']) && $_GET['reset'] == '1') || (isset($_GET['role_edited']) && $_GET['role_edited'] != '' && isset($_GET['msg']) && $_GET['msg'] != '')) {
            $display = "roles";
        } else {
            $display = "users";
        }
        add_filter('pre_user_query', 'ublk_sort_by_member_number');
        $meta_query_array[] = array('relation' => 'AND');
        $meta_query_array[] = array('key' => 'block_date');
        $meta_query_array[] = array(
            array(
                'relation' => 'OR',
                array(
                    'key' => 'is_active',
                    'compare' => 'NOT EXISTS'
                ),
                array(
                    'key' => 'is_active',
                    'value' => 'n',
                    'compare' => '!='
                )
            )
        );
        $filter_ary['orderby'] = $orderby;
        $filter_ary['order'] = $order;
        $filter_ary['meta_query'] = $meta_query_array;
        //Query for counting results
        $get_users_u1 = new WP_User_Query($filter_ary);
        $total_items = $get_users_u1->total_users;
        $total_pages = ceil($total_items / $records_per_page);
        $next_page = (int) $paged + 1;
        if ($next_page > $total_pages)
            $next_page = $total_pages;
        $filter_ary['number'] = $records_per_page;
        $filter_ary['offset'] = $offset;
        $prev_page = (int) $paged - 1;
        if ($prev_page < 1)
            $prev_page = 1;
        $sr_no = 1;
        if (isset($paged) && $paged > 1) {
            $sr_no = ( $records_per_page * ( $paged - 1 ) + 1);
        }
        //Main query
        $get_users_u = new WP_User_Query($filter_ary);
        remove_filter('pre_user_query', 'ublk_sort_by_member_number');
        $get_users = $get_users_u->get_results();
        ?>
        <div class="wrap" id="blocked-list">
            <h2 class="ublocker-page-title"><?php _e('Date Wise Blocked User list', 'user-blocker') ?></h2>
            <?php
            //Display success/error messages
            if ($msg != '') {
                ?>
                <div class="ublocker-notice <?php echo $msg_class; ?>">
                    <p><?php echo $msg; ?></p>
                </div>
                <?php
            }
            ?>
            <div class="tab_parent_parent">
                <div class="tab_parent">
                    <ul>
                        <li><a href="?page=blocked_user_list"><?php _e('Blocked User List By Time', 'user-blocker'); ?></a></li>
                        <li><a class="current" href="?page=datewise_blocked_user_list"><?php _e('Blocked User List By Date', 'user-blocker'); ?></a></li>
                        <li><a href="?page=permanent_blocked_user_list"><?php _e('Blocked User List Permanently', 'user-blocker'); ?></a></li>
                        <li><a href="?page=all_type_blocked_user_list"><?php _e('Blocked User List', 'user-blocker'); ?></a></li>
                    </ul>
                </div>
            </div>
            <div class="cover_form">
                <div class="search_box">
                    <div class="tablenav top">
                        <form id="frmSearch" name="frmSearch" method="get" action="<?php echo home_url() . '/wp-admin/admin.php'; ?>">
                            <div class="actions">
                                <?php
                                ublk_blocked_user_category_dropdown($display);
                                ublk_blocked_role_selection_dropdown($display, $get_roles, $srole);
                                ublk_blocked_pagination($total_pages, $total_items, $paged, $prev_page, $next_page, $srole, $txtUsername, $orderby, $order, 'datewise_blocked_user_list');
                                ?>
                            </div>
                            <?php ublk_search_field($display, $txtUsername, 'datewise_blocked_user_list'); ?>
                        </form>
                    </div>
                </div>
                <table class="widefat post role_records striped" <?php if ($display == 'roles') echo 'style="display: table"'; ?>>
                    <thead>
                        <tr>
                            <th class="th-role"><?php _e('Role', 'user-blocker'); ?></th>
                            <th class="blk-date"><?php _e('Block Date', 'user-blocker'); ?></th>
                            <th style="text-align:center"><?php _e('Message', 'user-blocker'); ?></th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th class="th-role"><?php _e('Role', 'user-blocker'); ?></th>
                            <th class="blk-date"><?php _e('Block Date', 'user-blocker'); ?></th>
                            <th style="text-align:center"><?php _e('Message', 'user-blocker'); ?></th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        $no_data = 1;
                        if ($get_roles) {
                            $k = 1;
                            foreach ($get_roles as $key => $value) {
                                $block_date = get_option($key . '_block_date');
                                $block_permenant = get_option($key . '_block_permenant');
                                if ($k % 2 == 0)
                                    $alt_class = 'alt';
                                else
                                    $alt_class = '';
                                if ($key == 'administrator' || $block_date == '' || $block_permenant != '')
                                    continue;
                                $no_data = 0;
                                ?>
                                <tr class="<?php echo $alt_class; ?>">
                                    <td class="user-role"><?php echo $value['name']; ?>
                                        <div class="row-actions">
                                            <span class="trash"><a title="<?php _e('Reset this item', 'user-blocker'); ?>" href="?page=datewise_blocked_user_list&reset=1&role=<?php echo $key; ?>&orderby=<?php echo $orderby; ?>&order=<?php echo $order; ?>"><?php _e('Reset', 'user-blocker'); ?></a></span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                        if (!empty($block_date) && isset($block_date) && $block_date != '') {
                                            if (array_key_exists('frmdate', $block_date) && array_key_exists('todate', $block_date)) {
                                                $frmdate = $block_date['frmdate'];
                                                $todate = $block_date['todate'];
                                                if ($frmdate != '' && $todate != '') {
                                                    echo ublk_dateTimeToTwelveHour($frmdate) . ' ' . __('to', 'user-blocker') . ' ' . ublk_dateTimeToTwelveHour($todate);
                                                }
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td style="text-align:center">
                                        <?php
                                        $block_msg_date = get_option($key . '_block_msg_date');
                                        echo ublk_disp_msg($block_msg_date);
                                        ?>
                                    </td>
                                </tr>
                                <?php
                                $k++;
                            }
                            if ($no_data == 1) {
                                ?>
                                <tr><td colspan="3" style="text-align:center"><?php echo __('No records found.', 'user-blocker'); ?></td></tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr><td colspan="3" style="text-align:center"><?php echo __('No records found.', 'user-blocker'); ?></td></tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
                <table class="widefat post fixed users_records striped" <?php if ($display == 'roles') echo 'style="display:none"'; ?>>
                    <thead>
                        <tr>
                            <th class="sr-no"><?php _e('S.N.', 'user-blocker'); ?></th>
                            <?php
                            $linkOrder = 'ASC';
                            if (isset($order)) {
                                if ($order == 'ASC') {
                                    $linkOrder = 'DESC';
                                } else if ($order == 'DESC') {
                                    $linkOrder = 'ASC';
                                }
                            }
                            ?>
                            <th class="th-username sortable <?php echo strtolower($order); ?>">
                                <a href="?page=datewise_blocked_user_list&orderby=user_login&order=<?php echo $linkOrder; ?>&txtUsername=<?php echo $txtUsername; ?>&srole=<?php echo $srole; ?>&paged=<?php echo $paged; ?>">
                                    <span><?php _e('Username', 'user-blocker'); ?></span>
                                    <span class="sorting-indicator"></span>
                                </a>
                            </th>
                            <th class="th-name sortable <?php echo strtolower($order); ?>">
                                <a href="?page=datewise_blocked_user_list&orderby=display_name&order=<?php echo $linkOrder; ?>&txtUsername=<?php echo $txtUsername; ?>&srole=<?php echo $srole; ?>&paged=<?php echo $paged; ?>">
                                    <span><?php _e('Name', 'user-blocker'); ?></span>
                                    <span class="sorting-indicator"></span>
                                </a>
                            </th>
                            <th class="th-email sortable <?php echo strtolower($order); ?>">
                                <a href="?page=datewise_blocked_user_list&orderby=user_email&order=<?php echo $linkOrder; ?>&txtUsername=<?php echo $txtUsername; ?>&srole=<?php echo $srole; ?>&paged=<?php echo $paged; ?>">
                                    <span><?php _e('Email', 'user-blocker'); ?></span>
                                    <span class="sorting-indicator"></span>
                                </a>
                            </th>
                            <th class="th-role"><?php _e('Role', 'user-blocker'); ?></th>
                            <th class="th-time"><?php _e('Block Date', 'user-blocker'); ?></th>
                            <th style="text-align:center"><?php _e('Message', 'user-blocker'); ?></th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th class="sr-no"><?php _e('S.N.', 'user-blocker'); ?></th>
                            <?php
                            $linkOrder = 'ASC';
                            if (isset($order)) {
                                if ($order == 'ASC') {
                                    $linkOrder = 'DESC';
                                } else if ($order == 'DESC') {
                                    $linkOrder = 'ASC';
                                }
                            }
                            ?>
                            <th class="th-username sortable <?php echo strtolower($order); ?>">
                                <a href="?page=datewise_blocked_user_list&orderby=user_login&order=<?php echo $linkOrder; ?>&txtUsername=<?php echo $txtUsername; ?>&srole=<?php echo $srole; ?>&paged=<?php echo $paged; ?>">
                                    <span><?php _e('Username', 'user-blocker'); ?></span>
                                    <span class="sorting-indicator"></span>
                                </a>
                            </th>
                            <th class="th-name sortable <?php echo strtolower($order); ?>">
                                <a href="?page=datewise_blocked_user_list&orderby=display_name&order=<?php echo $linkOrder; ?>&txtUsername=<?php echo $txtUsername; ?>&srole=<?php echo $srole; ?>&paged=<?php echo $paged; ?>">
                                    <span><?php _e('Name', 'user-blocker'); ?></span>
                                    <span class="sorting-indicator"></span>
                                </a>
                            </th>
                            <th class="th-email sortable <?php echo strtolower($order); ?>">
                                <a href="?page=datewise_blocked_user_list&orderby=user_email&order=<?php echo $linkOrder; ?>&txtUsername=<?php echo $txtUsername; ?>&srole=<?php echo $srole; ?>&paged=<?php echo $paged; ?>">
                                    <span><?php _e('Email', 'user-blocker'); ?></span>
                                    <span class="sorting-indicator"></span>
                                </a>
                            </th>
                            <th class="th-role"><?php _e('Role', 'user-blocker'); ?></th>
                            <th class="th-time"><?php _e('Block Date', 'user-blocker'); ?></th>
                            <th style="text-align:center"><?php _e('Message', 'user-blocker'); ?></th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        if ($get_users) {
                            foreach ($get_users as $user) {
                                if ($sr_no % 2 == 0)
                                    $alt_class = 'alt';
                                else
                                    $alt_class = '';
                                ?>
                                <tr class="<?php echo $alt_class; ?>">
                                    <td align="center"><?php echo $sr_no; ?></td>
                                    <td><?php echo $user->user_login; ?>
                                        <div class="row-actions">
                                            <span class="trash">
                                                <a title="<?php _e('Reset this item', 'user-blocker'); ?>" href="?page=datewise_blocked_user_list&reset=1&paged=<?php echo $paged; ?>&username=<?php echo $user->ID; ?>&role=<?php echo $srole; ?>&txtUsername=<?php echo $txtUsername; ?>">
                                                    <?php _e('Reset', 'user-blocker'); ?>
                                                </a>
                                            </span>
                                        </div>
                                    </td>
                                    <td><?php echo $user->display_name; ?></td>
                                    <td><?php echo $user->user_email; ?></td>
                                    <td class="user-role"><?php echo ucfirst(str_replace('_', ' ', $user->roles[0])); ?></td>
                                    <td>
                                        <?php
                                        $block_date = get_user_meta($user->ID, 'block_date', true);
                                        if (!empty($block_date)) {
                                            if (array_key_exists('frmdate', $block_date) && array_key_exists('todate', $block_date)) {
                                                $frmdate = $block_date['frmdate'];
                                                $todate = $block_date['todate'];
                                                if ($frmdate != '' && $todate != '') {
                                                    echo ublk_dateTimeToTwelveHour($frmdate) . ' ' . __('to', 'user-blocker') . ' ' . ublk_dateTimeToTwelveHour($todate);
                                                }
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td style="text-align:center">
                                        <?php
                                        $block_msg_date = get_user_meta($user->ID, 'block_msg_date', true);
                                        echo ublk_disp_msg($block_msg_date);
                                        ?>
                                    </td>
                                </tr>
                                <?php
                                $sr_no++;
                            }
                        } else {
                            ?>
                            <tr><td colspan="7" style="text-align:center">
                                    <?php _e('No Record Found.', 'user-blocker'); ?>
                                </td></tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }

}

/**
 *
 * @global type $wpdb
 * @global type $wp_roles
 * @return html Display permanent block user list
 */
if (!function_exists('ublk_permanent_block_user_list_page')) {

    function ublk_permanent_block_user_list_page() {
        global $wpdb;
        global $wp_roles;
        $txtUsername        = '';
        $role               = '';
        $srole              = '';
        $msg_class          = '';
        $msg                = '';
        $total_pages        = '';
        $next_page          = '';
        $prev_page          = '';
        $search_arg         = '';
        $records_per_page   = 10;
        $paged              = 1;
        $orderby            = 'user_login';
        $order              = 'ASC';
        
        $msg = (isset($_GET['msg']) && $_GET['msg'] != '') ? esc_attr($_GET['msg']) : '';
        $msg_class = (isset($_GET['msg_class']) && $_GET['msg_class'] != '') ? esc_attr($_GET['msg_class']) : '';
        $orderby = (isset($_GET['orderby']) && $_GET['orderby'] != '') ? esc_attr($_GET['orderby']) : 'user_login';
        $order = (isset($_GET['order']) && $_GET['order'] != '') ? esc_attr($_GET['order']) : 'ASC';
        $paged = isset($_GET['paged']) ? esc_attr($_GET['paged']) : 1;

        if (!is_numeric($paged))
            $paged = 1;
        if (isset($_REQUEST['filter_action'])) {
            if ($_REQUEST['filter_action'] == 'Search') {
                $paged = 1;
            }
        }
        
        $offset = ($paged - 1) * $records_per_page;
        //Only for roles
        $get_roles = $wp_roles->roles;
        //Reset users
        if (isset($_GET['reset']) && $_GET['reset'] == '1') {
            if (isset($_GET['username']) && $_GET['username'] != '') {
                $r_username = esc_attr($_GET['username']);
                $user_data = new WP_User($r_username);
                if (get_userdata($r_username) != false) {
                    delete_user_meta($r_username, 'is_active');
                    delete_user_meta($r_username, 'block_msg_permenant');
                    $msg_class = 'updated';
                    $msg = $user_data->user_login . '\'s '.__('blocking time is successfully reset.','user-blocker');
                } else {
                    $msg_class = 'error';
                    $msg = __('Invalid user for reset blocking time.','user-blocker');
                }
            }
            if (isset($_GET['role']) && $_GET['role'] != '') {
                $reset_roles = get_users(array('role' => esc_attr($_GET['role'])));
                if (!empty($reset_roles)) {
                    foreach ($reset_roles as $single_reset_role) {
                        $own_value = get_user_meta($single_reset_role->ID, 'is_active', true);
                        $role_value = get_option(esc_attr($_GET['role']) . '_is_active');
                        if ($own_value == $role_value) {
                            delete_user_meta($single_reset_role->ID, 'is_active');
                            delete_user_meta($single_reset_role->ID, 'block_msg_permenant');
                        }
                    }
                }
                delete_option(esc_attr($_GET['role']) . '_is_active');
                delete_option(esc_attr($_GET['role']) . '_block_msg_permenant');
                $msg_class = 'updated';
                $msg = esc_attr($_GET['role']) . '\'s '.__('blocking time is successfully reset.','user-blocker');
            }
        }
        if (isset($_GET['txtUsername']) && trim($_GET['txtUsername']) != '') {
            $txtUsername = esc_attr($_GET['txtUsername']);
            $filter_ary['search'] = '*' . $txtUsername . '*';
            $filter_ary['search_columns'] = array(
                'user_login',
                'user_nicename',
                'user_email',
                'display_name'
            );
        }
        if ($txtUsername == '') {
            if (isset($_GET['role']) && $_GET['role'] != '' && !isset($_GET['reset'])) {
                $filter_ary['role'] = esc_attr($_GET['role']);
                $srole = esc_attr($_GET['role']);
            }
        }
        if ((isset($_GET['display']) && $_GET['display'] == 'roles') || (isset($_GET['role']) && $_GET['role'] != '' && isset($_GET['reset']) && $_GET['reset'] == '1') || (isset($_GET['role_edited']) && $_GET['role_edited'] != '' && isset($_GET['msg']) && $_GET['msg'] != '')) {
            $display = "roles";
        } else {
            $display = "users";
        }
        $filter_ary['orderby'] = $orderby;
        $filter_ary['order'] = $order;
        $meta_query_array[] = array(
            'key' => 'is_active',
            'value' => 'n',
            'compare' => '=');
        $filter_ary['meta_query'] = $meta_query_array;
        //Query for counting results
        $get_users_u1 = new WP_User_Query($filter_ary);
        $total_items = $get_users_u1->total_users;
        $total_pages = ceil($total_items / $records_per_page);
        $next_page = (int) $paged + 1;
        if ($next_page > $total_pages)
            $next_page = $total_pages;
        $filter_ary['number'] = $records_per_page;
        $filter_ary['offset'] = $offset;
        $prev_page = (int) $paged - 1;
        if ($prev_page < 1)
            $prev_page = 1;
        $sr_no = 1;
        if (isset($paged) && $paged > 1) {
            $sr_no = ( $records_per_page * ( $paged - 1 ) + 1);
        }
        //Main query
        $get_users_u = new WP_User_Query($filter_ary);
        $get_users = $get_users_u->get_results();
        ?>
        <div class="wrap" id="blocked-list">
            <h2 class="ublocker-page-title"><?php _e('Permanently Blocked User list', 'user-blocker') ?></h2>
            <?php
            //Display success/error messages
            if ($msg != '') {
                ?>
                <div class="ublocker-notice <?php echo $msg_class; ?>">
                    <p><?php echo $msg; ?></p>
                </div>
                <?php
            }
            ?>
            <div class="tab_parent_parent">
                <div class="tab_parent">
                    <ul>
                        <li><a href="?page=blocked_user_list"><?php _e('Blocked User List By Time', 'user-blocker'); ?></a></li>
                        <li><a href="?page=datewise_blocked_user_list"><?php _e('Blocked User List By Date', 'user-blocker'); ?></a></li>
                        <li><a class="current" href="?page=permanent_blocked_user_list"><?php _e('Blocked User List Permanently', 'user-blocker'); ?></a></li>
                        <li><a href="?page=all_type_blocked_user_list"><?php _e('Blocked User List', 'user-blocker'); ?></a></li>
                    </ul>
                </div>
            </div>
            <div class="cover_form">
                <div class="search_box">
                    <div class="tablenav top">
                        <form id="frmSearch" name="frmSearch" method="get" action="<?php echo home_url() . '/wp-admin/admin.php'; ?>">
                            <div class="actions">
                                <?php
                                ublk_blocked_user_category_dropdown($display);
                                ublk_blocked_role_selection_dropdown($display, $get_roles, $srole);
                                ublk_blocked_pagination($total_pages, $total_items, $paged, $prev_page, $next_page, $srole, $txtUsername, $orderby, $order, 'permanent_blocked_user_list');
                                ?>
                            </div>
                            <?php ublk_search_field($display, $txtUsername, 'permanent_blocked_user_list'); ?>
                        </form>
                    </div>
                </div>
                <table class="widefat post role_records striped" <?php if ($display == 'roles') echo 'style="display: table"'; ?>>
                    <thead>
                        <tr>
                            <th class="th-role"><?php _e('Role', 'user-blocker'); ?></th>
                            <th style="text-align:center"><?php _e('Message', 'user-blocker'); ?></th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th class="th-role"><?php _e('Role', 'user-blocker'); ?></th>
                            <th style="text-align:center"><?php _e('Message', 'user-blocker'); ?></th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        $no_data = 1;
                        if ($get_roles) {
                            $k = 1;
                            foreach ($get_roles as $key => $value) {
                                $block_permenant = get_option($key . '_is_active');
                                if ($k % 2 == 0)
                                    $alt_class = 'alt';
                                else
                                    $alt_class = '';
                                if ($key == 'administrator' || $block_permenant != 'n')
                                    continue;
                                $no_data = 0;
                                ?>
                                <tr class="<?php echo $alt_class; ?>">
                                    <td class="user-role"><?php echo $value['name']; ?>
                                        <div class="row-actions">
                                            <span class="trash"><a title="<?php _e('Reset this item', 'user-blocker'); ?>" href="?page=permanent_blocked_user_list&reset=1&role=<?php echo $key; ?>&orderby=<?php echo $orderby; ?>&order=<?php echo $order; ?>"><?php _e('Reset', 'user-blocker'); ?></a></span>
                                        </div>
                                    </td>
                                    <td style="text-align:center">
                                        <?php
                                        $block_msg_permenant = get_option($key . '_block_msg_permenant');
                                        echo ublk_disp_msg($block_msg_permenant);
                                        ?>
                                    </td>
                                </tr>
                                <?php
                                $k++;
                            }
                            if ($no_data == 1) {
                                ?>
                                <tr><td colspan="2" style="text-align:center"><?php echo __('No records found.', 'user-blocker'); ?></td></tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr><td colspan="2" style="text-align:center"><?php echo __('No records found.', 'user-blocker'); ?></td></tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
                <table class="widefat post fixed users_records striped" <?php if ($display == 'roles') echo 'style="display:none"'; ?>>
                    <thead>
                        <tr>
                            <th class="sr-no"><?php _e('S.N.', 'user-blocker'); ?></th>
                            <?php
                            $linkOrder = 'ASC';
                            if (isset($order)) {
                                if ($order == 'ASC') {
                                    $linkOrder = 'DESC';
                                } else if ($order == 'DESC') {
                                    $linkOrder = 'ASC';
                                }
                            }
                            ?>
                            <th class="th-username sortable <?php echo strtolower($order); ?>">
                                <a href="?page=permanent_blocked_user_list&orderby=user_login&order=<?php echo $linkOrder; ?>&txtUsername=<?php echo $txtUsername; ?>&srole=<?php echo $srole; ?>&paged=<?php echo $paged; ?>">
                                    <span><?php _e('Username', 'user-blocker'); ?></span>
                                    <span class="sorting-indicator"></span>
                                </a>
                            </th>
                            <th class="th-name sortable <?php echo strtolower($order); ?>">
                                <a href="?page=permanent_blocked_user_list&orderby=display_name&order=<?php echo $linkOrder; ?>&txtUsername=<?php echo $txtUsername; ?>&srole=<?php echo $srole; ?>&paged=<?php echo $paged; ?>">
                                    <span><?php _e('Name', 'user-blocker'); ?></span>
                                    <span class="sorting-indicator"></span>
                                </a>
                            </th>
                            <th class="th-email sortable <?php echo strtolower($order); ?>">
                                <a href="?page=permanent_blocked_user_list&orderby=user_email&order=<?php echo $linkOrder; ?>&txtUsername=<?php echo $txtUsername; ?>&srole=<?php echo $srole; ?>&paged=<?php echo $paged; ?>">
                                    <span><?php _e('Email', 'user-blocker'); ?></span>
                                    <span class="sorting-indicator"></span>
                                </a>
                            </th>
                            <th class="th-role"><?php _e('Role', 'user-blocker'); ?></th>
                            <th style="text-align:center"><?php _e('Message', 'user-blocker'); ?></th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th class="sr-no"><?php _e('S.N.', 'user-blocker'); ?></th>
                            <?php
                            $linkOrder = 'ASC';
                            if (isset($order)) {
                                if ($order == 'ASC') {
                                    $linkOrder = 'DESC';
                                } else if ($order == 'DESC') {
                                    $linkOrder = 'ASC';
                                }
                            }
                            ?>
                            <th class="th-username sortable <?php echo strtolower($order); ?>">
                                <a href="?page=permanent_blocked_user_list&orderby=user_login&order=<?php echo $linkOrder; ?>&txtUsername=<?php echo $txtUsername; ?>&srole=<?php echo $srole; ?>&paged=<?php echo $paged; ?>">
                                    <span><?php _e('Username', 'user-blocker'); ?></span>
                                    <span class="sorting-indicator"></span>
                                </a>
                            </th>
                            <th class="th-name sortable <?php echo strtolower($order); ?>">
                                <a href="?page=permanent_blocked_user_list&orderby=display_name&order=<?php echo $linkOrder; ?>&txtUsername=<?php echo $txtUsername; ?>&srole=<?php echo $srole; ?>&paged=<?php echo $paged; ?>">
                                    <span><?php _e('Name', 'user-blocker'); ?></span>
                                    <span class="sorting-indicator"></span>
                                </a>
                            </th>
                            <th class="th-email sortable <?php echo strtolower($order); ?>">
                                <a href="?page=permanent_blocked_user_list&orderby=user_email&order=<?php echo $linkOrder; ?>&txtUsername=<?php echo $txtUsername; ?>&srole=<?php echo $srole; ?>&paged=<?php echo $paged; ?>">
                                    <span><?php _e('Email', 'user-blocker'); ?></span>
                                    <span class="sorting-indicator"></span>
                                </a>
                            </th>
                            <th class="th-time"><?php _e('Role', 'user-blocker'); ?></th>
                            <th style="text-align:center"><?php _e('Message', 'user-blocker'); ?></th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        if ($get_users) {
                            foreach ($get_users as $user) {
                                if ($sr_no % 2 == 0)
                                    $alt_class = 'alt';
                                else
                                    $alt_class = '';
                                ?>
                                <tr class="<?php echo $alt_class; ?>">
                                    <td align="center"><?php echo $sr_no; ?></td>
                                    <td><?php echo $user->user_login; ?>
                                        <div class="row-actions">
                                            <span class="trash"><a title="<?php _e('Reset this item', 'user-blocker'); ?>" href="?page=permanent_blocked_user_list&reset=1&paged=<?php echo $paged; ?>&username=<?php echo $user->ID; ?>&role=<?php echo $srole; ?>&txtUsername=<?php echo $txtUsername; ?>"><?php _e('Reset', 'user-blocker'); ?></a></span>
                                        </div>
                                    </td>
                                    <td><?php echo $user->display_name; ?></td>
                                    <td><?php echo $user->user_email; ?></td>
                                    <td class="user-role"><?php echo ucfirst(str_replace('_', ' ', $user->roles[0])); ?></td>
                                    <td style="text-align:center">
                                        <?php
                                        $block_msg_permenant = get_user_meta($user->ID, 'block_msg_permenant', true);
                                        echo ublk_disp_msg($block_msg_permenant);
                                        ?>
                                    </td>
                                </tr>
                                <?php
                                $sr_no++;
                            }
                        }
                        else {
                            ?>
                            <tr><td colspan="6" style="text-align:center">
                                    <?php _e('No records Found.', 'user-blocker'); ?>
                                </td></tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }

}

/**
 *
 * @global type $wpdb
 * @global type $wp_roles
 * @return html Display all type block user list
 */
if (!function_exists('ublk_all_type_block_user_list_page')) {

    function ublk_all_type_block_user_list_page() {
        global $wpdb;
        global $wp_roles;
        $txtUsername        = '';
        $role               = '';
        $srole              = '';
        $msg_class          = '';
        $msg                = '';
        $total_pages        = '';
        $next_page          = '';
        $prev_page          = '';
        $search_arg         = '';
        $records_per_page   = 10;
        $paged              = 1;
        $orderby            = 'user_login';
        $order              = 'ASC';

        $msg = (isset($_GET['msg']) && $_GET['msg'] != '') ? esc_attr($_GET['msg']) : '';
        $msg_class = (isset($_GET['msg_class']) && $_GET['msg_class'] != '') ? esc_attr($_GET['msg_class']) : '';
        $orderby = (isset($_GET['orderby']) && $_GET['orderby'] != '') ? esc_attr($_GET['orderby']) : 'user_login';
        $order = (isset($_GET['order']) && $_GET['order'] != '') ? esc_attr($_GET['order']) : 'ASC';
        $paged = isset($_GET['paged']) ? esc_attr($_GET['paged']) : 1;

        if (!is_numeric($paged))
            $paged = 1;

        if (isset($_REQUEST['filter_action'])) {
            if ($_REQUEST['filter_action'] == 'Search') {
                $paged = 1;
            }
        }

        $offset = ($paged - 1) * $records_per_page;
        //Only for roles

        $get_roles = $wp_roles->roles;
        //Reset users
        if (isset($_GET['reset']) && $_GET['reset'] == '1') {
            if (isset($_GET['username']) && $_GET['username'] != '') {
                $r_username = esc_attr($_GET['username']);
                $user_data = new WP_User($r_username);
                if (get_userdata($r_username) != false) {
                    delete_user_meta($r_username, 'block_day');
                    delete_user_meta($r_username, 'block_msg_date');
                    delete_user_meta($r_username, 'block_date');
                    delete_user_meta($r_username, 'block_msg_date');
                    delete_user_meta($r_username, 'is_active');
                    delete_user_meta($r_username, 'block_msg_permenant');
                    $msg_class = 'updated';
                    $msg = $user_data->user_login . '\'s blocking is successfully reset.';
                } else {
                    $msg_class = 'error';
                    $msg = __('Invalid user for reset blocking.','user-blocker');
                }
            }
            if (isset($_GET['role']) && $_GET['role'] != '') {
                $reset_roles = get_users(array('role' => esc_attr($_GET['role'])));
                if (!empty($reset_roles)) {
                    foreach ($reset_roles as $single_reset_role) {
                        //Permenant block data
                        $own_value = get_user_meta($single_reset_role->ID, 'is_active', true);
                        $role_value = get_option(esc_attr($_GET['role']) . '_is_active');
                        $own_value_msg = get_user_meta($single_reset_role->ID, 'block_msg_permenant', true);
                        $role_value_msg = get_option(esc_attr($_GET['role']) . '_block_msg_permenant');
                        if (($own_value == $role_value) && ($own_value_msg == $role_value_msg)) {
                            delete_user_meta($single_reset_role->ID, 'is_active');
                            delete_user_meta($single_reset_role->ID, 'block_msg_permenant');
                        }
                        //Date wise block data
                        $own_value_date = get_user_meta($single_reset_role->ID, 'block_date', true);
                        $role_value_date = get_option(esc_attr($_GET['role']) . '_block_date');
                        $own_value_date_msg = get_user_meta($single_reset_role->ID, 'block_msg_date', true);
                        $role_value_date_msg = get_option(esc_attr($_GET['role']) . '_block_msg_date');
                        if (($own_value_date == $role_value_date) && ($own_value_date_msg == $role_value_date_msg)) {
                            delete_user_meta($single_reset_role->ID, 'block_date');
                            delete_user_meta($single_reset_role->ID, 'block_msg_date');
                        }
                        //Day wise block data
                        $own_value_day = get_user_meta($single_reset_role->ID, 'block_day', true);
                        $role_value_day = get_option(esc_attr($_GET['role']) . '_block_day');
                        $own_value_day_msg = get_user_meta($single_reset_role->ID, 'block_msg_day', true);
                        $role_value_day_msg = get_option(esc_attr($_GET['role']) . '_block_msg_day');
                        if (($own_value_day == $role_value_day) && ($own_value_day_msg == $role_value_day_msg)) {
                            delete_user_meta($single_reset_role->ID, 'block_day');
                            delete_user_meta($single_reset_role->ID, 'block_msg_day');
                        }
                    }
                }
                delete_option(esc_attr($_GET['role']) . '_is_active');
                delete_option(esc_attr($_GET['role']) . '_block_date');
                delete_option(esc_attr($_GET['role']) . '_block_day');
                delete_option(esc_attr($_GET['role']) . '_block_msg_permenant');
                delete_option(esc_attr($_GET['role']) . '_block_msg_date');
                delete_option(esc_attr($_GET['role']) . '_block_msg_day');
                
                $msg_class = 'updated';
                $msg = esc_attr($_GET['role']) . '\'s blocking is successfully reset.';
            }
        }
        if (isset($_GET['txtUsername']) && trim($_GET['txtUsername']) != '') {
            $txtUsername = esc_attr($_GET['txtUsername']);
            $filter_ary['search'] = '*' . esc_attr($txtUsername) . '*';
            $filter_ary['search_columns'] = array(
                'user_login',
                'user_nicename',
                'user_email',
                'display_name'
            );
        }
        if ($txtUsername == '') {
            if (isset($_GET['role']) && $_GET['role'] != '' && !isset($_GET['reset'])) {
                $filter_ary['role'] = esc_attr($_GET['role']);
                $srole = esc_attr($_GET['role']);
            }
        }
        //end
        if ((isset($_GET['display']) && $_GET['display'] == 'roles') || (isset($_GET['role']) && $_GET['role'] != '' && isset($_GET['reset']) && $_GET['reset'] == '1') || (isset($_GET['role_edited']) && $_GET['role_edited'] != '' && isset($_GET['msg']) && $_GET['msg'] != '')) {
            $display = "roles";
        } else {
            $display = "users";
        }
        
        $filter_ary['orderby'] = $orderby;
        $filter_ary['order'] = $order;
        $meta_query_array[] = array(
            'relation' => 'OR',
            array(
                'key' => 'block_date',
                'compare' => 'EXISTS'),
            array(
                'key' => 'is_active',
                'value' => 'n',
                'compare' => '='),
            array(
                'key' => 'block_day',
                'compare' => 'EXISTS')
        );
        $filter_ary['meta_query'] = $meta_query_array;
        add_filter('pre_user_query', 'ublk_sort_by_member_number');
        //Query for counting results
        $get_users_u1 = new WP_User_Query($filter_ary);
        $total_items = $get_users_u1->total_users;
        $total_pages = ceil($total_items / $records_per_page);
        $next_page = (int) $paged + 1;
        if ($next_page > $total_pages)
            $next_page = $total_pages;
        $filter_ary['number'] = $records_per_page;
        $filter_ary['offset'] = $offset;
        $prev_page = (int) $paged - 1;
        if ($prev_page < 1)
            $prev_page = 1;
        $sr_no = 1;
        if (isset($paged) && $paged > 1) {
            $sr_no = ( $records_per_page * ( $paged - 1 ) + 1);
        }
        //Main query
        $get_users_u = new WP_User_Query($filter_ary);
        remove_filter('pre_user_query', 'ublk_sort_by_member_number');
        $get_users = $get_users_u->get_results();
        ?>
        <div class="wrap" id="blocked-list">
            <h2 class="ublocker-page-title"><?php _e('Blocked User list', 'user-blocker') ?></h2>
            <?php
            //Display success/error messages
            if ($msg != '') {
                ?>
                <div class="ublocker-notice <?php echo $msg_class; ?>">
                    <p><?php echo $msg; ?></p>
                </div>
                <?php
            }
            ?>
            <div class="tab_parent_parent">
                <div class="tab_parent">
                    <ul>
                        <li><a href="?page=blocked_user_list"><?php _e('Blocked User List By Time', 'user-blocker'); ?></a></li>
                        <li><a href="?page=datewise_blocked_user_list"><?php _e('Blocked User List By Date', 'user-blocker'); ?></a></li>
                        <li><a href="?page=permanent_blocked_user_list"><?php _e('Blocked User List Permanently', 'user-blocker'); ?></a></li>
                        <li><a class='current' href="?page=all_type_blocked_user_list"><?php _e('Blocked User List', 'user-blocker'); ?></a></li>
                    </ul>
                </div>
            </div>
            <div class="cover_form">
                <div class="search_box">
                    <div class="tablenav top">
                        <form id="frmSearch" name="frmSearch" method="get" action="<?php echo home_url() . '/wp-admin/admin.php'; ?>">
                            <div class="actions">
                                <?php
                                ublk_blocked_user_category_dropdown($display);
                                ublk_blocked_role_selection_dropdown($display, $get_roles, $srole);
                                ublk_blocked_pagination($total_pages, $total_items, $paged, $prev_page, $next_page, $srole, $txtUsername, $orderby, $order, 'all_type_blocked_user_list');
                                ?>
                            </div>
                            <?php ublk_search_field($display, $txtUsername, 'all_type_blocked_user_list'); ?>
                        </form>
                    </div>
                </div>
                <table class="widefat post role_records striped" <?php if ($display == 'roles') echo 'style="display: table"'; ?>>
                    <thead>
                        <tr>
                            <th class="th-role"><?php _e('Role', 'user-blocker'); ?></th>
                            <th style="text-align:center"><?php _e('Message', 'user-blocker'); ?></th>
                            <th class="th-username"><?php _e('Block Data', 'user-blocker'); ?></th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th class="th-role"><?php _e('Role', 'user-blocker'); ?></th>
                            <th style="text-align:center"><?php _e('Message', 'user-blocker'); ?></th>
                            <th class="th-username"><?php _e('Block Data', 'user-blocker'); ?></th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        $no_data = 1;
                        if ($get_roles) {
                            $k = 1;
                            foreach ($get_roles as $key => $value) {
                                $block_day = get_option($key . '_block_day');
                                $block_date = get_option($key . '_block_date');
                                $is_active = get_option($key . '_is_active');
                                if ($key == 'administrator' || ($is_active != 'n' && $block_date == '' && $block_day == ''))
                                    continue;
                                if ($k % 2 == 0)
                                    $alt_class = 'alt';
                                else
                                    $alt_class = '';
                                $no_data = 0;
                                ?>
                                <tr class="<?php echo $alt_class; ?>">
                                    <td class="user-role"><?php echo $value['name']; ?>
                                        <div class="row-actions">
                                            <span class="trash"><a title="<?php _e('Reset this item', 'user-blocker'); ?>" href="?page=all_type_blocked_user_list&reset=1&role=<?php echo $key; ?>&orderby=<?php echo $orderby; ?>&order=<?php echo $order; ?>"><?php _e('Reset', 'user-blocker'); ?></a></span>
                                        </div>
                                    </td>
                                    <td style="text-align:center">
                                        <?php ublk_all_block_data_msg_role($key); ?>
                                    </td>
                                    <td>
                                        <?php ublk_all_block_data_view_role($key); ?>
                                    </td>
                                </tr>
                                <?php
                                echo ublk_all_block_data_table_role($key);
                                $k++;
                            }
                            if ($no_data == 1) {
                                ?>
                                <tr><td colspan="3" style="text-align:center"><?php echo __('No records found.', 'user-blocker'); ?></td></tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr><td colspan="3" style="text-align:center"><?php echo __('No records found.', 'user-blocker'); ?></td></tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
                <table class="widefat post fixed users_records striped" <?php if ($display == 'roles') echo 'style="display:none"'; ?>>
                    <thead>
                        <tr>
                            <th class="sr-no"><?php _e('S.N.', 'user-blocker'); ?></th>
                            <?php
                            $linkOrder = 'ASC';
                            if (isset($order)) {
                                if ($order == 'ASC') {
                                    $linkOrder = 'DESC';
                                } else if ($order == 'DESC') {
                                    $linkOrder = 'ASC';
                                }
                            }
                            ?>
                            <th class="th-username sortable <?php echo strtolower($order); ?>">
                                <a href="?page=all_type_blocked_user_list&orderby=user_login&order=<?php echo $linkOrder; ?>&txtUsername=<?php echo $txtUsername; ?>&srole=<?php echo $srole; ?>&paged=<?php echo $paged; ?>">
                                    <span><?php _e('Username', 'user-blocker'); ?></span>
                                    <span class="sorting-indicator"></span>
                                </a>
                            </th>
                            <th class="th-name sortable <?php echo strtolower($order); ?>">
                                <a href="?page=all_type_blocked_user_list&orderby=display_name&order=<?php echo $linkOrder; ?>&txtUsername=<?php echo $txtUsername; ?>&srole=<?php echo $srole; ?>&paged=<?php echo $paged; ?>">
                                    <span><?php _e('Name', 'user-blocker'); ?></span>
                                    <span class="sorting-indicator"></span>
                                </a>
                            </th>
                            <th class="th-email sortable <?php echo strtolower($order); ?>">
                                <a href="?page=all_type_blocked_user_list&orderby=user_email&order=<?php echo $linkOrder; ?>&txtUsername=<?php echo $txtUsername; ?>&srole=<?php echo $srole; ?>&paged=<?php echo $paged; ?>">
                                    <span><?php _e('Email', 'user-blocker'); ?></span>
                                    <span class="sorting-indicator"></span>
                                </a>
                            </th>
                            <th class="th-username"><?php _e('Role', 'user-blocker'); ?></th>
                            <th style="text-align:center"><?php _e('Message', 'user-blocker'); ?></th>
                            <th class="th-username aligntextcenter"><?php _e('Block Data', 'user-blocker'); ?></th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th class="sr-no"><?php _e('S.N.', 'user-blocker'); ?></th>
                            <?php
                            $linkOrder = 'ASC';
                            if (isset($order)) {
                                if ($order == 'ASC') {
                                    $linkOrder = 'DESC';
                                } else if ($order == 'DESC') {
                                    $linkOrder = 'ASC';
                                }
                            }
                            ?>
                            <th class="th-username sortable <?php echo strtolower($order); ?>">
                                <a href="?page=all_type_blocked_user_list&orderby=user_login&order=<?php echo $linkOrder; ?>&txtUsername=<?php echo $txtUsername; ?>&srole=<?php echo $srole; ?>&paged=<?php echo $paged; ?>">
                                    <span><?php _e('Username', 'user-blocker'); ?></span>
                                    <span class="sorting-indicator"></span>
                                </a>
                            </th>
                            <th class="th-name sortable <?php echo strtolower($order); ?>">
                                <a href="?page=all_type_blocked_user_list&orderby=display_name&order=<?php echo $linkOrder; ?>&txtUsername=<?php echo $txtUsername; ?>&srole=<?php echo $srole; ?>&paged=<?php echo $paged; ?>">
                                    <span><?php _e('Name', 'user-blocker'); ?></span>
                                    <span class="sorting-indicator"></span>
                                </a>
                            </th>
                            <th class="th-email sortable <?php echo strtolower($order); ?>">
                                <a href="?page=all_type_blocked_user_list&orderby=user_email&order=<?php echo $linkOrder; ?>&txtUsername=<?php echo $txtUsername; ?>&srole=<?php echo $srole; ?>&paged=<?php echo $paged; ?>">
                                    <span><?php _e('Email', 'user-blocker'); ?></span>
                                    <span class="sorting-indicator"></span>
                                </a>
                            </th>
                            <th class="th-username"><?php _e('Role', 'user-blocker'); ?></th>
                            <th style="text-align:center"><?php _e('Message', 'user-blocker'); ?></th>
                            <th class="th-username aligntextcenter"><?php _e('Block Data', 'user-blocker'); ?></th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        if ($get_users) {
                            foreach ($get_users as $user) {
                                if ($sr_no % 2 == 0)
                                    $alt_class = 'alt';
                                else
                                    $alt_class = '';
                                ?>
                                <tr class="<?php echo $alt_class; ?>">
                                    <td align="center"><?php echo $sr_no; ?></td>
                                    <td><?php echo $user->user_login; ?>
                                        <div class="row-actions">
                                            <span class="trash"><a title="<?php _e('Reset this item', 'user-blocker'); ?>" href="?page=all_type_blocked_user_list&reset=1&paged=<?php echo $paged; ?>&username=<?php echo $user->ID; ?>&role=<?php echo $srole; ?>&txtUsername=<?php echo $txtUsername; ?>"><?php _e('Reset', 'user-blocker'); ?></a></span>
                                        </div>
                                    </td>
                                    <td><?php echo $user->display_name; ?></td>
                                    <td><?php echo $user->user_email; ?></td>
                                    <td class="user-role"><?php echo ucfirst(str_replace('_', ' ', $user->roles[0])); ?></td>
                                    <td style="text-align:center">
                                        <?php echo ublk_all_block_data_msg($user->ID); ?>
                                    </td>
                                    <td class="aligntextcenter">
                                        <?php ublk_all_block_data_view($user->ID); ?>
                                    </td>
                                </tr>
                                <?php
                                echo ublk_all_block_data_table($user->ID);
                                $sr_no++;
                            }
                        }
                        else {
                            echo '<tr><td colspan="7" style="text-align:center">' . __('No records found.', 'user-blocker') . '</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }

}