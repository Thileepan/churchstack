$(function () {

  setTimeout (function () {
  if ($.fn.isotope) {
  
    var $container = $('.works');
      
        $container.isotope({
          animationOptions: {

            easing: 'linear',
            duration: 250,
            queue: false
          },
          masonry: {
            //columnWidth: 120
          },
          resizing: true,
          animationEngine : "jquery"
        });

        $('#work-filters a').click(function(){
        var selector = $(this).attr('data-filter');
        $(this).parent ().addClass ('active').siblings ().removeClass ('active');
        $container.isotope({ filter: selector });
        return false;
      });

  }
}, 100);

});