/* =========================================================
 * admin_script.js
 * =========================================================
 * Started by Solwin theme
 * ========================================================= */

jQuery(document).ready(function () {

    // deactivation popup code
    var ublk_plugin_admin = jQuery('.documentation_ublk_plugin').closest('div').find('.deactivate').find('a');
    ublk_plugin_admin.click(function (event) {
        event.preventDefault();
        jQuery('#deactivation_thickbox_ublk').trigger('click');
        jQuery('#TB_window').removeClass('thickbox-loading');
        change_thickbox_size_ublk();
    });
    checkOtherDeactivate();
    jQuery('.sol_deactivation_reasons').click(function () {
        checkOtherDeactivate();
    });
    jQuery('#sbtDeactivationFormCloseublk').click(function (event) {
        event.preventDefault();
        jQuery("#TB_closeWindowButton").trigger('click');
    });

    jQuery('.ublk-deactivation').on('click', function() {
        window.location.href = ublk_plugin_admin.attr('href');
    });

    jQuery('#txtUsername').keypress(function (event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
            jQuery('#filter_action').trigger('click');
        }
    });
    //Datepicker
    jQuery("#frmdate").datetimepicker({
        dateFormat: 'mm/dd/yy',
        minDate: 0,
        changeMonth: true,
        changeYear: true,
        timeFormat: 'HH:mm:ss',
        onClose: function (selectedDate) {
            jQuery("#todate").datetimepicker("option", "minDate", selectedDate);
        }
    });
    jQuery("#todate").datetimepicker({
        dateFormat: 'mm/dd/yy',
        minDate: 0,
        changeMonth: true,
        changeYear: true,
        timeFormat: 'HH:mm:ss',
        onClose: function (selectedDate) {
            jQuery("#frmdate").datepicker("option", "maxDate", selectedDate);
        }
    });
    jQuery('#display_status').change(function () {
        if (jQuery(this).val() == 'roles') {
            jQuery('.role_records').show();
            jQuery('.users_records, .filter_div').hide();
        }
        else {
            jQuery('.role_records').hide();
            jQuery('.users_records, .filter_div').show();
        }
    });
    jQuery('#chkapply').click(function () {
        var txtSunFrom = jQuery('#txtSunFrom').val();
        var txtSunTo = jQuery('#txtSunTo').val();
        var txtMonFrom = jQuery('#txtMonFrom').val();
        var txtMonTo = jQuery('#txtMonTo').val();
        var txtTueFrom = jQuery('#txtTueFrom').val();
        var txtTueTo = jQuery('#txtTueTo').val();
        var txtWedFrom = jQuery('#txtWedFrom').val();
        var txtWedTo = jQuery('#txtWedTo').val();
        var txtThuFrom = jQuery('#txtThuFrom').val();
        var txtThuTo = jQuery('#txtThuTo').val();
        var txtFriFrom = jQuery('#txtFriFrom').val();
        var txtFriTo = jQuery('#txtFriTo').val();
        var txtSatFrom = jQuery('#txtSatFrom').val();
        var txtSatTo = jQuery('#txtSatTo').val();
        if(txtSunFrom != '' && txtSunTo != '') {
            var from = txtSunFrom;
            var to  = txtSunTo;
        } else if(txtMonFrom != '' && txtMonTo != '') {
            var from = txtMonFrom;
            var to  = txtMonTo;
        } else if(txtTueFrom != '' && txtTueTo != '') {
            var from = txtTueFrom;
            var to  = txtTueTo;
        } else if(txtWedFrom != '' && txtWedTo != '') {
            var from = txtWedFrom;
            var to  = txtWedTo;
        } else if(txtThuFrom != '' && txtThuTo != '') {
            var from = txtThuFrom;
            var to  = txtThuTo;
        } else if(txtFriFrom != '' && txtFriTo != '') {
            var from = txtFriFrom;
            var to  = txtFriTo;
        } else if(txtSatFrom != '' && txtSatTo != '') {
            var from = txtSatFrom;
            var to  = txtSatTo;
        }
        jQuery('#txtSunFrom').val(from);
        jQuery('#txtSunTo').val(to);
        jQuery('#txtMonFrom').val(from);
        jQuery('#txtMonTo').val(to);
        jQuery('#txtTueFrom').val(from);
        jQuery('#txtTueTo').val(to);
        jQuery('#txtWedFrom').val(from);
        jQuery('#txtWedTo').val(to);
        jQuery('#txtThuFrom').val(from);
        jQuery('#txtThuTo').val(to);
        jQuery('#txtFriFrom').val(from);
        jQuery('#txtFriTo').val(to);
        jQuery('#txtSatFrom').val(from);
        jQuery('#txtSatTo').val(to);
    });
    jQuery('#chkreset').click(function () {
        jQuery('#txtSunFrom').val('');
        jQuery('#txtSunTo').val('');
        jQuery('#txtMonFrom').val('');
        jQuery('#txtMonTo').val('');
        jQuery('#txtTueFrom').val('');
        jQuery('#txtTueTo').val('');
        jQuery('#txtWedFrom').val('');
        jQuery('#txtWedTo').val('');
        jQuery('#txtThuFrom').val('');
        jQuery('#txtThuTo').val('');
        jQuery('#txtFriFrom').val('');
        jQuery('#txtFriTo').val('');
        jQuery('#txtSatFrom').val('');
        jQuery('#txtSatTo').val('');
    });
    jQuery('.view_block_data').click(function (event) {
        event.preventDefault();
        jQuery(this).closest('tr').next('tr').slideToggle();
    });
    //Solve searching issue for role and text field
    jQuery('#srole').on('focus', function () {
        jQuery('#txtUsername').val('');
    });
    jQuery('#txtUsername').on('focus', function () {
        jQuery('#srole').val('');
    });
    //Datepicker
    jQuery('.view_block_data_all').click(function (event) {
        event.preventDefault();
        jQuery(this).closest('tr').next('tr').slideToggle();
    });
});

