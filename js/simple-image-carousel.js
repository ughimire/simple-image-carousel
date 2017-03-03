var wrapper = ".simplecarousel";
var wrapper_List = ".simplecarousel_List";
var wrapper_Inner_List = ".simplecarousel_InnerWrapper";

var defaultListWidth = 141;
var interval = 0;
var direction = "left";

jQuery(document).ready(function () {


    var WrapperWidth = jQuery(wrapper).width();
    var totalNumberOfList = Math.floor(WrapperWidth / defaultListWidth);

    var LoadedList = jQuery(wrapper_List + ' li').length;

    var positionRight = (WrapperWidth - (totalNumberOfList * defaultListWidth)) / 2;


    jQuery(wrapper + ' ' + wrapper_Inner_List).css({

        'width': (totalNumberOfList * defaultListWidth) + 'px',
        'float': 'none'


    });


    var counter = 0;
    jQuery.each(jQuery(wrapper + ' ' + wrapper_List + ' li'), function () {

        var position = counter * defaultListWidth;


        if (direction == "left") {
            jQuery(this).css('left', position + 'px');

        } else {

            jQuery(this).css('right', position + 'px');
        }

        counter++;

    });

    if (LoadedList > totalNumberOfList) {


        interval = setInterval(changeImages, 10);


    }

});

var listPosition = defaultListWidth;
var listItem = 0;

function changeImages() {

    var LoadedList = jQuery(wrapper_List + ' li').length;
    listPosition++;

    if ((listPosition) % (defaultListWidth) == 0) {


        listItem++;


        var ListHTML = jQuery('body').find(wrapper_List + ' li:nth-child(1)').html();


        jQuery('body').find(wrapper_List + ' li:nth-child(1)').remove();
        jQuery('body').find(wrapper_List).append("<li style='right:-20000'>" + ListHTML + "</li>");


        var counter = 0;
        jQuery.each(jQuery(wrapper + ' ' + wrapper_List + ' li'), function () {

            var position = counter * defaultListWidth;


            if (direction == "left") {

                jQuery(this).css({
                    'left': position + 'px',
                    '-webkit-transition': 'left 0.5s',
                    '-moz-transition': 'left 0.5s',
                    '-o-transition': 'left 0.5s',
                    'transition': 'left 0.5s'

                });
            } else {

                jQuery(this).css({
                    'right': position + 'px',
                    '-webkit-transition': 'left 0.5s',
                    '-moz-transition': 'left 0.5s',
                    '-o-transition': 'left 0.5s',
                    'transition': 'left 0.5s'
                });
            }
            counter++;

        });

    }
    if (listItem == 1) {
        listPosition = defaultListWidth;
        listItem = 0;


    }


}