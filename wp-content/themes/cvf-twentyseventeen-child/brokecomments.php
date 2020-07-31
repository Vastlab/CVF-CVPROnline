<?php
if (!defined('ABSPATH')) {
    exit();
}

$loadStartTime = WpdiscuzHelper::getMicrotime();
global $post;
$wpdiscuz = wpDiscuz();
if (!function_exists('wpdiscuz_close_divs')) {

    function wpdiscuz_close_divs($html) {
        global $wpdiscuz;
        @preg_match_all('|<div|is', $html, $wc_div_open, PREG_SET_ORDER);
        @preg_match_all('|</div|is', $html, $wc_div_close, PREG_SET_ORDER);
        $wc_div_open = count((array) $wc_div_open);
        $wc_div_close = count((array) $wc_div_close);
        $wc_div_delta = $wc_div_open - $wc_div_close;
        if ($wc_div_delta) {
            $wc_div_end_html = str_repeat('</div>', $wc_div_delta);
            $html = $html . $wc_div_end_html;
        }
        return $html;
    }

}

$currentUser = $wpdiscuz->helper->getCurrentUser();
do_action('wpdiscuz_before_load', $post, $currentUser, null);
if (!post_password_required($post->ID)) {
    $commentsCount = get_comments_number();
    $wpCommClasses = array();
    $wpCommClasses[] = $currentUser && $currentUser->ID ? 'wpdiscuz_auth' : 'wpdiscuz_unauth';
    $wpCommClasses[] = $wpdiscuz->optionsSerialized->theme;

    if (!$wpdiscuz->optionsSerialized->wordpressShowAvatars) {
        $wpCommClasses[] = 'wpdiscuz_no_avatar';
    }

    $wpCommClasses = apply_filters('wpdiscuz_container_classes', $wpCommClasses);
    $wpCommClasses = implode(' ', $wpCommClasses);
    $ob_stat = ini_get('output_buffering');
    if ($ob_stat || $ob_stat === '' || $ob_stat == '0') {
        $wc_ob_allowed = true;
        ob_start();
        do_action('comment_form_top');
        do_action('wpdiscuz_comment_form_top', $post, $currentUser, $commentsCount);
        $wc_comment_form_top_content = ob_get_clean();
        $wc_comment_form_top_content = wpdiscuz_close_divs($wc_comment_form_top_content);
    } else {
        $wc_ob_allowed = false;
    }

    if ((isset($_GET['deleteComments']) && $_GET['deleteComments'])) {
        $decodedEmail = get_transient(WpDiscuzConstants::TRS_USER_HASH . trim($_GET['deleteComments']));
        if ($decodedEmail) {
            $comments = get_comments(array('author_email' => $decodedEmail, 'status' => 'all', 'fields' => 'ids'));
            if ($comments) {
                foreach ($comments as $cid) {
                    wp_delete_comment($cid, true);
                }
                ?>
                <div id="wc_delete_content_message">
                    <span class="wc_delete_content_message"><?php _e('Your comments have been deleted from database', 'wpdiscuz'); ?></span>
                </div>
                <?php
            }
        }
    } else if (isset($_GET['deleteSubscriptions']) && $_GET['deleteSubscriptions']) {
        $decodedEmail = get_transient(WpDiscuzConstants::TRS_USER_HASH . trim($_GET['deleteSubscriptions']));
        if ($decodedEmail) {
            $wpdiscuz->dbManager->unsubscribeByEmail($decodedEmail);
            ?>
            <div id="wc_delete_content_message">
                <span class="wc_delete_content_message"><?php _e('You cancel all your subscriptions successfully', 'wpdiscuz'); ?></span>
            </div>
            <?php
        }
    } else if (isset($_GET['deleteFollows']) && $_GET['deleteFollows']) {
        $decodedEmail = get_transient(WpDiscuzConstants::TRS_USER_HASH . trim($_GET['deleteFollows']));
        if (get_transient(WpDiscuzConstants::TRS_USER_HASH . md5($decodedEmail)) !== false) {
            $wpdiscuz->dbManager->unfollowByEmail($decodedEmail);
            ?>
            <div id="wc_delete_content_message">
                <span class="wc_delete_content_message"><?php _e('You cancel all your follows successfully', 'wpdiscuz'); ?></span>
            </div>
            <?php
        }
    } else if (isset($_GET['wpdiscuzFollowID']) && isset($_GET['wpdiscuzFollowKey']) && isset($_GET['wpDiscuzComfirm'])) {
        if ($_GET['wpDiscuzComfirm']) {
            if ($wpdiscuz->dbManager->confirmFollow($_GET['wpdiscuzFollowID'], $_GET['wpdiscuzFollowKey'])) {
                ?>
                <div id="wc_follow_message">
                    <span class="wc_follow_message"><?php _e('Follow has been confirmed successfully', 'wpdiscuz'); ?></span>
                </div>
                <?php
            }
        } else {
            if ($wpdiscuz->dbManager->cancelFollow($_GET['wpdiscuzFollowID'], $_GET['wpdiscuzFollowKey'])) {
                ?>
                <div id="wc_follow_message">
                    <span class="wc_follow_message"><?php _e('Follow has been canceled successfully', 'wpdiscuz'); ?></span>
                </div>
                <?php
            }
        }
    }

    if (isset($_GET['wpdiscuzSubscribeID']) && isset($_GET['key'])) {
        $wpdiscuz->dbManager->unsubscribe($_GET['wpdiscuzSubscribeID'], $_GET['key']);
        ?>
        <div id="wc_unsubscribe_message">
            <span class="wc_unsubscribe_message"><?php echo $wpdiscuz->optionsSerialized->phrases['wc_unsubscribe_message']; ?></span>
        </div>
        <?php
    }

    if (isset($_GET['wpdiscuzConfirmID']) && isset($_GET['wpdiscuzConfirmKey']) && isset($_GET['wpDiscuzComfirm'])) {
        $wpdiscuz->dbManager->notificationConfirm($_GET['wpdiscuzConfirmID'], $_GET['wpdiscuzConfirmKey']);
        ?>
        <div id="wc_unsubscribe_message">
            <span class="wc_unsubscribe_message"><?php echo $wpdiscuz->optionsSerialized->phrases['wc_comfirm_success_message']; ?></span>
        </div>
        <?php
    }

    if (isset($_GET['subscriptionSuccess'])) {
        $errorClass = 'wpdiscuz-sendmail-error';
        if ($_GET['subscriptionSuccess'] == -1) {
            $subscriptionMsg = __('Unable to send an email', 'wpdiscuz');
        } elseif (!$_GET['subscriptionSuccess']) {
            $subscriptionMsg = __('Subscription Fault', 'wpdiscuz');
        } else {
            $errorClass = '';
            if (isset($_GET['subscriptionID']) && ($subscriptionID = intval($_GET['subscriptionID']))) {
                $noNeedMemberConfirm = ($currentUser->ID && $wpdiscuz->optionsSerialized->disableMemberConfirm);
                $noNeedGuestsConfirm = (!$currentUser->ID && $wpdiscuz->optionsSerialized->disableGuestsConfirm);
                if ($noNeedMemberConfirm || $noNeedGuestsConfirm) {
                    $subscriptionMsg = $wpdiscuz->optionsSerialized->phrases['wc_subscribe_message'];
                } else {
                    $subscriptionMsg = $wpdiscuz->optionsSerialized->phrases['wc_confirm_email'];
                }
            }
        }
        ?>
        <div id="wc_unsubscribe_message" class="<?php echo $errorClass; ?>">
            <span class="wc_unsubscribe_message"><?php echo $subscriptionMsg; ?></span>
        </div>
        <?php
    }
    ?>
    <div class="wpdiscuz_top_clearing"></div>
    <?php
    $form = $wpdiscuz->wpdiscuzForm->getForm($post->ID);
    $isShowSubscribeBar = $form->isShowSubscriptionBar();
    $isPostmaticActive = !class_exists('Prompt_Comment_Form_Handling') || (class_exists('Prompt_Comment_Form_Handling') && !$wpdiscuz->optionsSerialized->usePostmaticForCommentNotification);
    $wpdiscuzCommentsOrder = $wpdiscuz->optionsSerialized->wordpressCommentOrder;
    if (!$wpdiscuz->optionsSerialized->wordpressIsPaginate && $wpdiscuz->optionsSerialized->showSortingButtons && $wpdiscuz->optionsSerialized->mostVotedByDefault) {
        $wpdiscuzCommentsOrderBy = 'by_vote';
    } else {
        $wpdiscuzCommentsOrderBy = 'comment_date_gmt';
    }


    if (isset($_COOKIE[WpDiscuzCore::COOKIE_COMMENTS_SORTING . '_' . $post->ID]) && $_COOKIE[WpDiscuzCore::COOKIE_COMMENTS_SORTING . '_' . $post->ID] && !$wpdiscuz->optionsSerialized->wordpressIsPaginate) {
        $cookieOrderData = json_decode(stripslashes(trim($_COOKIE[WpDiscuzCore::COOKIE_COMMENTS_SORTING . '_' . $post->ID])));
        if ($cookieOrderData && isset($cookieOrderData->orderBy) && isset($cookieOrderData->order)) {
            $cookieOrderBy = strtolower(trim($cookieOrderData->orderBy));
            $cookieOrder = strtolower(trim($cookieOrderData->order));
            if (in_array($cookieOrder, array('asc', 'desc'))) {
                $wpdiscuzCommentsOrderBy = $cookieOrderBy;
                $wpdiscuzCommentsOrder = $cookieOrder;
            }
        }
    }

    $commentsOpen = comments_open($post);

    if ($commentsOpen) {
        $wpdiscuz->helper->superSocializerFix();
        $formCustomCss = $form->getCustomCSS();
        if ($formCustomCss) {
            echo '<style type="text/css">' . $formCustomCss . '</style>';
        }
        if (!$wpdiscuz->optionsSerialized->headerTextShowHide) {
            ?>
            <h3 id="wc-comment-header">
                <?php if ($commentsCount) { ?>
                    <div class="wpdiscuz-comment-count">
                        <div class="wpd-cc-value"><?php echo $commentsCount; ?></div>
                        <div class="wpd-cc-arrow"></div>
                    </div>
                <?php } ?>
                <?php echo htmlentities($form->getHeaderText()); ?>
            </h3>
            <?php
        }
        ?>
        <div id="wpcomm" class="<?php echo $wpCommClasses; ?>">
            <div class="wpdiscuz-form-top-bar">
                <div class="wpdiscuz-ftb-left">
                    <?php
                    $currentUserId = 0;
                    $currentUserEmail = isset($_COOKIE['comment_author_email_' . COOKIEHASH]) ? $_COOKIE['comment_author_email_' . COOKIEHASH] : '';
                    if ($currentUser && $currentUser->ID) {
                        $currentUserId = $currentUser->ID;
                        $currentUserEmail = $currentUser->user_email;
                    }
                    ?>
                    <?php if (!$wpdiscuz->optionsSerialized->hideUserSettingsButton && $currentUserEmail) { ?>
                        <div class="wpdiscuz-user-settings wpd-tooltip-left wpd-info wpd-not-clicked">
                            <i class="fas fa-user-cog"></i>
                            <wpdtip><?php echo $wpdiscuz->optionsSerialized->phrases['wc_content_and_settings']; ?></wpdtip>
                        </div>
                    <?php } ?>
                    <div id="wc_show_hide_loggedin_username">
                        <?php
                        if ($wpdiscuz->optionsSerialized->showHideLoggedInUsername) {
                            if ($currentUser && $currentUser->ID) {
                                $user_url = get_author_posts_url($currentUser->ID);
                                $user_url = apply_filters('wpdiscuz_profile_url', $user_url, $currentUser);
                                $logout = wp_loginout(get_permalink(), false);
                                $logout = preg_replace('!>([^<]+)!is', '>' . $wpdiscuz->optionsSerialized->phrases['wc_log_out'], $logout);
                                echo $wpdiscuz->optionsSerialized->phrases['wc_logged_in_as'] . ' <a href="' . $user_url . '">' . $wpdiscuz->helper->getCurrentUserDisplayName($currentUser) . '</a> | ' . $logout;
                            } else {
                                if (!$form->isUserCanComment($currentUser, $post->ID) || !$wpdiscuz->optionsSerialized->hideLoginLinkForGuests) {
                                    $login = wp_loginout(get_permalink(), false);
                                    $login = preg_replace('!>([^<]+)!is', '>' . $wpdiscuz->optionsSerialized->phrases['wc_log_in'], $login);
                                    $login = sprintf($wpdiscuz->optionsSerialized->phrases['wc_login_please'], $login);
                                    echo '<i class="fas fa-sign-in-alt"></i> <span>' . $login . '</span>';
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
                <?php do_action('comment_main_form_bar_top'); ?>
                <div class="wpd-clear"></div>
            </div>


            <?php do_action('comment_form_before'); ?>
            <div class="wc_social_plugin_wrapper">
                <?php
                if ($wc_ob_allowed) {
                    echo $wc_comment_form_top_content;
                } else {
                    do_action('comment_form_top');
                    do_action('wpdiscuz_comment_form_top', $post, $currentUser, $commentsCount);
                }
                ?>
            </div>
            <?php
            $wpdiscuz->wpdiscuzForm->renderFrontForm($commentsCount, $currentUser);
            do_action('comment_form_after');
            do_action('wpdiscuz_comment_form_after', $post, $currentUser, $commentsCount);
        } else {
            if ($commentsCount > 0) {
                $wpdiscuz->helper->superSocializerFix();
            } else {
                ?>
                <div id="comments" class="comments-area" style="display:none">
                    <div id="respond"></div>
                <?php } ?>
                <?php
                do_action('comment_form_closed');
                do_action('wpdiscuz_comment_form_closed', $post, $currentUser, $commentsCount);
                ?>
                <div id="wpcomm" class="<?php echo $wpCommClasses; ?>" style="border:none;">
                <?php } ?>
                <?php do_action('wpdiscuz_before_comments', $post, $currentUser, $commentsCount); ?>                   

                <?php if ($isShowSubscribeBar || $wpdiscuz->optionsSerialized->showSortingButtons) { ?>
                    <div class="wpdiscuz-front-actions">
                        <?php if ($isShowSubscribeBar && $isPostmaticActive && $commentsOpen) { ?>
                            <div class="wpdiscuz-sbs-wrap">
                                <span><i class="far fa-envelope" aria-hidden="true"></i>&nbsp; <?php echo $wpdiscuz->optionsSerialized->phrases['wc_subscribe_anchor']; ?> &nbsp;<i class="fas fa-caret-down" aria-hidden="true"></i></span>
                            </div>
                        <?php } ?>
                        <?php if ($commentsCount && $wpdiscuz->optionsSerialized->showSortingButtons && !$wpdiscuz->optionsSerialized->wordpressIsPaginate) { ?>                        
                            <div class="wpdiscuz-sort-buttons" style="font-size:14px; color: #777;">
                                <i class="fas fa-caret-up" aria-hidden="true"></i> 
                                <span class="wpdiscuz-sort-button wpdiscuz-date-sort-desc <?php print ($wpdiscuzCommentsOrderBy == 'comment_date_gmt' && $wpdiscuzCommentsOrder == 'desc') ? 'wpdiscuz-sort-button-active' : ''  ?>"><?php echo $wpdiscuz->optionsSerialized->phrases['wc_newest']; ?></span> <i class="fas fa-caret-up" aria-hidden="true"></i> 
                                <span class="wpdiscuz-sort-button wpdiscuz-date-sort-asc <?php print ($wpdiscuzCommentsOrderBy == 'comment_date_gmt' && $wpdiscuzCommentsOrder == 'asc') ? 'wpdiscuz-sort-button-active' : ''  ?>"><?php echo $wpdiscuz->optionsSerialized->phrases['wc_oldest']; ?></span>
                                <?php if (!$wpdiscuz->optionsSerialized->votingButtonsShowHide) { ?>
                                    <i class="fas fa-caret-up" aria-hidden="true"></i> <span class="wpdiscuz-sort-button wpdiscuz-vote-sort-up <?php print ($wpdiscuzCommentsOrderBy == 'by_vote') ? 'wpdiscuz-sort-button-active' : ''  ?>"><?php echo $wpdiscuz->optionsSerialized->phrases['wc_most_voted']; ?></span>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <div class="clearfix"></div>
                    </div>
                <?php } ?>
                <?php
                if ($isShowSubscribeBar && $isPostmaticActive) {
                    $wpdiscuz->subscriptionData = $wpdiscuz->dbManager->hasSubscription($post->ID, $currentUser->user_email);
                    $subscriptionType = null;
                    if ($wpdiscuz->subscriptionData) {
                        $isConfirmed = $wpdiscuz->subscriptionData['confirm'];
                        $subscriptionType = $wpdiscuz->subscriptionData['type'];
                        if ($subscriptionType == WpdiscuzCore::SUBSCRIPTION_POST || $subscriptionType == WpdiscuzCore::SUBSCRIPTION_ALL_COMMENT) {
                            $unsubscribeLink = $wpdiscuz->dbManager->unsubscribeLink($post->ID, $currentUser->user_email);
                        }
                    }
                    ?>
                    <div class="wpdiscuz-subscribe-bar wpdiscuz-hidden">
                        <?php
                        if ($subscriptionType != WpdiscuzCore::SUBSCRIPTION_POST) {
                            ?>
                            <form action="<?php echo admin_url('admin-ajax.php') . '?action=addSubscription'; ?>" method="post" id="wpdiscuz-subscribe-form">
                                <div class="wpdiscuz-subscribe-form-intro"><?php echo $wpdiscuz->optionsSerialized->phrases['wc_notify_of']; ?> </div>
                                <div class="wpdiscuz-subscribe-form-option" style="width:<?php echo (!$currentUser->ID) ? '40%' : '65%'; ?>;">
                                    <select class="wpdiscuz_select" name="wpdiscuzSubscriptionType" >
                                        <?php if ($wpdiscuz->optionsSerialized->subscriptionType != 3) { ?>
                                            <option value="<?php echo WpdiscuzCore::SUBSCRIPTION_POST; ?>"><?php echo $wpdiscuz->optionsSerialized->phrases['wc_notify_on_new_comment']; ?></option>
                                        <?php } ?>
                                        <?php if ($wpdiscuz->optionsSerialized->subscriptionType != 2) { ?>
                                            <option value="<?php echo WpdiscuzCore::SUBSCRIPTION_ALL_COMMENT; ?>" <?php echo (isset($unsubscribeLink) || !$wpdiscuz->optionsSerialized->wordpressThreadComments) ? 'disabled' : ''; ?>><?php echo $wpdiscuz->optionsSerialized->phrases['wc_notify_on_all_new_reply']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <?php if (!$currentUser->ID) { ?>
                                    <div class="wpdiscuz-item wpdiscuz-subscribe-form-email">
                                        <input  class="email" type="email" name="wpdiscuzSubscriptionEmail" required="required" value="" placeholder="<?php echo $wpdiscuz->optionsSerialized->phrases['wc_email_text']; ?>"/>
                                    </div>
                                <?php } ?>
                                <div class="wpdiscuz-subscribe-form-button">
                                    <input id="wpdiscuz_subscription_button" type="submit" value="<?php echo $wpdiscuz->optionsSerialized->phrases['wc_form_subscription_submit']; ?>" name="wpdiscuz_subscription_button" />
                                </div> 
                                <?php if (!$currentUser->ID && $form->isShowSubscriptionBarAgreement()): ?>
                                    <div class="wpdiscuz-subscribe-agreement">
                                        <input id="show_subscription_agreement" type="checkbox" required="required" name="show_subscription_agreement" value="1">
                                        <label for="show_subscription_agreement"><?php echo $form->subscriptionBarAgreementLabel(); ?></label>
                                    </div>
                                <?php endif; ?>
                                <?php wp_nonce_field('wpdiscuz_subscribe_form_nonce_action', 'wpdiscuz_subscribe_form_nonce'); ?>
                                <?php do_action('wpdiscuz_after_subscription_form'); ?>
                                <input type="hidden" value="<?php echo $post->ID; ?>" name="wpdiscuzSubscriptionPostId" />
                            </form>
                        <?php } ?>
                        <div class="wpdiscuz_clear"></div>
                        <?php
                        if (isset($unsubscribeLink)) {
                            $subscribeMessage = $isConfirmed ? $wpdiscuz->optionsSerialized->phrases['wc_unsubscribe'] : $wpdiscuz->optionsSerialized->phrases['wc_ignore_subscription'];
                            if ($subscriptionType == 'all_comment')
                                $introText = $wpdiscuz->optionsSerialized->phrases['wc_subscribed_to'] . ' ' . $wpdiscuz->optionsSerialized->phrases['wc_notify_on_all_new_reply'];
                            elseif ($subscriptionType == 'post')
                                $introText = $wpdiscuz->optionsSerialized->phrases['wc_subscribed_to'] . ' ' . $wpdiscuz->optionsSerialized->phrases['wc_notify_on_new_comment'];
                            echo '<div class="wpdiscuz_subscribe_status">' . $introText . " | <a href='$unsubscribeLink'>" . $subscribeMessage . "</a></div>";
                        }
                        ?>
                    </div>
                    <?php
                }
                ?>

                <?php if ($wpdiscuz->optionsSerialized->commentListUpdateType == 2) { ?>
                    <div class="wc_new_comment_and_replies">
                        <div class="wc_new_comment wc-update-on-click"></div>
                        <div class="wc_new_reply wc-update-on-click"></div>
                        <div class="wpdiscuz_clear"></div>
                    </div>
                    <div class="wpdiscuz_clear"></div>
                <?php } ?>
                <div id="wcThreadWrapper" class="wc-thread-wrapper">
                    <?php
                    $args = array('first_load' => 1, 'orderby' => $wpdiscuzCommentsOrderBy);
                    $args['orderby'] = $wpdiscuzCommentsOrderBy;
                    $args['order'] = $wpdiscuzCommentsOrder;
                    $commentData = $wpdiscuz->getWPComments($args);
                    echo $commentData['comment_list'];
                    ?>                
                    <div class="wpdiscuz-comment-pagination">
                        <?php
                        if (!$wpdiscuz->optionsSerialized->wordpressIsPaginate && $commentData['is_show_load_more']) {
                            $loadMoreButtonText = ($wpdiscuz->optionsSerialized->commentListLoadType == 1) ? $wpdiscuz->optionsSerialized->phrases['wc_load_rest_comments_submit_text'] : $wpdiscuz->optionsSerialized->phrases['wc_load_more_submit_text'];
                            ?>
                            <div class="wc-load-more-submit-wrap">
                                <div class="wc-load-more-link" data-lastparentid="<?php echo $commentData['last_parent_id']; ?>">
                                    <button name="submit"  class="wc-load-more-submit wc-loaded button">
                                        <?php echo $loadMoreButtonText; ?>
                                    </button>
                                </div>
                            </div>
                            <input id="wpdiscuzHasMoreComments" type="hidden" value="<?php echo $commentData['is_show_load_more']; ?>" />
                            <?php
                        } else if ($wpdiscuz->optionsSerialized->wordpressIsPaginate) {
                            paginate_comments_links();
                        }
                        ?>
                    </div>
                </div>
                <div class="wpdiscuz_clear"></div>
                <?php do_action('wpdiscuz_after_comments', $post, $currentUser, $commentsCount); ?>
                <?php if ($commentsCount) { ?>
                    <?php if ($wpdiscuz->optionsSerialized->showPluginPoweredByLink) { ?>
                        <div class="by-wpdiscuz">
                            <span id="awpdiscuz" onclick='javascript:document.getElementById("bywpdiscuz").style.display = "inline";
                                    document.getElementById("awpdiscuz").style.display = "none";'>
                                <img alt="wpdiscuz" src="<?php echo plugins_url(WPDISCUZ_DIR_NAME . '/assets/img/plugin-icon/icon_info.png'); ?>"  align="absmiddle" class="wpdimg"/>
                            </span>&nbsp;
                            <a href="http://wpdiscuz.com/" target="_blank" id="bywpdiscuz" title="wpDiscuz v<?php echo get_option(WpdiscuzCore::OPTION_SLUG_VERSION); ?> - Supercharged native comments">wpDiscuz</a>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
        <div id="wpdiscuz-loading-bar" class="wpdiscuz-loading-bar <?php echo ($currentUser->ID) ? 'wpdiscuz-loading-bar-auth' : 'wpdiscuz-loading-bar-unauth'; ?>"></div>
        <?php
        $loadEndTime = WpdiscuzHelper::getMicrotime();
        if (isset($_GET['wpdLoadTime'])) {
            ?>
            <div><?php echo $loadEndTime - $loadStartTime; ?></div>
            <?php
        }
    }