/**
 *
 * @description change user function
 */
function changeUserBy() {
    var cmbUserBy = jQuery('#cmbUserBy').val();
    jQuery('.user-records').hide();
    jQuery('#' + cmbUserBy).show();
    jQuery('#hidden_cmbUserBy').val(cmbUserBy);
    var btnVal = jQuery('#sbt-block').val();
    var is_update = 0;
    if (btnVal.toLowerCase().indexOf("update") < 0) {
        is_update = 1;
        var new_btnval = btnVal.replace("User", "Role");
        var new_btnval1 = btnVal.replace("Role", "User");
    }
    if (cmbUserBy == 'role') {
        jQuery('.filter_div').hide();
        if (is_update == 1) {
            jQuery('#sbt-block').val(new_btnval);
        }
    }
    else {
        jQuery('.filter_div').show();
        if (is_update == 1) {
            jQuery('#sbt-block').val(new_btnval1);
        }
    }
}

/**
 *
 * @description click function for search user
 */
function searchUser() {
    jQuery('#filter_action').trigger('click');
}

jQuery(window).on('load',function () {
    jQuery('#subscribe_thickbox').trigger('click');
    jQuery("#TB_closeWindowButton").click(function () {
        jQuery.post(ajaxurl,
                {
                    'action': 'close_tab'
                });
    });
});

function ublk_show_hide_permission() {
    jQuery('.ublk_permission_cover').slideToggle();
}

function ublk_submit_optin(options) {
    result = {};
    result.action = 'ublk_submit_optin';
    result.email = jQuery('#ublk_admin_email').val();
    result.type = options;

    if (options == 'submit') {
        if (jQuery('input#ublk_agree_gdpr').is(':checked')) {
            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: result,
                error: function () { },
                success: function () {
                    window.location.href = "admin.php?page=block_user";
                },
                complete: function () {
                    window.location.href = "admin.php?page=block_user";
                }
            });
        }
        else {
            jQuery('.ublk_agree_gdpr_lbl').css('color', '#ff0000');
        }
    }
    else if (options == 'deactivate') {
        if (jQuery('input#ublk_agree_gdpr_deactivate').is(':checked')) {
            var ublk_plugin_admin = jQuery('.documentation_ublk_plugin').closest('div').find('.deactivate').find('a');
            result.selected_option_de = jQuery('input[name=sol_deactivation_reasons_ublk]:checked', '#frmDeactivationublk').val();
            result.selected_option_de_id = jQuery('input[name=sol_deactivation_reasons_ublk]:checked', '#frmDeactivationublk').attr("id");
            result.selected_option_de_text = jQuery("label[for='" + result.selected_option_de_id + "']").text();
            result.selected_option_de_other = jQuery('.sol_deactivation_reason_other_ublk').val();
            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: result,
                error: function () { },
                success: function () {
                    window.location.href = ublk_plugin_admin.attr('href');
                },
                complete: function () {
                    window.location.href = ublk_plugin_admin.attr('href');
                }
            });
        }
        else {
            jQuery('.ublk_agree_gdpr_lbl').css('color', '#ff0000');
        }
    }
    else {
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: result,
            error: function () { },
            success: function () {
                window.location.href = "admin.php?page=block_user";
            },
            complete: function () {
                window.location.href = "admin.php?page=block_user";
            }
        });
    }
}

function change_thickbox_size_ublk() {
    jQuery(document).find('#TB_window').width('700').height('420').css('margin-left', -700 / 2);
    jQuery(document).find('#TB_ajaxContent').width('640');
    jQuery(document).find('#TB_ajaxContent').addClass('ub_deactive_window');
    var doc_height = jQuery(window).height();
    var doc_space = doc_height - 400;
    if (doc_space > 0) {
        jQuery(document).find('#TB_window').css('margin-top', doc_space / 2);
    }
}

function checkOtherDeactivate() {
    var selected_option_de = jQuery('input[name=sol_deactivation_reasons_ublk]:checked', '#frmDeactivationublk').val();
    if (selected_option_de == '6') {
        jQuery('.sol_deactivation_reason_other_ublk').val('');
        jQuery('.sol_deactivation_reason_other_ublk').show();
    }
    else {
        jQuery('.sol_deactivation_reason_other_ublk').val('');
        jQuery('.sol_deactivation_reason_other_ublk').hide();
    }
}