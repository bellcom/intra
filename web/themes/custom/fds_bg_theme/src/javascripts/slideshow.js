/*
 * Used for paragraph slideshow.
 */

// Tiny Slider slideshow img.
(function($, Drupal, drupalSettings) {
  var selector = '.field--name-field-os2web-slideshow-media-ref.field--view-mode--default .field__items';

  if (document.querySelector(selector) !== null) {
    var items = 2;
    // Run tiny slider.
    tns({
      container: selector,
      items: 1,
      autoplay: false,
      autoplayHoverPause: true,
      gutter: 32,
      rewind: true,
    });
  }
  var selector2 = '.field--name-field-os2web-page-paragraph-bann.field__items';

  if (document.querySelector(selector2) !== null) {
    // Run tiny slider.
    tns({
      container: selector2,
      items: 1,
      autoplay: false,
      autoplayHoverPause: true,
      gutter: 32,
      rewind: true,
      onInit: function () {
        var items = $(selector2).find('.tns-item > .banner');
        var height = 0;
        for (var i = 0; i < items.length; i++) {
          height = Math.max(height, $(items[i]).height());
        }
        items.height(height);
      }
    });
  }
})(jQuery, Drupal, drupalSettings);
