'use strict';

//Changing price in a toggle pricing block
$('#pricing-toggle').on('click', function () {
  $('.pricing__card').toggleClass('pricing__card--year')
});

//Changing price in common pricing
$(function () {

  if (document.getElementById('price')) {

    var select = $("#price");
    var arrMonth = ['$ 0', '$ 9.99', '$ 19.99', '$ 39.99', '$ 59.99'];
    var arrYear = ['$ 0', '$ 108.5', '$ 228.5', '$ 468.5', '$ 768.5'];
    var arrPlans = document.getElementsByClassName('pricing__plan');

    //Creating slider
    var slider = $("<div id='slider'></div>").insertAfter(select).slider({
      min: 1,
      max: 5,
      range: "min",
      value: select[0].selectedIndex + 3,
      slide: function (event, ui) {
        if (ui.value === 1) {
          return false;
        }
        select[0].selectedIndex = ui.value - 1;
        document.getElementById("month").innerHTML = arrMonth[ui.value - 1];
        document.getElementById("year").innerHTML = arrYear[ui.value - 1];
        var arrLi = document.getElementsByClassName('ui-slider-point');

        //Remove all price plans, change classes
        for (var i = 0; i < arrPlans.length; i++) {
          $(arrLi[i]).removeClass('active');
          $(arrPlans[i]).addClass('pricing__plan--disable').removeAttr('style');
          $(arrPlans[i]).removeClass('active');
        }

        //Show choosing price plan(s)
        for (var i = 0; i < ui.value - 1; i++) {
          $(arrLi[i]).addClass('active');
          $(arrPlans[i + 1]).removeClass('pricing__plan--disable');
        }

        //Using for mobile view
        $(arrPlans[ui.value - 1]).css({'visibility': 'visible'});
        $(arrPlans[ui.value - 1]).addClass('active');
      }
    });

    $("#price").on("change", function () {
      slider.slider("value", this.selectedIndex + 1);
    }, 300);

    $(arrPlans[2]).css({'visibility': 'visible'});
  }
});

$(function () {

  //Adding icon to slider button
  var icon = '<div class="ui-slider-icon"><i class="mdi mdi-unfold-more-vertical"></i></div>';
  var el = document.getElementsByClassName('ui-state-default');
  for (var i = 0; i < el.length; i++) {
    el[i].innerHTML = icon;
  }

  //Adding color strip (I don't know how it is called)
  var points = '<ul class="ui-slider-points"><li class="ui-slider-point active"></li>' +
    '<li class="ui-slider-point active"></li><li class="ui-slider-point"></li>' +
    '<li></li></ul>';
  var p = document.getElementsByClassName('ui-slider-horizontal');
  for (var i = 0; i < p.length; i++) {
    $(points).insertAfter(p[i]);
  }
});

$(function () {
  var btnPrev = '<span><i class="mdi mdi-chevron-left"></i></span>';
  var btnNext = '<span><i class="mdi mdi-chevron-right"></i></span>';

  var elL = document.getElementsByClassName('left');
  var elR = document.getElementsByClassName('right');
  for (var i = 0; i < elL.length; i++) {
    elL[i].innerHTML = btnPrev;
    elR[i].innerHTML = btnNext;
  }
});

$(function () {
  var width = $('.tablesaw-swipe-cellpersist').width() + 24;
  $('.left').css("left", width);
});

$(window).on('resize', function () {
  var width = $('.tablesaw-swipe-cellpersist').width() + 24;
  $('.left').css("left", width);
});
