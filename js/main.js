$(function () {

  enableSidetabs ();

  enableLightbox ();

  enableBackToTop ();

  enableEnhancedAccordion ();

  enableTooltip ();

  enableContactMap ();

});




function enableSidetabs () {
    if ($.fn.sidetabs) {
        $('.sidetabs').sidetabs ({ history: true });
    }
}

function enableTooltip () {
    if ($.fn.tooltip) {
        $('.ui-tooltip').tooltip ({});
    }
}

function enableLightbox () {
    if ($.fn.lightbox) {
        $('.ui-lightbox').lightbox ();
    }
}

function enableEnhancedAccordion () {

    $('.accordion-toggle').prepend ('<span class="accordion-icon accordion-plus">+</span>');
    $('.accordion-toggle').prepend ('<span class="accordion-icon accordion-minus">-</span>');

    $('.accordion').on('show', function (e) {
         $(e.target).prev('.accordion-heading').parent ().addClass('open');
    });

    $('.accordion').on('hide', function (e) {
        $(this).find('.accordion-toggle').not($(e.target)).parents ('.accordion-group').removeClass('open');
    });
    
    $('.accordion').each (function () {         
        $(this).find ('.accordion-body.in').parent ().addClass ('open');
    });
}

function enableContactMap () {
  var m = $("#contact-map");

    if ($().gMap && 0<m.length) {

        var b=m.data("lat")
            , q=m.data("lng")
            , B=m.data("address")
            , D=m.data("zoom")
            , x={};B?x.address=B:(x.latitude=b,x.longitude=q);

        x.icon = {
            image: './img/map-icon/map-icon.png'
            , iconsize: [36, 48]
            , iconanchor: [36, 48]
        };


        m.gMap ({ 
            maptype: 'ROADMAP'
            , scrollwheel: false
            , markers: [x]
            , zoom: D 
        });
    }
}

function enableBackToTop () {
  var backToTop = $('<a>', { id: 'back-to-top', href: '#top' });
  var icon = $('<i>', { 'class': 'icon-chevron-up' });

  backToTop.appendTo ('body');
  icon.appendTo (backToTop);

  backToTop.hide();

  $(window).scroll(function () {
      if ($(this).scrollTop() > 150) {
          backToTop.fadeIn ();
      } else {
          backToTop.fadeOut ();
      }
  });

  backToTop.click (function (e) {
  	e.preventDefault ();

      $('body, html').animate({
          scrollTop: 0
      }, 600);
  });


}