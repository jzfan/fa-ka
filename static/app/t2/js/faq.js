'use strict';

//The code for opening and closing
$('.faq__card').on('click', function () {

  var el = this.getElementsByClassName('faq__card-description')[0];
  var p = $(el).children();
  var height = 0;

  for (var i = 0; i < $(p).length; i++) {
    height += $(p[i]).height() + 32;
  }

  if ($(this).hasClass('active')) {
    $(el).removeAttr('style');
    $(this).removeClass('active');
  } else {
    $(el).css({'height': height});
    $(this).addClass('active');
  }
});