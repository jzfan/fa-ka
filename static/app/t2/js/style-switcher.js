'use strict';

//Open swtcher menu
$('.style-switcher__control').on('click', function () {
  $('.style-switcher').toggleClass('style-switcher--active');
});

$('.style-switcher__link--red').on('click', function () {
  $('.style-switcher__link').removeClass('active');
  $(this).addClass('active');
  $('link[id="main_style"]').attr('href', 'assets/css/red.css');
});

$('.style-switcher__link--blue').on('click', function () {
  $('.style-switcher__link').removeClass('active');
  $(this).addClass('active');
  $('link[id="main_style"]').attr('href', 'assets/css/blue.css');
});

$('.style-switcher__link--violet').on('click', function () {
  $('.style-switcher__link').removeClass('active');
  $(this).addClass('active');
  $('link[id="main_style"]').attr('href', 'assets/css/violet.css');
});

$('.style-switcher__link--green').on('click', function () {
  $('.style-switcher__link').removeClass('active');
  $(this).addClass('active');
  $('link[id="main_style"]').attr('href', 'assets/css/green.css');
});

$('.style-switcher__link--red-gradient').on('click', function () {
  $('.style-switcher__link').removeClass('active');
  $(this).addClass('active');
  $('link[id="main_style"]').attr('href', 'assets/css/red-gradient.css');
});

$('.style-switcher__link--blue-gradient').on('click', function () {
  $('.style-switcher__link').removeClass('active');
  $(this).addClass('active');
  $('link[id="main_style"]').attr('href', 'assets/css/blue-gradient.css');
});

$('.style-switcher__link--violet-gradient').on('click', function () {
  $('.style-switcher__link').removeClass('active');
  $(this).addClass('active');
  $('link[id="main_style"]').attr('href', 'assets/css/violet-gradient.css');
});

$('.style-switcher__link--green-gradient').on('click', function () {
  $('.style-switcher__link').removeClass('active');
  $(this).addClass('active');
  $('link[id="main_style"]').attr('href', 'assets/css/green-gradient.css');
});