'use strict';

//Fixed topbar
var mTop = $('.topbar').offset().top;
var eTop = $('#endMenu').offset().top;
$(window).on('scroll', function () {
  var top = $(document).scrollTop();

  if (top >= (mTop - 100) && top <= (eTop - 350)) {
    $('.topbar').addClass('topbar--fixed').removeAttr('style');
    $('.jsfeatures').addClass('jsfeatures--active');
  } else if (top >= (eTop - 350) && top <= (eTop - 150)) {
    $('.topbar').css({'transform': 'translateY(-150px)'});
  } else {
    $('.topbar').removeClass('topbar--fixed').removeAttr('style');
    $('.jsfeatures').removeClass('jsfeatures--active');
  }
});

//topbar
$(window).on('scroll', function () {
  var $sections = $('.about-app');
  $sections.each(function (i, el) {
    var top = $(el).offset().top - 283;
    var bottom = top + $(el).height() + 105;
    var scroll = $(window).scrollTop();
    var id = $(el).attr('id');
    if (scroll > top && scroll < bottom) {
      $('a.active').removeClass('active');
      $('a[href="#' + id + '"]').addClass('active');
    }
  })
});

//Anchors
$(function () {
  $('a[href^="#"]').on('click', function () {
    var target = $(this).attr('href');
    $('html, body').animate({scrollTop: $(target).offset().top - 200}, 800);
    return false;
  });
});