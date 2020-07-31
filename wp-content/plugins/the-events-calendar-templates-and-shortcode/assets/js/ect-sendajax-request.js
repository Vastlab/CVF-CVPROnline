jQuery(document).ready(function ($) {
   $('.ect-user-status-wrapper').each(function () {
  var user_choice = $(this).parents("div[id^='event-']").find('.ect-user-choice').data('userchoice');
  
  $(this).parents("div[id^='event-']").find('option[value="' + user_choice + '"]').attr('selected','selected');
   });
 $("select.ect_interest_events").change(function(){

    var selectedCountry = $(this).children("option:selected").val();
    var event_id = $(this).data('ect-id');

if(selectedCountry!='not-interested'){
  $('.ect_submit_data').each(function () {
    $(this).click(function (e) {
      e.preventDefault();
      var evt_id = $(this).parents("form[id='ect_frm_data']").find("input[name='ect_hidden_id']").val()
      var name = $(this).parents("form[id='ect_frm_data']").find("input[name='ect_txt_name']").val()
      var email = $(this).parents("form[id='ect_frm_data']").find("input[name='ect_txt_email']").val()
      var nounce = $("#ect_wpnonce").val()

      if (name.length < 3) {
        alert('You must enter your name');
        return;
      }
      var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
      if (!email.match(mailformat)) {
        alert('Please Enter valid email address');
        return;
      }
     

      var data = {
        action: "ect_interested_event",
        event_id: $(this).parents("form[id='ect_frm_data']").find("input[name='ect_hidden_id']").val(),
        ect_username: $(this).parents("form[id='ect_frm_data']").find("input[name='ect_txt_name']").val(),
        ect_useremail: $(this).parents("form[id='ect_frm_data']").find("input[name='ect_txt_email']").val(),
        ect_eventname: $(this).parents("form[id='ect_frm_data']").find("input[name='ect_txt_title']").val(),
        ect_noncevalue: $("#ect_wpnonce").val(),
        ect_choice: selectedCountry,
      };
      
      $.post(ect_events_obj.ajax_url, data, function (data) {
      

        $(ect_modalid).hide(); 
       
      });
      
    });
    
  });
 }

  else{
   var not_intereste_duser = {
     action: "ect_interested_event",
      event_id: $(this).parents("form[id='ect_frm_data']").find("input[name='ect_hidden_id']").val(),
     event_id: event_id,
     ect_notinterested: selectedCountry,
    }

    $.post(ect_events_obj.ajax_url, not_intereste_duser, function (not_intereste_duser) {

   });
}
});
});

