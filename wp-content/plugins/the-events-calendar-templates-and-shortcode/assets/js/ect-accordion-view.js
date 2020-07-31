jQuery(document).ready(function ($) {
  $("#ect-accordion-wrapper").each(function (e) {
    var thisElement = $(this);
    thisElement.find(".ect-accordion-footer:first").addClass('show-event');
    thisElement.find('.ect-accordion-header:first').addClass('active-event');
    thisElement.find('.ect-accordion-event:first').addClass('active-event');
  });

  $('.ect-accordion-header').click(function(e){
      
    if($(this).hasClass("active-event")){
      $(this).parent(".ect-accordion-event").find(".ect-accordion-footer").removeClass('show-event');
      $(this).removeClass('active-event');
      $(this).parent(".ect-accordion-event").removeClass('active-event');
      return;
    }

    $(".ect-accordion-footer").removeClass('show-event');
    $(".ect-accordion-header").removeClass('active-event');
    $(".ect-accordion-event").removeClass('active-event');
    $(this).parent(".ect-accordion-event").find(".ect-accordion-footer").addClass('show-event');
    $(this).addClass('active-event');
    $(this).parent(".ect-accordion-event").addClass('active-event');
    //$(this)[0].scrollIntoView();

    var offset = $(this).offset();
    offset.top -= 60;
    $('html, body').animate({
      scrollTop: offset.top,
    }, 1000)
 
  });
  
});

(function($){
  $("#ect-accordion-wrapper").each(function (e) {
    var thisElement = $(this);
    thisElement.find(".ect-accordion-footer:first").addClass('show-event');
    thisElement.find('.ect-accordion-header:first').addClass('active-event');
  });

  

  ectAccordion(ele="#ect-accordion-wrapper");
function ectAccordion(ele) {
  var thisEle=$(ele);
    var accordionHeader=thisEle.find('.ect-accordion-header');
    accordionHeader.on("click",function (){
    if(accordionHeader.hasClass("active-event")){
      accordionHeader.parent(".ect-accordion-event").find(".ect-accordion-footer").removeClass('show-event');
      accordionHeader.removeClass('active-event');
      return;
    }
     
    thisEle.find(".ect-accordion-footer").removeClass('show-event');
    thisEle.find(".ect-accordion-header").removeClass('active-event');
    accordionHeader.parent(".ect-accordion-event").find(".ect-accordion-footer").addClass('show-event');
    accordionHeader.addClass('active-event');
    //$(this)[0].scrollIntoView();
    var offset = accordionHeader.offset();
    offset.top -= 60;
    $('html, body').animate({
      scrollTop: offset.top,
    }, 1000);
  });

  }
})(jQuery);