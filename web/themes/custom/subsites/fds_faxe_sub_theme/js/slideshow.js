/*
 * Used for paragraph slideshow.
 */

// Tiny Slider slideshow img.
(function() {

  let selectorMedia = '.paragraph--type--os2web-slideshow .field--name-field-media > .field__item';
  if (document.querySelectorAll(selectorMedia) !== null) {
    if (document.querySelectorAll(selectorMedia).length < 2) { selectorMedia = null; }
  }

  if (selectorMedia) {
    var selector = '.paragraph--type--os2web-slideshow .field--name-field-media';
    if (document.querySelector(selector) !== null) {
      // Run tiny slider.
      tns({
        container: selector,
        items: 1,
        autoplay: true,
        autoHeight: true,
        autoplayHoverPause: true,
        mouseDrag: true,
        gutter: 0,
        rewind: true,
        controls: false,
        nested: false,
      });
    }
  }
  
})();

