<?php

/**
 * Wise Chat BuddyPress group.
 *
 * @author Kainex <contact@kaine.pl>
 */
class WiseChatBuddyPressGroupExtension extends BP_Group_Extension {
    const DEFAULT_NAV_ITEM_POSITION = 40;
    const DEFAULT_NAV_ITEM_NAME = 'Chat';

    /**
     * @var WiseChatOptions
     */
    private $options;

    public function __construct() {
        $this->options = WiseChatOptions::getInstance();

        $args =  array(
            'slug' => 'chat',
            'name' => $this->localize('message_bp_manage_tab_name', 'Chat'),
            'nav_item_name' => self::DEFAULT_NAV_ITEM_NAME,
            'nav_item_position' => self::DEFAULT_NAV_ITEM_POSITION,
        );

        $id = groups_get_groupmeta($this->get_group_id(), 'bp_wisechat_id');
        $isEnabled = groups_get_groupmeta($this->get_group_id(), 'bp_wisechat_enabled') == true;
        $navItemPosition = intval(groups_get_groupmeta($this->get_group_id(), 'bp_wisechat_nav_item_position'));
        $navItemName = groups_get_groupmeta($this->get_group_id(), 'bp_wisechat_nav_item_name');
        if (!$isEnabled) {
            $args['show_tab'] = 'noone';
        }
        if ($navItemPosition > 0) {
            $args['nav_item_position'] = $navItemPosition;
        }
        if (strlen($navItemName) > 0) {
            $args['nav_item_name'] = $navItemName;
        }
        if (strlen($id) === 0) {
            $id = 'bp-chat-'.$this->get_group_id().'-'.substr(md5($this->get_group_id().time()), 0, 6);
            groups_update_groupmeta($this->get_group_id(), 'bp_wisechat_id', $id);
        }

        parent::init($args);
    }

    public function display($groupId = null) {
        $groupId = bp_get_group_id();
        $isEnabled = groups_get_groupmeta($groupId, 'bp_wisechat_enabled') == true;
        $id = groups_get_groupmeta($this->get_group_id(), 'bp_wisechat_id');
        $attributes = groups_get_groupmeta($groupId, 'bp_wisechat_attributes');
        $parsedAttributes = strlen($attributes) > 0 ? shortcode_parse_atts($attributes) : array();

        if ($isEnabled) {
            if (function_exists('wise_chat')) {
                $parsedAttributes['channel'] = $id;

                $wiseChat = WiseChatContainer::get('WiseChat');
                echo $wiseChat->getRenderedShortcode($parsedAttributes);
                $wiseChat->registerResources();
            }
        }
    }

