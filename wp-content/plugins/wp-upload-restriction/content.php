<h4><?php _e('Allowed File Types', 'wp_upload_restriction'); ?></h4>
<p><?php _e('Files with selected types will be allowed for uploading.', 'wp_upload_restriction'); ?></p>
<div class="check-uncheck-links"><a class="check" href="#"><?php _e('Check all', 'wp_upload_restriction'); ?></a> | <a class="uncheck" href="#"><?php _e('Uncheck all', 'wp_upload_restriction'); ?></a></div>
<div class="list">
<?php
    $i = 1;
            
    foreach($wp_mime_types as $ext => $type){
        $checked = $check_all ? 'checked="checked"' : (isset($selected_mimes[$ext]) ? 'checked="checked"' : '');
?>
    <div>
        <label for="ext_<?php echo $i; ?>">
            <input id="ext_<?php echo $i; ?>" type="checkbox" name="types[]" <?php echo $checked; ?> value="<?php echo $ext; ?>::<?php echo $type; ?>"> <?php echo $this->processExtention($ext); ?>
        </label>
    </div>
<?php    
        $i++;
    }
    
     
?>      
</div>
<p>&nbsp</p>
<h4><?php _e('Allowed Upload Size', 'wp_upload_restriction'); ?>:</h4>
<p><?php _e('Check the box below and enter value in the field to restrict upload size for the selected role.', 'wp_upload_restriction'); ?></p>
<input type="checkbox" name="restrict_upload_size" value="1" <?php echo $restrict_upload_size ? 'checked="checked"' : ''; ?>> <lable for="restrict_upload_size"><?php _e('Restrict upload size to', 'wp_upload_restriction'); ?></lable> <label><input type="text" maxlength="3" size="4" name="upload_size" value="<?php echo $upload_size; ?>"> MB</label>
            


<script type="text/javascript">
(function($){
    $('document').ready(function(){
        $('div.check-uncheck-links a.check').on('click', function(e){
            e.preventDefault();
            $('div#mime-list input[type="checkbox"]').attr('checked', 'checked');
        });

        $('div.check-uncheck-links a.uncheck').on('click', function(e){
            e.preventDefault();
            $('div#mime-list input[type="checkbox"]').removeAttr('checked');
        });
    });
})(jQuery);
</script>