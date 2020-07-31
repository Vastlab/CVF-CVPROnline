$('#headerMainMenu li').hover(function(){
    var marginAdjust = 100;
    var parentElement = $(this).parent();

    var navPosition = $(parentElement).position();
    var navWidth = $(parentElement).width();
    var navRight = navPosition.left+navWidth;

    var position = $(this).position();
    var thisWidth = $(this).children('ul').width();
    var thisRight = position.left+thisWidth-marginAdjust;

    if (thisRight > navWidth) $(this).children('ul').css('margin-left', navWidth-thisRight);
});