    public function settings_screen($groupId = null ) {
        $isEnabled = groups_get_groupmeta($groupId, 'bp_wisechat_enabled');
        $navItemPosition = intval(groups_get_groupmeta($groupId, 'bp_wisechat_nav_item_position'));
        $navItemName = groups_get_groupmeta($groupId, 'bp_wisechat_nav_item_name');
        $isPermissionModEditMessagesGranted = groups_get_groupmeta($groupId, 'bp_wisechat_permissions_mod_edit_messages');
        $isPermissionModDeleteMessagesGranted = groups_get_groupmeta($groupId, 'bp_wisechat_permissions_mod_delete_messages');
        $isPermissionModBanUsersGranted = groups_get_groupmeta($groupId, 'bp_wisechat_permissions_mod_ban_users');
        $isPermissionAdminEditMessagesGranted = groups_get_groupmeta($groupId, 'bp_wisechat_permissions_admin_edit_messages');
        $isPermissionAdminDeleteMessagesGranted = groups_get_groupmeta($groupId, 'bp_wisechat_permissions_admin_delete_messages');
        $isPermissionAdminBanUsersGranted = groups_get_groupmeta($groupId, 'bp_wisechat_permissions_admin_ban_users');
        $attributes = groups_get_groupmeta($groupId, 'bp_wisechat_attributes');
        if ($navItemPosition == 0) {
            $navItemPosition = self::DEFAULT_NAV_ITEM_POSITION;
        }
        if (strlen($navItemName) === 0) {
            $navItemName = self::DEFAULT_NAV_ITEM_NAME;
        }
        ?>
            <label for="bp_wisechat_enabled"><?php echo $this->localize('message_bp_manage_enable_chat', 'Enable Chat'); ?></label>
            <input type="checkbox" value="1" tabindex="0" id="bp_wisechat_enabled" name="bp_wisechat_enabled" <?php echo $isEnabled ? 'checked': ''; ?> />
            <br />
            <label for="bp_wisechat_nav_item_position"><?php echo $this->localize('message_bp_manage_tab_position', 'Tab Position'); ?></label>
            <input name="bp_wisechat_nav_item_position" id="bp_wisechat_nav_item_position" value="<?php echo $navItemPosition; ?>" aria-required="true" type="number" style="width: 80px;" />
            <br />
            <label for="bp_wisechat_nav_item_name"><?php echo $this->localize('message_bp_manage_tab_label', 'Tab Label'); ?></label>
            <input name="bp_wisechat_nav_item_name" id="bp_wisechat_nav_item_name" value="<?php echo $navItemName; ?>" aria-required="true" type="text" />
            <br />
            <label for="bp_wisechat_nav_item_name"><?php echo $this->localize('message_bp_manage_permissions', 'Permissions'); ?></label>
            <p>
                <label for="bp_wisechat_permissions_mod_edit_messages">
                    <input type="checkbox" value="1" tabindex="0" id="bp_wisechat_permissions_mod_edit_messages" name="bp_wisechat_permissions_mod_edit_messages" <?php echo $isPermissionModEditMessagesGranted ? 'checked': ''; ?> />
                    <?php echo $this->localize('message_bp_manage_permissions_mods_edit', 'Allow mods to edit messages'); ?>
                </label>
                <label for="bp_wisechat_permissions_mod_delete_messages">
                    <input type="checkbox" value="1" tabindex="0" id="bp_wisechat_permissions_mod_delete_messages" name="bp_wisechat_permissions_mod_delete_messages" <?php echo $isPermissionModDeleteMessagesGranted ? 'checked': ''; ?> />
                    <?php echo $this->localize('message_bp_manage_permissions_mods_delete', 'Allow mods to delete messages'); ?>
                </label>
                <label for="bp_wisechat_permissions_mod_ban_users">
                    <input type="checkbox" value="1" tabindex="0" id="bp_wisechat_permissions_mod_ban_users" name="bp_wisechat_permissions_mod_ban_users" <?php echo $isPermissionModBanUsersGranted ? 'checked': ''; ?> />
                    <?php echo $this->localize('message_bp_manage_permissions_mods_ban', 'Allow mods to ban users'); ?>
                </label>

                <label for="bp_wisechat_permissions_admin_edit_messages">
                    <input type="checkbox" value="1" tabindex="0" id="bp_wisechat_permissions_admin_edit_messages" name="bp_wisechat_permissions_admin_edit_messages" <?php echo $isPermissionAdminEditMessagesGranted ? 'checked': ''; ?> />
                    <?php echo $this->localize('message_bp_manage_permissions_admins_edit', 'Allow admins to edit messages'); ?>
                </label>
                <label for="bp_wisechat_permissions_admin_delete_messages">
                    <input type="checkbox" value="1" tabindex="0" id="bp_wisechat_permissions_admin_delete_messages" name="bp_wisechat_permissions_admin_delete_messages" <?php echo $isPermissionAdminDeleteMessagesGranted ? 'checked': ''; ?> />
                    <?php echo $this->localize('message_bp_manage_permissions_admins_delete', 'Allow admins to delete messages'); ?>
                </label>
                <label for="bp_wisechat_permissions_admin_ban_users">
                    <input type="checkbox" value="1" tabindex="0" id="bp_wisechat_permissions_admin_ban_users" name="bp_wisechat_permissions_admin_ban_users" <?php echo $isPermissionAdminBanUsersGranted ? 'checked': ''; ?> />
                    <?php echo $this->localize('message_bp_manage_permissions_admins_ban', 'Allow admins to ban users'); ?>
                </label>
                <small>
                    <strong><?php echo $this->localize('message_bp_manage_notice', 'Notice:'); ?></strong>
                    <?php echo $this->localize('message_bp_manage_notice_text', 'These permissions will work only with enabled admin actions in Wise Chat Pro settings'); ?>
                </small>
            </p>
            <label for="bp_wisechat_attributes"><?php echo $this->localize('message_bp_manage_shortcode', 'Wise Chat Pro detailed parameters (shortcode syntax required)'); ?></label>
            <textarea name="bp_wisechat_attributes" id="bp_wisechat_attributes" aria-required="true"><?php echo $attributes; ?></textarea>
            <small><strong><?php echo $this->localize('message_bp_manage_shortcode_example', 'Example:'); ?></strong> window_title="The Chat" show_users="1"</small>
            <br />
            <br />
            <p style="float: right;">
                <a href="https://kaine.pl/projects/wp-plugins/wise-chat-pro/" title="Wise Chat Pro - Chat plugin for WordPress and BuddyPress">
                    <img src="<?php echo $this->options->getBaseDir() ?>/gfx/misc/powered-by-wise-chat-pro.png" alt="Wise Chat Pro plugin for WordPress" />
                </a>
            </p>
            <br style="clear: both;" />
        <?php
    }

