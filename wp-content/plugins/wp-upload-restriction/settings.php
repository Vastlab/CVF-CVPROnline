<?php
    wp_enqueue_style('wp-upload-restrictions-styles');

    $tab = filter_input(INPUT_GET, 'tab');
    $roles = $wpUploadRestriction->getAllRoles();
    $custom_types_html = $wpUploadRestriction->prepareCustomTypeHTML();
?>
<div id="message" class="updated fade"><p><?php _e('Settings saved.', 'wp_upload_restriction') ?></p></div>
<div id="error_message" class="error fade"><p><?php _e('Settings could not be saved.', 'wp_upload_restriction') ?></p></div>
<div class="wrap">
    <div class="icon32" id="icon-options-general"><br></div>
    <h2>WP Upload Restriction</h2>
    <h2 class="nav-tab-wrapper">
        <a href="?page=wp-upload-restriction%2Fsettings.php" class="nav-tab <?php echo empty($tab) ? 'nav-tab-active' : ''; ?>"><?php _e('Restrictions', 'wp_upload_restriction'); ?></a>
        <a href="?page=wp-upload-restriction%2Fsettings.php&tab=custom" class="nav-tab <?php echo $tab ==  'custom' ? 'nav-tab-active' : ''; ?>"><?php _e('Custom File Types', 'wp_upload_restriction'); ?></a>
    </h2>

    <?php if(empty($tab)): ?>
    <div class="role-list">

        <div class="sub-title"><?php _e('Roles', 'wp_upload_restriction'); ?></div>
        <div class="wp-roles">
        <?php foreach($roles as $key => $role):?>
        <a href="<?php print $key; ?>"><?php print $role['name']; ?></a>
        <?php endforeach; ?>
        </div>
    </div>
    
    <div class="mime-list-section">
        <form action="" method="post" id="wp-upload-restriction-form">
            <h2 id="role-name"><?php _e('Role', 'wp_upload_restriction'); ?>: <span></span></h2>
            <div id="mime-list">
 
            </div>
            <input type="hidden" name="role" value="" id="current-role">
            <input type="hidden" name="action" value="save_selected_mimes_by_role">
            <?php wp_nonce_field( 'wp-upload-restrict', 'wpur_nonce' ) ?>
            <p class="submit"><input type="button" value="<?php  _e('Save Changes', 'wp_upload_restriction'); ?>"> <span class="submit-loading ajax-loading-img"></span></p>
        </form>
    </div>
    <?php elseif($tab == 'custom'): ?>
    <form action="" method="post">
        <table class="form-table">
            <tbody>
                <tr>
                    <th><?php  _e('Extenstions', 'wp_upload_restriction'); ?></th>
                    <td>
                        <input type="input" value="" name="extensions" id="extensions" size="50" maxlength="50" required data-msg="<?php  _e('Extensions field is required', 'wp_upload_restriction'); ?>">
                        <br><span class="description"><?php  _e('Enter the file extension here. If there are multiple extensions then seperate them with "|". Example, "jpg|jpeg".', 'wp_upload_restriction'); ?></span>
                    </td>
                </tr>
                <tr>
                    <th><?php  _e('MIME Type', 'wp_upload_restriction'); ?></th>
                    <td><input type="input" value="" name="mime_type" id="mime_type" size="50" maxlength="50" required data-msg="<?php  _e('MIME Type field is required', 'wp_upload_restriction'); ?>"></td>
                </tr>
                <tr>
                    <th>&nbsp;</th>
                    <td id="cont_save_type">
                        <input type="button" id="save_type" value="<?php  _e('Add Type', 'wp_upload_restriction'); ?>">
                        <div class="message">Type successfully added.</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
    <hr>
    <h3><?php  _e('Custom Extensions', 'wp_upload_restriction'); ?></h3>
    <table class="wp-list-table widefat striped list-custom-types">
        <thead>
        <tr>
            <th><?php  _e('Extensions', 'wp_upload_restriction'); ?></th>
            <th><?php  _e('MIME Type', 'wp_upload_restriction'); ?></th>
            <th>&nbsp;</th>
        </tr>
        </thead>
        <tbody><?php echo $wpUploadRestriction->prepareCustomTypeHTML(); ?></tbody>
    </table>
    <?php endif;?>
</div>
