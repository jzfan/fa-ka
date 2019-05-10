'use strict';

$(document).on('ready', function () {

  //Settings for activation carousels
  //More information about settings here: https://github.com/kenwheeler/slick/

  $("#feature-slider").slick({
    // normal options...
    infinite: true,
    autoplay: true,
    autoplaySpeed: 5000
  });

  $("#user-thinks").slick({
    // normal options...
    infinite: true,
    autoplay: true,
    autoplaySpeed: 5000,
    dots: true,
    dotsClass: 'carousel__navigation-stick'
  });

  //Custom navigation panel
  $('.carousel__navigation-item').on('click', function () {
    //remove .active class from all items
    $('.carousel__navigation-item').removeClass('slick-active');

    //change slide
    var id = $(this).attr('tabindex');
    $("#feature-slider").slick('slickGoTo', id, false);
    //add .active to actual nav-item
    $(this).addClass('slick-active')
  });

  //Changing style of navigation item before appear next slide
  $("#feature-slider").on('beforeChange', function (event, slick, currentSlide, nextSlide) {
    $('.carousel__navigation-item').removeClass('slick-active');
    $('.carousel__navigation-item[tabindex="' + nextSlide + '"]').addClass('slick-active');

    var leftPos = nextSlide * 150;
    $(".carousel__navigation").animate({scrollLeft: leftPos}, 400);
  });

  //Replace html-code from plugin to necessary for theme
  $(function () {
    //Add icons for all sliders
    var prev = '<span><i class="mdi mdi-chevron-left"></i></span>';
    var arrArrowLenght = document.getElementsByClassName("slick-prev").length;

    for (var i = 0; i < arrArrowLenght; i++) {
      document.getElementsByClassName("slick-prev")[i].innerHTML = prev;
    }

    var next = '<span><i class="mdi mdi-chevron-right"></i></span>';
    for (var i = 0; i < arrArrowLenght; i++) {
      document.getElementsByClassName("slick-next")[i].innerHTML = next;
    }
  });

  if (device.desktop()) $('.carousel__navigation, .carousel__navigation-items').dragscrollable();
});

