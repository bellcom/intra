
(function($, Drupal, drupalSettings) {

  $(".webdav-buttons .webdav-button").on("click", function() {
    $("#webdavframe").attr("src", '/webdavhandler/?action=' + $(this).data('action') + '&id=' + $(this).data('id'));

// console.log( $(this).data("action") );
// console.log( $(this).data("id") );
// console.log( $(this).data("link") );

  });



})(jQuery, Drupal, drupalSettings);