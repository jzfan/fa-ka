// *** RUN YOUTUBE VIDEO IN CLICK

(function () {
  var video = '<iframe class="embed-responsive-item" src="https://www.youtube.com/embed/{%video%}?rel=0&amp;showinfo=0&autoplay=1" frameborder="0" allowfullscreen></iframe>';
  $('.video__video').on('click', function (e) {
    e.preventDefault();
    var videoId = $(this).attr('data-video');
    $('.video__video').addClass('video__video--loading');
    $('.video__video .embed-responsive').html(video.replace('{%video%}', videoId))
    $('.video__video')
      .removeClass('video__video--loading')
      .addClass('video__video--loaded')
      .off('click')
  });
})();