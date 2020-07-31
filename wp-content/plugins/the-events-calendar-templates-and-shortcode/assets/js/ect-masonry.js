(function($){


jQuery(document).ready(function($) {
 // mansory layout integration
 $(".ect-masonry-template-cont").each(function(){
  var tempWrapper=$(this);
      var self =tempWrapper.find(".ect-masonary-cont");
      self.find('.ect-grid-event').first().addClass('grid-sizer');
        self.imagesLoaded(function () {
            self.masonry({
                gutterWidth: 15,
                isAnimated: true,
                itemSelector:'.ect-grid-event',
                columnWidth: '.grid-sizer',
                percentPosition: true
            });
        });
        tempWrapper.find(".ect-categories li").click(function(e) {
            e.preventDefault();
            var filter = $(this).attr("data-filter");
            self.masonryFilter({
                filter: function () {
                    if (!filter) return true;
                    var content= $(this).attr("data-filter");
                      return content.includes(filter);
                }
            });
        });

      
        // category filters
        tempWrapper.find(".ect-categories li").on("click",function(){
        $(this).addClass("ect-active").siblings().removeClass('ect-active');
        var catEvents= $(this).attr('data-posts');
        var catPages=parseInt($(this).attr('data-pages'));
        var thisEle=$(this);
        var activeCat=thisEle.attr('data-filter');
        var prefetch=$(this).attr('data-prefetch');
        if(prefetch=='false'){
              $('.ect-masonay-load-more').find('.ect-load-more-btn').hide();
                ectLoadMoreContent(thisEle,tempWrapper,'catcontent');
            }else{
              if(catPages>0){
                $('.ect-masonay-load-more').find('.ect-load-more-btn').show();
              }
            }
  
    });

    tempWrapper.find('.ect-masonay-load-more').find('.ect-load-more-btn').on("click",function(){
      var thisEle=$(this);
      ectLoadMoreContent(thisEle,tempWrapper,'loadmore');
      return false;
    });
  
  });
});

  function ectLoadMoreContent(thisEle,tempWrapper,type){
   
   var settingContainer= thisEle.parents('.ect-masonry-template-cont').find('#ect-lm-settings');
  
   // var settingContainer=thisEle.parents('.ect-masonry-template-cont').find('#ect-lm-settings');
    var ajaxUrl= settingContainer.data('ajax-url');
    var settings=settingContainer.data('settings');
    var excludeEventsJson=settingContainer.attr('data-exclude-events');

    var loadMore=settingContainer.data('load-more');
    var loading=settingContainer.data('loading');
    var noEvent=settingContainer.data('loaded');
    var json_query=settingContainer.siblings('#ect-query-arg').html();
    var query=JSON.parse(json_query);

    if(type=="loadmore"){
      thisEle.find('.ect-preloader').show();
      thisEle.find('span').text(loading);
      var activeCat=tempWrapper.find(".ect-categories li.ect-active");
      var catSlug=activeCat.attr('data-filter');
      var catEvents=activeCat.attr('data-posts');
      var catPages=activeCat.attr('data-pages');
      var paged=activeCat.attr('data-paged');
    }else{
      var activeCat=thisEle;
      var catSlug=activeCat.attr('data-filter');
      var catEvents=activeCat.attr('data-posts');
      var catPages=activeCat.attr('data-pages');
      settingContainer.find('#ect-cat-load-more').show();
      var paged=thisEle.attr('data-paged');
    }
      var data = {
        'action': 'ect_catfilters_load_more',
        'query':query,
        'paged':paged,
        'cat':catSlug,
        'exclude_events':excludeEventsJson,
        'per-page':catEvents,
        'settings':settings,
      };
   jQuery.post(ajaxUrl, data, function(response) {
        var rs=JSON.parse(response);
          if(rs.events=="yes"){
    setTimeout(function() {
            var content=rs.content;
        $.each(content, function (key, val) {
              var html=$(val);
              tempWrapper.find('.ect-masonary-cont').append(html).masonry( 'appended', html, true );
              // $('.ect-masonary-cont').masonry( 'reload' );
        });
            paged=parseInt(paged)+1;
            if(rs.exclude_events){
              var oldlist=JSON.parse(excludeEventsJson);
              newExcludeList = oldlist.concat(JSON.parse(rs.exclude_events));
              settingContainer.attr('data-exclude-events','['+newExcludeList+']');
            }
           

            if(type=="loadmore"){
              activeCat.attr('data-paged',paged);
             thisEle.find('span').text(loadMore);
             thisEle.find('.ect-preloader').hide();
            }else{
              thisEle.attr('data-paged',paged);
             thisEle.attr('data-prefetch','true');
             settingContainer.find('#ect-cat-load-more').hide();
             if(catPages>0){
              tempWrapper.find('.ect-masonay-load-more').find('.ect-load-more-btn').show();
             }
            }

            masonryCustomCatColors(tempWrapper)
    },1000);
          } else{
          thisEle.find('.ect-preloader').hide();
          thisEle.find('span').text(noEvent);  
setTimeout(function() {
        if(type=="loadmore"){
          thisEle.hide().find('span').text(loadMore);
          settingContainer.find('#ect-cat-load-more').hide();
      //   settingContainer.next().append(noEvent);
            }else{
              var content=rs.content;
              thisEle.attr('data-prefetch','true');
              settingContainer.find('#ect-cat-load-more').hide();
              tempWrapper.find('.ect-masonay-load-more').find('.ect-load-more-btn').hide();
            }
 },1500);
          }  
      }); 
      
      return false;
  }

   function masonryCustomCatColors(parentWrapper){
      parentWrapper.find('.ect-grid-event').each(function(){
      var element=$(this); 
      var bgcolor = element.data("cat-bgcolor");
      var txtcolor = element.data("cat-txtcolor");
        if (bgcolor != null) {
        
          element.find(".ect-event-category ul.tribe_events_cat li a").css({
            "color": "#" + txtcolor,
            "background": convertHex2("#" + bgcolor, 0.9),
            "border-color":"#" + bgcolor
          });
       
          if (element.hasClass("style-1")) {
            element.find(".ect-grid-date").css({ 
              "background": convertHex2("#" + bgcolor, 0.95),
              "box-shadow": "none"
            });
          }
          else if(element.hasClass("style-2")) {
            element.find(".ect-grid-date").css({
              "background": "#" + bgcolor,
              "box-shadow": "none"
            });
          }
          else if (element.hasClass("style-3")) {
            element.find(".ect-grid-event-area").css({
              "background": "#" + bgcolor,
              "box-shadow": "none",
              "border-color": "#" + bgcolor
            });
            element.find(".ect-grid-event-area .ect-grid-title h4, .ect-grid-event-area a, .ect-grid-event-area .ect-venue-details, .ect-grid-event-area .ect-icon, .ect-grid-event-area .ect-rate").css({
              "color": "#" + txtcolor
            });
          }
      }
      if (txtcolor != null) {
        element.find(".ect-grid-date .ect-date-area").css({
          "color": "#" + txtcolor
        });
      }
    });
  }
   

    /*---Covert Hex Color into RGB with color opacity - START---*/
	function convertHex2(hex, opacity) {
		hex = hex.replace('#', '');
		r = parseInt(hex.substring(0, 2), 16);
		g = parseInt(hex.substring(2, 4), 16);
		b = parseInt(hex.substring(4, 6), 16);
		result = 'rgba(' + r + ',' + g + ',' + b + ',' + opacity + ')';
		return result;
	}
    })(jQuery);