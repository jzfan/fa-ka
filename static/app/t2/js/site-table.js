'use strict';

$('.site-table, .site-table--responsive').dragscrollable();

//Responsive table
var arrTr = $('.site-table--responsive-js tr');
var arrTh = arrTr[0].getElementsByClassName('site-table__head-th');
var index = 0;

function tableView() {
  var doc_w = $(window).width();
  if (doc_w < 752) {
    showColumn(index);
  } else {
    for (var j = 0; j < (arrTh).length; j++) {
      $(arrTh[j]).removeClass('hidden');
      hiddenButton(j);
    }
    for (var i = 1; i < (arrTr).length; i++) {
      var arrTd = arrTr[i].getElementsByTagName('td');
      for (var j = 0; j < (arrTd).length; j++) {
        $(arrTd[j]).removeClass('hidden');
      }
    }
  }
}

//Table view when page is opening
tableView();

function hiddenButton(index) {
  var next = arrTh[index].getElementsByClassName('site-table__btn-next');
  $(next).removeAttr('style');
  var prev = arrTh[index].getElementsByClassName('site-table__btn-prev');
  $(prev).removeAttr('style');
}

function showButton(index) {
  var next = arrTh[index].getElementsByClassName('site-table__btn-next');
  $(next).css({'visibility': 'visible', 'opacity': '1'});
  var prev = arrTh[index].getElementsByClassName('site-table__btn-prev');
  $(prev).css({'visibility': 'visible', 'opacity': '1'});
}

//what column of table you want to show
function showColumn(index, animate, typeAnimation, indexOld) {
  if (!animate) {
    for (var j = 0; j < (arrTh).length; j++) {
      $(arrTh[j]).addClass('hidden');
    }
    $(arrTh[index]).removeClass('hidden');
    showButton(index);

    for (var i = 1; i < (arrTr).length; i++) {
      var arrTd = arrTr[i].getElementsByTagName('td');
      for (var j = 0; j < (arrTd).length; j++) {
        $(arrTd[j]).addClass('hidden');
      }
      $(arrTd[index]).removeClass('hidden');
    }
  } else {

    if (typeAnimation == 'right') {
      $(arrTh[indexOld]).children('p').css({'transform': 'translateX(-120%)'});
    } else {
      $(arrTh[indexOld]).children('p').css({'transform': 'translateX(120%)'});
    }
    hiddenButton(indexOld)
    for (var i = 1; i < (arrTr).length; i++) {
      var arrTd = arrTr[i].getElementsByTagName('td');
      if (typeAnimation == 'right') {
        $(arrTd[indexOld]).children().css({'transform': 'translateX(-120%)'});
      } else {
        $(arrTd[indexOld]).children().css({'transform': 'translateX(120%)'});
      }
    }
    setTimeout(function () {
      $(arrTh[indexOld]).addClass('hidden');
      $(arrTh[indexOld]).children('p').removeAttr('style');
      $(arrTh[index]).removeClass('hidden');
      if (typeAnimation == 'left') {
        $(arrTh[index]).children('p').css({'transform': 'translateX(-120%)'});
      } else {
        $(arrTh[index]).children('p').css({'transform': 'translateX(120%)'});
      }

      for (var i = 1; i < (arrTr).length; i++) {
        var arrTd = arrTr[i].getElementsByTagName('td');
        $(arrTd[indexOld]).addClass('hidden');
        $(arrTd[indexOld]).children('p').removeAttr('style');
        $(arrTd[index]).removeClass('hidden');
        if (typeAnimation == 'left') {
          $(arrTd[index]).children().css({'transform': 'translateX(-120%)'});
        } else {
          $(arrTd[index]).children().css({'transform': 'translateX(120%)'});
        }
      }
    }, 250);
    setTimeout(function () {
      for (var i = 1; i < (arrTr).length; i++) {
        var arrTd = arrTr[i].getElementsByTagName('td');
        $(arrTd[index]).children().removeAttr('style');
      }
      $(arrTh[index]).children('p').removeAttr('style');
    }, 300);
    setTimeout(function () {
      showButton(index);
    }, 600);
  }
}

//change table view when size of window change
$(window).on('resize', function () {
  tableView();
});

$('.site-table__btn-next').on('click', function () {
  var old = index;
  index++;
  showColumn(index, true, 'right', old);
});

$('.site-table__btn-prev').on('click', function () {
  var old = index;
  index--;
  showColumn(index, true, 'left', old);
});