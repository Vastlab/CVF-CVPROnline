jQuery(document).ready(function () {
  jQuery('.wpcp-carousel-section').each(function () {
    jQuery(this).magnificPopup({
      showCloseBtn: true,
      type: 'image',
      image: {
        // options for image content type
        titleSrc: 'title',
        verticalFit: true
      },
      closeOnContentClick: false,
      closeBtnInside: false,
      mainClass: 'mfp-with-zoom mfp-img-mobile wpcp-light-box',
      gallery: {
        enabled: true,
        tCounter: ''
      },
      delegate: 'a.wcp-light-box',
      zoom: {
        enabled: true,
        duration: 300, // Don't forget to change the duration also in CSS
        opener: function (element) {
          return element.find('img')
        }
      }
    })
  })

  // Video popup
  jQuery('.wcp-video').magnificPopup({
    type: 'iframe',
    mainClass: 'mfp-fade',
    preloader: false,
    fixedContentPos: false,
    iframe: {
      patterns: {
        dailymotion: {
          index: 'dailymotion.com',

          id: function (url) {
            var m = url.match(
              /^.+dailymotion.com\/(video|hub)\/([^_]+)[^#]*(#video=([^_&]+))?/
            )
            if (m !== null) {
              if (m[4] !== undefined) {
                return m[4]
              }
              return m[2]
            }
            return null
          },

          src: 'https://www.dailymotion.com/embed/video/%id%?autoplay=1'
        }
      }
    }
  }) // wcp video end.

  jQuery('.wcp-video img').each(function (ix, it) {
    if ( jQuery(it)[0].naturalHeight <= 90 ) {
      var path = jQuery(it).attr('src')
      var altpath = path.replace('maxresdefault.jpg', '0.jpg')

      jQuery(it).attr('src', altpath)
    }
  })
})
