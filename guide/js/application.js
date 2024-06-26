// NOTICE!! DO NOT USE ANY OF THIS JAVASCRIPT
// IT'S ALL JUST JUNK FOR OUR DOCS!
// ++++++++++++++++++++++++++++++++++++++++++

!function ($) {

  $(function(){

    var $window = $(window);

    // side bar
    /*setTimeout(function () {
      $('.sidebar__list').affix();
    }, 100);*/
    // make code pretty
    window.prettyPrint && prettyPrint();

  });

  add_offset = 55;
  



  $('.img-polaroid').click(function() {
    var img = $(this);
    $(this).toggleClass('enlarge');

    if (img.hasClass('is-2x')) {
      img.width(img.get(0).naturalWidth / 2);
    }
  });

  $('body').scrollspy({ target: '.sidebar__nav', offset: add_offset});


}(window.jQuery);