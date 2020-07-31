(function($){
    $('document').ready(function(){
        $('.role-list').on('click', 'a', function(e){
            e.preventDefault();
            $('.role-list a').removeClass('active');
            $(this).addClass('ajax-loading-img');
            $(this).addClass('active');
            $('#mime-list input').attr('disabled','disabled');
            $('#message, #error_message').hide();
            $('#role-name span').html($(this).html());
            $('#current-role').val($(this).attr('href'));

            var d = new Date();
            var data = {
                'action': 'get_selected_mimes_by_role',
                'role': $('#current-role').val()
            };

            $.post(ajaxurl, data, function(response) {
                $('#mime-list').html(response);
                $('.role-list a').removeClass('ajax-loading-img');
            });
        });
        
        $('.role-list a').first().trigger('click');
        
        $('.submit').on('click', 'input', function(){
            $('.submit-loading').css('display', 'inline-block');
            $('#message, #error_message').hide();           
            data = $('#wp-upload-restriction-form :input').serializeArray();
            
            $.post(ajaxurl, data, function(response) {
                $('.submit-loading').css('display', 'none');
                if(response == 'yes'){
                    $('#message').show();
                }
                else{
                    $('#error_message').show();
                }
            });
        });

        $('#save_type').on('click', function () {
            if($('#extensions').val() == ''){
                alert($('#extensions').attr('data-msg'));
                return false;
            }
            if($('#mime_type').val() == ''){
                alert($('#mime_type').attr('data-msg'));
                return false;
            }

            var data = {
                action : 'save_custom_type',
                ext : $('#extensions').val(),
                mime : $('#mime_type').val()
            };

            $.post(ajaxurl, data, function(response) {
                response = $.parseJSON( response );
                console.log(response);
                console.log(response.success);
                if(response.success == 'yes'){
                    $('.list-custom-types tbody').html(response.types);
                    $('#extensions, #mime_type').val('');
                    $('#cont_save_type .message').show();
                    $('#cont_save_type .message').delay(2000).hide(400);
                }
                else{
                    if(response.error){
                        alert(response.error);
                    }
                    else {
                        alert('An error has occurred. Please try again.');
                    }
                }
            });
        });

        $('body').on('click', 'a.del-mime', function (event) {
            event.preventDefault();

            var ext = $(this).attr('data');

            var data = {
                action : 'delete_custom_type',
                ext : ext
            };

            var parent = $('#' + $(this).attr('data-row'));

            parent.css('background-color', '#ffcccc');

            $.post(ajaxurl, data, function(response) {
                if(response == 'yes'){
                    parent.remove();
                }
                else{
                    alert('An error has occurred. Please try again.');
                }
            });
        });
    });
})(jQuery);