    public function settings_screen_save($groupId = null) {
        $this->saveBooleanGroupMeta($groupId, 'bp_wisechat_enabled', false);
        $this->saveIntegerGroupMeta($groupId, 'bp_wisechat_nav_item_position', self::DEFAULT_NAV_ITEM_POSITION);
        $this->saveStringGroupMeta($groupId, 'bp_wisechat_nav_item_name', self::DEFAULT_NAV_ITEM_NAME);
        $this->saveBooleanGroupMeta($groupId, 'bp_wisechat_permissions_mod_delete_messages', false);
        $this->saveBooleanGroupMeta($groupId, 'bp_wisechat_permissions_mod_edit_messages', false);
        $this->saveBooleanGroupMeta($groupId, 'bp_wisechat_permissions_mod_ban_users', false);
        $this->saveBooleanGroupMeta($groupId, 'bp_wisechat_permissions_admin_delete_messages', false);
        $this->saveBooleanGroupMeta($groupId, 'bp_wisechat_permissions_admin_edit_messages', false);
        $this->saveBooleanGroupMeta($groupId, 'bp_wisechat_permissions_admin_ban_users', false);
        $this->saveLongStringGroupMeta($groupId, 'bp_wisechat_attributes', '');
    }

    private function localize($key, $default) {
        return $this->options->getEncodedOption($key, $default);
    }

    private function saveBooleanGroupMeta($groupId, $postKey, $default) {
        groups_update_groupmeta($groupId, $postKey, array_key_exists($postKey, $_POST) && $_POST[$postKey] == '1' ? true : $default);
    }

    private function saveIntegerGroupMeta($groupId, $postKey, $default) {
        groups_update_groupmeta(
            $groupId, $postKey,
            array_key_exists($postKey, $_POST) && intval($_POST[$postKey]) > 0
                ? intval($_POST[$postKey])
                : $default
        );
    }

    private function saveStringGroupMeta($groupId, $postKey, $default) {
        groups_update_groupmeta(
            $groupId, $postKey,
            array_key_exists($postKey, $_POST) && strlen($_POST[$postKey]) > 0
                ? htmlentities($_POST[$postKey], ENT_QUOTES, 'UTF-8')
                : $default
        );
    }

    private function saveLongStringGroupMeta($groupId, $postKey, $default) {
        groups_update_groupmeta(
            $groupId, $postKey,
            array_key_exists($postKey, $_POST) && strlen($_POST[$postKey]) > 0
                ? htmlentities($_POST[$postKey], ENT_NOQUOTES, 'UTF-8')
                : $default
        );
    }

}