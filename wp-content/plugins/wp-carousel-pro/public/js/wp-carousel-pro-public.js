(function ($) {
    'use strict';
    jQuery('body').find('.wpcp-carousel-section.wpcp-standard').each(function () {

        var carousel_id = $(this).attr('id');
        var _this = $(this);

        switch ( _this.data( 'arrowtype' ) ) {
            case 'angle':
            var left_arrow  = 'fa-angle-left';
            var right_arrow = 'fa-angle-right';
            break;
            case 'chevron':
            var left_arrow  = 'fa-chevron-left';
            var right_arrow = 'fa-chevron-right';
            break;
            case 'angle-double':
            var left_arrow  = 'fa-angle-double-left';
            var right_arrow = 'fa-angle-double-right';
            break;
            case 'arrow':
            var left_arrow  = 'fa-arrow-left';
            var right_arrow = 'fa-arrow-right';
            break;
            case 'long-arrow':
            var left_arrow  = 'fa-long-arrow-left';
            var right_arrow = 'fa-long-arrow-right';
            break;
            case 'caret':
            var left_arrow  = 'fa-caret-left';
            var right_arrow = 'fa-caret-right';
            break;
        }

        if (jQuery().slick) {
            jQuery('#' + carousel_id).slick({
                prevArrow: '<button class="slick-prev" data-role="none" aria-label="Previous slide" role="button"><i class="fa ' + left_arrow + '"></i></button>',
                nextArrow: '<button class="slick-next" data-role="none" aria-label="Next slide" role="button"><i class="fa ' + right_arrow + '"></i></button>',
            });
        }
    });
})(jQuery);