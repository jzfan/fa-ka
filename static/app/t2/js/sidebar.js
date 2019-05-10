'use strict';

//Fixed sidebar
var mTop = $('.sidebar').offset().top;
$(window).on('scroll', function () {
  var top = $(document).scrollTop();
  var eTop = $('#endMenu').offset().top;

  if (top >= (mTop - 100) && top <= (eTop - 550)) {
    $('.sidebar').addClass('sidebar--fixed').removeAttr('style');
    var width = $('.col-3').width();
    $('.sidebar').css("width", width);
  } else if (top >= (eTop - 550) && top <= (eTop - 350)) {
    $('.sidebar').css({'transform': 'translateY(-100%)'});
  } else {
    $('.sidebar').removeClass('sidebar--fixed').removeAttr('style');
  }
});

$(window).on('resize', function () {
  var width = $('.col-3').width();
  $('.sidebar').css("width", width);
});

$(window).on('scroll', function () {
  var $sections = $('.chapter');
  $sections.each(function (i, el) {
    var top = $(el).offset().top - 225;
    var bottom = top + $(el).height();
    var scroll = $(window).scrollTop();
    var id = $(el).attr('id');
    if (scroll > top && scroll < bottom) {
      $('li.active').removeClass('active');
      $('a[href="#' + id + '"]').parent().addClass('active');
    }
  })
});

//Anchors
$(function () {
  $('a[href^="#"]').on('click', function () {
    var target = $(this).attr('href');
    $('html, body').animate({scrollTop: $(target).offset().top - 110}, 800);
    return false;
  });
});